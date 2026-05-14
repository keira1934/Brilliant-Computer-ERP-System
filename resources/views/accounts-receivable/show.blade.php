@extends('layouts.app')
@php $title = 'AR ' . $invoice->invoice_number; @endphp
@section('content')
<div class="page-header">
    <div>
        <h1>{{ $invoice->invoice_number }}</h1>
        <p class="page-sub">{{ $invoice->customer?->name ?? 'Walk-in Customer' }} - {{ $invoice->invoice_date->format('d F Y') }}</p>
    </div>
    <a href="{{ route('accounts-receivable.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="grid-2 mb-5" style="align-items:start">
    <div class="card">
        <div class="card-header"><h5 class="card-title">Invoice Control</h5></div>
        <div class="card-body">
            <table style="width:100%;font-size:13.5px">
                <tr><td class="td-muted" style="padding:5px 0;width:140px">Invoice</td><td class="font-mono fw-semibold">{{ $invoice->invoice_number }}</td></tr>
                <tr><td class="td-muted" style="padding:5px 0">Sale</td><td>{{ $invoice->sale?->sale_number ?? '-' }}</td></tr>
                <tr><td class="td-muted" style="padding:5px 0">Due Date</td><td>{{ $invoice->due_date?->format('d M Y') ?? '-' }}</td></tr>
                <tr><td class="td-muted" style="padding:5px 0">Status</td><td><span class="badge {{ $invoice->status === 'Paid' ? 'badge-success' : 'badge-warning' }}">{{ $invoice->status }}</span></td></tr>
                <tr><td class="td-muted" style="padding:5px 0">Outstanding</td><td class="fw-bold">Rp {{ number_format($invoice->outstanding,0,',','.') }}</td></tr>
            </table>
        </div>
    </div>

    @if($invoice->outstanding > 0 && $invoice->status !== 'Cancelled')
    <div class="card">
        <div class="card-header"><h5 class="card-title">Record Collection</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('accounts-receivable.payments.store', $invoice) }}">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Date</label>
                        <input name="payment_date" type="date" value="{{ old('payment_date', date('Y-m-d')) }}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Amount</label>
                        <input name="amount" type="number" min="0.01" max="{{ $invoice->outstanding }}" step="0.01" value="{{ old('amount', $invoice->outstanding) }}" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Method</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="Cash">Cash</option>
                            <option value="Transfer">Bank Transfer</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Reference</label>
                        <input name="reference" value="{{ old('reference') }}" class="form-control">
                    </div>
                </div>
                <button class="btn btn-success w-100" onclick="return confirm('Record and post this customer payment?')"><i class="bi bi-cash-coin"></i> Record Payment</button>
            </form>
        </div>
    </div>
    @endif
</div>

<div class="card">
    <div class="card-header"><h5 class="card-title">Payment History</h5></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Payment</th><th>Date</th><th>Method</th><th>Reference</th><th class="text-right">Amount</th></tr></thead>
            <tbody>
            @forelse($invoice->payments as $payment)
            <tr>
                <td class="font-mono td-primary">{{ $payment->payment_number }}</td>
                <td class="td-muted">{{ $payment->payment_date->format('d/m/Y') }}</td>
                <td>{{ $payment->payment_method }}</td>
                <td>{{ $payment->reference ?? '-' }}</td>
                <td class="text-right fw-bold">Rp {{ number_format($payment->amount,0,',','.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5"><div class="empty-state"><i class="bi bi-cash"></i><p>No collections recorded yet</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
