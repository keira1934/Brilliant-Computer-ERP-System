@extends('layouts.app')
@php $title = 'Financial Period Detail'; @endphp
@section('content')
<div class="page-header">
    <div>
        <h1>{{ $period->name }}</h1>
        <p class="page-sub">{{ $period->start_date->format('d M Y') }} - {{ $period->end_date->format('d M Y') }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('financial-periods.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Periods</a>
        @if($period->isOpen())
            <form method="POST" action="{{ route('financial-periods.close', $period) }}" onsubmit="return confirm('Close this period and post closing entries?')">
                @csrf
                <button class="btn btn-danger"><i class="bi bi-lock"></i> Close Period</button>
            </form>
        @else
            <form method="POST" action="{{ route('financial-periods.reopen', $period) }}" onsubmit="return confirm('Reopen this closed period?')">
                @csrf
                <button class="btn btn-secondary"><i class="bi bi-unlock"></i> Reopen</button>
            </form>
        @endif
    </div>
</div>

<div class="grid-4 mb-5">
    <div class="metric-card"><div class="metric-label">Status</div><div class="metric-value">{{ ucfirst($period->status) }}</div></div>
    <div class="metric-card"><div class="metric-label">Created</div><div class="metric-value">{{ $period->created_at?->format('d M Y') }}</div></div>
    <div class="metric-card"><div class="metric-label">Closed</div><div class="metric-value">{{ $period->closed_at?->format('d M Y H:i') ?? '-' }}</div></div>
    <div class="metric-card"><div class="metric-label">Journal Entries</div><div class="metric-value">{{ number_format($journalCount) }}</div></div>
</div>

<div class="kpi-grid">
    <div class="kpi-card"><div class="kpi-icon success"><i class="bi bi-graph-up"></i></div><div><div class="kpi-label">Revenue</div><div class="kpi-value text-success">Rp {{ number_format($revenue,0,',','.') }}</div></div></div>
    <div class="kpi-card"><div class="kpi-icon danger"><i class="bi bi-graph-down"></i></div><div><div class="kpi-label">Expenses</div><div class="kpi-value text-danger">Rp {{ number_format($expenses,0,',','.') }}</div></div></div>
    <div class="kpi-card"><div class="kpi-icon {{ $netProfit >= 0 ? 'success' : 'danger' }}"><i class="bi bi-calculator"></i></div><div><div class="kpi-label">{{ $netProfit >= 0 ? 'Net Profit' : 'Net Loss' }}</div><div class="kpi-value {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format(abs($netProfit),0,',','.') }}</div></div></div>
    <div class="kpi-card"><div class="kpi-icon navy"><i class="bi bi-cash-stack"></i></div><div><div class="kpi-label">Ending Cash</div><div class="kpi-value text-navy">Rp {{ number_format($endingCash,0,',','.') }}</div></div></div>
</div>

<div class="grid-3 mb-5">
    <div class="card">
        <div class="card-header"><h3><i class="bi bi-clipboard-data"></i> Trial Balance</h3></div>
        <div class="card-body">
            <div class="report-row"><span>Debit</span><strong>Rp {{ number_format($trialDebit,2) }}</strong></div>
            <div class="report-row"><span>Credit</span><strong>Rp {{ number_format($trialCredit,2) }}</strong></div>
            <div class="report-total-row"><span>Status</span><strong class="{{ $trialBalanced ? 'text-success' : 'text-danger' }}">{{ $trialBalanced ? 'Balanced' : 'Unbalanced' }}</strong></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3><i class="bi bi-bank"></i> Balance Sheet</h3></div>
        <div class="card-body">
            <div class="report-row"><span>Assets</span><strong>Rp {{ number_format($balanceSheet['totalAssets'],2) }}</strong></div>
            <div class="report-row"><span>Liabilities</span><strong>Rp {{ number_format($balanceSheet['totalLiabilities'],2) }}</strong></div>
            <div class="report-row"><span>Equity</span><strong>Rp {{ number_format($balanceSheet['totalEquity'],2) }}</strong></div>
            <div class="report-total-row"><span>Status</span><strong class="{{ $balanceSheet['isBalanced'] ? 'text-success' : 'text-danger' }}">{{ $balanceSheet['isBalanced'] ? 'Balanced' : 'Unbalanced' }}</strong></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3><i class="bi bi-arrow-left-right"></i> Cash Flow</h3></div>
        <div class="card-body">
            <div class="report-row"><span>Opening Cash</span><strong>Rp {{ number_format($openingCash,2) }}</strong></div>
            <div class="report-row"><span>Net Cash Flow</span><strong>Rp {{ number_format($netCash,2) }}</strong></div>
            <div class="report-total-row"><span>Ending Cash</span><strong>Rp {{ number_format($endingCash,2) }}</strong></div>
        </div>
    </div>
</div>

<div class="grid-4 mb-5">
    <div class="metric-card"><div class="metric-label">AR Ledger</div><div class="metric-value">Rp {{ number_format($arBalance,0,',','.') }}</div></div>
    <div class="metric-card"><div class="metric-label">AP Ledger</div><div class="metric-value">Rp {{ number_format($apBalance,0,',','.') }}</div></div>
    <div class="metric-card"><div class="metric-label">Inventory Value</div><div class="metric-value">Rp {{ number_format($inventoryValuation,0,',','.') }}</div></div>
    <div class="metric-card"><div class="metric-label">Journal Lines</div><div class="metric-value">{{ number_format($transactionCount) }}</div></div>
</div>

<div class="card">
    <div class="card-header"><h3><i class="bi bi-clock-history"></i> Recent Activities</h3></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Date</th><th>Journal</th><th>Description</th><th class="text-right">Debit</th><th class="text-right">Credit</th></tr></thead>
            <tbody>
            @forelse($recentEntries as $entry)
                <tr>
                    <td class="font-mono td-muted">{{ $entry->entry_date->format('d/m/Y') }}</td>
                    <td class="font-mono">{{ $entry->journal_number }}</td>
                    <td>{{ $entry->description }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($entry->getTotalDebit(),2) }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($entry->getTotalCredit(),2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty-state"><i class="bi bi-journal"></i><p>No activity in this period</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
