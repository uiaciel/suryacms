<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\Post;

use Uiaciel\SuryaCms\Models\Post;
use Uiaciel\SuryaCms\Models\Language;
use Uiaciel\SuryaCms\Models\Category;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PostEdit extends Component
{
    public $titlePage;
    public $postId;
    public $title;
    public $konten;
    public $datepublish;
    public $language_id;
    public $translation_id;
    public $category_id;
    public $tags;
    public $source_url;
    public $source_favicon;
    public $source_title;
    public $feature; // Akan menjadi boolean
    public $flash;   // Akan menjadi boolean
    public $status;
    public $languages;
    public $categories;

    public $setting;

    protected $listeners = ['tinymce_updated' => 'updateKonten'];

    public function mount()
    {
        $this->postId = request()->id;
        $post = Post::find($this->postId);

        if ($post) {
            $this->titlePage = "Edit Post";
            $this->title = $post->title;
            $this->konten = $post->content;
            // Pastikan format tanggal sesuai dengan input date HTML (YYYY-MM-DD)
            $this->datepublish = $post->datepublish;
            $this->language_id = $post->language_id;
            $this->translation_id = $post->translation_id;
            $this->category_id = $post->category_id;
            $this->source_url = $post->source_url;
            $this->source_favicon = $post->source_favicon;
            $this->source_title = $post->source_title;
            // Konversi 'Yes'/'No' dari database ke boolean untuk toggle switch
            $this->feature = ($post->feature === 'Yes');
            $this->flash = ($post->flash === 'Yes');
            $this->tags = $post->tags;
            $this->status = $post->status; // Ambil status dari post yang ada

            $this->languages = Language::all();
            $this->categories = Category::all();

            // Ambil setting dari database. Sesuaikan dengan cara Anda mendapatkan setting.
            // Contoh: $this->setting = \Uiaciel\SuryaCms\Models\Setting::first();
            $this->setting = (object)['is_multilingual' => 'No']; // Dummy, ganti ini
        } else {
            abort(404, 'Post not found');
        }
    }

    // Metode ini diperlukan jika TinyMCE tidak langsung update wire:model
    public function updateKonten($content)
    {
        $this->konten = $content;
    }

    // Metode untuk fetching terjemahan, jika diperlukan oleh blade
    public function getTranslations($languageId)
    {
        // Sesuaikan query jika Anda ingin hanya post yang belum punya terjemahan
        // dan tidak menyertakan post yang sedang diedit itu sendiri sebagai terjemahan
        return Post::where('language_id', '!=', $languageId)
            ->where('id', '!=', $this->postId) // Exclude current post
            ->get();
    }

    public function updatePost()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'konten' => 'required|string',
            'datepublish' => 'required|date',
            'language_id' => 'required|exists:languages,id',
            'translation_id' => 'nullable|exists:posts,id',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|string',
            'source_url' => 'nullable|url',
            'source_favicon' => 'nullable|string',
            'source_title' => 'nullable|string',
            'feature' => 'boolean', // Validasi untuk boolean
            'flash' => 'boolean',   // Validasi untuk boolean
            'status' => 'required|in:Publish,Draft', // Pastikan hanya 2 nilai ini
        ]);

        $post = Post::find($this->postId);

        if ($post) {
            $post->update([
                'title' => $this->title,
                'content' => $this->konten,
                'datepublish' => $this->datepublish,
                'language_id' => $this->language_id,
                'translation_id' => $this->translation_id,
                'category_id' => $this->category_id,
                'tags' => $this->tags,
                'source_url' => $this->source_url,
                'source_favicon' => $this->source_favicon,
                'source_title' => $this->source_title,
                // Konversi boolean dari toggle switch ke 'Yes'/'No' untuk database
                'feature' => $this->feature ? 'Yes' : 'No',
                'flash' => $this->flash ? 'Yes' : 'No',
                'status' => 'Publish', // Set status ke "Publish" saat tombol Update ditekan
                'slug' => Str::slug($this->title),
                // 'user_id' tidak perlu diupdate karena ini edit
            ]);

            session()->flash('success', 'Post updated successfully.');
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Success',
                'text' => 'Post updated Successfully!'
            ]);

            return $this->redirect('/admin/posts', navigate: true);
        } else {
            session()->flash('error', 'Post not found.');
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Post not found!'
            ]);
            return $this->redirect('/admin/posts', navigate: true);
        }
    }

    public function saveDraft()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'konten' => 'nullable|string', // Konten bisa kosong untuk draft
            'datepublish' => 'required|date',
            'language_id' => 'required|exists:languages,id',
            'translation_id' => 'nullable|exists:posts,id',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|string',
            'source_url' => 'nullable|url',
            'source_favicon' => 'nullable|string',
            'source_title' => 'nullable|string',
            'feature' => 'boolean',
            'flash' => 'boolean',
        ]);

        $post = Post::find($this->postId);

        if ($post) {
            $post->update([
                'title' => $this->title,
                'content' => $this->konten,
                'datepublish' => $this->datepublish,
                'language_id' => $this->language_id,
                'translation_id' => $this->translation_id,
                'category_id' => $this->category_id,
                'tags' => $this->tags,
                'source_url' => $this->source_url,
                'source_favicon' => $this->source_favicon,
                'source_title' => $this->source_title,
                'feature' => $this->feature ? 'Yes' : 'No',
                'flash' => $this->flash ? 'Yes' : 'No',
                'status' => 'Draft', // Set status ke "Draft"
                'slug' => Str::slug($this->title),
            ]);

            session()->flash('info', 'Post saved as draft.');
            $this->dispatch('swal', [
                'icon' => 'info',
                'title' => 'Saved!',
                'text' => 'Post saved as draft!'
            ]);
        } else {
            session()->flash('error', 'Post not found.');
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Post not found!'
            ]);
            return $this->redirect('/admin/posts', navigate: true);
        }
    }

    public function deletePost()
    {
        $post = Post::find($this->postId);

        if ($post) {
            $post->delete();

            session()->flash('success', 'Post deleted successfully.');
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Deleted!',
                'text' => 'Post deleted Successfully!'
            ]);

            return $this->redirect('/admin/posts', navigate: true);
        } else {
            session()->flash('error', 'Post not found.');
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Post not found!'
            ]);
        }
    }

    public function render()
    {
        return view('suryacms::livewire.admin.post.post-edit', [
            'setting' => $this->setting, // Pastikan $this->setting tersedia
        ])->layout('suryacms::layouts.app');
    }
}
