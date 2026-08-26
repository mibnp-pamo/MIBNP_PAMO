@php
    $pageTitle = $pageTitle ?? 'Mts. Iglit-Baco Natural Park';
    $metaDescription = $metaDescription ?? 'Tourism and visitor information for Mts. Iglit-Baco Natural Park.';
    $themeColor = $themeColor ?? '#17392c';
    $bodyClass = trim($bodyClass ?? '');
    $backgroundSlides = $backgroundSlides ?? [];
    $headerTheme = $headerTheme ?? 'dark';
    $footerBgTarget = $footerBgTarget ?? null;
    $brandHref = $brandHref ?? route('home', [], false);
    $includeLeaflet = $includeLeaflet ?? false;
    $canonicalUrl = $canonicalUrl ?? url()->current();
    $socialImage = $socialImage ?? \App\Support\PublicSiteContent::optimizedAsset('bckgrndHome/hbg0.jpg');
    $showSiteNewsSidebar = $showSiteNewsSidebar ?? $bodyClass !== 'geography-page';
    $siteNewsItems = $siteNewsItems ?? [];
@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $pageTitle }}</title>
        <meta name="description" content="{{ $metaDescription }}">
        <meta name="theme-color" content="{{ $themeColor }}">
        <link rel="canonical" href="{{ $canonicalUrl }}">
        <link rel="icon" href="{{ asset('generated/optimized/logo.webp') }}" type="image/webp">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="Mounts Iglit-Baco Natural Park">
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $metaDescription }}">
        <meta property="og:url" content="{{ $canonicalUrl }}">
        <meta property="og:image" content="{{ $socialImage }}">
        <meta property="og:image:alt" content="Mounts Iglit-Baco Natural Park landscape">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $pageTitle }}">
        <meta name="twitter:description" content="{{ $metaDescription }}">
        <meta name="twitter:image" content="{{ $socialImage }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap"
            rel="stylesheet"
        >
        @if ($includeLeaflet)
            <link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}">
        @endif
        <link rel="stylesheet" href="{{ asset('css/site.css') }}">
        @stack('head')
        @if ($includeLeaflet)
            <script src="{{ asset('vendor/leaflet/leaflet.js') }}" defer></script>
        @endif
        <script src="{{ asset('js/site.js') }}" defer></script>
    </head>
    <body @if ($bodyClass !== '') class="{{ $bodyClass }}" @endif>
        <a class="skip-link" href="#page-content">Skip to main content</a>

        <div class="site-background" aria-hidden="true">
            <div class="site-background-slides">
                @foreach ($backgroundSlides as $slide)
                    <div
                        class="site-background-slide {{ $loop->first ? 'is-active' : '' }}"
                        data-bg-slide="{{ $slide['id'] }}"
                        style="background-image: url('{{ $slide['image'] }}');"
                    ></div>
                @endforeach
            </div>
            <div class="site-background-scrim"></div>
        </div>

        <div class="page-shell">
            <header class="site-header" data-header data-theme="{{ $headerTheme }}">
                <div class="container nav-shell">
                    <a class="brand" href="{{ $brandHref }}" aria-label="Go to homepage">
                        <span class="brand-mark">
                            <img
                                src="{{ \App\Support\PublicSiteContent::optimizedAsset('logo.png') }}"
                                alt="Mts. Iglit-Baco Natural Park logo"
                            >
                        </span>
                        <span class="brand-copy">
                            <strong>Mts. Iglit-Baco</strong>
                            <span>Natural Park</span>
                        </span>
                    </a>

                    <button
                        class="menu-toggle"
                        type="button"
                        aria-controls="site-nav"
                        aria-expanded="false"
                        aria-label="Open menu"
                        data-menu-toggle
                    >
                        <span></span>
                        <span></span>
                    </button>

                    @include('partials.main-site-nav', ['brandHref' => $brandHref])
                </div>
            </header>

            <div class="site-nav-backdrop" data-menu-backdrop aria-hidden="true"></div>
            <button
                class="site-news-backdrop"
                type="button"
                data-news-backdrop
                aria-label="Close news updates"
            ></button>

            <div class="site-content-with-news @if ($showSiteNewsSidebar && count($siteNewsItems) > 0) has-news-sidebar @endif">
                <div id="page-content" tabindex="-1">
                    @yield('content')
                </div>

                @if ($showSiteNewsSidebar && count($siteNewsItems) > 0)
                    @include('partials.site-news-sidebar', ['siteNewsItems' => $siteNewsItems])
                @endif
            </div>

            <footer class="site-footer" @if ($footerBgTarget) data-bg-target="{{ $footerBgTarget }}" @endif>
                @include('partials.site-footer')
            </footer>
        </div>

        @stack('after-body')
    </body>
</html>
