<?php

namespace Uiaciel\SuryaCms\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use RuntimeException;
use Uiaciel\SuryaCMS\Models\Page;
use Uiaciel\SuryaCms\Services\ThemeAssetReferenceService;

class ThemeBuilderPreviewController extends Controller
{
    public function source(string $sessionId, string $path = '')
    {
        $this->validateSessionId($sessionId);
        $sourceRoot = storage_path("app/temp/themes/{$sessionId}/source");
        if ($path === '') {
            $metaPath = storage_path("app/temp/themes/{$sessionId}/builder-meta.json");
            if (! File::exists($metaPath)) {
                abort(404);
            }
            $meta = json_decode(File::get($metaPath), true);
            $path = (string) ($meta['indexRelativePath'] ?? '');
        }
        $filePath = $this->resolveFile($sourceRoot, $path);
        $content = File::get($filePath);
        $mimeType = File::mimeType($filePath) ?: 'application/octet-stream';

        if (str_contains($mimeType, 'text/html')) {
            $directory = trim(str_replace('\\', '/', dirname($path)), '.\\/');
            $encodedDirectory = implode('/', array_map('rawurlencode', explode('/', $directory)));
            $baseUrl = $this->sourceUrl($sessionId).($directory === '' ? '' : $encodedDirectory.'/');
            $baseTag = '<base href="'.e($baseUrl).'">';
            $headCount = 0;
            $content = preg_replace('/<head\b[^>]*>/i', '$0'.$baseTag, $content, 1, $headCount) ?? $content;
            if ($headCount === 0) {
                $htmlCount = 0;
                $content = preg_replace('/<html\b[^>]*>/i', '$0<head>'.$baseTag.'</head>', $content, 1, $htmlCount) ?? $content;
                if ($htmlCount === 0) {
                    $content = $baseTag.$content;
                }
            }
            $sourceUrl = $this->sourceUrl($sessionId);
            $content = preg_replace_callback('/\b(src|href)=([\'"])(\/(?!\/)[^\'"]*)\2/i', static function (array $match) use ($sourceUrl): string {
                $attribute = strtolower($match[1]) === 'src' ? 'src' : 'href';
                if (! ThemeAssetReferenceService::isAssetReference($match[3], $attribute)) {
                    return $match[0];
                }

                return $match[1].'='.$match[2].$sourceUrl.ltrim($match[3], '/').$match[2];
            }, $content) ?? $content;
        } elseif (str_contains($mimeType, 'text/css')) {
            $sourceUrl = $this->sourceUrl($sessionId);
            $content = preg_replace_callback('/url\(\s*([\'"]?)(\/(?!\/)[^\'")]+)\1\s*\)/i', static function (array $match) use ($sourceUrl): string {
                return 'url('.$match[1].$sourceUrl.ltrim($match[2], '/').$match[1].')';
            }, $content) ?? $content;
        }

        return response($content)
            ->header('Content-Type', $mimeType)
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Content-Security-Policy', "default-src 'self' data: https:; img-src 'self' data: https:; style-src 'self' 'unsafe-inline' https:; script-src 'self' 'unsafe-inline' https:; object-src 'none'; frame-ancestors 'self'");
    }

    public function render(string $sessionId, string $view = 'index')
    {
        $this->validateSessionId($sessionId);
        $view = trim(str_replace('\\', '/', $view), '/');

        $dataSource = (string) request()->query('data', 'dummy');
        if (! in_array($dataSource, ['dummy', 'latest'], true)) {
            abort(400, 'Sumber data preview tidak valid.');
        }

        $sessionPath = storage_path("app/temp/themes/{$sessionId}");
        $metaPath = $sessionPath.DIRECTORY_SEPARATOR.'builder-meta.json';
        if (! File::exists($metaPath)) {
            throw new RuntimeException('Sesi builder tidak ditemukan.');
        }

        $meta = json_decode(File::get($metaPath), true);
        $theme = Str::slug((string) ($meta['themeName'] ?? ''));
        if ($theme === '') {
            throw new RuntimeException('Nama tema pada sesi builder tidak valid.');
        }

        $themePath = resource_path("views/frontend/{$theme}");
        $appPath = "{$themePath}/app.blade.php";
        $previewViews = $this->availablePreviewViews($themePath);
        if (! isset($previewViews[$view])) {
            abort(404, 'File ini tidak tersedia untuk live preview.');
        }
        $previewPath = $previewViews[$view];
        if (! File::isFile($appPath) || ! File::isFile($previewPath)) {
            throw new RuntimeException("Generate tema dan pastikan file {$view}.blade.php tersedia untuk preview.");
        }

        $finder = clone View::getFinder();
        if (! isset($finder->getHints()['frontend'])) {
            throw new RuntimeException('Namespace view frontend belum terdaftar.');
        }
        $finder->flush();
        $finder->replaceNamespace('frontend', $themePath);
        $previewViewsFactory = new \Illuminate\View\Factory(
            View::getEngineResolver(),
            $finder,
            new \Illuminate\Events\Dispatcher(app())
        );


        $page = $dataSource === 'latest' ? $this->latestPublishedPage() : $this->previewPage();
        $posts = collect([
            $this->previewPost('Artikel Contoh Pertama', 'contoh-artikel-pertama'),
            $this->previewPost('Artikel Contoh Kedua', 'contoh-artikel-kedua'),
            $this->previewPost('Artikel Contoh Ketiga', 'contoh-artikel-ketiga'),
        ]);
        $category = (object) ['name' => 'Berita', 'slug' => 'berita'];
        $menus = collect([
            (object) ['name' => 'Beranda', 'link' => '/', 'category' => 'Primary', 'children' => collect()],
            (object) ['name' => 'Tentang', 'link' => '/tentang', 'category' => 'Primary', 'children' => collect()],
        ]);
        $setting = (object) [
            'language' => 'id',
            'sitename' => 'Preview SuryaCMS',
            'sitename_translation' => 'SuryaCMS Preview',
            'description' => 'Contoh data untuk preview tema',
            'logo' => '',
            'email' => 'halo@example.test',
            'phone' => '080000000000',
            'address' => 'Alamat contoh',
            'facebook' => '#',
            'instagram' => '#',
            'twitter' => '#',
            'whatsapp' => '#',
        ];
        $data = [
            'setting' => $setting,
            'activeTheme' => $theme,
            'menus' => $menus,
            'categories' => collect([$category]),
            'posts' => $posts,
            'latestposts' => $posts,
            'latestlatests' => $posts,
            'topposts' => $posts,
            'tagcloud' => collect(['contoh' => 3, 'surya' => 2]),
            'youtubes' => collect([
                (object) [
                    'url' => 'https://www.youtube.com/watch?v=example',
                    'video_id' => '',
                    'title' => 'Video Contoh',
                    'thumbnail' => '',
                    'created_at' => now(),
                ],
            ]),
            'sliders' => collect([
                (object) ['image_path' => '', 'alt_text' => 'Slide contoh', 'name' => 'Slide contoh', 'description' => 'Deskripsi slide contoh'],
            ]),
            'gallery' => collect([
                (object) ['image_path' => '', 'cover_path' => '', 'alt_text' => 'Galeri contoh'],
            ]),
            'pages' => collect([$page]),
            'lang' => collect([(object) ['code' => 'id', 'name' => 'Indonesia']]),
            'contacts' => collect([(object) ['name' => 'Pengunjung Contoh', 'email' => 'pengunjung@example.test', 'message' => 'Pesan contoh']]),
            'page' => $page,
            'post' => $posts->first(),
            'category' => $category,
            'seo' => (object) [
                'title' => $page->title,
                'keyword' => '',
                'description' => 'Preview SuryaCMS dengan data contoh.',
                'image' => '',
                'url' => url('/preview'),
            ],
            'html' => '<div class="container"><h1>Konten preview</h1><p>Contoh konten untuk pengujian tema.</p></div>',
            'css' => '',
            'recentpost' => $posts,
            'latest' => $posts,
            'errors' => collect(),
        ];

        if ($view === 'navigation') {
            $html = $previewViewsFactory->file($appPath, $data)->render();
        } elseif (str_starts_with($view, 'plugin/')) {
            $partial = $previewViewsFactory->file($previewPath, $data)->render();
            $html = $previewViewsFactory->file($appPath, array_merge($data, ['slot' => $partial]))->render();
        } else {
            $html = $previewViewsFactory->file($previewPath, $data)->render();
        }

        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Content-Security-Policy', "default-src 'self' data: https:; img-src 'self' data: https:; style-src 'self' 'unsafe-inline' https:; script-src 'self' 'unsafe-inline' https:; object-src 'none'; frame-ancestors 'self'");
    }

    private function availablePreviewViews(string $themePath): array
    {
        $base = realpath($themePath);
        if (! $base) {
            return [];
        }

        $views = [];
        foreach (File::allFiles($base) as $file) {
            $relative = str_replace('\\', '/', $file->getRelativePathname());
            $normalized = strtolower($relative);
            if (
                ! str_ends_with($normalized, '.blade.php') ||
                in_array($normalized, ['homepage.blade.php', 'editor.blade.php'], true)
            ) {
                continue;
            }

            $path = realpath($file->getPathname());
            if (! $path || ! str_starts_with($path, rtrim($base, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR)) {
                continue;
            }

            $key = substr($relative, 0, -strlen('.blade.php'));
            $views[$key] = $path;
        }

        return $views;
    }

    private function latestPublishedPage(): Page
    {
        $page = Page::query()
            ->where('status', 'Publish')
            ->latest()
            ->first();

        if (! $page) {
            abort(404, 'Belum ada page dengan status Publish untuk preview.');
        }

        return $page;
    }

    private function previewPage(): object
    {
        return new class
        {
            public string $title = 'Halaman Contoh SuryaCMS';

            public string $slug = 'halaman-contoh';

            public string $content = '<p>Ini adalah konten halaman contoh untuk menguji layout single page.</p><p>Gunakan pilihan data “Latest published page” untuk melihat data dari halaman Publish terbaru.</p>';

            public string $datepublish;

            public int $view = 12;

            public string $pdf = '';

            public string $html = '';

            public string $css = '';

            public bool $is_builder = false;

            public string $keyword = 'contoh, preview';

            public ?string $thumbnail = null;

            public function __construct()
            {
                $this->datepublish = now()->toDateString();
            }

            public function gambar(): array
            {
                return [];
            }
        };
    }

    private function resolveFile(string $root, string $relativePath): string
    {
        $relativePath = str_replace('\\', '/', rawurldecode($relativePath));
        if ($relativePath === '' || str_contains($relativePath, "\0") || in_array('..', explode('/', $relativePath), true)) {
            abort(404);
        }

        $base = realpath($root);
        $file = realpath($root.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relativePath));
        if (! $base || ! $file || ! str_starts_with($file, $base.DIRECTORY_SEPARATOR) || ! is_file($file)) {
            abort(404);
        }

        return $file;
    }

    private function validateSessionId(string $sessionId): void
    {
        if (! preg_match('/^[a-zA-Z0-9-]+$/', $sessionId)) {
            abort(404);
        }
    }

    private function sourceUrl(string $sessionId): string
    {
        return url('/'.trim(config('suryacms.admin_prefix', 'admin'), '/')."/themes/preview/{$sessionId}/source/");
    }

    private function previewPost(string $title, string $slug): object
    {
        $post = new class
        {
            public int $id;

            public string $title;

            public string $slug;

            public string $content;

            public string $excerpt;

            public string $datepublish;

            public \Illuminate\Support\Carbon $created_at;

            public int $view;

            public string $tags;

            public object $category;

            public string $image = '';

            public function gambar(): array
            {
                return $this->image === '' ? [] : [$this->image];
            }
        };
        $post->id = 0;
        $post->title = $title;
        $post->slug = $slug;
        $post->content = '<p>Isi artikel contoh untuk melihat tampilan loop.</p>';
        $post->excerpt = 'Ringkasan artikel contoh.';
        $post->datepublish = now()->toDateString();
        $post->created_at = now();
        $post->view = 42;
        $post->tags = 'contoh, preview';
        $post->category = (object) ['name' => 'Berita', 'slug' => 'berita'];
        $post->image = '';

        return $post;
    }
}
