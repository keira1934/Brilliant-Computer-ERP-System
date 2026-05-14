<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\FinancialPeriod;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountingService
{
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
    private function generateJournalNumber(): string
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
