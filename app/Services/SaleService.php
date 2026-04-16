<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(private AccountingService $accounting) {}

    public function createSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $items    = $data['items'];
            $subtotal = 0;
            $cogs     = 0;

            $preparedItems = [];
            foreach ($items as $item) {
                if (empty($item['product_id'])) continue;

                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['qty']) {
                    throw new \RuntimeException("Insufficient stock for: {$product->name}. Available: {$product->stock}.");
                }

                $lineTotal = $item['qty'] * $item['unit_price'];
                $lineCogs  = $item['qty'] * $product->cost_price;
                $subtotal += $lineTotal;
                $cogs     += $lineCogs;

                $preparedItems[] = [
                    'product_id' => $product->id,
                    'qty'        => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'unit_cost'  => $product->cost_price,
                    'total'      => $lineTotal,
                ];
            }

            if (empty($preparedItems)) {
                throw new \RuntimeException('No valid items in this sale.');
            }

            $discount = (float) ($data['discount'] ?? 0);
            $total    = $subtotal - $discount;

            // Determine which cash/bank account to use
            $paymentMethod = $data['payment_method'] ?? 'Cash';
            $cashCode      = $paymentMethod === 'Transfer' ? '1-1100' : '1-1000';
            $paymentDesc   = $paymentMethod === 'Transfer' ? 'Bank receipt from sale' : 'Cash receipt from sale';

            // Create sale record
            $sale = Sale::create([
                'sale_number'    => $this->generateSaleNumber(),
                'customer_id'    => $data['customer_id'] ?: null,
                'sale_date'      => $data['sale_date'],
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'total'          => $total,
                'payment_method' => $paymentMethod,
                'notes'          => $data['notes'] ?? null,
            ]);

            // Create sale items & decrement stock
            foreach ($preparedItems as $itemData) {
                SaleItem::create(array_merge($itemData, ['sale_id' => $sale->id]));
                Product::where('id', $itemData['product_id'])->decrement('stock', $itemData['qty']);
            }

            // Journal: Dr Cash/Bank / Cr Revenue
            $this->accounting->postJournal(
                $data['sale_date'],
                "Sale #{$sale->sale_number}",
                'Sale',
                $sale->id,
                [
                    ['code' => $cashCode, 'debit' => $total,  'credit' => 0,     'description' => $paymentDesc],
                    ['code' => '4-1000',  'debit' => 0,       'credit' => $total,'description' => 'Product sales revenue'],
                ]
            );

            // Journal: Dr COGS / Cr Inventory
            if ($cogs > 0) {
                $this->accounting->postJournal(
                    $data['sale_date'],
                    "COGS — Sale #{$sale->sale_number}",
                    'Sale',
                    $sale->id,
                    [
                        ['code' => '5-1000', 'debit' => $cogs, 'credit' => 0,    'description' => 'Cost of goods sold'],
                        ['code' => '1-2000', 'debit' => 0,     'credit' => $cogs,'description' => 'Inventory outflow'],
                    ]
                );
            }

            return $sale;
        });
    }

    private function generateSaleNumber(): string
    {
        $prefix = 'SL-' . date('Ym') . '-';
        $last   = Sale::where('sale_number', 'like', $prefix . '%')->count() + 1;
        return $prefix . str_pad($last, 4, '0', STR_PAD_LEFT);
    }
}
