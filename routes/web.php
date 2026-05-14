<?php

use Illuminate\Support\Facades\Route;

// ─── Public Routes ───────────────────────────────────────────────────────────

Route::get('/', [\App\Http\Controllers\Public\HomeController::class, 'index'])->name('home');
Route::get('/about', [\App\Http\Controllers\Public\HomeController::class, 'about'])->name('about');
Route::get('/privacy', [\App\Http\Controllers\Public\HomeController::class, 'privacy'])->name('privacy');
Route::get('/contact', [\App\Http\Controllers\Public\ContactController::class, 'index'])->name('contact');

// ─── Contact (Rate Limited) ───────────────────────────────────────────────────

Route::middleware(['throttle:contact'])->group(function () {
    Route::post('/contact', [\App\Http\Controllers\Public\ContactController::class, 'store'])
         ->name('contact.store');
});

// ─── Services ────────────────────────────────────────────────────────────────

Route::prefix('services')->name('services.')->group(function () {
    Route::get('/',      [\App\Http\Controllers\Public\ServiceController::class, 'index'])->name('index');
    Route::get('/{slug}',[\App\Http\Controllers\Public\ServiceController::class, 'show'])->name('show');
});

// ─── Portfolio ────────────────────────────────────────────────────────────────

Route::prefix('portfolio')->name('portfolio.')->group(function () {
    Route::get('/',       [\App\Http\Controllers\Public\PortfolioController::class, 'index'])->name('index');
    Route::get('/filter', [\App\Http\Controllers\Public\PortfolioController::class, 'filter'])->name('filter');  // HTMX endpoint
    Route::get('/{slug}', [\App\Http\Controllers\Public\PortfolioController::class, 'show'])->name('show');
    Route::post('/{slug}/like', [\App\Http\Controllers\Public\PortfolioController::class, 'like'])->name('like');
});

// ─── Blog ─────────────────────────────────────────────────────────────────────

Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/',                [\App\Http\Controllers\Public\BlogController::class, 'index'])->name('index');
    Route::get('/search',          [\App\Http\Controllers\Public\BlogController::class, 'search'])->name('search');       // HTMX
    Route::get('/category/{slug}', [\App\Http\Controllers\Public\BlogController::class, 'category'])->name('category');
    Route::get('/tag/{slug}',      [\App\Http\Controllers\Public\BlogController::class, 'tag'])->name('tag');
    Route::get('/{slug}',          [\App\Http\Controllers\Public\BlogController::class, 'show'])->name('show');
});

// ─── SEO Files ────────────────────────────────────────────────────────────────

Route::get('/sitemap.xml', [\App\Http\Controllers\Public\SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt',  [\App\Http\Controllers\Public\SeoController::class, 'robots'])->name('robots');

// ─── Auth ─────────────────────────────────────────────────────────────────────

Route::middleware('guest')->group(function () {
    Route::get('/login',  [\App\Http\Controllers\Auth\LoginController::class, 'create'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'destroy'])
     ->middleware('auth')
     ->name('logout');

// ─── Admin Panel ──────────────────────────────────────────────────────────────

Route::prefix('admin')
     ->name('admin.')
     ->middleware(['auth', 'role:admin|editor'])
     ->group(base_path('routes/admin.php'));

// ─── Client Portal ────────────────────────────────────────────────────────────

Route::prefix('portal')
     ->name('portal.')
     ->middleware(['auth', 'role:client'])
     ->group(base_path('routes/portal.php'));
