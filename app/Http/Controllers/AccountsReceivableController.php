<?php

namespace App\Http\Controllers;

use App\Models\ArInvoice;
use App\Models\ArPayment;
use App\Models\ChartOfAccount;
use App\Services\AccountsReceivableService;
use Illuminate\Http\Request;

class AccountsReceivableController extends Controller
{
    public function __construct(private AccountsReceivableService $receivableService) {}

    // ── Index ─────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $asOf = $request->as_of ?? now()->toDateString();

        $query = ArInvoice::with('customer', 'sale', 'payments')
            ->orderByDesc('invoice_date')
            ->orderByDesc('id');

        if ($request->search) {
            $query->where('invoice_number', 'like', "%{$request->search}%")
                ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        // Filter by combined status (invoice status + latest payment verification state)
        if ($request->status) {
            match($request->status) {
                // Payment-level filters — look at the latest payment's status
                'Pending Verification' => $query->whereHas('payments', fn($q) =>
                    $q->where('status', 'Pending Verification')
                      ->whereNotExists(fn($sub) =>
                          $sub->from('ar_payments as newer')
                              ->whereColumn('newer.ar_invoice_id', 'ar_payments.ar_invoice_id')
                              ->whereRaw('newer.created_at > ar_payments.created_at')
                      )
                ),
                'Payment Rejected' => $query->whereHas('payments', fn($q) =>
                    $q->where('status', 'Rejected')
                      ->whereNotExists(fn($sub) =>
                          $sub->from('ar_payments as newer')
                              ->whereColumn('newer.ar_invoice_id', 'ar_payments.ar_invoice_id')
                              ->whereRaw('newer.created_at > ar_payments.created_at')
                      )
                ),
                // Invoice-level filters
                default => $query->where('status', $request->status),
            };
        }

        $invoices = $query->paginate(20)->withQueryString();

        $agingInvoices = ArInvoice::with('customer')
            ->whereIn('status', ['Open', 'Partially Paid'])
            ->get();

        $aging = collect(['Current' => 0, '1-30' => 0, '31-60' => 0, '61-90' => 0, '90+' => 0]);
        foreach ($agingInvoices as $invoice) {
            $aging[$invoice->agingBucket($asOf)] += $invoice->outstanding;
        }

        $ledgerBalance      = ChartOfAccount::where('code', '1-1200')->first()?->getBalance(null, $asOf) ?? 0;
        $invoiceOutstanding = $agingInvoices->sum('outstanding');
        $openingReceivable  = max(0, round($ledgerBalance - $invoiceOutstanding, 2));
        if ($openingReceivable > 0) {
            $aging['90+'] += $openingReceivable;
        }

        // Pending verifications count — shown as a badge for Finance/Manager
        $pendingVerifications = ArPayment::where('status', ArPayment::STATUS_PENDING)->count();

        return view('accounts-receivable.index', compact(
            'invoices', 'aging', 'asOf', 'ledgerBalance',
            'invoiceOutstanding', 'openingReceivable', 'pendingVerifications'
        ));
    }

    // ── Show ──────────────────────────────────────────────────────────────

    public function show(ArInvoice $invoice)
    {
        $invoice->load('customer', 'sale', 'payments.verifiedByUser');
        return view('accounts-receivable.show', compact('invoice'));
    }

    // ── Step 1: Cashier submits payment ───────────────────────────────────

    public function storePayment(Request $request, ArInvoice $invoice)
    {
        $data = $request->validate([
            'payment_date'   => 'required|date',
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Cash,Transfer,Other',
            'reference'      => 'nullable|string|max:80',
            'notes'          => 'nullable|string|max:255',
        ]);

        try {
            $payment = $this->receivableService->recordPayment($invoice, $data);
            return back()->with('success',
                "Payment {$payment->payment_number} submitted and is now pending Finance verification.");
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    // ── Step 2a: Finance/Manager verifies ────────────────────────────────

    public function verifyPayment(Request $request, ArPayment $payment)
    {
        $request->validate([
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            $this->receivableService->verifyPayment($payment, $request->notes);
            return back()->with('success',
                "Payment {$payment->payment_number} verified. Journal entry posted and invoice updated.");
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ── Step 2b: Finance/Manager rejects ─────────────────────────────────

    public function rejectPayment(Request $request, ArPayment $payment)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        try {
            $this->receivableService->rejectPayment($payment, $request->rejection_reason);
            return back()->with('success',
                "Payment {$payment->payment_number} rejected. The invoice remains open.");
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
