<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct(private PurchaseService $purchaseService) {}

    public function index(Request $request)
    {
        $query = Purchase::with('supplier')->latest();
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
        $purchase->load('supplier', 'items.product');
        return view('purchases.show', compact('purchase'));
    }

    public function receive(Purchase $purchase)
    {
        if ($purchase->status === 'Received') {
            return back()->with('error', 'Goods for this PO have already been received.');
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
        $purchase->items()->delete();
        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', 'Purchase order deleted.');
    }
}
