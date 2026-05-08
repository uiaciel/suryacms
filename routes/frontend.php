<?php

use Uiaciel\SuryaCms\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| FRONTEND ROUTES - SEO-FRIENDLY MULTILINGUAL
|--------------------------------------------------------------------------
|
| URL Structure:
| - Default language (e.g., 'id'): / /profil-perusahaan /category/tech
| - Non-default languages (e.g., 'en'): /en /en/profil-perusahaan /en/category/tech
|
| This approach is SEO-friendly as default language has no prefix
*/

try {

    // Get settings
    $setting = cache()->rememberForever('cms_settings', function () {
        try {
            return Schema::hasTable('settings')
                ? \Uiaciel\SuryaCms\Models\Setting::first()
                : null;
        } catch (\Exception $e) {
            return null;
        }
    });

} catch (\Exception $e) {

    $setting = Schema::hasTable('settings')
            ? \Uiaciel\SuryaCms\Models\Setting::first()
            : null;
}

$isMultilingual = ($setting && $setting->is_multilingual === 'Yes');
$defaultLang = $setting->language ?? 'id';

// Define route middleware
$routeMiddleware = ['web', 'suryacms.locale'];

// Build excluded slugs pattern untuk wildcard route
$excludedSlugs = ['login', 'register', 'password', 'email', 'logout', 'contact-us', 'homepage-builder', 'suryacms', 'api', '_debugbar', 'horizon', 'telescope', 'category', 'media'];

// Jika multilingual, exclude language codes juga
if ($isMultilingual) {
    $activeLangs = active_languages();
    $excludedSlugs = array_merge($excludedSlugs, $activeLangs);
}

$excludedPattern = implode('|', array_map('preg_quote', $excludedSlugs));

// Helper untuk register frontend routes
$registerFrontendRoutes = function() use ($excludedPattern) {
    Route::get('/', [FrontendController::class, 'index'])->name('homepage');
    Route::get('category', [FrontendController::class, 'categoryIndex'])->name('frontend.category.index');
    Route::get('category/{slug}', [FrontendController::class, 'category'])->name('frontend.category.show');
    Route::get('media/{slug}', [FrontendController::class, 'postshow'])->name('frontend.post.show');
    Route::get('contact-us', [FrontendController::class, 'contact'])->name('frontend.contact');
    Route::post('contact-us/send', [FrontendController::class, 'sendcontact'])->name('frontend.contact.send');

    // Wildcard page route - MUST BE LAST
    // Exclude: admin routes, API, debugbars, categories, media, dan language codes
    Route::get('{slug}', [FrontendController::class, 'pageshow'])
        ->where('slug', '^(?!' . $excludedPattern . ').*$')
        ->name('frontend.page.show');
};

if ($isMultilingual) {
    /*
    |--------------------------------------------------------------------------
    | DEFAULT LANGUAGE ROUTES (No Prefix) - SEO Friendly
    |--------------------------------------------------------------------------
    | Akses ke: /, /profil-perusahaan, /category/tech
    | Akan set locale ke default language (e.g., 'id')
    */
    Route::middleware($routeMiddleware)
        ->group(function() use ($registerFrontendRoutes) {
            $registerFrontendRoutes();
        });

    /*
    |--------------------------------------------------------------------------
    | NON-DEFAULT LANGUAGE ROUTES (With {lang} Prefix)
    |--------------------------------------------------------------------------
    | Akses ke: /en, /en/profil-perusahaan, /en/category/tech
    | Akan set locale ke language yang di-prefix
    */
    Route::group([
        'prefix' => '{lang}',
        'where' => ['lang' => '[a-z]{2}'],
        'middleware' => $routeMiddleware
    ], $registerFrontendRoutes);
} else {
    /*
    |--------------------------------------------------------------------------
    | SINGLE LANGUAGE ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['web'])
        ->group($registerFrontendRoutes);
}
