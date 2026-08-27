<?php

namespace App\Http\Controllers;

use App\Models\NewsPost;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsController extends Controller
{
    public function document(NewsPost $newsPost): StreamedResponse
    {
        abort_unless(
            $newsPost->isVisible()
                && $newsPost->document_path
                && Storage::disk('local')->exists($newsPost->document_path),
            404,
        );

        $response = Storage::disk('local')->response(
            $newsPost->document_path,
            $newsPost->document_name ?: Str::slug($newsPost->title).'.pdf',
            ['Content-Type' => 'application/pdf'],
            'inline',
        );
        $response->headers->set('Cache-Control', 'no-store, private');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }

    public function show(NewsPost $newsPost): View
    {
        abort_unless($newsPost->isVisible(), 404);

        return view('news.show', [
            'pageTitle' => $newsPost->title.' | Mounts Iglit-Baco Natural Park',
            'metaDescription' => $newsPost->summary,
            'bodyClass' => 'news-detail-page',
            'backgroundSlides' => [
                ['id' => 'news-detail-scene', 'image' => $newsPost->publicImage()],
            ],
            'footerBgTarget' => 'news-detail-scene',
            'socialImage' => $newsPost->publicImage(),
            'newsPost' => $newsPost,
            'siteNewsItems' => NewsPost::query()
                ->visible()
                ->publicOrder()
                ->limit(3)
                ->get()
                ->map(fn (NewsPost $post): array => $post->toPublicItem())
                ->all(),
            'brandHref' => route('home', [], false),
            'themeColor' => '#17392c',
            'headerTheme' => 'dark',
        ]);
    }
}
