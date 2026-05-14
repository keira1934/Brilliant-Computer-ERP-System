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
        $suppliers = $query->latest()->paginate(15)->withQueryString();
        return view('suppliers.index', compact('suppliers'));
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
