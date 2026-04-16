@extends('layouts.app')
@php $title = 'General Ledger'; @endphp
@section('content')
<div class="page-header">
    <div><h1>General Ledger</h1><p class="page-sub">All journal lines by account and date</p></div>
    <a href="{{ request()->fullUrlWithQuery(['print'=>1]) }}" target="_blank" class="btn btn-outline"><i class="bi bi-printer"></i> Print All</a>
</div>

<div class="card mb-4">
    <div class="card-body" style="padding-bottom:0">
        <form class="filter-bar" method="GET">
            <select name="account_id" class="form-control" style="width:240px">
                <option value="">All Accounts</option>
                @foreach($accounts as $acc)
                <option value="{{ $acc->id }}" {{ $accountId==$acc->id?'selected':'' }}>{{ $acc->code }} — {{ $acc->name }}</option>
                @endforeach
            </select>
            <input name="from" type="date" value="{{ $from }}" class="form-control" style="width:160px">
            <input name="to"   type="date" value="{{ $to }}"   class="form-control" style="width:160px">
            <button class="btn btn-secondary"><i class="bi bi-funnel"></i> View</button>
            <a href="{{ route('ledger.general') }}" class="btn btn-outline">Reset</a>
        </form>
    </div>
</div>

@if($selectedAccount)
<div class="kpi-card mb-4" style="max-width:300px">
    <span class="kpi-label">{{ $selectedAccount->code }} — {{ $selectedAccount->name }}</span>
    <div class="kpi-value">Rp {{ number_format($selectedAccount->getBalance($from, $to),0,',','.') }}</div>
    <span class="td-muted">Balance ({{ $from }} to {{ $to }})</span>
</div>
@endif

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Ledger Lines</h5>
        <span class="badge badge-navy">{{ $isPrint ? $lines->count() : $lines->total() }} entries</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Date</th><th>Reference</th><th>Account</th><th>Description</th><th class="text-right">Debit</th><th class="text-right">Credit</th></tr></thead>
            <tbody>
            @forelse($isPrint ? $lines : $lines as $line)
            <tr>
                <td class="td-muted font-mono">{{ $line->entry_date }}</td>
                <td class="td-muted font-mono" style="font-size:11px">{{ $line->journalEntry->reference_type }}-{{ $line->journalEntry->reference_id }}</td>
                <td><span class="font-mono" style="font-size:12px">{{ $line->account->code }}</span><br><span class="td-muted" style="font-size:11px">{{ $line->account->name }}</span></td>
                <td style="font-size:12.5px">{{ $line->entry_desc }}<br><span class="td-muted" style="font-size:11px">{{ $line->description }}</span></td>
                <td class="text-right fw-semibold {{ $line->debit > 0 ? '' : 'td-muted' }}">{{ $line->debit > 0 ? 'Rp '.number_format($line->debit,0,',','.') : '-' }}</td>
                <td class="text-right fw-semibold {{ $line->credit > 0 ? '' : 'td-muted' }}">{{ $line->credit > 0 ? 'Rp '.number_format($line->credit,0,',','.') : '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="6"><div class="empty-state"><i class="bi bi-journal-text"></i><p>No ledger entries for this period</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if(!$isPrint && $lines->hasPages())<div class="card-footer">{{ $lines->links('vendor.pagination.simple') }}</div>@endif
</div>
@endsection
