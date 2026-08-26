<?php

use App\Http\Controllers\Auth\StaffSessionController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\SitePageController;
use App\Http\Controllers\Staff\NewsPostController;
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
Route::get('/news/{newsPost:slug}/document', [NewsController::class, 'document'])->name('news.document');
Route::get('/news/{newsPost:slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/sitemap.xml', [SitePageController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SitePageController::class, 'robots'])->name('robots');

Route::prefix('pamo-staff')->name('staff.')->middleware('staff.local')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [StaffSessionController::class, 'create'])->name('login');
        Route::post('/login', [StaffSessionController::class, 'store'])
            ->middleware('throttle:5,1')
            ->name('login.store');
    });

    Route::middleware(['auth', 'staff.admin'])->group(function (): void {
        Route::post('/logout', [StaffSessionController::class, 'destroy'])->name('logout');
        Route::get('/news/{newsPost}/document', [NewsPostController::class, 'document'])->name('news.document');
        Route::get('/news/{newsPost}/preview', [NewsPostController::class, 'preview'])->name('news.preview');
        Route::resource('news', NewsPostController::class)
            ->parameters(['news' => 'newsPost'])
            ->except('show');
    });
});

Route::redirect('/about', '/#story');
Route::redirect('/maps', '/geography');
Route::redirect('/guidelines', '/#visit');
Route::redirect('/visitor-rules', '/#visit');
Route::redirect('/conservation-rules', '/#visit');
Route::redirect('/wildlife', '/biodiversity');
Route::redirect('/affiliations', '/partners');
Route::redirect('/contact', '/#contact');
