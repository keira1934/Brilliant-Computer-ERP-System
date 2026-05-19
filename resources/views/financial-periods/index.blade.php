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

    {{-- ── Create Form ─────────────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-header"><h3>Create New Period</h3></div>
        <div class="card-body">
            <form method="POST" action="{{ route('financial-periods.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Period Name</label>
                    <input type="text" name="name" class="form-input"
                           placeholder="e.g. January 2025" required value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-input"
                           required value="{{ old('start_date') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-input"
                           required value="{{ old('end_date') }}">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%">
                    <i class="bi bi-plus-circle"></i> Create Period
                </button>
            </form>
        </div>
    </div>

    {{-- ── Year-folder accordion ───────────────────────────────────────── --}}
    <div>
        @forelse($periodsByYear as $year => $periods)
        @php
            $yearOpen   = $periods->contains(fn($p) => $p->isOpen());
            $yearClosed = $periods->every(fn($p) => $p->isClosed());
            $openCount  = $periods->where('status','open')->count();
            $totalCount = $periods->count();
            // Auto-expand the most recent year
            $expanded   = $loop->first;
        @endphp

        <div class="card mb-3" style="overflow:visible">
            {{-- Year header (clickable) --}}
            <div class="card-header year-folder-header"
                 style="cursor:pointer; user-select:none; display:flex; align-items:center; gap:12px;"
                 onclick="toggleYear('year-{{ $year }}', this)">

                <i class="bi bi-folder{{ $expanded ? '2-open' : '2' }} folder-icon"
                   style="font-size:1.3rem; color:{{ $yearClosed ? 'var(--text-muted,#888)' : 'var(--primary,#4f46e5)' }}"></i>

                <span style="font-weight:600; font-size:1.05rem;">{{ $year }}</span>

                <span class="badge {{ $yearClosed ? 'status-cancelled' : 'status-completed' }}"
                      style="margin-left:4px; font-size:.75rem;">
                    {{ $yearClosed ? 'All Closed' : "$openCount Open" }}
                </span>

                <span class="text-muted" style="font-size:.85rem; margin-left:auto;">
                    {{ $totalCount }} {{ Str::plural('period', $totalCount) }}
                </span>

                <i class="bi bi-chevron-{{ $expanded ? 'up' : 'down' }} chevron-icon"
                   style="font-size:.9rem; color:var(--text-muted,#888)"></i>
            </div>

            {{-- Month rows (collapsible) --}}
            <div id="year-{{ $year }}" style="{{ $expanded ? '' : 'display:none' }}">
                <table class="data-table" style="margin:0">
                    <thead>
                        <tr>
                            <th>Period Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Closed By</th>
                            <th style="width:160px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($periods->sortByDesc('start_date') as $period)
                        <tr>
                            <td>
                                <a href="{{ route('financial-periods.show', $period) }}" class="td-primary">
                                    <i class="bi bi-calendar3" style="margin-right:6px; opacity:.6"></i>
                                    {{ $period->name }}
                                </a>
                            </td>
                            <td>{{ $period->start_date->format('d M Y') }}</td>
                            <td>{{ $period->end_date->format('d M Y') }}</td>
                            <td>
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
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('financial-periods.show', $period) }}"
                                   class="btn btn-sm btn-outline" title="View Detail">
                                    <i class="bi bi-eye"></i>
                                </a>

                                @if($period->isOpen())
                                    <form method="POST" action="{{ route('financial-periods.close', $period) }}"
                                          style="display:inline"
                                          onsubmit="return confirm('Close this period? No more transactions can be posted to dates within this period.')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-lock"></i> Close
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('financial-periods.reopen', $period) }}"
                                          style="display:inline"
                                          onsubmit="return confirm('Reopen this period? Transactions will be allowed again.')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-secondary">
                                            <i class="bi bi-unlock"></i> Reopen
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @empty
        <div class="card">
            <div class="card-body text-center text-muted" style="padding:40px">
                <i class="bi bi-calendar-x" style="font-size:2rem; display:block; margin-bottom:8px"></i>
                No financial periods defined yet. Create one using the form on the left.
            </div>
        </div>
        @endforelse
    </div>

</div>

<script>
function toggleYear(id, header) {
    const body    = document.getElementById(id);
    const chevron = header.querySelector('.chevron-icon');
    const folder  = header.querySelector('.folder-icon');
    const isOpen  = body.style.display !== 'none';

    body.style.display = isOpen ? 'none' : '';

    // swap chevron
    chevron.classList.toggle('bi-chevron-down', isOpen);
    chevron.classList.toggle('bi-chevron-up',   !isOpen);

    // swap folder icon
    folder.classList.toggle('bi-folder2',      isOpen);
    folder.classList.toggle('bi-folder2-open', !isOpen);
}
</script>
@endsection
