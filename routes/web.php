<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ClientProjectController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MediaFileController;
use App\Http\Controllers\PublicWebsiteController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

Route::get('/', [PublicWebsiteController::class, 'root'])->name('home');
Route::get('/media/{path}', [MediaFileController::class, 'show'])
    ->where('path', '.+')
    ->name('media.show');
Route::get('/{locale}', [PublicWebsiteController::class, 'home'])
    ->whereIn('locale', ['en', 'ar'])
    ->name('localized.home');

Route::get('/contact', function () {
    return redirect()->route('localized.contact.show', ['locale' => 'en']);
})->name('contact.redirect');

Route::get('/{locale}/contact', [PublicWebsiteController::class, 'contact'])
    ->whereIn('locale', ['en', 'ar'])
    ->name('localized.contact.show');

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

        Route::get('/clients', [ClientProjectController::class, 'clients'])->name('clients.index');
        Route::get('/clients/create', [ClientProjectController::class, 'createClient'])->name('clients.create');
        Route::post('/clients', [ClientProjectController::class, 'storeClient'])->name('clients.store');
        Route::get('/clients/{client}', [ClientProjectController::class, 'showClient'])->name('clients.show');

        Route::get('/clients/{client}/delivery-projects/create', [ClientProjectController::class, 'createProject'])->name('delivery-projects.create');
        Route::post('/clients/{client}/delivery-projects', [ClientProjectController::class, 'storeProject'])->name('delivery-projects.store');
        Route::get('/delivery-projects/{project}', [ClientProjectController::class, 'showProject'])->name('delivery-projects.show');
        Route::put('/delivery-projects/{project}', [ClientProjectController::class, 'updateProject'])->name('delivery-projects.update');
        Route::post('/delivery-projects/{project}/updates', [ClientProjectController::class, 'storeUpdate'])->name('delivery-projects.updates.store');
        Route::post('/project-updates/{update}/send', [ClientProjectController::class, 'sendUpdate'])->name('project-updates.send');

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
