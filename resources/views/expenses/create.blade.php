@extends('layouts.app')
@php $title = 'Add Expense'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Record Expense</h1></div>
    <a href="{{ route('expenses.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card" style="max-width:640px">
    <div class="card-body">
        <form method="POST" action="{{ route('expenses.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Expense Date <span class="required">*</span></label>
                    <input name="expense_date" type="date" value="{{ old('expense_date', date('Y-m-d')) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Category <span class="required">*</span></label>
                    <select name="category" class="form-control" required>
                        @foreach(['Electricity','Maintenance','Internet','Rent','Other'] as $c)
                        <option value="{{ $c }}" {{ old('category')==$c?'selected':'' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <input name="description" value="{{ old('description') }}" class="form-control" placeholder="Brief description of this expense">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Amount (Rp) <span class="required">*</span></label>
                    <input name="amount" type="number" min="1" value="{{ old('amount') }}" class="form-control" required placeholder="0">
                </div>
                <div class="form-group">
                    <label class="form-label">GL Account (Expense)</label>
                    <select name="account_id" class="form-control">
                        <option value="">-- Auto-select by category --</option>
                        @foreach($expenseAccounts as $acc)
                        <option value="{{ $acc->id }}" {{ old('account_id')==$acc->id?'selected':'' }}>{{ $acc->code }} — {{ $acc->name }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Leave blank to use the default Operational Expense account</div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Reference / Receipt No.</label>
                <input name="reference" value="{{ old('reference') }}" class="form-control" placeholder="e.g. INV-2024-123">
            </div>
            <div class="alert" style="background:var(--navy-50);border:1px solid var(--navy-200);border-radius:8px;display:flex;gap:10px;align-items:flex-start;font-size:13px">
                <i class="bi bi-info-circle" style="color:var(--navy-500);margin-top:2px"></i>
                <span>Saving this expense will automatically post: <strong>Dr Expense Account / Cr Cash</strong> in the General Ledger.</span>
            </div>
            <div class="flex gap-3 mt-4">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Expense</button>
                <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
