<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\AuditService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request)
    {
        $query = Product::query();
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
        }
        if ($request->category) {
            $query->where('category', $request->category);
        }
        $products = $query->orderBy('category')->orderBy('name')->paginate(20)->withQueryString();
        $lowCount = Product::whereColumn('stock', '<=', 'min_stock')->count();
        return view('products.index', compact('products', 'lowCount'));
    }

    public function create()
    {
        // UI suggestion only; the unique database constraint remains the source of truth.
        $nextId = ((int) Product::max('id')) + 1;
        $autoSku = 'PRD-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        return view('products.create', compact('autoSku'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sku'         => 'required|string|max:50|unique:products,sku',
            'name'        => 'required|string|max:150',
            'category'    => 'required|in:Laptop,Printer,CPU,Accessories,Other',
            'unit'        => 'required|string|max:20',
            'cost_price'  => 'required|numeric|min:0',
            'sell_price'  => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'min_stock'   => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);
        $product = Product::create($data);
        $this->auditService->logCreation('inventory', $product, "Product {$product->sku} created");

        return redirect()->route('products.index')->with('success', "Product '{$data['name']}' added successfully.");
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'sku'         => 'required|string|max:50|unique:products,sku,' . $product->id,
            'name'        => 'required|string|max:150',
            'category'    => 'required|in:Laptop,Printer,CPU,Accessories,Other',
            'unit'        => 'required|string|max:20',
            'cost_price'  => 'required|numeric|min:0',
            'sell_price'  => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'min_stock'   => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);
        $oldValues = $product->toArray();
        $product->update($data);
        $this->auditService->logUpdate('inventory', $product, $oldValues, "Product {$product->sku} updated");

        return redirect()->route('products.index')->with('success', "Product '{$product->name}' updated successfully.");
    }

    public function destroy(Product $product)
    {
        $oldValues = $product->toArray();
        $product->delete();
        $this->auditService->log('inventory', 'soft_delete', $product, $oldValues, null, "Product {$product->sku} archived");

        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }
}
