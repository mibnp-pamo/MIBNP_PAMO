@extends('layouts.staff')

@section('title', 'Preview news update')

@section('content')
    <div class="staff-page-heading">
        <div>
            <p class="staff-eyebrow">Private preview</p>
            <h1>{{ $newsPost->title }}</h1>
            <p class="staff-muted">This preview is visible only to signed-in staff.</p>
        </div>
        <a class="staff-button staff-button-primary" href="{{ route('staff.news.edit', $newsPost) }}">Edit update</a>
    </div>

    <article class="staff-preview">
        <img src="{{ $newsPost->publicImage() }}" alt="{{ $newsPost->image_alt }}">

        <div class="staff-preview-copy">
            <div class="staff-badges">
                <span class="staff-badge">{{ $newsPost->category }}</span>
                <span class="staff-badge">{{ ucfirst($newsPost->status) }}</span>
                @if ($newsPost->is_pinned)
                    <span class="staff-badge staff-badge-pinned">Featured</span>
                @endif
            </div>

            <p class="staff-preview-source">
                {{ $newsPost->source }}
                @if ($newsPost->published_at)
                    · {{ $newsPost->published_at->format('F j, Y') }}
                @endif
            </p>
            <h2>{{ $newsPost->title }}</h2>
            <p class="staff-preview-summary">{{ $newsPost->summary }}</p>

            @if ($newsPost->body)
                <div class="staff-preview-body">{!! nl2br(e($newsPost->body)) !!}</div>
            @endif

            @if ($newsPost->document_path)
                <a class="staff-document-card" href="{{ route('staff.news.document', $newsPost) }}">
                    <div>
                        <strong>{{ $newsPost->documentDisplayName() }}</strong>
                        <span>PDF{{ $newsPost->documentSizeLabel() ? ' · '.$newsPost->documentSizeLabel() : '' }}</span>
                    </div>
                    <span>Download</span>
                </a>
            @endif

            @if ($newsPost->external_url)
                <a class="staff-button staff-button-secondary" href="{{ $newsPost->external_url }}" target="_blank" rel="noopener noreferrer">
                    Open external source
                </a>
            @endif
        </div>
    </article>
@endsection
