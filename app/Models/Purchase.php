<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'Draft'      => 'badge status-draft',
            'Ordered'    => 'badge status-ordered',
            'Received'   => 'badge status-completed',
            'Cancelled'  => 'badge status-cancelled',
            default      => 'badge badge-gray',
        };
    }
}
