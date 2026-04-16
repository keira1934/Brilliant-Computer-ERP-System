@extends('layouts.app')
@php $title = 'Add Employee'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Add Employee</h1></div>
    <a href="{{ route('employees.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card" style="max-width:720px">
    <div class="card-body">
        <form method="POST" action="{{ route('employees.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Employee Code <span class="required">*</span></label>
                    <input name="employee_code" id="employee_code" value="{{ old('employee_code', $autoCode) }}" class="form-control @error('employee_code') is-invalid @enderror" required placeholder="{{ $autoCode }}">
                    @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Auto-generated. You may change it to a custom code.</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Full Name <span class="required">*</span></label>
                    <input name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Position / Role <span class="required">*</span></label>
                    <input name="position" id="position_input" value="{{ old('position') }}" class="form-control" required placeholder="e.g. Technician, Admin">
                </div>
                <div class="form-group">
                    <label class="form-label">Join Date <span class="required">*</span></label>
                    <input name="join_date" type="date" value="{{ old('join_date', date('Y-m-d')) }}" class="form-control" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input name="phone" value="{{ old('phone') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input name="email" type="email" value="{{ old('email') }}" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Salary Type <span class="required">*</span></label>
                    <select name="salary_type" class="form-control" required>
                        <option value="monthly" {{ old('salary_type','monthly')==='monthly'?'selected':'' }}>Monthly</option>
                        <option value="daily"   {{ old('salary_type')==='daily'?'selected':'' }}>Daily</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Base Salary (Rp) <span class="required">*</span></label>
                    <input name="base_salary" type="number" min="0" value="{{ old('base_salary', 3000000) }}" class="form-control" required>
                </div>
            </div>
            <div class="flex gap-3 mt-4">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Employee</button>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
