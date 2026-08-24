@php
    $galleryCurrent = request()->routeIs('gallery');

    $navLinks = [
        ['href' => route('home', [], false), 'label' => 'Home'],
        [
            'href' => route('geography', [], false),
            'label' => 'Geography',
            'current' => request()->routeIs('geography'),
        ],
        [
            'href' => route('biodiversity', [], false),
            'label' => 'Biodiversity',
            'children' => [
                ['href' => route('biodiversity', [], false) . '#fauna', 'label' => 'Fauna'],
                ['href' => route('biodiversity', [], false) . '#flora', 'label' => 'Flora'],
            ],
            'current' => request()->routeIs('biodiversity'),
        ],
        [
            'href' => route('gallery', [], false),
            'label' => 'Gallery',
            'children' => [
                ['href' => route('gallery', [], false) . '#collection', 'label' => 'Collection'],
            ],
            'current' => $galleryCurrent,
        ],
        [
            'href' => route('office-profile', [], false),
            'label' => 'Office Profile',
            'children' => [
                ['href' => route('partners', [], false), 'label' => 'Partners'],
                ['href' => route('partners', [], false) . '#news', 'label' => 'News Corner'],
            ],
            'current' => request()->routeIs('partners') || request()->routeIs('office-profile'),
        ],

    ];
@endphp

<nav id="site-nav" class="site-nav" data-site-nav aria-label="Primary navigation">
    <div class="site-nav-mobile-head">
        <a class="site-nav-mobile-brand" href="{{ $brandHref ?? route('home', [], false) }}" aria-label="Go to homepage">
            <span class="site-nav-mobile-brand-mark">
                <img
                    src="{{ \App\Support\PublicSiteContent::optimizedAsset('logo.png') }}"
                    alt="Mts. Iglit-Baco Natural Park logo"
                >
            </span>
            <span class="site-nav-mobile-brand-copy">
                <strong>Mts. Iglit-Baco</strong>
                <span>Natural Park</span>
            </span>
        </a>

        <p class="site-nav-mobile-label">Main Menu</p>
    </div>

    @include('partials.site-nav', ['navLinks' => $navLinks])
</nav>
