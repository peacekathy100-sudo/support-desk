@extends('layouts.client')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1" style="color:var(--brand-blue);">Knowledge Base</h1>
        <p class="text-muted mb-0">Find answers and learn how to use our services</p>
    </div>
</div>

<!-- Search and Filter -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" placeholder="Search articles..." value="{{ $searchTerm ?? '' }}">
            </div>
            <div class="col-md-4">
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected($selectedCategory == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-sm">Search</button>
                <a href="{{ route('client.knowledge.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Articles Grid -->
@if($articles->count())
    <div class="row g-3">
        @foreach($articles as $article)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('client.knowledge.show', ['article' => $article->id]) }}" class="text-decoration-none">
                    <div class="card shadow-sm h-100 border-0 hover-card" style="transition: transform 0.2s;">
                        <div class="card-body">
                            @if($article->is_featured)
                                <span class="badge bg-warning text-dark mb-2">Featured</span>
                            @endif
                            <h5 class="card-title" style="color:var(--brand-blue);">{{ $article->title }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit(strip_tags($article->content), 100) }}</p>
                            <div class="small text-muted">
                                <span class="badge bg-light text-dark">{{ $article->category->name }}</span>
                                <span class="ms-2">👁 {{ $article->views }} views</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $articles->links() }}
    </div>
@else
    <div class="alert alert-info text-center py-5">
        <p class="mb-0">No articles found. Try a different search or category.</p>
    </div>
@endif

@endsection
