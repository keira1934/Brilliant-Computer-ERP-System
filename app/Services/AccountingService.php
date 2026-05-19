<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\FinancialPeriod;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    public const OPENING_BALANCE_REFERENCE = 'OpeningBalance';
    public const OPENING_BALANCE_DESCRIPTION = 'SYSTEM OPENING ENTRY - OPENING BALANCE';
    public const OPENING_EQUITY_ACCOUNT = '3-1000';

    public function __construct(private AuditService $auditService) {}

    /**
     * Post a balanced journal entry.
     *
     * @param  string      $date        YYYY-MM-DD
     * @param  string      $description
     * @param  string|null $refType     e.g. 'Sale', 'ServiceOrder', 'Purchase', 'Payroll', 'Expense'
     * @param  int|null    $refId
     * @param  array       $lines       [{code, debit, credit, description?}, ...]
     * @param  bool        $autoPost    If true, immediately post (default for auto-generated journals)
     */
    public function postJournal(
        string  $date,
        string  $description,
        ?string $refType,
        ?int    $refId,
        array   $lines,
        bool    $autoPost = true
    ): JournalEntry {
        if (count($lines) < 2) {
            throw new \RuntimeException('Journal entry must contain at least two lines.');
        }

        foreach ($lines as $line) {
            $debit = (float) ($line['debit'] ?? 0);
            $credit = (float) ($line['credit'] ?? 0);

            if ($debit < 0 || $credit < 0) {
                throw new \RuntimeException('Journal line amounts cannot be negative.');
            }

            if ($debit > 0 && $credit > 0) {
                throw new \RuntimeException('A journal line cannot contain both debit and credit amounts.');
            }

            if ($debit === 0.0 && $credit === 0.0) {
                throw new \RuntimeException('Journal line amounts must be greater than zero.');
            }
        }

        // Validate balanced entry
        $totalDebit  = array_sum(array_column($lines, 'debit'));
        $totalCredit = array_sum(array_column($lines, 'credit'));

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            throw new \RuntimeException(
                "Journal not balanced: Debit={$totalDebit} Credit={$totalCredit}"
            );
        }

        if ($totalDebit <= 0) {
            throw new \RuntimeException('Journal entry must have amounts greater than zero.');
        }

        // Check if date is in a closed financial period
        if (FinancialPeriod::isDateInClosedPeriod($date)) {
            throw new \RuntimeException(
                "Cannot post journal for date {$date}: the financial period is closed."
            );
        }

        return DB::transaction(function () use ($date, $description, $refType, $refId, $lines, $autoPost) {
            $status   = $autoPost ? JournalEntry::STATUS_POSTED : JournalEntry::STATUS_DRAFT;
            $postedAt = $autoPost ? now() : null;

            $entry = JournalEntry::create([
                'journal_number'  => $this->generateJournalNumber(),
                'entry_date'      => $date,
                'description'     => $description,
                'reference_type'  => $refType,
                'reference_id'    => $refId,
                'status'          => $status,
                'posted_at'       => $postedAt,
                'posted_by'       => $autoPost ? Auth::id() : null,
                'created_by'      => Auth::id(),
            ]);

            foreach ($lines as $line) {
                $account = ChartOfAccount::where('code', $line['code'])->firstOrFail();

                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id'       => $account->id,
                    'description'      => $line['description'] ?? null,
                    'debit'            => $line['debit']  ?? 0,
                    'credit'           => $line['credit'] ?? 0,
                ]);
            }

            $this->auditService->logCreation('journal', $entry, "Journal {$entry->journal_number} created ({$status})");

            return $entry;
        });
    }

    /**
     * Reverse a posted journal entry by creating a new reversal entry.
     */
    public function reverseJournal(JournalEntry $entry, ?string $reason = null): JournalEntry
    {
        return DB::transaction(function () use ($entry, $reason) {
            $entry = JournalEntry::whereKey($entry->id)->lockForUpdate()->firstOrFail();

            if (!$entry->isPosted()) {
                throw new \RuntimeException('Only posted journals can be reversed.');
            }

            $entry->load('lines.account');

            $reversalDescription = "Reversal of {$entry->journal_number}";
            if ($reason) {
                $reversalDescription .= " — {$reason}";
            }

            // Create reversal entry with swapped debits/credits
            $reversalLines = [];
            foreach ($entry->lines as $line) {
                $reversalLines[] = [
                    'code'        => $line->account->code,
                    'debit'       => (float) $line->credit,
                    'credit'      => (float) $line->debit,
                    'description' => "Reversal: " . ($line->description ?? ''),
                ];
            }

            $reversalEntry = $this->postJournal(
                now()->toDateString(),
                $reversalDescription,
                $entry->reference_type,
                $entry->reference_id,
                $reversalLines
            );

            // Mark original as reversed
            $entry->update([
                'status'              => JournalEntry::STATUS_REVERSED,
                'reversed_by_entry_id' => $reversalEntry->id,
            ]);

            $this->auditService->logStatusChange(
                'journal', $entry, 'reverse',
                "Journal {$entry->journal_number} reversed by {$reversalEntry->journal_number}"
            );

            return $reversalEntry;
        });
    }

    /**
     * Get account balance from POSTED journal lines only.
     */
    public function getAccountBalance(string $code, ?string $from = null, ?string $to = null): float
    {
        $account = ChartOfAccount::where('code', $code)->first();
        if (!$account) return 0.0;
        return $account->getBalance($from, $to);
    }

    public function syncOpeningBalanceJournal(): ?JournalEntry
    {
        return DB::transaction(function () {
            $accounts = ChartOfAccount::where('is_active', true)
                ->where('opening_balance', '!=', 0)
                ->orderBy('code')
                ->lockForUpdate()
                ->get();

            $existing = JournalEntry::where('reference_type', self::OPENING_BALANCE_REFERENCE)
                ->lockForUpdate()
                ->get();

            foreach ($existing as $entry) {
                if (FinancialPeriod::isDateInClosedPeriod($entry->entry_date->toDateString())) {
                    throw new \RuntimeException('Opening balances cannot be changed because the current opening journal is in a closed financial period.');
                }
            }

            JournalEntry::where('reference_type', self::OPENING_BALANCE_REFERENCE)->delete();

            if ($accounts->isEmpty()) {
                return null;
            }

            $openingDateValue = $accounts
                ->pluck('opening_balance_date')
                ->filter()
                ->min();
            $openingDate = $openingDateValue
                ? Carbon::parse($openingDateValue)->toDateString()
                : now()->startOfYear()->toDateString();

            if (FinancialPeriod::isDateInClosedPeriod($openingDate)) {
                throw new \RuntimeException("Cannot post opening balances for {$openingDate}: the financial period is closed.");
            }

            $lines = [];
            $totalDebit = 0.0;
            $totalCredit = 0.0;

            foreach ($accounts as $account) {
                $amount = round(abs((float) $account->opening_balance), 2);
                if ($amount <= 0) {
                    continue;
                }

                $isNormalSide = (float) $account->opening_balance >= 0;
                $debit = ($account->normal_balance === 'debit') === $isNormalSide ? $amount : 0.0;
                $credit = $debit > 0 ? 0.0 : $amount;

                $totalDebit += $debit;
                $totalCredit += $credit;

                $lines[] = [
                    'code' => $account->code,
                    'debit' => $debit,
                    'credit' => $credit,
                    'description' => "Opening balance - {$account->code} {$account->name}",
                ];
            }

            $difference = round($totalDebit - $totalCredit, 2);
            if ($difference > 0) {
                $lines[] = [
                    'code' => self::OPENING_EQUITY_ACCOUNT,
                    'debit' => 0,
                    'credit' => $difference,
                    'description' => 'Opening balance equity plug',
                ];
            } elseif ($difference < 0) {
                $lines[] = [
                    'code' => self::OPENING_EQUITY_ACCOUNT,
                    'debit' => abs($difference),
                    'credit' => 0,
                    'description' => 'Opening balance equity plug',
                ];
            }

            if (count($lines) < 2) {
                return null;
            }

            return $this->postJournal(
                $openingDate,
                self::OPENING_BALANCE_DESCRIPTION,
                self::OPENING_BALANCE_REFERENCE,
                null,
                $lines
            );
        });
    }

    public function getAccountMovement(ChartOfAccount $account, ?string $from = null, ?string $to = null, bool $excludeClosing = false): array
    {
        $q = $account->journalLines()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->whereIn('journal_entries.status', JournalEntry::ledgerStatuses());

        if ($from) $q->where('journal_entries.entry_date', '>=', $from);
        if ($to)   $q->where('journal_entries.entry_date', '<=', $to);
        if ($excludeClosing) {
            $q->where(function ($query) {
                $query->whereNull('journal_entries.reference_type')
                    ->orWhere('journal_entries.reference_type', '!=', 'PeriodClosing');
            });
        }

        $debit = (float) (clone $q)->sum('journal_entry_lines.debit');
        $credit = (float) (clone $q)->sum('journal_entry_lines.credit');
        $signed = $account->normal_balance === 'debit' ? ($debit - $credit) : ($credit - $debit);

        return compact('debit', 'credit', 'signed');
    }

    public function getTrialBalance(?string $from, ?string $to)
    {
        return ChartOfAccount::where('is_active', true)
            ->orderBy('code')
            ->get()
            ->map(function (ChartOfAccount $account) use ($from, $to) {
                $beginning = $from
                    ? $account->getBalance(null, Carbon::parse($from)->subDay()->toDateString())
                    : 0.0;
                $movement = $this->getAccountMovement($account, $from, $to);
                $ending = $account->getBalance(null, $to);

                $account->beginning_balance = $beginning;
                $account->total_debit = $movement['debit'];
                $account->total_credit = $movement['credit'];
                $account->ending_balance = $ending;

                if ($ending >= 0) {
                    $account->tb_debit = $account->normal_balance === 'debit' ? $ending : 0;
                    $account->tb_credit = $account->normal_balance === 'credit' ? $ending : 0;
                } else {
                    $account->tb_debit = $account->normal_balance === 'credit' ? abs($ending) : 0;
                    $account->tb_credit = $account->normal_balance === 'debit' ? abs($ending) : 0;
                }

                return $account;
            })
            ->filter(fn($a) => round(abs($a->beginning_balance) + $a->total_debit + $a->total_credit + abs($a->ending_balance), 2) > 0)
            ->values();
    }

    public function getBalanceSheet(string $asOf): array
    {
        $withBalances = fn(string $type) => ChartOfAccount::where('type', $type)
            ->where('is_active', true)
            ->orderBy('code')
            ->get()
            ->map(function (ChartOfAccount $account) use ($asOf) {
                $account->balance = $account->getBalance(null, $asOf);
                if ($account->type === 'asset' && $account->normal_balance === 'credit') {
                    $account->balance *= -1;
                    $account->is_contra = true;
                }
                return $account;
            });

        $assetAccounts = $withBalances('asset');
        $liabilityAccounts = $withBalances('liability');
        $equityAccounts = $withBalances('equity');

        $totalRevenue = ChartOfAccount::where('type', 'revenue')->get()
            ->sum(fn($a) => $a->getBalance(null, $asOf));
        $totalExpenses = ChartOfAccount::where('type', 'expense')->get()
            ->sum(fn($a) => $a->getBalance(null, $asOf));
        $currentEarnings = round($totalRevenue - $totalExpenses, 2);

        $totalAssets = round($assetAccounts->sum('balance'), 2);
        $totalLiabilities = round($liabilityAccounts->sum('balance'), 2);
        $totalEquity = round($equityAccounts->sum('balance') + $currentEarnings, 2);
        $isBalanced = round($totalAssets, 2) === round($totalLiabilities + $totalEquity, 2);

        return compact(
            'assetAccounts', 'liabilityAccounts', 'equityAccounts',
            'totalRevenue', 'totalExpenses', 'currentEarnings',
            'totalAssets', 'totalLiabilities', 'totalEquity', 'isBalanced'
        );
    }

    public function getCashBalance(?string $asOf = null): float
    {
        return ChartOfAccount::whereIn('code', ['1-1000', '1-1100'])
            ->get()
            ->sum(fn($account) => $account->getBalance(null, $asOf));
    }

    /**
     * Get total debit/credit for a type of accounts within a date range.
     * Only considers POSTED journals.
     */
    public function getTotalByType(string $type, string $column, ?string $from = null, ?string $to = null): float
    {
        $query = JournalEntryLine::query()
            ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'journal_entry_lines.account_id')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('chart_of_accounts.type', $type)
            ->whereIn('journal_entries.status', JournalEntry::ledgerStatuses());

        if ($from) $query->where('journal_entries.entry_date', '>=', $from);
        if ($to)   $query->where('journal_entries.entry_date', '<=', $to);

        return (float) $query->sum("journal_entry_lines.{$column}");
    }

    /**
     * Generate an atomic journal number using lockForUpdate to prevent race conditions.
     */
    public function generateJournalNumber(): string
    {
        $prefix = 'JRN-' . date('Ym') . '-';

        // Atomic: lock + MAX to prevent duplicates
        $lastNumber = JournalEntry::where('journal_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->max(DB::raw("CAST(SUBSTRING(journal_number, " . (strlen($prefix) + 1) . ") AS UNSIGNED)"));

        $next = ($lastNumber ?? 0) + 1;

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
