@extends('layouts.staff')

@section('title', 'News updates')

@section('content')
    <div class="staff-page-heading">
        <div>
            <p class="staff-eyebrow">Content management</p>
            <h1>News updates</h1>
            <p class="staff-muted">Publish notices, conservation stories, events, and partner updates.</p>
        </div>

        <a class="staff-button staff-button-primary" href="{{ route('staff.news.create') }}">Create update</a>
    </div>

    @if ($newsPosts->isEmpty())
        <section class="staff-empty-state">
            <h2>No news updates yet</h2>
            <p>Create the first update to populate the public News Corner.</p>
            <a class="staff-button staff-button-primary" href="{{ route('staff.news.create') }}">Create update</a>
        </section>
    @else
        <section class="staff-list" aria-label="News updates">
            @foreach ($newsPosts as $newsPost)
                @php
                    $displayStatus = match (true) {
                        $newsPost->status === \App\Models\NewsPost::STATUS_DRAFT => 'Draft',
                        $newsPost->published_at?->isFuture() => 'Scheduled',
                        $newsPost->expires_at?->isPast() => 'Expired',
                        default => 'Published',
                    };
                @endphp

                <article class="staff-list-item">
                    <div class="staff-list-copy">
                        <div class="staff-badges">
                            <span class="staff-badge staff-badge-{{ strtolower($displayStatus) }}">{{ $displayStatus }}</span>
                            <span class="staff-badge">{{ $newsPost->category }}</span>
                            @if ($newsPost->is_pinned)
                                <span class="staff-badge staff-badge-pinned">Featured</span>
                            @endif
                        </div>

                        <h2>{{ $newsPost->title }}</h2>
                        <p>{{ $newsPost->summary }}</p>

                        <dl class="staff-list-meta">
                            <div>
                                <dt>Source</dt>
                                <dd>{{ $newsPost->source }}</dd>
                            </div>
                            <div>
                                <dt>Publication</dt>
                                <dd>{{ $newsPost->published_at?->format('M j, Y g:i A') ?? 'Not set' }}</dd>
                            </div>
                            <div>
                                <dt>Updated</dt>
                                <dd>{{ $newsPost->updated_at->diffForHumans() }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="staff-list-actions">
                        <a class="staff-button staff-button-secondary" href="{{ route('staff.news.preview', $newsPost) }}">Preview</a>
                        <a class="staff-button staff-button-primary" href="{{ route('staff.news.edit', $newsPost) }}">Edit</a>
                    </div>
                </article>
            @endforeach
        </section>

        @if ($newsPosts->hasPages())
            <nav class="staff-pagination" aria-label="News pages">
                @if ($newsPosts->onFirstPage())
                    <span>Previous</span>
                @else
                    <a href="{{ $newsPosts->previousPageUrl() }}">Previous</a>
                @endif

                <span>Page {{ $newsPosts->currentPage() }} of {{ $newsPosts->lastPage() }}</span>

                @if ($newsPosts->hasMorePages())
                    <a href="{{ $newsPosts->nextPageUrl() }}">Next</a>
                @else
                    <span>Next</span>
                @endif
            </nav>
        @endif
    @endif
@endsection
