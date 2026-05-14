<?php

use App\Http\Controllers\Admin;

// Dashboard
Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

// CMS Pages
Route::prefix('pages')->name('pages.')->group(function () {
    Route::get('/',             [Admin\PageController::class, 'index'])->name('index');
    Route::get('/create',       [Admin\PageController::class, 'create'])->name('create');
    Route::post('/',            [Admin\PageController::class, 'store'])->name('store');
    Route::get('/{page}/edit',  [Admin\PageController::class, 'edit'])->name('edit');
    Route::put('/{page}',       [Admin\PageController::class, 'update'])->name('update');
    Route::delete('/{page}',    [Admin\PageController::class, 'destroy'])->name('destroy');
    Route::post('/{page}/clone',[Admin\PageController::class, 'clone'])->name('clone');
});

// Portfolio
Route::prefix('portfolio')->name('portfolio.')->group(function () {
    Route::get('/',                [Admin\PortfolioController::class, 'index'])->name('index');
    Route::get('/create',          [Admin\PortfolioController::class, 'create'])->name('create');
    Route::post('/',               [Admin\PortfolioController::class, 'store'])->name('store');
    Route::get('/{project}/edit',  [Admin\PortfolioController::class, 'edit'])->name('edit');
    Route::put('/{project}',       [Admin\PortfolioController::class, 'update'])->name('update');
    Route::delete('/{project}',    [Admin\PortfolioController::class, 'destroy'])->name('destroy');
    Route::post('/sort',           [Admin\PortfolioController::class, 'sort'])->name('sort');
});

// Blog
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/',            [Admin\BlogController::class, 'index'])->name('index');
    Route::get('/create',      [Admin\BlogController::class, 'create'])->name('create');
    Route::post('/',           [Admin\BlogController::class, 'store'])->name('store');
    Route::get('/{post}/edit', [Admin\BlogController::class, 'edit'])->name('edit');
    Route::put('/{post}',      [Admin\BlogController::class, 'update'])->name('update');
    Route::delete('/{post}',   [Admin\BlogController::class, 'destroy'])->name('destroy');

    // Categories & Tags
    Route::apiResource('categories', Admin\BlogCategoryController::class)->except(['show']);
    Route::apiResource('tags',       Admin\BlogTagController::class)->except(['show']);
});

// Contacts / Inbox
Route::prefix('contacts')->name('contacts.')->group(function () {
    Route::get('/',              [Admin\ContactController::class, 'index'])->name('index');
    Route::get('/{contact}',     [Admin\ContactController::class, 'show'])->name('show');
    Route::post('/{contact}/read', [Admin\ContactController::class, 'markRead'])->name('read');
    Route::delete('/{contact}',  [Admin\ContactController::class, 'destroy'])->name('destroy');
    Route::get('/htmx/list',    [Admin\ContactController::class, 'htmxList'])->name('htmx.list'); // HTMX
});

// Services
Route::prefix('services')->name('services.')->group(function () {
    Route::get('/',              [Admin\ServiceController::class, 'index'])->name('index');
    Route::get('/create',        [Admin\ServiceController::class, 'create'])->name('create');
    Route::post('/',             [Admin\ServiceController::class, 'store'])->name('store');
    Route::get('/{service}/edit',[Admin\ServiceController::class, 'edit'])->name('edit');
    Route::put('/{service}',     [Admin\ServiceController::class, 'update'])->name('update');
    Route::delete('/{service}',  [Admin\ServiceController::class, 'destroy'])->name('destroy');
    Route::post('/sort',         [Admin\ServiceController::class, 'sort'])->name('sort');
});

// Media Library
Route::prefix('media')->name('media.')->group(function () {
    Route::get('/',           [Admin\MediaController::class, 'index'])->name('index');
    Route::post('/upload',    [Admin\MediaController::class, 'upload'])->name('upload');
    Route::delete('/{media}', [Admin\MediaController::class, 'destroy'])->name('destroy');
});

// Analytics
Route::get('/analytics', [Admin\AnalyticsController::class, 'index'])->name('analytics');
Route::get('/analytics/chart/{type}', [Admin\AnalyticsController::class, 'chart'])->name('analytics.chart'); // HTMX

// Settings
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/',                  [Admin\SettingsController::class, 'index'])->name('index');
    Route::put('/general',           [Admin\SettingsController::class, 'updateGeneral'])->name('general');
    Route::put('/seo',               [Admin\SettingsController::class, 'updateSeo'])->name('seo');
    Route::put('/social',            [Admin\SettingsController::class, 'updateSocial'])->name('social');
    Route::put('/pricing',           [Admin\SettingsController::class, 'updatePricing'])->name('pricing');
});

// Client Management (Admin only)
Route::middleware('role:admin')->prefix('clients')->name('clients.')->group(function () {
    Route::get('/',                 [Admin\ClientController::class, 'index'])->name('index');
    Route::get('/create',           [Admin\ClientController::class, 'create'])->name('create');
    Route::post('/',                [Admin\ClientController::class, 'store'])->name('store');
    Route::get('/{client}/edit',    [Admin\ClientController::class, 'edit'])->name('edit');
    Route::put('/{client}',         [Admin\ClientController::class, 'update'])->name('update');
    Route::delete('/{client}',      [Admin\ClientController::class, 'destroy'])->name('destroy');
});
