@extends('layouts.master')

@section('content')
<div id="printable-content" class="container py-4">
    @php
        $reportFilters = array_filter([
            'Status' => request('is_active') !== null && request('is_active') !== '' ? (request('is_active') ? 'Active' : 'Inactive') : null,
        ], fn ($value) => filled($value));
    @endphp

    <div class="receipt-document">
        <div class="receipt-header">
            <div>
                <span class="receipt-badge">Official receipt-style document</span>
                <h1 class="receipt-title mt-2 mb-1">Client Report</h1>
                <p class="receipt-subtitle mb-0">A printable document that combines client activity, status insights, and audit trail history.</p>
            </div>
            <div class="receipt-idbox">
                <div class="receipt-meta-label">Generated</div>
                <div class="receipt-meta-value">{{ now()->format('F j, Y \a\t g:i A') }}</div>
                <div class="receipt-meta-label mt-2">Prepared by</div>
                <div class="receipt-meta-value">{{ auth()->user()->full_name }}</div>
            </div>
        </div>

        <div class="receipt-meta-grid">
            <div class="receipt-meta-card">
                <span class="receipt-meta-label">Document type</span>
                <span class="receipt-meta-value">Client report</span>
            </div>
            <div class="receipt-meta-card">
                <span class="receipt-meta-label">Total records</span>
                <span class="receipt-meta-value">{{ $stats['total'] }}</span>
            </div>
            <div class="receipt-meta-card">
                <span class="receipt-meta-label">Audit trail entries</span>
                <span class="receipt-meta-value">{{ $auditTrail->count() }}</span>
            </div>
            <div class="receipt-meta-card">
                <span class="receipt-meta-label">Client health</span>
                <span class="receipt-meta-value">{{ $stats['active'] }} active / {{ $stats['inactive'] }} inactive</span>
            </div>
        </div>

        @if($reportFilters)
        <div class="receipt-section">
            <div class="receipt-section-title">Filter summary</div>
            <div class="d-flex flex-wrap gap-2">
                @foreach($reportFilters as $label => $value)
                    <span class="badge bg-light text-dark border">{{ $label }}: {{ $value }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mt-3 mb-3 no-print">
        <div>
            <h2 class="h5 mb-1" style="color:var(--brand-blue);">Client summary</h2>
            <p class="text-muted mb-0">Generate a receipt-ready copy of the current client view.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm html2pdf__ignore" onclick="window.print()">
                <i class="ph-printer me-1"></i>Print
            </button>
            <button type="button" class="btn btn-primary btn-sm html2pdf__ignore" onclick="downloadPDF('printable-content', 'client-report.pdf')">
                <i class="ph-download-simple me-1"></i>Download
            </button>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <div class="text-muted small">Total clients</div>
                    <div class="h3 mb-0">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <div class="text-muted small">Active clients</div>
                    <div class="h3 mb-0">{{ $stats['active'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <div class="text-muted small">Inactive clients</div>
                    <div class="h3 mb-0">{{ $stats['inactive'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm" style="border-radius:var(--card-radius);">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:0.92rem;">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Tickets</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                        <tr>
                            <td>{{ $client->client_name }}</td>
                            <td>{{ $client->client_email ?? '—' }}</td>
                            <td>{{ $client->tickets_count }}</td>
                            <td><span class="badge bg-{{ $client->is_active ? 'success' : 'secondary' }}">{{ $client->is_active ? 'Active' : 'Inactive' }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No client records available.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-3" style="border-radius:var(--card-radius);">
        <div class="card-body p-3">
            <div class="receipt-section-title mb-2">
                Audit trail
                <small class="text-muted">Recent client activity captured in the system</small>
            </div>

            @if($auditTrail->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-sm mb-0 receipt-audit-table">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Action</th>
                                <th>Performed by</th>
                                <th>Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditTrail as $entry)
                                <tr>
                                    <td>{{ $entry->created_at->format('M j, Y g:i A') }}</td>
                                    <td>{{ ucfirst($entry->action) }}</td>
                                    <td>{{ $entry->user?->full_name ?? 'System' }}</td>
                                    <td>{{ $entry->url ?? 'Client record update' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">No audit trail entries were recorded for the selected client set.</p>
            @endif
        </div>
    </div>

    <div class="receipt-footer">Generated from Flaxem Support Desk • For internal use only</div>
</div>
@endsection
