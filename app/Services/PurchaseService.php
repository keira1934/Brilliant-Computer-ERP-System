<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function __construct(private AccountingService $accounting) {}

    public function createPurchase(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            $items    = $data['items'];
            $subtotal = 0;

            $preparedItems = [];
            foreach ($items as $item) {
                $lineTotal = $item['qty'] * $item['unit_cost'];
                $subtotal += $lineTotal;
                $preparedItems[] = [
                    'product_id' => $item['product_id'],
                    'qty'        => $item['qty'],
                    'unit_cost'  => $item['unit_cost'],
                    'total'      => $lineTotal,
                ];
            }

            $purchase = Purchase::create([
                'po_number'      => $this->generatePoNumber(),
                'supplier_id'    => $data['supplier_id'],
                'purchase_date'  => $data['purchase_date'],
                'expected_date'  => $data['expected_date'] ?? null,
                'subtotal'       => $subtotal,
                'total'          => $subtotal,
                'status'         => 'Ordered',
                'notes'          => $data['notes'] ?? null,
            ]);

            foreach ($preparedItems as $itemData) {
                PurchaseItem::create(array_merge($itemData, ['purchase_id' => $purchase->id]));
            }

            return $purchase;
        });
    }

    /** Receive purchase — update stock and post journal */
    public function receivePurchase(Purchase $purchase): Purchase
    {
        return DB::transaction(function () use ($purchase) {
            $purchase->load('items.product');

            foreach ($purchase->items as $item) {
                Product::where('id', $item->product_id)->increment('stock', $item->qty);
                // Also update product cost price
                $item->product->update(['cost_price' => $item->unit_cost]);
            }

            $purchase->update(['status' => 'Received']);

            // Post journal: Dr Persediaan / Cr Hutang Usaha
            $this->accounting->postJournal(
                now()->toDateString(),
                "Penerimaan Barang PO #{$purchase->po_number}",
                'Purchase',
                $purchase->id,
                [
                    ['code' => '1-2000', 'debit' => $purchase->total, 'credit' => 0,                  'description' => 'Penambahan persediaan'],
                    ['code' => '2-1000', 'debit' => 0,                'credit' => $purchase->total,   'description' => 'Hutang usaha ke supplier'],
                ]
            );

            return $purchase;
        });
    }

    private function generatePoNumber(): string
    {
        $prefix = 'PO-' . date('Ym') . '-';
        $last   = Purchase::where('po_number', 'like', $prefix . '%')->count() + 1;
        return $prefix . str_pad($last, 4, '0', STR_PAD_LEFT);
    }
}
