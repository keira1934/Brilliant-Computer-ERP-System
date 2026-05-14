@extends('layouts.app')
@section('content')
@php $title = 'Audit Trail'; @endphp

<div class="page-header">
    <div>
        <h2><i class="bi bi-clock-history"></i> Audit Trail</h2>
        <p class="text-muted">Immutable log of all system activities. Records cannot be modified or deleted.</p>
    </div>
</div>

<div class="card" style="margin-bottom:20px">
    <div class="card-body">
        <form method="GET" class="filter-row">
            <div class="filter-group">
                <label class="form-label">Module</label>
                <select name="module" class="form-input">
                    <option value="">All Modules</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}" {{ request('module') === $mod ? 'selected' : '' }}>{{ ucfirst($mod) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label class="form-label">Action</label>
                <select name="action" class="form-input">
                    <option value="">All Actions</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ ucfirst($act) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label class="form-label">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="form-input">
            </div>
            <div class="filter-group">
                <label class="form-label">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="form-input">
            </div>
            <div class="filter-group" style="align-self:flex-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:150px">Timestamp</th>
                    <th style="width:130px">User</th>
                    <th style="width:100px">Module</th>
                    <th style="width:80px">Action</th>
                    <th>Description</th>
                    <th style="width:110px">IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="font-mono" style="font-size:12px">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $log->user->name ?? 'System' }}</td>
                    <td><span class="badge badge-outline">{{ ucfirst($log->module) }}</span></td>
                    <td><span class="badge badge-action-{{ $log->action }}">{{ ucfirst($log->action) }}</span></td>
                    <td style="font-size:13px">{{ $log->description ?? '—' }}</td>
                    <td class="font-mono" style="font-size:12px">{{ $log->ip_address ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">No audit logs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="card-footer">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
