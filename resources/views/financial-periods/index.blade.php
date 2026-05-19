@extends('layouts.app')
@section('content')
@php $title = 'Financial Periods'; @endphp

<div class="page-header">
    <div>
        <h2><i class="bi bi-calendar-range"></i> Financial Periods</h2>
        <p class="text-muted">Manage accounting periods. Closed periods prevent new transactions.</p>
    </div>
</div>

<div class="content-grid" style="display:grid; grid-template-columns:380px 1fr; gap:24px; align-items:start;">
    {{-- Create Form --}}
    <div class="card">
        <div class="card-header"><h3>Create New Period</h3></div>
        <div class="card-body">
            <form method="POST" action="{{ route('financial-periods.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Period Name</label>
                    <input type="text" name="name" class="form-input" placeholder="e.g. January 2025" required value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-input" required value="{{ old('start_date') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-input" required value="{{ old('end_date') }}">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%">
                    <i class="bi bi-plus-circle"></i> Create Period
                </button>
            </form>
        </div>
    </div>

    {{-- Periods List --}}
    <div class="card">
        <div class="card-header"><h3>All Periods</h3></div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Period Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Closed By</th>
                        <th style="width:140px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periods as $period)
                    <tr>
                        <td><a href="{{ route('financial-periods.show', $period) }}" class="td-primary">{{ $period->name }}</a></td>
                        <td>{{ $period->start_date->format('d M Y') }}</td>
                        <td>{{ $period->end_date->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('financial-periods.show', $period) }}" class="btn btn-sm btn-outline" title="View Detail"><i class="bi bi-eye"></i></a>
                            @if($period->isOpen())
                                <span class="badge status-completed">Open</span>
                            @else
                                <span class="badge status-cancelled">Closed</span>
                            @endif
                        </td>
                        <td>
                            @if($period->closed_at)
                                {{ $period->closedByUser->name ?? '—' }}<br>
                                <small class="text-muted">{{ $period->closed_at->format('d M Y H:i') }}</small>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($period->isOpen())
                                <form method="POST" action="{{ route('financial-periods.close', $period) }}" style="display:inline"
                                      onsubmit="return confirm('Close this period? No more transactions can be posted to dates within this period.')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-lock"></i> Close</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('financial-periods.reopen', $period) }}" style="display:inline"
                                      onsubmit="return confirm('Reopen this period? Transactions will be allowed again.')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-secondary"><i class="bi bi-unlock"></i> Reopen</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">No financial periods defined yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($periods->hasPages())
        <div class="card-footer">{{ $periods->links() }}</div>
        @endif
    </div>
</div>
@endsection
