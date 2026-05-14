<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct(private SaleService $saleService) {}

    public function index(Request $request)
    {
        $query = Sale::with('customer')->latest();
        if ($request->from)   $query->where('sale_date', '>=', $request->from);
        if ($request->to)     $query->where('sale_date', '<=', $request->to);
        if ($request->search) $query->where('sale_number', 'like', "%{$request->search}%");
        $sales = $query->paginate(15)->withQueryString();
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products  = Product::where('stock', '>', 0)->orderBy('name')->get();
        return view('sales.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sale_date'           => 'required|date',
            'payment_method'      => 'required|in:Cash,Transfer,Other',
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.qty'         => 'required|integer|min:1',
            'items.*.unit_price'  => 'required|numeric|min:0',
            'is_credit_sale'      => 'nullable|boolean',
            'payment_terms_days'  => 'nullable|integer|min:0|max:365',
        ]);

        try {
            $sale = $this->saleService->createSale($request->all());
            return redirect()->route('sales.show', $sale)
                ->with('success', "Sale #{$sale->sale_number} saved. Journal entries posted.");
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Sale $sale)
    {
        $sale->load('customer', 'items.product', 'arInvoices.payments');
        return view('sales.show', compact('sale'));
    }
}
