<?php
// app/Http/Controllers/ThemeBladePreviewController.php

namespace Uiaciel\SuryaCms\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class ThemeBladePreviewController
{
    /**
     * Render blade theme preview dengan sample data.
     */
    public function render(string $sessionId)
    {
        $metaPath = storage_path("app/temp/themes/{$sessionId}/builder-meta.json");
        if (! File::exists($metaPath)) {
            abort(404, 'Preview session not found.');
        }

        $meta = json_decode(File::get($metaPath), true) ?: [];
        $theme = $meta['themeName'] ?? null;

        if (! $theme) {
            abort(404, 'Theme name missing.');
        }

        $viewPath = resource_path("views/frontend/{$theme}");
        if (! File::isDirectory($viewPath)) {
            abort(404, 'Theme views not found.');
        }

        // Pilih view utama: index.blade.php (yang extends app.blade.php)
        // Fallback: app.blade.php
        $mainView = 'frontend.' . $theme . '.index';
        if (! View::exists($mainView)) {
            $mainView = 'frontend.' . $theme . '.app';
        }

        try {
            $html = view($mainView, $this->sampleData())->render();
        } catch (\Throwable $e) {
            return response(
                '<pre style="color:#f87171;background:#0f172a;padding:20px;font-family:monospace;">'
                . 'Render error: ' . e($e->getMessage()) . "\n\n"
                . e($e->getTraceAsString())
                . '</pre>',
                500
            );
        }

        // Rebase asset URLs agar mengarah ke public/frontend/{theme}
        $html = $this->rebaseAssets($html, $theme);

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Cache-Control' => 'no-store',
        ]);
    }

    /**
     * Sample data untuk semua variabel yang mungkin dipakai di blade.
     */
    private function sampleData(): array
    {
        $post = (object) [
            'id' => 1,
            'title' => 'Contoh Judul Artikel yang Cukup Panjang',
            'slug' => 'contoh-artikel',
            'content' => '<p>Ini adalah konten contoh artikel. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>',
            'excerpt' => 'Ringkasan artikel contoh.',
            'datepublish' => now()->format('d M Y'),
            'created_at' => now(),
            'gambar' => fn () => ['https://picsum.photos/800/400?random=1'],
        ];

        $page = (object) [
            'title' => 'Contoh Halaman',
            'slug' => 'contoh-halaman',
            'content' => '<p>Konten halaman contoh.</p>',
            'image' => 'https://picsum.photos/800/400?random=2',
        ];

        $setting = (object) [
            'sitename' => 'Contoh Situs',
            'description' => 'Deskripsi situs contoh.',
            'logo' => 'https://via.placeholder.com/150x40',
            'email' => 'info@example.com',
            'phone' => '+62 812 3456 7890',
            'address' => 'Jl. Contoh No. 123',
            'active_theme' => 'default',
        ];

        $menu = (object) [
            'name' => 'Menu Contoh',
            'link' => '/contoh',
            'children' => collect(),
        ];

        $category = (object) [
            'name' => 'Kategori Contoh',
            'slug' => 'kategori-contoh',
        ];

        return [
            'setting' => $setting,
            'activeTheme' => 'default',
            'page' => $page,
            'post' => $post,
            'category' => $category,
            'seo' => (object) ['title' => $page->title],
            'html' => '<div class="container py-5"><h1>Konten Builder</h1><p>Preview homepage builder.</p></div>',
            'css' => '',
            'menus' => collect([$menu]),
            'latestposts' => collect([$post, $post, $post]),
            'posts' => collect([$post, $post]),
            'topposts' => collect([$post]),
            'categories' => collect([$category]),
            'pages' => collect([$page]),
            'gallery' => collect(),
            'sliders' => collect(),
            'tagcloud' => collect(['tag1' => 3, 'tag2' => 5]),
            'youtubes' => collect(),
            'lang' => collect(),
            'contacts' => collect(),
            'recentpost' => collect([$post]),
        ];
    }

    /**
     * Rebase relative URLs ke /frontend/{theme}/
     */
    private function rebaseAssets(string $html, string $theme): string
    {
        $prefix = '/frontend/' . $theme . '/';

        $html = preg_replace_callback(
            '/\b(src|href)=(["\'])(.*?)\2/i',
            function ($m) use ($prefix) {
                $url = trim($m[3]);
                if (
                    $url === '' ||
                    preg_match('/^(https?:)?\/\//i', $url) ||
                    preg_match('/^(data:|mailto:|tel:|javascript:|#)/i', $url) ||
                    str_starts_with($url, $prefix) ||
                    str_starts_with($url, '{{') ||
                    str_starts_with($url, '{!!')
                ) {
                    return $m[0];
                }
                $clean = ltrim(preg_replace('#^\./#', '', $url), '/');
                return $m[1] . '=' . $m[2] . $prefix . $clean . $m[2];
            },
            $html
        ) ?? $html;

        return $html;
    }
}
