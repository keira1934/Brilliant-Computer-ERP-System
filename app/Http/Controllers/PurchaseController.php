<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\ApprovalService;
use App\Services\AuditService;
use App\Services\PurchaseService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct(
        private PurchaseService $purchaseService,
        private AuditService $auditService,
        private ApprovalService $approvalService
    ) {}

    public function index(Request $request)
    {
        $query = Purchase::with('supplier')->orderByDesc('purchase_date')->orderByDesc('id');
        if ($request->status) $query->where('status', $request->status);
        if ($request->search) {
            $query->where('po_number', 'like', "%{$request->search}%");
        }
        $purchases = $query->paginate(15)->withQueryString();
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products  = Product::orderBy('name')->get();
        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'        => 'required|exists:suppliers,id',
            'purchase_date'      => 'required|date',
            'expected_date'      => 'nullable|date',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|integer|min:1',
            'items.*.unit_cost'  => 'required|numeric|min:0',
        ]);

        $purchase = $this->purchaseService->createPurchase($request->all());
        return redirect()->route('purchases.show', $purchase)
            ->with('success', "Purchase Order #{$purchase->po_number} created successfully.");
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('supplier', 'items.product', 'apInvoices.payments');
        return view('purchases.show', compact('purchase'));
    }

    public function receive(Purchase $purchase)
    {
        if (in_array($purchase->status, ['Received', 'Paid'], true)) {
            return back()->with('error', 'Goods for this PO have already been received.');
        }
        if ($purchase->status === 'Cancelled') {
            return back()->with('error', 'Cancelled purchase orders cannot be received.');
        }
        try {
            $this->purchaseService->receivePurchase($purchase);
            return back()->with('success', 'Goods received. Stock updated & journal entry posted.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Purchase $purchase)
    {
        if ($purchase->status !== 'Draft') {
            return back()->with('error', 'Only Draft purchase orders can be deleted.');
        }
        $purchase->update(['status' => 'Cancelled']);
        $this->auditService->logStatusChange('purchase', $purchase, 'cancel', "Draft purchase order {$purchase->po_number} cancelled");

        return redirect()->route('purchases.index')->with('success', 'Purchase order cancelled.');
    }

    public function approve(Request $request, Purchase $purchase)
    {
        $request->validate([
            'notes' => 'nullable|string|max:255',
        ]);

        $approval = $purchase->approvals()->where('status', 'Pending')->latest()->first();
        if (!$approval) {
            return back()->with('error', 'No pending approval was found for this purchase order.');
        }

        try {
            $this->approvalService->approve($approval, $request->notes);
            $purchase->update(['status' => 'Approved']);
            $this->auditService->logStatusChange('purchase', $purchase, 'approve', "Purchase order {$purchase->po_number} approved");

            return back()->with('success', "Purchase order {$purchase->po_number} approved.");
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
