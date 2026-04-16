<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChartOfAccount extends Model
{
    protected $fillable = ['code', 'name', 'type', 'normal_balance', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class, 'account_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'account_id');
    }

    /** Get balance for this account from journal lines */
    public function getBalance(?string $from = null, ?string $to = null): float
    {
        $q = $this->journalLines()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id');

        if ($from) $q->where('journal_entries.entry_date', '>=', $from);
        if ($to)   $q->where('journal_entries.entry_date', '<=', $to);

        $debit  = (float) $q->sum('journal_entry_lines.debit');
        $credit = (float) $q->sum('journal_entry_lines.credit');

        return $this->normal_balance === 'debit' ? ($debit - $credit) : ($credit - $debit);
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
