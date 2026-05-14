<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function __construct(
        private AccountingService $accounting,
        private AuditService $auditService,
        private AccountsPayableService $accountsPayable,
        private InventoryLedgerService $inventoryLedger,
        private ApprovalService $approvalService
    ) {}

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

            $requiresApproval = $subtotal >= ApprovalService::HIGH_VALUE_PO_THRESHOLD;

            $purchase = Purchase::create([
                'po_number'      => $this->generatePoNumber(),
                'supplier_id'    => $data['supplier_id'],
                'purchase_date'  => $data['purchase_date'],
                'expected_date'  => $data['expected_date'] ?? null,
                'subtotal'       => $subtotal,
                'total'          => $subtotal,
                'status'         => $requiresApproval ? 'Pending Approval' : 'Approved',
                'notes'          => $data['notes'] ?? null,
            ]);

            foreach ($preparedItems as $itemData) {
                PurchaseItem::create(array_merge($itemData, ['purchase_id' => $purchase->id]));
            }

            $this->auditService->logCreation('purchase', $purchase, "Purchase order {$purchase->po_number} created");

            if ($requiresApproval) {
                $this->approvalService->request(
                    'purchase',
                    $purchase,
                    (float) $purchase->total,
                    "High-value purchase order {$purchase->po_number} requires manager approval"
                );
            }

            return $purchase;
        });
    }

    /** Receive purchase — update stock and post journal */
    public function receivePurchase(Purchase $purchase): Purchase
    {
        return DB::transaction(function () use ($purchase) {
            $purchase = Purchase::whereKey($purchase->id)->lockForUpdate()->firstOrFail();

            if (in_array($purchase->status, ['Received', 'Paid'], true)) {
                throw new \RuntimeException("Purchase order {$purchase->po_number} has already been received.");
            }
            if ($purchase->status === 'Cancelled') {
                throw new \RuntimeException("Purchase order {$purchase->po_number} has been cancelled.");
            }
            if ($purchase->status === 'Pending Approval') {
                throw new \RuntimeException("Purchase order {$purchase->po_number} must be approved before receiving.");
            }

            $purchase->load('items.product');

            foreach ($purchase->items as $item) {
                $product = Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail();
                $oldStock = $product->stock;
                $oldCost  = (float) $product->cost_price;
                $newQty   = $item->qty;
                $newCost  = (float) $item->unit_cost;

                // Weighted average costing
                $totalOldValue = $oldStock * $oldCost;
                $totalNewValue = $newQty * $newCost;
                $totalQty      = $oldStock + $newQty;
                $weightedAvgCost = $totalQty > 0
                    ? round(($totalOldValue + $totalNewValue) / $totalQty, 2)
                    : $newCost;

                $product->update([
                    'stock' => $totalQty,
                    'cost_price' => $weightedAvgCost,
                ]);

                $this->inventoryLedger->recordMovement(
                    $product,
                    now()->toDateString(),
                    'purchase_receipt',
                    $newQty,
                    0,
                    $weightedAvgCost,
                    'Purchase',
                    $purchase->id,
                    "Goods received from PO {$purchase->po_number}"
                );
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

            $this->accountsPayable->createInvoiceFromPurchase($purchase);

            $this->auditService->logStatusChange('purchase', $purchase, 'receive', "Purchase order {$purchase->po_number} received and posted");

            return $purchase;
        });
    }

    private function generatePoNumber(): string
    {
        $prefix = 'PO-' . date('Ym') . '-';
        $lastNumber = Purchase::where('po_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->max(DB::raw("CAST(SUBSTRING(po_number, " . (strlen($prefix) + 1) . ") AS UNSIGNED)"));
        $next = ($lastNumber ?? 0) + 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
