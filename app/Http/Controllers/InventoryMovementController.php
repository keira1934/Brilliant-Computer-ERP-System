<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryMovementController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::orderBy('name')->get();
        $query = InventoryMovement::with('product')
            ->orderByDesc('movement_date')
            ->orderByDesc('id');

        if ($request->product_id) $query->where('product_id', $request->product_id);
        if ($request->type) $query->where('movement_type', $request->type);
        if ($request->from) $query->where('movement_date', '>=', $request->from);
        if ($request->to) $query->where('movement_date', '<=', $request->to);

        $movements = $query->paginate(30)->withQueryString();

        return view('inventory-movements.index', compact('movements', 'products'));
    }
}
