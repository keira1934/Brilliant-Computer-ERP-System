<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class ArInvoice extends Model
{
    protected $fillable = [
        'invoice_number', 'customer_id', 'sale_id', 'invoice_date', 'due_date',
        'subtotal', 'tax', 'discount', 'total', 'paid_amount', 'status', 'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ArPayment::class);
    }

    public function getOutstandingAttribute(): float
    {
        return max(0, (float) $this->total - (float) $this->paid_amount);
    }

    public function agingBucket(?string $asOf = null): string
    {
        $asOfDate = $asOf ? Carbon::parse($asOf) : now();
        $basis = $this->due_date ?? $this->invoice_date;
        $days = max(0, $basis->diffInDays($asOfDate, false));

        return match (true) {
            $days <= 0 => 'Current',
            $days <= 30 => '1-30',
            $days <= 60 => '31-60',
            $days <= 90 => '61-90',
            default => '90+',
        };
    }
}
