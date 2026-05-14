@extends('layouts.app')
@section('content')
@php $title = 'Trial Balance'; @endphp

<div class="report-header">
    <div class="report-title-block">
        <h2><i class="bi bi-clipboard-data"></i> Trial Balance</h2>
        <p class="report-period">Period: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
    </div>
    <form class="report-filters" method="GET">
        <input type="date" name="from" value="{{ $from }}" class="form-input">
        <input type="date" name="to" value="{{ $to }}" class="form-input">
        <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
    </form>
</div>

@if(!$isBalanced)
<div class="alert alert-danger">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <span><strong>ACCOUNTING ERROR:</strong> Trial balance is NOT balanced. Total Debits (Rp {{ number_format($totalDebit, 2) }}) ≠ Total Credits (Rp {{ number_format($totalCredit, 2) }}). Financial statements cannot be generated until this is resolved.</span>
</div>
@else
<div class="alert alert-success">
    <i class="bi bi-check-circle-fill"></i>
    <span>Trial balance is balanced. Debit = Credit = Rp {{ number_format($totalDebit, 2) }}</span>
</div>
@endif

<div class="card">
    <div class="card-body p-0">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:100px">Code</th>
                    <th>Account Name</th>
                    <th style="width:100px">Type</th>
                    <th class="text-right" style="width:160px">Debit (Rp)</th>
                    <th class="text-right" style="width:160px">Credit (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $account)
                <tr>
                    <td class="font-mono">{{ $account->code }}</td>
                    <td>{{ $account->name }}</td>
                    <td><span class="badge badge-{{ $account->type }}">{{ $account->getTypeLabel() }}</span></td>
                    <td class="text-right font-mono">{{ $account->tb_debit > 0 ? number_format($account->tb_debit, 2) : '—' }}</td>
                    <td class="text-right font-mono">{{ $account->tb_credit > 0 ? number_format($account->tb_credit, 2) : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">No journal transactions found for this period.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="total-row {{ $isBalanced ? 'balanced' : 'unbalanced' }}">
                    <td colspan="3"><strong>TOTAL</strong></td>
                    <td class="text-right font-mono"><strong>{{ number_format($totalDebit, 2) }}</strong></td>
                    <td class="text-right font-mono"><strong>{{ number_format($totalCredit, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
