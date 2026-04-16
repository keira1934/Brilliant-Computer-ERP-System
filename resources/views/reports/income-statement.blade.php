@extends('layouts.app')
@php $title = 'Income Statement'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Income Statement</h1><p class="page-sub">Revenue, Expenses & Net Profit — {{ $from }} to {{ $to }}</p></div>
    <a href="{{ request()->fullUrlWithQuery(['print'=>1]) }}" target="_blank" class="btn btn-outline"><i class="bi bi-printer"></i> Print</a>
</div>

<div class="card mb-4">
    <div class="card-body" style="padding-bottom:0">
        <form class="filter-bar" method="GET">
            <input name="from" type="date" value="{{ $from }}" class="form-control" style="width:160px">
            <input name="to"   type="date" value="{{ $to }}"   class="form-control" style="width:160px">
            <button class="btn btn-secondary"><i class="bi bi-funnel"></i> Generate</button>
            <a href="{{ route('reports.income-statement') }}" class="btn btn-outline">Current Year</a>
        </form>
    </div>
</div>

<div style="max-width:700px">
    <div class="card mb-4">
        <div class="card-header" style="background:var(--navy-800);color:white">
            <h5 class="card-title" style="color:white;font-size:16px">Briliant Computer — Income Statement</h5>
            <span style="font-size:12px;opacity:0.75">{{ $from }} to {{ $to }}</span>
        </div>

        {{-- Revenue Section --}}
        <div style="padding:16px 20px;border-bottom:2px solid var(--navy-100)">
            <div style="font-weight:700;font-size:13px;text-transform:uppercase;color:var(--navy-600);letter-spacing:.5px;margin-bottom:10px">Revenue</div>
            @foreach($revenueAccounts as $acc)
            <div style="display:flex;justify-content:space-between;padding:5px 0;font-size:13.5px">
                <span style="color:var(--gray-700)"><span class="font-mono td-muted" style="min-width:65px;display:inline-block">{{ $acc->code }}</span> {{ $acc->name }}</span>
                <span class="fw-semibold">Rp {{ number_format($acc->period_balance,0,',','.') }}</span>
            </div>
            @endforeach
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-top:1px solid var(--navy-200);margin-top:6px;font-weight:700;color:var(--navy-800)">
                <span>Total Revenue</span>
                <span style="font-size:15px">Rp {{ number_format($totalRevenue,0,',','.') }}</span>
            </div>
        </div>

        {{-- Expense Section --}}
        <div style="padding:16px 20px;border-bottom:2px solid var(--navy-100)">
            <div style="font-weight:700;font-size:13px;text-transform:uppercase;color:var(--danger);letter-spacing:.5px;margin-bottom:10px">Expenses</div>
            @foreach($expenseAccounts as $acc)
            <div style="display:flex;justify-content:space-between;padding:5px 0;font-size:13.5px">
                <span style="color:var(--gray-700)"><span class="font-mono td-muted" style="min-width:65px;display:inline-block">{{ $acc->code }}</span> {{ $acc->name }}</span>
                <span class="fw-semibold">Rp {{ number_format($acc->period_balance,0,',','.') }}</span>
            </div>
            @endforeach
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-top:1px solid var(--gray-200);margin-top:6px;font-weight:700;color:var(--danger)">
                <span>Total Expenses</span>
                <span style="font-size:15px">Rp {{ number_format($totalExpenses,0,',','.') }}</span>
            </div>
        </div>

        {{-- Net Profit --}}
        <div style="padding:20px;background:{{ $netProfit >= 0 ? 'var(--navy-800)' : '#7f1d1d' }};color:white;display:flex;justify-content:space-between;align-items:center;border-radius:0 0 12px 12px">
            <span style="font-size:16px;font-weight:700">{{ $netProfit >= 0 ? 'Net Profit' : 'Net Loss' }}</span>
            <span style="font-size:22px;font-weight:800">Rp {{ number_format(abs($netProfit),0,',','.') }}</span>
        </div>
    </div>
</div>
@endsection
