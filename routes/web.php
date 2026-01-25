<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Uiaciel\SuryaCms\Http\Controllers\AdminController;
use Uiaciel\SuryaCms\Http\Controllers\AuthenticatedSessionController;
use Uiaciel\SuryaCms\Http\Controllers\FrontendController;
use Uiaciel\SuryaCms\Http\Controllers\ProfileController;
use Uiaciel\SuryaCms\Http\Livewire\Admin;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Backup;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Contact;
use Uiaciel\SuryaCms\Http\Livewire\Admin\FileCheck;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Menu\MenuCreate;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Menu\MenuList;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Page\PageCreate;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Page\PageEdit;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Page\PageIndex;
use Uiaciel\SuryaCms\Http\Livewire\Admin\PageBuilder\HomepageBuilder;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Post\PostCreate;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Post\PostEdit;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Post\PostIndex;
use Uiaciel\SuryaCms\Http\Livewire\Admin\SearchResult;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Youtube\YoutubeCreate;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Youtube\YoutubeList;
use Uiaciel\SuryaCms\Http\Livewire\SettingWeb;
use Uiaciel\SuryaCms\Models\Setting;

Route::middleware(['web', 'auth'])->group(function () {
    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// ============================================================================
// BACKEND ADMIN ROUTES - Protected with Authentication
// ============================================================================
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['web', 'auth'])
    ->group(function () {
        // Admin Dashboard
        Route::get('/', Admin::class)->name('admin');

        // Settings
        Route::get('setting', SettingWeb::class)->name('setting');
        Route::get('file-check', FileCheck::class)->name('file-check');
        // Route::get('theme', SettingTheme::class)->name('theme');

        // Pages Management
        Route::prefix('pages')
            ->name('page.')
            ->group(function () {
                Route::get('/', PageIndex::class)->name('index');
                Route::get('create', PageCreate::class)->name('create');
                Route::get('edit/{id}', PageEdit::class)->name('edit');
                Route::get('translations/{language_id}', [PageCreate::class, 'getTranslations'])->name('get-translations');
            });

        // Posts Management
        Route::prefix('posts')
            ->name('post.')
            ->group(function () {
                Route::get('/', PostIndex::class)->name('index');
                Route::get('create', PostCreate::class)->name('create');
                Route::get('edit/{id}', PostEdit::class)->name('edit');
                Route::get('translations/{language_id}', [PostCreate::class, 'getTranslations'])->name('get-translations');
            });

        // YouTube Videos Management
        Route::prefix('youtube')
            ->name('youtube.')
            ->group(function () {
                Route::get('/', YoutubeList::class)->name('index');
                Route::get('create', YoutubeCreate::class)->name('create');
            });

        // Gallery Management
        Route::prefix('galleries')
            ->name('gallery.')
            ->group(function () {
                Route::get('/', [AdminController::class, 'gallery'])->name('index');
                Route::post('/', [AdminController::class, 'saveGallery'])->name('store');
                Route::post('{id}/edit', [AdminController::class, 'editGallery'])->name('edit');
            });

        // Contacts
        Route::get('contacts', Contact::class)->name('contact.index');

        // Backups
        Route::get('backups', Backup::class)->name('backup.index');

        // Menus Management
        Route::get('menu', MenuCreate::class)->name('menu.create');
        Route::get('menus', MenuList::class)->name('menus.index');

        Route::get('getmenus/posts', [FrontendController::class, 'getPosts'])->name('menus.getPosts');
        Route::get('getmenus/pages', [FrontendController::class, 'getPages'])->name('menus.getPages');
        Route::get('getmenus/categories', [FrontendController::class, 'getCategories'])->name('menus.getCategories');

        // Homepage Builder
        Route::get('homepage-builder/{pageSlug}', HomepageBuilder::class)->name('homepage.builder');

        Route::get('search', SearchResult::class)->name('search.results');
        Route::get('users', [ProfileController::class, 'index'])->name('users.index');
        Route::get('users/create', [ProfileController::class, 'create'])->name('users.create');
        Route::post('users/verify-password', [ProfileController::class, 'verifyPassword'])->name('users.verifyPassword');
        Route::post('users/store', [ProfileController::class, 'store'])->name('users.store');

        // Route::view('profile', 'profile')->name('profile');
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // General Uploads
        Route::post('upload', [AdminController::class, 'tinymce'])->name('upload');
    });

// ============================================================================
// FRONTEND PUBLIC ROUTES
// ============================================================================
Route::middleware(['web', 'suryacms.maintenance'])->group(function () {
    try {
        if (Schema::hasTable('settings')) {
            $setting = Setting::first() ?? null;
        } else {
            $setting = null;
        }
    } catch (\Exception $e) {
        $setting = null;
    }

    $isMultilingual = $setting && isset($setting->is_multilingual) && $setting->is_multilingual === 'Yes';

    if ($isMultilingual) {
        // Multilingual Routes - Redirect root to default language
        if ($setting && isset($setting->language)) {
            $defaultLang = $setting->language === 'id' ? 'id' : 'en';
            Route::get('/', function () use ($defaultLang) {
                return redirect()->to("/{$defaultLang}/");
            });
        }

        // Language-specific homepage
        Route::get('/{lang}/', [FrontendController::class, 'index'])
            ->where('lang', 'id|en')
            ->name('homepage');

        // Language-specific category index (must be before catch-all)
        Route::get('/{lang}/category', [FrontendController::class, 'categoryIndex'])
            ->where('lang', 'id|en')
            ->name('frontend.category.index');

        // Language-specific post/media (must be before catch-all page)
        Route::get('/{lang}/media/{slug}', [FrontendController::class, 'postshow'])
            ->where('lang', 'id|en')
            ->where('slug', '.+')
            ->name('frontend.post.show');

        // Language-specific category detail (must be before catch-all page)
        Route::get('/{lang}/category/{slug}', [FrontendController::class, 'category'])
            ->where('lang', 'id|en')
            ->where('slug', '.+')
            ->name('frontend.category.show');

        // Language-specific page (catch-all for custom pages) - MUST be last
        Route::get('/{lang}/{slug}', [FrontendController::class, 'pageshow'])
            ->where('lang', 'id|en')
            ->where('slug', '^(?!admin).*')  // Exclude /admin/* routes
            ->name('frontend.page.show');
    } else {
        // Monolingual Routes
        Route::get('/', [FrontendController::class, 'single'])
            ->name('homepage.single');

        // Category index (must be before catch-all)
        Route::get('category', [FrontendController::class, 'categoryIndex'])
            ->name('frontend.category.index');

        // Post/media (must be before catch-all page)
        Route::get('media/{slug}', [FrontendController::class, 'postshow'])
            ->where('slug', '.+')
            ->name('frontend.post.show');

        // Category detail (must be before catch-all page)
        Route::get('category/{slug}', [FrontendController::class, 'category'])
            ->where('slug', '.+')
            ->name('frontend.category.show');

        // Catch-all page route - must be last
        Route::get('/{slug}', [FrontendController::class, 'pageshow'])
            ->where('slug', '^(?!login|admin).*')  // Exclude /admin/* routes
            ->name('frontend.page.show');
    }

    // Shared Frontend Routes (not language-specific)
    Route::get('contact-us', [FrontendController::class, 'contact'])->name('frontend.contact');
    Route::post('contact-us/send', [FrontendController::class, 'sendcontact'])->name('sendcontact');

    // Test route
    Route::get('suryacms/test', function () {
        return 'SuryaCMS aktif!';
    });
});
