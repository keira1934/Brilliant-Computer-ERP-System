<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    private const CATEGORY_PREFIX = [
        'Laptop' => 'LAP',
        'Printer' => 'PRT',
        'CPU' => 'CPU',
        'Accessories' => 'ACC',
        'Other' => 'OTH',
    ];

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
        $products = $query->orderBy('category')->orderBy('sku')->orderBy('name')->paginate(20)->withQueryString();
        $lowCount = Product::whereColumn('stock', '<=', 'min_stock')->count();
        return view('products.index', compact('products', 'lowCount'));
    }

    public function create()
    {
        $autoSkus = collect(array_keys(self::CATEGORY_PREFIX))
            ->mapWithKeys(fn($category) => [$category => $this->previewSku($category)])
            ->all();
        $autoSku = $autoSkus['Laptop'];

        return view('products.create', compact('autoSku', 'autoSkus'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sku'         => 'nullable|string|max:50',
            'name'        => 'required|string|max:150',
            'category'    => 'required|in:Laptop,Printer,CPU,Accessories,Other',
            'unit'        => 'required|string|max:20',
            'cost_price'  => 'required|numeric|min:0',
            'sell_price'  => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'min_stock'   => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);
        $product = DB::transaction(function () use ($data) {
            $data['sku'] = $this->generateSku($data['category']);
            $product = Product::create($data);
            $this->auditService->logCreation('inventory', $product, "Product {$product->sku} created");

            return $product;
        });

        return redirect()->route('products.index')->with('success', "Product '{$data['name']}' added successfully.");
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'sku'         => 'nullable|string|max:50',
            'name'        => 'required|string|max:150',
            'category'    => 'required|in:Laptop,Printer,CPU,Accessories,Other',
            'unit'        => 'required|string|max:20',
            'cost_price'  => 'required|numeric|min:0',
            'sell_price'  => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'min_stock'   => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);
        $data['sku'] = $product->sku;
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

    private function previewSku(string $category): string
    {
        $prefix = self::CATEGORY_PREFIX[$category] ?? 'OTH';
        $lastSku = Product::withTrashed()
            ->where('sku', 'like', $prefix . '-%')
            ->orderByDesc(DB::raw("CAST(SUBSTRING(sku, " . (strlen($prefix) + 2) . ") AS UNSIGNED)"))
            ->value('sku');

        $next = 1;
        if ($lastSku && preg_match('/(\d+)$/', $lastSku, $matches)) {
            $next = (int) $matches[1] + 1;
        }

        return $prefix . '-' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    private function generateSku(string $category): string
    {
        $prefix = self::CATEGORY_PREFIX[$category] ?? 'OTH';
        $lastNumber = Product::withTrashed()
            ->where('sku', 'like', $prefix . '-%')
            ->lockForUpdate()
            ->max(DB::raw("CAST(SUBSTRING(sku, " . (strlen($prefix) + 2) . ") AS UNSIGNED)"));

        return $prefix . '-' . str_pad(((int) $lastNumber) + 1, 3, '0', STR_PAD_LEFT);
    }
}
