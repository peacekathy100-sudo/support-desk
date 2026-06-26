@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-3">
        <div>
            <h1 class="h4 mb-1" style="color:var(--brand-blue);">Portal clients</h1>
            <p class="text-muted mb-0">External accounts that can sign in to the client portal and submit tickets.</p>
        </div>
        <a href="{{ route('admin.external-clients.create') }}" class="btn" style="background:var(--brand-blue); color:#fff;">Add portal client</a>
    </div>

    <div class="card shadow-sm" style="border-radius:var(--card-radius);">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:0.92rem;">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th>Company</th>
                            <th>Contact</th>
                            <th>Username</th>
                            <th>Representative</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                        <tr>
                            <td>{{ $client->company_name }}</td>
                            <td>{{ $client->full_name }}<br><small class="text-muted">{{ $client->email }}</small></td>
                            <td>{{ $client->username }}</td>
                            <td>{{ $client->assignedRepresentative?->user_name ?? '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $client->status === 'active' ? 'success' : ($client->status === 'suspended' ? 'danger' : 'secondary') }}">
                                    {{ ucfirst($client->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.external-clients.show', $client) }}" class="btn btn-sm btn-outline-primary">View</a>
                                <a href="{{ route('admin.external-clients.edit', $client) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted">No portal clients yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $clients->links() }}</div>
        </div>
    </div>
</div>
@endsection
