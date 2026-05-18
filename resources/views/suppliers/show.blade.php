@extends('layouts.app')
@php $title = 'Supplier Detail'; @endphp
@section('content')

<div class="page-header">
    <div>
        <h1>Supplier Detail</h1>
        <p class="page-sub">{{ $supplier->name }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit</a>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
</div>

{{-- KPI Summary --}}
<div class="kpi-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px">
    <div class="kpi-card">
        <div class="kpi-icon navy"><i class="bi bi-cart3"></i></div>
        <div>
            <div class="kpi-label">Total Orders</div>
            <div class="kpi-value">{{ $totalOrders }}</div>
            <div class="kpi-sub">Purchase orders</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon warning"><i class="bi bi-currency-dollar"></i></div>
        <div>
            <div class="kpi-label">Total Purchased</div>
            <div class="kpi-value" style="font-size:16px">Rp {{ number_format($totalPurchases, 0, ',', '.') }}</div>
            <div class="kpi-sub">Lifetime value</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon {{ $outstandingAP > 0 ? 'danger' : 'success' }}"><i class="bi bi-clock-history"></i></div>
        <div>
            <div class="kpi-label">Outstanding AP</div>
            <div class="kpi-value {{ $outstandingAP > 0 ? 'text-danger' : 'text-success' }}" style="font-size:16px">
                Rp {{ number_format($outstandingAP, 0, ',', '.') }}
            </div>
            <div class="kpi-sub">Unpaid invoices</div>
        </div>
    </div>
</div>

<div class="grid-2" style="gap:24px">

    {{-- LEFT: Supplier Info + Products --}}
    <div style="display:flex;flex-direction:column;gap:20px">

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-building" style="color:var(--navy-500)"></i> Supplier Information</h3>
            </div>
            <div class="card-body">
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px">
                    <div style="width:56px;height:56px;border-radius:12px;background:linear-gradient(135deg,var(--navy-600),var(--navy-400));display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;font-weight:800;flex-shrink:0">
                        {{ strtoupper(substr($supplier->name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size:18px;font-weight:800;color:var(--navy-900)">{{ $supplier->name }}</div>
                        @if($supplier->contact_person)
                        <div style="font-size:13px;color:var(--gray-500);margin-top:2px">Contact: {{ $supplier->contact_person }}</div>
                        @endif
                    </div>
                </div>
                <table style="width:100%;font-size:13.5px;border-collapse:collapse">
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600;width:38%">Contact Person</td>
                        <td style="padding:8px 0">{{ $supplier->contact_person ?? '—' }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Phone</td>
                        <td style="padding:8px 0">{{ $supplier->phone ?? '—' }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Email</td>
                        <td style="padding:8px 0">{{ $supplier->email ?? '—' }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Address</td>
                        <td style="padding:8px 0">{{ $supplier->address ?? '—' }}</td>
                    </tr>
                    @if($supplier->notes)
                    <tr>
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Notes</td>
                        <td style="padding:8px 0">{{ $supplier->notes }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        {{-- Products from this supplier --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-box-seam" style="color:var(--navy-500)"></i> Products Supplied</h3>
                <span class="badge badge-navy">{{ $products->count() }} product(s)</span>
            </div>
            <div class="card-body" style="padding:0">
                @if($products->isEmpty())
                    <div class="empty-state" style="padding:24px"><i class="bi bi-box-seam"></i><p>No products found</p></div>
                @else
                    <table class="data-table compact">
                        <thead>
                            <tr><th>SKU</th><th>Product Name</th><th>Category</th><th class="text-right">Stock</th><th class="text-right">Cost Price</th></tr>
                        </thead>
                        <tbody>
                            @foreach($products as $prod)
                            <tr>
                                <td class="font-mono td-muted">{{ $prod->sku }}</td>
                                <td>
                                    <a href="{{ route('products.show', $prod) }}" style="color:var(--navy-700);font-weight:600">{{ $prod->name }}</a>
                                    @if($prod->trashed()) <span class="badge badge-gray" style="font-size:10px">Archived</span> @endif
                                </td>
                                <td><span class="badge badge-navy">{{ $prod->category }}</span></td>
                                <td class="text-right {{ $prod->isLowStock() ? 'text-danger' : '' }} fw-semibold">{{ $prod->stock }}</td>
                                <td class="text-right td-muted">Rp {{ number_format($prod->cost_price, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

    </div>

    {{-- RIGHT: Purchase History + AP Invoices --}}
    <div style="display:flex;flex-direction:column;gap:20px">

        {{-- Purchase History --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-receipt" style="color:var(--navy-500)"></i> Purchase History</h3>
                <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-secondary">View All</a>
            </div>
            <div class="card-body" style="padding:0">
                @if($purchases->isEmpty())
                    <div class="empty-state" style="padding:24px"><i class="bi bi-receipt"></i><p>No purchases found</p></div>
                @else
                    <table class="data-table compact">
                        <thead>
                            <tr><th>PO #</th><th>Date</th><th>Items</th><th class="text-right">Total</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach($purchases as $po)
                            <tr>
                                <td class="font-mono td-muted">
                                    <a href="{{ route('purchases.show', $po) }}" style="color:var(--navy-600)">{{ $po->po_number }}</a>
                                </td>
                                <td>{{ $po->purchase_date->format('d M Y') }}</td>
                                <td class="td-muted">{{ $po->items->count() }} item(s)</td>
                                <td class="text-right fw-bold">Rp {{ number_format($po->total, 0, ',', '.') }}</td>
                                <td><span class="{{ $po->getStatusBadgeClass() }}">{{ $po->status }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="padding:10px 16px;font-weight:700;color:var(--gray-600)">Total (shown)</td>
                                <td class="text-right" style="padding:10px 16px;font-weight:800;color:var(--navy-900)">Rp {{ number_format($purchases->sum('total'), 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </div>
        </div>

        {{-- AP Invoices --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-file-earmark-text" style="color:var(--navy-500)"></i> AP Invoices</h3>
                <a href="{{ route('accounts-payable.index') }}" class="btn btn-sm btn-secondary">View All AP</a>
            </div>
            <div class="card-body" style="padding:0">
                @if($apInvoices->isEmpty())
                    <div class="empty-state" style="padding:24px"><i class="bi bi-file-earmark-text"></i><p>No AP invoices found</p></div>
                @else
                    <table class="data-table compact">
                        <thead>
                            <tr><th>Invoice #</th><th>Date</th><th class="text-right">Total</th><th class="text-right">Paid</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach($apInvoices as $inv)
                            <tr>
                                <td class="font-mono td-muted">{{ $inv->invoice_number }}</td>
                                <td>{{ $inv->invoice_date->format('d M Y') }}</td>
                                <td class="text-right fw-semibold">Rp {{ number_format($inv->total, 0, ',', '.') }}</td>
                                <td class="text-right text-success">Rp {{ number_format($inv->paid_amount, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $cls = match($inv->status ?? '') {
                                            'Paid' => 'badge-success',
                                            'Partially Paid' => 'badge-warning',
                                            'Cancelled' => 'badge-danger',
                                            default => 'badge-gray',
                                        };
                                    @endphp
                                    <span class="badge {{ $cls }}">{{ $inv->status ?? 'Open' }}</span>
                                </td>
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
