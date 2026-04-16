<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * Post a balanced journal entry.
     *
     * @param  string  $date       YYYY-MM-DD
     * @param  string  $description
     * @param  string|null $refType  e.g. 'Sale', 'ServiceOrder', 'Purchase', 'Payroll', 'Expense'
     * @param  int|null    $refId
     * @param  array   $lines      [{code, debit, credit, description?}, ...]
     */
    public function postJournal(
        string  $date,
        string  $description,
        ?string $refType,
        ?int    $refId,
        array   $lines
    ): JournalEntry {
        // Validate balanced entry
        $totalDebit  = array_sum(array_column($lines, 'debit'));
        $totalCredit = array_sum(array_column($lines, 'credit'));

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            throw new \RuntimeException(
                "Jurnal tidak seimbang: Debit={$totalDebit} Credit={$totalCredit}"
            );
        }

        return DB::transaction(function () use ($date, $description, $refType, $refId, $lines) {
            $entry = JournalEntry::create([
                'entry_date'     => $date,
                'description'    => $description,
                'reference_type' => $refType,
                'reference_id'   => $refId,
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

            return $entry;
        });
    }

    /**
     * Get account balance from journal lines.
     */
    public function getAccountBalance(string $code, ?string $from = null, ?string $to = null): float
    {
        $account = ChartOfAccount::where('code', $code)->first();
        if (!$account) return 0.0;
        return $account->getBalance($from, $to);
    }

    /**
     * Get total debit/credit for a type of accounts within a date range.
     */
    public function getTotalByType(string $type, string $column, ?string $from = null, ?string $to = null): float
    {
        $query = JournalEntryLine::query()
            ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'journal_entry_lines.account_id')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('chart_of_accounts.type', $type);

        if ($from) $query->where('journal_entries.entry_date', '>=', $from);
        if ($to)   $query->where('journal_entries.entry_date', '<=', $to);

        return (float) $query->sum("journal_entry_lines.{$column}");
    }
}
