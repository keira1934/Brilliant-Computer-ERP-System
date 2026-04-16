@extends('layouts.app')
@php $title = 'Journal Entries'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Journal Entries</h1><p class="page-sub">All double-entry accounting records</p></div>
    <a href="{{ request()->fullUrlWithQuery(['print'=>1]) }}" target="_blank" class="btn btn-outline"><i class="bi bi-printer"></i> Print All</a>
</div>

<div class="card mb-4">
    <div class="card-body" style="padding-bottom:0">
        <form class="filter-bar" method="GET">
            <input name="from" type="date" value="{{ $from }}" class="form-control" style="width:160px">
            <input name="to"   type="date" value="{{ $to }}"   class="form-control" style="width:160px">
            <button class="btn btn-secondary"><i class="bi bi-funnel"></i> View</button>
            <a href="{{ route('ledger.journal') }}" class="btn btn-outline">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
    @forelse($isPrint ? $entries : $entries as $entry)
    <div style="border:1px solid var(--gray-200);border-radius:10px;margin:12px 16px;overflow:hidden">
        <div style="background:var(--navy-50);padding:10px 16px;display:flex;justify-content:space-between;align-items:center">
            <div>
                <span class="font-mono fw-bold" style="color:var(--navy-800)">{{ $entry->reference_type }}-{{ $entry->reference_id }}</span>
                <span style="margin-left:12px;color:var(--gray-600);font-size:13px">{{ $entry->description }}</span>
            </div>
            <span class="td-muted font-mono">{{ $entry->entry_date }}</span>
        </div>
        <table style="width:100%">
            <tbody>
            @foreach($entry->lines as $line)
            <tr>
                <td style="padding:7px 16px;font-size:12px;color:var(--gray-500);width:110px" class="font-mono">{{ $line->account->code }}</td>
                <td style="padding:7px 8px;font-size:13px">{{ $line->account->name }}</td>
                <td style="padding:7px 8px;font-size:12px;color:var(--gray-500)">{{ $line->description }}</td>
                <td style="padding:7px 16px;text-align:right;font-weight:600;width:140px;color:{{ $line->debit > 0 ? 'var(--navy-700)' : 'transparent' }}">
                    {{ $line->debit > 0 ? 'Rp '.number_format($line->debit,0,',','.') : '' }}
                </td>
                <td style="padding:7px 16px;text-align:right;font-weight:600;width:140px;color:{{ $line->credit > 0 ? 'var(--success)' : 'transparent' }}">
                    {{ $line->credit > 0 ? 'Rp '.number_format($line->credit,0,',','.') : '' }}
                </td>
            </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr style="background:var(--gray-50)">
                    <td colspan="3" class="text-right td-muted" style="padding:6px 16px;font-size:11.5px;font-weight:600">TOTAL</td>
                    <td style="padding:6px 16px;text-align:right;font-weight:700;color:var(--navy-700)">Rp {{ number_format($entry->lines->sum('debit'),0,',','.') }}</td>
                    <td style="padding:6px 16px;text-align:right;font-weight:700;color:var(--success)">Rp {{ number_format($entry->lines->sum('credit'),0,',','.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @empty
    <div class="empty-state"><i class="bi bi-journal-text"></i><p>No journal entries found for this period</p></div>
    @endforelse
    </div>
    @if(!$isPrint && $entries->hasPages())<div class="card-footer">{{ $entries->links('vendor.pagination.simple') }}</div>@endif
</div>
@endsection
