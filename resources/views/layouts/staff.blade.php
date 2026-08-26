<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow, noarchive">
        <title>@yield('title', 'PAMO Staff') | MIBNP</title>
        <link rel="icon" href="{{ asset('generated/optimized/logo.webp') }}" type="image/webp">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap"
            rel="stylesheet"
        >
        <link rel="stylesheet" href="{{ asset('css/staff.css') }}">
    </head>
    <body>
        <a class="staff-skip-link" href="#staff-content">Skip to main content</a>

        @auth
            <header class="staff-header">
                <a class="staff-brand" href="{{ route('staff.news.index') }}">
                    <img src="{{ \App\Support\PublicSiteContent::optimizedAsset('logo.png') }}" alt="">
                    <span>
                        <strong>MIBNP</strong>
                        <small>Staff publishing</small>
                    </span>
                </a>

                <nav class="staff-nav" aria-label="Staff navigation">
                    <a href="{{ route('staff.news.index') }}">News updates</a>
                    <a href="{{ route('partners', [], false) }}#news" target="_blank" rel="noopener noreferrer">
                        View public site
                    </a>
                    <form method="POST" action="{{ route('staff.logout') }}">
                        @csrf
                        <button type="submit">Sign out</button>
                    </form>
                </nav>
            </header>
        @endauth

        <main id="staff-content" class="staff-main">
            @if (session('status'))
                <div class="staff-alert staff-alert-success" role="status">{{ session('status') }}</div>
            @endif

            @yield('content')
        </main>
    </body>
</html>
