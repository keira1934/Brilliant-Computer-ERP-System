<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Purchase extends Model
{
    protected $fillable = [
        'po_number', 'supplier_id', 'purchase_date', 'expected_date',
        'subtotal', 'total', 'status', 'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'expected_date' => 'date',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function apInvoices(): HasMany
    {
        return $this->hasMany(ApInvoice::class);
    }

    public function approvals(): MorphMany
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'Draft'      => 'badge status-draft',
            'Pending Approval' => 'badge status-ordered',
            'Approved'   => 'badge status-completed',
            'Ordered'    => 'badge status-ordered',
            'Received'   => 'badge status-completed',
            'Paid'       => 'badge status-completed',
            'Cancelled'  => 'badge status-cancelled',
            default      => 'badge badge-gray',
        };
    }
}
