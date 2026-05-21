@extends('layouts.app')
@php $title = 'Accounts Receivable'; @endphp
@section('content')
<div class="page-header">
    <div>
        <h1>Accounts Receivable</h1>
        <p class="page-sub">Customer invoices, collections, and aging control</p>
    </div>
</div>

{{-- Pending verifications alert — Finance/Manager only --}}
@if($pendingVerifications > 0 && auth()->user()->hasRole('finance','manager'))
<div class="alert" style="background:var(--warning-50,#fffbeb); border:1px solid var(--warning-300,#fcd34d);
     color:var(--warning-800,#92400e); border-radius:10px; display:flex; align-items:center;
     gap:12px; padding:14px 18px; margin-bottom:20px">
    <i class="bi bi-clock-history" style="font-size:1.3rem; flex-shrink:0"></i>
    <div>
        <strong>{{ $pendingVerifications }} payment{{ $pendingVerifications > 1 ? 's' : '' }} pending your verification.</strong>
        Open the relevant invoice below to verify or reject.
    </div>
</div>
@endif

<div class="grid-3 mb-5">
    <div class="metric-card">
        <span class="metric-label">AR Ledger Balance</span>
        <span class="metric-value">Rp {{ number_format($ledgerBalance,0,',','.') }}</span>
        <span class="metric-sub" style="font-size:11px; color:var(--gray-400)">From posted journal entries (acct 1-1200)</span>
    </div>
    <div class="metric-card">
        <span class="metric-label">Invoice Outstanding</span>
        <span class="metric-value">Rp {{ number_format($invoiceOutstanding,0,',','.') }}</span>
        <span class="metric-sub" style="font-size:11px; color:var(--gray-400)">Sum of open &amp; partial invoices</span>
    </div>
    <div class="metric-card" style="{{ round($ledgerBalance,2) !== round($invoiceOutstanding,2) ? 'border:1px solid #fca5a5' : '' }}">
        <span class="metric-label">
            @if(round($ledgerBalance,2) === round($invoiceOutstanding,2))
                <i class="bi bi-check-circle-fill" style="color:#15803d"></i> Balanced
            @else
                <i class="bi bi-exclamation-triangle-fill" style="color:#b45309"></i> Unmatched
            @endif
        </span>
        <span class="metric-value" style="font-size:18px; color:{{ round($ledgerBalance,2) === round($invoiceOutstanding,2) ? '#15803d' : '#b45309' }}">
            Rp {{ number_format(abs($ledgerBalance - $invoiceOutstanding),0,',','.') }}
        </span>
        <span class="metric-sub" style="font-size:11px; color:var(--gray-400)">
            @if(round($ledgerBalance,2) === round($invoiceOutstanding,2))
                Ledger matches invoices
            @elseif($ledgerBalance > $invoiceOutstanding)
                Ledger &gt; Invoices (untracked receivable)
            @else
                Invoices &gt; Ledger (pending verifications not yet posted)
            @endif
        </span>
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
            <select name="status" class="form-control" style="width:200px">
                <option value="">All Status</option>
                <option value="Open"             {{ request('status')==='Open'             ?'selected':'' }}>Open</option>
                <option value="Pending Verification" {{ request('status')==='Pending Verification' ?'selected':'' }}>Pending Verification</option>
                <option value="Payment Rejected" {{ request('status')==='Payment Rejected' ?'selected':'' }}>Payment Rejected</option>
                <option value="Partially Paid"   {{ request('status')==='Partially Paid'   ?'selected':'' }}>Partial & Verified</option>
                <option value="Paid"             {{ request('status')==='Paid'             ?'selected':'' }}>Paid & Verified</option>
                <option value="Cancelled"        {{ request('status')==='Cancelled'        ?'selected':'' }}>Cancelled</option>
            </select>
            <input type="date" name="as_of" value="{{ $asOf }}" class="form-control" style="width:160px">
            <button class="btn btn-secondary"><i class="bi bi-funnel"></i> Filter</button>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Due</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Paid</th>
                    <th class="text-right">Outstanding</th>
                    <th style="text-align:center">Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($invoices as $invoice)
            @php
                // Use the LATEST payment to determine verification state
                $latestPayment = $invoice->payments->sortByDesc('created_at')->first();

                if ($latestPayment && $latestPayment->isPending()) {
                    $combinedLabel = 'Pending Verification';
                    $combinedIcon  = 'bi-clock-history';
                    $combinedStyle = 'background:#fffbeb; color:#b45309; border:1px solid #fcd34d';
                } elseif ($latestPayment && $latestPayment->isRejected()) {
                    $combinedLabel = 'Payment Rejected';
                    $combinedIcon  = 'bi-x-circle-fill';
                    $combinedStyle = 'background:#fef2f2; color:#dc2626; border:1px solid #fca5a5';
                } elseif ($invoice->status === 'Paid') {
                    $combinedLabel = 'Paid & Verified';
                    $combinedIcon  = 'bi-check-circle-fill';
                    $combinedStyle = 'background:#f0fdf4; color:#15803d; border:1px solid #86efac';
                } elseif ($invoice->status === 'Partially Paid') {
                    $combinedLabel = 'Partial & Verified';
                    $combinedIcon  = 'bi-check-circle';
                    $combinedStyle = 'background:#eff6ff; color:#2563eb; border:1px solid #93c5fd';
                } elseif ($invoice->status === 'Cancelled') {
                    $combinedLabel = 'Cancelled';
                    $combinedIcon  = 'bi-slash-circle';
                    $combinedStyle = 'background:#f9fafb; color:#6b7280; border:1px solid #d1d5db';
                } else {
                    $combinedLabel = 'Open';
                    $combinedIcon  = 'bi-circle';
                    $combinedStyle = 'background:#f8fafc; color:#475569; border:1px solid #cbd5e1';
                }
            @endphp
            <tr>
                <td class="td-primary font-mono">{{ $invoice->invoice_number }}</td>
                <td>{{ $invoice->customer?->name ?? 'Walk-in Customer' }}</td>
                <td class="td-muted">{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                <td class="td-muted">{{ $invoice->due_date?->format('d/m/Y') ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($invoice->total,0,',','.') }}</td>
                <td class="text-right td-muted">Rp {{ number_format($invoice->paid_amount,0,',','.') }}</td>
                <td class="text-right fw-bold">Rp {{ number_format($invoice->outstanding,0,',','.') }}</td>
                <td style="text-align:center">
                    <span style="display:inline-flex; align-items:center; gap:5px; padding:4px 10px;
                                 border-radius:20px; font-size:12px; font-weight:600; white-space:nowrap;
                                 {{ $combinedStyle }}">
                        <i class="bi {{ $combinedIcon }}"></i>
                        {{ $combinedLabel }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('accounts-receivable.show', $invoice) }}"
                       class="btn btn-sm btn-outline"><i class="bi bi-eye"></i></a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9">
                    <div class="empty-state"><i class="bi bi-receipt"></i><p>No receivable invoices found</p></div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())<div class="card-footer">{{ $invoices->links('vendor.pagination.simple') }}</div>@endif
</div>
@endsection
