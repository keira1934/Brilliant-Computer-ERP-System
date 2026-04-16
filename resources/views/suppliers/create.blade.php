@extends('layouts.app')
@php $title = 'Add Supplier'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Add Supplier</h1></div>
    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card" style="max-width:640px">
    <div class="card-body">
        <form method="POST" action="{{ route('suppliers.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Supplier Name <span class="required">*</span></label>
                <input name="name" value="{{ old('name') }}" class="form-control" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Contact Person</label>
                    <input name="contact_person" value="{{ old('contact_person') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input name="phone" value="{{ old('phone') }}" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input name="email" type="email" value="{{ old('email') }}" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
            </div>
            <div class="flex gap-3 mt-4">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Supplier</button>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
