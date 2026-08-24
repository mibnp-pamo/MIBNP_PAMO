# Project Review

Generated on: 2026-04-29

> Historical snapshot: this review predates the current shared layout, production
> Geography route, centralized public content, and expanded test coverage. See
> `README.md` for the current deployment and verification status.

This review focuses on real code structure problems, UX/design issues, and the parts of the site that make it feel more like an AI-generated brochure than a polished custom website.

## Overall Summary

The project has a solid visual base and a consistent identity, but it currently feels too template-driven because:

- the same page shell is duplicated across many Blade files
- large amounts of content are hard-coded directly inside views
- many sections use the same glass-panel treatment and full-screen background pattern
- some copy explains the page instead of telling the park's story
- a few public-facing parts still look like prototypes rather than finished product

## Findings

### 1. High: Shared page structure is duplicated instead of using one real layout

The site repeats the full HTML document, header, scripts, and footer across many view files.

Main files:

- `resources/views/home.blade.php`
- `resources/views/gallery.blade.php`
- `resources/views/biodiversity.blade.php`
- `resources/views/partners.blade.php`
- `resources/views/map-testing.blade.php`

There is already a layout file:

- `resources/views/app.blade.php`

Problem:

- changes to shared UI are harder to maintain
- pages drift apart over time
- the codebase feels copied rather than intentionally structured

Recommended change:

- make `resources/views/app.blade.php` the real base layout
- move shared head, header, page shell, and footer into the layout
- leave each page file responsible only for page-specific content

### 2. High: Too much content lives inside Blade `@php` blocks

Many public pages define arrays and content directly inside the view.

Examples:

- `resources/views/home.blade.php`
- `resources/views/biodiversity.blade.php`
- `resources/views/gallery.blade.php`
- `resources/views/partners.blade.php`
- `resources/views/map-testing.blade.php`

Problem:

- content, data, and presentation are mixed together
- editing content becomes error-prone
- the site feels static and mock-like

Recommended change:

- move page data into controllers, config/content files, or database-backed models
- keep Blade files focused on rendering only

### 3. Medium: The gallery interaction feels unstable and over-designed

Gallery hover behavior is spread across shared and page-specific CSS.

Main file:

- `public/css/site.css`

Problem areas:

- gallery image hover scale is strong and visually aggressive
- some gallery overrides allow overflow outside the card
- frame styling and hover behavior have been layered several times
- card motion and image motion are not fully consistent

Recommended change:

- simplify to one hover pattern
- keep a smaller zoom level
- prefer hidden overflow unless the effect is intentionally framed
- keep card movement subtle and consistent

### 4. Medium: The whole site uses the same background and panel language too often

Main files:

- `public/css/site.css`
- `public/js/site.js`

Problem:

- full-screen fixed background slides are reused across many sections
- the same dark glass `text-panel` pattern appears repeatedly
- this makes different pages feel like variations of one generated landing page

Recommended change:

- keep strong hero sections
- reduce background-swapping across regular content sections
- create distinct section styles per page instead of repeating the same panel system everywhere

### 5. Medium: The Geography page still looks like a prototype

Main files:

- `routes/web.php`
- `resources/views/map-testing.blade.php`
- `resources/views/partials/main-site-nav.blade.php`

Problem:

- the public route is still `/map-testing`
- the page uses "test" naming in file and route structure
- panel behavior and route preview copy still feel like a lab/demo interface

Recommended change:

- rename the route and page to `geography`
- replace prototype wording with production wording
- redesign the side panels as final content instead of a test dashboard

### 6. Medium: A lot of copy sounds explanatory instead of place-driven

Examples:

- `resources/views/partners.blade.php`
- `resources/views/gallery.blade.php`
- `resources/views/home.blade.php`

Problem:

- phrases like "this page", "this section", and "keeps every official link reachable" sound generated
- much of the copy describes the website structure instead of the park itself
- this is one of the strongest "AI-made website" signals in the project

Recommended change:

- rewrite headings and intros around place, people, wildlife, access, and conservation
- use more concrete language and fewer meta explanations
- make each page sound like it belongs to a real institution, not a design prompt

### 8. Low: Visual system is too uniform across pages

Main file:

- `public/css/site.css`

Problem:

- many pages rely on the same rounded pills, same shadows, same panel treatment, same button rhythm
- this creates consistency, but too much sameness reduces character

Recommended change:

- vary section composition more
- let some sections breathe with simpler surfaces
- use fewer "hero + glass panel + buttons + dark overlay" combinations

### 9. Low: There is almost no real test coverage

Current tests found:

- `tests/Feature/ExampleTest.php`
- `tests/Unit/ExampleTest.php`

Problem:

- important user flows are not protected by tests
- future UI/content refactors will be riskier

Recommended change:

- add feature tests for public pages

## What Makes It Look "AI-Based"

These are the biggest contributors to that feeling:

1. Repeated structure on every page
2. Repeated glassmorphism panels and background slideshow treatment
3. Copy that explains the section instead of communicating something memorable
4. Prototype names still visible in production routes and files
5. Over-styled hover effects and decorative motion without enough content hierarchy

## Best First Changes

If you want the highest-impact cleanup first, do these in order:

1. Convert all public pages to one shared Blade layout
2. Move hard-coded page data out of views
3. Rewrite the generic page intro copy
4. Rename `/map-testing` to a real production geography route
5. Simplify the gallery hover and framing system

## Fastest Design Wins

If your goal is specifically to make the site look less AI-generated visually, start here:

1. Reduce reuse of the same `text-panel` style in `public/css/site.css`
2. Give each page one unique visual section instead of repeating the same panel system
3. Rewrite page leads in `home`, `gallery`, and `partners` to sound more human and specific
4. Tone down hover effects in the gallery so they feel premium instead of experimental

## Notes

This review was based on the current Blade views, shared CSS/JS, and routes. It is a code-and-structure review, not a full browser/device QA pass.
