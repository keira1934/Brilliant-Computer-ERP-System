@extends('layouts.app')
@php $title = 'Add Account'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Add Chart of Account</h1><p class="page-sub">Create a controlled GL account for journal posting and reports</p></div>
    <a href="{{ route('ledger.coa') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="card" style="max-width:760px">
    <div class="card-body">
        <form method="POST" action="{{ route('ledger.coa.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Account Code <span class="required">*</span></label>
                    <input name="code" value="{{ old('code') }}" class="form-control" required placeholder="e.g. 1-4000">
                </div>
                <div class="form-group">
                    <label class="form-label">Account Name <span class="required">*</span></label>
                    <input name="name" value="{{ old('name') }}" class="form-control" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Type <span class="required">*</span></label>
                    <select name="type" class="form-control" required>
                        @foreach(['asset'=>'Assets','liability'=>'Liabilities','equity'=>'Equity','revenue'=>'Revenue','expense'=>'Expenses'] as $value => $label)
                        <option value="{{ $value }}" {{ old('type')===$value?'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Normal Balance <span class="required">*</span></label>
                    <select name="normal_balance" class="form-control" required>
                        <option value="debit" {{ old('normal_balance')==='debit'?'selected':'' }}>Debit</option>
                        <option value="credit" {{ old('normal_balance')==='credit'?'selected':'' }}>Credit</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>
            <div class="flex gap-3 mt-4">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Account</button>
                <a href="{{ route('ledger.coa') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
