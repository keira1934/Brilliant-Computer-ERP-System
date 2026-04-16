@extends('layouts.app')
@php $title = 'Service Orders'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Service Orders</h1><p class="page-sub">Manage device repair orders</p></div>
    <a href="{{ route('service-orders.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Order</a>
</div>
<div class="card">
    <div class="card-body" style="padding-bottom:0">
        <form class="filter-bar" method="GET">
            <div class="search-wrap"><i class="bi bi-search"></i>
                <input name="search" value="{{ request('search') }}" placeholder="Search order no. / customer..." class="form-control" style="width:240px">
            </div>
            <select name="status" class="form-control" style="width:160px">
                <option value="">All Status</option>
                @foreach(['Received','InProgress','Done','Completed'] as $s)
                <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ $s === 'InProgress' ? 'In Progress' : $s }}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary"><i class="bi bi-funnel"></i> Filter</button>
            @if(request()->hasAny(['search','status']))<a href="{{ route('service-orders.index') }}" class="btn btn-outline">Reset</a>@endif
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Order No.</th><th>Customer</th><th>Device</th><th>Problem</th><th>Status</th><th>Date In</th><th>Cost</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($orders as $order)
            <tr>
                <td class="td-primary font-mono">{{ $order->order_number }}</td>
                <td>{{ $order->customer?->name ?? 'Walk-in' }}<br><span class="td-muted" style="font-size:11px">{{ $order->customer?->phone }}</span></td>
                <td>{{ $order->device_type }}<br><span class="td-muted" style="font-size:11px">{{ $order->brand }}</span></td>
                <td class="td-muted" style="max-width:160px;white-space:normal">{{ Str::limit($order->problem_description, 60) }}</td>
                <td><span class="{{ $order->getStatusBadgeClass() }}">{{ $order->getStatusLabel() }}</span></td>
                <td class="td-muted">{{ $order->received_at?->format('d/m/Y') }}</td>
                <td class="fw-semibold">{{ $order->service_cost ? 'Rp '.number_format($order->service_cost,0,',','.') : '-' }}</td>
                <td><a href="{{ route('service-orders.show', $order) }}" class="btn btn-sm btn-outline"><i class="bi bi-eye"></i></a></td>
            </tr>
            @empty
            <tr><td colspan="8"><div class="empty-state"><i class="bi bi-wrench-adjustable"></i><p>No service orders found</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())<div class="card-footer">{{ $orders->links('vendor.pagination.simple') }}</div>@endif
</div>
@endsection
