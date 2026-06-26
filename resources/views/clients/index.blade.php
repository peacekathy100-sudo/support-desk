@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-3">
        <div>
            <h1 class="h4 mb-1" style="color:var(--brand-blue);">Clients</h1>
            <p class="text-muted mb-0">Manage active clients and keep their contact details up to date. Clients are auto-synced from the portal.</p>
        </div>
    </div>

    <div class="card shadow-sm mb-4" style="border-radius:var(--card-radius);">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('clients.index') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input name="search" value="{{ request('search') }}" type="search" class="form-control form-control-sm" placeholder="Name, email, code, rep">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary btn-sm">Search</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm" style="border-radius:var(--card-radius);">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:0.92rem;">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Representative</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                        <tr>
                            <td>{{ $client->client_code }}</td>
                            <td>{{ $client->client_name }}</td>
                            <td>{{ $client->client_email ?? '—' }}</td>
                            <td>{{ $client->client_contact ?? '—' }}</td>
                            <td>{{ $client->client_representative ?? '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $client->is_active ? 'success' : 'secondary' }}">{{ $client->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-outline-primary me-2">Edit</a>
                                <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this client?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No clients found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $clients->withQueryString()->links() }}</div>
        </div>
    </div>
</div>
@endsection
