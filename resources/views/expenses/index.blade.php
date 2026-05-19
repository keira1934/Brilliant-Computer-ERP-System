@extends('layouts.app')
@php $title = 'Expenses'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Expenses</h1><p class="page-sub">Record operational expenses and overhead costs</p></div>
    <a href="{{ route('expenses.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Expense</a>
</div>

<div class="kpi-card" style="max-width:280px;margin-bottom:24px">
    <span class="kpi-label">This Month's Expenses</span>
    <div class="kpi-value text-danger">Rp {{ number_format($totalMonth,0,',','.') }}</div>
</div>

<div class="card">
    <div class="card-body" style="padding-bottom:0">
        <form class="filter-bar" method="GET">
            <select name="category" class="form-control" style="width:160px">
                <option value="">All Categories</option>
                @foreach(['Electricity','Maintenance','Internet','Rent','Other'] as $c)
                <option value="{{ $c }}" {{ request('category')==$c?'selected':'' }}>{{ $c }}</option>
                @endforeach
            </select>
            <input name="from" type="date" value="{{ request('from') }}" class="form-control" style="width:160px">
            <input name="to"   type="date" value="{{ request('to') }}"   class="form-control" style="width:160px">
            <button class="btn btn-secondary"><i class="bi bi-funnel"></i> Filter</button>
            @if(request()->hasAny(['category','from','to']))<a href="{{ route('expenses.index') }}" class="btn btn-outline">Reset</a>@endif
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Date</th><th>Category</th><th>Description</th><th>GL Account</th><th class="text-right">Amount</th><th>Reference</th></tr></thead>
            <tbody>
            @forelse($expenses as $exp)
            <tr>
                <td class="td-muted">{{ $exp->expense_date?->format('d/m/Y') }}</td>
                <td><span class="badge badge-navy">{{ $exp->category }}</span></td>
                <td class="td-primary">{{ $exp->description ?? '-' }}</td>
                <td class="font-mono td-muted">{{ $exp->account?->code }} {{ $exp->account?->name }}</td>
                <td class="text-right fw-bold text-danger">Rp {{ number_format($exp->amount,0,',','.') }}</td>
                <td class="td-muted">{{ $exp->reference ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="6"><div class="empty-state"><i class="bi bi-wallet2"></i><p>No expenses recorded</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($expenses->hasPages())<div class="card-footer">{{ $expenses->links('vendor.pagination.simple') }}</div>@endif
</div>
@endsection
