@extends('layouts.site')

@section('content')
    <main>
        <section class="news-detail-hero" data-section data-bg-target="news-detail-scene">
            <div class="container news-detail-hero-copy">
                <p class="eyebrow section-tag-light">{{ $newsPost->category }}</p>
                <h1>{{ $newsPost->title }}</h1>
                <p>{{ $newsPost->source }} · {{ $newsPost->published_at->format('F j, Y') }}</p>
            </div>
        </section>

        <section class="section news-detail-section" data-section data-bg-target="news-detail-scene">
            <article class="container news-detail-article">
                <img src="{{ $newsPost->publicImage() }}" alt="{{ $newsPost->image_alt }}">

                <div class="news-detail-copy">
                    <p class="news-detail-summary">{{ $newsPost->summary }}</p>

                    @if ($newsPost->body)
                        <div class="news-detail-body">{!! nl2br(e($newsPost->body)) !!}</div>
                    @endif

                    @if ($newsPost->document_path)
                        <a
                            class="news-document-card"
                            href="{{ route('news.document', $newsPost) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <span class="news-document-icon" aria-hidden="true">PDF</span>
                            <span>
                                <strong>{{ $newsPost->documentDisplayName() }}</strong>
                                <small>
                                    View PDF{{ $newsPost->documentSizeLabel() ? ' · '.$newsPost->documentSizeLabel() : '' }}
                                </small>
                            </span>
                            <span class="news-document-arrow" aria-hidden="true">↗</span>
                        </a>
                    @endif

                    <a class="button button-primary" href="{{ route('partners', [], false) }}#news">Back to News Corner</a>
                </div>
            </article>
        </section>
    </main>
@endsection
