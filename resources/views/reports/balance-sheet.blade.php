@extends('layouts.app')
@section('content')
@php $title = 'Balance Sheet'; @endphp

<div class="report-header">
    <div class="report-title-block">
        <h2><i class="bi bi-journal-bookmark-fill"></i> Balance Sheet</h2>
        <p class="report-period">As of: {{ \Carbon\Carbon::parse($asOf)->format('d F Y') }}</p>
    </div>
    <form class="report-filters" method="GET">
        <label class="form-label">As of Date</label>
        <input type="date" name="as_of" value="{{ $asOf }}" class="form-input">
        <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Generate</button>
    </form>
</div>

@if(!$isBalanced)
<div class="alert alert-danger">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <span><strong>WARNING:</strong> Balance Sheet does NOT balance. Assets (Rp {{ number_format($totalAssets, 2) }}) ≠ Liabilities + Equity (Rp {{ number_format($totalLiabilities + $totalEquity, 2) }})</span>
</div>
@else
<div class="alert alert-success">
    <i class="bi bi-check-circle-fill"></i>
    <span>Balance Sheet is balanced. Assets = Liabilities + Equity = Rp {{ number_format($totalAssets, 2) }}</span>
</div>
@endif

<div class="balance-sheet-grid">
    {{-- ASSETS --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="bi bi-building"></i> ASSETS</h3>
        </div>
        <div class="card-body p-0">
            <table class="data-table compact">
                <thead>
                    <tr>
                        <th style="width:80px">Code</th>
                        <th>Account</th>
                        <th class="text-right" style="width:160px">Balance (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assetAccounts as $account)
                    <tr>
                        <td class="font-mono">{{ $account->code }}</td>
                        <td>{{ $account->name }} @if(!empty($account->is_contra))<span class="badge badge-gray">Contra Asset</span>@endif</td>
                        <td class="text-right font-mono {{ $account->balance < 0 ? 'text-danger' : '' }}">
                            {{ $account->balance < 0 ? '(' . number_format(abs($account->balance), 2) . ')' : number_format($account->balance, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="2"><strong>TOTAL ASSETS</strong></td>
                        <td class="text-right font-mono"><strong>{{ number_format($totalAssets, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- LIABILITIES + EQUITY --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="bi bi-bank"></i> LIABILITIES & EQUITY</h3>
        </div>
        <div class="card-body p-0">
            <table class="data-table compact">
                <thead>
                    <tr>
                        <th style="width:80px">Code</th>
                        <th>Account</th>
                        <th class="text-right" style="width:160px">Balance (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Liabilities --}}
                    <tr class="section-header"><td colspan="3"><strong>Liabilities</strong></td></tr>
                    @foreach($liabilityAccounts as $account)
                    <tr>
                        <td class="font-mono">{{ $account->code }}</td>
                        <td>{{ $account->name }}</td>
                        <td class="text-right font-mono">{{ number_format($account->balance, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="subtotal-row">
                        <td colspan="2">Total Liabilities</td>
                        <td class="text-right font-mono">{{ number_format($totalLiabilities, 2) }}</td>
                    </tr>

                    {{-- Equity --}}
                    <tr class="section-header"><td colspan="3"><strong>Equity</strong></td></tr>
                    @foreach($equityAccounts as $account)
                    <tr>
                        <td class="font-mono">{{ $account->code }}</td>
                        <td>{{ $account->name }}</td>
                        <td class="text-right font-mono">{{ number_format($account->balance, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td></td>
                        <td><em>Current Period Earnings (Revenue - Expenses)</em></td>
                        <td class="text-right font-mono"><em>{{ number_format($currentEarnings, 2) }}</em></td>
                    </tr>
                    <tr class="subtotal-row">
                        <td colspan="2">Total Equity</td>
                        <td class="text-right font-mono">{{ number_format($totalEquity, 2) }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="2"><strong>TOTAL LIABILITIES & EQUITY</strong></td>
                        <td class="text-right font-mono"><strong>{{ number_format($totalLiabilities + $totalEquity, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="accounting-equation">
    <div class="eq-block">
        <span class="eq-label">Assets</span>
        <span class="eq-value">Rp {{ number_format($totalAssets, 2) }}</span>
    </div>
    <span class="eq-operator">=</span>
    <div class="eq-block">
        <span class="eq-label">Liabilities</span>
        <span class="eq-value">Rp {{ number_format($totalLiabilities, 2) }}</span>
    </div>
    <span class="eq-operator">+</span>
    <div class="eq-block">
        <span class="eq-label">Equity</span>
        <span class="eq-value">Rp {{ number_format($totalEquity, 2) }}</span>
    </div>
</div>

@php
    $hasOpeningBalances = \App\Models\ChartOfAccount::where('opening_balance', '!=', 0)->exists();
@endphp
@if(!$hasOpeningBalances)
<div class="alert alert-warning" style="margin-top:16px">
    <i class="bi bi-info-circle-fill"></i>
    <span>
        <strong>No opening balances set.</strong>
        If Cash or Bank balances appear negative, it may be because no beginning balances have been entered.
        <a href="{{ route('ledger.coa') }}" style="color:inherit;font-weight:700;text-decoration:underline">Set opening balances in Chart of Accounts →</a>
    </span>
</div>
@endif
@endsection
