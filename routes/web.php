<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PublicWebsiteController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

Route::get('/', [PublicWebsiteController::class, 'root'])->name('home');
Route::get('/{locale}', [PublicWebsiteController::class, 'home'])
    ->whereIn('locale', ['en', 'ar'])
    ->name('localized.home');

Route::get('/contact', function () {
    return redirect()->route('localized.contact.show', ['locale' => 'en']);
})->name('contact.redirect');

Route::get('/{locale}/contact', function (string $locale) {
    return view('contact', ['locale' => $locale]);
})->whereIn('locale', ['en', 'ar'])->name('localized.contact.show');

Route::post('/{locale}/contact', [ContactController::class, 'store'])
    ->whereIn('locale', ['en', 'ar'])
    ->middleware(['throttle:5,1', ProtectAgainstSpam::class])
    ->name('localized.contact.store');

// Keep the existing POST endpoint name for older clients while new forms use localized URLs.
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware(['throttle:5,1', ProtectAgainstSpam::class])
    ->name('contact.store');

Route::get('/{locale}/{section}', [PublicWebsiteController::class, 'index'])
    ->whereIn('locale', ['en', 'ar'])
    ->whereIn('section', ['services', 'projects', 'products', 'templates', 'insights'])
    ->name('public.content.index');

Route::get('/{locale}/{section}/{slug}', [PublicWebsiteController::class, 'show'])
    ->whereIn('locale', ['en', 'ar'])
    ->whereIn('section', ['services', 'projects', 'products', 'templates', 'insights'])
    ->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')
    ->name('public.content.show');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');

Route::prefix('admin-panel')->name('admin.')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('authenticate');

    Route::middleware('admin')->group(function (): void {
        Route::view('/', 'admin.dashboard')->name('dashboard');
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
        Route::get('/inquiries', [\App\Http\Controllers\Admin\InquiryController::class, 'index'])->name('inquiries.index');
        Route::get('/inquiries/{inquiry}/edit', [\App\Http\Controllers\Admin\InquiryController::class, 'edit'])->name('inquiries.edit');
        Route::put('/inquiries/{inquiry}', [\App\Http\Controllers\Admin\InquiryController::class, 'update'])->name('inquiries.update');
        Route::resource('services', \App\Http\Controllers\Admin\ServiceController::class)->except(['show', 'destroy']);
        Route::resource('projects', \App\Http\Controllers\Admin\ProjectController::class)->except(['show', 'destroy']);
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)->except(['show', 'destroy']);
        Route::resource('templates', \App\Http\Controllers\Admin\WebsiteTemplateController::class)->except(['show', 'destroy']);
        Route::resource('insights', \App\Http\Controllers\Admin\InsightController::class)->except(['show', 'destroy']);
        Route::resource('insight-categories', \App\Http\Controllers\Admin\InsightCategoryController::class)->except(['show', 'destroy']);
    });
});
