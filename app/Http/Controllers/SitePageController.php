<?php

namespace App\Http\Controllers;

use App\Support\PublicSiteContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

class SitePageController extends Controller
{
    public function home(): View
    {
        return view('home', PublicSiteContent::home());
    }

    public function biodiversity(): View
    {
        return view('biodiversity', PublicSiteContent::biodiversity());
    }

    public function partners(): View
    {
        return view('partners', PublicSiteContent::partners());
    }

    public function officeProfile(): View
    {
        return view('office-profile', PublicSiteContent::officeProfile());
    }

    public function geography(): View
    {
        return view('geography', PublicSiteContent::geography());
    }

    public function gallery(): View
    {
        return view('gallery', PublicSiteContent::gallery());
    }

    public function privacy(): View
    {
        return view('privacy', [
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
            'Sitemap: '.route('sitemap'),
            '',
        ]);

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
