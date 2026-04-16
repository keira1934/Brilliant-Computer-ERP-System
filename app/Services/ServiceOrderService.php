<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\ServiceOrder;
use Illuminate\Support\Facades\DB;

class ServiceOrderService
{
    public function __construct(private AccountingService $accounting) {}

    public function createOrder(array $data): ServiceOrder
    {
        return ServiceOrder::create([
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
    }

    public function updateProgress(ServiceOrder $order, array $data): ServiceOrder
    {
        $order->update([
            'diagnosis' => $data['diagnosis'] ?? $order->diagnosis,
            'status'    => $data['status'],
        ]);
        return $order;
    }

    /** Mark as Done (repair complete, awaiting payment) */
    public function markDone(ServiceOrder $order, float $serviceCost, string $diagnosis = null): ServiceOrder
    {
        $order->update([
            'status'       => 'Done',
            'service_cost' => $serviceCost,
            'diagnosis'    => $diagnosis ?? $order->diagnosis,
        ]);
        return $order;
    }

    /** Mark as Completed (customer paid) — posts journal entry */
    public function completeWithPayment(ServiceOrder $order): ServiceOrder
    {
        return DB::transaction(function () use ($order) {
            $order->update([
                'status'       => 'Completed',
                'completed_at' => now(),
            ]);

            $cost = (float) $order->service_cost;
            $date = now()->toDateString();

            $this->accounting->postJournal(
                $date,
                "Pendapatan Servis #{$order->order_number}",
                'ServiceOrder',
                $order->id,
                [
                    ['code' => '1-1000', 'debit' => $cost, 'credit' => 0,    'description' => 'Penerimaan kas servis'],
                    ['code' => '4-2000', 'debit' => 0,     'credit' => $cost,'description' => 'Pendapatan jasa servis'],
                ]
            );

            return $order;
        });
    }

    private function generateOrderNumber(): string
    {
        $prefix = 'SRV-' . date('Ym') . '-';
        $last   = ServiceOrder::where('order_number', 'like', $prefix . '%')->count() + 1;
        return $prefix . str_pad($last, 4, '0', STR_PAD_LEFT);
    }
}
