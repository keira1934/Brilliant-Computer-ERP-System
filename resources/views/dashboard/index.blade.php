@extends('layouts.app')
@section('content')

{{-- KPI CARDS --}}
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon navy"><i class="bi bi-graph-up-arrow"></i></div>
        <div>
            <div class="kpi-label">Revenue This Month</div>
            <div class="kpi-value text-navy">Rp {{ number_format($totalRevenue,0,',','.') }}</div>
            <div class="kpi-sub">Sales + Service Revenue</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon danger"><i class="bi bi-arrow-down-circle"></i></div>
        <div>
            <div class="kpi-label">Total Expenses</div>
            <div class="kpi-value text-danger">Rp {{ number_format($totalExpenses,0,',','.') }}</div>
            <div class="kpi-sub">COGS + Payroll + Operations</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon {{ $netProfit >= 0 ? 'success' : 'danger' }}">
            <i class="bi bi-currency-dollar"></i>
        </div>
        <div>
            <div class="kpi-label">{{ $netProfit >= 0 ? 'Net Profit' : 'Net Loss' }}</div>
            <div class="kpi-value {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                Rp {{ number_format(abs($netProfit),0,',','.') }}
            </div>
            <div class="kpi-sub">{{ $netProfit >= 0 ? 'Profit for this period' : 'Loss for this period' }}</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon {{ $lowStockCount > 0 ? 'warning' : 'success' }}">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
        <div>
            <div class="kpi-label">Low Stock Items</div>
            <div class="kpi-value {{ $lowStockCount > 0 ? 'text-warning' : 'text-success' }}">{{ $lowStockCount }}</div>
            <div class="kpi-sub">Products need restocking</div>
        </div>
    </div>
</div>

<div class="grid-4 mb-5">
    <div class="metric-card"><div class="metric-label">Cash / Bank</div><div class="metric-value">Rp {{ number_format($cashBalance,0,',','.') }}</div></div>
    <div class="metric-card"><div class="metric-label">Accounts Receivable</div><div class="metric-value">Rp {{ number_format($arBalance,0,',','.') }}</div></div>
    <div class="metric-card"><div class="metric-label">Accounts Payable</div><div class="metric-value">Rp {{ number_format($apBalance,0,',','.') }}</div></div>
    <div class="metric-card"><div class="metric-label">Inventory Valuation</div><div class="metric-value">Rp {{ number_format($inventoryValuation,0,',','.') }}</div></div>
</div>

@if($lowStockProducts->count())
<div class="stock-alert">
    <i class="bi bi-exclamation-triangle-fill" style="font-size:18px;flex-shrink:0"></i>
    <span><strong>Stock Alert!</strong> {{ $lowStockProducts->count() }} product(s) are below minimum stock level:
        {{ $lowStockProducts->pluck('name')->join(', ') }}</span>
    <a href="{{ route('products.index') }}" class="btn btn-warning btn-sm" style="margin-left:auto;flex-shrink:0">View Products</a>
</div>
@endif

{{-- CHARTS --}}
<div class="grid-2 mb-6">
    <div class="card">
        <div class="card-header"><h5 class="card-title"><i class="bi bi-bar-chart-line" style="margin-right:8px;color:var(--navy-500)"></i>Revenue vs Expenses (6 Months)</h5></div>
        <div class="card-body"><div class="chart-container"><canvas id="revenueChart"></canvas></div></div>
    </div>
    <div class="card">
        <div class="card-header"><h5 class="card-title"><i class="bi bi-pie-chart" style="margin-right:8px;color:var(--navy-500)"></i>Service Order Status</h5></div>
        <div class="card-body"><div class="chart-container"><canvas id="serviceChart"></canvas></div></div>
    </div>
</div>

{{-- TABLES --}}
<div class="grid-2">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Active Service Orders</h5>
            <a href="{{ route('service-orders.index') }}" class="btn btn-sm btn-outline">View All</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Order No.</th><th>Customer</th><th>Device</th><th>Status</th></tr></thead>
                <tbody>
                @forelse($activeOrders as $order)
                <tr>
                    <td><a href="{{ route('service-orders.show', $order) }}" class="td-primary">{{ $order->order_number }}</a></td>
                    <td>{{ $order->customer?->name ?? 'Walk-in' }}</td>
                    <td>{{ $order->device_type }}</td>
                    <td><span class="{{ $order->getStatusBadgeClass() }}">{{ $order->getStatusLabel() }}</span></td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted" style="padding:24px">No active service orders</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Recent Sales</h5>
            <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline">View All</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Sale No.</th><th>Customer</th><th>Total</th></tr></thead>
                <tbody>
                @forelse($recentSales as $sale)
                <tr>
                    <td><a href="{{ route('sales.show', $sale) }}" class="td-primary">{{ $sale->sale_number }}</a></td>
                    <td>{{ $sale->customer?->name ?? 'Walk-in' }}</td>
                    <td class="fw-bold text-navy">Rp {{ number_format($sale->total,0,',','.') }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-muted" style="padding:24px">No sales yet</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const chartData   = @json($chartData);
const statusData  = @json($serviceStatusData);

// Revenue vs Expense Bar Chart
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: chartData.months,
        datasets: [
            { label: 'Revenue', data: chartData.revenue, backgroundColor: 'rgba(27,50,117,.75)', borderRadius: 6 },
            { label: 'Expenses', data: chartData.expense, backgroundColor: 'rgba(220,38,38,.6)', borderRadius: 6 },
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { font: { family: 'Inter', size: 12 } } } },
        scales: {
            y: { ticks: { callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'M', font: { family: 'Inter', size: 11 } }, grid: { color: '#F1F5F9' } },
            x: { ticks: { font: { family: 'Inter', size: 11 } }, grid: { display: false } }
        }
    }
});

// Service Status Doughnut
const statusColors = { Received: '#2563EB', InProgress: '#D97706', Done: '#059669', Completed: '#047857' };
const labels = Object.keys(statusData).map(k => k === 'InProgress' ? 'In Progress' : k);
const values = Object.values(statusData);
const colors = Object.keys(statusData).map(k => statusColors[k] || '#94A3B8');

new Chart(document.getElementById('serviceChart'), {
    type: 'doughnut',
    data: { labels, datasets: [{ data: values, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }] },
    options: {
        responsive: true, maintainAspectRatio: false, cutout: '65%',
        plugins: { legend: { position: 'bottom', labels: { font: { family: 'Inter', size: 12 }, padding: 16 } } }
    }
});
</script>
@endpush
