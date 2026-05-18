@extends('layouts.app')
@php $title = 'Cash Flow Statement'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Cash Flow Statement</h1><p class="page-sub">Cash & Bank inflows and outflows — {{ $from }} to {{ $to }}</p></div>
    <a href="{{ request()->fullUrlWithQuery(['print'=>1]) }}" target="_blank" class="btn btn-outline"><i class="bi bi-printer"></i> Print</a>
</div>

<div class="card mb-4">
    <div class="card-body" style="padding-bottom:0">
        <form class="filter-bar" method="GET">
            <input name="from" type="date" value="{{ $from }}" class="form-control" style="width:160px">
            <input name="to"   type="date" value="{{ $to }}"   class="form-control" style="width:160px">
            <button class="btn btn-secondary"><i class="bi bi-funnel"></i> Generate</button>
            <a href="{{ route('reports.cash-flow') }}" class="btn btn-outline">Current Year</a>
        </form>
    </div>
</div>

<div class="grid-3 mb-5">
    <div class="kpi-card"><span class="kpi-label">Total Cash In</span><div class="kpi-value text-success">Rp {{ number_format($totalIn,0,',','.') }}</div></div>
    <div class="kpi-card"><span class="kpi-label">Total Cash Out</span><div class="kpi-value text-danger">Rp {{ number_format($totalOut,0,',','.') }}</div></div>
    <div class="kpi-card"><span class="kpi-label">Net Cash InFlow</span><div class="kpi-value {{ $netCash >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($netCash,0,',','.') }}</div></div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header" style="background:linear-gradient(135deg,#166534,#16a34a);color:white">
            <h5 class="card-title" style="color:white"><i class="bi bi-arrow-down-circle" style="margin-right:8px"></i>Cash Inflows</h5>
            <span style="font-weight:700">Rp {{ number_format($totalIn,0,',','.') }}</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Date</th><th>Description</th><th class="text-right">Amount</th></tr></thead>
                <tbody>
                @forelse($cashIn as $row)
                <tr>
                    <td class="td-muted font-mono">{{ \Illuminate\Support\Carbon::parse($row->entry_date)->format('d/m/Y') }}<br><span style="font-size:11px">{{ $row->entry_created_at ? \Illuminate\Support\Carbon::parse($row->entry_created_at)->timezone(config('app.timezone'))->format('H:i:s') : '' }}</span></td>
                    <td style="font-size:12.5px">{{ $row->entry_desc }}</td>
                    <td class="text-right fw-semibold text-success">Rp {{ number_format($row->debit,0,',','.') }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center td-muted" style="padding:20px">No inflows</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="background:linear-gradient(135deg,#7f1d1d,#dc2626);color:white">
            <h5 class="card-title" style="color:white"><i class="bi bi-arrow-up-circle" style="margin-right:8px"></i>Cash Outflows</h5>
            <span style="font-weight:700">Rp {{ number_format($totalOut,0,',','.') }}</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Date</th><th>Description</th><th class="text-right">Amount</th></tr></thead>
                <tbody>
                @forelse($cashOut as $row)
                <tr>
                    <td class="td-muted font-mono">{{ \Illuminate\Support\Carbon::parse($row->entry_date)->format('d/m/Y') }}<br><span style="font-size:11px">{{ $row->entry_created_at ? \Illuminate\Support\Carbon::parse($row->entry_created_at)->timezone(config('app.timezone'))->format('H:i:s') : '' }}</span></td>
                    <td style="font-size:12.5px">{{ $row->entry_desc }}</td>
                    <td class="text-right fw-semibold text-danger">Rp {{ number_format($row->credit,0,',','.') }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center td-muted" style="padding:20px">No outflows</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
