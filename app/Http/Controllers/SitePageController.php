<?php

namespace App\Http\Controllers;

use App\Models\NewsPost;
use App\Support\PublicSiteContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

class SitePageController extends Controller
{
    public function home(): View
    {
        return $this->siteView('home', PublicSiteContent::home());
    }

    public function biodiversity(): View
    {
        return $this->siteView('biodiversity', PublicSiteContent::biodiversity());
    }

    public function partners(): View
    {
        $data = PublicSiteContent::partners();
        $newsItems = NewsPost::query()
            ->visible()
            ->publicOrder()
            ->get()
            ->map(fn (NewsPost $post): array => $post->toPublicItem())
            ->all();
        $data['newsItems'] = $newsItems;

        return $this->siteView('partners', $data, $newsItems);
    }

    public function officeProfile(): View
    {
        return $this->siteView('office-profile', PublicSiteContent::officeProfile());
    }

    public function geography(): View
    {
        return $this->siteView('geography', PublicSiteContent::geography(), []);
    }

    public function gallery(): View
    {
        return $this->siteView('gallery', PublicSiteContent::gallery());
    }

    public function privacy(): View
    {
        return $this->siteView('privacy', [
            'pageTitle' => 'Privacy Notice | Mounts Iglit-Baco Natural Park',
            'metaDescription' => 'Privacy notice for visitors using the Mounts Iglit-Baco Natural Park website and visitation request form.',
            'bodyClass' => 'privacy-page',
            'backgroundSlides' => [],
        ]);
    }

    public function sitemap(): Response
    {
        $entries = [
            ['route' => 'home', 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['route' => 'biodiversity', 'changefreq' => 'monthly', 'priority' => '0.9'],
            ['route' => 'geography', 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['route' => 'gallery', 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['route' => 'office-profile', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['route' => 'partners', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['route' => 'privacy', 'changefreq' => 'yearly', 'priority' => '0.3'],
        ];

        NewsPost::query()
            ->visible()
            ->publicOrder()
            ->whereNull('external_url')
            ->each(function (NewsPost $post) use (&$entries): void {
                $entries[] = [
                    'url' => route('news.show', $post),
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                ];
            });

        return response()
            ->view('sitemap', ['entries' => $entries])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $content = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /api/',
            'Disallow: /pamo-staff/',
            'Sitemap: '.route('sitemap'),
            '',
        ]);

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    private function siteView(string $view, array $data, ?array $siteNewsItems = null): View
    {
        $data['siteNewsItems'] = $siteNewsItems ?? NewsPost::query()
            ->visible()
            ->publicOrder()
            ->limit(3)
            ->get()
            ->map(fn (NewsPost $post): array => $post->toPublicItem())
            ->all();

        return view($view, $data);
    }
}
