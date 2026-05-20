@extends('layouts.app')
@section('content')
@php $title = 'User Management'; @endphp

<div class="page-header">
    <div>
        <h2><i class="bi bi-people-fill"></i> User Management</h2>
        <p class="text-muted">Manage system users and access roles.</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="bi bi-person-plus"></i> Add User</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th style="width:140px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td class="font-mono" style="font-size:13px">{{ $user->email }}</td>
                    <td><span class="badge badge-role-{{ $user->role }}">{{ $user->getRoleLabel() }}</span></td>
                    <td>
                        @if($user->is_active)
                            <span class="badge status-completed">Active</span>
                        @else
                            <span class="badge status-cancelled">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at?->format('d M Y') }}</td>
                    <td>
                        <div class="action-group">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-secondary"><i class="bi bi-pencil"></i></a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $user) }}" style="display:inline"
                                  onsubmit="return confirm('Delete this user account?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete Account"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer">{{ $users->links('vendor.pagination.simple') }}</div>
    @endif
</div>
@endsection
