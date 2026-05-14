<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    protected $fillable = [
        'movement_number', 'product_id', 'movement_date', 'movement_type',
        'reference_type', 'reference_id', 'qty_in', 'qty_out', 'balance_qty',
        'unit_cost', 'total_cost', 'notes',
    ];

    protected $casts = [
        'movement_date' => 'date',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
