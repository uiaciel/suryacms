<?php

namespace Uiaciel\SuryaCms\Services;

use DOMDocument;
use DOMXPath;

class HeadNormalizerService
{
    private $dom;

    private $xpath;

    private $themeName;

    private $originalTitle;

    private $html;

    public function __construct($htmlContent, $themeName)
    {
        $this->themeName = $themeName;
        $this->loadHTML($htmlContent);
    }

    private function loadHTML($htmlContent)
    {
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $this->dom->loadHTML(
            mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        $this->xpath = new DOMXPath($this->dom);
    }

    public function normalize()
    {
        // Extract data from original head
        $this->originalTitle = $this->extractTitle();
        $cssLinks = $this->extractCSSLinks();
        $jsScripts = $this->extractJSScripts();
        $externalFonts = $this->extractExternalFonts();

        // Build new head content as string
        $newHeadContent = $this->buildNewHead($cssLinks, $jsScripts, $externalFonts);

        // Get HTML and replace head section using string manipulation
        // This preserves Blade syntax without entity encoding
        $html = $this->dom->saveHTML();

        $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8'); // <-- Baris perbaikan

        // Find and replace head section using regex
        $pattern = '/<head[^>]*>.*?<\/head>/is';
        if (preg_match($pattern, $html)) {
            // Head exists - replace it
            $html = preg_replace($pattern, $newHeadContent, $html, 1);
        } else {
            // Head doesn't exist - insert before body
            $html = preg_replace('/(<body[^>]*>)/i', $newHeadContent."\n$1", $html, 1);
        }

        // Update html lang attribute
        $html = $this->updateHtmlLang($html);

        $this->html = $html;

        return $this->html;
    }

    private function extractTitle()
    {
        $titleNode = $this->xpath->query('//head/title')->item(0);

        return $titleNode ? trim($titleNode->textContent) : 'Website Title';
    }

    private function extractCSSLinks()
    {
        $cssLinks = [];
        $links = $this->xpath->query('//head/link[@rel="stylesheet" or @rel="preload"]');

        if (! $links instanceof \DOMNodeList) {
            return $cssLinks;
        }

        foreach ($links as $link) {
            $href = $link->getAttribute('href');

            // Skip if it's favicon or manifest
            if (strpos($href, 'favicon') !== false || strpos($href, 'manifest') !== false) {
                continue;
            }

            $cssLinks[] = [
                'href' => $this->adjustPath($href),
                'rel' => $link->getAttribute('rel') ?: 'stylesheet',
                'integrity' => $link->getAttribute('integrity'),
                'crossorigin' => $link->getAttribute('crossorigin'),
                'media' => $link->getAttribute('media'),
                'is_external' => $this->isExternalUrl($href),
            ];
        }

        return $cssLinks;
    }

    private function extractJSScripts()
    {
        $jsScripts = [];
        $scripts = $this->xpath->query('//head/script[@src]');

        if (! $scripts instanceof \DOMNodeList) {
            return $jsScripts;
        }

        foreach ($scripts as $script) {
            $src = $script->getAttribute('src');

            $jsScripts[] = [
                'src' => $this->adjustPath($src),
                'integrity' => $script->getAttribute('integrity'),
                'crossorigin' => $script->getAttribute('crossorigin'),
                'async' => $script->hasAttribute('async'),
                'defer' => $script->hasAttribute('defer'),
                'is_external' => $this->isExternalUrl($src),
            ];
        }

        return $jsScripts;
    }

    private function extractExternalFonts()
    {
        $fonts = [];

        // Google Fonts preconnect
        $preconnects = $this->xpath->query('//head/link[@rel="preconnect"]');
        if ($preconnects instanceof \DOMNodeList) {
            foreach ($preconnects as $link) {
                $href = $link->getAttribute('href');
                if (strpos($href, 'fonts.googleapis') !== false || strpos($href, 'fonts.gstatic') !== false) {
                    $fonts[] = [
                        'type' => 'preconnect',
                        'href' => $href,
                        'crossorigin' => $link->getAttribute('crossorigin'),
                    ];
                }
            }
        }

        // Google Fonts stylesheet
        $fontLinks = $this->xpath->query('//head/link[@href*="fonts.googleapis.com"]');
        if ($fontLinks instanceof \DOMNodeList) {
            foreach ($fontLinks as $link) {
                $fonts[] = [
                    'type' => 'stylesheet',
                    'href' => $link->getAttribute('href'),
                ];
            }
        }

        return $fonts;
    }

    private function adjustPath($path)
    {
        // Skip external URLs
        if ($this->isExternalUrl($path)) {
            return $path;
        }

        // Remove leading ./ or /
        $path = ltrim($path, './');
        $path = ltrim($path, '/');

        // Add theme path
        return "/frontend/{$this->themeName}/{$path}";
    }

    private function isExternalUrl($url)
    {
        return preg_match('/^(https?:)?\/\//', $url) === 1;
    }

    private function buildNewHead($cssLinks, $jsScripts, $externalFonts)
    {
        $head = '<head>'."\n";

        // 1. Charset & Language
        $head .= '    <meta charset="utf-8" />'."\n";

        // 2. SEO Yield (untuk override dari pages)
        $head .= '    @yield(\'seo\')'."\n";

        // 3. Conditional SEO Meta (EN/ID)
        $head .= $this->buildSEOMeta();

        // 4. Favicon Structure (dari tema standard)
        $head .= $this->buildFaviconMeta();

        // 5. External Fonts (dari index.html)
        $head .= $this->buildFontLinks($externalFonts);

        // 6. CSS Links (dari index.html)
        $head .= $this->buildCSSLinks($cssLinks);

        // 7. PWA Support
        $head .= $this->buildPWAMeta();

        // 8. Service Worker Script
        $head .= $this->buildServiceWorkerScript();

        // 9. Google Integrations
        $head .= $this->buildGoogleMeta();

        // 10. Head Scripts (dari index.html)
        $head .= $this->buildJSScripts($jsScripts);

        $head .= '</head>';

        return $head;
    }

    private function buildSEOMeta()
    {
        return <<<'BLADE'
    @if($setting->language == "en")
    <title>{{ $seo->title ?? $setting->sitename_translation }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="{{ $seo->keyword ?? $setting->keywords_translation }}" name="keywords" />
    <meta content="{{ $seo->description ?? $setting->description_translation }}" name="description" />
    <meta name="author" content="Kreasi Tek Media">
    <meta name="robots" content="index, follow">

    <meta property="og:title" content="{{ $seo->title ?? $setting->sitename_translation }}">
    <meta property="og:description" content="{{ $seo->description ?? $setting->description_translation }}">
    <meta property="og:image" content="{{ $seo->image ?? $setting->images }}">
    <meta property="og:url" content="{{ $seo->url ?? $setting->url }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $seo->title ?? $setting->sitename }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo->title ?? $setting->sitename_translation }}">
    <meta name="twitter:description" content="{{ $seo->description ?? $setting->description_translation }}">
    <meta name="twitter:image" content="{{ $seo->image ?? $setting->images }}">
    <meta name="twitter:url" content="{{ $seo->url ?? $setting->url }}">
    @else
    <title>{{ $seo->title ?? $setting->sitename }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="{{ $seo->keyword ?? $setting->keywords }}" name="keywords" />
    <meta content="{{ $seo->description ?? $setting->description }}" name="description" />
    <meta name="author" content="Kreasi Tek Media">
    <meta name="robots" content="index, follow">

    <meta property="og:title" content="{{ $seo->title ?? $setting->sitename }}">
    <meta property="og:description" content="{{ $seo->description ?? $setting->description }}">
    <meta property="og:image" content="{{ $seo->image ?? $setting->images }}">
    <meta property="og:url" content="{{ $seo->url ?? $setting->url }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $seo->title ?? $setting->sitename }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo->title ?? $setting->sitename }}">
    <meta name="twitter:description" content="{{ $seo->description ?? $setting->description }}">
    <meta name="twitter:image" content="{{ $seo->image ?? $setting->images }}">
    <meta name="twitter:url" content="{{ $seo->url ?? $setting->url }}">
    @endif

BLADE;
    }

    private function buildFaviconMeta()
    {
        return <<<BLADE
    <link href="/frontend/{$this->themeName}/favicon.ico" rel="icon" />

    <link rel="apple-touch-icon" sizes="180x180" href="/frontend/{$this->themeName}/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/frontend/{$this->themeName}/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/frontend/{$this->themeName}/favicon/favicon-16x16.png">

    <link rel="mask-icon" href="/frontend/{$this->themeName}/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#9f00a7">
    <meta name="theme-color" content="#ffffff">

BLADE;
    }

    private function buildFontLinks($fonts)
    {
        $html = '';

        foreach ($fonts as $font) {
            if ($font['type'] === 'preconnect') {
                $crossorigin = $font['crossorigin'] ? ' crossorigin' : '';
                $html .= "    <link rel=\"preconnect\" href=\"{$font['href']}\"{$crossorigin} />\n";
            } elseif ($font['type'] === 'stylesheet') {
                $html .= "    <link href=\"{$font['href']}\" rel=\"stylesheet\" />\n";
            }
        }

        return $html ? "\n".$html : '';
    }

    private function buildCSSLinks($cssLinks)
    {
        $html = "\n";

        foreach ($cssLinks as $css) {
            $attributes = "href=\"{$css['href']}\" rel=\"{$css['rel']}\"";

            if ($css['integrity']) {
                $attributes .= " integrity=\"{$css['integrity']}\"";
            }
            if ($css['crossorigin']) {
                $attributes .= " crossorigin=\"{$css['crossorigin']}\"";
            }
            if ($css['media']) {
                $attributes .= " media=\"{$css['media']}\"";
            }

            $html .= "    <link {$attributes} />\n";
        }

        return $html;
    }

    private function buildPWAMeta()
    {
        return <<<BLADE

    <link rel="manifest" href="/frontend/{$this->themeName}/manifest.json">

    <meta name="theme-color" content="#000000">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

BLADE;
    }

    private function buildServiceWorkerScript()
    {
        return <<<BLADE
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/frontend/{$this->themeName}/service-worker.js')
                    .then(reg => console.log('✅ Service Worker registered:', reg.scope))
                    .catch(err => console.error('❌ Service Worker registration failed:', err));
            });
        }
    </script>

BLADE;
    }

    private function buildGoogleMeta()
    {
        return <<<'BLADE'
    <meta name="google-site-verification" content="{{ $setting->googlesiteverification }}">
    <meta name="google_analytics" content="{{ $setting->google_analytics }}">
    <meta name="google_adsense" content="{{ $setting->google_adsense }}">

BLADE;
    }

    private function buildJSScripts($jsScripts)
    {
        $html = "\n";

        foreach ($jsScripts as $js) {
            $attributes = "src=\"{$js['src']}\"";

            if ($js['integrity']) {
                $attributes .= " integrity=\"{$js['integrity']}\"";
            }
            if ($js['crossorigin']) {
                $attributes .= " crossorigin=\"{$js['crossorigin']}\"";
            }
            if ($js['async']) {
                $attributes .= ' async';
            }
            if ($js['defer']) {
                $attributes .= ' defer';
            }

            $html .= "    <script {$attributes}></script>\n";
        }

        return $html;
    }

    private function updateHtmlLang($html)
    {
        // Replace lang attribute using string manipulation to preserve Blade syntax
        $html = preg_replace(
            '/<html[^>]*\slang="[^"]*"/',
            '<html lang="{{$setting->language}}"',
            $html,
            1
        );

        // If no lang attribute exists, add it
        if (! preg_match('/\slang=/', $html)) {
            $html = preg_replace(
                '/(<html[^>]*)(>)/i',
                '$1 lang="{{$setting->language}}"$2',
                $html,
                1
            );
        }

        return $html;
    }

    public function getOriginalTitle()
    {
        return $this->originalTitle;
    }
}
