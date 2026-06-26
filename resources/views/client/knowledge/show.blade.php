@extends('layouts.client')

@section('content')
<a href="{{ route('client.knowledge.index') }}" class="btn btn-outline-secondary btn-sm mb-3">← Back to Knowledge Base</a>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h1 class="h3 mb-2" style="color:var(--brand-blue);">{{ $article->title }}</h1>
                <div class="text-muted small">
                    <span class="badge bg-light text-dark">{{ $article->category->name }}</span>
                    <span class="ms-2">Last updated: {{ $article->updated_at->format('M j, Y') }}</span>
                    <span class="ms-2">👁 {{ $article->views }} views</span>
                </div>
            </div>
        </div>

        <div class="mt-4">
            {!! $article->content !!}
        </div>

        @if($article->video_url)
            <div class="mt-4">
                <h5 style="color:var(--brand-blue);">Related Video</h5>
                <iframe width="100%" height="315" src="{{ $article->video_url }}" frameborder="0" allowfullscreen style="border-radius: 8px;"></iframe>
            </div>
        @endif
    </div>
</div>

<!-- Related Articles -->
@if($relatedArticles->count())
    <div class="mt-4">
        <h5 style="color:var(--brand-blue);">Related Articles</h5>
        <div class="row g-3">
            @foreach($relatedArticles as $related)
                <div class="col-md-4">
                    <a href="{{ route('client.knowledge.show', ['article' => $related->id]) }}" class="text-decoration-none">
                        <div class="card shadow-sm border-0 h-100" style="transition: transform 0.2s;">
                            <div class="card-body">
                                <h6 style="color:var(--brand-blue);">{{ $related->title }}</h6>
                                <p class="small text-muted">{{ Str::limit(strip_tags($related->content), 80) }}</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endif

@endsection
