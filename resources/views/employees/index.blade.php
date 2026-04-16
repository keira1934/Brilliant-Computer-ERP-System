@extends('layouts.app')
@php $title = 'Employees'; @endphp
@section('content')
<div class="page-header">
    <div><h1>Employees</h1><p class="page-sub">Manage employee data and salaries</p></div>
    <a href="{{ route('employees.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Employee</a>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Code</th><th>Name</th><th>Position</th><th>Contact</th><th>Salary Type</th><th class="text-right">Base Salary</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($employees as $emp)
            <tr>
                <td class="font-mono td-muted">{{ $emp->employee_code }}</td>
                <td class="td-primary">{{ $emp->name }}</td>
                <td>{{ $emp->position }}</td>
                <td class="td-muted">{{ $emp->phone ?? $emp->email ?? '-' }}</td>
                <td><span class="badge badge-navy">{{ ucfirst($emp->salary_type) }}</span></td>
                <td class="text-right fw-semibold">Rp {{ number_format($emp->base_salary,0,',','.') }}</td>
                <td>
                    @if($emp->is_active)
                    <span class="badge badge-success">Active</span>
                    @else
                    <span class="badge badge-gray">Inactive</span>
                    @endif
                </td>
                <td>
                    <div class="flex gap-2">
                        <a href="{{ route('employees.edit', $emp) }}" class="btn btn-sm btn-secondary"><i class="bi bi-pencil"></i></a>
                        <button onclick="deleteRecord('{{ route('employees.destroy', $emp) }}', 'Delete employee {{ addslashes($emp->name) }}?')" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8"><div class="empty-state"><i class="bi bi-person-badge"></i><p>No employees found</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($employees->hasPages())<div class="card-footer">{{ $employees->links('vendor.pagination.simple') }}</div>@endif
</div>
@endsection
