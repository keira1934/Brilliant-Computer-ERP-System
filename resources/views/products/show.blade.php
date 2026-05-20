@extends('layouts.app')
@php $title = 'Product Detail'; @endphp
@section('content')

<div class="page-header">
    <div>
        <h1>Product Detail</h1>
        <p class="page-sub">{{ $product->sku }} — {{ $product->name }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit</a>
        <a href="{{ route('products.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
</div>

{{-- KPI Summary --}}
<div class="kpi-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px">
    <div class="kpi-card">
        <div class="kpi-icon {{ $product->isLowStock() ? 'danger' : 'success' }}"><i class="bi bi-boxes"></i></div>
        <div>
            <div class="kpi-label">Current Stock</div>
            <div class="kpi-value {{ $product->isLowStock() ? 'text-danger' : '' }}">{{ $product->stock }} {{ $product->unit }}</div>
            <div class="kpi-sub">Min: {{ $product->min_stock }} {{ $product->unit }}</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon navy"><i class="bi bi-tag"></i></div>
        <div>
            <div class="kpi-label">Selling Price</div>
            <div class="kpi-value" style="font-size:16px">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</div>
            <div class="kpi-sub">Per {{ $product->unit }}</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon warning"><i class="bi bi-cart3"></i></div>
        <div>
            <div class="kpi-label">Cost Price</div>
            <div class="kpi-value" style="font-size:16px">Rp {{ number_format($product->cost_price, 0, ',', '.') }}</div>
            <div class="kpi-sub">Purchase cost</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon success"><i class="bi bi-graph-up-arrow"></i></div>
        <div>
            <div class="kpi-label">Gross Margin</div>
            @php
                $margin = $product->sell_price > 0
                    ? round((($product->sell_price - $product->cost_price) / $product->sell_price) * 100, 1)
                    : 0;
            @endphp
            <div class="kpi-value {{ $margin > 0 ? 'text-success' : 'text-danger' }}">{{ $margin }}%</div>
            <div class="kpi-sub">Rp {{ number_format($product->sell_price - $product->cost_price, 0, ',', '.') }} per unit</div>
        </div>
    </div>
</div>

<div class="grid-2" style="gap:24px">

    {{-- LEFT: Product Info --}}
    <div style="display:flex;flex-direction:column;gap:20px">

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-box-seam" style="color:var(--navy-500)"></i> Product Information</h3>
                @if($product->isLowStock())
                    <span class="badge badge-warning"><i class="bi bi-exclamation-triangle"></i> Low Stock</span>
                @endif
            </div>
            <div class="card-body">
                <table style="width:100%;font-size:13.5px;border-collapse:collapse">
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600;width:40%">SKU</td>
                        <td style="padding:8px 0" class="font-mono fw-bold">{{ $product->sku }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Product Name</td>
                        <td style="padding:8px 0;font-weight:700;color:var(--navy-900)">{{ $product->name }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Category</td>
                        <td style="padding:8px 0"><span class="badge badge-navy">{{ $product->category }}</span></td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Unit</td>
                        <td style="padding:8px 0">{{ $product->unit }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Purchase Price</td>
                        <td style="padding:8px 0">Rp {{ number_format($product->cost_price, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Selling Price</td>
                        <td style="padding:8px 0;font-weight:700">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Current Stock</td>
                        <td style="padding:8px 0;font-weight:700;color:{{ $product->isLowStock() ? 'var(--danger)' : 'var(--success)' }}">
                            {{ $product->stock }} {{ $product->unit }}
                        </td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Minimum Stock</td>
                        <td style="padding:8px 0">{{ $product->min_stock }} {{ $product->unit }}</td>
                    </tr>
                    @if($primarySupplier)
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Primary Supplier</td>
                        <td style="padding:8px 0">
                            <a href="{{ route('suppliers.show', $primarySupplier) }}" style="color:var(--navy-600);font-weight:600">{{ $primarySupplier->name }}</a>
                        </td>
                    </tr>
                    @endif
                    @if($product->description)
                    <tr>
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Description</td>
                        <td style="padding:8px 0">{{ $product->description }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        {{-- Stock Movement History --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-arrow-left-right" style="color:var(--navy-500)"></i> Stock Movement History</h3>
                <a href="{{ route('inventory-movements.index') }}" class="btn btn-sm btn-secondary">View All</a>
            </div>
            <div class="card-body" style="padding:0">
                @if($movements->isEmpty())
                    <div class="empty-state" style="padding:24px"><i class="bi bi-arrow-left-right"></i><p>No movements found</p></div>
                @else
                    <table class="data-table compact">
                        <thead>
                            <tr><th>Date</th><th>Type</th><th class="text-right">In</th><th class="text-right">Out</th><th class="text-right">Balance</th></tr>
                        </thead>
                        <tbody>
                            @foreach($movements as $mv)
                            <tr>
                                <td>{{ $mv->movement_date->format('d M Y') }}</td>
                                <td><span class="badge badge-navy" style="font-size:11px">{{ $mv->movement_type }}</span></td>
                                <td class="text-right text-success fw-semibold">{{ $mv->qty_in > 0 ? '+' . $mv->qty_in : '—' }}</td>
                                <td class="text-right text-danger fw-semibold">{{ $mv->qty_out > 0 ? '-' . $mv->qty_out : '—' }}</td>
                                <td class="text-right fw-bold">{{ $mv->balance_qty }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

    </div>

    {{-- RIGHT: Purchase & Sales History --}}
    <div style="display:flex;flex-direction:column;gap:20px">

        {{-- Purchase Transactions --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-cart3" style="color:var(--navy-500)"></i> Related Purchases</h3>
                <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-secondary">View All</a>
            </div>
            <div class="card-body" style="padding:0">
                @if($purchaseItems->isEmpty())
                    <div class="empty-state" style="padding:24px"><i class="bi bi-cart3"></i><p>No purchase records found</p></div>
                @else
                    <table class="data-table compact">
                        <thead>
                            <tr><th>PO #</th><th>Supplier</th><th>Date</th><th class="text-right">Qty</th><th class="text-right">Unit Cost</th></tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseItems as $pi)
                            <tr>
                                <td class="font-mono td-muted">
                                    <a href="{{ route('purchases.show', $pi->purchase) }}" style="color:var(--navy-600)">{{ $pi->purchase->po_number }}</a>
                                </td>
                                <td>{{ $pi->purchase->supplier->name ?? '—' }}</td>
                                <td>{{ $pi->purchase->purchase_date->format('d M Y') }}</td>
                                <td class="text-right fw-semibold">{{ $pi->qty }}</td>
                                <td class="text-right td-muted">Rp {{ number_format($pi->unit_cost, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        {{-- Sales Transactions --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-bag-check" style="color:var(--navy-500)"></i> Related Sales</h3>
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-secondary">View All</a>
            </div>
            <div class="card-body" style="padding:0">
                @if($saleItems->isEmpty())
                    <div class="empty-state" style="padding:24px"><i class="bi bi-bag"></i><p>No sales records found</p></div>
                @else
                    <table class="data-table compact">
                        <thead>
                            <tr><th>Sale #</th><th>Customer</th><th>Date</th><th class="text-right">Qty</th><th class="text-right">Unit Price</th></tr>
                        </thead>
                        <tbody>
                            @foreach($saleItems as $si)
                            <tr>
                                <td class="font-mono td-muted">
                                    <a href="{{ route('sales.show', $si->sale) }}" style="color:var(--navy-600)">{{ $si->sale->sale_number }}</a>
                                </td>
                                <td>{{ $si->sale->customer->name ?? 'Walk-in' }}</td>
                                <td>{{ $si->sale->sale_date->format('d M Y') }}</td>
                                <td class="text-right fw-semibold">{{ $si->qty }}</td>
                                <td class="text-right td-muted">Rp {{ number_format($si->unit_price, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
