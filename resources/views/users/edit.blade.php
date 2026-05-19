@extends('layouts.app')
@section('content')
@php $title = 'Edit User'; @endphp

<div class="page-header">
    <h2><i class="bi bi-person-gear"></i> Edit User: {{ $user->name }}</h2>
</div>

<div class="card" style="max-width:600px">
    <div class="card-body">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-input" required value="{{ old('name', $user->name) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-input" required value="{{ old('email', $user->email) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Role</label>
                <select name="role" class="form-input" required>
                    @foreach(['admin'=>'Administrator','finance'=>'Finance','cashier'=>'Cashier','inventory'=>'Inventory Staff','hr'=>'Human Resources','manager'=>'Manager'] as $val => $label)
                        <option value="{{ $val }}" {{ old('role', $user->role) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                <input type="password" name="password" class="form-input" minlength="8">
            </div>
            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label" style="display:flex;align-items:center;gap:8px">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                    Account is active
                </label>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Update User</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
