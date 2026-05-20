<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ServiceOrder;
use App\Services\ServiceOrderService;
use Illuminate\Http\Request;

class ServiceOrderController extends Controller
{
    public function __construct(private ServiceOrderService $service) {}

    public function index(Request $request)
    {
        $query = ServiceOrder::with('customer')->orderByDesc('received_at')->orderByDesc('id');
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $query->where('order_number', 'like', "%{$request->search}%")
                  ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }
        $orders = $query->paginate(15)->withQueryString();
        return view('service-orders.index', compact('orders'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        return view('service-orders.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'         => 'required|exists:customers,id',
            'device_type'         => 'required|in:Laptop,Printer,CPU,All-in-One,Other',
            'brand'               => 'nullable|string|max:80',
            'serial_number'       => 'nullable|string|max:80',
            'problem_description' => 'required|string',
            'notes'               => 'nullable|string',
        ]);
        $order = $this->service->createOrder($data);
        return redirect()->route('service-orders.show', $order)
            ->with('success', "Service order #{$order->order_number} created successfully.");
    }

    public function show(ServiceOrder $serviceOrder)
    {
        $serviceOrder->load('customer');
        return view('service-orders.show', ['order' => $serviceOrder]);
    }

    public function markInProgress(ServiceOrder $serviceOrder, Request $request)
    {
        $data = $request->validate(['diagnosis' => 'nullable|string']);
        $this->service->updateProgress($serviceOrder, array_merge($data, ['status' => 'InProgress']));
        return back()->with('success', 'Status updated: In Progress.');
    }

    public function markDone(ServiceOrder $serviceOrder, Request $request)
    {
        $request->validate([
            'service_cost' => 'required|numeric|min:0',
            'diagnosis'    => 'nullable|string',
        ]);
        $this->service->markDone($serviceOrder, $request->service_cost, $request->diagnosis);
        return back()->with('success', 'Service completed. Waiting for customer payment.');
    }

    public function markCompleted(ServiceOrder $serviceOrder)
    {
        if ($serviceOrder->status !== 'Done') {
            return back()->with('error', 'Order must be in "Done" status before confirming payment.');
        }
        try {
            $this->service->completeWithPayment($serviceOrder);
            return back()->with('success', 'Payment received. Order completed & journal entry posted.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
