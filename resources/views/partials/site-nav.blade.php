@foreach ($navLinks as $link)
    @php
        $hasChildren = !empty($link['children']);
        $isCurrentPage = !empty($link['current']);
    @endphp

    @if ($hasChildren)
        <div class="site-nav-dropdown" data-nav-dropdown>
            <div class="site-nav-dropdown-trigger">
                <a class="{{ $link['class'] ?? '' }}" href="{{ $link['href'] }}" data-nav-link @if ($isCurrentPage) aria-current="page" @endif>
                    {{ $link['label'] }}
                </a>

                <button
                    class="site-nav-dropdown-toggle"
                    type="button"
                    aria-expanded="false"
                    aria-label="Open {{ strtolower($link['label']) }} sections"
                    data-nav-dropdown-toggle
                >
                    <span class="site-nav-dropdown-chevron" aria-hidden="true"></span>
                </button>
            </div>

            <div class="site-nav-dropdown-menu" data-nav-dropdown-menu hidden>
                @foreach ($link['children'] as $child)
                    <a href="{{ $child['href'] }}" data-nav-link>{{ $child['label'] }}</a>
                @endforeach
            </div>
        </div>
    @else
        <a class="{{ $link['class'] ?? '' }}" href="{{ $link['href'] }}" data-nav-link @if ($isCurrentPage) aria-current="page" @endif>
            {{ $link['label'] }}
        </a>
    @endif
@endforeach
