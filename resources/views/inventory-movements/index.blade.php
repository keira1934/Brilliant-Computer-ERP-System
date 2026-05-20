@extends('layouts.app')
@php $title = 'Inventory Ledger'; @endphp
@section('content')
<div class="page-header">
    <div>
        <h1>Inventory Ledger</h1>
        <p class="page-sub">Perpetual stock movement register with running balances</p>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding-bottom:0">
        <form class="filter-bar" method="GET">
            <select name="product_id" class="form-control" style="width:260px">
                <option value="">All Products</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}" {{ request('product_id')==$product->id?'selected':'' }}>{{ $product->sku }} - {{ $product->name }}</option>
                @endforeach
            </select>
            <select name="type" class="form-control" style="width:180px">
                <option value="">All Types</option>
                @foreach(['purchase_receipt','sale_issue','adjustment','opening'] as $type)
                <option value="{{ $type }}" {{ request('type')===$type?'selected':'' }}>{{ str_replace('_',' ',ucwords($type,'_')) }}</option>
                @endforeach
            </select>
            <input name="from" type="date" value="{{ request('from') }}" class="form-control" style="width:160px">
            <input name="to" type="date" value="{{ request('to') }}" class="form-control" style="width:160px">
            <button class="btn btn-secondary"><i class="bi bi-funnel"></i> Filter</button>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Movement</th><th>Date</th><th>Product</th><th>Type</th><th class="text-right">In</th><th class="text-right">Out</th><th class="text-right">Balance</th><th class="text-right">Unit Cost</th><th>Reference</th></tr></thead>
            <tbody>
            @forelse($movements as $movement)
            <tr>
                <td class="font-mono td-primary">{{ $movement->movement_number }}</td>
                <td class="td-muted">{{ $movement->movement_date->format('d/m/Y') }}</td>
                <td>{{ $movement->product->sku }}<br><span class="td-muted">{{ $movement->product->name }}</span></td>
                <td><span class="badge badge-navy">{{ str_replace('_',' ',ucwords($movement->movement_type,'_')) }}</span></td>
                <td class="text-right">{{ $movement->qty_in ?: '-' }}</td>
                <td class="text-right">{{ $movement->qty_out ?: '-' }}</td>
                <td class="text-right fw-bold">{{ $movement->balance_qty }}</td>
                <td class="text-right">Rp {{ number_format($movement->unit_cost,0,',','.') }}</td>
                <td class="td-muted">{{ $movement->reference_type }} #{{ $movement->reference_id }}</td>
            </tr>
            @empty
            <tr><td colspan="9"><div class="empty-state"><i class="bi bi-arrow-left-right"></i><p>No inventory movements found</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($movements->hasPages())<div class="card-footer">{{ $movements->links('vendor.pagination.simple') }}</div>@endif
</div>
@endsection
