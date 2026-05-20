<?php

namespace App\Services;

use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class InventoryLedgerService
{
    public function recordMovement(
        Product $product,
        string $date,
        string $type,
        int $qtyIn,
        int $qtyOut,
        float $unitCost,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null
    ): InventoryMovement {
        if ($qtyIn < 0 || $qtyOut < 0 || ($qtyIn === 0 && $qtyOut === 0)) {
            throw new \RuntimeException('Inventory movement must have a positive quantity in or out.');
        }

        return InventoryMovement::create([
            'movement_number' => $this->generateMovementNumber(),
            'product_id' => $product->id,
            'movement_date' => $date,
            'movement_type' => $type,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'qty_in' => $qtyIn,
            'qty_out' => $qtyOut,
            'balance_qty' => $product->stock,
            'unit_cost' => $unitCost,
            'total_cost' => ($qtyIn ?: $qtyOut) * $unitCost,
            'notes' => $notes,
        ]);
    }

    private function generateMovementNumber(): string
    {
        $prefix = 'INV-' . date('Ym') . '-';
        $lastNumber = InventoryMovement::where('movement_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->max(DB::raw("CAST(SUBSTRING(movement_number, " . (strlen($prefix) + 1) . ") AS UNSIGNED)"));

        return $prefix . str_pad(($lastNumber ?? 0) + 1, 4, '0', STR_PAD_LEFT);
    }
}
