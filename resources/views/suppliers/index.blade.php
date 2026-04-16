@extends('layouts.app')
@php $title = 'Suppliers'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Suppliers</h1><p class="page-sub">Manage supplier data</p></div>
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Supplier</a>
</div>
<div class="card">
    <div class="card-body" style="padding-bottom:0">
        <form class="filter-bar" method="GET">
            <div class="search-wrap"><i class="bi bi-search"></i>
                <input name="search" value="{{ request('search') }}" placeholder="Search supplier name..." class="form-control" style="width:280px">
            </div>
            <button class="btn btn-secondary"><i class="bi bi-funnel"></i> Search</button>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Supplier Name</th><th>Contact Person</th><th>Phone</th><th>Email</th><th>Address</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($suppliers as $s)
            <tr>
                <td class="td-muted">{{ $suppliers->firstItem() + $loop->index }}</td>
                <td class="td-primary">{{ $s->name }}</td>
                <td>{{ $s->contact_person ?? '-' }}</td>
                <td>{{ $s->phone ?? '-' }}</td>
                <td class="td-muted">{{ $s->email ?? '-' }}</td>
                <td class="td-muted">{{ Str::limit($s->address,40) ?? '-' }}</td>
                <td>
                    <div class="flex gap-2">
                        <a href="{{ route('suppliers.edit', $s) }}" class="btn btn-sm btn-secondary"><i class="bi bi-pencil"></i></a>
                        <button onclick="deleteRecord('{{ route('suppliers.destroy', $s) }}', 'Delete supplier &quot;{{ addslashes($s->name) }}&quot;?')" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7"><div class="empty-state"><i class="bi bi-building"></i><p>No suppliers found</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($suppliers->hasPages())<div class="card-footer">{{ $suppliers->links('vendor.pagination.simple') }}</div>@endif
</div>
@endsection
