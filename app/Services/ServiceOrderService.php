<?php

namespace App\Services;

use App\Models\ServiceOrder;
use Illuminate\Support\Facades\DB;

class ServiceOrderService
{
    public function __construct(
        private AccountingService $accounting,
        private AuditService $auditService
    ) {}

    public function createOrder(array $data): ServiceOrder
    {
        return DB::transaction(function () use ($data) {
            $order = ServiceOrder::create([
                'order_number'       => $this->generateOrderNumber(),
                'customer_id'        => $data['customer_id'],
                'device_type'        => $data['device_type'],
                'brand'              => $data['brand'] ?? null,
                'serial_number'      => $data['serial_number'] ?? null,
                'problem_description'=> $data['problem_description'],
                'status'             => 'Received',
                'received_at'        => now(),
                'notes'              => $data['notes'] ?? null,
            ]);

            $this->auditService->logCreation('service_order', $order, "Service order {$order->order_number} created");

            return $order;
        });
    }

    public function updateProgress(ServiceOrder $order, array $data): ServiceOrder
    {
        $oldValues = $order->only(['status', 'diagnosis']);

        $order->update([
            'diagnosis' => $data['diagnosis'] ?? $order->diagnosis,
            'status'    => $data['status'],
        ]);

        $this->auditService->logUpdate('service_order', $order, $oldValues, "Service order {$order->order_number} progress updated");

        return $order;
    }

    /** Mark as Done (repair complete, awaiting payment) */
    public function markDone(ServiceOrder $order, float $serviceCost, string $diagnosis = null): ServiceOrder
    {
        $oldValues = $order->only(['status', 'service_cost', 'diagnosis']);

        $order->update([
            'status'       => 'Done',
            'service_cost' => $serviceCost,
            'diagnosis'    => $diagnosis ?? $order->diagnosis,
        ]);

        $this->auditService->logUpdate('service_order', $order, $oldValues, "Service order {$order->order_number} marked done");

        return $order;
    }

    /** Mark as Completed (customer paid) — posts journal entry */
    public function completeWithPayment(ServiceOrder $order): ServiceOrder
    {
        return DB::transaction(function () use ($order) {
            $order = ServiceOrder::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->status !== 'Done') {
                throw new \RuntimeException('Order must be in Done status before confirming payment.');
            }

            $order->update([
                'status'       => 'Completed',
                'completed_at' => now(),
            ]);

            $cost = (float) $order->service_cost;
            $date = now()->toDateString();

            $this->accounting->postJournal(
                $date,
                "Service Revenue #{$order->order_number}",
                'ServiceOrder',
                $order->id,
                [
                    ['code' => '1-1000', 'debit' => $cost, 'credit' => 0, 'description' => 'Cash receipt from service order'],
                    ['code' => '4-2000', 'debit' => 0, 'credit' => $cost, 'description' => 'Service revenue'],
                ]
            );

            $this->auditService->logStatusChange('service_order', $order, 'complete', "Service order {$order->order_number} completed and posted");

            return $order;
        });
    }

    private function generateOrderNumber(): string
    {
        $prefix = 'SRV-' . date('Ym') . '-';
        $lastNumber = ServiceOrder::where('order_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->max(DB::raw("CAST(SUBSTRING(order_number, " . (strlen($prefix) + 1) . ") AS UNSIGNED)"));
        $next = ($lastNumber ?? 0) + 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
