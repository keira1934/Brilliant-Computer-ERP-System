@extends('layouts.app')
@php $title = 'AR ' . $invoice->invoice_number; @endphp
@section('content')

<div class="page-header">
    <div>
        <h1>{{ $invoice->invoice_number }}</h1>
        <p class="page-sub">{{ $invoice->customer?->name ?? 'Walk-in Customer' }} &mdash; {{ $invoice->invoice_date->format('d F Y') }}</p>
    </div>
    <a href="{{ route('accounts-receivable.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

{{-- ── Top KPI row ──────────────────────────────────────────────────────── --}}
<div class="kpi-grid" style="grid-template-columns:repeat(4,1fr); margin-bottom:24px">
    <div class="kpi-card">
        <div class="kpi-icon navy"><i class="bi bi-file-earmark-text"></i></div>
        <div>
            <div class="kpi-label">Invoice Total</div>
            <div class="kpi-value" style="font-size:16px">Rp {{ number_format($invoice->total,0,',','.') }}</div>
            <div class="kpi-sub">{{ $invoice->sale?->sale_number ?? '—' }}</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon success"><i class="bi bi-cash-coin"></i></div>
        <div>
            <div class="kpi-label">Paid (Verified)</div>
            <div class="kpi-value" style="font-size:16px">Rp {{ number_format($invoice->paid_amount,0,',','.') }}</div>
            <div class="kpi-sub">Confirmed collections</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon {{ $invoice->outstanding > 0 ? 'warning' : 'success' }}">
            <i class="bi bi-hourglass-split"></i>
        </div>
        <div>
            <div class="kpi-label">Outstanding</div>
            <div class="kpi-value" style="font-size:16px">Rp {{ number_format($invoice->outstanding,0,',','.') }}</div>
            <div class="kpi-sub">Remaining balance</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon {{ $invoice->status === 'Paid' ? 'success' : ($invoice->status === 'Partially Paid' ? 'warning' : 'navy') }}">
            <i class="bi bi-shield-check"></i>
        </div>
        <div>
            <div class="kpi-label">Status</div>
            <div class="kpi-value" style="font-size:15px">
                @php
                    $cls = match($invoice->status) {
                        'Paid'           => 'badge-success',
                        'Partially Paid' => 'badge-warning',
                        'Cancelled'      => 'badge-danger',
                        default          => 'badge-gray',
                    };
                @endphp
                <span class="badge {{ $cls }}">{{ $invoice->status }}</span>
            </div>
            <div class="kpi-sub">Due: {{ $invoice->due_date?->format('d M Y') ?? '—' }}</div>
        </div>
    </div>
</div>

<div class="grid-2" style="gap:24px; align-items:start">

    {{-- ── LEFT column ─────────────────────────────────────────────────── --}}
    <div style="display:flex; flex-direction:column; gap:20px">

        {{-- Invoice detail --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-file-earmark-text" style="color:var(--navy-500)"></i> Invoice Detail</h3>
            </div>
            <div class="card-body">
                <table style="width:100%; font-size:13.5px; border-collapse:collapse">
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:7px 0; color:var(--gray-500); font-weight:600; width:40%">Invoice #</td>
                        <td class="font-mono fw-semibold">{{ $invoice->invoice_number }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:7px 0; color:var(--gray-500); font-weight:600">Customer</td>
                        <td>{{ $invoice->customer?->name ?? 'Walk-in' }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:7px 0; color:var(--gray-500); font-weight:600">Sale Reference</td>
                        <td>{{ $invoice->sale?->sale_number ?? '—' }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:7px 0; color:var(--gray-500); font-weight:600">Invoice Date</td>
                        <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:7px 0; color:var(--gray-500); font-weight:600">Due Date</td>
                        <td>{{ $invoice->due_date?->format('d M Y') ?? '—' }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:7px 0; color:var(--gray-500); font-weight:600">Subtotal</td>
                        <td>Rp {{ number_format($invoice->subtotal,0,',','.') }}</td>
                    </tr>
                    @if($invoice->discount > 0)
                    <tr style="border-bottom:1px solid var(--gray-100)">
                        <td style="padding:7px 0; color:var(--gray-500); font-weight:600">Discount</td>
                        <td class="text-danger">- Rp {{ number_format($invoice->discount,0,',','.') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding:7px 0; color:var(--gray-500); font-weight:600">Total</td>
                        <td class="fw-bold" style="font-size:15px">Rp {{ number_format($invoice->total,0,',','.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Submit payment form — Cashier/Finance/Manager, only when invoice is open --}}
        @if($invoice->outstanding > 0 && !in_array($invoice->status, ['Cancelled','Paid']))
        @php
            $hasPending = $invoice->payments->contains(fn($p) => $p->isPending());
        @endphp

        <div class="card" style="border:2px solid {{ $hasPending ? 'var(--warning-300,#fcd34d)' : 'var(--primary-200,#c7d2fe)' }}">
            <div class="card-header" style="background:{{ $hasPending ? 'var(--warning-50,#fffbeb)' : '' }}">
                <h3 class="card-title">
                    <i class="bi bi-cash-coin" style="color:var(--navy-500)"></i>
                    @if($hasPending)
                        Payment Awaiting Verification
                    @else
                        Record Customer Payment
                    @endif
                </h3>
            </div>
            <div class="card-body">
                @if($hasPending)
                    <div class="alert alert-warning" style="margin-bottom:0">
                        <i class="bi bi-clock-history"></i>
                        <span>A payment has been submitted and is <strong>pending Finance verification</strong>.
                        No new payment can be submitted until it is verified or rejected.</span>
                    </div>
                @else
                    <form method="POST" action="{{ route('accounts-receivable.payments.store', $invoice) }}">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input name="payment_date" type="date"
                                       value="{{ old('payment_date', date('Y-m-d')) }}"
                                       class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Amount (Rp) <span class="text-danger">*</span></label>
                                <input name="amount" type="number" min="0.01"
                                       max="{{ $invoice->outstanding }}" step="0.01"
                                       value="{{ old('amount', $invoice->outstanding) }}"
                                       class="form-input" required>
                                <small class="text-muted">Max: Rp {{ number_format($invoice->outstanding,0,',','.') }}</small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select name="payment_method" class="form-input" required>
                                    <option value="Cash">Cash</option>
                                    <option value="Transfer">Bank Transfer</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Reference / Receipt No.</label>
                                <input name="reference" value="{{ old('reference') }}"
                                       class="form-input" placeholder="e.g. transfer ref, receipt no.">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Notes</label>
                            <input name="notes" value="{{ old('notes') }}" class="form-input"
                                   placeholder="Optional notes for Finance">
                        </div>

                        {{-- Internal control notice --}}
                        <div style="background:var(--blue-50,#eff6ff); border:1px solid var(--blue-200,#bfdbfe);
                                    border-radius:8px; padding:12px 14px; margin-bottom:16px; font-size:13px;
                                    color:var(--blue-800,#1e40af)">
                            <i class="bi bi-info-circle-fill" style="margin-right:6px"></i>
                            This payment will be submitted for <strong>Finance/Manager verification</strong>
                            before the journal is posted. The invoice status will not change until verified.
                        </div>

                        <button class="btn btn-primary" style="width:100%"
                                onclick="return confirm('Submit this payment for Finance verification?')">
                            <i class="bi bi-send"></i> Submit for Verification
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @endif

    </div>

    {{-- ── RIGHT column — Payment history ─────────────────────────────── --}}
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-list-check" style="color:var(--navy-500)"></i> Payment History
                </h3>
            </div>
            <div class="card-body p-0">
                @forelse($invoice->payments->sortByDesc('created_at') as $payment)
                <div style="padding:16px 20px; border-bottom:1px solid var(--gray-100);
                            background:{{ $payment->isPending() ? 'var(--warning-50,#fffbeb)' : ($payment->isRejected() ? 'var(--red-50,#fef2f2)' : '') }}">

                    {{-- Payment header row --}}
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px">
                        <span class="font-mono fw-semibold" style="color:var(--navy-700)">
                            {{ $payment->payment_number }}
                        </span>
                        <span class="badge {{ $payment->statusBadgeClass() }}">
                            {{ $payment->status }}
                        </span>
                        <span class="text-muted" style="font-size:12px; margin-left:auto">
                            {{ $payment->payment_date->format('d M Y') }}
                        </span>
                    </div>

                    {{-- Payment detail --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px;
                                font-size:13px; margin-bottom:10px">
                        <div>
                            <div class="text-muted" style="font-size:11px; font-weight:600; text-transform:uppercase">Amount</div>
                            <div class="fw-bold" style="color:var(--navy-900)">Rp {{ number_format($payment->amount,0,',','.') }}</div>
                        </div>
                        <div>
                            <div class="text-muted" style="font-size:11px; font-weight:600; text-transform:uppercase">Method</div>
                            <div>{{ $payment->payment_method }}</div>
                        </div>
                        <div>
                            <div class="text-muted" style="font-size:11px; font-weight:600; text-transform:uppercase">Reference</div>
                            <div>{{ $payment->reference ?? '—' }}</div>
                        </div>
                    </div>

                    @if($payment->notes)
                    <div style="font-size:12px; color:var(--gray-600); margin-bottom:8px">
                        <i class="bi bi-chat-left-text"></i> {{ $payment->notes }}
                    </div>
                    @endif

                    {{-- Verified info --}}
                    @if($payment->isVerified())
                    <div style="font-size:12px; color:var(--green-700,#15803d); background:var(--green-50,#f0fdf4);
                                border-radius:6px; padding:6px 10px">
                        <i class="bi bi-check-circle-fill"></i>
                        Verified by <strong>{{ $payment->verifiedByUser?->name ?? '—' }}</strong>
                        on {{ $payment->verified_at?->format('d M Y H:i') }}
                    </div>
                    @endif

                    {{-- Rejected info --}}
                    @if($payment->isRejected())
                    <div style="font-size:12px; color:var(--red-700,#b91c1c); background:var(--red-50,#fef2f2);
                                border-radius:6px; padding:6px 10px">
                        <i class="bi bi-x-circle-fill"></i>
                        Rejected by <strong>{{ $payment->verifiedByUser?->name ?? '—' }}</strong>
                        on {{ $payment->verified_at?->format('d M Y H:i') }}
                        @if($payment->rejection_reason)
                            &mdash; <em>{{ $payment->rejection_reason }}</em>
                        @endif
                    </div>
                    @endif

                    {{-- Verify / Reject actions — Finance & Manager only, pending payments only --}}
                    @if($payment->isPending() && auth()->user()->hasRole('finance','manager'))
                    <div style="display:flex; gap:10px; margin-top:12px; padding-top:12px;
                                border-top:1px dashed var(--warning-300,#fcd34d)">

                        {{-- Verify form --}}
                        <form method="POST"
                              action="{{ route('accounts-receivable.payments.verify', $payment) }}"
                              style="flex:1"
                              onsubmit="return confirm('Confirm this payment is verified and post the journal entry?')">
                            @csrf
                            <input type="hidden" name="notes" value="Verified by Finance">
                            <button type="submit" class="btn btn-success" style="width:100%">
                                <i class="bi bi-check-circle"></i> Verify & Post
                            </button>
                        </form>

                        {{-- Reject form --}}
                        <form method="POST"
                              action="{{ route('accounts-receivable.payments.reject', $payment) }}"
                              style="flex:1"
                              id="reject-form-{{ $payment->id }}">
                            @csrf
                            <input type="text" name="rejection_reason"
                                   id="reject-reason-{{ $payment->id }}"
                                   class="form-input" style="margin-bottom:6px"
                                   placeholder="Rejection reason (required)" required>
                            <button type="submit" class="btn btn-danger" style="width:100%"
                                    onclick="return document.getElementById('reject-reason-{{ $payment->id }}').value.trim() !== '' || (alert('Please enter a rejection reason.'), false)">
                                <i class="bi bi-x-circle"></i> Reject
                            </button>
                        </form>

                    </div>
                    @endif

                </div>
                @empty
                <div class="empty-state" style="padding:40px">
                    <i class="bi bi-cash" style="font-size:2rem; display:block; margin-bottom:8px; opacity:.4"></i>
                    <p>No payments recorded yet</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
