@extends('layouts.app')
@php $title = 'Edit Supplier'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Edit Supplier</h1></div>
    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card" style="max-width:640px">
    <div class="card-body">
        <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Supplier Name <span class="required">*</span></label>
                <input name="name" value="{{ old('name', $supplier->name) }}" class="form-control" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Contact Person</label>
                    <input name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input name="phone" value="{{ old('phone', $supplier->phone) }}" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input name="email" type="email" value="{{ old('email', $supplier->email) }}" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address', $supplier->address) }}</textarea>
            </div>
            <div class="flex gap-3 mt-4">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Update Supplier</button>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
