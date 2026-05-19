<?php

namespace App\Services;

use App\Models\ApInvoice;
use App\Models\ApPayment;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

class AccountsPayableService
{
    public function __construct(
        private AccountingService $accounting,
        private AuditService $auditService
    ) {}

    public function createInvoiceFromPurchase(Purchase $purchase): ApInvoice
    {
        return DB::transaction(function () use ($purchase) {
            $purchase->loadMissing('supplier');

            $existing = ApInvoice::where('purchase_id', $purchase->id)->lockForUpdate()->first();
            if ($existing) {
                return $existing;
            }

            $invoice = ApInvoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'supplier_id' => $purchase->supplier_id,
                'purchase_id' => $purchase->id,
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'subtotal' => $purchase->subtotal,
                'total' => $purchase->total,
                'paid_amount' => 0,
                'status' => 'Open',
                'notes' => "Generated from goods receipt {$purchase->po_number}",
            ]);

            $this->auditService->logCreation('accounts_payable', $invoice, "AP invoice {$invoice->invoice_number} created from PO {$purchase->po_number}");

            return $invoice;
        });
    }

    public function recordPayment(ApInvoice $invoice, array $data): ApPayment
    {
        return DB::transaction(function () use ($invoice, $data) {
            $invoice = ApInvoice::whereKey($invoice->id)->lockForUpdate()->firstOrFail();

            if ($invoice->status === 'Cancelled' || $invoice->status === 'Paid') {
                throw new \RuntimeException("AP invoice {$invoice->invoice_number} is not open for payment.");
            }

            $amount = round((float) $data['amount'], 2);
            if ($amount <= 0 || $amount > $invoice->outstanding) {
                throw new \RuntimeException('Payment amount must be greater than zero and cannot exceed invoice outstanding balance.');
            }

            $payment = ApPayment::create([
                'payment_number' => $this->generatePaymentNumber(),
                'ap_invoice_id' => $invoice->id,
                'supplier_id' => $invoice->supplier_id,
                'payment_date' => $data['payment_date'],
                'amount' => $amount,
                'payment_method' => $data['payment_method'] ?? 'Cash',
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $newPaid = round((float) $invoice->paid_amount + $amount, 2);
            $invoice->update([
                'paid_amount' => $newPaid,
                'status' => $newPaid >= (float) $invoice->total ? 'Paid' : 'Partially Paid',
            ]);

            if ($invoice->purchase) {
                $invoice->purchase->update([
                    'status' => $newPaid >= (float) $invoice->total ? 'Paid' : 'Received',
                ]);
            }

            $cashCode = $payment->payment_method === 'Transfer' ? '1-1100' : '1-1000';
            $cashDesc = $payment->payment_method === 'Transfer' ? 'Bank payment to supplier' : 'Cash payment to supplier';

            $this->accounting->postJournal(
                $payment->payment_date->toDateString(),
                "AP Payment {$payment->payment_number} - Invoice {$invoice->invoice_number}",
                'ApPayment',
                $payment->id,
                [
                    ['code' => '2-1000', 'debit' => $amount, 'credit' => 0, 'description' => 'Settlement of accounts payable'],
                    ['code' => $cashCode, 'debit' => 0, 'credit' => $amount, 'description' => $cashDesc],
                ]
            );

            $this->auditService->logCreation('accounts_payable', $payment, "AP payment {$payment->payment_number} recorded for invoice {$invoice->invoice_number}");

            return $payment;
        });
    }

    private function generateInvoiceNumber(): string
    {
        $prefix = 'API-' . date('Ym') . '-';
        $lastNumber = ApInvoice::where('invoice_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->max(DB::raw("CAST(SUBSTRING(invoice_number, " . (strlen($prefix) + 1) . ") AS UNSIGNED)"));

        return $prefix . str_pad(($lastNumber ?? 0) + 1, 4, '0', STR_PAD_LEFT);
    }

    private function generatePaymentNumber(): string
    {
        $prefix = 'APP-' . date('Ym') . '-';
        $lastNumber = ApPayment::where('payment_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->max(DB::raw("CAST(SUBSTRING(payment_number, " . (strlen($prefix) + 1) . ") AS UNSIGNED)"));

        return $prefix . str_pad(($lastNumber ?? 0) + 1, 4, '0', STR_PAD_LEFT);
    }
}
