@extends('layouts.site')

@section('content')
    <main class="geography-main">
        <h1 class="visually-hidden">Geography of Mounts Iglit-Baco Natural Park</h1>
        <section class="geography-stage" id="map-lab" data-section data-bg-target="geography-board">
            <div class="geography-shell">
                <div class="geo-map-shell geography-map-shell">
                    <div class="geo-map-toolbar geography-map-toolbar">
                        <a
                            class="geo-toolbar-link"
                            href="{{ $mapPoints[0]['osm_url'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            data-map-external
                        >
                            Open in OpenStreetMap
                        </a>
                    </div>
                    <div
                        class="geo-map geography-map"
                        data-map
                        data-default-lat="12.885"
                        data-default-lng="120.94"
                        data-default-zoom="9"
                        data-freeze-view="true"
                        data-limit-zoom-out-to-bounds="true"
                        data-max-zoom="18"
                        data-focus-zoom="18"
                        data-fit-points="true"
                        data-fit-pad-ratio="0.06"
                        data-fit-max-zoom="15"
                        data-scroll-wheel-zoom="false"
                        data-route-origin="pamo"
                        role="region"
                        aria-label="Interactive map of public field points in Mounts Iglit-Baco Natural Park"
                    ></div>
                    <div class="geo-disclaimer geography-disclaimer">Leaflet map using OpenStreetMap tiles and park field reference points.</div>
                </div>

                <div class="geography-point-rail">
                    <div class="text-panel geography-point-panel" data-map-point-panel aria-live="polite">
                        <figure class="geography-point-media" data-map-point-media>
                            <img
                                src="{{ $selectedPoint['image'] }}"
                                alt="{{ $selectedPoint['image_alt'] }}"
                                data-map-point-image
                            >
                        </figure>
                        <div class="geography-point-copy">
                            <p class="section-tag" data-map-point-type>{{ $selectedPoint['type'] }}</p>
                            <h3 data-map-point-title>{{ $selectedPoint['title'] }}</h3>
                            <p class="geography-point-location" data-map-point-location>{{ $selectedPoint['location'] }}</p>
                            <p class="geography-point-note" data-map-point-note>{{ $selectedPoint['note'] }}</p>
                        </div>
                    </div>
                </div>

                <aside class="geography-panel">
                    <div class="text-panel geography-route-panel" data-map-route-panel>
                        <p class="section-tag">Route Preview</p>
                        <h3 data-map-route-title>Select a destination from PAMO</h3>
                        <p class="map-route-summary" data-map-route-summary>
                            Keep the Protected Area Management Office active, then choose another station marker to preview
                            an approximate route line, estimated distance, and the change in elevation across the park.
                        </p>
                        <div class="map-route-stats">
                            <div class="map-route-stat">
                                <span>Approx. Distance</span>
                                <strong data-map-route-distance>--</strong>
                            </div>
                            <div class="map-route-stat">
                                <span>Height Gain</span>
                                <strong data-map-route-elevation-gain>--</strong>
                            </div>
                            <div class="map-route-stat">
                                <span>High Point</span>
                                <strong data-map-route-highest-point>--</strong>
                            </div>
                        </div>
                        <p class="map-route-caption">
                            Route previews use approximate path lines and elevation between key field points.
                            They do not represent the exact trekking path visitors will follow on the ground.
                        </p>
                        <p class="map-route-warning">
                            Vehicle access ends at the Protected Area Management Office. Beyond PAMO, travel continues
                            on foot and only with park coordination.
                        </p>
                        <button class="button button-secondary button-full map-route-clear" type="button" data-map-route-clear hidden>
                            Clear route and return to PAMO
                        </button>
                    </div>
                </aside>
            </div>
        </section>
    </main>
@endsection

@push('after-body')
    <script type="application/json" data-map-points>@json($mapPoints)</script>
    <script type="application/json" data-map-routes>@json($mapRoutes)</script>
@endpush
