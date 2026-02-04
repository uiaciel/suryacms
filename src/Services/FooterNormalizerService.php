<?php

namespace Uiaciel\SuryaCms\Services;

use DOMDocument;
use DOMXPath;

class FooterNormalizerService
{
    private $dom;

    private $xpath;

    private $themeName;

    public function __construct($htmlContent, $themeName = 'default')
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
        try {
            $scripts = $this->extractScripts();
            $inlineScripts = $this->extractInlineScripts();

            // Build footer scripts section
            $footerScriptsContent = $this->buildFooterScripts($scripts, $inlineScripts);

            // Get HTML and insert footer scripts before </body>
            $html = $this->dom->saveHTML();

            // Insert footer scripts before closing body tag
            $html = preg_replace('/(<\/body>)/i', $footerScriptsContent."\n$1", $html, 1);

            return $html;
        } catch (\Exception $e) {
            return $this->dom->saveHTML();
        }
    }

    private function extractScripts()
    {
        $scripts = [];
        $scriptElements = $this->xpath->query('//body//script[@src]');

        if ($scriptElements instanceof \DOMNodeList) {
            foreach ($scriptElements as $script) {
                $src = $script->getAttribute('src');

                $scripts[] = [
                    'src' => $this->adjustPath($src),
                    'integrity' => $script->getAttribute('integrity'),
                    'crossorigin' => $script->getAttribute('crossorigin'),
                    'async' => $script->hasAttribute('async'),
                    'defer' => $script->hasAttribute('defer'),
                    'is_external' => $this->isExternalUrl($src),
                ];
            }
        }

        return $scripts;
    }

    private function extractInlineScripts()
    {
        $inlineScripts = [];
        $scriptElements = $this->xpath->query('//body//script[not(@src)]');

        if ($scriptElements instanceof \DOMNodeList) {
            foreach ($scriptElements as $script) {
                $content = trim($script->textContent);

                if (! empty($content)) {
                    $inlineScripts[] = [
                        'content' => $content,
                        'type' => $script->getAttribute('type') ?: 'text/javascript',
                    ];
                }
            }
        }

        return $inlineScripts;
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

    private function buildFooterScripts($scripts, $inlineScripts)
    {
        $html = "\n    <!-- Footer Scripts -->\n";

        // External scripts
        foreach ($scripts as $script) {
            $attributes = "src=\"{$script['src']}\"";

            if ($script['integrity']) {
                $attributes .= " integrity=\"{$script['integrity']}\"";
            }
            if ($script['crossorigin']) {
                $attributes .= " crossorigin=\"{$script['crossorigin']}\"";
            }
            if ($script['async']) {
                $attributes .= ' async';
            }
            if ($script['defer']) {
                $attributes .= ' defer';
            }

            $html .= "    <script {$attributes}></script>\n";
        }

        // Inline scripts
        foreach ($inlineScripts as $script) {
            $html .= "    <script type=\"{$script['type']}\">\n";
            $html .= $this->indentContent($script['content'], 8);
            $html .= "    </script>\n";
        }

        return $html;
    }

    private function indentContent($content, $spaces = 8)
    {
        $indent = str_repeat(' ', $spaces);
        $lines = explode("\n", $content);

        foreach ($lines as &$line) {
            if (trim($line)) {
                $line = $indent.$line;
            }
        }

        return implode("\n", $lines)."\n";
    }

    public function getScriptStatistics()
    {
        $scripts = $this->extractScripts();
        $inlineScripts = $this->extractInlineScripts();

        return [
            'external_scripts' => count($scripts),
            'inline_scripts' => count($inlineScripts),
            'total_scripts' => count($scripts) + count($inlineScripts),
        ];
    }
}
