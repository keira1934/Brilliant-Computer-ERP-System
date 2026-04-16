<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    protected $fillable = ['entry_date', 'description', 'reference_type', 'reference_id'];

    protected $casts = ['entry_date' => 'date'];

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function getTotalDebit(): float
    {
        return (float) $this->lines->sum('debit');
    }

    public function getTotalCredit(): float
    {
        return (float) $this->lines->sum('credit');
    }
}
