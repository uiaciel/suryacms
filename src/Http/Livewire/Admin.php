<?php

namespace Uiaciel\SuryaCms\Http\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Livewire\Component;
use Uiaciel\SuryaCms\Models\Category;
use Uiaciel\SuryaCms\Models\Language;
use Uiaciel\SuryaCMS\Models\Menu;
use Uiaciel\SuryaCms\Models\Page;
use Uiaciel\SuryaCms\Models\Post;
use Uiaciel\SuryaCms\Models\Setting;

class Admin extends Component
{
    public $categoryName;

    public $categories;

    public $categoryStatus = 'Publish';

    public $isEditMode = false;

    public $posts;

    public $pages;

    public $commandOutput = null;

    public $statusMessage = '';

    public $languageName;

    public $languageCode;

    public $languages;

    public $isLanguageEditMode = false;

    public $site_maintenance;

    public function mount()
    {
        // Check if setting exists, if not redirect to setting page for initial setup
        $setting = Setting::first();
        if (! $setting) {
            $this->redirect(route('admin.setting'), navigate: true);

            return;
        }

        $this->categories = Category::all();
        $this->languages = Language::all();
        $this->posts = Post::all();
        $this->pages = Page::all();
        $this->site_maintenance = (bool) ($setting->site_maintenance ?? false);
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }

    public function updatedSiteMaintenance($value)
    {
        $setting = Setting::first();
        if ($setting) {
            $setting->site_maintenance = $value;
            $setting->save();
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Success',
                'text' => 'Site maintenance mode '.($value ? 'enabled' : 'disabled').' successfully!',
            ]);
        } else {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Settings not found.',
            ]);
        }
    }

    public function generateOffline()
    {
        // Reset output sebelumnya
        $this->commandOutput = null;
        $this->statusMessage = 'Starting offline page generation...';

        try {
            // 1. Jalankan command Artisan
            Artisan::call('app:generate-offline');
            $output = Artisan::output();

            // 2. Set output sebagai string.
            // Gunakan nl2br untuk mengganti newline dengan <br>, dan e() untuk escaping,
            // memastikan Livewire hanya menyimpan string yang aman.
            $this->commandOutput = nl2br(e($output));

            // 3. Set pesan sukses
            $this->statusMessage = 'Generate Offline Successfully. Check output for details.';

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Success',
                'text' => 'Generate Offline Successfully.',
            ]);
        } catch (\Exception $e) {
            // Tangani error jika Artisan gagal total
            $this->statusMessage = 'An unexpected error occurred: '.$e->getMessage();
            $this->commandOutput = nl2br(e($e->getMessage()));

            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Failed to generate offline pages.',
            ]);
        }
    }

    public function generateSitemap()
    {

        $urls = collect();
        $now = now()->toAtomString();

        $urls->push([
            'loc' => url('/'),
            'lastmod' => $now,
            'priority' => '1.0',
        ]);

        $pages = Page::where('status', 'Publish')->get();
        foreach ($pages as $page) {
            $urls->push([
                'loc' => URL::to('/'.$page->slug),
                'lastmod' => Carbon::parse($page->updated_at)->toAtomString(),
                'priority' => '0.8',
            ]);
        }

        $posts = Post::where('status', 'Publish')->get();
        foreach ($posts as $post) {
            $urls->push([
                'loc' => URL::to('/media/'.$post->slug),
                'lastmod' => Carbon::parse($post->updated_at)->toAtomString(),
                'priority' => '0.7',
            ]);
        }

        $categories = Category::all();
        foreach ($categories as $cat) {
            $urls->push([
                'loc' => URL::to('/category/'.$cat->slug),
                'lastmod' => $now,
                'priority' => '0.6',
            ]);
        }

        $menus = Menu::all();

        foreach ($menus as $menu) {
            $urls->push([
                'loc' => url($menu->link),
                'lastmod' => Carbon::parse($menu->updated_at)->toAtomString(),
                'priority' => '0.5',
            ]);
        }

        event(new \Uiaciel\SuryaCms\Events\SitemapGenerating($urls));

        $xml = view('suryacms::livewire.admin.sitemap', ['urls' => $urls->toArray()])->render();

        File::put(public_path('sitemap.xml'), $xml);

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Success',
            'text' => 'Generate Sitemap Successfully.',
        ]);

        session()->flash('success', 'Generate Sitemap Successfully.');
    }

    public function saveCategory()
    {
        $this->validate([
            'categoryName' => 'required',
        ]);

        Category::create([
            'name' => $this->categoryName,
            'slug' => Str::slug($this->categoryName),
            'status' => $this->categoryStatus,
        ]);

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Data berhasil ditambahkan!',
        ]);

        session()->flash('success', 'Category Created Successfully.');
        $this->resetCategoryFields();
    }

    public function editCategory($id)
    {
        $category = Category::find($id);

        if ($category) {
            $this->categoryName = $category->name;
            $this->isEditMode = true;
        } else {
            session()->flash('error', 'Category Not Found.');
        }
    }

    public function updateCategory($id)
    {
        $this->validate([
            'categoryName' => 'required',
        ]);

        $category = Category::find($id);

        if ($category) {
            $category->update([
                'name' => $this->categoryName,
                'slug' => Str::slug($this->categoryName),
                'status' => $this->categoryStatus,
            ]);

            session()->flash('success', 'Category Updated Successfully.');
            $this->resetCategoryFields();
        } else {
            session()->flash('error', 'Category Not Found.');
        }
    }

    public function changeStatus($id)
    {
        $category = Category::find($id);

        if ($category) {
            $category->status = $category->status === 'Publish' ? 'Draft' : 'Publish';
            $category->save();

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Berhasil',
                'text' => 'Category Status Updated Successfully!',
            ]);
            $this->redirect(route('admin.dashboard'), navigate: true);
            session()->flash('success', 'Category Status Updated Successfully.');
        }
    }

    public function deleteCategory($id)
    {
        $category = Category::find($id);

        if ($category) {
            $category->delete();
            session()->flash('success', 'Category Deleted Successfully.');
        } else {
            session()->flash('error', 'Category Not Found.');
        }
    }

    public function saveLanguage()
    {
        $this->validate([
            'languageName' => 'required',
            'languageCode' => 'required',
        ]);

        $language = Language::create([
            'name' => $this->languageName,
            'code' => $this->languageCode,
            'status' => 'Publish',
        ]);

        Page::create(

            [
                'language_id' => $language->id,
                'translation_id' => null,
                'user_id' => Auth::id(),
                'title' => 'Homepage '.$language->name,
                'slug' => 'homepage-'.$language->code,
                'content' => null,
                'html' => null,
                'css' => null,
                'pdf' => null,
                'datepublish' => now(),
                'view' => 0,
                'status' => 'Publish',
            ]
        );

        $this->redirect(route('admin.dashboard'), navigate: true);

        session()->flash('success', 'Language Created Successfully.');
        $this->resetLanguageFields();
    }

    public function editLanguage($id)
    {
        $language = Language::find($id);

        if ($language) {
            $this->languageName = $language->name;
            $this->languageCode = $language->code;
            $this->isLanguageEditMode = true;
        } else {
            session()->flash('error', 'Language Not Found.');
        }
    }

    public function updateLanguage($id)
    {
        $this->validate([
            'languageName' => 'required',
            'languageCode' => 'required',
        ]);

        $language = Language::find($id);

        if ($language) {
            $language->update([
                'name' => $this->languageName,
                'code' => $this->languageCode,
            ]);

            session()->flash('success', 'Language Updated Successfully.');
            $this->resetLanguageFields();
        } else {
            session()->flash('error', 'Language Not Found.');
        }
    }

    public function deleteLanguage($id)
    {
        $language = Language::find($id);

        if ($language) {
            $language->delete();
            session()->flash('success', 'Language Deleted Successfully.');
        } else {
            session()->flash('error', 'Language Not Found.');
        }
    }

    private function resetCategoryFields()
    {
        $this->categoryName = '';
        $this->categoryStatus = 'Publish';
        $this->isEditMode = false;
    }

    private function resetLanguageFields()
    {
        $this->languageName = '';
        $this->languageCode = '';
        $this->isLanguageEditMode = false;
    }

    public function render()
    {
        return view('suryacms::livewire.admin')
            ->layout('suryacms::layouts.app');
    }
}
