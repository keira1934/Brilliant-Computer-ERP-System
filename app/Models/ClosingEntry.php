<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClosingEntry extends Model
{
    protected $fillable = [
        'financial_period_id', 'journal_entry_id', 'closing_date',
        'revenue_closed', 'expenses_closed', 'net_income',
    ];

    protected $casts = [
        'closing_date' => 'date',
        'revenue_closed' => 'decimal:2',
        'expenses_closed' => 'decimal:2',
        'net_income' => 'decimal:2',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(FinancialPeriod::class, 'financial_period_id');
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
