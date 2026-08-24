<div class="container footer-grid">
    <div>
        <a class="brand footer-brand" href="{{ route('home', [], false) }}">
            <span class="brand-mark">
                <img
                    src="{{ \App\Support\PublicSiteContent::optimizedAsset('logo.png') }}"
                    alt="Mts. Iglit-Baco Natural Park logo"
                    loading="lazy"
                >
            </span>
            <span class="brand-copy">
                <strong>Mts. Iglit-Baco</strong>
                <span>Natural Park</span>
            </span>
        </a>
        <p class="footer-blurb">
            Official park information and visitor coordination from the Protected Area Management Office.
        </p>
    </div>

    <div>
        <p class="footer-title">Quick Links</p>
        <div class="footer-links">
            <a href="{{ route('home', [], false) }}">Home</a>
            <a href="{{ route('geography', [], false) }}">Geography</a>
            <a href="{{ route('biodiversity', [], false) }}">Biodiversity</a>
            <a href="{{ route('gallery', [], false) }}">Gallery</a>
        </div>
    </div>

    <div>
        <p class="footer-title">Explore</p>
        <div class="footer-links">
            <a href="{{ route('geography', [], false) }}">Open the geography map</a>
            <a href="{{ route('gallery', [], false) }}">Browse the field gallery</a>
        </div>
    </div>

    @include('partials.footer-contact')
</div>

<div class="container footer-bottom">
    <p>&copy; {{ now()->year }} Mts. Iglit-Baco Natural Park.</p>
    <p>
        Protected Area Management Office.
        <a href="{{ route('privacy', [], false) }}">Privacy notice</a>
    </p>
</div>
