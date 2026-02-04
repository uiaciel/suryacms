<?php

namespace Uiaciel\SuryaCms\Services;

use DOMDocument;
use DOMXPath;

/**
 * ThemeLayoutGeneratorService
 *
 * Service untuk generate app.blade.php layout dari HTML yang sudah dinormalisasi.
 * Memisahkan HTML menjadi bagian-bagian:
 * - Head section
 * - Navigation
 * - Main content area
 * - Footer
 * - Scripts
 */
class ThemeLayoutGeneratorService
{
    private $htmlContent;

    private $themeName;

    public function __construct($htmlContent, $themeName = 'theme')
    {
        $this->htmlContent = $htmlContent;
        $this->themeName = $themeName;
    }

    /**
     * Generate app.blade.php dari normalized HTML
     *
     * @return string Blade template content
     */
    public function generate()
    {
        try {
            $dom = new DOMDocument;
            @$dom->loadHTML('<?xml encoding="UTF-8">'.$this->htmlContent);
            $xpath = new DOMXPath($dom);

            // Extract bagian-bagian utama
            $headContent = $this->extractHead($xpath);
            $navigationHtml = $this->extractNavigation($xpath);
            $mainContent = $this->extractMainContent($xpath);
            $footerHtml = $this->extractFooter($xpath);
            $scriptsHtml = $this->extractScripts($xpath);

            // Build Blade template
            $bladeTemplate = $this->buildBladeTemplate(
                $headContent,
                $navigationHtml,
                $mainContent,
                $footerHtml,
                $scriptsHtml
            );

            return $bladeTemplate;
        } catch (\Exception $e) {
            throw new \Exception('Error generating app.blade.php: '.$e->getMessage());
        }
    }

    /**
     * Extract head section dari HTML
     * Ambil seluruh <head>...</head> tag menggunakan regex
     */
    private function extractHead($xpath)
    {
        // Gunakan regex untuk mengekstrak head section dari HTML string
        // Ini lebih reliable daripada DOM karena preserve semua element
        if (preg_match('/<head[^>]*>(.*?)<\/head>/is', $this->htmlContent, $matches)) {
            $headContent = $matches[1];

            // Tambahkan @stack('styles') sebelum </head>
            $headHtml = "<head>\n".trim($headContent)."\n    @stack('styles')\n</head>";

            return $headHtml;
        }

        return '';
    }

    /**
     * Extract navigation dari HTML
     * Cari element dengan id="navigation" dan extract struktur untuk Blade loop
     * Kembalikan HTML dengan struktur Bootstrap navbar untuk @foreach menu items
     */
    private function extractNavigation($xpath)
    {
        // Cari by id="navigation"
        $navNodes = $xpath->query('//*[@id="navigation"]');

        if ($navNodes->length > 0) {
            $navNode = $navNodes->item(0);
            $navHtml = $this->domToString($navNode);

            // Generate Blade navigation template dengan foreach dan submenu support
            return $this->generateBladeNavigation($navHtml);
        }

        // Fallback: Cari <nav> element
        $navNodes = $xpath->query('//nav');
        if ($navNodes->length > 0) {
            $navHtml = $this->domToString($navNodes->item(0));

            return $this->generateBladeNavigation($navHtml);
        }

        return '<!-- Navigation -->';
    }

    /**
     * Generate Blade navigation template dengan foreach dan submenu support
     * Menggunakan struktur Bootstrap navbar collapse dan dropdown
     */
    private function generateBladeNavigation($navHtml)
    {
        // Extract struktur navbar dari original HTML
        // Gunakan pattern Bootstrap navbar-collapse dengan navbar-nav
        $navigationTemplate = <<<'BLADE'
<div class="collapse navbar-collapse" id="navbarCollapse">
    <div class="navbar-nav ms-auto">
        @if (session('locale', 'id') === 'id')
        @foreach ($menus->where('category', 'Indonesia') as $menu)
        @if ($menu->children->count())
        <div class="nav-item dropdown">
            <a href="{{ $menu->link ?? '#' }}" class="nav-link dropdown-toggle text-white"
                @if($menu->children->count()) data-bs-toggle="dropdown" @endif>{{ $menu->name }}</a>
            @if ($menu->children->count())
            <div class="dropdown-menu bg-light rounded-0 m-0">
                @foreach ($menu->children as $submenu)
                <a href="{{ $submenu->link ?? '#' }}" class="dropdown-item">{{ $submenu->name }}</a>
                @endforeach
            </div>
            @endif
        </div>
        @else
        <a href="{{ $menu->link ?? '/' }}" class="nav-item nav-link text-white">{{ $menu->name }}</a>
        @endif
        @endforeach
        @elseif (session('locale', 'id') === 'en')
        @foreach ($menus->where('category', 'English') as $menu)
        @if ($menu->children->count())
        <div class="nav-item dropdown">
            <a href="{{ $menu->link ?? '#' }}" class="nav-link dropdown-toggle text-white"
                @if($menu->children->count()) data-bs-toggle="dropdown" @endif>{{ $menu->name }}</a>
            @if ($menu->children->count())
            <div class="dropdown-menu bg-light rounded-0 m-0">
                @foreach ($menu->children as $submenu)
                <a href="{{ $submenu->link ?? '#' }}" class="dropdown-item">{{ $submenu->name }}</a>
                @endforeach
            </div>
            @endif
        </div>
        @else
        <a href="{{ $menu->link ?? '/' }}" class="nav-item nav-link text-white">{{ $menu->name }}</a>
        @endif
        @endforeach
        @endif
        @if($setting->is_multilingual === 'Yes')
        <div class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle text-white" data-bs-toggle="dropdown">
                @if (session('locale', 'id') === 'id')
                <img src="/frontend/sge/images/id.png" alt="Indonesia" width="20" class="align-middle me-1">
                @else
                <img src="/frontend/sge/images/us.png" alt="English" width="20" class="align-middle me-1">
                @endif
            </a>
            <div class="dropdown-menu bg-light rounded-0 m-0">
                <a href="{{ url('/id/') }}" class="dropdown-item"><img src="/frontend/sge/images/id.png"
                        alt="Indonesia" width="20" class="align-middle me-1">Indonesia</a>
                <a href="{{ url('/en/') }}" class="dropdown-item"><img src="/frontend/sge/images/us.png"
                        alt="English" width="20" class="align-middle me-1">English</a>
            </div>
        </div>
        @endif
    </div>
</div>
BLADE;

        return $navigationTemplate;
    }

    /**
     * Extract main content area
     * Cari <main> atau <section>
     */
    private function extractMainContent($xpath)
    {
        // Cari <main> element
        $mainNodes = $xpath->query('//main');

        if ($mainNodes->length > 0) {
            $mainNode = $mainNodes->item(0);
            $content = '';

            // Extract inner content dari <main>
            foreach ($mainNode->childNodes as $child) {
                if ($child->nodeType == 1) { // Element node
                    $content .= $this->domToString($child);
                }
            }

            return $content ?: '<!-- Main content -->';
        }

        // Fallback: Cari sections
        $sections = $xpath->query('//section');
        if ($sections->length > 0) {
            $content = '';
            foreach ($sections as $section) {
                $content .= $this->domToString($section);
            }

            return $content;
        }

        return '<!-- Main content -->';
    }

    /**
     * Extract footer dari HTML
     * Cari by id="footer" atau <footer> element
     */
    private function extractFooter($xpath)
    {
        // Cari by id="footer"
        $footerNodes = $xpath->query('//*[@id="footer"]');

        if ($footerNodes->length > 0) {
            return $this->domToString($footerNodes->item(0));
        }

        // Fallback: Cari <footer> element
        $footerNodes = $xpath->query('//footer');
        if ($footerNodes->length > 0) {
            return $this->domToString($footerNodes->item(0));
        }

        return '<!-- Footer -->';
    }

    /**
     * Extract scripts dari HTML
     * Ambil semua <script> tags dari akhir body
     */
    private function extractScripts($xpath)
    {
        $scripts = $xpath->query('//body//script');
        $scriptHtml = '';

        foreach ($scripts as $script) {
            $scriptHtml .= $this->domToString($script);
        }

        return $scriptHtml ?: '';
    }

    /**
     * Build Blade template dengan struktur lengkap
     */
    private function buildBladeTemplate($head, $nav, $main, $footer, $scripts)
    {
        $template = <<<'BLADE'
<!DOCTYPE html>
<html lang="id">
BLADE;

        // Add extracted head content (<head>...</head>)
        if (! empty($head)) {
            $template .= "\n".trim($head)."\n";
        }

        $template .= <<<'BLADE'
<body>
    <!-- Navigation -->
BLADE;

        // Add navigation
        if (! empty($nav) && $nav !== '<!-- Navigation -->') {
            $template .= "\n    ".str_replace("\n", "\n    ", trim($nav))."\n";
        } else {
            $template .= "\n    <!-- Navigation Placeholder -->\n";
        }

        $template .= <<<'BLADE'

    <!-- Main Content Area -->
    <main>

        @hasSection('content')
        @yield('content')
        @else
        {{ $slot ?? '' }}
        @endif

    </main>

    <!-- Footer -->
BLADE;

        // Add footer
        if (! empty($footer) && $footer !== '<!-- Footer -->') {
            $template .= "\n    ".str_replace("\n", "\n    ", trim($footer))."\n";
        } else {
            $template .= "\n    <!-- Footer Placeholder -->\n";
        }

        $template .= "\n\n    <!-- Scripts -->\n";

        // Add scripts
        if (! empty($scripts)) {
            $template .= '    '.str_replace("\n", "\n    ", trim($scripts))."\n";
        }

        $template .= <<<'BLADE'

    @stack('scripts')
</body>
</html>
BLADE;

        return $template;
    }

    /**
     * Convert DOM node to string
     */
    private function domToString($node)
    {
        $dom = new DOMDocument;
        $dom->appendChild($dom->importNode($node, true));
        $html = $dom->saveHTML();

        // Remove <?xml> declaration dan <html><body> tags that getDOMDocument adds
        $html = preg_replace('/<\?xml[^>]*\?>/', '', $html);
        $html = preg_replace('/<(!DOCTYPE|html|body)[^>]*>/', '', $html);
        $html = preg_replace('/<\/(html|body)>/', '', $html);

        return trim($html);
    }

    /**
     * Generate editor.blade.php layout
     * Berisikan head, main dengan $slot, scripts dan livewireScripts
     *
     * @return string Blade template content
     */
    public function generateEditor()
    {
        try {
            $dom = new DOMDocument;
            @$dom->loadHTML('<?xml encoding="UTF-8">'.$this->htmlContent);
            $xpath = new DOMXPath($dom);

            // Extract bagian-bagian yang dibutuhkan
            $headContent = $this->extractHead($xpath);
            $scriptsHtml = $this->extractScriptsFromBody($xpath);

            // Build editor Blade template
            $editorTemplate = $this->buildEditorBladeTemplate($headContent, $scriptsHtml);

            return $editorTemplate;
        } catch (\Exception $e) {
            throw new \Exception('Error generating editor.blade.php: '.$e->getMessage());
        }
    }

    /**
     * Extract scripts dari akhir body (setelah footer)
     * Ambil semua script tag yang ada
     */
    private function extractScriptsFromBody($xpath)
    {
        $scripts = $xpath->query('//body//script');
        $scriptHtml = '';

        foreach ($scripts as $script) {
            $scriptHtml .= $this->domToString($script);
        }

        return $scriptHtml ?: '';
    }

    /**
     * Build editor Blade template
     * Head + main dengan $slot + scripts + @livewireScripts
     */
    private function buildEditorBladeTemplate($head, $scripts)
    {
        $template = <<<'BLADE'
<!DOCTYPE html>
<html lang="id">
BLADE;

        // Add extracted head content
        if (! empty($head)) {
            $template .= "\n".trim($head)."\n";
        }

        $template .= <<<'BLADE'
<body>
    <!-- Main Content Area -->
    <main class="main" role="main">
        {{ $slot }}
    </main>

    <!-- Scripts -->
BLADE;

        // Add scripts from body
        if (! empty($scripts)) {
            $template .= "\n    ".str_replace("\n", "\n    ", trim($scripts))."\n";
        }

        $template .= <<<'BLADE'

    @stack('scripts')
    @livewireScripts
</body>
</html>
BLADE;

        return $template;
    }

    /**
     * Generate homepage.blade.php
     * Berisikan extends app + section content dengan $css dan $html
     *
     * @return string Blade template content
     */
    public function generateHomepage()
    {
        $template = <<<'BLADE'
@extends('frontend::app')
@section('content')
    <style>
        {!! $css !!}
    </style>
    {!! $html !!}
@endsection
BLADE;

        return $template;
    }

    /**
     * Generate index.blade.php
     * Berisikan extends app + section content dengan semua section dari index.html
     *
     * @return string Blade template content
     */
    public function generateIndex()
    {
        try {
            $dom = new DOMDocument;
            @$dom->loadHTML('<?xml encoding="UTF-8">'.$this->htmlContent);
            $xpath = new DOMXPath($dom);

            // Extract semua sections dari body (kecuali nav dan footer)
            $sections = $this->extractAllSections($xpath);

            // Build index Blade template
            $template = <<<'BLADE'
@extends('frontend::app')
@section('content')
BLADE;

            if (! empty($sections)) {
                $template .= "\n".trim($sections)."\n";
            }

            $template .= <<<'BLADE'
@endsection
BLADE;

            return $template;
        } catch (\Exception $e) {
            throw new \Exception('Error generating index.blade.php: '.$e->getMessage());
        }
    }

    /**
     * Extract semua section element dari body
     * (kecuali navigation dan footer yang sudah di-handle)
     */
    private function extractAllSections($xpath)
    {
        $sections = $xpath->query('//body/section');
        $sectionsHtml = '';

        if ($sections->length > 0) {
            foreach ($sections as $section) {
                $sectionsHtml .= $this->domToString($section)."\n\n";
            }
        }

        return trim($sectionsHtml);
    }

    /**
     * Generate theme.php configuration file
     * Berisikan info tema, assets, custom_blocks, dan sections
     *
     * @return string PHP code untuk theme.php
     */
    public function generateThemeConfig()
    {
        try {
            $dom = new DOMDocument;
            @$dom->loadHTML('<?xml encoding="UTF-8">'.$this->htmlContent);
            $xpath = new DOMXPath($dom);

            // Extract assets yang sebenarnya dari HTML
            $assets = $this->extractAssets($xpath);

            // Extract sections dari HTML
            $sections = $this->extractSectionsForConfig($xpath);

            // Build theme config array
            $configArray = [
                'info' => [
                    'name' => ucfirst($this->themeName),
                    'path' => strtolower($this->themeName),
                    'version' => '1.0.0',
                    'author' => 'Theme Generator',
                    'url' => 'https://uiaciel.com',
                    'license' => 'MIT',
                    'description' => "Tema {$this->themeName} yang di-generate otomatis.",
                    'theme_preview' => "/frontend/{$this->themeName}/theme_preview.jpg",
                ],
                'assets' => $assets,
                'custom_blocks' => [
                    [
                        'id' => 'gallery-block',
                        'label' => 'Gallery',
                        'category' => 'Plugin',
                        'icon' => '🖼️',
                        'content' => '[[gallery]]',
                    ],
                    [
                        'id' => 'blogs-block',
                        'label' => 'Blogs',
                        'category' => 'Plugin',
                        'icon' => '📝',
                        'content' => '[[blog]]',
                    ],
                    [
                        'id' => 'youtube-block',
                        'label' => 'Youtube',
                        'category' => 'Plugin',
                        'icon' => '📺',
                        'content' => '[[youtube]]',
                    ],
                ],
                'sections' => $sections,
            ];

            // Convert to PHP code
            return $this->arrayToPhpCode($configArray);

        } catch (\Exception $e) {
            throw new \Exception('Error generating theme.php: '.$e->getMessage());
        }
    }

    /**
     * Extract assets (styles dan scripts) dari HTML
     * Ambil link dan script tags dari head dan body
     */
    private function extractAssets($xpath)
    {
        // Extract link stylesheets dari head
        $styles = [];
        $linkNodes = $xpath->query('//head//link[@rel="stylesheet"]');

        foreach ($linkNodes as $link) {
            $href = $link->getAttribute('href');
            if (! empty($href)) {
                $styles[] = $href;
            }
        }

        // Extract external scripts dari body (exclude inline scripts)
        $scripts = [];
        $scriptNodes = $xpath->query('//body//script[@src]');

        foreach ($scriptNodes as $script) {
            $src = $script->getAttribute('src');
            if (! empty($src)) {
                $scripts[] = $src;
            }
        }

        return [
            'styles' => ! empty($styles) ? $styles : ['/css/bootstrap.min.css', '/css/styles.css'],
            'scripts' => ! empty($scripts) ? $scripts : ['/js/bootstrap.bundle.min.js', '/js/scripts.js'],
        ];
    }

    /**
     * Extract sections dari HTML untuk config
     * Generate array of sections dari body sections
     */
    private function extractSectionsForConfig($xpath)
    {
        $sections = $xpath->query('//body/section');
        $sectionsArray = [];
        $counter = 1;

        if ($sections->length > 0) {
            foreach ($sections as $section) {
                $sectionContent = $this->domToString($section);

                // Generate section ID dari section element
                $sectionId = $section->getAttribute('id') ?: "section-{$counter}";

                $sectionsArray[] = [
                    'id' => $sectionId,
                    'label' => 'Section '.$counter,
                    'category' => 'Themes Sections',
                    'icon' => $this->getNumberIcon($counter),
                    'content' => $sectionContent,
                ];

                $counter++;
            }
        }

        return $sectionsArray;
    }

    /**
     * Get numbered icon emoji
     */
    private function getNumberIcon($number)
    {
        $icons = ['1️⃣', '2️⃣', '3️⃣', '4️⃣', '5️⃣', '6️⃣', '7️⃣', '8️⃣', '9️⃣', '🔟'];

        return $icons[$number - 1] ?? '📌';
    }

    /**
     * Convert PHP array to PHP code string
     * Format tanpa numeric keys untuk output yang lebih rapi
     */
    private function arrayToPhpCode($array, $indent = 0)
    {
        $baseIndent = str_repeat('    ', $indent);
        $nextIndent = str_repeat('    ', $indent + 1);
        $result = "[\n";

        foreach ($array as $key => $value) {
            // Jika key adalah numeric dan sequential, skip key display
            $isNumericKey = is_int($key);

            if ($isNumericKey) {
                $result .= $nextIndent;
            } else {
                $result .= $nextIndent."'".$key."' => ";
            }

            $result .= $this->formatValue($value, $indent + 1).",\n";
        }

        $result .= $baseIndent.']';

        return $result;
    }

    /**
     * Format value untuk PHP code
     * Handle string, array, dan value lainnya
     */
    private function formatValue($value, $indent = 0)
    {
        if (is_array($value)) {
            if (empty($value)) {
                return '[]';
            }

            // Check apakah ini indexed array (numeric keys sequential dari 0)
            $keys = array_keys($value);
            $isIndexed = ! empty($keys) && $keys === range(0, count($keys) - 1);

            if ($isIndexed) {
                // Format indexed array tanpa numeric keys
                $nextIndent = str_repeat('    ', $indent + 1);
                $result = "[\n";

                foreach ($value as $item) {
                    $result .= $nextIndent.$this->formatValue($item, $indent + 1).",\n";
                }

                $result .= str_repeat('    ', $indent).']';

                return $result;
            } else {
                // Format associative array dengan keys
                $nextIndent = str_repeat('    ', $indent + 1);
                $result = "[\n";

                foreach ($value as $k => $v) {
                    $result .= $nextIndent."'".$k."' => ".$this->formatValue($v, $indent + 1).",\n";
                }

                $result .= str_repeat('    ', $indent).']';

                return $result;
            }
        } elseif (is_string($value)) {
            // Escape single quotes dalam string
            return "'".str_replace("'", "\\'", $value)."'";
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_null($value)) {
            return 'null';
        } else {
            return (string) $value;
        }
    }

    /**
     * Get statistics tentang struktur yang di-extract
     */
    public function getStatistics()
    {
        return [
            'theme_name' => $this->themeName,
            'head_content_extracted' => ! empty($this->extractHead(new DOMXPath(new DOMDocument))),
            'navigation_found' => ! empty($this->extractNavigation(new DOMXPath(new DOMDocument))),
            'main_content_found' => ! empty($this->extractMainContent(new DOMXPath(new DOMDocument))),
            'footer_found' => ! empty($this->extractFooter(new DOMXPath(new DOMDocument))),
            'scripts_found' => ! empty($this->extractScripts(new DOMXPath(new DOMDocument))),
        ];
    }
}
