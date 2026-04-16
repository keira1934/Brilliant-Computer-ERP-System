@extends('layouts.app')
@php $title = 'Purchase Orders'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Purchase Orders</h1><p class="page-sub">Manage goods procurement from suppliers</p></div>
    <a href="{{ route('purchases.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New PO</a>
</div>
<div class="card">
    <div class="card-body" style="padding-bottom:0">
        <form class="filter-bar" method="GET">
            <div class="search-wrap"><i class="bi bi-search"></i>
                <input name="search" value="{{ request('search') }}" placeholder="Search PO number..." class="form-control" style="width:220px">
            </div>
            <select name="status" class="form-control" style="width:140px">
                <option value="">All Status</option>
                @foreach(['Draft','Ordered','Received','Cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ $s }}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary"><i class="bi bi-funnel"></i> Filter</button>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>PO Number</th><th>Supplier</th><th>Date</th><th>Expected</th><th class="text-right">Total</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($purchases as $po)
            <tr>
                <td class="td-primary font-mono">{{ $po->po_number }}</td>
                <td>{{ $po->supplier->name }}</td>
                <td class="td-muted">{{ $po->purchase_date->format('d/m/Y') }}</td>
                <td class="td-muted">{{ $po->expected_date?->format('d/m/Y') ?? '-' }}</td>
                <td class="text-right fw-semibold">Rp {{ number_format($po->total,0,',','.') }}</td>
                <td><span class="{{ $po->getStatusBadgeClass() }}">{{ $po->status }}</span></td>
                <td>
                    <div class="flex gap-2">
                        <a href="{{ route('purchases.show', $po) }}" class="btn btn-sm btn-outline"><i class="bi bi-eye"></i></a>
                        @if($po->status === 'Draft')
                        <button onclick="deleteRecord('{{ route('purchases.destroy', $po) }}', 'Delete PO {{ $po->po_number }}?')" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7"><div class="empty-state"><i class="bi bi-cart3"></i><p>No purchase orders found</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($purchases->hasPages())<div class="card-footer">{{ $purchases->links('vendor.pagination.simple') }}</div>@endif
</div>
@endsection
