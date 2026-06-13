<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Uiaciel\SuryaCms\Http\Controllers\{
    AboutController,
    AdminController,
    AuthenticatedSessionController,
    FrontendController,
    GuideController,
    ProfileController
};
use Uiaciel\SuryaCms\Http\Livewire\Admin;
use Uiaciel\SuryaCms\Http\Livewire\Admin\{
    Backup,
    Contact,
    FileCheck,
    SearchResult
};
use Uiaciel\SuryaCms\Http\Livewire\Admin\Menu\{MenuCreate, MenuList};
use Uiaciel\SuryaCms\Http\Livewire\Admin\Page\{PageCreate, PageEdit, PageIndex};
use Uiaciel\SuryaCms\Http\Livewire\Admin\PageBuilder\{HomepageBuilder, IndexPageBuilder};
use Uiaciel\SuryaCms\Http\Livewire\Admin\Post\{PostCreate, PostEdit, PostIndex};
use Uiaciel\SuryaCms\Http\Livewire\Admin\Themes\{
    BuilderTheme,
    ConvertTheme,
    CreateTheme,
    DocsTheme,
    EditorTheme,
    SettingTheme
};
use Uiaciel\SuryaCms\Http\Livewire\Admin\Youtube\{YoutubeCreate, YoutubeList};
use Uiaciel\SuryaCms\Http\Livewire\SettingWeb;

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
        Route::get('guide', [GuideController::class, 'index'])->name('guide.index');
        Route::get('guide/{package}', [GuideController::class, 'show'])->name('guide.show');

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
        Route::get('playground', fn () => view('suryacms::livewire.admin.page-builder.playground'))->name('playground');

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
                Route::get('/builder', BuilderTheme::class)->name('builder');
                Route::get('/docs', DocsTheme::class)->name('docs');
                Route::get('/create/{theme?}', CreateTheme::class)->name('create');
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
});

    require __DIR__.'/frontend.php';

    Route::get('suryacms/test', fn () => 'SuryaCMS aktif!');
