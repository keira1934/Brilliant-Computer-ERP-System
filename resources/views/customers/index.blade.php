@extends('layouts.app')
@php $title = 'Customer List'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Customers</h1><p class="page-sub">Manage customer data</p></div>
    <a href="{{ route('customers.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Customer</a>
</div>
<div class="card">
    <div class="card-body" style="padding-bottom:0">
        <form class="filter-bar" method="GET">
            <div class="search-wrap"><i class="bi bi-search"></i>
                <input name="search" value="{{ request('search') }}" placeholder="Search name / phone / email..." class="form-control" style="width:280px">
            </div>
            <button class="btn btn-secondary"><i class="bi bi-funnel"></i> Search</button>
            @if(request('search'))<a href="{{ route('customers.index') }}" class="btn btn-outline">Clear</a>@endif
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Name</th><th>Phone</th><th>Email</th><th>Address</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($customers as $c)
            <tr>
                <td class="td-muted">{{ $customers->firstItem() + $loop->index }}</td>
                <td class="td-primary">{{ $c->name }}</td>
                <td>{{ $c->phone ?? '-' }}</td>
                <td class="td-muted">{{ $c->email ?? '-' }}</td>
                <td class="td-muted">{{ Str::limit($c->address, 40) ?? '-' }}</td>
                <td>
                    <div class="flex gap-2">
                        <a href="{{ route('customers.edit', $c) }}" class="btn btn-sm btn-secondary"><i class="bi bi-pencil"></i></a>
                        <button onclick="deleteRecord('{{ route('customers.destroy', $c) }}', 'Delete customer {{ addslashes($c->name) }}?')" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6"><div class="empty-state"><i class="bi bi-people"></i><p>No customers found</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())<div class="card-footer">{{ $customers->links('vendor.pagination.simple') }}</div>@endif
</div>
@endsection
