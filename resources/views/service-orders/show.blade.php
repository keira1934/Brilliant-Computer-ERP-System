@extends('layouts.app')
@php $title = 'Service Order #' . $order->order_number; @endphp
@section('content')
<div class="page-header">
    <div>
        <h1>{{ $order->order_number }}</h1>
        <p class="page-sub">Received {{ $order->received_at?->format('d M Y, H:i') }}</p>
    </div>
    <div class="page-header-actions">
        <span class="{{ $order->getStatusBadgeClass() }}" style="font-size:13px;padding:6px 14px">{{ $order->getStatusLabel() }}</span>
        <a href="{{ route('service-orders.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
</div>

<div class="grid-2 mb-5">
    <div class="card">
        <div class="card-header"><h5 class="card-title">Order Information</h5></div>
        <div class="card-body">
            <table style="width:100%;font-size:13.5px">
                <tr><td style="padding:5px 0;color:var(--gray-500);width:140px">Order No.</td><td class="font-mono fw-semibold">{{ $order->order_number }}</td></tr>
                <tr><td style="padding:5px 0;color:var(--gray-500)">Customer</td><td class="fw-semibold">{{ $order->customer?->name ?? 'Walk-in' }}</td></tr>
                <tr><td style="padding:5px 0;color:var(--gray-500)">Phone</td><td>{{ $order->customer?->phone ?? '-' }}</td></tr>
                <tr><td style="padding:5px 0;color:var(--gray-500)">Device</td><td>{{ $order->device_type }} — {{ $order->brand ?? '-' }}</td></tr>
                <tr><td style="padding:5px 0;color:var(--gray-500)">Serial No.</td><td>{{ $order->serial_number ?? '-' }}</td></tr>
                <tr><td style="padding:5px 0;color:var(--gray-500)">Problem</td><td>{{ $order->problem_description }}</td></tr>
                <tr><td style="padding:5px 0;color:var(--gray-500)">Diagnosis</td><td>{{ $order->diagnosis ?? 'Not yet diagnosed' }}</td></tr>
                @if($order->service_cost)
                <tr><td style="padding:5px 0;color:var(--gray-500)">Service Cost</td><td class="fw-bold text-navy" style="font-size:15px">Rp {{ number_format($order->service_cost,0,',','.') }}</td></tr>
                @endif
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h5 class="card-title">Workflow Timeline</h5></div>
        <div class="card-body">
            @php
            $statuses = ['Received','InProgress','Done','Completed'];
            $current  = array_search($order->status, $statuses);
            @endphp
            <div style="position:relative;padding-left:28px">
                @foreach(['Received' => 'Device received & registered',
                          'InProgress' => 'Diagnosis & repair in progress',
                          'Done' => 'Repair completed — awaiting payment',
                          'Completed' => 'Payment received & order closed'] as $step => $label)
                @php $idx = array_search($step, $statuses); @endphp
                <div style="position:relative;padding:10px 0;display:flex;align-items:flex-start;gap:14px">
                    <div style="position:absolute;left:-28px;top:12px;width:18px;height:18px;border-radius:50%;background:{{ $idx <= $current ? 'var(--navy-600)' : 'var(--gray-300)' }};display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-check" style="color:white;font-size:11px"></i>
                    </div>
                    @if(!$loop->last)
                    <div style="position:absolute;left:-20px;top:30px;width:2px;height:calc(100% - 8px);background:{{ $idx < $current ? 'var(--navy-400)' : 'var(--gray-200)' }}"></div>
                    @endif
                    <div>
                        <div style="font-weight:600;font-size:13px;color:{{ $idx <= $current ? 'var(--navy-800)' : 'var(--gray-400)' }}">{{ $step === 'InProgress' ? 'In Progress' : $step }}</div>
                        <div style="font-size:12px;color:var(--gray-500)">{{ $label }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ACTIONS BASED ON STATUS --}}
@if($order->status === 'Received')
<div class="card mb-4" style="border-left:4px solid var(--navy-500)">
    <div class="card-header"><h5 class="card-title"><i class="bi bi-tools" style="margin-right:8px;color:var(--navy-500)"></i>Start Repair</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ route('service-orders.in-progress', $order) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Initial Diagnosis</label>
                <textarea name="diagnosis" class="form-control" rows="2" placeholder="Describe the diagnosis result...">{{ old('diagnosis') }}</textarea>
            </div>
            <button type="submit" onclick="return confirm('Mark this order as In Progress?')" class="btn btn-primary"><i class="bi bi-play-fill"></i> Start Repair</button>
        </form>
    </div>
</div>
@endif

@if($order->status === 'InProgress')
<div class="card mb-4" style="border-left:4px solid var(--warning)">
    <div class="card-header"><h5 class="card-title"><i class="bi bi-check2-circle" style="margin-right:8px;color:var(--warning)"></i>Mark as Done</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ route('service-orders.done', $order) }}" id="mark-done-form">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Final Diagnosis / Work Done <span class="required">*</span></label>
                    <textarea name="diagnosis" class="form-control" rows="2" required placeholder="Describe exactly what was fixed...">{{ old('diagnosis', $order->diagnosis) }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Service Cost (Rp) <span class="required">*</span></label>
                    <input id="service_cost_input" name="service_cost" type="number" min="1" value="{{ old('service_cost') }}" class="form-control" required placeholder="0">
                    <div class="form-text">The total fee charged to the customer</div>
                </div>
            </div>
            <button type="submit" onclick="return confirm('Mark repair as Done? Customer will need to pay before closing.')" class="btn btn-warning"><i class="bi bi-patch-check"></i> Mark as Done</button>
        </form>
    </div>
</div>
@endif

@if($order->status === 'Done')
<div class="card mb-4" style="border-left:4px solid var(--success)">
    <div class="card-header"><h5 class="card-title"><i class="bi bi-cash-stack" style="margin-right:8px;color:var(--success)"></i>Confirm Payment & Complete</h5></div>
    <div class="card-body">
        <p style="margin-bottom:16px;color:var(--gray-600)">
            Confirming payment will mark the order as <strong>Completed</strong> and post journal entry:<br>
            <span class="font-mono" style="font-size:12px;background:var(--navy-50);padding:4px 10px;border-radius:4px;display:inline-block;margin-top:6px">Dr Cash &nbsp;Rp {{ number_format($order->service_cost,0,',','.') }} &nbsp;/&nbsp; Cr Service Revenue &nbsp;Rp {{ number_format($order->service_cost,0,',','.') }}</span>
        </p>
        <button type="button"
            onclick="postAction('{{ route('service-orders.complete', $order) }}', 'Confirm payment of Rp {{ number_format($order->service_cost,0,',','.') }} and complete this order?')"
            class="btn btn-success">
            <i class="bi bi-check-circle-fill"></i> Confirm Payment & Complete
        </button>
    </div>
</div>
@endif

@if($order->status === 'Completed')
<div class="alert alert-success">
    <i class="bi bi-check-circle-fill"></i>
    <span>This order is <strong>Completed</strong>. Payment received on {{ $order->completed_at?->format('d M Y') }}. Journal entry posted automatically.</span>
</div>
@endif
@endsection
