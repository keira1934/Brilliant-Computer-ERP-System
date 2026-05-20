@extends('layouts.app')
@php $title = 'New Adjusting Entry'; @endphp
@section('content')
<div class="page-header">
    <div><h1>New Adjusting Entry</h1></div>
    <a href="{{ route('adjusting-entries.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<form method="POST" action="{{ route('adjusting-entries.store') }}">
@csrf
<div class="grid-2" style="align-items:start">
    <div class="card">
        <div class="card-header"><h5 class="card-title">Adjustment Header</h5></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input name="adjustment_date" type="date" value="{{ old('adjustment_date', date('Y-m-d')) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Type</label>
                    <select name="adjustment_type" class="form-control" required>
                        @foreach(['depreciation','accrual','prepaid','inventory','other'] as $type)
                        <option value="{{ $type }}" {{ old('adjustment_type')===$type?'selected':'' }}>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <input name="description" value="{{ old('description') }}" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Amount</label>
                <input name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount') }}" class="form-control" required>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="card-title">Double-Entry Lines</h5></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Debit Account</label>
                <select name="debit_account_id" class="form-control" required>
                    <option value="">-- Select debit account --</option>
                    @foreach($accounts as $account)
                    <option value="{{ $account->id }}" {{ old('debit_account_id')==$account->id?'selected':'' }}>{{ $account->code }} - {{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Credit Account</label>
                <select name="credit_account_id" class="form-control" required>
                    <option value="">-- Select credit account --</option>
                    @foreach($accounts as $account)
                    <option value="{{ $account->id }}" {{ old('credit_account_id')==$account->id?'selected':'' }}>{{ $account->code }} - {{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-primary w-100" onclick="return confirm('Post this adjusting journal entry?')"><i class="bi bi-check-lg"></i> Post Adjustment</button>
        </div>
    </div>
</div>
</form>
@endsection
