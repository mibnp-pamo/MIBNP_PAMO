@extends('layouts.site')

@section('content')
    <main>
        <section class="hero gallery-hero" id="top" data-section data-bg-target="gallery-hero">
            <div class="container hero-content">
                <div class="hero-copy">
                    <h1>See the park through ridges, weather, and wildlife habitat</h1>
                    <p class="hero-lead">
                        Browse official field photos from grassland approaches, forest edges, scenic ridges, and Tamaraw range
                        across Mts. Iglit-Baco Natural Park.
                    </p>
                    <div class="hero-actions">
                        <a class="button button-primary" href="#collection">Open the collection</a>
                        <a class="button button-secondary" href="{{ route('geography', [], false) }}">Study the geography</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="section gallery-collection-section" id="collection" data-section data-bg-target="gallery-wildlife">
            <div class="container gallery-collection-layout">
                <div class="gallery-collection-top">
                    <div class="gallery-collection-intro-content">
                        <div class="section-heading compact-heading text-panel">
                            <div>
                                <p class="section-tag">Photo Collection</p>
                                <h2>Photos from the archives of Mts. Iglit-Baco Natural Park</h2>
                            </div>
                        </div>

                        <div class="gallery-collection-intro" aria-label="Collection summary">
                            <div class="gallery-collection-pill">
                                <span>Archive</span>
                                <strong>{{ $galleryPhotoCount }} official images</strong>
                            </div>
                            <div class="gallery-collection-pill">
                                <span>Groups</span>
                                <strong>{{ count($galleryGroups) }} photo sets</strong>
                            </div>
                        </div>
                    </div>

                    <aside class="gallery-collection-sidebar site-side-panel" aria-labelledby="gallery-collection-guide-title">
                        <p class="section-tag">Collection Guide</p>
                        <h3 id="gallery-collection-guide-title">A field archive, organised by landscape</h3>
                        <p>
                            Open any image for a closer view, then move through the archive to see the habitats,
                            wildlife, and field work that shape the park.
                        </p>
                        <a class="button button-secondary button-full" href="{{ route('biodiversity', [], false) }}">
                            Explore biodiversity
                        </a>
                    </aside>
                </div>

                <div class="gallery-group-stack">
                    @foreach ($galleryGroups as $group)
                        @php
                            $archiveLabelWords = [
                                1 => '01',
                                2 => '02',
                                3 => '03',
                                4 => '04',
                                5 => '05',
                                6 => '06',
                            ];
                            $archiveTitle = 'Photo Archive ' . ($archiveLabelWords[$loop->iteration] ?? $loop->iteration);
                            $initialPhotoCount = 8;
                            $remainingPhotoCount = max(0, $group['count'] - $initialPhotoCount);
                        @endphp
                        <section class="gallery-group-shell">
                            <header class="gallery-group-heading">
                                <div>
                                    <p class="gallery-group-kicker">{{ $archiveTitle }}</p>
                                    <h3 class="gallery-group-title">{{ $group['title'] }}</h3>
                                    <p class="gallery-group-summary">{{ $group['summary'] }}</p>
                                </div>
                                <span class="gallery-group-count">{{ $group['count'] }} photos</span>
                            </header>

                            <div class="gallery-group-photo-grid" id="gallery-group-{{ $group['id'] }}">
                                @foreach ($group['frames'] as $frame)
                                    <figure
                                        class="gallery-frame-card gallery-group-photo-card"
                                        @if ($loop->iteration > $initialPhotoCount) data-gallery-extra hidden @endif
                                        data-lightbox-item
                                        data-lightbox-group="{{ $group['id'] }}"
                                        data-lightbox-hide-caption="true"
                                        data-lightbox-src="{{ $frame['image'] }}"
                                        data-lightbox-alt="{{ $frame['alt'] ?? $frame['title'] }}"
                                        data-lightbox-title="Archive photo"
                                        data-lightbox-description="{{ $frame['caption'] ?? '' }}"
                                        data-lightbox-meta="{{ $frame['meta'] }}"
                                        data-lightbox-credit="{{ $frame['credit'] ?? '' }}"
                                        role="button"
                                        tabindex="0"
                                        aria-label="Open archive photo"
                                    >
                                        <div class="gallery-card-image">
                                            <img
                                                src="{{ $frame['thumbnail'] ?? $frame['image'] }}"
                                                alt="{{ $frame['alt'] ?? $frame['title'] }}"
                                                loading="lazy"
                                                decoding="async"
                                            >
                                        </div>
                                    </figure>
                                @endforeach
                            </div>

                            @if ($remainingPhotoCount > 0)
                                <div class="gallery-group-actions">
                                    <button
                                        class="button button-secondary gallery-expand-button"
                                        type="button"
                                        data-gallery-expand-toggle
                                        data-collapsed-label="View {{ $remainingPhotoCount }} more photos"
                                        data-expanded-label="Show fewer photos"
                                        aria-controls="gallery-group-{{ $group['id'] }}"
                                        aria-expanded="false"
                                    >
                                        View {{ $remainingPhotoCount }} more photos
                                    </button>
                                </div>
                            @endif
                        </section>
                    @endforeach
                </div>
            </div>
        </section>

    </main>
@endsection

@push('after-body')
    @include('partials.lightbox')
@endpush
