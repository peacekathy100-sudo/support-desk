@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-3">
        <div>
            <h1 class="h4 mb-1" style="color:var(--brand-blue);">Users</h1>
            <p class="text-muted mb-0">Manage system users and their access roles.</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn" style="background:var(--brand-blue); color:#fff;">Create User</a>
    </div>

    <div class="card shadow-sm mb-4" style="border-radius:var(--card-radius);">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:0.92rem;">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr class="{{ $user->trashed() ? 'text-muted' : '' }}">
                            <td>{{ $user->user_surname }} {{ $user->user_othername }}</td>
                            <td>{{ $user->user_email }}</td>
                            <td>{{ $user->role?->ur_name ?? '—' }}</td>
                            <td>{{ $user->department?->dept_name ?? '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $user->user_status === 'active' ? 'success' : ($user->user_status === 'inactive' ? 'secondary' : 'warning') }}">
                                    {{ ucfirst($user->user_status) }}
                                </span>
                                @if($user->trashed())<span class="badge bg-danger">Deleted</span>@endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary me-2">Edit</a>
                                @if(!$user->trashed())
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No users found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $users->links() }}</div>
        </div>
    </div>
</div>
@endsection
