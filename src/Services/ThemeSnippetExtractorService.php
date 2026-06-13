<?php

namespace Uiaciel\SuryaCms\Services;

use DOMDocument;
use DOMXPath;

/**
 * ThemeSnippetExtractorService
 *
 * Ekstrak semua potongan kode (snippets) dari index.html:
 * - Inline CSS (<style> blocks)
 * - CSS external links (<link rel="stylesheet">)
 * - Google Fonts / Font preconnect
 * - Icon libraries (Font Awesome, Bootstrap Icons, dll)
 * - Inline JS (<script> tanpa src)
 * - External JS (<script src="...">)
 */
class ThemeSnippetExtractorService
{
    private string $html;
    private ?DOMDocument $dom = null;
    private ?DOMXPath $xpath = null;

    public function __construct(string $htmlContent)
    {
        $this->html = $htmlContent;
        $this->loadDom();
    }

    private function loadDom(): void
    {
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $this->dom->loadHTML(
            mb_convert_encoding($this->html, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        $this->xpath = new DOMXPath($this->dom);
    }

    /**
     * Ekstrak semua snippets sekaligus
     */
    public function extractAll(): array
    {
        return [
            'inline_css'  => $this->extractInlineCSS(),
            'css_links'   => $this->extractCSSLinks(),
            'fonts'       => $this->extractFonts(),
            'icons'       => $this->extractIcons(),
            'inline_js'   => $this->extractInlineJS(),
            'js_links'    => $this->extractJSLinks(),
        ];
    }

    /**
     * Ekstrak semua <style> block dari head dan body
     */
    public function extractInlineCSS(): array
    {
        $results = [];
        $styleNodes = $this->xpath->query('//style');

        foreach ($styleNodes as $style) {
            $content = trim($style->textContent);
            if (!empty($content)) {
                $results[] = [
                    'tag'     => '<style>',
                    'content' => $content,
                    'lines'   => substr_count($content, "\n") + 1,
                    'copy'    => "<style>\n" . $content . "\n</style>",
                ];
            }
        }

        return $results;
    }

    /**
     * Ekstrak semua <link rel="stylesheet"> dari head
     */
    public function extractCSSLinks(): array
    {
        $results = [];
        $links = $this->xpath->query('//head/link[@rel="stylesheet"]');

        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            $integrity = $link->getAttribute('integrity');
            $crossorigin = $link->getAttribute('crossorigin');

            $attrs = "rel=\"stylesheet\" href=\"{$href}\"";
            if ($integrity) $attrs .= " integrity=\"{$integrity}\"";
            if ($crossorigin) $attrs .= " crossorigin=\"{$crossorigin}\"";

            $tag = "<link {$attrs}>";

            $results[] = [
                'href'        => $href,
                'tag'         => $tag,
                'is_external' => $this->isExternal($href),
                'type'        => $this->guessCSSType($href),
                'copy'        => $tag,
            ];
        }

        return $results;
    }

    /**
     * Ekstrak font links (Google Fonts, font preconnect)
     */
    public function extractFonts(): array
    {
        $results = [];

        // Preconnect
        $preconnects = $this->xpath->query('//head/link[@rel="preconnect"]');
        foreach ($preconnects as $link) {
            $href = $link->getAttribute('href');
            $crossorigin = $link->getAttribute('crossorigin');
            $attrs = "rel=\"preconnect\" href=\"{$href}\"";
            if ($crossorigin) $attrs .= " crossorigin=\"{$crossorigin}\"";
            $tag = "<link {$attrs}>";

            $results[] = [
                'href'  => $href,
                'tag'   => $tag,
                'type'  => 'preconnect',
                'copy'  => $tag,
            ];
        }

        // Google Fonts stylesheets
        $fontLinks = $this->xpath->query('//head/link[contains(@href, "fonts.googleapis.com") or contains(@href, "fonts.gstatic.com")]');
        foreach ($fontLinks as $link) {
            $href = $link->getAttribute('href');
            $tag = "<link rel=\"stylesheet\" href=\"{$href}\">";

            $results[] = [
                'href'  => $href,
                'tag'   => $tag,
                'type'  => 'google-font',
                'copy'  => $tag,
            ];
        }

        // @import dalam style
        $styleNodes = $this->xpath->query('//style');
        foreach ($styleNodes as $style) {
            $content = $style->textContent;
            preg_match_all('/@import\s+url\([\'"]?(https?:\/\/fonts\.googleapis\.com[^\'")\s]+)[\'"]?\)/i', $content, $matches);
            foreach ($matches[1] as $url) {
                $tag = "@import url('{$url}');";
                $results[] = [
                    'href' => $url,
                    'tag'  => $tag,
                    'type' => 'css-import',
                    'copy' => $tag,
                ];
            }
        }

        return $results;
    }

    /**
     * Ekstrak icon library links (Font Awesome, Bootstrap Icons, dll)
     */
    public function extractIcons(): array
    {
        $results = [];
        $iconPatterns = [
            'font-awesome'     => ['fontawesome', 'font-awesome', 'fa.'],
            'bootstrap-icons'  => ['bootstrap-icons', 'bi.'],
            'material-icons'   => ['material-icons', 'material.icons'],
            'ionicons'         => ['ionicons'],
            'feather'          => ['feather'],
            'remixicon'        => ['remixicon'],
            'heroicons'        => ['heroicons'],
            'phosphor'         => ['phosphor'],
        ];

        // Check <link> tags
        $links = $this->xpath->query('//head/link[@rel="stylesheet"]');
        foreach ($links as $link) {
            $href = strtolower($link->getAttribute('href'));
            foreach ($iconPatterns as $library => $patterns) {
                foreach ($patterns as $pattern) {
                    if (str_contains($href, $pattern)) {
                        $originalHref = $link->getAttribute('href');
                        $attrs = "rel=\"stylesheet\" href=\"{$originalHref}\"";
                        $integrity = $link->getAttribute('integrity');
                        if ($integrity) $attrs .= " integrity=\"{$integrity}\"";
                        $tag = "<link {$attrs}>";

                        $results[] = [
                            'library' => $library,
                            'href'    => $originalHref,
                            'tag'     => $tag,
                            'type'    => 'link',
                            'copy'    => $tag,
                        ];
                        break 2;
                    }
                }
            }
        }

        // Check <script> tags for icon libraries
        $scripts = $this->xpath->query('//script[@src]');
        foreach ($scripts as $script) {
            $src = strtolower($script->getAttribute('src'));
            foreach ($iconPatterns as $library => $patterns) {
                foreach ($patterns as $pattern) {
                    if (str_contains($src, $pattern)) {
                        $originalSrc = $script->getAttribute('src');
                        $tag = "<script src=\"{$originalSrc}\"></script>";
                        $results[] = [
                            'library' => $library,
                            'href'    => $originalSrc,
                            'tag'     => $tag,
                            'type'    => 'script',
                            'copy'    => $tag,
                        ];
                        break 2;
                    }
                }
            }
        }

        return $results;
    }

    /**
     * Ekstrak semua inline <script> (tanpa src)
     */
    public function extractInlineJS(): array
    {
        $results = [];
        $scripts = $this->xpath->query('//script[not(@src)]');

        foreach ($scripts as $script) {
            $content = trim($script->textContent);
            if (!empty($content) && strlen($content) > 10) {
                $preview = mb_strimwidth($content, 0, 80, '...');
                $results[] = [
                    'content' => $content,
                    'preview' => $preview,
                    'lines'   => substr_count($content, "\n") + 1,
                    'copy'    => "<script>\n" . $content . "\n</script>",
                ];
            }
        }

        return $results;
    }

    /**
     * Ekstrak semua external <script src="...">
     */
    public function extractJSLinks(): array
    {
        $results = [];
        $scripts = $this->xpath->query('//script[@src]');

        foreach ($scripts as $script) {
            $src = $script->getAttribute('src');
            $defer = $script->hasAttribute('defer');
            $async = $script->hasAttribute('async');
            $integrity = $script->getAttribute('integrity');

            $attrs = "src=\"{$src}\"";
            if ($defer) $attrs .= ' defer';
            if ($async) $attrs .= ' async';
            if ($integrity) $attrs .= " integrity=\"{$integrity}\"";

            $tag = "<script {$attrs}></script>";

            $results[] = [
                'src'         => $src,
                'tag'         => $tag,
                'defer'       => $defer,
                'async'       => $async,
                'is_external' => $this->isExternal($src),
                'type'        => $this->guessJSType($src),
                'copy'        => $tag,
            ];
        }

        return $results;
    }

    /**
     * Cek apakah URL adalah external
     */
    private function isExternal(string $url): bool
    {
        return (bool) preg_match('/^(https?:)?\/\//', $url);
    }

    /**
     * Tebak tipe CSS berdasarkan nama file
     */
    private function guessCSSType(string $href): string
    {
        $href = strtolower($href);
        if (str_contains($href, 'bootstrap')) return 'Bootstrap';
        if (str_contains($href, 'tailwind')) return 'Tailwind';
        if (str_contains($href, 'bulma')) return 'Bulma';
        if (str_contains($href, 'normalize')) return 'Normalize';
        if (str_contains($href, 'reset')) return 'Reset';
        if (str_contains($href, 'animate')) return 'Animate';
        if (str_contains($href, 'owl') || str_contains($href, 'slick') || str_contains($href, 'swiper')) return 'Slider';
        if (str_contains($href, 'font') || str_contains($href, 'icon')) return 'Font/Icon';
        if ($this->isExternal($href)) return 'External';
        return 'Local';
    }

    /**
     * Tebak tipe JS berdasarkan nama file
     */
    private function guessJSType(string $src): string
    {
        $src = strtolower($src);
        if (str_contains($src, 'jquery')) return 'jQuery';
        if (str_contains($src, 'bootstrap')) return 'Bootstrap';
        if (str_contains($src, 'alpine')) return 'AlpineJS';
        if (str_contains($src, 'livewire')) return 'Livewire';
        if (str_contains($src, 'vue')) return 'Vue';
        if (str_contains($src, 'react')) return 'React';
        if (str_contains($src, 'owl') || str_contains($src, 'slick') || str_contains($src, 'swiper')) return 'Slider';
        if (str_contains($src, 'gsap') || str_contains($src, 'wow') || str_contains($src, 'aos')) return 'Animation';
        if ($this->isExternal($src)) return 'External';
        return 'Local';
    }
}
