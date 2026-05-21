<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArPayment extends Model
{
    protected $fillable = [
        'payment_number', 'ar_invoice_id', 'customer_id', 'payment_date',
        'amount', 'payment_method', 'reference', 'notes',
        'status', 'verified_by', 'verified_at', 'rejection_reason',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
        'verified_at'  => 'datetime',
    ];

    // ── Status constants ──────────────────────────────────────────────────
    public const STATUS_PENDING  = 'Pending Verification';
    public const STATUS_VERIFIED = 'Verified';
    public const STATUS_REJECTED = 'Rejected';

    // ── Relationships ─────────────────────────────────────────────────────
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(ArInvoice::class, 'ar_invoice_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ── Helpers ───────────────────────────────────────────────────────────
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isVerified(): bool
    {
        return $this->status === self::STATUS_VERIFIED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_VERIFIED => 'badge-success',
            self::STATUS_REJECTED => 'badge-danger',
            default               => 'badge-warning',
        };
    }
}
