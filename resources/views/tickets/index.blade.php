@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-3">
        <div>
            <h1 class="h4 mb-1" style="color:var(--brand-blue);">Ticket Management</h1>
            <p class="text-muted mb-0">Search, filter and review ticket activity.</p>
        </div>
        <a href="{{ route('tickets.create') }}" class="btn" style="background:var(--brand-blue); color:#fff;">Create Ticket</a>
    </div>

    <div class="card shadow-sm mb-4" style="border-radius:var(--card-radius);">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('tickets.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input name="search" value="{{ request('search') }}" type="search" class="form-control form-control-sm" placeholder="Ticket number or subject">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach(['open' => 'Open', 'in_progress' => 'In Progress', 'on_hold' => 'On Hold', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-select form-select-sm">
                        <option value="">Any</option>
                        @foreach(['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'] as $key => $label)
                            <option value="{{ $key }}" {{ request('priority') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select form-select-sm">
                        <option value="">All categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
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
                            <th>#</th>
                            <th>Subject</th>
                            <th>Client</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Due</th>
                            <th class="text-end">Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->ticket_number }}</td>
                            <td><a href="{{ route('tickets.show', $ticket) }}" class="text-decoration-none">{{ Str::limit($ticket->subject, 50) }}</a></td>
                            <td>{{ $ticket->client?->client_name ?? 'Internal' }}</td>
                            <td>{{ $ticket->category?->name ?? 'General' }}</td>
                            <td><span class="badge" style="background:{{ $ticket->priority_color }}; color:#fff;">{{ ucfirst($ticket->priority) }}</span></td>
                            <td><span class="badge" style="background:{{ $ticket->status_color }}; color:#fff;">{{ ucfirst(str_replace('_',' ', $ticket->status)) }}</span></td>
                            <td>{{ $ticket->due_at?->format('M j, Y') ?? '—' }}</td>
                            <td class="text-end text-muted">{{ $ticket->updated_at?->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No tickets found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $tickets->withQueryString()->links() }}</div>
        </div>
    </div>
</div>
@endsection
