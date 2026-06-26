@extends('layouts.client')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h4 mb-1" style="color:var(--brand-blue);">Rate your support experience</h1>
        <p class="text-muted mb-0">Ticket #{{ $ticket->ticket_number }}</p>
    </div>
    <a href="{{ route('client.tickets.show', $ticket->ticket_id) }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        @if($existingRating)
            <div class="alert alert-info mb-4">
                <strong>You've already rated this ticket.</strong> You can update your rating below.
            </div>
        @endif

        <form method="POST" action="{{ route('client.ratings.store', $ticket->ticket_id) }}">
            @csrf

            <div class="mb-4">
                <label class="form-label">How satisfied are you with the support you received?</label>
                <div class="rating-stars">
                    @for($i = 1; $i <= 5; $i++)
                        <label class="form-check-inline" style="cursor: pointer; font-size: 2rem;">
                            <input type="radio" name="rating" value="{{ $i }}" 
                                @checked($existingRating && $existingRating->rating == $i)
                                class="form-check-input" required>
                            <span class="star">
                                @for($j = 1; $j <= $i; $j++)
                                    ⭐
                                @endfor
                            </span>
                        </label>
                    @endfor
                </div>
                @error('rating')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="comment" class="form-label">Additional comments (optional)</label>
                <textarea name="comment" id="comment" class="form-control" rows="4" placeholder="Share your feedback...">{{ $existingRating?->comment }}</textarea>
                <small class="text-muted">Max 1000 characters</small>
                @error('comment')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Submit Rating</button>
                <a href="{{ route('client.tickets.show', $ticket->ticket_id) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
    .rating-stars label {
        margin-right: 1rem;
        transition: transform 0.2s;
    }
    .rating-stars label:hover {
        transform: scale(1.1);
    }
</style>

@endsection
