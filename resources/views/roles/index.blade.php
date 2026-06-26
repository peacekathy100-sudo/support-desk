@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-3">
        <div>
            <h1 class="h4 mb-1" style="color:var(--brand-blue);">User Roles</h1>
            <p class="text-muted mb-0">Define role permissions and assign roles to users.</p>
        </div>
        <a href="{{ route('roles.create') }}" class="btn" style="background:var(--brand-blue); color:#fff;">New Role</a>
    </div>

    <div class="card shadow-sm" style="border-radius:var(--card-radius);">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:0.92rem;">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th>Role</th>
                            <th>Permissions</th>
                            <th>Users</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                        <tr>
                            <td>{{ $role->ur_name }}</td>
                            <td>{{ implode(', ', $role->permissions ?? []) ?: 'None' }}</td>
                            <td>{{ $role->users_count }}</td>
                            <td><span class="badge bg-{{ $role->is_active ? 'success' : 'secondary' }}">{{ $role->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-primary me-2">Edit</a>
                                <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this role?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No roles available.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
