<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdjustingEntry extends Model
{
    protected $fillable = [
        'journal_entry_id', 'adjustment_date', 'adjustment_type',
        'description', 'amount', 'status',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
