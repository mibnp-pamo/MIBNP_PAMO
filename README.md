# Mounts Iglit-Baco Natural Park PAMO Website

Public information website for Mounts Iglit-Baco Natural Park and its Protected Area Management Office. The Laravel application publishes visitor guidance, biodiversity records, maps, partner information, field photography, and a visitation-request form.

## Current publication status

The application code has been hardened for internet deployment, but a server is not ready to go live until its real domain, database, SMTP service, TLS certificate, and any reverse-proxy addresses are configured. The local `.env` remains a development environment intentionally.

Before every production release, this command must pass:

```bash
php artisan app:production-check
```

Production HTTP requests also fail closed when critical settings are unsafe. Console commands remain available so a new server can generate its key, migrate, clear caches, and repair configuration.

## Requirements

- PHP 8.2 or newer with the extensions Laravel requires
- Composer
- MySQL or another production-grade database supported by Laravel
- An SMTP or transactional email service
- Apache or Nginx with the document root set to `public/`
- HTTPS

The GD extension is only required when regenerating optimized images. Composer needs PHP ZIP support or an external `unzip`/7-Zip executable when installing distribution archives.

## Local development

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

For the included XAMPP environment, `php` may need to be replaced with:

```powershell
C:\xampp\php\php.exe artisan serve
```

### Local staff news editor

The news sidebar and News Corner are managed from a private staff area backed by
the application database. Prepare it after installing the project:

```powershell
C:\xampp\php\php.exe artisan migrate --seed
C:\xampp\php\php.exe artisan storage:link
C:\xampp\php\php.exe artisan staff:create pamoStaff --name="PAMO Administrator"
```

The last command requests the password privately in the terminal and never
prints or stores it as plain text. Local accounts require at least 8 characters;
production accounts require at least 12. Add a real address with
`--email=staff@example.gov.ph` when needed. No public account registration route
exists.

When the project is opened through its current XAMPP directory, staff can sign
in at:

```text
http://localhost/MIBNP_PAMO/public/pamo-staff/login
```

When using `artisan serve`, use the displayed local origin followed by
`/pamo-staff/login`, for example `http://127.0.0.1:8000/pamo-staff/login`.
The URL is not present in public navigation or the sitemap. In the `local`
environment, requests to the staff area from devices other than the host
computer return 404. Authentication still protects all editor routes.

Staff can create drafts, schedule publication and expiration, feature an item,
upload a JPEG/PNG/WebP image of up to 5 MB, attach a visitor-facing PDF of up to
10 MB, preview the result, and publish it. Public PDFs are stored on Laravel's
private disk and served through a visibility-checked download route, so documents
belonging to drafts, scheduled, expired, or removed updates are not downloadable.
PDFs containing scripts, embedded files, or launch actions are rejected. Removed
items are soft-deleted so their database records remain recoverable.

The local template deliberately uses the log mailer. A visitation form submitted with the `log` or `array` mailer now returns a delivery error instead of falsely claiming that the office received it.

## Production deployment

### 1. Deploy only application files

Set the virtual host or hosting control panel document root to:

```text
/path/to/MIBNP_PAMO/public
```

Do not publish the project directory itself. Do not copy development artifacts such as:

- `.env` from a developer machine
- `database/database.sqlite` or `database/backups/`
- `composer-audit-temp.phar`
- `node_modules/`, `tests/`, temporary files, or local logs

The root `.htaccess` is a development fallback for XAMPP. It now blocks application directories and sensitive project files, but it is not a substitute for a correct `public/` document root.
Its `RewriteBase /MIBNP_PAMO/` matches the current XAMPP folder name; update that value if the local project directory is renamed.

### 2. Install locked production dependencies

```bash
composer install --no-dev --optimize-autoloader
```

The lock file is authoritative. Do not run an unrestricted `composer update` on the production server.

### 3. Create the production environment

Copy `.env.production.example` to a server-only `.env`, then replace every example hostname and every `CHANGE_ME` value:

```bash
cp .env.production.example .env
php artisan key:generate --force
```

Required settings include:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://the-real-domain`
- a non-placeholder `APP_KEY`
- production database credentials
- `MAIL_MAILER` and the real SMTP/service settings
- an authorized `MAIL_FROM_ADDRESS`
- `SESSION_SECURE_COOKIE=true`
- the PAMO destination in `VISITATION_REQUEST_EMAIL`
- exact comma-separated hostnames in `TRUSTED_HOSTS`

`TRUSTED_PROXIES` must be empty when Laravel is directly internet-facing. Behind a CDN, load balancer, or reverse proxy, it must contain only that service's explicit IP addresses or CIDR ranges. Wildcards are rejected.

### 4. Prepare Laravel

Make `storage/` and `bootstrap/cache/` writable by the PHP/web-server account, then run:

```bash
php artisan migrate --force
php artisan storage:link
php artisan staff:create pamoStaff --name="PAMO Administrator" --email=staff@example.gov.ph
php artisan optimize
php artisan app:production-check
```

If the last command reports an issue, do not publish the virtual host.

### 5. Configure the web and mail services

- Redirect all HTTP traffic to HTTPS.
- Keep the security headers returned by Laravel; do not overwrite them with weaker values.
- Enable compression and static-file caching. Equivalent Apache directives are included in `public/.htaccess`.
- Configure SPF, DKIM, and DMARC for the sender domain.
- Verify that the SMTP provider accepts the configured sender and that the PAMO mailbox receives a real form submission.
- Restrict direct access to the origin when a CDN or reverse proxy is used.
- Back up the production database and document the restore procedure.
- Back up `storage/app/private/news-documents/` with the database; it contains
  the public news documents managed by staff but is intentionally not directly
  web-accessible.

For visitation-form delivery, request a dedicated sending mailbox or SMTP
credential from the organization's mail administrator. Configure the production
`.env` with that credential and keep the PAMO mailbox as the destination:

```dotenv
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=the-provider-smtp-host
MAIL_PORT=587
MAIL_USERNAME=the-smtp-username
MAIL_PASSWORD=the-smtp-password
MAIL_FROM_ADDRESS=the-authorized-sender-address
MAIL_FROM_NAME="${APP_NAME}"
VISITATION_REQUEST_EMAIL="r4b.mibnp@denr.gov.ph"
```

Use `MAIL_SCHEME=smtp` for port 587, where the mailer automatically negotiates
STARTTLS. If the provider requires implicit TLS on port 465, use
`MAIL_SCHEME=smtps` and `MAIL_PORT=465`. The sender address must be authorized by
the provider. The visitor's submitted address is deliberately used as Reply-To,
not From, so replies reach the visitor without breaking the sender domain's
SPF/DMARC policy.

Never commit the SMTP password or copy a development `.env` to the server. After
editing the production `.env`, run:

```bash
php artisan optimize:clear
php artisan app:production-check
```

### 6. Perform the live smoke test

Check the following on the real HTTPS domain:

- `/`, `/biodiversity`, `/geography`, `/gallery`, `/office-profile`, `/partners`, and `/privacy`
- `/up`, `/robots.txt`, and `/sitemap.xml`
- desktop and mobile navigation
- keyboard navigation and the skip link
- OpenStreetMap tiles and the embedded office map
- a valid PDF visitation request and actual mailbox delivery
- rejection of an executable, DOC/DOCX file, oversized file, and active-content PDF
- HTTP-to-HTTPS redirects, canonical URLs, social metadata, and response security headers

## Security fixes recorded on 2026-07-28

### Production configuration

- Added `.env.production.example` with secure session, HTTPS, database, mail, trusted-host, and explicit-proxy settings.
- Declared the optional previous-key and maintenance-store environment values explicitly so Laravel configuration and editor environment checks stay aligned.
- Corrected the SMTP scheme example for port 587 and added a production check that rejects unsupported mail schemes.
- Added `App\Support\ProductionConfiguration`, a `php artisan app:production-check` command, and a production HTTP fail-closed check.
- Changed the visitation controller so non-delivering `log` and `array` mailers cannot return a success message.

### Proxy, host, and abuse protection

- Removed the wildcard trusted proxy.
- Added exact trusted-host patterns based on `TRUSTED_HOSTS`, falling back to the `APP_URL` host.
- Added explicit proxy parsing through `TRUSTED_PROXIES`; `*` and `**` are rejected.
- Added a regression test proving that forged `X-Forwarded-For` values do not bypass the visitation throttle.

### Obsolete public API

- Removed the unused, unthrottled `/api/inquiries` route.
- Removed its controller, model, and pending migration.
- Added a test confirming that the endpoint returns 404.

### Dependency vulnerabilities

- Updated `guzzlehttp/guzzle` from 7.14.2 to 7.15.2.
- Updated `guzzlehttp/psr7` from 2.12.5 to 2.13.0.
- Verified the production lock file with `composer audit --locked --no-dev`; no known advisories remained at the time of the fix.

### Browser and hosting protection

- Added application-wide Content Security Policy, frame protection, MIME sniffing protection, referrer policy, permissions policy, and cross-domain policy headers.
- Kept the `bootstrap/app.php` middleware import order aligned with the project formatter so editor diagnostics remain clean.
- Added HSTS on HTTPS responses.
- Added Apache compression, caching, and static response headers.
- Hardened the development root rewrite and blocked private directories and project artifacts.
- Disabled Laravel's unused private-disk serving/upload routes so `storage/app/private` has no public HTTP surface.

The CSP currently permits this site's own assets, Google Fonts, OpenStreetMap tiles, and the OpenStreetMap iframe. Any future analytics, embeds, scripts, fonts, or image hosts must be deliberately added to `App\Http\Middleware\SecurityHeaders`.

### Visitation uploads and privacy

- Restricted supporting documents to PDFs of at most 5 MB.
- Added PDF-signature validation and rejection of scripts, embedded files, launch actions, and rich-media markers.
- Standardized the attachment filename sent to the office.
- Added a public `/privacy` notice and linked it from the consent field and footer.

The PDF rule is defense in depth, not antivirus software. The production mail gateway should still scan attachments. The privacy notice and the office's exact retention procedure must be approved by the responsible PAMO/DENR privacy or records officer before launch.

### Image performance

- Added `tools/optimize-public-images.php`.
- Generated WebP derivatives under `public/generated/optimized/` without deleting source images.
- Converted 77.5 MB of selected source assets into 4.2 MB of web-delivery derivatives.
- Updated backgrounds, logos, maps, brochures, and major content images to prefer optimized files.
- Added lazy loading and asynchronous decoding to below-the-fold images.
- Kept original brochure images available only when a visitor explicitly opens the full-resolution version.

To regenerate after changing source images:

```powershell
C:\xampp\php\php.exe -d extension=gd -d memory_limit=512M tools\optimize-public-images.php
```

The measured potential local asset payload changed as follows:

| Page | Before | After |
|---|---:|---:|
| Home | 28.0 MB | 1.8 MB |
| Partners | 16.7 MB | 1.0 MB |
| Office Profile | 24.3 MB | 2.0 MB |
| Biodiversity | 18.9 MB | 4.0 MB |
| Gallery | 20.9 MB | 4.0 MB |

These figures count referenced local files and are not a substitute for a live Lighthouse/WebPageTest measurement through the production CDN.

### Search and accessibility

- Added canonical URLs, favicon metadata, Open Graph tags, and Twitter card metadata.
- Replaced the static robots file with host-aware `/robots.txt`.
- Added a dynamic `/sitemap.xml` covering all public pages.
- Omitted the optional XML declaration from the Blade sitemap template so editors do not misread it as PHP; the endpoint still returns standards-compliant UTF-8 XML.
- Corrected the displayed bird-photo source spelling to `eBird.org`.
- Consolidated the homepage footer into one shared partial so every public page uses the same identity, navigation, contact, privacy, and copyright content.
- Connected the footer's Geography link directly to the dedicated `/geography` page instead of the homepage section anchor.
- Added a keyboard skip link.
- Added the missing Geography `<h1>`, an accessible map-region label, and polite announcements for selected map-point changes.
- Existing alternative text, visible focus styles, and reduced-motion rules were preserved.

### Visitation panel presentation

- Removed the explanatory paragraph beneath the visitation-request heading.
- Removed sticky positioning from the green introduction column so it no longer follows the viewport while the form is being viewed.

## Verification commands

Run these after any publishing-related change:

```bash
php artisan config:clear
php artisan test
composer validate --no-check-publish
composer audit --locked --no-dev
php artisan route:list --except-vendor
php artisan optimize
php artisan app:production-check
```

The production check is expected to fail in the local development environment. It must pass on the configured server.

## Remaining owner approvals

Code cannot supply or approve the following:

- the final public domain and TLS/DNS configuration
- database and mail-service credentials
- approved proxy/CDN IP ranges
- SPF, DKIM, and DMARC records
- the official privacy-retention wording and responsible privacy contact
- photo, logo, brochure, map-data, and copied-content publication rights
- real-device visual QA and a live email-delivery test

These must be completed before making the domain public.
