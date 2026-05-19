<?php

namespace App\Http\Controllers;

use App\Models\ApInvoice;
use App\Models\ChartOfAccount;
use App\Services\AccountsPayableService;
use Illuminate\Http\Request;

class AccountsPayableController extends Controller
{
    public function __construct(private AccountsPayableService $payableService) {}

    public function index(Request $request)
    {
        $asOf = $request->as_of ?? now()->toDateString();

        $query = ApInvoice::with('supplier', 'purchase')
            ->orderByDesc('invoice_date')
            ->orderByDesc('id');
        if ($request->status) $query->where('status', $request->status);
        if ($request->search) {
            $query->where('invoice_number', 'like', "%{$request->search}%")
                ->orWhereHas('supplier', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $invoices = $query->paginate(20)->withQueryString();
        $agingInvoices = ApInvoice::with('supplier')
            ->whereIn('status', ['Open', 'Partially Paid'])
            ->get();

        $aging = collect(['Current' => 0, '1-30' => 0, '31-60' => 0, '61-90' => 0, '90+' => 0]);
        foreach ($agingInvoices as $invoice) {
            $aging[$invoice->agingBucket($asOf)] += $invoice->outstanding;
        }
        $ledgerBalance = ChartOfAccount::where('code', '2-1000')->first()?->getBalance(null, $asOf) ?? 0;
        $invoiceOutstanding = $agingInvoices->sum('outstanding');
        $openingPayable = max(0, round($ledgerBalance - $invoiceOutstanding, 2));
        if ($openingPayable > 0) {
            $aging['90+'] += $openingPayable;
        }

        return view('accounts-payable.index', compact('invoices', 'aging', 'asOf', 'ledgerBalance', 'invoiceOutstanding', 'openingPayable'));
    }

    public function show(ApInvoice $invoice)
    {
        $invoice->load('supplier', 'purchase', 'payments');
        return view('accounts-payable.show', compact('invoice'));
    }

    public function storePayment(Request $request, ApInvoice $invoice)
    {
        $data = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Cash,Transfer,Other',
            'reference' => 'nullable|string|max:80',
            'notes' => 'nullable|string',
        ]);

        try {
            $payment = $this->payableService->recordPayment($invoice, $data);
            return back()->with('success', "Supplier payment {$payment->payment_number} recorded and posted.");
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
