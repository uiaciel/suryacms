<?php

namespace Uiaciel\SuryaCms\Livewire\Admin\Themes;

use Livewire\Component;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MakeEditorTheme extends Component
{
    public $sessionId;
    public $theme;

    public $fileTree = [];
    public $selectedFile = null;      // Relative path (misal: views/app.blade.php)
    public $selectedFilePath = null;  // Absolute path di server
    public $fileContent = '';
    public $isDirty = false;

    public $previewMode = 'editor';
    public $searchQuery = '';

    public function mount($sessionId)
    {
        $this->sessionId = $sessionId;
        $this->theme = Str::before($sessionId, '-');
        $this->loadFileTree();
    }

    /**
     * Menyediakan data snippets untuk Alpine.js.
     * PENTING: Menggunakan tanda kutip tunggal ('...') agar PHP TIDAK mengevaluasi variabel Blade.
     */
    public function getEditorSnippetsProperty(): array
    {
        return [
            'variables' => [
                ['label' => 'Nama situs', 'variable' => '$setting?->sitename', 'snippet' => '{{ $setting?->sitename }}'],
                ['label' => 'Tagline', 'variable' => '$setting?->tagline', 'snippet' => '{{ $setting?->tagline }}'],
                ['label' => 'Deskripsi situs', 'variable' => '$setting?->description', 'snippet' => '{{ $setting?->description }}'],
                ['label' => 'URL situs', 'variable' => '$setting?->url', 'snippet' => '{{ $setting?->url }}'],
                ['label' => 'Logo situs', 'variable' => '$setting?->logo', 'snippet' => '<img src="{{ asset($setting?->logo) }}" alt="{{ $setting?->sitename }}">'],
                ['label' => 'Favicon', 'variable' => '$setting?->favicon', 'snippet' => '<link rel="icon" href="{{ asset($setting?->favicon) }}">'],
                ['label' => 'Bahasa situs', 'variable' => '$setting?->language', 'snippet' => '{{ $setting?->language }}'],
                ['label' => 'Email kontak', 'variable' => '$setting?->email', 'snippet' => '<a href="mailto:{{ $setting?->email }}">{{ $setting?->email }}</a>'],
                ['label' => 'Telepon', 'variable' => '$setting?->phone', 'snippet' => '{{ $setting?->phone }}'],
                ['label' => 'WhatsApp', 'variable' => '$setting?->whatsapp', 'snippet' => '<a href="https://wa.me/{{ $setting?->whatsapp }}">WhatsApp</a>'],
                ['label' => 'Facebook', 'variable' => '$setting?->facebook', 'snippet' => '{{ $setting?->facebook }}'],
                ['label' => 'Instagram', 'variable' => '$setting?->instagram', 'snippet' => '{{ $setting?->instagram }}'],
                ['label' => 'Twitter', 'variable' => '$setting?->twitter', 'snippet' => '{{ $setting?->twitter }}'],
                ['label' => 'LinkedIn', 'variable' => '$setting?->linkedin', 'snippet' => '{{ $setting?->linkedin }}'],
                ['label' => 'YouTube', 'variable' => '$setting?->youtube', 'snippet' => '{{ $setting?->youtube }}'],
                ['label' => 'TikTok', 'variable' => '$setting?->tiktok', 'snippet' => '{{ $setting?->tiktok }}'],
                ['label' => 'Warna utama', 'variable' => '$setting?->color_primary', 'snippet' => 'var(--color-primary, {{ $setting?->color_primary ?? "#3b82f6" }})'],
            ],

            'globalData' => [
                ['label' => 'Tema aktif', 'variable' => '$activeTheme', 'snippet' => '{{ $activeTheme }}'],
                ['label' => 'Menu navigasi', 'variable' => '$menus', 'snippet' => '@foreach($menus as $menu)' . "\n" . '    <a href="{{ $menu->link }}">{{ $menu->name }}</a>' . "\n" . '@endforeach'],
                ['label' => 'Kategori', 'variable' => '$categories', 'snippet' => '@foreach($categories as $category)' . "\n" . '    <a href="{{ url("/category/".$category->slug) }}">{{ $category->name }}</a>' . "\n" . '@endforeach'],
                ['label' => 'Semua artikel', 'variable' => '$posts', 'snippet' => '@foreach($posts as $post)' . "\n" . '    <a href="{{ url("/".$post->slug) }}">{{ $post->title }}</a>' . "\n" . '@endforeach'],
                ['label' => 'Artikel terbaru', 'variable' => '$latestposts', 'snippet' => '@foreach($latestposts as $post)' . "\n" . '    <a href="{{ url("/".$post->slug) }}">{{ $post->title }}</a>' . "\n" . '@endforeach'],
                ['label' => 'Artikel populer', 'variable' => '$topposts', 'snippet' => '@foreach($topposts as $post)' . "\n" . '    <a href="{{ url("/".$post->slug) }}">{{ $post->title }}</a>' . "\n" . '@endforeach'],
                ['label' => 'Halaman', 'variable' => '$pages', 'snippet' => '@foreach($pages as $page)' . "\n" . '    <a href="{{ url("/".$page->slug) }}">{{ $page->title }}</a>' . "\n" . '@endforeach'],
                ['label' => 'Tag cloud', 'variable' => '$tagcloud', 'snippet' => '@foreach($tagcloud as $tag => $count)' . "\n" . '    <a href="{{ url("/tag/".Str::slug($tag)) }}">{{ $tag }} ({{ $count }})</a>' . "\n" . '@endforeach'],
                ['label' => 'Video YouTube', 'variable' => '$youtubes', 'snippet' => '@foreach($youtubes as $video)' . "\n" . '    <iframe src="https://www.youtube.com/embed/{{ $video->video_id }}" title="{{ $video->title }}" allowfullscreen></iframe>' . "\n" . '@endforeach'],
                ['label' => 'Slider / galeri', 'variable' => '$sliders', 'snippet' => '@foreach($sliders as $slide)' . "\n" . '    <img src="{{ asset("storage/".$slide->image_path) }}" alt="{{ $slide->name }}">' . "\n" . '@endforeach'],
                ['label' => 'Galeri', 'variable' => '$gallery', 'snippet' => '@foreach($gallery as $image)' . "\n" . '    <img src="{{ asset("storage/".$image->image_path) }}" alt="{{ $image->name }}">' . "\n" . '@endforeach'],
                ['label' => 'Pengaturan global', 'variable' => '$globalsetting', 'snippet' => '@foreach($globalsetting as $siteSetting)' . "\n" . '    {{ $siteSetting->sitename }}' . "\n" . '@endforeach'],
                ['label' => 'Bahasa', 'variable' => '$lang', 'snippet' => '@foreach($lang as $language)' . "\n" . '    {{ $language->name }}' . "\n" . '@endforeach'],
                ['label' => 'Kontak masuk', 'variable' => '$contacts', 'snippet' => '@foreach($contacts as $contact)' . "\n" . '    <a href="mailto:{{ $contact->email }}">{{ $contact->name }}</a>' . "\n" . '@endforeach'],
            ],

            'blade' => [
                ['label' => 'If / Else', 'snippet' => '@if($condition)' . "\n" . '    ' . "\n" . '@else' . "\n" . '    ' . "\n" . '@endif'],
                ['label' => 'Foreach Loop', 'snippet' => '@foreach($items as $item)' . "\n" . '    ' . "\n" . '@endforeach'],
                ['label' => 'Include plugin tema', 'snippet' => '@include("frontend.".$activeTheme.".plugin.blog")'],
                ['label' => 'Path asset tema', 'snippet' => '{{ asset("frontend/".$activeTheme."/css/style.css") }}'],
                ['label' => 'Gambar artikel', 'snippet' => '<img src="{{ $post->gambar() }}" alt="{{ $post->title }}">'],
                ['label' => 'Kutipan artikel', 'snippet' => '{{ Str::limit(strip_tags($post->content), 150) }}'],
                ['label' => 'Tanggal artikel', 'snippet' => '{{ \Carbon\Carbon::parse($post->datepublish)->format("d M Y") }}'],
                ['label' => 'Tag artikel', 'snippet' => '@foreach(array_filter(explode(",", $post->tags)) as $tag)' . "\n" . '    <a href="{{ url("/tag/".Str::slug(trim($tag))) }}">{{ trim($tag) }}</a>' . "\n" . '@endforeach'],
                ['label' => 'SEO meta tag', 'snippet' => '{{ seo_meta($seo ?? null) }}'],
                ['label' => 'Google Analytics', 'snippet' => '@if($setting?->google_analytics)' . "\n" . '    {!! $setting->google_analytics !!}' . "\n" . '@endif'],
                ['label' => 'Konten halaman builder', 'snippet' => '@if($page?->is_builder)' . "\n" . '    {!! $page->html !!}' . "\n" . '    <style>{!! $page->css !!}</style>' . "\n" . '@else' . "\n" . '    {!! $page->content !!}' . "\n" . '@endif'],
            ],
        ];
    }

    private function buildTree($dir, $prefix = '')
    {
        $items = [];
        if (!File::isDirectory($dir)) return $items;

        $dirs = collect(File::directories($dir))->sort()->values();
        $files = collect(File::files($dir))->sort()->values();

        // 1. Proses Direktori
        foreach ($dirs as $d) {
            $name = basename($d);
            $items[] = [
                'name' => $name,
                'type' => 'dir',
                'path' => $prefix . $name, // Selalu gunakan forward slash
                'depth' => substr_count($prefix, '/'),
                'is_root' => $prefix === ''
            ];
            $items = array_merge($items, $this->buildTree($d, $prefix . $name . '/'));
        }

        // 2. Proses File
        $allowedExt = ['php', 'blade', 'css', 'js', 'json', 'html', 'txt', 'xml'];
        foreach ($files as $f) {
            $name = $f->getFilename();
            $ext = $f->getExtension();

            if (in_array($ext, $allowedExt)) {
                $items[] = [
                    'name' => $name,
                    'type' => 'file',
                    'path' => $prefix . $name, // Selalu gunakan forward slash
                    'ext' => $ext,
                    'depth' => substr_count($prefix, '/'),
                    'size' => $f->getSize()
                ];
            }
        }
        return $items;
    }

    public function loadFileTree()
    {
        $viewsDir = resource_path("views/frontend/{$this->theme}");
        $publicDir = public_path("frontend/{$this->theme}");
        $tree = [];

        if (File::isDirectory($viewsDir)) {
            $tree[] = ['name' => 'views', 'type' => 'dir', 'path' => 'views', 'depth' => 0, 'is_root' => true];
            $tree = array_merge($tree, $this->buildTree($viewsDir, 'views/'));
        }

        if (File::isDirectory($publicDir)) {
            $tree[] = ['name' => 'public', 'type' => 'dir', 'path' => 'public', 'depth' => 0, 'is_root' => true];
            $tree = array_merge($tree, $this->buildTree($publicDir, 'public/'));
        }

        $this->fileTree = $tree;
    }

    public function selectFile(string $relativePath)
    {
        $this->selectedFile = $relativePath;
        $this->isDirty = false;

        // Rekonstruksi path absolut di backend (AMAN dari backslash Windows)
        if (str_starts_with($relativePath, 'views/')) {
            $subPath = Str::after($relativePath, 'views/');
            $this->selectedFilePath = resource_path("views/frontend/{$this->theme}/{$subPath}");
        } elseif (str_starts_with($relativePath, 'public/')) {
            $subPath = Str::after($relativePath, 'public/');
            $this->selectedFilePath = public_path("frontend/{$this->theme}/{$subPath}");
        } else {
            $this->selectedFilePath = null;
        }

        try {
            if ($this->selectedFilePath && File::exists($this->selectedFilePath)) {
                $this->fileContent = File::get($this->selectedFilePath);
            } else {
                $this->fileContent = "/* File tidak ditemukan atau tidak dapat dibaca. */";
            }
        } catch (\Exception $e) {
            $this->fileContent = "/* Gagal membaca file: " . $e->getMessage() . " */";
            Log::error('Failed to read theme file', ['path' => $this->selectedFilePath, 'error' => $e->getMessage()]);
        }

        // Kirim event ke Alpine.js untuk mengupdate textarea (karena menggunakan wire:ignore)
        $this->dispatch('file-loaded', content: $this->fileContent, file: $this->selectedFile);
    }

    public function saveFile(string $newContent)
    {
        if (!$this->selectedFilePath) return;

        try {
            File::put($this->selectedFilePath, $newContent);
            $this->fileContent = $newContent;
            $this->isDirty = false;
            $this->dispatch('file-saved', content: $newContent);

            // Gunakan toast atau session flash sesuai sistem notifikasi Anda
            session()->flash('success', "File {$this->selectedFile} berhasil disimpan.");
        } catch (\Exception $e) {
            session()->flash('error', "Gagal menyimpan file: " . $e->getMessage());
            Log::error('Failed to save theme file', ['path' => $this->selectedFilePath, 'error' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('suryacms::livewire.admin.themes.make-editor-theme')
            ->layout('suryacms::layouts.app');
    }
}
