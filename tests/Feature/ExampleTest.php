<?php

namespace Tests\Feature;

use App\Mail\VisitationRequestMail;
use App\Support\PublicSiteContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), geolocation=(), microphone=()');
        $response->assertHeaderContains('Content-Security-Policy', "default-src 'self'");
        $response->assertHeaderContains('Content-Security-Policy', "frame-ancestors 'none'");
        $response->assertHeaderMissing('Strict-Transport-Security');
        $response->assertSee('<link rel="canonical"', false);
        $response->assertSee('property="og:title"', false);
        $response->assertSee('class="skip-link"', false);
        $response->assertSee(route('visitation-requests.store', [], false), false);
        $response->assertSee('enctype="multipart/form-data"', false);
        $response->assertSee('max="50"', false);
        $response->assertSee('Maximum 50 visitors per request.');
    }

    public function test_https_responses_enable_hsts(): void
    {
        $this->get('https://localhost/')
            ->assertHeader(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains',
            );
    }

    public function test_public_pages_share_the_same_footer_content(): void
    {
        $canonicalFooter = null;

        foreach (['/', '/biodiversity', '/geography', '/gallery', '/office-profile', '/partners', '/privacy'] as $path) {
            $response = $this->get($path)->assertOk();
            $html = $response->getContent();

            $this->assertSame(
                1,
                preg_match('/<footer class="site-footer"[^>]*>(.*?)<\/footer>/s', $html, $matches),
                "The page {$path} must contain the shared site footer.",
            );

            $footer = trim($matches[1]);
            $this->assertStringContainsString('Official park information and visitor coordination', $footer);
            $this->assertStringContainsString('r4b.mibnp@denr.gov.ph', $footer);
            $this->assertStringContainsString('Privacy notice', $footer);
            $this->assertStringContainsString('href="/geography">Geography</a>', $footer);
            $this->assertStringNotContainsString('href="/#geography"', $footer);

            if ($canonicalFooter === null) {
                $canonicalFooter = $footer;

                continue;
            }

            $this->assertSame($canonicalFooter, $footer, "The footer on {$path} differs from the homepage.");
        }
    }

    public function test_the_partners_page_returns_a_successful_response(): void
    {
        $response = $this->get('/partners');

        $response->assertStatus(200);
    }

    public function test_the_geography_page_returns_a_successful_response(): void
    {
        $response = $this->get('/geography');

        $response->assertStatus(200);
        $response->assertSee('<h1 class="visually-hidden">', false);
        $response->assertSee('stations\/MAGAWANG%20STATION%203.jpg', false);
        $response->assertSee('stations\/LOIBFU%20STATION%204.jpg', false);
    }

    public function test_the_legacy_map_testing_route_redirects_to_geography(): void
    {
        $response = $this->get('/map-testing');

        $response->assertRedirect(route('geography'));
    }

    public function test_the_biodiversity_page_returns_a_successful_response(): void
    {
        $response = $this->get('/biodiversity');
        $profiles = collect(PublicSiteContent::biodiversity()['faunaProfiles'])->keyBy('title');

        $response->assertStatus(200);
        $response->assertSee('class="gallery-highlight-card biodiversity-flora-card"', false);
        $response->assertDontSee('class="gallery-frame-card biodiversity-flora-card"', false);
        $response->assertSee('Philippine%20Brown%20Deer.jpg', false);
        $response->assertSee('Limnonectes%20beloncioi%20%281%29.jpg', false);
        $response->assertSee('Leptobrachium%20mangyanorum%20%281%29.jpg', false);
        $response->assertSee('Mindoro%20Variable%20Backed%20Frog.jpg', false);
        $response->assertSee('Mindoro%20Stream%20Frog%20%28Pulchrana%20mangyanum%29%202.JPG', false);
        $response->assertSee('Mindoro%20Shrub%20Frog%20%28Philautus%20schmackeri%29%201.JPG', false);
        $response->assertSee('Mindoro%20Bulbul.JPG', false);
        $response->assertSee('Mindoro%20Racquet%20-%20Tail%20Parrot.jpg', false);
        $response->assertSee('2 photos');
        $this->assertStringContainsString('Philippine%20Brown%20Deer.jpg', $profiles['Philippine Brown Deer']['image']);
        $this->assertStringContainsString('Limnonectes%20beloncioi%20%281%29.jpg', $profiles['Mindoro Fanged Frog']['image']);
        $this->assertStringContainsString('Leptobrachium%20mangyanorum%20%281%29.jpg', $profiles['Mindoro Litter Frog']['image']);
        $this->assertStringContainsString('Mindoro%20Variable%20Backed%20Frog.jpg', $profiles['Mindoro Variable-backed Frog']['image']);
        $this->assertStringContainsString('Mindoro%20Stream%20Frog', $profiles['Mindoro Variable-backed Frog']['additional_images'][0]['image']);
        $this->assertStringContainsString('Mindoro%20Shrub%20Frog', $profiles['Mindoro Tree Frog']['image']);
        $this->assertStringContainsString('Mindoro%20Bulbul.JPG', $profiles['Mindoro Bulbul']['image']);
        $this->assertStringContainsString('Mindoro%20Racquet%20-%20Tail%20Parrot.jpg', $profiles['Mindoro Racquet Tail']['image']);
    }

    public function test_the_gallery_page_returns_a_successful_response(): void
    {
        $response = $this->get('/gallery');

        $response->assertStatus(200);
        $response->assertSee('data-gallery-expand-toggle', false);
        $response->assertDontSee('data-gallery-group-toggle', false);
    }

    public function test_native_flora_photos_are_grouped_with_wildlife_flora_and_habitat(): void
    {
        $galleryGroups = collect(PublicSiteContent::gallery()['galleryGroups']);
        $wildlifeTitles = collect($galleryGroups->firstWhere('id', 'wildlife-habitat')['frames'])
            ->pluck('title');
        $scenicTitles = collect($galleryGroups->firstWhere('id', 'scenic-views')['frames'])
            ->pluck('title');

        foreach (['Mindoro Pine', 'Narra', 'Almaciga'] as $floraTitle) {
            $this->assertTrue($wildlifeTitles->contains($floraTitle));
            $this->assertFalse($scenicTitles->contains($floraTitle));
        }
    }

    public function test_tourist_photo_posting_and_moderation_routes_are_removed(): void
    {
        $this->get('/gallery/tourist-posts')->assertNotFound();
        $this->post('/gallery/tourist-posts')->assertNotFound();
        $this->get('/moderator/login')->assertNotFound();
    }

    public function test_a_visitation_request_with_a_document_is_emailed_to_the_office(): void
    {
        config(['mail.default' => 'smtp']);
        Mail::fake();

        $document = UploadedFile::fake()->createWithContent(
            'request-letter.pdf',
            "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF",
        );

        $response = $this->post(route('visitation-requests.store'), [
            'name' => 'Maria Visitor',
            'email' => 'maria@example.com',
            'phone' => '+63 900 000 0000',
            'organization' => 'Mindoro Nature Group',
            'activity' => 'wildlife-viewing',
            'intended_date' => now()->addWeek()->toDateString(),
            'visitor_count' => 4,
            'document_type' => 'request-letter',
            'document' => $document,
            'message' => 'We would like to coordinate an educational visit.',
            'privacy_consent' => '1',
        ]);

        $response->assertRedirect(route('home').'#visitation-request');
        $response->assertSessionHas('visitation_status');

        Mail::assertSent(VisitationRequestMail::class, function (VisitationRequestMail $mail): bool {
            return $mail->hasTo(config('mail.visitation_requests.to', 'r4b.mibnp@denr.gov.ph'))
                && $mail->details['name'] === 'Maria Visitor'
                && $mail->details['activity'] === 'Wildlife viewing'
                && $mail->attachmentName === 'visitation-supporting-document.pdf'
                && str_contains($mail->render(), 'Maria Visitor')
                && count($mail->attachments()) === 1;
        });
    }

    public function test_a_visitation_request_rejects_unsupported_documents(): void
    {
        config(['mail.default' => 'smtp']);
        Mail::fake();

        $response = $this->from(route('home').'#visitation-request')
            ->post(route('visitation-requests.store'), [
                'name' => 'Maria Visitor',
                'email' => 'maria@example.com',
                'activity' => 'hiking',
                'intended_date' => now()->addWeek()->toDateString(),
                'visitor_count' => 2,
                'document_type' => 'other',
                'document' => UploadedFile::fake()->create(
                    'unsafe.exe',
                    100,
                    'application/x-msdownload',
                ),
                'privacy_consent' => '1',
            ]);

        $response->assertRedirect(route('home').'#visitation-request');
        $response->assertSessionHasErrors('document');
        Mail::assertNothingSent();
    }

    public function test_a_visitation_request_rejects_more_than_fifty_visitors(): void
    {
        config(['mail.default' => 'smtp']);
        Mail::fake();

        $response = $this->from(route('home').'#visitation-request')
            ->post(route('visitation-requests.store'), [
                'name' => 'Large Visitor Group',
                'email' => 'group@example.com',
                'activity' => 'hiking',
                'intended_date' => now()->addWeek()->toDateString(),
                'visitor_count' => 51,
                'privacy_consent' => '1',
            ]);

        $response->assertRedirect(route('home').'#visitation-request');
        $response->assertSessionHasErrors('visitor_count');
        Mail::assertNothingSent();
    }

    public function test_a_visitation_request_rejects_active_pdf_content(): void
    {
        config(['mail.default' => 'smtp']);
        Mail::fake();

        $response = $this->from(route('home').'#visitation-request')
            ->post(route('visitation-requests.store'), [
                'name' => 'Maria Visitor',
                'email' => 'maria@example.com',
                'activity' => 'research',
                'intended_date' => now()->addWeek()->toDateString(),
                'visitor_count' => 2,
                'document_type' => 'proposal',
                'document' => UploadedFile::fake()->createWithContent(
                    'proposal.pdf',
                    "%PDF-1.4\n1 0 obj\n<< /JavaScript (alert) >>\nendobj\n%%EOF",
                ),
                'privacy_consent' => '1',
            ]);

        $response->assertRedirect(route('home').'#visitation-request');
        $response->assertSessionHasErrors('document');
        Mail::assertNothingSent();
    }

    public function test_a_visitation_request_does_not_report_success_with_a_non_delivering_mailer(): void
    {
        config(['mail.default' => 'log']);
        Mail::fake();

        $response = $this->from(route('home').'#visitation-request')
            ->post(route('visitation-requests.store'), [
                'name' => 'Maria Visitor',
                'email' => 'maria@example.com',
                'activity' => 'hiking',
                'intended_date' => now()->addWeek()->toDateString(),
                'visitor_count' => 2,
                'privacy_consent' => '1',
            ]);

        $response->assertRedirect(route('home').'#visitation-request');
        $response->assertSessionHasErrors('delivery');
        $response->assertSessionMissing('visitation_status');
        Mail::assertNothingSent();
    }

    public function test_spoofed_forwarded_ips_do_not_bypass_the_visitation_throttle(): void
    {
        config(['mail.default' => 'smtp']);
        Mail::fake();

        for ($attempt = 1; $attempt <= 4; $attempt++) {
            $response = $this
                ->withServerVariables(['REMOTE_ADDR' => '198.51.100.25'])
                ->withHeader('X-Forwarded-For', "203.0.113.{$attempt}")
                ->post(route('visitation-requests.store'), [
                    'name' => 'Rate Limit Visitor',
                    'email' => 'visitor@example.com',
                    'activity' => 'hiking',
                    'intended_date' => now()->addWeek()->toDateString(),
                    'visitor_count' => 2,
                    'privacy_consent' => '1',
                ]);

            if ($attempt <= 3) {
                $response->assertRedirect(route('home').'#visitation-request');
            } else {
                $response->assertTooManyRequests();
            }
        }

        Mail::assertSentCount(3);
    }

    public function test_the_obsolete_schedule_request_api_is_removed(): void
    {
        $this->postJson('/api/schedule-requests')->assertNotFound();
        $this->postJson('/api/inquiries')->assertNotFound();
    }

    public function test_the_privacy_notice_is_public_and_linked_from_the_form(): void
    {
        $this->get(route('privacy'))
            ->assertOk()
            ->assertSee('Privacy notice')
            ->assertSee('Information collected');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('privacy', [], false), false);
    }

    public function test_search_engine_discovery_files_are_generated_for_the_current_host(): void
    {
        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('home'), false)
            ->assertSee(route('privacy'), false);

        $this->get(route('robots'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Sitemap: '.route('sitemap'), false);
    }
}
