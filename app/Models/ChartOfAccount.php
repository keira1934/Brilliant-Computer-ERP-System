<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChartOfAccount extends Model
{
    protected $fillable = [
        'code', 'name', 'type', 'normal_balance', 'description',
        'is_active', 'opening_balance', 'opening_balance_date',
    ];

    protected $casts = [
        'is_active'            => 'boolean',
        'opening_balance'      => 'decimal:2',
        'opening_balance_date' => 'date',
    ];

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class, 'account_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'account_id');
    }

    /**
     * Get balance for this account from ledger-affecting journal lines only.
     * Opening balance is included when no $from date is specified (cumulative balance).
     */
    public function getBalance(?string $from = null, ?string $to = null): float
    {
        $q = $this->journalLines()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->whereIn('journal_entries.status', JournalEntry::ledgerStatuses());

        if ($from) $q->where('journal_entries.entry_date', '>=', $from);
        if ($to)   $q->where('journal_entries.entry_date', '<=', $to);

        $debit  = (float) (clone $q)->sum('journal_entry_lines.debit');
        $credit = (float) (clone $q)->sum('journal_entry_lines.credit');

        $journalBalance = $this->normal_balance === 'debit'
            ? ($debit - $credit)
            : ($credit - $debit);

        // Include opening balance when doing a cumulative (no $from) or when
        // the opening balance date falls within the requested range.
        $openingBalance = (float) ($this->opening_balance ?? 0);
        if ($openingBalance != 0) {
            $obDate = $this->opening_balance_date;
            $includeOpening = false;

            if ($obDate === null) {
                // No date set — include only in cumulative queries (no $from)
                $includeOpening = ($from === null);
            } else {
                $obDateStr = $obDate instanceof \Carbon\Carbon
                    ? $obDate->toDateString()
                    : (string) $obDate;
                $afterFrom  = ($from === null || $obDateStr >= $from);
                $beforeTo   = ($to   === null || $obDateStr <= $to);
                $includeOpening = $afterFrom && $beforeTo;
            }

            if ($includeOpening) {
                $journalBalance += $openingBalance;
            }
        }

        return $journalBalance;
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'asset'     => 'Aset',
            'liability' => 'Kewajiban',
            'equity'    => 'Ekuitas',
            'revenue'   => 'Pendapatan',
            'expense'   => 'Beban',
            default     => $this->type,
        };
    }
}
