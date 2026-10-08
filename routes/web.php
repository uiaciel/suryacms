<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Uiaciel\SuryaCms\Http\Controllers\AboutController;
use Uiaciel\SuryaCms\Http\Controllers\AdminController;
use Uiaciel\SuryaCms\Http\Controllers\AuthenticatedSessionController;
use Uiaciel\SuryaCms\Http\Controllers\BackupController;
use Uiaciel\SuryaCms\Http\Controllers\FrontendController;
use Uiaciel\SuryaCms\Http\Controllers\GuideController;
use Uiaciel\SuryaCms\Http\Controllers\ProfileController;
use Uiaciel\SuryaCms\Livewire\Admin;
use Uiaciel\SuryaCms\Livewire\Admin\Backup;
use Uiaciel\SuryaCms\Livewire\Admin\Contact;
use Uiaciel\SuryaCms\Livewire\Admin\FileCheck;
use Uiaciel\SuryaCms\Livewire\Admin\Gallery;
use Uiaciel\SuryaCms\Livewire\Admin\Menu\MenuCreate;
use Uiaciel\SuryaCms\Livewire\Admin\Menu\MenuList;
use Uiaciel\SuryaCms\Livewire\Admin\Page\PageCreate;
use Uiaciel\SuryaCms\Livewire\Admin\Page\PageEdit;
use Uiaciel\SuryaCms\Livewire\Admin\Page\PageIndex;
use Uiaciel\SuryaCms\Livewire\Admin\PageBuilder\HomepageBuilder;
use Uiaciel\SuryaCms\Livewire\Admin\PageBuilder\IndexPageBuilder;
use Uiaciel\SuryaCms\Livewire\Admin\Post\GeneratePost;
use Uiaciel\SuryaCms\Livewire\Admin\Post\PostCreate;
use Uiaciel\SuryaCms\Livewire\Admin\Post\PostEdit;
use Uiaciel\SuryaCms\Livewire\Admin\Post\PostIndex;
use Uiaciel\SuryaCms\Livewire\Admin\SearchResult;
use Uiaciel\SuryaCms\Livewire\Admin\Themes\BuilderTheme;
use Uiaciel\SuryaCms\Livewire\Admin\Themes\ConvertTheme;
use Uiaciel\SuryaCms\Livewire\Admin\Themes\CreateTheme;
use Uiaciel\SuryaCms\Livewire\Admin\Themes\DocsTheme;
use Uiaciel\SuryaCms\Livewire\Admin\Themes\EditorTheme;
use Uiaciel\SuryaCms\Livewire\Admin\Themes\SettingTheme;
use Uiaciel\SuryaCms\Livewire\Admin\Youtube\YoutubeCreate;
use Uiaciel\SuryaCms\Livewire\Admin\Youtube\YoutubeList;
use Uiaciel\SuryaCms\Livewire\SettingWeb;
use Uiaciel\SuryaCms\Livewire\System\FullRestore;
use Uiaciel\SuryaCms\Models\Setting;

Route::middleware(['web', 'auth'])->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware(['web', 'guest']);

Route::prefix(config('suryacms.admin_prefix', 'admin'))
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
                Route::get('generate', GeneratePost::class)->name('generate');
            });

        Route::prefix('menus')
            ->name('menu.')
            ->group(function () {
                Route::get('/', MenuCreate::class)->name('create');
                // Route::get('menus', MenuList::class)->name('index');
                Route::get('getmenus/posts', [FrontendController::class, 'getPosts'])->name('getPosts');
                Route::get('getmenus/pages', [FrontendController::class, 'getPages'])->name('getPages');
                Route::get('getmenus/categories', [FrontendController::class, 'getCategories'])->name('getCategories');
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
                Route::get('/', Gallery::class)->name('index');
                // Route::get('/', [AdminController::class, 'gallery'])->name('index');
                Route::post('/', [AdminController::class, 'saveGallery'])->name('store');
                Route::post('{id}/edit', [AdminController::class, 'editGallery'])->name('edit');
            });
        Route::get('/system', FileCheck::class)->name('suryacms.admin.system');
        Route::get('/system/restore', FullRestore::class)->name('suryacms.admin.restore');
        Route::get('/system/backups', Backup::class)->name('suryacms.admin.backups');
        Route::get('/system/backup/download/{filename}', [BackupController::class, 'download'])
            ->name('suryacms.admin.backup.download');
        Route::get('/system/backups/download/{folder}/{filename}', [BackupController::class, 'download'])
            ->name('suryacms.admin.partial-backup.download');

        Route::get('contacts', Contact::class)->name('contact.index');

        Route::get('backups', Backup::class)->name('backup.index');

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

        Route::get('/test-session/{type}', function ($type) {
            switch ($type) {
                case 'success':
                    session()->flash('success', 'Data PDF berhasil diupload dan disimpan ke galeri!');
                    break;
                case 'error':
                    session()->flash('error', 'Gagal terhubung ke server storage. Silakan coba lagi.');
                    break;
                case 'info':
                    session()->flash('info', 'Sistem akan melakukan maintenance pada pukul 00:00 WIB.');
                    break;
                case 'warning':
                    session()->flash('warning', 'Ukuran file Anda mendekati batas maksimal 20MB.');
                    break;
                case 'validation':
                    // Membuat instance error bag palsu untuk mengetes $errors->any()
                    $validator = Validator::make([], []);
                    $validator->errors()->add('pdfFile', 'File yang diupload wajib berformat .pdf');
                    $validator->errors()->add('title', 'Judul dokumen tidak boleh kosong.');
                    session()->flash('errors', $validator->errors());
                    break;
            }

            // Redirect kembali ke halaman sebelumnya (halaman tempat Anda menaruh tombol test)
            return redirect()->back();
        })->name('test.session');
    });

Route::middleware(['web', 'suryacms.maintenance', 'suryacms.locale'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Detect Setting (safe)
    |--------------------------------------------------------------------------
    */
    try {
        $setting = Schema::hasTable('settings')
            ? Setting::first()
            : null;
    } catch (Exception $e) {
        $setting = null;
    }
});

require __DIR__.'/frontend.php';
require __DIR__.'/api.php';

Route::get('suryacms/test', fn () => 'SuryaCMS aktif!');
Route::get('suryacms/sw.js', function () {

    return response(<<<'JS'
    self.addEventListener('install', event => {
        self.skipWaiting();
    });

    self.addEventListener('activate', event => {
        event.waitUntil(clients.claim());
    });
    JS
        , 200)->header('Content-Type', 'application/javascript');

});
Route::get('suryacms/manifest.json', [AdminController::class, 'manifest'])->name('admin.manifest');
