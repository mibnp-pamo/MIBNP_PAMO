<details class="site-news-sidebar">
    <summary class="site-news-sidebar-toggle" aria-label="Show or hide the latest news and announcements">
        <span class="site-news-sidebar-toggle-open">
            <span class="site-news-sidebar-toggle-dot" aria-hidden="true"></span>
            <span>News</span>
            <span class="site-news-sidebar-count">{{ count($siteNewsItems) }}</span>
        </span>
        <span class="site-news-sidebar-toggle-close">Close updates <span aria-hidden="true">×</span></span>
    </summary>

    <div class="site-news-sidebar-panel" aria-labelledby="site-news-sidebar-title">
        <div class="site-news-sidebar-heading">
            <div class="site-news-sidebar-kicker">
                <p class="section-tag">News &amp; Announcements</p>
                <span class="site-news-sidebar-live">Featured</span>
            </div>
            <h2 id="site-news-sidebar-title">Latest field updates</h2>
            <p>Official partner notices and conservation stories from across the Mts. Iglit-Baco landscape.</p>
        </div>

        <div class="site-news-sidebar-list">
            @foreach ($siteNewsItems as $item)
                <article class="site-news-sidebar-item">
                    <div class="site-news-sidebar-meta">
                        <span>{{ $item['source'] }}</span>
                        <span>{{ $item['status'] }}</span>
                    </div>
                    <p class="site-news-sidebar-category">{{ $item['tag'] }}</p>
                    <h3>{{ $item['title'] }}</h3>
                    <div class="site-news-sidebar-links">
                        <a
                            href="{{ $item['url'] }}"
                            @if ($item['external']) target="_blank" rel="noopener noreferrer" @endif
                        >
                            {{ $item['cta'] }}
                        </a>
                        @if (!empty($item['document_url']))
                            <a href="{{ $item['document_url'] }}">Download PDF</a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <a class="site-news-sidebar-all" href="{{ route('partners', [], false) }}#news">
            Open News Corner
        </a>
    </div>
</details>
