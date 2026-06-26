@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-3">
        <div>
            <h1 class="h4 mb-1" style="color:var(--brand-blue);">Cost Centers</h1>
            <p class="text-muted mb-0">Manage departments and their assigned heads.</p>
        </div>
        <a href="{{ route('departments.create') }}" class="btn" style="background:var(--brand-blue); color:#fff;">New Department</a>
    </div>

    <div class="card shadow-sm" style="border-radius:var(--card-radius);">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:0.92rem;">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Head</th>
                            <th>Users</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $department)
                        <tr>
                            <td>{{ $department->dept_code }}</td>
                            <td>{{ $department->dept_name }}</td>
                            <td>{{ $department->head?->full_name ?? 'Unassigned' }}</td>
                            <td>{{ $department->users->count() }}</td>
                            <td><span class="badge bg-{{ $department->is_active ? 'success' : 'secondary' }}">{{ $department->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('departments.edit', $department) }}" class="btn btn-sm btn-outline-primary me-2">Edit</a>
                                <form action="{{ route('departments.destroy', $department) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this department?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No departments found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
