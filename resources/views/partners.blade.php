@extends('layouts.site')

@section('content')
    <main>
        <section class="hero partners-hero" id="top" data-section data-nav-id="directory" data-bg-target="partner-scene">
            <div class="container hero-content">
                <div class="hero-copy">
                    <h1>Institutions Supporting the Natural Park</h1>
                    <p class="hero-lead">
                        Meet the partner institutions supporting the park's management across the protected landscape,
                        alongside local governments and park managers working to align access, stewardship, public
                        information, and conservation updates.
                    </p>
                    <div class="hero-actions">
                        <a class="button button-primary" href="#directory">Browse partner links</a>
                        <a class="button button-secondary" href="#news">Open the news corner</a>
                        <a class="button button-secondary" href="{{ route('home', [], false) }}">Return home</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="section partner-directory-section" id="directory" data-section data-nav-id="directory" data-bg-target="partner-scene">
            <div class="container partner-directory-layout">
                @php
                    $logoPartners = collect($partners)->filter(fn ($partner) => !empty($partner['logo']))->values();
                @endphp

                <div class="section-heading compact-heading text-panel">
                    <div>
                        <p class="section-tag">Partner Directory</p>
                        <h2>Official Organizations and Local Governments linked to the park</h2>
                    </div>
                    <p>
                        Each logo links directly to an institution involved in protected-area management,
                        biodiversity conservation, or the wider governance landscape around Mts. Iglit-Baco.
                    </p>
                </div>

                <div class="partner-directory-grid">
                    @foreach ($logoPartners as $partner)
                        <article class="partner-directory-card">
                            <a
                                class="partner-logo-link"
                                href="{{ $partner['url'] }}"
                                aria-label="Visit {{ $partner['name'] }}"
                                @if ($partner['external']) target="_blank" rel="noopener noreferrer" @endif
                            >
                                <div class="partner-directory-logo">
                                    <img src="{{ $partner['logo'] }}" alt="{{ $partner['alt'] }}" loading="lazy" decoding="async">
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="section partner-news-section" id="news" data-section data-nav-id="news" data-bg-target="support-scene">
            <div class="container partner-news-layout">
                @php
                    $featuredNews = $newsItems[0] ?? null;
                    $headlineNews = collect($newsItems)->slice(1)->values();
                @endphp

                <div class="section-heading compact-heading text-panel partner-news-heading">
                    <div>
                        <h2>News Corner</h2>
                    </div>
                </div>

                @if ($featuredNews)
                    <div class="news-corner-board">
                        <article class="news-corner-feature">
                            <div class="news-corner-feature-media">
                                <img
                                    src="{{ $featuredNews['image'] }}"
                                    alt="{{ $featuredNews['image_alt'] }}"
                                >
                            </div>

                            <div class="news-corner-feature-copy">
                                <div class="news-corner-meta">
                                    <span class="section-tag">{{ $featuredNews['tag'] }}</span>
                                    <span class="news-corner-source">{{ $featuredNews['source'] }}</span>
                                </div>

                                @if (!empty($featuredNews['status']))
                                    <p class="news-corner-status">{{ $featuredNews['status'] }}</p>
                                @endif

                                <h3>{{ $featuredNews['title'] }}</h3>
                                <p>{{ $featuredNews['body'] }}</p>

                                <div class="news-corner-actions">
                                    <a
                                        class="news-corner-link"
                                        href="{{ $featuredNews['url'] }}"
                                        @if ($featuredNews['external']) target="_blank" rel="noopener noreferrer" @endif
                                    >
                                        {{ $featuredNews['cta'] }}
                                    </a>
                                    @if (!empty($featuredNews['document_url']))
                                        <a class="news-corner-link news-corner-document-link" href="{{ $featuredNews['document_url'] }}">
                                            {{ $featuredNews['document_name'] }}
                                            @if ($featuredNews['document_size'])
                                                <span>{{ $featuredNews['document_size'] }}</span>
                                            @endif
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </article>

                        <aside class="news-corner-rail site-side-panel" aria-labelledby="field-updates-title">
                            <div class="news-corner-rail-heading">
                                <p class="section-tag">Field Updates</p>
                                <h3 id="field-updates-title">Latest from partners</h3>
                                <p>
                                    Follow conservation work, community updates, and useful notices from across the
                                    wider protected landscape.
                                </p>
                            </div>

                            <div class="news-corner-stack">
                                @foreach ($headlineNews as $item)
                                    <article class="news-corner-brief">
                                        <div class="news-corner-brief-meta">
                                            <span class="news-corner-source">{{ $item['source'] }}</span>
                                            @if ($item['external'])
                                                <span class="news-corner-external">External source</span>
                                            @endif
                                        </div>

                                        <p class="news-corner-brief-tag">{{ $item['tag'] }}</p>

                                        <h4>{{ $item['title'] }}</h4>

                                        @if (!empty($item['status']))
                                            <p class="news-corner-brief-status">{{ $item['status'] }}</p>
                                        @endif

                                        <div class="news-corner-actions">
                                            <a
                                                class="news-corner-link"
                                                href="{{ $item['url'] }}"
                                                @if ($item['external']) target="_blank" rel="noopener noreferrer" @endif
                                            >
                                                {{ $item['cta'] }}
                                            </a>
                                            @if (!empty($item['document_url']))
                                                <a class="news-corner-link news-corner-document-link" href="{{ $item['document_url'] }}">
                                                    {{ $item['document_name'] }}
                                                    @if ($item['document_size'])
                                                        <span>{{ $item['document_size'] }}</span>
                                                    @endif
                                                </a>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </aside>
                    </div>
                @endif

            </div>
        </section>

    </main>
@endsection
