@extends('layouts.app')
@php $title = 'New Service Order'; @endphp
@section('content')
<div class="page-header">
    <div><h1>New Service Order</h1></div>
    <a href="{{ route('service-orders.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card" style="max-width:700px">
    <div class="card-body">
        <form method="POST" action="{{ route('service-orders.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Customer <span class="required">*</span></label>
                <select name="customer_id" class="form-control" required>
                    <option value="">-- Select Customer --</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ old('customer_id')==$c->id?'selected':'' }}>{{ $c->name }} — {{ $c->phone }}</option>
                    @endforeach
                </select>
                <div class="form-text">If walk-in customer not in list, <a href="{{ route('customers.create') }}" target="_blank">add them first</a>.</div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Device Type <span class="required">*</span></label>
                    <select name="device_type" class="form-control" required>
                        @foreach(['Laptop','Printer','CPU','All-in-One','Other'] as $d)
                        <option value="{{ $d }}" {{ old('device_type')==$d?'selected':'' }}>{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Brand / Model</label>
                    <input name="brand" value="{{ old('brand') }}" class="form-control" placeholder="e.g. HP, ASUS, Epson">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Serial Number</label>
                <input name="serial_number" value="{{ old('serial_number') }}" class="form-control" placeholder="Optional">
            </div>
            <div class="form-group">
                <label class="form-label">Problem Description <span class="required">*</span></label>
                <textarea name="problem_description" class="form-control" rows="3" required placeholder="Describe the issue reported by the customer...">{{ old('problem_description') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Internal Notes</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Internal notes (not shown to customer)">{{ old('notes') }}</textarea>
            </div>
            <div class="flex gap-3 mt-4">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Create Order</button>
                <a href="{{ route('service-orders.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
