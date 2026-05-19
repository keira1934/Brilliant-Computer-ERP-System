@extends('layouts.app')
@php $title = 'Sales Transactions'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Sales Transactions</h1><p class="page-sub">Product sales with auto journal posting</p></div>
    <a href="{{ route('sales.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Sale</a>
</div>
<div class="card">
    <div class="card-body" style="padding-bottom:0">
        <form class="filter-bar" method="GET">
            <div class="search-wrap"><i class="bi bi-search"></i>
                <input name="search" value="{{ request('search') }}" placeholder="Search sale number..." class="form-control" style="width:220px">
            </div>
            <input name="from" type="date" value="{{ request('from') }}" class="form-control" style="width:160px">
            <input name="to"   type="date" value="{{ request('to') }}"   class="form-control" style="width:160px">
            <button class="btn btn-secondary"><i class="bi bi-funnel"></i> Filter</button>
            @if(request()->hasAny(['search','from','to']))<a href="{{ route('sales.index') }}" class="btn btn-outline">Reset</a>@endif
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Sale No.</th><th>Date</th><th>Customer</th><th>Payment</th><th class="text-right">Subtotal</th><th class="text-right">Discount</th><th class="text-right">Total</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($sales as $sale)
            <tr>
                <td class="td-primary font-mono">{{ $sale->sale_number }}</td>
                <td class="td-muted">{{ $sale->sale_date->format('d/m/Y') }}</td>
                <td>
                    @if($sale->customer)
                        {{ $sale->customer->name }}
                    @else
                        <span class="td-muted">Walk-in</span>
                    @endif
                </td>
                <td><span class="badge {{ $sale->payment_method === 'Cash' ? 'badge-success' : 'badge-navy' }}">{{ $sale->payment_method }}</span></td>
                <td class="text-right">Rp {{ number_format($sale->subtotal,0,',','.') }}</td>
                <td class="text-right td-muted">{{ $sale->discount > 0 ? '-Rp '.number_format($sale->discount,0,',','.') : '-' }}</td>
                <td class="text-right fw-bold">Rp {{ number_format($sale->total,0,',','.') }}</td>
                <td><a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline"><i class="bi bi-eye"></i></a></td>
            </tr>
            @empty
            <tr><td colspan="8"><div class="empty-state"><i class="bi bi-receipt"></i><p>No sales transactions found</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($sales->hasPages())<div class="card-footer">{{ $sales->links('vendor.pagination.simple') }}</div>@endif
</div>
@endsection
