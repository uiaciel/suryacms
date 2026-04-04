<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Uiaciel\SuryaCms\Http\Controllers\AboutController;
use Uiaciel\SuryaCms\Http\Controllers\AdminController;
use Uiaciel\SuryaCms\Http\Controllers\AuthenticatedSessionController;
use Uiaciel\SuryaCms\Http\Controllers\DocumentationController;
use Uiaciel\SuryaCms\Http\Controllers\FrontendController;
use Uiaciel\SuryaCms\Http\Controllers\ProfileController;
use Uiaciel\SuryaCms\Http\Livewire\Admin;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Backup;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Themes\SettingTheme;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Themes\ConvertTheme;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Themes\EditorTheme;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Contact;
use Uiaciel\SuryaCms\Http\Livewire\Admin\FileCheck;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Menu\MenuCreate;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Menu\MenuList;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Page\PageCreate;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Page\PageEdit;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Page\PageIndex;
use Uiaciel\SuryaCms\Http\Livewire\Admin\PageBuilder\HomepageBuilder;
use Uiaciel\SuryaCms\Http\Livewire\Admin\PageBuilder\IndexPageBuilder;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Post\PostCreate;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Post\PostEdit;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Post\PostIndex;
use Uiaciel\SuryaCms\Http\Livewire\Admin\SearchResult;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Youtube\YoutubeCreate;
use Uiaciel\SuryaCms\Http\Livewire\Admin\Youtube\YoutubeList;
use Uiaciel\SuryaCms\Http\Livewire\SettingWeb;
use Uiaciel\SuryaCms\Models\Setting;

Route::middleware(['web', 'auth'])->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['web', 'auth'])
    ->group(function () {
        Route::get('/', Admin::class)->name('admin');
        Route::get('/', Admin::class)->name('dashboard');

        Route::get('about', [AboutController::class, 'index'])->name('about');
        Route::get('documentation', [DocumentationController::class, 'index'])->name('documentation');

        Route::get('setting', SettingWeb::class)->name('setting');
        Route::get('file-check', FileCheck::class)->name('file-check');

        Route::prefix('pages')
            ->name('page.')
            ->group(function () {
                Route::get('/', PageIndex::class)->name('index');
                Route::get('create', PageCreate::class)->name('create');
                Route::get('edit/{id}', PageEdit::class)->name('edit');
                Route::get('translations/{language_id}', [PageCreate::class, 'getTranslations'])->name('get-translations');
            });

        Route::prefix('posts')
            ->name('post.')
            ->group(function () {
                Route::get('/', PostIndex::class)->name('index');
                Route::get('create', PostCreate::class)->name('create');
                Route::get('edit/{id}', PostEdit::class)->name('edit');
                Route::get('translations/{language_id}', [PostCreate::class, 'getTranslations'])->name('get-translations');
            });

        Route::prefix('youtube')
            ->name('youtube.')
            ->group(function () {
                Route::get('/', YoutubeList::class)->name('index');
                Route::get('create', YoutubeCreate::class)->name('create');
            });

        Route::prefix('galleries')
            ->name('gallery.')
            ->group(function () {
                Route::get('/', [AdminController::class, 'gallery'])->name('index');
                Route::post('/', [AdminController::class, 'saveGallery'])->name('store');
                Route::post('{id}/edit', [AdminController::class, 'editGallery'])->name('edit');
            });

        Route::get('contacts', Contact::class)->name('contact.index');

        Route::get('backups', Backup::class)->name('backup.index');

        Route::get('menu', MenuCreate::class)->name('menu.create');
        Route::get('menus', MenuList::class)->name('menus.index');

        Route::get('getmenus/posts', [FrontendController::class, 'getPosts'])->name('menus.getPosts');
        Route::get('getmenus/pages', [FrontendController::class, 'getPages'])->name('menus.getPages');
        Route::get('getmenus/categories', [FrontendController::class, 'getCategories'])->name('menus.getCategories');

        Route::get('homepage-builder/{pageSlug}', HomepageBuilder::class)->name('homepage.builder');
        Route::get('page-builder', IndexPageBuilder::class)->name('page.builder');

        Route::get('search', SearchResult::class)->name('search.results');
        Route::get('users', [ProfileController::class, 'index'])->name('users.index');
        Route::get('users/create', [ProfileController::class, 'create'])->name('users.create');
        Route::post('users/verify-password', [ProfileController::class, 'verifyPassword'])->name('users.verifyPassword');
        Route::post('users/store', [ProfileController::class, 'store'])->name('users.store');

        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::post('upload', [AdminController::class, 'tinymce'])->name('upload');

        Route::prefix('themes')
            ->name('themes.')
            ->group(function () {
                Route::get('/', SettingTheme::class)->name('index');
                 Route::get('/generate', ConvertTheme::class)->name('convert.theme');
                Route::get('/editor', EditorTheme::class)->name('admin.theme.editor');
            });
    });

Route::middleware(['web', 'suryacms.maintenance', 'suryacms.locale'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Detect Setting (safe)
    |--------------------------------------------------------------------------
    */
    try {
        $setting = Schema::hasTable('settings')
            ? \Uiaciel\SuryaCms\Models\Setting::first()
            : null;
    } catch (\Exception $e) {
        $setting = null;
    }

    /*
    |--------------------------------------------------------------------------
    | HOMEPAGE (MULTILINGUAL ONLY HERE)
    |--------------------------------------------------------------------------
    */

    $setting = Schema::hasTable('settings') ? \Uiaciel\SuryaCms\Models\Setting::first() : null;
    $isMultilingual = ($setting->is_multilingual ?? 'No') === 'Yes';
    $defaultLang = $setting->language ?? 'id';

    if ($isMultilingual) {

    Route::get('/', function () use ($defaultLang) {
            return redirect()->to('/' . session('locale', $defaultLang));
        });

        Route::get('{lang}', [FrontendController::class, 'index'])
            ->where('lang', '[a-z]{2}')
            ->name('homepage');
    } else {
        Route::get('/', [FrontendController::class, 'index'])->name('homepage.single');

        Route::get('{lang}', function () { return redirect()->to('/'); })->where('lang', '[a-z]{2}');

    }

    /*
    |--------------------------------------------------------------------------
    | CONTENT ROUTES (NO MULTILINGUAL PREFIX)
    |--------------------------------------------------------------------------
    */

    Route::get('category', [FrontendController::class, 'categoryIndex'])
        ->name('frontend.category.index');

    Route::get('category/{slug}', [FrontendController::class, 'category'])
        ->where('slug', '[a-zA-Z0-9\-_]+')
        ->name('frontend.category.show');

    Route::get('media/{slug}', [FrontendController::class, 'postshow'])
        ->where('slug', '[a-zA-Z0-9\-_]+')
        ->name('frontend.post.show');

    Route::get('{slug}', [FrontendController::class, 'pageshow'])
        ->where('slug', '^(?!login|register|password|email|logout|contact-us|homepage-builder|suryacms|api|_debugbar|horizon|telescope).*$')
        ->name('frontend.page.show');

    /*
    |--------------------------------------------------------------------------
    | CONTACT
    |--------------------------------------------------------------------------
    */

    Route::get('contact-us', [FrontendController::class, 'contact'])
        ->name('frontend.contact');

    Route::post('contact-us/send', [FrontendController::class, 'sendcontact'])
        ->name('sendcontact');

    /*
    |--------------------------------------------------------------------------
    | TEST
    |--------------------------------------------------------------------------
    */

    Route::get('suryacms/test', fn () => 'SuryaCMS aktif!');
});
