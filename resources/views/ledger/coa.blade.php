@extends('layouts.app')
@php $title = 'Chart of Accounts'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Chart of Accounts (COA)</h1><p class="page-sub">Double-entry accounting account structure</p></div>
    <a href="{{ request()->fullUrlWithQuery(['print'=>1]) }}" target="_blank" class="btn btn-outline"><i class="bi bi-printer"></i> Print</a>
</div>

@php
$typeLabels = ['asset'=>'Assets','liability'=>'Liabilities','equity'=>'Equity','revenue'=>'Revenue','expense'=>'Expenses'];
$typeColors = ['asset'=>'badge-navy','liability'=>'badge-warning','equity'=>'badge-gray','revenue'=>'badge-success','expense'=>'badge-danger'];
@endphp

@foreach($typeLabels as $type => $label)
@if(isset($accounts[$type]) && $accounts[$type]->count())
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title">{{ $label }}</h5>
        <span class="badge {{ $typeColors[$type] ?? 'badge-gray' }}">{{ $accounts[$type]->count() }} accounts</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Code</th><th>Account Name</th><th>Normal Balance</th><th>Description</th></tr></thead>
            <tbody>
            @foreach($accounts[$type] as $account)
            <tr>
                <td class="font-mono td-primary">{{ $account->code }}</td>
                <td class="fw-semibold">{{ $account->name }}</td>
                <td><span class="badge {{ $account->normal_balance === 'debit' ? 'badge-warning' : 'badge-success' }}">{{ ucfirst($account->normal_balance) }}</span></td>
                <td class="td-muted">{{ $account->description ?? '-' }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endforeach
@endsection
