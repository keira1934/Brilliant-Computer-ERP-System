@extends('layouts.app')
@php $title = 'Edit Employee'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Edit Employee</h1></div>
    <a href="{{ route('employees.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card" style="max-width:720px">
    <div class="card-body">
        <form method="POST" action="{{ route('employees.update', $employee) }}">
            @csrf @method('PUT')
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Employee Code <span class="required">*</span></label>
                    <input name="employee_code" value="{{ $employee->employee_code }}" class="form-control @error('employee_code') is-invalid @enderror" readonly required>
                    @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Full Name <span class="required">*</span></label>
                    <input name="name" value="{{ old('name', $employee->name) }}" class="form-control" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Position <span class="required">*</span></label>
                    <input name="position" value="{{ old('position', $employee->position) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Join Date</label>
                    <input name="join_date" type="date" value="{{ old('join_date', $employee->join_date?->format('Y-m-d')) }}" class="form-control" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input name="phone" value="{{ old('phone', $employee->phone) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input name="email" type="email" value="{{ old('email', $employee->email) }}" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address', $employee->address) }}</textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Salary Type</label>
                    <select name="salary_type" class="form-control">
                        <option value="monthly" {{ old('salary_type',$employee->salary_type)==='monthly'?'selected':'' }}>Monthly</option>
                        <option value="daily"   {{ old('salary_type',$employee->salary_type)==='daily'?'selected':'' }}>Daily</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Base Salary (Rp)</label>
                    <input name="base_salary" type="number" min="0" value="{{ old('base_salary', $employee->base_salary) }}" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $employee->is_active) ? 'checked' : '' }} style="width:18px;height:18px">
                    <span>Active Employee</span>
                </label>
            </div>
            <div class="flex gap-3 mt-4">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Update Employee</button>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
