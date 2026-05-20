<?php

namespace App\Services;

use App\Models\ArInvoice;
use App\Models\ArPayment;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class AccountsReceivableService
{
    public function __construct(
        private AccountingService $accounting,
        private AuditService $auditService
    ) {}

    public function createInvoiceFromSale(Sale $sale): ArInvoice
    {
        return DB::transaction(function () use ($sale) {
            $sale->loadMissing('customer');

            $invoice = ArInvoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'customer_id' => $sale->customer_id,
                'sale_id' => $sale->id,
                'invoice_date' => $sale->sale_date,
                'due_date' => $sale->is_credit_sale
                    ? $sale->sale_date->copy()->addDays((int) $sale->payment_terms_days)
                    : $sale->sale_date,
                'subtotal' => $sale->subtotal,
                'discount' => $sale->discount,
                'total' => $sale->total,
                'paid_amount' => 0,
                'status' => 'Open',
                'notes' => "Generated from sale {$sale->sale_number}",
            ]);

            $this->accounting->postJournal(
                $sale->sale_date->toDateString(),
                "AR Invoice {$invoice->invoice_number} - Sale {$sale->sale_number}",
                'ArInvoice',
                $invoice->id,
                [
                    ['code' => '1-1200', 'debit' => $invoice->total, 'credit' => 0, 'description' => 'Accounts receivable from customer invoice'],
                    ['code' => '4-1000', 'debit' => 0, 'credit' => $invoice->total, 'description' => 'Product sales revenue'],
                ]
            );

            $this->auditService->logCreation('accounts_receivable', $invoice, "AR invoice {$invoice->invoice_number} created from sale {$sale->sale_number}");

            return $invoice;
        });
    }

    public function recordPayment(ArInvoice $invoice, array $data): ArPayment
    {
        return DB::transaction(function () use ($invoice, $data) {
            $invoice = ArInvoice::whereKey($invoice->id)->lockForUpdate()->firstOrFail();

            if ($invoice->status === 'Cancelled' || $invoice->status === 'Paid') {
                throw new \RuntimeException("AR invoice {$invoice->invoice_number} is not open for payment.");
            }

            $amount = round((float) $data['amount'], 2);
            if ($amount <= 0 || $amount > $invoice->outstanding) {
                throw new \RuntimeException('Payment amount must be greater than zero and cannot exceed invoice outstanding balance.');
            }

            $payment = ArPayment::create([
                'payment_number' => $this->generatePaymentNumber(),
                'ar_invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer_id,
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

            $cashCode = $payment->payment_method === 'Transfer' ? '1-1100' : '1-1000';
            $cashDesc = $payment->payment_method === 'Transfer' ? 'Bank receipt from customer' : 'Cash receipt from customer';

            $this->accounting->postJournal(
                $payment->payment_date->toDateString(),
                "AR Payment {$payment->payment_number} - Invoice {$invoice->invoice_number}",
                'ArPayment',
                $payment->id,
                [
                    ['code' => $cashCode, 'debit' => $amount, 'credit' => 0, 'description' => $cashDesc],
                    ['code' => '1-1200', 'debit' => 0, 'credit' => $amount, 'description' => 'Settlement of accounts receivable'],
                ]
            );

            $this->auditService->logCreation('accounts_receivable', $payment, "AR payment {$payment->payment_number} recorded for invoice {$invoice->invoice_number}");

            return $payment;
        });
    }

    private function generateInvoiceNumber(): string
    {
        $prefix = 'ARI-' . date('Ym') . '-';
        $lastNumber = ArInvoice::where('invoice_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->max(DB::raw("CAST(SUBSTRING(invoice_number, " . (strlen($prefix) + 1) . ") AS UNSIGNED)"));

        return $prefix . str_pad(($lastNumber ?? 0) + 1, 4, '0', STR_PAD_LEFT);
    }

    private function generatePaymentNumber(): string
    {
        $prefix = 'ARP-' . date('Ym') . '-';
        $lastNumber = ArPayment::where('payment_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->max(DB::raw("CAST(SUBSTRING(payment_number, " . (strlen($prefix) + 1) . ") AS UNSIGNED)"));

        return $prefix . str_pad(($lastNumber ?? 0) + 1, 4, '0', STR_PAD_LEFT);
    }
}
