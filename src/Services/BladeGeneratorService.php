<?php

namespace Uiaciel\SuryaCms\Services;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BladeGeneratorService
{
    public function __construct(
        private string $html,
        private string $theme
    ) {}

    /**
     * Generate semua file blade ke $viewPath.
     */
    public function generateAll(string $viewPath): void
    {
        $this->ensureDir($viewPath);
        $this->ensureDir($viewPath . '/page');
        $this->ensureDir($viewPath . '/plugin');

        // Layout & Core
        $this->write("{$viewPath}/app.blade.php",        $this->generateApp());
        $this->write("{$viewPath}/navigation.blade.php", $this->generateNavigation());
        $this->write("{$viewPath}/index.blade.php",      $this->generateIndex());
        $this->write("{$viewPath}/homepage.blade.php",   $this->generateHomepage());
        $this->write("{$viewPath}/editor.blade.php",     $this->generateEditor());

        // Page Views
        $this->write("{$viewPath}/page/show.blade.php",      $this->generatePageShow());
        $this->write("{$viewPath}/page/post.blade.php",      $this->generatePagePost());
        $this->write("{$viewPath}/page/category.blade.php",  $this->generatePageCategory());
        $this->write("{$viewPath}/page/contact.blade.php",   $this->generatePageContact());
        $this->write("{$viewPath}/page/maintenance.blade.php", $this->generatePageMaintenance());

        // Plugin / Widget Views
        $this->write("{$viewPath}/plugin/blog.blade.php",    $this->generatePluginBlog());
        $this->write("{$viewPath}/plugin/slider.blade.php",  $this->generatePluginSlider());
        $this->write("{$viewPath}/plugin/youtube.blade.php", $this->generatePluginYoutube());

        // Config (Sekarang mencakup deteksi style, assets, dan custom blocks)
        $this->write("{$viewPath}/theme.php", $this->generateThemeConfig());
    }

    // ═══════════════════════════════════════════════════════════
    // app.blade.php — layout skeleton
    // ═══════════════════════════════════════════════════════════

    private function generateApp(): string
    {
        $head    = $this->extractHead();
        $footer  = $this->extractFooter();
        $scripts = $this->extractScripts();

        return <<<BLADE
<!DOCTYPE html>
<html lang="id">
{$head}
<body>
    @include('frontend.{$this->theme}.navigation')

    <main>
        @hasSection('seo')
            @yield('seo')
        @endif

        @hasSection('schema')
            @yield('schema')
        @endif

        @hasSection('content')
            @yield('content')
        @else
            {{ \$slot ?? '' }}
        @endif
    </main>

{$footer}

{$scripts}

    @stack('scripts')
</body>
</html>
BLADE;
    }

    // ═══════════════════════════════════════════════════════════
    // navigation.blade.php — menu dinamis
    // ═══════════════════════════════════════════════════════════

    private function generateNavigation(): string
    {
        return <<<BLADE
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ \$setting?->sitename ?? 'SuryaCMS' }}
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto">
                @foreach(\$menus ?? [] as \$menu)
                    @if(isset(\$menu->children) && \$menu->children->count())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                {{ \$menu->name }}
                            </a>
                            <ul class="dropdown-menu">
                                @foreach(\$menu->children as \$child)
                                    <li><a class="dropdown-item" href="{{ url(\$child->link ?? '/') }}">{{ \$child->name }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url(\$menu->link ?? '/') }}">{{ \$menu->name }}</a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
</nav>
BLADE;
    }

    // ═══════════════════════════════════════════════════════════
    // index.blade.php — SEMUA section dari body KECUALI header/footer
    // ═══════════════════════════════════════════════════════════

    private function generateIndex(): string
    {
        $content = $this->extractBodyContentWithoutHeaderFooter();

        return <<<BLADE
@extends('frontend::app')

@section('content')
{$content}
@endsection
BLADE;
    }

    // ═══════════════════════════════════════════════════════════
    // homepage.blade.php — untuk Page Builder
    // ═══════════════════════════════════════════════════════════

    private function generateHomepage(): string
    {
        return <<<BLADE
@extends('frontend::app')

@section('content')
    @if(isset(\$css) && \$css)
        <style>{!! \$css !!}</style>
    @endif
    @if(isset(\$html) && \$html)
        {!! \$html !!}
    @endif
@endsection
BLADE;
    }

    // ═══════════════════════════════════════════════════════════
    // editor.blade.php — untuk Livewire Editor
    // ═══════════════════════════════════════════════════════════

    private function generateEditor(): string
    {
        $head    = $this->extractHead();
        $scripts = $this->extractScripts();

        return <<<BLADE
<!DOCTYPE html>
<html lang="id">
{$head}
<body class="bg-gray-100">
    @livewireStyles

    <main class="min-h-screen">
        {{ \$slot }}
    </main>

{$scripts}

    @stack('scripts')
    @livewireScripts
</body>
</html>
BLADE;
    }

    // ═══════════════════════════════════════════════════════════
    // PAGE VIEWS (Sesuai Peta Variabel Global)
    // ═══════════════════════════════════════════════════════════

    private function generatePageShow(): string
    {
        return <<<BLADE
@extends('frontend::app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">{{ \$page->title }}</h1>

    @if(\$page->is_builder)
        @if(\$page->css)
            <style>{!! \$page->css !!}</style>
        @endif
        <div class="page-content">
            {!! \$page->html ?? \$page->content !!}
        </div>
    @else
        <div class="page-content">
            {!! \$page->content !!}
        </div>
    @endif
</div>
@endsection
BLADE;
    }

    private function generatePagePost(): string
    {
        return <<<BLADE
@extends('frontend::app')

@section('content')
<article class="container py-5">
    <header class="mb-4">
        <h1 class="display-5">{{ \$post->title }}</h1>
        <div class="text-muted small">
            <span>Kategori: <a href="{{ url('/category/' . \$post->category->slug) }}">{{ \$post->category->name }}</a></span>
            <span class="mx-2">|</span>
            <span>{{ \Carbon\Carbon::parse(\$post->datepublish)->format('d M Y') }}</span>
            <span class="mx-2">|</span>
            <span>{{ \$post->view }} Views</span>
        </div>
    </header>

    <div class="post-content">
        {!! \$post->content !!}
    </div>

    @if(\$post->tags)
    <div class="mt-4">
        <strong>Tags:</strong>
        @foreach(array_filter(explode(',', \$post->tags)) as \$tag)
            <a href="{{ url('/tag/' . \Str::slug(trim(\$tag))) }}" class="badge bg-secondary text-decoration-none">
                {{ trim(\$tag) }}
            </a>
        @endforeach
    </div>
    @endif
</article>

{{-- Recent Posts --}}
<section class="container py-4 border-top">
    <h3>Postingan Terbaru</h3>
    <div class="row">
        @foreach(\$recentpost ?? [] as \$recent)
        <div class="col-md-4 mb-3">
            <h5><a href="{{ url('/' . \$recent->slug) }}" class="text-decoration-none">{{ \$recent->title }}</a></h5>
            <small class="text-muted">{{ \Str::limit(strip_tags(\$recent->content), 80) }}</small>
        </div>
        @endforeach
    </div>
</section>
@endsection
BLADE;
    }

    private function generatePageCategory(): string
    {
        return <<<BLADE
@extends('frontend::app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Kategori: {{ \$category->name }}</h1>

    <div class="row">
        @forelse(\$posts as \$post)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{ url('/' . \$post->slug) }}" class="text-decoration-none">{{ \$post->title }}</a>
                        </h5>
                        <p class="card-text text-muted small">
                            {{ \Carbon\Carbon::parse(\$post->datepublish)->format('d M Y') }}
                        </p>
                        <p class="card-text">{{ \Str::limit(strip_tags(\$post->content), 100) }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Belum ada postingan di kategori ini.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ \$posts->links() }}
    </div>
</div>
@endsection
BLADE;
    }

    private function generatePageContact(): string
    {
        return <<<BLADE
@extends('frontend::app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Hubungi Kami</h1>
    <div class="row">
        <div class="col-md-6">
            <p>Silakan kirimkan pesan Anda melalui form di bawah ini.</p>
        </div>
        <div class="col-md-6">
            @if(\$setting)
                <h4>Informasi Kontak</h4>
                <p><strong>Website:</strong> {{ \$setting->url }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
BLADE;
    }

    private function generatePageMaintenance(): string
    {
        return <<<BLADE
@extends('frontend::app')

@section('content')
<div class="container py-5 text-center">
    <h1>Maintenance</h1>
    <p>Website sedang dalam perbaikan. Silakan kembali lagi nanti.</p>
</div>
@endsection
BLADE;
    }

    // ═══════════════════════════════════════════════════════════
    // PLUGIN / WIDGET VIEWS (Menggunakan Variabel Global)
    // ═══════════════════════════════════════════════════════════

    private function generatePluginBlog(): string
    {
        return <<<BLADE
{{-- Widget Blog: Menampilkan Latest Posts --}}
<div class="widget-blog mb-4">
    <h4 class="widget-title">Postingan Terbaru</h4>
    <ul class="list-unstyled">
        @foreach(\$latestposts ?? [] as \$post)
            <li class="mb-2">
                <a href="{{ url('/' . \$post->slug) }}" class="text-decoration-none">
                    {{ \$post->title }}
                </a>
                <br>
                <small class="text-muted">{{ \Carbon\Carbon::parse(\$post->datepublish)->format('d M Y') }}</small>
            </li>
        @endforeach
    </ul>
</div>
BLADE;
    }

    private function generatePluginSlider(): string
    {
        return <<<BLADE
{{-- Widget Slider: Menampilkan data dari \$sliders --}}
<div class="widget-slider mb-4">
    @if(isset(\$sliders) && \$sliders->count() > 0)
        <div id="themeSlider" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach(\$sliders as \$index => \$slide)
                    <div class="carousel-item {{ \$index === 0 ? 'active' : '' }}">
                        <img src="{{ asset('storage/' . \$slide->image_path) }}" class="d-block w-100" alt="{{ \$slide->alt_text }}">
                        @if(\$slide->description)
                            <div class="carousel-caption d-none d-md-block">
                                <h5>{{ \$slide->name }}</h5>
                                <p>{{ \$slide->description }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#themeSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#themeSlider" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    @endif
</div>
BLADE;
    }

    private function generatePluginYoutube(): string
    {
        return <<<BLADE
{{-- Widget YouTube: Menampilkan data dari \$youtubes --}}
<div class="widget-youtube mb-4">
    @if(isset(\$youtubes) && \$youtubes->count() > 0)
        <h4 class="widget-title">Video Terbaru</h4>
        <div class="row">
            @foreach(\$youtubes as \$yt)
                <div class="col-md-6 mb-3">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/{{ \$yt->video_id }}" title="{{ \$yt->title }}" allowfullscreen></iframe>
                    </div>
                    <h6 class="mt-2">{{ \$yt->title }}</h6>
                </div>
            @endforeach
        </div>
    @endif
</div>
BLADE;
    }

    // ═══════════════════════════════════════════════════════════
    // theme.php — config (DIPERBARUI: Format Assets yang Benar)
    // ═══════════════════════════════════════════════════════════

    private function generateThemeConfig(): string
    {
        $style = $this->detectStyleFramework();
        $assets = $this->extractAssets();
        $sections = $this->extractSections(); // <-- Ekstrak sections dari HTML

        $stylesArray = $this->formatArrayForPhp($assets['styles']);
        $scriptsArray = $this->formatArrayForPhp($assets['scripts']);
        $sectionsArray = $this->formatSectionsForPhp($sections); // <-- Format sections

        $customBlocks = <<<'PHP'
    [
        [
            'id'       => 'blog-block',
            'label'    => 'Blog',
            'category' => 'Plugin',
            'icon'     => '📝',
            'content'  => '[[blog]]',
            'preview'  => '<div style="padding:16px;font-family:sans-serif;"><h2 style="margin:0 0 12px;">Blog</h2><p>Preview Blog...</p></div>',
        ],
        [
            'id'       => 'gallery-block',
            'label'    => 'Gallery',
            'category' => 'Plugin',
            'icon'     => '🖼️',
            'content'  => '[[gallery]]',
            'preview'  => '<div style="padding:16px;font-family:sans-serif;"><h2 style="margin:0 0 12px;">Gallery</h2><p>Preview Gallery...</p></div>',
        ],
        [
            'id'       => 'slider-block',
            'label'    => 'Slider',
            'category' => 'Plugin',
            'icon'     => '🎠',
            'content'  => '[[slider]]',
            'preview'  => '<div style="padding:16px;font-family:sans-serif;"><h2 style="margin:0 0 12px;">Slider</h2><p>Preview Slider...</p></div>',
        ],
        [
            'id'       => 'youtube-block',
            'label'    => 'YouTube',
            'category' => 'Plugin',
            'icon'     => '📺',
            'content'  => '[[youtube]]',
            'preview'  => '<div style="padding:16px;font-family:sans-serif;"><h2 style="margin:0 0 12px;">YouTube</h2><p>Preview YouTube...</p></div>',
        ],
    ]
PHP;

        return <<<PHP
<?php

return [
    'info' => [
        'name'          => '{$this->theme}',
        'path'          => '{$this->theme}',
        'version'       => '1.0.0',
        'style'         => '{$style}',
        'author'        => 'SuryaCMS Generator',
        'url'           => 'https://kreasitekmedia.com',
        'license'       => 'MIT',
        'description'   => 'Theme generated from HTML ZIP.',
        'theme_preview' => "/frontend/{$this->theme}/theme_preview.jpg",
    ],

    'assets' => [
        'styles'  => {$stylesArray},
        'scripts' => {$scriptsArray},
    ],

    'sections' => {$sectionsArray},

    'custom_blocks' => {$customBlocks},
];
PHP;
    }

    // ═══════════════════════════════════════════════════════════
    // DETECTION & EXTRACTION HELPERS
    // ═══════════════════════════════════════════════════════════

    private function detectStyleFramework(): string
    {
        $htmlLower = strtolower($this->html);
        if (str_contains($htmlLower, 'tailwindcss') || str_contains($htmlLower, 'cdn.tailwindcss.com') || str_contains($htmlLower, '@tailwind')) {
            return 'tailwindcss';
        }
        if (str_contains($htmlLower, 'bootstrap') || str_contains($htmlLower, 'getbootstrap.com') || str_contains($htmlLower, 'jsdelivr.net/npm/bootstrap')) {
            return 'bootstrap';
        }
        return 'css';
    }

    /**
     * Mengekstrak dan menormalisasi URL aset CSS dan JS.
     * - CDN eksternal dipertahankan.
     * - Aset tema diberi prefix domain + frontend/{theme}.
     * - Aset root publik diberi prefix domain saja.
     */
    private function extractAssets(): array
    {
        $styles = [];
        $scripts = [];
        $baseUrl = rtrim(url('/'), '/');
        $themePath = 'frontend/' . $this->theme;

        $processUrl = function(string $url) use ($baseUrl, $themePath): string {
            $url = trim($url);
            if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
                return $url;
            }
            if (str_starts_with($url, '//')) {
                return 'https:' . $url;
            }
            $url = '/' . ltrim($url, '/');
            $themeAssetPath = public_path($themePath . $url);

            if (file_exists($themeAssetPath)) {
                return $baseUrl . '/' . $themePath . $url;
            }
            return $baseUrl . $url;
        };

        if (preg_match_all('/<link[^>]+href=["\']([^"\']+)["\'][^>]*>/i', $this->html, $matches)) {
            foreach ($matches[1] as $href) {
                if (!str_starts_with($href, 'data:') && !str_starts_with($href, 'javascript:')) {
                    $styles[] = $processUrl($href);
                }
            }
        }

        if (preg_match_all('/<script[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $this->html, $matches)) {
            foreach ($matches[1] as $src) {
                $scripts[] = $processUrl($src);
            }
        }

        return [
            'styles'  => array_values(array_unique($styles)),
            'scripts' => array_values(array_unique($scripts)),
        ];
    }

    /**
     * Mengekstrak tag <section> dari HTML untuk digunakan sebagai blocks di GrapesJS.
     */
    private function extractSections(): array
    {
        $sections = [];
        $dom = new DOMDocument();

        // Suppress warnings untuk HTML yang mungkin tidak 100% valid (umum di template)
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . $this->html);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        // Cari semua tag <section>
        $nodes = $xpath->query('//section');

        $counter = 1;
        foreach ($nodes as $node) {
            $id = $node->getAttribute('id') ?: 'section-' . $counter;

            // Buat label yang ramah pembaca dari ID
            $rawLabel = $node->getAttribute('id') ?: 'section ' . $counter;
            $label = 'Section ' . ucwords(str_replace(['-', '_'], ' ', $rawLabel));

            // Ambil outer HTML dari node tersebut
            $content = trim($dom->saveHTML($node));

            $sections[] = [
                'id' => $id,
                'label' => $label,
                'category' => 'Themes Sections',
                'content' => $content,
            ];
            $counter++;
        }

        return $sections;
    }

    /**
     * Memformat array sections menjadi string PHP yang valid menggunakan Nowdoc (<<<'HTML')
     * agar aman dari error parsing tanda kutip di dalam HTML.
     */
    private function formatSectionsForPhp(array $sections): string
    {
        if (empty($sections)) {
            return "[]";
        }

        $output = "[\n";
        foreach ($sections as $section) {
            $output .= "        [\n";
            $output .= "            'id' => '" . addslashes($section['id']) . "',\n";
            $output .= "            'label' => '" . addslashes($section['label']) . "',\n";
            $output .= "            'category' => '" . addslashes($section['category']) . "',\n";
            $output .= "            'content' => <<<'HTML'\n";
            $output .= $section['content'] . "\n";
            $output .= "HTML\n";
            $output .= "        ],\n";
        }
        $output .= "    ]";

        return $output;
    }

    /**
     * Memformat array PHP agar rapi (bukan output default var_export yang berantakan)
     */
    private function formatArrayForPhp(array $items): string
    {
        if (empty($items)) {
            return '[]';
        }
        $formatted = "[\n";
        foreach ($items as $item) {
            $formatted .= "            '" . addslashes($item) . "',\n";
        }
        $formatted .= "        ]";
        return $formatted;
    }

    // ═══════════════════════════════════════════════════════════
    // EXTRACTORS (Tetap sama seperti sebelumnya)
    // ═══════════════════════════════════════════════════════════

    private function extractHead(): string
    {
        if (preg_match('/<head[^>]*>(.*?)<\/head>/is', $this->html, $m)) {
            $headContent = trim($m[1]);
            $headContent = preg_replace('/<title>.*?<\/title>/is', '', $headContent);
            return "<head>\n" . $headContent . "\n\n    {!! seo_meta(\$seo ?? null) !!}\n    {!! theme_vite_assets() !!}\n    @livewireStyles\n    @stack('styles')\n</head>";
        }
        return '<head></head>';
    }

    private function extractFooter(): string
    {
        if (preg_match('/<footer[^>]*>.*?<\/footer>/is', $this->html, $m)) {
            return $m[0];
        }
        return '<!-- Footer -->';
    }

    private function extractScripts(): string
    {
        if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $this->html, $bodyMatch)) {
            preg_match_all('/<script\b[^>]*>.*?<\/script>/is', $bodyMatch[1], $m);
            return implode("\n    ", $m[0] ?? []);
        }
        return '';
    }

    private function extractBodyContentWithoutHeaderFooter(): string
    {
        if (!preg_match('/<body[^>]*>(.*?)<\/body>/is', $this->html, $bodyMatch)) {
            return '';
        }
        $bodyContent = $bodyMatch[1];
        $patterns = [
            '/<header\b[^>]*>.*?<\/header>/is',
            '/<footer\b[^>]*>.*?<\/footer>/is',
            '/<nav\b[^>]*>.*?<\/nav>/is',
            '/<script\b[^>]*>.*?<\/script>/is',
            '/<noscript\b[^>]*>.*?<\/noscript>/is',
        ];
        $cleanContent = preg_replace($patterns, '', $bodyContent);
        return trim($cleanContent);
    }

    // ═══════════════════════════════════════════════════════════
    // HELPERS
    // ═══════════════════════════════════════════════════════════

    private function write(string $path, string $content): void
    {
        $content = str_replace(["\r\n", "\r"], "\n", trim($content));
        $content = preg_replace("/\n{3,}/", "\n\n", $content);
        File::put($path, $content . "\n");
    }

    private function ensureDir(string $path): void
    {
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }
}
