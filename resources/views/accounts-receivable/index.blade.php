@extends('layouts.app')
@php $title = 'Accounts Receivable'; @endphp
@section('content')
<div class="page-header">
    <div>
        <h1>Accounts Receivable</h1>
        <p class="page-sub">Customer invoices, collections, and aging control</p>
    </div>
</div>

<div class="grid-5 mb-5">
    @foreach($aging as $bucket => $amount)
    <div class="metric-card">
        <span class="metric-label">{{ $bucket }}</span>
        <span class="metric-value">Rp {{ number_format($amount,0,',','.') }}</span>
    </div>
    @endforeach
</div>

<div class="card">
    <div class="card-body" style="padding-bottom:0">
        <form class="filter-bar" method="GET">
            <div class="search-wrap"><i class="bi bi-search"></i>
                <input name="search" value="{{ request('search') }}" placeholder="Invoice or customer..." class="form-control" style="width:240px">
            </div>
            <select name="status" class="form-control" style="width:160px">
                <option value="">All Status</option>
                @foreach(['Open','Partially Paid','Paid','Cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ $s }}</option>
                @endforeach
            </select>
            <input type="date" name="as_of" value="{{ $asOf }}" class="form-control" style="width:160px">
            <button class="btn btn-secondary"><i class="bi bi-funnel"></i> Filter</button>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Invoice</th><th>Customer</th><th>Date</th><th>Due</th><th class="text-right">Total</th><th class="text-right">Paid</th><th class="text-right">Outstanding</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($invoices as $invoice)
            <tr>
                <td class="td-primary font-mono">{{ $invoice->invoice_number }}</td>
                <td>{{ $invoice->customer?->name ?? 'Walk-in Customer' }}</td>
                <td class="td-muted">{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                <td class="td-muted">{{ $invoice->due_date?->format('d/m/Y') ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($invoice->total,0,',','.') }}</td>
                <td class="text-right td-muted">Rp {{ number_format($invoice->paid_amount,0,',','.') }}</td>
                <td class="text-right fw-bold">Rp {{ number_format($invoice->outstanding,0,',','.') }}</td>
                <td><span class="badge {{ $invoice->status === 'Paid' ? 'badge-success' : 'badge-warning' }}">{{ $invoice->status }}</span></td>
                <td><a href="{{ route('accounts-receivable.show', $invoice) }}" class="btn btn-sm btn-outline"><i class="bi bi-eye"></i></a></td>
            </tr>
            @empty
            <tr><td colspan="9"><div class="empty-state"><i class="bi bi-receipt"></i><p>No receivable invoices found</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())<div class="card-footer">{{ $invoices->links('vendor.pagination.simple') }}</div>@endif
</div>
@endsection
