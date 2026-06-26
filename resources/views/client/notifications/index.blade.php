@extends('layouts.client')

@section('content')
<h1 class="h4 mb-3" style="color:var(--brand-blue);">Notifications</h1>
<div class="card shadow-sm">
    <div class="card-body text-muted">
        @if($notifications->isEmpty())
            You have no notifications at this time.
        @else
            <ul class="list-unstyled mb-0">
                @foreach($notifications as $notification)
                <li class="mb-2">{{ $notification->message ?? '' }}</li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
