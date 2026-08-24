@extends('layouts.site')

@php($formatScientificTitle = static fn (string $title): string => \App\Support\ScientificNameFormatter::formatName($title))
@php($formatScientificBody = static fn (string $body, string $title): string => \App\Support\ScientificNameFormatter::formatText($body, [$title]))

@section('content')
    <main>
        <section class="hero" id="top" data-section data-bg-target="hero-scene">
            <div class="container hero-content">
                <div class="hero-copy">
                    <h1>Invigorating Iglit - The Tamaraw Frontier</h1>
                    <p class="hero-lead">
                        Mts. Iglit-Baco Natural Park is one of Mindoro's great upland landscapes, where sweeping
                        ridges, open grasslands, forest edges, and a living conservation story come together in one
                        travel destination.
                    </p>
                    <div class="hero-actions">
                        <a class="button button-primary" href="#story">Explore the park</a>
                        <a class="button button-secondary" href="#visit">Plan a visit</a>
                    </div>
                    <div class="hero-facts">
                        <div>
                            <span class="hero-fact-label">Area</span>
                            <strong>106,655.62 hectares</strong>
                        </div>
                        <div>
                            <span class="hero-fact-label">Elevation</span>
                            <strong>600-2,500 masl</strong>
                        </div>
                        <div>
                            <span class="hero-fact-label">Region</span>
                            <strong>Mindoro, Philippines</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section story-section" id="story" data-section data-bg-target="story-scene">
            <div class="container story-grid">
                <div class="story-copy text-panel">

                    <h2>A Mountainous landscape shaped by ridges, grasslands, forest edges, and living culture</h2>
                    <p>
                        In the interior of Mindoro, the park stretches across rugged uplands that support watersheds,
                        endemic wildlife, scenic grassland routes, and the continued importance of Ethnic communities
                        connected to the land.
                    </p>
                </div>

                <div class="story-gallery">
                    @foreach ($galleryCards as $card)
                        <article class="gallery-card">
                            <img src="{{ $card['image'] }}" alt="{{ $card['title'] }}" loading="lazy" decoding="async">
                            <div class="gallery-card-copy">
                                <h3>{{ $card['title'] }}</h3>
                                <p>{{ $card['body'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="section geography-section" id="geography" data-section data-bg-target="map-scene">
            <div class="container">
                <div class="section-heading compact-heading text-panel geography-home-heading">
                    <div>
                        <p class="section-tag">Geography</p>
                        <h2>Park Geography/Location</h2>
                    </div>
                </div>

                <figure class="home-map-photo-holder">
                    <img
                        src="{{ \App\Support\PublicSiteContent::optimizedAsset('home-assets/Location Map of MIBNP.png') }}"
                        alt="Location map of Mounts Iglit-Baco Natural Park"
                        loading="lazy"
                        decoding="async"
                    >
                </figure>
            </div>
        </section>

        <section class="section biodiversity-section" id="biodiversity" data-section data-bg-target="wildlife-scene">
            <div class="container biodiversity-grid">
                <div class="biodiversity-image">
                    <img
                        src="{{ \App\Support\PublicSiteContent::optimizedAsset('bckgrndHome/tamaraw.JPG') }}"
                        alt="Tamaraw in Mts. Iglit-Baco Natural Park"
                        loading="lazy"
                    >
                    <div class="biodiversity-caption">
                        <span class="biodiversity-species">{!! $formatScientificTitle('TAMARAW') !!}</span>
                        <strong class="biodiversity-status">Critically Endangered</strong>
                    </div>
                </div>

                <div class="biodiversity-copy">

                    <h2>The Tamaraw gives the park its strongest wildlife identity.</h2>


                    <div class="biodiversity-cards">
                        @foreach ($tamarawFeatures as $card)
                            @php($cardTitleHtml = $formatScientificTitle($card['title']))
                            @php($cardBodyHtml = $formatScientificBody($card['body'], $card['title']))
                            <article class="biodiversity-card">
                                <p class="eyebrow">{{ $card['kicker'] }}</p>
                                <h3>{!! $cardTitleHtml !!}</h3>
                                <p>{!! $cardBodyHtml !!}</p>
                            </article>
                        @endforeach
                    </div>

                    <div class="hero-actions">
                        <a class="button button-primary" href="{{ route('biodiversity', [], false) }}">
                            View Biodiversity Archive
                        </a>
                        <a class="button button-secondary" href="{{ route('gallery', [], false) }}">
                            View Gallery Photos
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="section visit-section" id="visit" data-section data-bg-target="visit-scene">
            <div class="container visit-layout visit-layout-home">
                <div class="visit-main">
                    <div class="section-heading text-panel visit-heading">
                        <div>
                            <p class="section-tag">Visitation Guide</p>
                            <h2>Prepare for a responsible trip into the park</h2>
                        </div>
                        <p>
                            Conditions in the uplands are beautiful but demanding. A little planning goes a long way
                            toward making the visit safer, smoother, and more respectful to the landscape.
                        </p>
                    </div>

                    <div class="visit-steps">
                        @foreach ($visitSteps as $step)
                            <article class="visit-step">
                                <div class="visit-number">{{ $step['number'] }}</div>
                                <div>
                                    <h3>{{ $step['title'] }}</h3>
                                    <p>{{ $step['body'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                <aside class="visit-sidebar site-side-panel">
                    <p class="eyebrow">Before You Go</p>
                    <h3>Important field reminders that makes the visit better</h3>
                    <ul>
                        <li>Stay on marked routes to reduce <i>Erosion</i> and <i>Habitat Disturbance</i>.</li>
                        <li>Bring weather-ready gear, enough water, and basic first aid supplies.</li>
                        <li>Use respectful photography practices in local communities.</li>
                        <li>Coordinate research, Hiking, or Wildlife-viewing needs before arrival.</li>
                    </ul>

                </aside>
            </div>

            <div class="container visitation-request-panel" id="visitation-request">
                <div class="visitation-request-intro">
                    <p class="section-tag">Request Coordination</p>
                    <h2>Send your visitation request to the PAMO office</h2>
                </div>

                <div class="visitation-request-card">
                    @if (session('visitation_status'))
                        <div class="form-status form-status-success" role="status">
                            {{ session('visitation_status') }}
                        </div>
                    @endif

                    @error('delivery')
                        <div class="form-status form-status-error" role="alert">
                            {{ $message }}
                        </div>
                    @enderror

                    @if ($errors->any() && !$errors->has('delivery'))
                        <div class="form-status form-status-error" role="alert">
                            Please review the highlighted fields and submit the request again.
                        </div>
                    @endif

                    <form
                        class="visitation-request-form"
                        action="{{ route('visitation-requests.store', [], false) }}"
                        method="POST"
                        enctype="multipart/form-data"
                    >
                        @csrf

                        <div class="visitation-form-grid">
                            <label class="form-field">
                                <span>Full name <strong aria-hidden="true">*</strong></span>
                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    maxlength="255"
                                    autocomplete="name"
                                    required
                                    @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                                >
                                @error('name')
                                    <small class="form-field-error" id="name-error">{{ $message }}</small>
                                @enderror
                            </label>

                            <label class="form-field">
                                <span>Email address <strong aria-hidden="true">*</strong></span>
                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    maxlength="255"
                                    autocomplete="email"
                                    required
                                    @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                                >
                                @error('email')
                                    <small class="form-field-error" id="email-error">{{ $message }}</small>
                                @enderror
                            </label>

                            <label class="form-field">
                                <span>Phone number</span>
                                <input
                                    type="tel"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    maxlength="50"
                                    autocomplete="tel"
                                    @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror
                                >
                                @error('phone')
                                    <small class="form-field-error" id="phone-error">{{ $message }}</small>
                                @enderror
                            </label>

                            <label class="form-field">
                                <span>Organization</span>
                                <input
                                    type="text"
                                    name="organization"
                                    value="{{ old('organization') }}"
                                    maxlength="255"
                                    autocomplete="organization"
                                    @error('organization') aria-invalid="true" aria-describedby="organization-error" @enderror
                                >
                                @error('organization')
                                    <small class="form-field-error" id="organization-error">{{ $message }}</small>
                                @enderror
                            </label>

                            <label class="form-field">
                                <span>Activity <strong aria-hidden="true">*</strong></span>
                                <select
                                    name="activity"
                                    required
                                    @error('activity') aria-invalid="true" aria-describedby="activity-error" @enderror
                                >
                                    <option value="">Select an activity</option>
                                    <option value="hiking" @selected(old('activity') === 'hiking')>Hiking or trekking</option>
                                    <option value="wildlife-viewing" @selected(old('activity') === 'wildlife-viewing')>Wildlife viewing</option>
                                    <option value="research" @selected(old('activity') === 'research')>Research</option>
                                    <option value="education" @selected(old('activity') === 'education')>Educational visit</option>
                                    <option value="photography" @selected(old('activity') === 'photography')>Photography or filming</option>
                                    <option value="coordination" @selected(old('activity') === 'coordination')>Community or official coordination</option>
                                    <option value="other" @selected(old('activity') === 'other')>Other</option>
                                </select>
                                @error('activity')
                                    <small class="form-field-error" id="activity-error">{{ $message }}</small>
                                @enderror
                            </label>

                            <label class="form-field">
                                <span>Intended visit date <strong aria-hidden="true">*</strong></span>
                                <input
                                    type="date"
                                    name="intended_date"
                                    value="{{ old('intended_date') }}"
                                    min="{{ now()->toDateString() }}"
                                    required
                                    @error('intended_date') aria-invalid="true" aria-describedby="intended-date-error" @enderror
                                >
                                @error('intended_date')
                                    <small class="form-field-error" id="intended-date-error">{{ $message }}</small>
                                @enderror
                            </label>

                            <label class="form-field">
                                <span>Number of visitors <strong aria-hidden="true">*</strong></span>
                                <input
                                    type="number"
                                    name="visitor_count"
                                    value="{{ old('visitor_count', 1) }}"
                                    min="1"
                                    max="50"
                                    inputmode="numeric"
                                    required
                                    @error('visitor_count') aria-invalid="true" aria-describedby="visitor-count-help visitor-count-error" @else aria-describedby="visitor-count-help" @enderror
                                >
                                <small class="form-field-help" id="visitor-count-help">Maximum 50 visitors per request.</small>
                                @error('visitor_count')
                                    <small class="form-field-error" id="visitor-count-error">{{ $message }}</small>
                                @enderror
                            </label>

                            <label class="form-field">
                                <span>Document type</span>
                                <select
                                    name="document_type"
                                    @error('document_type') aria-invalid="true" aria-describedby="document-type-error" @enderror
                                >
                                    <option value="">Select when attaching a document</option>
                                    <option value="request-letter" @selected(old('document_type') === 'request-letter')>Request letter</option>
                                    <option value="itinerary" @selected(old('document_type') === 'itinerary')>Visitor itinerary</option>
                                    <option value="proposal" @selected(old('document_type') === 'proposal')>Research or activity proposal</option>
                                    <option value="endorsement" @selected(old('document_type') === 'endorsement')>Endorsement or permit</option>
                                    <option value="other" @selected(old('document_type') === 'other')>Other supporting document</option>
                                </select>
                                @error('document_type')
                                    <small class="form-field-error" id="document-type-error">{{ $message }}</small>
                                @enderror
                            </label>

                            <label class="form-field form-field-full">
                                <span>Supporting document</span>
                                <input
                                    type="file"
                                    name="document"
                                    accept=".pdf,application/pdf"
                                    @error('document') aria-invalid="true" aria-describedby="document-help document-error" @else aria-describedby="document-help" @enderror
                                >
                                <small class="form-field-help" id="document-help">
                                    Optional. PDF only; maximum file size 5 MB. PDFs containing scripts,
                                    embedded files, or launch actions are rejected.
                                </small>
                                @error('document')
                                    <small class="form-field-error" id="document-error">{{ $message }}</small>
                                @enderror
                            </label>

                            <label class="form-field form-field-full">
                                <span>Additional information</span>
                                <textarea
                                    name="message"
                                    rows="5"
                                    maxlength="3000"
                                    @error('message') aria-invalid="true" aria-describedby="message-error" @enderror
                                >{{ old('message') }}</textarea>
                                @error('message')
                                    <small class="form-field-error" id="message-error">{{ $message }}</small>
                                @enderror
                            </label>
                        </div>

                        <div class="visitation-honeypot" aria-hidden="true">
                            <label>
                                Website
                                <input type="text" name="website" value="" tabindex="-1" autocomplete="off">
                            </label>
                        </div>

                        <label class="form-consent">
                            <input
                                type="checkbox"
                                name="privacy_consent"
                                value="1"
                                required
                                @checked(old('privacy_consent'))
                                @error('privacy_consent') aria-invalid="true" aria-describedby="privacy-consent-error" @enderror
                            >
                            <span>
                                I consent to the PAMO office using these details and the attached document to process
                                this visitation request as described in the
                                <a href="{{ route('privacy', [], false) }}" target="_blank" rel="noopener noreferrer">privacy notice</a>.
                            </span>
                        </label>
                        @error('privacy_consent')
                            <small class="form-field-error" id="privacy-consent-error">{{ $message }}</small>
                        @enderror

                        <div class="visitation-form-actions">
                            <button class="button button-primary" type="submit">Send visitation request</button>
                            <p>
                                If the form is unavailable, email
                                <a href="mailto:r4b.mibnp@denr.gov.ph">r4b.mibnp@denr.gov.ph</a>.
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section class="section impact-section" id="impact" data-section data-bg-target="impact-scene">
            <div class="container">
                <div class="section-heading compact-heading impact-heading">
                    <div>

                        <h2>A few numbers that frame the experience</h2>
                    </div>
                </div>

                <div class="impact-grid">
                    @foreach ($impactStats as $stat)
                        <article class="impact-card">
                            <strong
                                class="impact-value"
                                data-countup="{{ $stat['value'] }}"
                                data-decimals="{{ $stat['decimals'] }}"
                                data-suffix="{{ $stat['suffix'] }}"
                            >
                                0
                            </strong>
                            <p>{{ $stat['label'] }}</p>
                        </article>
                    @endforeach
                </div>

            </div>
        </section>
    </main>
@endsection
