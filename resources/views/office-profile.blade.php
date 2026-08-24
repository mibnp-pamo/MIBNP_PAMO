@extends('layouts.site')

@php
    $emailItem = collect($officeAtGlance)->firstWhere('label', 'Email');
    $facebookItem = collect($officeAtGlance)->firstWhere('label', 'Facebook');
    $mapItem = collect($officeAtGlance)->firstWhere('label', 'Map reference');

    $emailHref = $emailItem['href'] ?? 'mailto:r4b.mibnp@denr.gov.ph';
    $facebookHref = $facebookItem['href'] ?? 'https://www.facebook.com/mibnppamo';
    $mapHref = $mapItem['href'] ?? route('geography', [], false);
@endphp

@section('content')
    <main>
        <section class="hero" id="top" data-section data-nav-id="overview" data-bg-target="office-hero">
            <div class="container hero-content">
                <div class="hero-copy">
                    <h1>Protected Area Management Office</h1>
                    <div class="hero-actions">
                        <a class="button button-primary" href="{{ route('geography', [], false) }}">View Map</a>
                        <a class="button button-secondary" href="{{ $emailHref }}">Email the office</a>

                    </div>
                </div>
            </div>
        </section>

        <section class="section" id="overview" data-section data-nav-id="overview" data-bg-target="office-story">
            <div class="container contact-layout">
                <div class="text-panel">
                    <p class="section-tag">Office Profile</p>
                    <h2>The Protected Area Management Office (PAMO) of Mts. Iglit-Baco Natural Park (MIBNP)</h2>
                    @foreach ($officeSummaryParagraphs as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>

                <aside class="office-location-card site-side-panel" aria-label="PAMO office photo and map">
                    <figure class="office-location-photo">
                        <img src="{{ $officePamoMedia['image'] }}" alt="{{ $officePamoMedia['image_alt'] }}" loading="lazy">
                        <figcaption>PAMO office</figcaption>
                    </figure>
                    <div class="office-location-map">
                        <iframe
                            src="{{ $officePamoMedia['map_embed_url'] }}"
                            title="OpenStreetMap location of the PAMO office"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                        <a href="{{ $officePamoMedia['map_url'] }}" target="_blank" rel="noopener noreferrer">
                            Open in OpenStreetMap
                        </a>
                    </div>
                </aside>
            </div>
        </section>

        <section class="section office-organization-section" id="organization" data-section data-bg-target="office-story">
            <div class="container">
                <div class="section-heading compact-heading text-panel office-organization-heading">
                    <div>
                        <p class="section-tag">Office Profile</p>
                        <h2>Organizational Chart</h2>
                    </div>
                </div>

                <figure class="office-organization-chart">
                    <a
                        href="{{ asset('home-assets/mibnp-organizational-chart.webp') }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Open the MIBNP PAMO organizational chart at full size"
                    >
                        <img
                            src="{{ \App\Support\PublicSiteContent::optimizedAsset('home-assets/mibnp-organizational-chart.webp') }}"
                            alt="Organizational chart of the Mts. Iglit-Baco Natural Park Protected Area Management Office"
                            width="3506"
                            height="2337"
                            loading="lazy"
                        >
                    </a>
                    <figcaption>Select the chart to view it at full size.</figcaption>
                </figure>
            </div>
        </section>

        <section class="section" id="functions" data-section data-nav-id="functions" data-bg-target="office-story">
            <div class="container">
                <div class="section-heading compact-heading text-panel">
                    <div>
                        <h2>How the office supports visitors and protected-area access</h2>
                    </div>
                </div>

                <div class="partner-news-grid">
                    @foreach ($officeFunctions as $item)
                        <article class="partner-news-card text-panel">
                            <div class="partner-news-meta">
                                <span class="section-tag">{{ $item['tag'] }}</span>
                            </div>
                            <h3>{{ $item['title'] }}</h3>
                            <p>{{ $item['body'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="section" id="planning" data-section data-nav-id="planning" data-bg-target="office-contact">
            <div class="container story-grid">
                <div class="story-copy text-panel">

                    <h2>How was the office established?</h2>
                    @foreach ($planningParagraphs as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>

                <div class="office-history-visual">
                    <div class="office-history-map" aria-label="Mapped office establishment timeline">
                        <div class="office-history-map-heading">
                            <span>Office history map</span>
                            <strong>PAMO timeline</strong>
                        </div>
                        <svg class="office-history-map-path" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true" focusable="false">
                            <path d="M 14 70 C 24 58 34 40 50 38 S 70 48 84 66" />
                        </svg>
                        @foreach ($planningCards as $card)
                            <article
                                class="office-history-map-point"
                                style="--point-x: {{ $card['map_x'] ?? 50 }}%; --point-y: {{ $card['map_y'] ?? 50 }}%;"
                            >
                                <span class="office-history-map-dot" aria-hidden="true"></span>
                                <h3>{{ $card['title'] }}</h3>
                                <p>{{ $card['body'] }}</p>
                            </article>
                        @endforeach
                    </div>

                </div>

                @if (!empty($brochureImages))
                    <div class="office-brochure-viewer" data-brochure-viewer>
                        <div class="office-brochure-stage">
                            @foreach ($brochureImages as $image)
                                <a
                                    class="office-brochure-slide{{ $loop->first ? ' is-active' : '' }}"
                                    href="{{ $image['full_image'] ?? $image['image'] }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label="Open brochure image {{ $loop->iteration }} in a new tab"
                                    data-brochure-slide
                                    @if (!$loop->first) hidden @endif
                                >
                                    <img src="{{ $image['image'] }}" alt="{{ $image['alt'] }}" loading="lazy">
                                </a>
                            @endforeach
                        </div>

                        @if (count($brochureImages) > 1)
                            <div class="office-brochure-controls">
                                <button
                                    class="office-brochure-nav"
                                    type="button"
                                    data-brochure-prev
                                    aria-label="Show previous brochure image"
                                >
                                    Previous
                                </button>
                                <p class="office-brochure-status" data-brochure-status>
                                    1 / {{ count($brochureImages) }}
                                </p>
                                <button
                                    class="office-brochure-nav"
                                    type="button"
                                    data-brochure-next
                                    aria-label="Show next brochure image"
                                >
                                    Next
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </section>

        <section class="section" id="contact-info" data-section data-nav-id="contact-info" data-bg-target="office-contact">
            <div class="container contact-layout">
                <div class="contact-card">
                    <p class="section-tag">Contact Information</p>
                    <h3>Use the official channels before traveling</h3>
                    <p>
                        Not every trip needs the same coordination. The most reliable way to get updated instructions,
                        current requirements, and park-specific guidance is to contact the office directly before you travel.
                    </p>

                    <dl>
                        @foreach ($officeAtGlance as $item)
                            <div>
                                <dt>{{ $item['label'] }}</dt>
                                <dd>
                                    @if (!empty($item['href']))
                                        <a href="{{ $item['href'] }}" @if (!empty($item['external'])) target="_blank" rel="noopener noreferrer" @endif>
                                            {{ $item['value'] }}
                                        </a>
                                    @else
                                        {{ $item['value'] }}
                                    @endif
                                </dd>
                            </div>
                        @endforeach
                    </dl>

                    <div class="contact-actions">
                        <a class="button button-primary" href="{{ $emailHref }}">Email the office</a>
                        <a class="button button-secondary" href="{{ $facebookHref }}" target="_blank" rel="noopener noreferrer">
                            Open Facebook
                        </a>
                    </div>
                </div>

                <aside class="visit-sidebar site-side-panel">
                    <p class="eyebrow">Before You Reach Out</p>
                    <h3>Bring these questions into your inquiry</h3>
                    <ul>
                        @foreach ($planningChecklist as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                    <a class="button button-primary button-full" href="{{ $mapHref }}" target="_blank" rel="noopener noreferrer">
                        Open PAMO map point
                    </a>
                </aside>
            </div>
        </section>
    </main>
@endsection
