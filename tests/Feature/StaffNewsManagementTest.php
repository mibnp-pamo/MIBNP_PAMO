<?php

namespace Tests\Feature;

use App\Models\NewsPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StaffNewsManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_area_is_unlinked_and_requires_authentication(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('/pamo-staff', false);

        $this->get(route('robots'))
            ->assertOk()
            ->assertSee('Disallow: /pamo-staff/', false);

        $this->get(route('staff.news.index'))
            ->assertRedirect(route('staff.login'));

        $this->get(route('staff.login'))
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive')
            ->assertSee('Staff sign in')
            ->assertSee('noindex, nofollow, noarchive', false);
    }

    public function test_only_administrators_can_sign_in_to_the_staff_area(): void
    {
        $administrator = User::query()->create([
            'name' => 'PAMO Administrator',
            'username' => 'pamoStaff',
            'email' => 'admin@example.com',
            'password' => 'a-secure-test-password',
            'is_admin' => true,
        ]);
        User::query()->create([
            'name' => 'Regular User',
            'username' => 'viewer',
            'email' => 'viewer@example.com',
            'password' => 'another-secure-password',
            'is_admin' => false,
        ]);

        $this->post(route('staff.login.store'), [
            'username' => 'viewer',
            'password' => 'another-secure-password',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();

        $this->post(route('staff.login.store'), [
            'username' => $administrator->username,
            'password' => 'a-secure-test-password',
        ])->assertRedirect(route('staff.news.index'));

        $this->assertAuthenticatedAs($administrator);
        $this->assertNotNull($administrator->fresh()->last_login_at);
    }

    public function test_local_staff_area_returns_not_found_to_remote_devices(): void
    {
        $originalEnvironment = app()->environment();
        app()->detectEnvironment(fn (): string => 'local');

        try {
            $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.25'])
                ->get(route('staff.login'))
                ->assertNotFound();

            $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
                ->get(route('staff.login'))
                ->assertOk();
        } finally {
            app()->detectEnvironment(fn (): string => $originalEnvironment);
        }
    }

    public function test_an_administrator_can_publish_an_internal_news_update(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $administrator = $this->administrator();

        $response = $this->actingAs($administrator)->post(route('staff.news.store'), [
            'title' => 'Temporary trail advisory',
            'summary' => 'A section of the trail will be unavailable while field maintenance is completed.',
            'body' => "Maintenance begins Monday.\nPlease coordinate with the PAMO before visiting.",
            'category' => 'Advisory',
            'source' => 'MIBNP PAMO',
            'status' => 'published',
            'is_pinned' => '1',
            'published_at' => now()->subMinute()->format('Y-m-d H:i:s'),
            'image_alt' => 'Rangers maintaining a mountain trail',
            'image' => new UploadedFile(
                public_path('bglogo/daboville.png'),
                'trail.png',
                'image/png',
                null,
                true,
            ),
            'document_label' => 'Visitor trail advisory',
            'document' => UploadedFile::fake()->createWithContent(
                'visitor-guide.pdf',
                "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF",
            ),
        ]);

        $response->assertSessionHasNoErrors();

        $newsPost = NewsPost::query()->firstOrFail();

        $response->assertRedirect(route('staff.news.edit', $newsPost));
        $this->assertSame('temporary-trail-advisory', $newsPost->slug);
        $this->assertTrue($newsPost->is_pinned);
        Storage::disk('public')->assertExists($newsPost->image_path);
        Storage::disk('local')->assertExists($newsPost->document_path);
        $this->assertSame('Visitor trail advisory', $newsPost->documentDisplayName());

        $this->get(route('staff.news.index'))
            ->assertOk()
            ->assertSee('Temporary trail advisory');
        $this->get(route('staff.news.create'))->assertOk();
        $this->get(route('staff.news.edit', $newsPost))->assertOk();
        $this->get(route('staff.news.preview', $newsPost))
            ->assertOk()
            ->assertSee('Private preview')
            ->assertSee('Visitor trail advisory');
        $this->get(route('staff.news.document', $newsPost))
            ->assertOk()
            ->assertDownload('visitor-guide.pdf');
        $this->get(route('staff.login'))->assertRedirect(route('staff.news.index'));

        $this->get(route('partners'))
            ->assertOk()
            ->assertSee('Temporary trail advisory')
            ->assertSee('Visitor trail advisory');

        $this->get(route('news.show', $newsPost))
            ->assertOk()
            ->assertSee('Maintenance begins Monday.')
            ->assertSee('Visitor trail advisory')
            ->assertSee('Back to News Corner');

        $this->get(route('news.document', $newsPost))
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertDownload('visitor-guide.pdf');

        $documentPath = $newsPost->document_path;
        $this->actingAs($administrator)
            ->put(route('staff.news.update', $newsPost), [
                'title' => $newsPost->title,
                'summary' => $newsPost->summary,
                'body' => $newsPost->body,
                'category' => $newsPost->category,
                'source' => $newsPost->source,
                'status' => $newsPost->status,
                'published_at' => $newsPost->published_at->format('Y-m-d H:i:s'),
                'remove_document' => '1',
            ])
            ->assertSessionHasNoErrors();

        Storage::disk('local')->assertMissing($documentPath);
        $this->assertNull($newsPost->fresh()->document_path);
    }

    public function test_draft_scheduled_and_expired_updates_are_not_public(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('news-documents/advisory.pdf', "%PDF-1.4\n%%EOF");

        $draft = $this->newsPost([
            'title' => 'Draft update',
            'slug' => 'draft-update',
            'status' => NewsPost::STATUS_DRAFT,
            'published_at' => now()->subDay(),
            'document_path' => 'news-documents/advisory.pdf',
            'document_name' => 'advisory.pdf',
        ]);
        $scheduled = $this->newsPost([
            'title' => 'Scheduled update',
            'slug' => 'scheduled-update',
            'published_at' => now()->addDay(),
            'document_path' => 'news-documents/advisory.pdf',
            'document_name' => 'advisory.pdf',
        ]);
        $expired = $this->newsPost([
            'title' => 'Expired update',
            'slug' => 'expired-update',
            'published_at' => now()->subDays(2),
            'expires_at' => now()->subDay(),
            'document_path' => 'news-documents/advisory.pdf',
            'document_name' => 'advisory.pdf',
        ]);
        $visible = $this->newsPost([
            'title' => 'Current update',
            'slug' => 'current-update',
            'published_at' => now()->subHour(),
        ]);

        $partners = $this->get(route('partners'))->assertOk();
        $partners->assertSee($visible->title);
        $partners->assertDontSee($draft->title);
        $partners->assertDontSee($scheduled->title);
        $partners->assertDontSee($expired->title);

        foreach ([$draft, $scheduled, $expired] as $hiddenPost) {
            $this->get(route('news.show', $hiddenPost))->assertNotFound();
            $this->get(route('news.document', $hiddenPost))->assertNotFound();
        }
    }

    public function test_active_pdf_content_is_rejected_by_the_news_editor(): void
    {
        Storage::fake('local');

        $this->actingAs($this->administrator())
            ->post(route('staff.news.store'), [
                'title' => 'Unsafe attachment test',
                'summary' => 'This update contains a document that must be rejected.',
                'body' => 'A body is supplied so only the document validation is under test.',
                'category' => 'Advisory',
                'source' => 'MIBNP PAMO',
                'status' => 'draft',
                'document' => UploadedFile::fake()->createWithContent(
                    'unsafe.pdf',
                    "%PDF-1.4\n1 0 obj\n<< /JavaScript (alert) >>\nendobj\n%%EOF",
                ),
            ])
            ->assertSessionHasErrors('document');

        $this->assertDatabaseCount('news_posts', 0);
        $this->assertSame([], Storage::disk('local')->allFiles('news-documents'));
    }

    public function test_removing_an_update_soft_deletes_it(): void
    {
        $newsPost = $this->newsPost();

        $this->actingAs($this->administrator())
            ->delete(route('staff.news.destroy', $newsPost))
            ->assertRedirect(route('staff.news.index'));

        $this->assertSoftDeleted($newsPost);
        $this->get(route('news.show', $newsPost))->assertNotFound();
    }

    private function administrator(): User
    {
        return User::query()->create([
            'name' => 'PAMO Administrator',
            'username' => 'pamoStaff',
            'email' => 'admin@example.com',
            'password' => 'a-secure-test-password',
            'is_admin' => true,
        ]);
    }

    private function newsPost(array $attributes = []): NewsPost
    {
        return NewsPost::query()->create(array_merge([
            'title' => 'Public conservation update',
            'slug' => 'public-conservation-update',
            'summary' => 'A current conservation update from the park office.',
            'body' => 'Full public update text.',
            'category' => 'Conservation',
            'source' => 'MIBNP PAMO',
            'status' => NewsPost::STATUS_PUBLISHED,
            'published_at' => now()->subHour(),
        ], $attributes));
    }
}
