<?php
// app/Http/Controllers/ThemeSourcePreviewController.php

namespace Uiaciel\SuryaCms\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ThemeSourcePreviewController
{
    /**
     * Serve original index.html dengan path di-rebase ke route asset.
     */
    public function index(string $sessionId)
    {
        $base = storage_path("app/temp/themes/{$sessionId}/source");
        $indexPath = storage_path("app/temp/themes/{$sessionId}/index.html");

        if (! File::isDirectory($base) || ! File::exists($indexPath)) {
            abort(404, 'Preview session not found.');
        }

        $html = File::get($indexPath);

        // Rebase semua relative path agar bisa di-resolve oleh browser
        // melalui route /admin/themes/preview/source/{sessionId}/asset/{path}
        $html = $this->rebaseAssetPaths($html, $sessionId);

        // Inject <base> tag sebagai fallback untuk path yang terlewat
        $baseHref = route('admin.themes.preview.source.asset', [
            'sessionId' => $sessionId,
            'path' => '__BASE__',
        ]);
        $baseHref = str_replace('__BASE__', '', $baseHref);

        if (stripos($html, '<base ') === false) {
            $html = preg_replace(
                '/<head([^>]*)>/i',
                '<head$1><base href="' . e($baseHref) . '">',
                $html,
                1
            );
        }

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    /**
     * Serve file asset dari folder temp source.
     */
    public function asset(string $sessionId, string $path = '')
    {
        $base = realpath(storage_path("app/temp/themes/{$sessionId}/source"));

        if (! $base) {
            abort(404);
        }

        // Sanitasi path
        $path = str_replace('\\', '/', $path);
        if (
            $path === '' ||
            str_contains($path, "\0") ||
            str_starts_with($path, '/') ||
            in_array('..', explode('/', $path), true)
        ) {
            abort(404);
        }

        $absolute = $base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
        $real = realpath($absolute);
        $basePrefix = rtrim($base, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (
            ! $real ||
            ! is_file($real) ||
            ($real !== $base && ! str_starts_with($real, $basePrefix)) ||
            is_link($real)
        ) {
            abort(404);
        }

        $mime = File::mimeType($real) ?: 'application/octet-stream';

        return response()->file($real, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Rebase src/href/srcset agar mengarah ke route asset.
     */
    private function rebaseAssetPaths(string $html, string $sessionId): string
    {
        $prefix = rtrim(route('admin.themes.preview.source.asset', [
            'sessionId' => $sessionId,
            'path' => '',
        ]), '/') . '/';

        $shouldRewrite = function (string $url) use ($prefix): bool {
            $url = trim($url);
            if ($url === '') return false;
            if (preg_match('/^(https?:)?\/\//i', $url)) return false;
            if (preg_match('/^(data:|mailto:|tel:|javascript:|#)/i', $url)) return false;
            if (str_starts_with($url, $prefix)) return false;
            return true;
        };

        $normalize = function (string $url): string {
            $url = trim($url);
            $url = preg_replace('#^\./#', '', $url) ?? $url;
            $url = ltrim($url, '/');
            return $url;
        };

        // src / href
        $html = preg_replace_callback(
            '/\b(src|href)=(["\'])(.*?)\2/i',
            function ($m) use ($prefix, $shouldRewrite, $normalize) {
                if (! $shouldRewrite($m[3])) return $m[0];
                $clean = $normalize($m[3]);
                $q = $m[2];
                return $m[1] . '=' . $q . $prefix . $clean . $q;
            },
            $html
        ) ?? $html;

        // srcset
        $html = preg_replace_callback(
            '/\bsrcset=(["\'])(.*?)\1/i',
            function ($m) use ($prefix, $shouldRewrite, $normalize) {
                $q = $m[1];
                $parts = array_map('trim', explode(',', $m[2]));
                $out = [];
                foreach ($parts as $part) {
                    if ($part === '') continue;
                    $segments = preg_split('/\s+/', $part, 2);
                    $url = $segments[0];
                    $descriptor = $segments[1] ?? '';
                    if ($shouldRewrite($url)) {
                        $url = $prefix . $normalize($url);
                    }
                    $out[] = $descriptor !== '' ? "{$url} {$descriptor}" : $url;
                }
                return 'srcset=' . $q . implode(', ', $out) . $q;
            },
            $html
        ) ?? $html;

        // url() di inline style
        $html = preg_replace_callback(
            '/url\(\s*([\'"]?)([^\'")]+)\1\s*\)/i',
            function ($m) use ($prefix, $shouldRewrite, $normalize) {
                if (! $shouldRewrite($m[2])) return $m[0];
                return 'url(' . $m[1] . $prefix . $normalize($m[2]) . $m[1] . ')';
            },
            $html
        ) ?? $html;

        return $html;
    }
}
