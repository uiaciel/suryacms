<?php

namespace Uiaciel\SuryaCms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View as ViewResponse;
use Uiaciel\SuryaCms\Models\Setting;

class PreviewThemeController
{
    public function show(Request $request, string $theme, string $path = ''): Response
    {
        abort_unless(preg_match('/\A[a-zA-Z0-9_-]+\z/', $theme) === 1, 404);

        $themePath = resource_path('views'.DIRECTORY_SEPARATOR.'frontend'.DIRECTORY_SEPARATOR.$theme);
        abort_unless(
            File::isDirectory($themePath) && File::exists($themePath.DIRECTORY_SEPARATOR.'index.blade.php'),
            404
        );

        $viewFinder = View::getFinder();
        $frontendNamespaces = $viewFinder->getHints()['frontend'] ?? [];
        $previousTheme = config('frontend.active');
        $hadCurrentSetting = app()->bound('current.setting');
        $previousSetting = $hadCurrentSetting ? app('current.setting') : null;
        $previousLocale = App::getLocale();

        $currentSetting = app()->bound('current.setting')
            ? app('current.setting')
            : Setting::first();

        try {
            if ($currentSetting) {
                $previewSetting = clone $currentSetting;
                $previewSetting->active_theme = $theme;
                app()->instance('current.setting', $previewSetting);
            }

            config(['frontend.active' => $theme]);
            View::prependNamespace('frontend', $themePath);

            $view = $this->renderFrontendPath($request, $path);
            $html = $this->rewriteInternalLinks($view->render(), $theme, $request);

            return response($html)
                ->header('Content-Type', 'text/html; charset=UTF-8')
                ->header('Cache-Control', 'no-store');
        } finally {
            $viewFinder->replaceNamespace('frontend', $frontendNamespaces);
            config(['frontend.active' => $previousTheme]);

            if ($hadCurrentSetting) {
                app()->instance('current.setting', $previousSetting);
            } else {
                app()->forgetInstance('current.setting');
            }

            App::setLocale($previousLocale);
        }
    }

    /**
     * Menampilkan file asli dari session ZIP (Original Design)
     */
    public function showSourceSession(string $sessionId, string $path = '')
    {
        Log::info("[Source Preview] Request", ['session' => $sessionId, 'path' => $path]);

        $base = realpath(storage_path("app/temp/themes/{$sessionId}/source"));
        $theme = Str::before($sessionId, '-');
        $publicThemePath = public_path("frontend/{$theme}");

        // 1. Jika $path kosong, sajikan index.html dari folder temp
        if (empty($path) || $path === '/' || $path === 'index.html') {
            if (!$base || !is_dir($base)) {
                abort(404, 'Session tema tidak ditemukan.');
            }

            $candidates = [
                $base . '/index.html',
                $base . '/index.htm',
                $base . '/home.html',
            ];

            foreach ($candidates as $candidate) {
                if (is_file($candidate)) {
                    Log::info("[Source Preview] Found HTML", ['file' => $candidate]);

                    $html = File::get($candidate);

                    // STRATEGI UTAMA: Rewrite semua jalur aset relatif agar langsung menunjuk ke folder public
                    $html = $this->rewriteSourceAssets($html, $sessionId);

                    $debugScript = "<script>console.log('%c SuryaCMS Source Preview Loaded ', 'background: #222; color: #bada55; font-size: 12px; padding: 4px;'); console.log('Theme:', '{$theme}');</script>";
                    $html = str_ireplace('</head>', $debugScript . "</head>", $html);

                    return response($html)
                        ->header('Content-Type', 'text/html; charset=UTF-8')
                        ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
                }
            }

            abort(404, 'File index.html tidak ditemukan di dalam arsip ZIP.');
        }

        // 2. FALLBACK: Jika ada request aset yang masih masuk ke route ini (misal regex rewrite terlewat),
        // coba layani langsung dari folder public tema.
        $cleanPath = str_replace(['../', '..\\', './', '\\'], '/', $path);
        $candidate = $publicThemePath . '/' . $cleanPath;
        $realFilePath = realpath($candidate);

        $allowedBase = realpath($publicThemePath);
        if ($allowedBase) {
            $allowedBase = rtrim($allowedBase, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }

        if ($realFilePath && is_file($realFilePath) && $allowedBase && str_starts_with($realFilePath, $allowedBase)) {
            Log::info("[Source Preview] Serving asset from public fallback", ['file' => $realFilePath]);
            return response()->file($realFilePath, [
                'Content-Type'  => File::mimeType($realFilePath) ?: 'application/octet-stream',
                'Cache-Control' => 'public, max-age=3600',
            ]);
        }

        // Abaikan warning log untuk file .map (source map) yang sering diminta browser secara otomatis tapi tidak wajib ada
        if (str_ends_with($cleanPath, '.map')) {
            return response('', 204);
        }

        Log::warning("[Source Preview] Asset not found", ['path' => $path, 'cleanPath' => $cleanPath]);
        abort(404, 'File tidak ditemukan: ' . $path);
    }

    /**
     * Rewrite jalur relatif (src/href) agar langsung menunjuk ke folder public asset tema.
     * Ini memungkinkan web server (Nginx/Apache) melayani file secara langsung tanpa melalui Laravel.
     */
    private function rewriteSourceAssets(string $html, string $sessionId): string
    {
        $theme = Str::before($sessionId, '-');
        // Arahkan langsung ke folder public asset tema
        $assetBaseUrl = '/frontend/' . $theme;

        // 1. Rewrite href (untuk <link> CSS, dll)
        // Abaikan jika jalur sudah absolut (dimulai dengan http, /, #, mailto, dll)
        $html = preg_replace_callback(
            '/(<(?:link|a)\b[^>]*\bhref=)(["\'])(?!https?:\/\/|\/|#|mailto:|tel:|javascript:|data:)([^"\']*)\2/i',
            function ($m) use ($assetBaseUrl) {
                return $m[1] . $m[2] . $assetBaseUrl . '/' . ltrim($m[3], '/') . $m[2];
            },
            $html
        );

        // 2. Rewrite src (untuk <img>, <script>, <iframe>, dll)
        $html = preg_replace_callback(
            '/(<(?:img|script|iframe|source|video|audio)\b[^>]*\bsrc=)(["\'])(?!https?:\/\/|\/|#|data:)([^"\']*)\2/i',
            function ($m) use ($assetBaseUrl) {
                return $m[1] . $m[2] . $assetBaseUrl . '/' . ltrim($m[3], '/') . $m[2];
            },
            $html
        );

        return $html;
    }

    /**
     * Deteksi folder pembungkus (wrapper) dengan lebih akurat.
     * Hanya mengembalikan nama folder jika:
     * 1. Hanya ada SATU folder di dalam direktori tersebut.
     * 2. TIDAK ADA file apapun di root direktori tersebut (semua file ada di dalam folder itu).
     */
    private function detectTopFolder(string $base): ?string
    {
        $entries = @scandir($base) ?: [];
        $dirs = [];
        $hasRootFiles = false;

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') continue;

            $fullPath = $base . '/' . $entry;
            if (is_dir($fullPath)) {
                $dirs[] = $entry;
            } elseif (is_file($fullPath)) {
                $hasRootFiles = true;
            }
        }

        // Jika hanya ada 1 folder DAN tidak ada file di root, maka itu adalah wrapper folder
        if (count($dirs) === 1 && !$hasRootFiles) {
            return $dirs[0];
        }

        return null; // File sudah tersebar di root, atau ada banyak folder. Tidak ada wrapper tunggal.
    }

    private function renderFrontendPath(Request $request, string $path): ViewResponse
    {
        $segments = array_values(array_filter(explode('/', trim($path, '/')), fn ($segment) => $segment !== ''));
        $language = null;

        if ($segments && is_multilingual() && in_array($segments[0], active_languages(), true)) {
            $language = array_shift($segments);
            App::setLocale($language);
        }

        $frontend = app(FrontendController::class);
        $firstSegment = $segments[0] ?? null;
        $secondSegment = $segments[1] ?? null;

        if ($firstSegment === null) {
            return $frontend->index();
        }

        if ($firstSegment === 'category') {
            if ($secondSegment === null && count($segments) === 1) {
                return $frontend->categoryIndex($request, $language);
            }

            abort_unless(count($segments) === 2, 404);

            return $language
                ? $frontend->category($request, $language, $secondSegment)
                : $frontend->category($request, $secondSegment);
        }

        if ($firstSegment === 'media') {
            abort_unless(count($segments) === 2, 404);

            return $language
                ? $frontend->postshow($request, $language, $secondSegment, false)
                : $frontend->postshow($request, $secondSegment, null, false);
        }

        if ($firstSegment === 'contact-us' && count($segments) === 1) {
            return $frontend->contact($request);
        }

        abort_unless(count($segments) === 1, 404);

        return $language
            ? $frontend->pageshow($request, $language, $firstSegment)
            : $frontend->pageshow($request, $firstSegment);
    }

    private function rewriteInternalLinks(string $html, string $theme, Request $request): string
    {
        $previewBase = route('admin.themes.preview', ['theme' => $theme], false);

        return preg_replace_callback(
            '/(<a\b[^>]*\bhref=)(["\'])(.*?)\2/i',
            function (array $match) use ($previewBase, $request): string {
                $url = trim($match[3]);
                if ($url === '' || preg_match('/^(#|mailto:|tel:|javascript:|data:)/i', $url)) {
                    return $match[0];
                }

                $parts = parse_url($url);
                if ($parts === false) {
                    return $match[0];
                }

                if (isset($parts['host']) && strcasecmp($parts['host'], $request->getHost()) !== 0) {
                    return $match[0];
                }

                $path = $parts['path'] ?? '';
                if ($path === '') {
                    return $match[0];
                }

                $path = '/'.ltrim($path, '/');
                if (
                    preg_match('#^/(?:frontend|storage|build|assets|favicon\.ico|robots\.txt)(?:/|$)#i', $path) ||
                    File::exists(public_path(ltrim($path, '/')))
                ) {
                    return $match[0];
                }

                $suffix = ($parts['query'] ?? '') !== '' ? '?'.$parts['query'] : '';
                $suffix .= ($parts['fragment'] ?? '') !== '' ? '#'.$parts['fragment'] : '';
                $previewPath = $path === '/' ? '' : $path;

                return $match[1].$match[2].$previewBase.$previewPath.$suffix.$match[2];
            },
            $html
        ) ?? $html;
    }
}
