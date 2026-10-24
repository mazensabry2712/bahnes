<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

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
    });
});
