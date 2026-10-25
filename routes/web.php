<?php

use App\Http\Controllers\Admin\AccessManagementController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ClientProjectController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\WebsiteTemplateController;
use App\Http\Controllers\Admin\InsightController;
use App\Http\Controllers\Admin\InsightCategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MediaFileController;
use App\Http\Controllers\PublicWebsiteController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

Route::get('/', [PublicWebsiteController::class, 'root'])->name('home');
Route::get('/media/{path}', [MediaFileController::class, 'show'])->where('path', '.+')->name('media.show');
Route::get('/{locale}', [PublicWebsiteController::class, 'home'])->whereIn('locale', ['en', 'ar'])->name('localized.home');
Route::get('/contact', fn () => redirect()->route('localized.contact.show', ['locale' => 'en']))->name('contact.redirect');
Route::get('/{locale}/contact', [PublicWebsiteController::class, 'contact'])->whereIn('locale', ['en', 'ar'])->name('localized.contact.show');
Route::post('/{locale}/contact', [ContactController::class, 'store'])->whereIn('locale', ['en', 'ar'])->middleware(['throttle:5,1', ProtectAgainstSpam::class])->name('localized.contact.store');
Route::post('/contact', [ContactController::class, 'store'])->middleware(['throttle:5,1', ProtectAgainstSpam::class])->name('contact.store');
Route::get('/{locale}/{section}', [PublicWebsiteController::class, 'index'])->whereIn('locale', ['en', 'ar'])->whereIn('section', ['services', 'projects', 'products', 'templates', 'insights'])->name('public.content.index');
Route::get('/{locale}/{section}/{slug}', [PublicWebsiteController::class, 'show'])->whereIn('locale', ['en', 'ar'])->whereIn('section', ['services', 'projects', 'products', 'templates', 'insights'])->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('public.content.show');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');

Route::prefix('admin-panel')->name('admin.')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('authenticate');

    Route::middleware('admin')->group(function (): void {
        Route::view('/', 'admin.dashboard')->name('dashboard');
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

        Route::get('/access', [AccessManagementController::class, 'index'])->name('access.index');
        Route::post('/access/roles', [AccessManagementController::class, 'storeRole'])->name('access.roles.store');
        Route::put('/access/roles/{role}', [AccessManagementController::class, 'updateRole'])->name('access.roles.update');
        Route::post('/access/users', [AccessManagementController::class, 'storeUser'])->name('access.users.store');
        Route::put('/access/users/{user}/role', [AccessManagementController::class, 'updateUserRole'])->name('access.users.role.update');

        Route::get('/clients', [ClientProjectController::class, 'clients'])->middleware('permission:clients.view')->name('clients.index');
        Route::get('/clients/create', [ClientProjectController::class, 'createClient'])->middleware('permission:clients.create')->name('clients.create');
        Route::post('/clients', [ClientProjectController::class, 'storeClient'])->middleware('permission:clients.create')->name('clients.store');
        Route::put('/clients/{client}', [ClientProjectController::class, 'updateClient'])->middleware('permission:clients.update')->name('clients.update');
        Route::get('/clients/{client}', [ClientProjectController::class, 'showClient'])->middleware('permission:clients.view')->name('clients.show');
        Route::get('/clients/{client}/delivery-projects/create', [ClientProjectController::class, 'createProject'])->middleware('permission:projects.create')->name('delivery-projects.create');
        Route::post('/clients/{client}/delivery-projects', [ClientProjectController::class, 'storeProject'])->middleware('permission:projects.create')->name('delivery-projects.store');
        Route::get('/delivery-projects/{project}', [ClientProjectController::class, 'showProject'])->middleware('permission:projects.view')->name('delivery-projects.show');
        Route::put('/delivery-projects/{project}', [ClientProjectController::class, 'updateProject'])->middleware('permission:projects.update')->name('delivery-projects.update');
        Route::post('/delivery-projects/{project}/updates', [ClientProjectController::class, 'storeUpdate'])->middleware('permission:project_updates.create')->name('delivery-projects.updates.store');
        Route::post('/project-updates/{update}/send', [ClientProjectController::class, 'sendUpdate'])->middleware('permission:project_updates.send')->name('project-updates.send');

        Route::get('/inquiries', [InquiryController::class, 'index'])->middleware('permission:inquiries.view')->name('inquiries.index');
        Route::get('/inquiries/{inquiry}/edit', [InquiryController::class, 'edit'])->middleware('permission:inquiries.view')->name('inquiries.edit');
        Route::put('/inquiries/{inquiry}', [InquiryController::class, 'update'])->middleware('permission:inquiries.update')->name('inquiries.update');

        // CMS access is granted per module and action, not by hiding navigation links.
        Route::get('/services', [ServiceController::class, 'index'])->middleware('permission:cms.services.view')->name('services.index');
        Route::get('/services/create', [ServiceController::class, 'create'])->middleware('permission:cms.services.create')->name('services.create');
        Route::post('/services', [ServiceController::class, 'store'])->middleware('permission:cms.services.create')->name('services.store');
        Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->middleware('permission:cms.services.update')->name('services.edit');
        Route::match(['put', 'patch'], '/services/{service}', [ServiceController::class, 'update'])->middleware('permission:cms.services.update')->name('services.update');

        Route::get('/projects', [ProjectController::class, 'index'])->middleware('permission:cms.projects.view')->name('projects.index');
        Route::get('/projects/create', [ProjectController::class, 'create'])->middleware('permission:cms.projects.create')->name('projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->middleware('permission:cms.projects.create')->name('projects.store');
        Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->middleware('permission:cms.projects.update')->name('projects.edit');
        Route::match(['put', 'patch'], '/projects/{project}', [ProjectController::class, 'update'])->middleware('permission:cms.projects.update')->name('projects.update');

        Route::get('/products', [ProductController::class, 'index'])->middleware('permission:cms.products.view')->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->middleware('permission:cms.products.create')->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->middleware('permission:cms.products.create')->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->middleware('permission:cms.products.update')->name('products.edit');
        Route::match(['put', 'patch'], '/products/{product}', [ProductController::class, 'update'])->middleware('permission:cms.products.update')->name('products.update');

        Route::get('/templates', [WebsiteTemplateController::class, 'index'])->middleware('permission:cms.templates.view')->name('templates.index');
        Route::get('/templates/create', [WebsiteTemplateController::class, 'create'])->middleware('permission:cms.templates.create')->name('templates.create');
        Route::post('/templates', [WebsiteTemplateController::class, 'store'])->middleware('permission:cms.templates.create')->name('templates.store');
        Route::get('/templates/{template}/edit', [WebsiteTemplateController::class, 'edit'])->middleware('permission:cms.templates.update')->name('templates.edit');
        Route::match(['put', 'patch'], '/templates/{template}', [WebsiteTemplateController::class, 'update'])->middleware('permission:cms.templates.update')->name('templates.update');

        Route::get('/insights', [InsightController::class, 'index'])->middleware('permission:cms.insights.view')->name('insights.index');
        Route::get('/insights/create', [InsightController::class, 'create'])->middleware('permission:cms.insights.create')->name('insights.create');
        Route::post('/insights', [InsightController::class, 'store'])->middleware('permission:cms.insights.create')->name('insights.store');
        Route::get('/insights/{insight}/edit', [InsightController::class, 'edit'])->middleware('permission:cms.insights.update')->name('insights.edit');
        Route::match(['put', 'patch'], '/insights/{insight}', [InsightController::class, 'update'])->middleware('permission:cms.insights.update')->name('insights.update');

        Route::get('/insight-categories', [InsightCategoryController::class, 'index'])->middleware('permission:cms.insights.view')->name('insight-categories.index');
        Route::get('/insight-categories/create', [InsightCategoryController::class, 'create'])->middleware('permission:cms.insights.create')->name('insight-categories.create');
        Route::post('/insight-categories', [InsightCategoryController::class, 'store'])->middleware('permission:cms.insights.create')->name('insight-categories.store');
        Route::get('/insight-categories/{insight_category}/edit', [InsightCategoryController::class, 'edit'])->middleware('permission:cms.insights.update')->name('insight-categories.edit');
        Route::match(['put', 'patch'], '/insight-categories/{insight_category}', [InsightCategoryController::class, 'update'])->middleware('permission:cms.insights.update')->name('insight-categories.update');
    });
});
