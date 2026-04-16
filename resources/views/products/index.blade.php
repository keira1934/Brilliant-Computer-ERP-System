@extends('layouts.app')
@php $title = 'Product Catalog'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Product Catalog</h1><p class="page-sub">Manage stock, pricing and inventory</p></div>
    <a href="{{ route('products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Product</a>
</div>

@if($lowCount > 0)
<div class="stock-alert">
    <i class="bi bi-exclamation-triangle-fill" style="font-size:18px"></i>
    <span><strong>{{ $lowCount }} product(s)</strong> are below minimum stock level and need restocking!</span>
    <a href="{{ route('products.index', ['filter' => 'low']) }}" class="btn btn-sm btn-warning" style="margin-left:auto">View Low Stock</a>
</div>
@endif

<div class="card">
    <div class="card-body" style="padding-bottom:0">
        <form class="filter-bar" method="GET">
            <div class="search-wrap"><i class="bi bi-search"></i>
                <input name="search" value="{{ request('search') }}" placeholder="Search name / SKU..." class="form-control" style="width:260px">
            </div>
            <select name="category" class="form-control" style="width:160px">
                <option value="">All Categories</option>
                @foreach(['Laptop','Printer','CPU','Accessories','Other'] as $cat)
                <option value="{{ $cat }}" {{ request('category')==$cat?'selected':'' }}>{{ $cat }}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary"><i class="bi bi-funnel"></i> Filter</button>
            @if(request()->hasAny(['search','category']))<a href="{{ route('products.index') }}" class="btn btn-outline">Reset</a>@endif
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>SKU</th><th>Product Name</th><th>Category</th><th class="text-right">Cost Price</th><th class="text-right">Sell Price</th><th class="text-right">Stock</th><th class="text-right">Min Stock</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($products as $p)
            <tr class="{{ $p->isLowStock() ? 'low-stock-row' : '' }}">
                <td class="font-mono td-muted">{{ $p->sku }}</td>
                <td class="td-primary">
                    {{ $p->name }}
                    @if($p->isLowStock())<span class="badge badge-warning" style="margin-left:6px"><i class="bi bi-exclamation-triangle"></i> Low Stock</span>@endif
                </td>
                <td><span class="badge badge-navy">{{ $p->category }}</span></td>
                <td class="text-right td-muted">Rp {{ number_format($p->cost_price,0,',','.') }}</td>
                <td class="text-right fw-semibold">Rp {{ number_format($p->sell_price,0,',','.') }}</td>
                <td class="text-right fw-bold {{ $p->isLowStock() ? 'text-danger' : 'text-success' }}">{{ $p->stock }}</td>
                <td class="text-right td-muted">{{ $p->min_stock }}</td>
                <td>
                    <div class="flex gap-2">
                        <a href="{{ route('products.edit', $p) }}" class="btn btn-sm btn-secondary"><i class="bi bi-pencil"></i></a>
                        <button onclick="deleteRecord('{{ route('products.destroy', $p) }}', 'Delete product &quot;{{ addslashes($p->name) }}&quot;?')" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8"><div class="empty-state"><i class="bi bi-box-seam"></i><p>No products found</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())<div class="card-footer">{{ $products->links('vendor.pagination.simple') }}</div>@endif
</div>
@endsection
