@extends('layouts.app')
@php $title = 'Adjusting Entries'; @endphp
@section('content')
<div class="page-header">
    <div>
        <h1>Adjusting Entries</h1>
        <p class="page-sub">Accruals, depreciation, prepaid expense, and valuation adjustments</p>
    </div>
    <a href="{{ route('adjusting-entries.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Adjustment</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Date</th><th>Type</th><th>Description</th><th>Journal</th><th class="text-right">Amount</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($entries as $entry)
            <tr>
                <td class="td-muted">{{ $entry->adjustment_date->format('d/m/Y') }}</td>
                <td><span class="badge badge-navy">{{ ucfirst($entry->adjustment_type) }}</span></td>
                <td class="td-primary">{{ $entry->description }}</td>
                <td class="font-mono">{{ $entry->journalEntry?->journal_number ?? '-' }}</td>
                <td class="text-right fw-bold">Rp {{ number_format($entry->amount,0,',','.') }}</td>
                <td><span class="badge badge-success">{{ $entry->status }}</span></td>
            </tr>
            @empty
            <tr><td colspan="6"><div class="empty-state"><i class="bi bi-journal-plus"></i><p>No adjusting entries posted yet</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($entries->hasPages())<div class="card-footer">{{ $entries->links('vendor.pagination.simple') }}</div>@endif
</div>
@endsection
