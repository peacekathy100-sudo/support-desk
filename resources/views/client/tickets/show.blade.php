@extends('layouts.client')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h1 class="h4 mb-1" style="color:var(--brand-blue);">{{ $ticket->subject }}</h1>
        <p class="text-muted mb-0">{{ $ticket->ticket_number }} · {{ $ticket->status }} · {{ $ticket->priority }}</p>
    </div>
    <a href="{{ route('client.tickets.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <p class="mb-0">{!! nl2br(e($ticket->description)) !!}</p>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-white"><strong>Comments</strong></div>
    <div class="card-body">
        @forelse($comments as $comment)
        <div class="border-bottom pb-2 mb-2">
            <div class="small text-muted">{{ $comment->created_at?->format('M j, Y H:i') }}</div>
            <div>{!! nl2br(e($comment->comment)) !!}</div>
        </div>
        @empty
        <p class="text-muted mb-0">No comments yet.</p>
        @endforelse
        {{ $comments->links() }}
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="POST" action="{{ route('client.tickets.comment', $ticket) }}">
            @csrf
            <label class="form-label">Add comment</label>
            <textarea name="message" class="form-control mb-2" rows="3" required></textarea>
            <button type="submit" class="btn btn-primary btn-sm">Post comment</button>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('client.tickets.attach', $ticket) }}" enctype="multipart/form-data">
            @csrf
            <label class="form-label">Attach file</label>
            <input type="file" name="file" class="form-control mb-2" required>
            <button type="submit" class="btn btn-outline-primary btn-sm">Upload</button>
        </form>
    </div>
</div>

<!-- Ticket Timeline -->
<div class="card shadow-sm mt-3">
    <div class="card-header bg-white"><strong>Timeline</strong></div>
    <div class="card-body">
        <div class="timeline">
            <div class="timeline-item mb-3">
                <div class="small text-muted">{{ $ticket->created_at?->format('M j, Y H:i') }}</div>
                <div><i class="ph-plus-circle" style="color:var(--brand-blue);"></i> Ticket created</div>
            </div>
            @foreach($ticket->history ?? [] as $history)
                <div class="timeline-item mb-3">
                    <div class="small text-muted">{{ $history->created_at?->format('M j, Y H:i') }}</div>
                    <div><i class="ph-arrow-right" style="color:var(--brand-blue);"></i> {{ $history->action ?? 'Status updated' }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Rating Section -->
@if($ticket->status === 'resolved' || $ticket->status === 'closed')
    <div class="card shadow-sm mt-3 border-success">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Rate your support experience</h6>
                    <p class="text-muted small mb-0">Help us improve by rating this ticket</p>
                </div>
                <a href="{{ route('client.ratings.create', $ticket->ticket_id) }}" class="btn btn-success btn-sm">Rate Now</a>
            </div>
        </div>
    </div>
@endif

<style>
    .timeline {
        position: relative;
        padding-left: 20px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--brand-blue);
    }
    .timeline-item {
        position: relative;
        padding-left: 20px;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -24px;
        top: 2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--brand-blue);
        border: 2px solid white;
    }
</style>
@endsection
