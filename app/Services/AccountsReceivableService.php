<?php

namespace App\Services;

use App\Models\ArInvoice;
use App\Models\ArPayment;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountsReceivableService
{
    public function __construct(
        private AccountingService $accounting,
        private AuditService $auditService
    ) {}

    // ── Invoice creation (unchanged) ──────────────────────────────────────

    public function createInvoiceFromSale(Sale $sale): ArInvoice
    {
        return DB::transaction(function () use ($sale) {
            $sale->loadMissing('customer');

            $invoice = ArInvoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'customer_id'    => $sale->customer_id,
                'sale_id'        => $sale->id,
                'invoice_date'   => $sale->sale_date,
                'due_date'       => $sale->is_credit_sale
                    ? $sale->sale_date->copy()->addDays((int) $sale->payment_terms_days)
                    : $sale->sale_date,
                'subtotal'       => $sale->subtotal,
                'discount'       => $sale->discount,
                'total'          => $sale->total,
                'paid_amount'    => 0,
                'status'         => 'Open',
                'notes'          => "Generated from sale {$sale->sale_number}",
            ]);

            $this->accounting->postJournal(
                $sale->sale_date->toDateString(),
                "AR Invoice {$invoice->invoice_number} - Sale {$sale->sale_number}",
                'ArInvoice',
                $invoice->id,
                [
                    ['code' => '1-1200', 'debit' => $invoice->total, 'credit' => 0,              'description' => 'Accounts receivable from customer invoice'],
                    ['code' => '4-1000', 'debit' => 0,               'credit' => $invoice->total, 'description' => 'Product sales revenue'],
                ]
            );

            $this->auditService->logCreation('accounts_receivable', $invoice,
                "AR invoice {$invoice->invoice_number} created from sale {$sale->sale_number}");

            return $invoice;
        });
    }

    // ── Step 1: Cashier submits payment (Pending Verification) ────────────

    /**
     * Cashier records that cash/transfer was received.
     * The payment is saved as "Pending Verification" — NO journal is posted yet.
     * The invoice outstanding does NOT change yet.
     * Finance/Manager must verify before the journal posts and the invoice updates.
     */
    public function recordPayment(ArInvoice $invoice, array $data): ArPayment
    {
        return DB::transaction(function () use ($invoice, $data) {
            $invoice = ArInvoice::whereKey($invoice->id)->lockForUpdate()->firstOrFail();

            if (in_array($invoice->status, ['Cancelled', 'Paid'], true)) {
                throw new \RuntimeException("AR invoice {$invoice->invoice_number} is not open for payment.");
            }

            $amount = round((float) $data['amount'], 2);
            if ($amount <= 0) {
                throw new \RuntimeException('Payment amount must be greater than zero.');
            }
            if ($amount > $invoice->outstanding) {
                throw new \RuntimeException('Payment amount cannot exceed the invoice outstanding balance of Rp ' . number_format($invoice->outstanding, 0, ',', '.'));
            }

            // Check for existing pending payment — prevent duplicate submissions
            $pendingExists = $invoice->payments()
                ->where('status', ArPayment::STATUS_PENDING)
                ->exists();
            if ($pendingExists) {
                throw new \RuntimeException('This invoice already has a payment pending verification. Please wait for Finance to verify it before submitting another.');
            }

            $payment = ArPayment::create([
                'payment_number' => $this->generatePaymentNumber(),
                'ar_invoice_id'  => $invoice->id,
                'customer_id'    => $invoice->customer_id,
                'payment_date'   => $data['payment_date'],
                'amount'         => $amount,
                'payment_method' => $data['payment_method'] ?? 'Cash',
                'reference'      => $data['reference'] ?? null,
                'notes'          => $data['notes'] ?? null,
                'status'         => ArPayment::STATUS_PENDING,
            ]);

            $this->auditService->logCreation('accounts_receivable', $payment,
                "AR payment {$payment->payment_number} submitted by cashier — pending verification");

            return $payment;
        });
    }

    // ── Step 2a: Finance/Manager verifies → journal posts ─────────────────

    /**
     * Finance or Manager confirms the payment was actually received.
     * This is the point where the journal entry is posted and the invoice status updates.
     */
    public function verifyPayment(ArPayment $payment, ?string $notes = null): ArPayment
    {
        return DB::transaction(function () use ($payment, $notes) {
            $payment = ArPayment::whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if (!$payment->isPending()) {
                throw new \RuntimeException("Payment {$payment->payment_number} is not pending verification.");
            }

            // Reload invoice with lock
            $invoice = ArInvoice::whereKey($payment->ar_invoice_id)->lockForUpdate()->firstOrFail();

            if (in_array($invoice->status, ['Cancelled'], true)) {
                throw new \RuntimeException("Cannot verify payment — invoice {$invoice->invoice_number} has been cancelled.");
            }

            $amount = (float) $payment->amount;

            // Mark payment as verified
            $payment->update([
                'status'      => ArPayment::STATUS_VERIFIED,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'notes'       => $notes ?: $payment->notes,
            ]);

            // NOW update the invoice paid amount and status
            $newPaid = round((float) $invoice->paid_amount + $amount, 2);
            $invoice->update([
                'paid_amount' => $newPaid,
                'status'      => $newPaid >= (float) $invoice->total ? 'Paid' : 'Partially Paid',
            ]);

            // NOW post the journal entry
            $cashCode = $payment->payment_method === 'Transfer' ? '1-1100' : '1-1000';
            $cashDesc = $payment->payment_method === 'Transfer'
                ? 'Bank receipt from customer (verified)'
                : 'Cash receipt from customer (verified)';

            $this->accounting->postJournal(
                $payment->payment_date->toDateString(),
                "AR Payment {$payment->payment_number} — Invoice {$invoice->invoice_number}",
                'ArPayment',
                $payment->id,
                [
                    ['code' => $cashCode,  'debit' => $amount, 'credit' => 0,       'description' => $cashDesc],
                    ['code' => '1-1200',   'debit' => 0,       'credit' => $amount, 'description' => 'Settlement of accounts receivable'],
                ]
            );

            $this->auditService->logStatusChange('accounts_receivable', $payment, 'verify',
                "AR payment {$payment->payment_number} verified by " . Auth::user()?->name . " — journal posted");

            return $payment;
        });
    }

    // ── Step 2b: Finance/Manager rejects → recalculate invoice ──────────

    /**
     * Finance or Manager rejects the payment (e.g. cash not actually received,
     * wrong amount, suspicious entry).
     * The invoice paid_amount is recalculated from VERIFIED payments only,
     * so the invoice status correctly reverts if needed. No journal is posted.
     */
    public function rejectPayment(ArPayment $payment, string $reason): ArPayment
    {
        return DB::transaction(function () use ($payment, $reason) {
            $payment = ArPayment::whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if (!$payment->isPending()) {
                throw new \RuntimeException("Payment {$payment->payment_number} is not pending verification.");
            }

            // Mark as rejected
            $payment->update([
                'status'           => ArPayment::STATUS_REJECTED,
                'verified_by'      => Auth::id(),
                'verified_at'      => now(),
                'rejection_reason' => $reason,
            ]);

            // Recalculate invoice from VERIFIED payments only
            $invoice = ArInvoice::whereKey($payment->ar_invoice_id)->lockForUpdate()->firstOrFail();

            $verifiedTotal = round(
                (float) $invoice->payments()
                    ->where('status', ArPayment::STATUS_VERIFIED)
                    ->sum('amount'),
                2
            );

            $correctStatus = match(true) {
                $verifiedTotal <= 0                         => 'Open',
                $verifiedTotal >= (float) $invoice->total   => 'Paid',
                default                                     => 'Partially Paid',
            };

            $invoice->update([
                'paid_amount' => $verifiedTotal,
                'status'      => $correctStatus,
            ]);

            $this->auditService->logStatusChange('accounts_receivable', $payment, 'reject',
                "AR payment {$payment->payment_number} rejected by " . Auth::user()?->name . ": {$reason}");

            return $payment;
        });
    }

    // ── Number generators ─────────────────────────────────────────────────

    private function generateInvoiceNumber(): string
    {
        $prefix = 'ARI-' . date('Ym') . '-';
        $last   = ArInvoice::where('invoice_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->max(DB::raw("CAST(SUBSTRING(invoice_number, " . (strlen($prefix) + 1) . ") AS UNSIGNED)"));
        return $prefix . str_pad(($last ?? 0) + 1, 4, '0', STR_PAD_LEFT);
    }

    private function generatePaymentNumber(): string
    {
        $prefix = 'ARP-' . date('Ym') . '-';
        $last   = ArPayment::where('payment_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->max(DB::raw("CAST(SUBSTRING(payment_number, " . (strlen($prefix) + 1) . ") AS UNSIGNED)"));
        return $prefix . str_pad(($last ?? 0) + 1, 4, '0', STR_PAD_LEFT);
    }
}
