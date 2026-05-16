<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\AuditService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request)
    {
        $query = Customer::query();
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
        }
        $customers = $query->orderBy('name')->paginate(15)->withQueryString();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'phone'   => 'required|string|max:20',
            'email'   => 'required|email|max:100',
            'address' => 'required|string',
            'notes'   => 'nullable|string',
        ]);
        $customer = Customer::create($data);
        $this->auditService->logCreation('customer', $customer, "Customer {$customer->name} created");

        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'phone'   => 'required|string|max:20',
            'email'   => 'required|email|max:100',
            'address' => 'required|string',
            'notes'   => 'nullable|string',
        ]);
        $oldValues = $customer->toArray();
        $customer->update($data);
        $this->auditService->logUpdate('customer', $customer, $oldValues, "Customer {$customer->name} updated");

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $oldValues = $customer->toArray();
        $customer->delete();
        $this->auditService->log('customer', 'soft_delete', $customer, $oldValues, null, "Customer {$customer->name} archived");

        return redirect()->route('customers.index')->with('success', 'Customer deleted.');
    }
}
