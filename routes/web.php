<?php

use App\Http\Controllers\SitePageController;
use App\Http\Controllers\VisitationRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SitePageController::class, 'home'])->name('home');
Route::post('/visitation-requests', [VisitationRequestController::class, 'store'])
    ->middleware('throttle:3,10')
    ->name('visitation-requests.store');
Route::get('/biodiversity', [SitePageController::class, 'biodiversity'])->name('biodiversity');
Route::get('/partners', [SitePageController::class, 'partners'])->name('partners');
Route::get('/office-profile', [SitePageController::class, 'officeProfile'])->name('office-profile');
Route::get('/geography', [SitePageController::class, 'geography'])->name('geography');
Route::redirect('/map-testing', '/geography');
Route::get('/gallery', [SitePageController::class, 'gallery'])->name('gallery');
Route::get('/privacy', [SitePageController::class, 'privacy'])->name('privacy');
Route::get('/sitemap.xml', [SitePageController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SitePageController::class, 'robots'])->name('robots');

Route::redirect('/about', '/#story');
Route::redirect('/maps', '/geography');
Route::redirect('/guidelines', '/#visit');
Route::redirect('/visitor-rules', '/#visit');
Route::redirect('/conservation-rules', '/#visit');
Route::redirect('/wildlife', '/biodiversity');
Route::redirect('/affiliations', '/partners');
Route::redirect('/contact', '/#contact');
