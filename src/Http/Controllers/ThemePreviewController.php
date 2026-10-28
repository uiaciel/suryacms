<?php
// app/Http/Controllers/ThemePreviewController.php

namespace Uiaciel\SuryaCms\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class ThemePreviewController
{
    /**
     * Serve original index.html + rebase asset paths.
     */
    public function source(string $sessionId)
    {
        $base = storage_path("app/temp/themes/{$sessionId}");
        $indexPath = "{$base}/index.html";

        if (! File::exists($indexPath)) {
            abort(404, 'Preview session not found.');
        }

        $html = File::get($indexPath);
        $html = $this->rebaseAssetPaths($html, $sessionId);

        return response($html, 200, [
            'Content-Type'  => 'text/html; charset=UTF-8',
            'Cache-Control' => 'no-store',
        ]);
    }

    /**
     * Serve asset dari folder temp source.
     */
    public function sourceAsset(string $sessionId, string $path = '')
    {
        $base = realpath(storage_path("app/temp/themes/{$sessionId}/source"));
        if (! $base) abort(404);

        // Handle kalau ZIP punya top-level folder
        $candidates = [
            $base . '/' . $path,
            $base . '/' . $this->detectTopFolder($base) . '/' . $path,
        ];

        foreach ($candidates as $candidate) {
            $real = realpath($candidate);
            if ($real && is_file($real) && str_starts_with($real, $base)) {
                return response()->file($real, [
                    'Content-Type'  => File::mimeType($real) ?: 'application/octet-stream',
                    'Cache-Control' => 'public, max-age=3600',
                ]);
            }
        }

        abort(404);
    }

    /**
     * Render blade theme dengan sample data.
     */
    public function render(string $sessionId)
    {
        $metaPath = storage_path("app/temp/themes/{$sessionId}/builder-meta.json");
        if (! File::exists($metaPath)) {
            abort(404);
        }

        // Ambil theme slug dari folder session
        $theme = preg_replace('/-[a-z0-9]{8}$/', '', $sessionId);
        $viewName = "frontend.{$theme}.index";

        if (! View::exists($viewName)) {
            return response(
                '<pre style="color:#f87171;background:#0f172a;padding:20px;">'
                . 'View tidak ditemukan: ' . e($viewName)
                . '</pre>',
                500
            );
        }

        try {
            $html = view($viewName, $this->sampleData())->render();
        } catch (\Throwable $e) {
            return response(
                '<pre style="color:#f87171;background:#0f172a;padding:20px;">'
                . 'Render error: ' . e($e->getMessage())
                . "\n\n" . e($e->getTraceAsString())
                . '</pre>',
                500
            );
        }

        return response($html, 200, [
            'Content-Type'  => 'text/html; charset=UTF-8',
            'Cache-Control' => 'no-store',
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // HELPERS
    // ═══════════════════════════════════════════════════════════

    private function rebaseAssetPaths(string $html, string $sessionId): string
    {
        $prefix = '/theme/preview/source/' . $sessionId . '/asset/';

        // src / href
        $html = preg_replace_callback(
            '/\b(src|href)=(["\'])(.*?)\2/i',
            function ($m) use ($prefix) {
                $url = trim($m[3]);
                if (
                    $url === '' ||
                    preg_match('/^(https?:)?\/\//i', $url) ||
                    preg_match('/^(data:|mailto:|tel:|javascript:|#)/i', $url) ||
                    str_starts_with($url, $prefix)
                ) {
                    return $m[0];
                }
                $clean = ltrim(preg_replace('#^\./#', '', $url), '/');
                return $m[1] . '=' . $m[2] . $prefix . $clean . $m[2];
            },
            $html
        ) ?? $html;

        // srcset
        $html = preg_replace_callback(
            '/\bsrcset=(["\'])(.*?)\1/i',
            function ($m) use ($prefix) {
                $parts = array_map('trim', explode(',', $m[2]));
                $out = array_map(function ($part) use ($prefix) {
                    if ($part === '') return '';
                    $seg = preg_split('/\s+/', $part, 2);
                    $url = $seg[0];
                    $desc = $seg[1] ?? '';
                    if (
                        ! preg_match('/^(https?:)?\/\//i', $url) &&
                        ! preg_match('/^(data:|#)/i', $url)
                    ) {
                        $url = $prefix . ltrim(preg_replace('#^\./#', '', $url), '/');
                    }
                    return $desc ? "{$url} {$desc}" : $url;
                }, $parts);
                return 'srcset=' . $m[1] . implode(', ', $out) . $m[1];
            },
            $html
        ) ?? $html;

        // url() di inline CSS
        $html = preg_replace_callback(
            '/url\(\s*([\'"]?)([^\'")]+)\1\s*\)/i',
            function ($m) use ($prefix) {
                $url = trim($m[2]);
                if (
                    preg_match('/^(https?:)?\/\//i', $url) ||
                    preg_match('/^(data:|#)/i', $url)
                ) {
                    return $m[0];
                }
                return 'url(' . $m[1] . $prefix . ltrim(preg_replace('#^\./#', '', $url), '/') . $m[1] . ')';
            },
            $html
        ) ?? $html;

        return $html;
    }

    private function detectTopFolder(string $base): ?string
    {
        $entries = @scandir($base) ?: [];
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') continue;
            if (is_dir($base . '/' . $entry)) {
                return $entry;
            }
        }
        return null;
    }

    private function sampleData(): array
    {
        $post = (object) [
            'id'          => 1,
            'title'       => 'Contoh Judul Artikel',
            'slug'        => 'contoh-artikel',
            'content'     => '<p>Ini adalah konten contoh artikel.</p>',
            'datepublish' => now()->format('d M Y'),
            'gambar'      => fn () => ['https://picsum.photos/800/400?random=1'],
        ];

        return [
            'setting'    => (object) [
                'sitename'    => 'Contoh Situs',
                'description' => 'Deskripsi contoh',
                'logo'        => 'https://via.placeholder.com/150x40',
                'email'       => 'info@example.com',
                'phone'       => '+62 812 3456 7890',
                'address'     => 'Jl. Contoh No. 123',
            ],
            'menus'      => collect([]),
            'page'       => (object) ['title' => 'Contoh', 'content' => '<p>Isi halaman.</p>', 'slug' => 'contoh'],
            'post'       => $post,
            'latestposts'=> collect([$post]),
            'categories' => collect([]),
            'css'        => '',
            'html'       => '<div class="container py-5"><h1>Homepage</h1></div>',
        ];
    }
}
