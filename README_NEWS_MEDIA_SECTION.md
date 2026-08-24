# Partners & News Video Section

This project now includes a `Videos` area inside the `Partners & News` page.

## What It Does

- Adds a video section under the `News Corner`
- Lets you later replace placeholder cards with real links for:
  - YouTube or Facebook videos
  - documentary clips
  - short park explainers

## Where It Is Controlled

### Content

Edit:

- `app/Support/PublicSiteContent.php`

Look for:

- `mediaVideos`

Each item uses this structure:

```php
[
    'tag' => 'Video slot',
    'title' => 'Park overview video',
    'body' => 'Short description here.',
    'url' => null,
    'external' => false,
    'cta' => 'Add video link',
]
```

## How To Add A Real Video Link

Example:

```php
[
    'tag' => 'Video',
    'title' => 'Mts. Iglit-Baco Overview',
    'body' => 'Short introduction video for visitors.',
    'url' => 'https://www.youtube.com/watch?v=example',
    'external' => true,
    'cta' => 'Watch video',
]
```

## Page Markup

The section is rendered in:

- `resources/views/partners.blade.php`

## Styling

The styles are in:

- `public/css/site.css`

Look for classes like:

- `.partner-media-library`
- `.partner-media-grid`
- `.partner-media-column`
- `.partner-media-card`
- `.partner-media-pending`

## Navigation

The dropdown link for this section is in:

- `resources/views/partials/main-site-nav.blade.php`

Anchor used:

- `#resources`

## Current State

Right now the section uses placeholder cards only.

That means:

- no real videos are attached yet
- you only need to replace the `url`, `title`, `body`, and `cta` values when ready
