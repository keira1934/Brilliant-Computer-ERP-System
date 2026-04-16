@extends('layouts.app')
@php $title = 'Transaction Summary'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Transaction Summary</h1><p class="page-sub">All posted journal entries — {{ $from }} to {{ $to }}</p></div>
    <a href="{{ request()->fullUrlWithQuery(['print'=>1]) }}" target="_blank" class="btn btn-outline"><i class="bi bi-printer"></i> Print All</a>
</div>

<div class="card mb-4">
    <div class="card-body" style="padding-bottom:0">
        <form class="filter-bar" method="GET">
            <input name="from" type="date" value="{{ $from }}" class="form-control" style="width:160px">
            <input name="to"   type="date" value="{{ $to }}"   class="form-control" style="width:160px">
            <button class="btn btn-secondary"><i class="bi bi-funnel"></i> Filter</button>
            <a href="{{ route('reports.transactions') }}" class="btn btn-outline">Current Year</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Journal Transactions</h5>
        <span class="badge badge-navy">{{ $isPrint ? $entries->count() : $entries->total() }} transactions</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Date</th><th>Reference</th><th>Description</th><th class="text-right">Total Debit</th><th class="text-right">Total Credit</th><th>Balanced</th></tr></thead>
            <tbody>
            @forelse($isPrint ? $entries : $entries as $entry)
            @php
            $totalD = $entry->lines->sum('debit');
            $totalC = $entry->lines->sum('credit');
            $balanced = abs($totalD - $totalC) < 0.01;
            @endphp
            <tr>
                <td class="td-muted font-mono">{{ $entry->entry_date }}</td>
                <td class="font-mono" style="font-size:12px">{{ $entry->reference_type }}-{{ $entry->reference_id }}</td>
                <td class="td-primary">{{ $entry->description }}</td>
                <td class="text-right fw-semibold">Rp {{ number_format($totalD,0,',','.') }}</td>
                <td class="text-right fw-semibold">Rp {{ number_format($totalC,0,',','.') }}</td>
                <td>
                    @if($balanced)
                    <span class="badge badge-success"><i class="bi bi-check-lg"></i> OK</span>
                    @else
                    <span class="badge badge-danger"><i class="bi bi-x-lg"></i> Unbalanced!</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6"><div class="empty-state"><i class="bi bi-receipt"></i><p>No transactions found for this period</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if(!$isPrint && $entries->hasPages())<div class="card-footer">{{ $entries->links('vendor.pagination.simple') }}</div>@endif
</div>
@endsection
