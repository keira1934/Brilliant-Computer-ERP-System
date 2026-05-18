@extends('layouts.app')
@php $title = 'Chart of Accounts'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Chart of Accounts (COA)</h1><p class="page-sub">Double-entry accounting account structure</p></div>
    <div class="page-header-actions">
        <a href="{{ route('ledger.coa.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Account</a>
        <a href="{{ request()->fullUrlWithQuery(['print'=>1]) }}" target="_blank" class="btn btn-outline"><i class="bi bi-printer"></i> Print</a>
    </div>
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
            <thead><tr><th>Code</th><th>Account Name</th><th>Normal Balance</th><th class="text-right">Opening Balance</th><th>OB Date</th><th>Description</th><th>Actions</th></tr></thead>
            <tbody>
            @foreach($accounts[$type] as $account)
            <tr>
                <td class="font-mono td-primary">{{ $account->code }}</td>
                <td class="fw-semibold">{{ $account->name }}</td>
                <td><span class="badge {{ $account->normal_balance === 'debit' ? 'badge-warning' : 'badge-success' }}">{{ ucfirst($account->normal_balance) }}</span></td>
                <td class="text-right font-mono {{ $account->opening_balance != 0 ? 'fw-bold text-navy' : 'td-muted' }}">
                    {{ $account->opening_balance != 0 ? number_format($account->opening_balance, 2) : '—' }}
                </td>
                <td class="td-muted">{{ $account->opening_balance_date ? $account->opening_balance_date->format('d M Y') : '—' }}</td>
                <td class="td-muted">{{ $account->description ?? '-' }}</td>
                <td>
                    <button onclick="openObModal({{ $account->id }}, '{{ addslashes($account->name) }}', '{{ $account->code }}', {{ $account->opening_balance ?? 0 }}, '{{ $account->opening_balance_date?->format('Y-m-d') ?? '' }}')"
                        class="btn btn-sm btn-outline" title="Set Opening Balance">
                        <i class="bi bi-wallet2"></i> OB
                    </button>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endforeach

{{-- Opening Balance Modal --}}
<div id="ob-modal-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:var(--radius);box-shadow:var(--shadow-lg);width:100%;max-width:480px;padding:28px;position:relative">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
            <div>
                <h3 style="font-size:16px;font-weight:800;color:var(--navy-900);margin:0">Set Opening Balance</h3>
                <p id="ob-modal-subtitle" style="font-size:12.5px;color:var(--gray-500);margin:4px 0 0"></p>
            </div>
            <button onclick="closeObModal()" style="background:none;border:none;font-size:20px;color:var(--gray-400);cursor:pointer;line-height:1">&times;</button>
        </div>

        <form id="ob-form" method="POST">
            @csrf
            @method('PATCH')
            <div class="form-group">
                <label class="form-label">Opening Balance (Rp)</label>
                <input type="number" name="opening_balance" id="ob-amount" step="0.01" class="form-control"
                    placeholder="e.g. 5000000" required>
                <div class="form-text">Enter the beginning balance for this account. Use the account's normal balance sign convention (positive = normal side).</div>
            </div>
            <div class="form-group">
                <label class="form-label">Effective Date</label>
                <input type="date" name="opening_balance_date" id="ob-date" class="form-control">
                <div class="form-text">The date this opening balance takes effect. Leave blank to apply to all periods.</div>
            </div>
            <div class="flex gap-3 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Opening Balance</button>
                <button type="button" onclick="closeObModal()" class="btn btn-secondary">Cancel</button>
                <button type="button" onclick="clearOb()" class="btn btn-outline" style="margin-left:auto;color:var(--danger);border-color:var(--danger)">
                    <i class="bi bi-x-circle"></i> Clear
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openObModal(id, name, code, currentOb, currentDate) {
    document.getElementById('ob-modal-overlay').style.display = 'flex';
    document.getElementById('ob-modal-subtitle').textContent = code + ' — ' + name;
    document.getElementById('ob-form').action = '/ledger/coa/' + id + '/opening-balance';
    document.getElementById('ob-amount').value = currentOb != 0 ? currentOb : '';
    document.getElementById('ob-date').value = currentDate || '';
}
function closeObModal() {
    document.getElementById('ob-modal-overlay').style.display = 'none';
}
function clearOb() {
    document.getElementById('ob-amount').value = '0';
    document.getElementById('ob-date').value = '';
    document.getElementById('ob-form').submit();
}
// Close on overlay click
document.getElementById('ob-modal-overlay').addEventListener('click', function(e) {
    if (e.target === this) closeObModal();
});
</script>
@endpush
@endsection
