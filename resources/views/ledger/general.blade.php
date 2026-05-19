@extends('layouts.app')
@php $title = 'General Ledger'; @endphp
@section('content')
<div class="page-header">
    <div><h1>General Ledger</h1><p class="page-sub">Posted journal lines, opening balances, and running balances</p></div>
    <a href="{{ request()->fullUrlWithQuery(['print'=>1]) }}" target="_blank" class="btn btn-outline"><i class="bi bi-printer"></i> Print All</a>
</div>

<div class="card mb-4">
    <div class="card-body" style="padding-bottom:0">
        <form class="filter-bar" method="GET">
            <select name="account_id" class="form-control" style="width:240px">
                <option value="">All Accounts</option>
                @foreach($accounts as $acc)
                <option value="{{ $acc->id }}" {{ $accountId==$acc->id?'selected':'' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                @endforeach
            </select>
            <input name="from" type="date" value="{{ $from }}" class="form-control" style="width:160px">
            <input name="to" type="date" value="{{ $to }}" class="form-control" style="width:160px">
            <button class="btn btn-secondary"><i class="bi bi-funnel"></i> View</button>
            <a href="{{ route('ledger.general') }}" class="btn btn-outline">Reset</a>
        </form>
    </div>
</div>

@if($selectedAccount)
<div class="grid-3 mb-4">
    <div class="kpi-card"><span class="kpi-label">Opening Balance</span><div class="kpi-value">Rp {{ number_format($openingBalance,0,',','.') }}</div><span class="td-muted">Before {{ $from }}</span></div>
    <div class="kpi-card"><span class="kpi-label">Current Balance</span><div class="kpi-value text-navy">Rp {{ number_format($endingBalance,0,',','.') }}</div><span class="td-muted">{{ $selectedAccount->code }} - {{ $selectedAccount->name }}</span></div>
    <div class="kpi-card"><span class="kpi-label">Period Movement</span><div class="kpi-value {{ ($endingBalance - $openingBalance) >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format(abs($endingBalance - $openingBalance),0,',','.') }}</div><span class="td-muted">{{ $from }} to {{ $to }}</span></div>
</div>
@endif

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Ledger Lines</h5>
        <span class="badge badge-navy">{{ $isPrint ? $lines->count() : $lines->total() }} entries</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th><th>Reference</th><th>Account</th><th>Description</th>
                    <th class="text-right">Debit</th><th class="text-right">Credit</th>
                    @if($selectedAccount)<th class="text-right">Running Balance</th>@endif
                </tr>
            </thead>
            <tbody>
            @if($selectedAccount)
            <tr>
                <td class="td-muted font-mono">{{ \Illuminate\Support\Carbon::parse($from)->subDay()->format('d/m/Y') }}</td>
                <td class="font-mono">OPENING</td>
                <td><span class="font-mono" style="font-size:12px">{{ $selectedAccount->code }}</span><br><span class="td-muted" style="font-size:11px">{{ $selectedAccount->name }}</span></td>
                <td class="fw-semibold">Opening balance brought forward</td>
                <td class="text-right td-muted">-</td>
                <td class="text-right td-muted">-</td>
                <td class="text-right fw-bold font-mono">Rp {{ number_format($openingBalance,0,',','.') }}</td>
            </tr>
            @endif
            @forelse($isPrint ? $lines : $lines as $line)
            <tr>
                <td class="td-muted font-mono">{{ \Illuminate\Support\Carbon::parse($line->entry_date)->format('d/m/Y') }}<br><span style="font-size:11px">{{ $line->entry_created_at ? \Illuminate\Support\Carbon::parse($line->entry_created_at)->timezone(config('app.timezone'))->format('H:i:s') : '' }}</span></td>
                <td class="td-muted font-mono" style="font-size:11px">{{ $line->journalEntry->reference_type }}-{{ $line->journalEntry->reference_id }}</td>
                <td><span class="font-mono" style="font-size:12px">{{ $line->account->code }}</span><br><span class="td-muted" style="font-size:11px">{{ $line->account->name }}</span></td>
                <td style="font-size:12.5px">{{ $line->entry_desc }}<br><span class="td-muted" style="font-size:11px">{{ $line->description }}</span></td>
                <td class="text-right fw-semibold {{ $line->debit > 0 ? '' : 'td-muted' }}">{{ $line->debit > 0 ? 'Rp '.number_format($line->debit,0,',','.') : '-' }}</td>
                <td class="text-right fw-semibold {{ $line->credit > 0 ? '' : 'td-muted' }}">{{ $line->credit > 0 ? 'Rp '.number_format($line->credit,0,',','.') : '-' }}</td>
                @if($selectedAccount)<td class="text-right fw-bold font-mono">Rp {{ number_format($line->running_balance ?? 0,0,',','.') }}</td>@endif
            </tr>
            @empty
            <tr><td colspan="{{ $selectedAccount ? 7 : 6 }}"><div class="empty-state"><i class="bi bi-journal-text"></i><p>No ledger entries for this period</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if(!$isPrint && $lines->hasPages())<div class="card-footer">{{ $lines->links('vendor.pagination.simple') }}</div>@endif
</div>
@endsection
