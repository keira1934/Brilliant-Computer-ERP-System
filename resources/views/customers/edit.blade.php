@extends('layouts.app')
@php $title = 'Edit Customer'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Edit Customer</h1></div>
    <a href="{{ route('customers.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card" style="max-width:640px">
    <div class="card-body">
        <form method="POST" action="{{ route('customers.update', $customer) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Full Name <span class="required">*</span></label>
                <input name="name" value="{{ old('name', $customer->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Phone Number <span class="required">*</span></label>
                    <input name="phone" value="{{ old('phone', $customer->phone) }}" class="form-control @error('phone') is-invalid @enderror" required>
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email <span class="required">*</span></label>
                    <input name="email" type="email" value="{{ old('email', $customer->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Address <span class="required">*</span></label>
                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" required>{{ old('address', $customer->address) }}</textarea>
                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $customer->notes) }}</textarea>
            </div>
            <div class="flex gap-3 mt-4">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Update</button>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
