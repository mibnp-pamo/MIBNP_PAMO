@extends('layouts.site')

@php($faunaCoverProfile = collect($faunaProfiles)->first(fn ($profile) => !empty($profile['image'])))
@php($faunaCoverImage = $faunaCoverProfile['image'] ?? \App\Support\PublicSiteContent::optimizedAsset('bckgrndHome/hbg2.jpg'))
@php($floraCoverProfile = collect($floraProfiles)->first(fn ($profile) => !empty($profile['image'])))
@php($floraCoverImage = $floraCoverProfile['image'] ?? \App\Support\PublicSiteContent::optimizedAsset('bckgrndHome/biodiv-reference-2.jpg'))
@php($formatScientificTitle = static fn (string $title): string => \App\Support\ScientificNameFormatter::formatName($title))
@php($formatScientificBody = static fn (string $body, string $title): string => \App\Support\ScientificNameFormatter::formatText($body, [$title]))
@php($formatProfileReference = static function (?string $sourceLabel, ?string $sourceUrl, ?string $sourceName = null): string {
    if (!is_string($sourceLabel) || trim($sourceLabel) === '') {
        return '';
    }

    $safeLabel = e(trim($sourceLabel));
    $resolvedSourceName = is_string($sourceName) ? trim($sourceName) : '';

    if (!is_string($sourceUrl) || trim($sourceUrl) === '') {
        return $resolvedSourceName !== '' ? $safeLabel.' '.e($resolvedSourceName) : $safeLabel;
    }

    if ($resolvedSourceName === '') {
        $resolvedSourceName = (string) parse_url(trim($sourceUrl), PHP_URL_HOST);
        $resolvedSourceName = preg_replace('/^www\./i', '', $resolvedSourceName) ?? '';
    }

    if ($resolvedSourceName === '') {
        $resolvedSourceName = trim($sourceUrl);
    }

    return $safeLabel.' <a href="'.e(trim($sourceUrl)).'" target="_blank" rel="noopener noreferrer">'.e($resolvedSourceName).'</a>';
})
@php($appendProfileReference = static function (string $bodyHtml, string $referenceHtml): string {
    if (trim(strip_tags($referenceHtml)) === '') {
        return $bodyHtml;
    }

    return $bodyHtml.'<br><span class="gallery-reference">'.$referenceHtml.'</span>';
})
@php($faunaGroupDefinitions = [
    'mammals' => [
        'label' => 'Mammals',
        'title' => 'Mammals documented in the park',
        'empty_title' => 'No mammal profiles yet',
        'empty_body' => 'Mammal records will appear here once they are added to the biodiversity collection.',
    ],
    'birds' => [
        'label' => 'Birds',
        'title' => 'Bird records, Documented inside the park',
        'empty_title' => 'No bird profiles yet',
        'empty_body' => 'Bird records will appear here once they are added to the biodiversity collection.',
    ],
    'amphibians' => [
        'label' => 'Amphibians and Lizards',
        'title' => 'Amphibians and Lizards, Documented inside the park',
        'empty_title' => 'No amphibian profiles yet',
        'empty_body' => 'Amphibian profiles will appear here once they are added to the biodiversity collection.',
    ],
])
@php($faunaProfilesByGroup = collect($faunaProfiles)->groupBy(static function (array $profile): string {
    $group = $profile['fauna_group'] ?? 'mammals';

    return $group === 'reptile' ? 'amphibians' : $group;
}))
@php($philippinesRedListStatusLabels = [
    'CR' => 'Critically Endangered',
    'EN' => 'Endangered',
    'VU' => 'Vulnerable',
    'OTS' => 'Other Threatened Species',
    'OWS' => 'Other Wildlife Species',
])


@section('content')
    <main>
        <section class="hero biodiversity-hero" id="top" data-section data-nav-id="overview" data-bg-target="biodiversity-hero">
            <div class="container hero-content">
                <div class="hero-copy">
                    <h1>Wildlife and Plant life that give the park its Beauty</h1>
                    <p class="hero-lead">
                        Beyond scenic ridges and grassland views, Mts. Iglit-Baco rewards visitors with a deeper
                        biodiversity story shaped by endemic wildlife, native trees, and habitat found only in Mindoro.
                    </p>

                </div>
            </div>
        </section>

        <section class="section" id="overview" data-section data-nav-id="overview" data-bg-target="biodiversity-fauna">
            <div class="container visit-layout">
                <div class="text-panel">
                    <p class="section-tag">Overview</p>
                    <h2>Use this page to read the park beyond the scenery</h2>
                    <p>
                        Mts. Iglit-Baco protects one of Mindoro's most important living landscapes. These species
                        profiles help visitors connect the views they see in person with the animals and plant communities
                        that make the park ecologically special.
                    </p>
                    <p>
                        See the wildlife, the Fauna like the <i>Bubalus mindorensis</i> mostly know as the Tamaraw, and the collection of documented birds, then continue into the Flora section to see how forest structure, native
                        trees, vines and vegetation that support the wildlife inside the park.
                    </p>
                    <p>
                        Source acknowledgements: selected bird descriptions on this page are adapted from
                        <a href="https://ebird.org/explore" target="_blank" rel="noopener noreferrer">eBird.org</a>.
                        Some mammal reference summaries use species information from
                        <a href="https://www.wikipedia.org/" target="_blank" rel="noopener noreferrer">Wikipedia</a>.
                        Conservation symbols for applicable fauna entries follow the <i>PHILIPPINE RED LIST</i> under
                        <a href="https://www.scribd.com/document/611636953/DAO-2019-09" target="_blank" rel="noopener noreferrer"><i>DENR Administrative Order 2019-09</i></a>.
                    </p>
                    <div class="biodiversity-legend" aria-label="Philippine Red List symbol legend">
                        <p class="biodiversity-legend-title">Symbol legend</p>
                        <div class="biodiversity-legend-items">
                            <span class="biodiversity-legend-item">
                                <span class="biodiversity-status-badge biodiversity-status-cr">
                                    <span class="biodiversity-status-symbol" aria-hidden="true"></span>
                                    <span>CR</span>
                                </span>
                                <span>Critically Endangered</span>
                            </span>
                            <span class="biodiversity-legend-item">
                                <span class="biodiversity-status-badge biodiversity-status-en">
                                    <span class="biodiversity-status-symbol" aria-hidden="true"></span>
                                    <span>EN</span>
                                </span>
                                <span>Endangered</span>
                            </span>
                            <span class="biodiversity-legend-item">
                                <span class="biodiversity-status-badge biodiversity-status-vu">
                                    <span class="biodiversity-status-symbol" aria-hidden="true"></span>
                                    <span>VU</span>
                                </span>
                                <span>Vulnerable</span>
                            </span>
                            <span class="biodiversity-legend-item">
                                <span class="biodiversity-status-badge biodiversity-status-ots">
                                    <span class="biodiversity-status-symbol" aria-hidden="true"></span>
                                    <span>OTS</span>
                                </span>
                                <span>Other Threatened Species</span>
                            </span>
                            <span class="biodiversity-legend-item">
                                <span class="biodiversity-status-badge biodiversity-status-ows">
                                    <span class="biodiversity-status-symbol" aria-hidden="true"></span>
                                    <span>OWS</span>
                                </span>
                                <span>Other Wildlife Species</span>
                            </span>
                        </div>
                    </div>
                    <a class="button button-secondary" href="{{ route('home', [], false) }}#biodiversity">
                        Return to the homepage
                    </a>
                </div>

                <aside class="visit-sidebar site-side-panel">
                    <p class="eyebrow">What You Will Find</p>
                    <h3>What this biodiversity page helps you explore</h3>
                    <ul>
                        <li>Fauna profiles highlight the Tamaraw and other wildlife linked to Mindoro.</li>
                        <li>Flora profiles introduce native vines, hardwoods, and forest species found in the park.</li>
                        <li>Each entry pairs a visual reference with a short, visitor-friendly description.</li>
                        <li>Bird records add more context to the park's wider biodiversity story.</li>
                    </ul>
                    <a class="button button-primary button-full" href="#fauna">
                        Explore fauna and flora
                    </a>
                </aside>
            </div>
        </section>

        <section
            class="section gallery-highlights-section biodiversity-photo-gutters"
            id="fauna"
            data-section
            data-nav-id="fauna"
            data-bg-target="biodiversity-fauna"
            style="--biodiversity-section-photo: url('{{ $faunaCoverImage }}');"
        >
            <div class="container gallery-highlights-layout">
                <div class="gallery-group-stack biodiversity-group-stack">
                    <section class="gallery-group-shell biodiversity-group-shell">
                        <button
                            class="gallery-frame-card gallery-group-card gallery-group-trigger biodiversity-group-trigger"
                            type="button"
                            data-gallery-group-toggle="fauna"
                            aria-controls="gallery-group-panel-fauna"
                            aria-expanded="false"
                            aria-label="Open Fauna photo group with {{ count($faunaProfiles) }} profiles"
                        >
                            <div class="biodiversity-group-art" style="background-image: url('{{ $faunaCoverImage }}');" aria-hidden="true"></div>
                            <div class="biodiversity-group-copy">

                                <h2 class="biodiversity-group-title">Wildlife tied to Mindoro's Highland habitats</h2>

                            </div>
                            <div class="gallery-group-overlay biodiversity-group-overlay" aria-hidden="true">
                                <h3 class="gallery-group-hover-title">Fauna</h3>
                            </div>
                        </button>

                        <div
                            class="gallery-group-panel biodiversity-group-panel"
                            id="gallery-group-panel-fauna"
                            data-gallery-group-panel="fauna"
                            hidden
                        >
                            <div class="biodiversity-fauna-groups">
                                @foreach ($faunaGroupDefinitions as $groupId => $group)
                                    @php($groupProfiles = $faunaProfilesByGroup->get($groupId, collect()))
                                    <section class="biodiversity-fauna-group" aria-label="{{ $group['label'] }}">
                                        <div class="biodiversity-fauna-group-header">
                                            <p class="section-tag">{{ $group['label'] }}</p>
                                            <h3>{{ $group['title'] }}</h3>
                                        </div>

                                        @if ($groupProfiles->isNotEmpty())
                                            <div class="gallery-highlight-grid">
                                                @foreach ($groupProfiles as $profile)
                                                    @php($profileTitleHtml = $formatScientificTitle($profile['title']))
                                                    @php($profileBodyHtml = $formatScientificBody($profile['body'], $profile['title']))
                                                    @php($profileReferenceHtml = $formatProfileReference($profile['source_label'] ?? null, $profile['source_url'] ?? null, $profile['source_name'] ?? null))
                                                    @php($profileLightboxDescriptionHtml = $appendProfileReference($profileBodyHtml, $profileReferenceHtml))
                                                    @php($profileScientificName = $profile['scientific_name'] ?? null)
                                                    @php($philippinesRedListStatus = $profile['philippines_red_list_status'] ?? null)
                                                    @php($philippinesRedListStatusLabel = $philippinesRedListStatusLabels[$philippinesRedListStatus] ?? null)
                                                    @php($philippinesRedListStatusSymbol = $philippinesRedListStatusSymbols[$philippinesRedListStatus] ?? null)
                                                    @php($showScientificName = is_string($profileScientificName) && trim($profileScientificName) !== '' && strcasecmp(trim($profileScientificName), trim($profile['title'])) !== 0)
                                                    @php($profileLightboxTitleHtml = $showScientificName ? $profileTitleHtml.'<span class="lightbox-scientific-name"><em>'.e($profileScientificName).'</em></span>' : $profileTitleHtml)
                                                    @php($additionalImages = collect($profile['additional_images'] ?? [])->filter(fn ($image) => is_array($image) && !empty($image['image'])))
                                                    <article
                                                        class="gallery-highlight-card biodiversity-fauna-card{{ empty($profile['image']) ? ' is-empty' : '' }}{{ $additionalImages->isNotEmpty() ? ' has-multiple-images' : '' }}"
                                                        @unless (empty($profile['image']))
                                                            data-lightbox-item
                                                            data-lightbox-group="fauna"
                                                            data-lightbox-src="{{ $profile['image'] }}"
                                                            data-lightbox-alt="{{ $profile['alt'] }}"
                                                            data-lightbox-title="{{ $profile['title'] }}"
                                                            data-lightbox-title-html="{{ $profileLightboxTitleHtml }}"
                                                            data-lightbox-description="{{ $profile['body'] }}"
                                                            data-lightbox-description-html="{{ $profileLightboxDescriptionHtml }}"
                                                            data-lightbox-meta="{{ $profile['meta'] }}"
                                                            data-lightbox-credit="{{ $profile['credit'] ?? '' }}"
                                                            role="button"
                                                            tabindex="0"
                                                            aria-label="Open {{ $additionalImages->isNotEmpty() ? ($additionalImages->count() + 1).' photos of ' : '' }}{{ $profile['title'] }}"
                                                        @endunless
                                                    >
                                                        <div class="gallery-card-image">
                                                            @unless (empty($profile['image']))
                                                                <img src="{{ $profile['image'] }}" alt="{{ $profile['alt'] }}" loading="lazy" decoding="async">
                                                            @endunless
                                                            @if ($additionalImages->isNotEmpty())
                                                                <span class="biodiversity-photo-count" aria-label="{{ $additionalImages->count() + 1 }} photos">
                                                                    {{ $additionalImages->count() + 1 }} photos
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="gallery-highlight-copy">
                                                            <div class="biodiversity-card-meta-row">
                                                                <span class="gallery-frame-meta">{{ $profile['meta'] }}</span>
                                                                @if ($philippinesRedListStatusLabel !== null)
                                                                    <span
                                                                        class="biodiversity-status-badge biodiversity-status-{{ strtolower($philippinesRedListStatus) }}"
                                                                        title="Philippine Red List: {{ $philippinesRedListStatusLabel }}"
                                                                        aria-label="Philippine Red List: {{ $philippinesRedListStatusLabel }}"
                                                                    >
                                                                        <span class="biodiversity-status-symbol" aria-hidden="true">{{ $philippinesRedListStatusSymbol }}</span>
                                                                        <span>{{ $philippinesRedListStatus }}</span>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <h3>{!! $profileTitleHtml !!}</h3>
                                                            @if ($showScientificName)
                                                                <p class="gallery-scientific-name"><em>{{ $profileScientificName }}</em></p>
                                                            @endif
                                                            <p>{!! $profileBodyHtml !!}</p>
                                                            @if ($profileReferenceHtml !== '')
                                                                <p class="gallery-reference">{!! $profileReferenceHtml !!}</p>
                                                            @endif
                                                        </div>
                                                    </article>
                                                    @foreach ($additionalImages as $additionalImage)
                                                        <span
                                                            hidden
                                                            data-lightbox-item
                                                            data-lightbox-group="fauna"
                                                            data-lightbox-src="{{ $additionalImage['image'] }}"
                                                            data-lightbox-alt="{{ $additionalImage['alt'] ?? $profile['alt'] }}"
                                                            data-lightbox-title="{{ $profile['title'] }}"
                                                            data-lightbox-title-html="{{ $profileLightboxTitleHtml }}"
                                                            data-lightbox-description="{{ $profile['body'] }}"
                                                            data-lightbox-description-html="{{ $profileLightboxDescriptionHtml }}"
                                                            data-lightbox-meta="{{ $profile['meta'] }}"
                                                            data-lightbox-credit="{{ $additionalImage['credit'] ?? ($profile['credit'] ?? '') }}"
                                                        ></span>
                                                    @endforeach
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="empty-state-card biodiversity-fauna-empty">
                                                <h3>{{ $group['empty_title'] }}</h3>
                                                <p>{{ $group['empty_body'] }}</p>
                                            </div>
                                        @endif
                                    </section>
                                @endforeach
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </section>

        <section
            class="section gallery-collection-section biodiversity-photo-gutters"
            id="flora"
            data-section
            data-nav-id="flora"
            data-bg-target="biodiversity-flora"
            style="--biodiversity-section-photo: url('{{ $floraCoverImage }}');"
        >
            <div class="container gallery-collection-layout">
                <div class="gallery-group-stack biodiversity-group-stack">
                    <section class="gallery-group-shell biodiversity-group-shell">
                        <button
                            class="gallery-frame-card gallery-group-card gallery-group-trigger biodiversity-group-trigger"
                            type="button"
                            data-gallery-group-toggle="flora"
                            aria-controls="gallery-group-panel-flora"
                            aria-expanded="false"
                            aria-label="Open Flora photo group with {{ count($floraProfiles) }} profiles"
                        >
                            <div class="biodiversity-group-art" style="background-image: url('{{ $floraCoverImage }}');" aria-hidden="true"></div>
                            <div class="biodiversity-group-copy">

                                <h2 class="biodiversity-group-title">Native trees, vines, and vegetation</h2>

                            </div>
                            <div class="gallery-group-overlay biodiversity-group-overlay" aria-hidden="true">
                                <h3 class="gallery-group-hover-title">Flora</h3>
                            </div>
                        </button>

                        <div
                            class="gallery-group-panel biodiversity-group-panel"
                            id="gallery-group-panel-flora"
                            data-gallery-group-panel="flora"
                            hidden
                        >
                            <div class="gallery-highlight-grid">
                                @foreach ($floraProfiles as $profile)
                                    @php($profileTitleHtml = $formatScientificTitle($profile['title']))
                                    @php($profileBodyHtml = $formatScientificBody($profile['body'], $profile['title']))
                                    @php($profileReferenceHtml = $formatProfileReference($profile['source_label'] ?? null, $profile['source_url'] ?? null, $profile['source_name'] ?? null))
                                    @php($profileLightboxDescriptionHtml = $appendProfileReference($profileBodyHtml, $profileReferenceHtml))
                                    @php($profileScientificName = $profile['scientific_name'] ?? null)
                                    @php($philippinesRedListStatus = $profile['philippines_red_list_status'] ?? null)
                                    @php($philippinesRedListStatusLabel = $philippinesRedListStatusLabels[$philippinesRedListStatus] ?? null)
                                    @php($philippinesRedListStatusSymbol = $philippinesRedListStatusSymbols[$philippinesRedListStatus] ?? null)
                                    @php($showScientificName = is_string($profileScientificName) && trim($profileScientificName) !== '' && strcasecmp(trim($profileScientificName), trim($profile['title'])) !== 0)
                                    @php($profileLightboxTitleHtml = $showScientificName ? $profileTitleHtml.'<span class="lightbox-scientific-name"><em>'.e($profileScientificName).'</em></span>' : $profileTitleHtml)
                                    <article
                                        class="gallery-highlight-card biodiversity-flora-card{{ empty($profile['image']) ? ' is-empty' : '' }}"
                                        @unless (empty($profile['image']))
                                            data-lightbox-item
                                            data-lightbox-group="flora"
                                            data-lightbox-src="{{ $profile['image'] }}"
                                            data-lightbox-alt="{{ $profile['alt'] }}"
                                            data-lightbox-title="{{ $profile['title'] }}"
                                            data-lightbox-title-html="{{ $profileLightboxTitleHtml }}"
                                            data-lightbox-description="{{ $profile['body'] }}"
                                            data-lightbox-description-html="{{ $profileLightboxDescriptionHtml }}"
                                            data-lightbox-meta="{{ $profile['meta'] }}"
                                            data-lightbox-credit="{{ $profile['credit'] ?? '' }}"
                                            role="button"
                                            tabindex="0"
                                            aria-label="Open {{ $profile['title'] }}"
                                        @endunless
                                    >
                                        <div class="gallery-card-image">
                                            @unless (empty($profile['image']))
                                                <img src="{{ $profile['image'] }}" alt="{{ $profile['alt'] }}" loading="lazy" decoding="async">
                                            @endunless
                                        </div>
                                        <div class="gallery-highlight-copy">
                                            <div class="biodiversity-card-meta-row">
                                                <span class="gallery-frame-meta">{{ $profile['meta'] }}</span>
                                                @if ($philippinesRedListStatusLabel !== null)
                                                    <span
                                                        class="biodiversity-status-badge biodiversity-status-{{ strtolower($philippinesRedListStatus) }}"
                                                        title="Philippine Red List: {{ $philippinesRedListStatusLabel }}"
                                                        aria-label="Philippine Red List: {{ $philippinesRedListStatusLabel }}"
                                                    >
                                                        <span class="biodiversity-status-symbol" aria-hidden="true">{{ $philippinesRedListStatusSymbol }}</span>
                                                        <span>{{ $philippinesRedListStatus }}</span>
                                                    </span>
                                                @endif
                                            </div>
                                            <h3>{!! $profileTitleHtml !!}</h3>
                                            @if ($showScientificName)
                                                <p class="gallery-scientific-name"><em>{{ $profileScientificName }}</em></p>
                                            @endif
                                            <p>{!! $profileBodyHtml !!}</p>
                                            @if ($profileReferenceHtml !== '')
                                                <p class="gallery-reference">{!! $profileReferenceHtml !!}</p>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </section>

        @foreach ($archiveSections as $section)
            @continue(($section['id'] ?? null) === 'invertebrate-records')
            @php($archiveCoverImage = $section['profiles'][0]['image'] ?? \App\Support\PublicSiteContent::optimizedAsset('bckgrndHome/hbg2.jpg'))

            <section
                class="section gallery-collection-section biodiversity-archive-section biodiversity-photo-gutters"
                id="{{ $section['id'] }}"
                data-section
                data-bg-target="{{ $section['bg_target'] }}"
                style="--biodiversity-section-photo: url('{{ $archiveCoverImage }}');"
            >
                <div class="container gallery-collection-layout">
                    <div class="gallery-group-stack biodiversity-group-stack">
                        <section class="gallery-group-shell biodiversity-group-shell">
                            <button
                                class="gallery-frame-card gallery-group-card gallery-group-trigger biodiversity-group-trigger"
                                type="button"
                                data-gallery-group-toggle="{{ $section['id'] }}"
                                aria-controls="gallery-group-panel-{{ $section['id'] }}"
                                aria-expanded="false"
                                aria-label="Open {{ $section['tag'] }} photo group with {{ $section['count'] }} records"
                            >
                                <div class="biodiversity-group-art" style="background-image: url('{{ $archiveCoverImage }}');" aria-hidden="true"></div>
                                <div class="biodiversity-group-copy">

                                    <h2 class="biodiversity-group-title">{{ $section['title'] }}</h2>

                                </div>
                                <div class="gallery-group-overlay biodiversity-group-overlay" aria-hidden="true">
                                    <h3 class="gallery-group-hover-title">{{ $section['tag'] }}</h3>
                                </div>
                            </button>

                            <div
                                class="gallery-group-panel biodiversity-group-panel"
                                id="gallery-group-panel-{{ $section['id'] }}"
                                data-gallery-group-panel="{{ $section['id'] }}"
                                hidden
                            >
                                <div class="gallery-collection-grid biodiversity-archive-grid">
                                    @foreach ($section['profiles'] as $profile)
                                        @php($profileTitleHtml = $formatScientificTitle($profile['title']))
                                        @php($profileBodyHtml = $formatScientificBody($profile['body'], $profile['title']))
                                        @php($profileReferenceHtml = $formatProfileReference($profile['source_label'] ?? null, $profile['source_url'] ?? null, $profile['source_name'] ?? null))
                                        @php($profileLightboxDescriptionHtml = $appendProfileReference($profileBodyHtml, $profileReferenceHtml))
                                        @php($profileScientificName = $profile['scientific_name'] ?? null)
                                        @php($philippinesRedListStatus = $profile['philippines_red_list_status'] ?? null)
                                        @php($philippinesRedListStatusLabel = $philippinesRedListStatusLabels[$philippinesRedListStatus] ?? null)
                                        @php($philippinesRedListStatusSymbol = $philippinesRedListStatusSymbols[$philippinesRedListStatus] ?? null)
                                        @php($showScientificName = is_string($profileScientificName) && trim($profileScientificName) !== '' && strcasecmp(trim($profileScientificName), trim($profile['title'])) !== 0)
                                        @php($profileLightboxTitleHtml = $showScientificName ? $profileTitleHtml.'<span class="lightbox-scientific-name"><em>'.e($profileScientificName).'</em></span>' : $profileTitleHtml)
                                        <figure
                                            class="gallery-frame-card biodiversity-archive-card"
                                            data-lightbox-item
                                            data-lightbox-group="{{ $section['id'] }}"
                                            data-lightbox-src="{{ $profile['image'] }}"
                                            data-lightbox-alt="{{ $profile['alt'] }}"
                                            data-lightbox-title="{{ $profile['title'] }}"
                                            data-lightbox-title-html="{{ $profileLightboxTitleHtml }}"
                                            data-lightbox-description="{{ $profile['body'] }}"
                                            data-lightbox-description-html="{{ $profileLightboxDescriptionHtml }}"
                                            data-lightbox-meta="{{ $profile['meta'] }}"
                                            data-lightbox-credit="{{ $profile['credit'] ?? '' }}"
                                            role="button"
                                            tabindex="0"
                                            aria-label="Open {{ $profile['title'] }}"
                                        >
                                            <div class="gallery-card-image">
                                                <img src="{{ $profile['image'] }}" alt="{{ $profile['alt'] }}" loading="lazy" decoding="async">
                                            </div>
                                            <figcaption class="gallery-frame-copy">
                                                <div class="biodiversity-card-meta-row">
                                                    <span class="gallery-frame-meta">{{ $profile['meta'] }}</span>
                                                    @if ($philippinesRedListStatusLabel !== null)
                                                        <span
                                                            class="biodiversity-status-badge biodiversity-status-{{ strtolower($philippinesRedListStatus) }}"
                                                            title="Philippine Red List: {{ $philippinesRedListStatusLabel }}"
                                                            aria-label="Philippine Red List: {{ $philippinesRedListStatusLabel }}"
                                                        >
                                                            <span class="biodiversity-status-symbol" aria-hidden="true">{{ $philippinesRedListStatusSymbol }}</span>
                                                            <span>{{ $philippinesRedListStatus }}</span>
                                                        </span>
                                                    @endif
                                                </div>
                                                <h3>{!! $profileTitleHtml !!}</h3>
                                                @if ($showScientificName)
                                                    <p class="gallery-scientific-name"><em>{{ $profileScientificName }}</em></p>
                                                @endif
                                                <p>{!! $profileBodyHtml !!}</p>
                                                @if ($profileReferenceHtml !== '')
                                                    <p class="gallery-reference">{!! $profileReferenceHtml !!}</p>
                                                @endif
                                            </figcaption>
                                        </figure>
                                    @endforeach
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </section>
        @endforeach

    </main>
@endsection

@push('after-body')
    @include('partials.lightbox')
@endpush
