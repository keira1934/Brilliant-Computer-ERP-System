<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\AuditService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request)
    {
        $query = Supplier::query();
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('contact_person', 'like', "%{$request->search}%");
        }
        $suppliers = $query->orderBy('name')->paginate(15)->withQueryString();
        return view('suppliers.index', compact('suppliers'));
    }

    public function show(Supplier $supplier)
    {
        $purchases = $supplier->purchases()
            ->with('items.product')
            ->orderByDesc('purchase_date')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $apInvoices = $supplier->apInvoices()
            ->orderByDesc('invoice_date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $totalPurchases     = $supplier->purchases()->sum('total');
        $totalOrders        = $supplier->purchases()->count();
        $outstandingAP      = $supplier->apInvoices()
            ->whereIn('status', ['Open', 'Partially Paid'])
            ->sum(\Illuminate\Support\Facades\DB::raw('total - paid_amount'));

        // Products purchased from this supplier (distinct)
        $productIds = \App\Models\PurchaseItem::whereHas('purchase', fn($q) => $q->where('supplier_id', $supplier->id))
            ->distinct()
            ->pluck('product_id');
        $products = \App\Models\Product::withTrashed()->whereIn('id', $productIds)->get();

        return view('suppliers.show', compact(
            'supplier', 'purchases', 'apInvoices',
            'totalPurchases', 'totalOrders', 'outstandingAP', 'products'
        ));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'contact_person' => 'nullable|string|max:100',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:100',
            'address'        => 'nullable|string',
        ]);
        $supplier = Supplier::create($data);
        $this->auditService->logCreation('supplier', $supplier, "Supplier {$supplier->name} created");

        return redirect()->route('suppliers.index')->with('success', "Supplier '{$data['name']}' added successfully.");
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'contact_person' => 'nullable|string|max:100',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:100',
            'address'        => 'nullable|string',
        ]);
        $oldValues = $supplier->toArray();
        $supplier->update($data);
        $this->auditService->logUpdate('supplier', $supplier, $oldValues, "Supplier {$supplier->name} updated");

        return redirect()->route('suppliers.index')->with('success', "Supplier '{$supplier->name}' updated successfully.");
    }

    public function destroy(Supplier $supplier)
    {
        $oldValues = $supplier->toArray();
        $supplier->delete();
        $this->auditService->log('supplier', 'soft_delete', $supplier, $oldValues, null, "Supplier {$supplier->name} archived");

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted.');
    }
}
