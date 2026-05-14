<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(
        private AccountingService $accounting,
        private AuditService $auditService,
        private AccountsReceivableService $accountsReceivable,
        private InventoryLedgerService $inventoryLedger
    ) {}

    public function createSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $items    = $data['items'];
            $subtotal = 0;
            $cogs     = 0;

            $preparedItems = [];
            $lockedProducts = [];
            $reservedQty = [];

            foreach ($items as $item) {
                if (empty($item['product_id'])) continue;

                $productId = (int) $item['product_id'];
                if (!isset($lockedProducts[$productId])) {
                    $lockedProducts[$productId] = Product::whereKey($productId)->lockForUpdate()->firstOrFail();
                    $reservedQty[$productId] = 0;
                }

                $product = $lockedProducts[$productId];
                $qty = (int) $item['qty'];
                $availableStock = $product->stock - $reservedQty[$productId];

                if ($availableStock < $qty) {
                    throw new \RuntimeException("Insufficient stock for: {$product->name}. Available: {$availableStock}.");
                }

                $reservedQty[$productId] += $qty;

                $lineTotal = $qty * $item['unit_price'];
                $lineCogs  = $qty * $product->cost_price;
                $subtotal += $lineTotal;
                $cogs     += $lineCogs;

                $preparedItems[] = [
                    'product_id' => $product->id,
                    'qty'        => $qty,
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
            $isCreditSale = !empty($data['is_credit_sale']);

            if ($isCreditSale && empty($data['customer_id'])) {
                throw new \RuntimeException('Credit sales require a registered customer.');
            }

            // Determine which cash/bank account to use
            $paymentMethod = $data['payment_method'] ?? 'Cash';

            // Create sale record
            $sale = Sale::create([
                'sale_number'    => $this->generateSaleNumber(),
                'customer_id'    => $data['customer_id'] ?? null,
                'sale_date'      => $data['sale_date'],
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'total'          => $total,
                'payment_method' => $paymentMethod,
                'is_credit_sale' => $isCreditSale,
                'payment_terms_days' => $isCreditSale ? (int) ($data['payment_terms_days'] ?? 30) : 0,
                'notes'          => $data['notes'] ?? null,
            ]);

            // Create sale items & decrement stock
            foreach ($preparedItems as $itemData) {
                SaleItem::create(array_merge($itemData, ['sale_id' => $sale->id]));
            }

            foreach ($reservedQty as $productId => $qty) {
                Product::whereKey($productId)->decrement('stock', $qty);
                $product = Product::whereKey($productId)->firstOrFail();
                $this->inventoryLedger->recordMovement(
                    $product,
                    $data['sale_date'],
                    'sale_issue',
                    0,
                    $qty,
                    (float) $lockedProducts[$productId]->cost_price,
                    'Sale',
                    $sale->id,
                    "Stock issued for sale {$sale->sale_number}"
                );
            }

            // AIS revenue cycle: invoice first, then settle AR if payment is immediate.
            $invoice = $this->accountsReceivable->createInvoiceFromSale($sale);

            if (!$sale->is_credit_sale) {
                $this->accountsReceivable->recordPayment($invoice, [
                    'payment_date' => $data['sale_date'],
                    'amount' => $total,
                    'payment_method' => $paymentMethod,
                    'reference' => $sale->sale_number,
                    'notes' => 'Immediate payment at sale',
                ]);
            }

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

            $this->auditService->logCreation('sale', $sale, "Sale {$sale->sale_number} created and posted");

            return $sale;
        });
    }

    private function generateSaleNumber(): string
    {
        $prefix = 'SL-' . date('Ym') . '-';
        $lastNumber = Sale::where('sale_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->max(DB::raw("CAST(SUBSTRING(sale_number, " . (strlen($prefix) + 1) . ") AS UNSIGNED)"));
        $next = ($lastNumber ?? 0) + 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
