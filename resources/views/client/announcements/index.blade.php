@extends('layouts.client')

@section('content')
<h1 class="h4 mb-4" style="color:var(--brand-blue);">
    <i class="ph-megaphone"></i> Announcements
</h1>

@if($announcements->count())
    @foreach($announcements as $announcement)
        <div class="card shadow-sm mb-3 border-start" style="border-left: 4px solid var(--brand-blue);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h5 class="mb-1">{{ $announcement->title }}</h5>
                        <div class="small text-muted">
                            <span class="badge bg-{{ $announcement->type === 'maintenance' ? 'danger' : ($announcement->type === 'feature' ? 'success' : 'info') }}">
                                {{ ucfirst($announcement->type) }}
                            </span>
                            <span class="ms-2">{{ $announcement->created_at->format('M j, Y H:i') }}</span>
                        </div>
                    </div>
                    <span class="badge bg-{{ $announcement->priority === 'high' ? 'danger' : ($announcement->priority === 'medium' ? 'warning' : 'secondary') }}">
                        {{ ucfirst($announcement->priority) }}
                    </span>
                </div>

                <div class="mt-3">
                    {!! nl2br(e($announcement->content)) !!}
                </div>

                @if($announcement->start_date || $announcement->end_date)
                    <div class="small text-muted mt-3">
                        @if($announcement->start_date)
                            <i class="ph-calendar"></i> From: {{ $announcement->start_date->format('M j, Y H:i') }}
                        @endif
                        @if($announcement->end_date)
                            <br><i class="ph-calendar"></i> Until: {{ $announcement->end_date->format('M j, Y H:i') }}
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $announcements->links() }}
    </div>
@else
    <div class="alert alert-info text-center py-5">
        <p class="mb-0">No announcements at this time.</p>
    </div>
@endif

@endsection
