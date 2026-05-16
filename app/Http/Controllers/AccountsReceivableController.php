<?php

namespace App\Http\Controllers;

use App\Models\ArInvoice;
use App\Services\AccountsReceivableService;
use Illuminate\Http\Request;

class AccountsReceivableController extends Controller
{
    public function __construct(private AccountsReceivableService $receivableService) {}

    public function index(Request $request)
    {
        $asOf = $request->as_of ?? now()->toDateString();

        $query = ArInvoice::with('customer', 'sale')
            ->orderByDesc('invoice_date')
            ->orderByDesc('id');
        if ($request->status) $query->where('status', $request->status);
        if ($request->search) {
            $query->where('invoice_number', 'like', "%{$request->search}%")
                ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $invoices = $query->paginate(20)->withQueryString();
        $agingInvoices = ArInvoice::with('customer')
            ->whereIn('status', ['Open', 'Partially Paid'])
            ->get();

        $aging = collect(['Current' => 0, '1-30' => 0, '31-60' => 0, '61-90' => 0, '90+' => 0]);
        foreach ($agingInvoices as $invoice) {
            $aging[$invoice->agingBucket($asOf)] += $invoice->outstanding;
        }

        return view('accounts-receivable.index', compact('invoices', 'aging', 'asOf'));
    }

    public function show(ArInvoice $invoice)
    {
        $invoice->load('customer', 'sale', 'payments');
        return view('accounts-receivable.show', compact('invoice'));
    }

    public function storePayment(Request $request, ArInvoice $invoice)
    {
        $data = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Cash,Transfer,Other',
            'reference' => 'nullable|string|max:80',
            'notes' => 'nullable|string',
        ]);

        try {
            $payment = $this->receivableService->recordPayment($invoice, $data);
            return back()->with('success', "Customer payment {$payment->payment_number} recorded and posted.");
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
