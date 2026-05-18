@extends('layouts.app')
@php $title = 'Customer Detail'; @endphp
@section('content')

<div class="page-header">
    <div>
        <h1>Customer Detail</h1>
        <p class="page-sub">{{ $customer->name }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit</a>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
</div>

{{-- KPI Summary --}}
<div class="kpi-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px">
    <div class="kpi-card">
        <div class="kpi-icon navy"><i class="bi bi-receipt"></i></div>
        <div>
            <div class="kpi-label">Total Transactions</div>
            <div class="kpi-value">{{ $totalTransactions }}</div>
            <div class="kpi-sub">Sales orders</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon success"><i class="bi bi-currency-dollar"></i></div>
        <div>
            <div class="kpi-label">Total Spending</div>
            <div class="kpi-value" style="font-size:16px">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
            <div class="kpi-sub">Lifetime value</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon warning"><i class="bi bi-tools"></i></div>
        <div>
            <div class="kpi-label">Service Orders</div>
            <div class="kpi-value">{{ $totalServiceOrders }}</div>
            <div class="kpi-sub">Total service jobs</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon {{ $outstandingAR > 0 ? 'danger' : 'success' }}"><i class="bi bi-clock-history"></i></div>
        <div>
            <div class="kpi-label">Outstanding AR</div>
            <div class="kpi-value {{ $outstandingAR > 0 ? 'text-danger' : 'text-success' }}" style="font-size:16px">
                Rp {{ number_format($outstandingAR, 0, ',', '.') }}
            </div>
            <div class="kpi-sub">Unpaid invoices</div>
        </div>
    </div>
</div>

<div class="grid-2" style="gap:24px">

    {{-- LEFT: Customer Info --}}
    <div style="display:flex;flex-direction:column;gap:20px">

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-person-circle" style="color:var(--navy-500)"></i> Customer Information</h3>
            </div>
            <div class="card-body">
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px">
                    <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,var(--navy-600),var(--navy-400));display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;font-weight:800;flex-shrink:0">
                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size:18px;font-weight:800;color:var(--navy-900)">{{ $customer->name }}</div>
                        <div style="font-size:12px;color:var(--gray-500);margin-top:2px">Customer since {{ $customer->created_at->format('d M Y') }}</div>
                    </div>
                </div>
                <table style="width:100%;font-size:13.5px;border-collapse:collapse">
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600;width:35%">Phone</td>
                        <td style="padding:8px 0">{{ $customer->phone ?? '—' }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Email</td>
                        <td style="padding:8px 0">{{ $customer->email ?? '—' }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Address</td>
                        <td style="padding:8px 0">{{ $customer->address ?? '—' }}</td>
                    </tr>
                    @if($customer->notes)
                    <tr>
                        <td style="padding:8px 0;color:var(--gray-500);font-weight:600">Notes</td>
                        <td style="padding:8px 0">{{ $customer->notes }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        {{-- AR Invoices --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-file-earmark-text" style="color:var(--navy-500)"></i> Recent Invoices (AR)</h3>
                <a href="{{ route('accounts-receivable.index') }}" class="btn btn-sm btn-secondary">View All AR</a>
            </div>
            <div class="card-body" style="padding:0">
                @if($arInvoices->isEmpty())
                    <div class="empty-state" style="padding:24px"><i class="bi bi-file-earmark-text"></i><p>No invoices found</p></div>
                @else
                    <table class="data-table compact">
                        <thead>
                            <tr><th>Invoice #</th><th>Date</th><th class="text-right">Total</th><th class="text-right">Paid</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach($arInvoices as $inv)
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

    {{-- RIGHT: Sales & Service Orders --}}
    <div style="display:flex;flex-direction:column;gap:20px">

        {{-- Sales History --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-bag-check" style="color:var(--navy-500)"></i> Sales History</h3>
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-secondary">View All Sales</a>
            </div>
            <div class="card-body" style="padding:0">
                @if($sales->isEmpty())
                    <div class="empty-state" style="padding:24px"><i class="bi bi-bag"></i><p>No sales found</p></div>
                @else
                    <table class="data-table compact">
                        <thead>
                            <tr><th>Sale #</th><th>Date</th><th>Payment</th><th class="text-right">Total</th><th>Type</th></tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $sale)
                            <tr>
                                <td class="font-mono td-muted">
                                    <a href="{{ route('sales.show', $sale) }}" style="color:var(--navy-600)">{{ $sale->sale_number }}</a>
                                </td>
                                <td>{{ $sale->sale_date->format('d M Y') }}</td>
                                <td class="td-muted">{{ $sale->payment_method }}</td>
                                <td class="text-right fw-bold">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                <td>
                                    @if($sale->is_credit_sale)
                                        <span class="badge badge-warning">Credit</span>
                                    @else
                                        <span class="badge badge-success">Cash</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="padding:10px 16px;font-weight:700;color:var(--gray-600)">Total (shown)</td>
                                <td class="text-right" style="padding:10px 16px;font-weight:800;color:var(--navy-900)">Rp {{ number_format($sales->sum('total'), 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </div>
        </div>

        {{-- Service Orders --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-tools" style="color:var(--navy-500)"></i> Service Orders</h3>
                <a href="{{ route('service-orders.index') }}" class="btn btn-sm btn-secondary">View All</a>
            </div>
            <div class="card-body" style="padding:0">
                @if($serviceOrders->isEmpty())
                    <div class="empty-state" style="padding:24px"><i class="bi bi-tools"></i><p>No service orders found</p></div>
                @else
                    <table class="data-table compact">
                        <thead>
                            <tr><th>Order #</th><th>Device</th><th>Received</th><th>Status</th><th class="text-right">Cost</th></tr>
                        </thead>
                        <tbody>
                            @foreach($serviceOrders as $so)
                            <tr>
                                <td class="font-mono td-muted">{{ $so->order_number ?? $so->id }}</td>
                                <td>{{ trim(($so->device_type ?? '') . ' ' . ($so->brand ?? '')) ?: '—' }}</td>
                                <td>{{ $so->received_at ? $so->received_at->format('d M Y') : '—' }}</td>
                                <td>
                                    @php
                                        $cls = match($so->status ?? '') {
                                            'Done', 'Completed' => 'status-done',
                                            'InProgress' => 'status-inprogress',
                                            default => 'status-received',
                                        };
                                    @endphp
                                    <span class="badge {{ $cls }}">{{ $so->getStatusLabel() }}</span>
                                </td>
                                <td class="text-right fw-semibold">
                                    @if(isset($so->service_cost) && $so->service_cost > 0)
                                        Rp {{ number_format($so->service_cost, 0, ',', '.') }}
                                    @else
                                        <span class="td-muted">—</span>
                                    @endif
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
