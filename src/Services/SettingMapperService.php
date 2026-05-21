<?php

namespace Uiaciel\SuryaCms\Services;

use DOMDocument;
use DOMXPath;

class SettingMapperService
{
    private $dom;

    private $xpath;

    private $dataMap = [];

    // bagian head tidak perlu di setting mapper

    // alt="Logo" tambahkan atau ubah src="{{ $setting->logo }}"
    // rel="icon" tambahkan atau ubah href="{{ $setting->favicon }}"

    public function __construct($htmlContent, array $dataMap = [])
    {
        $this->dataMap = $dataMap;
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

    public function map()
    {
        if (empty($this->dataMap)) {
            return $this->dom->saveHTML();
        }

        $html = $this->dom->saveHTML();

        // Replace text content mapping using preg_replace with SKIP/FAIL verbs (safe from HTML attributes/scripts/styles)
        foreach ($this->dataMap as $placeholder => $replacement) {
            // Skip link attributes mapping (handled separately)
            if (in_array($placeholder, ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'tiktok'])) {
                continue;
            }

            // Matches <script>...</script>, <style>...</style>, and any tag <...>, skipping them,
            // while matching the placeholder only as a whole word outside of them.
            $pattern = '~<(script|style)[^>]*>.*?<\/\1>|<[^>]+>(*SKIP)(*F)|\b' . preg_quote($placeholder, '~') . '\b~is';
            $html = preg_replace($pattern, $replacement, $html);
        }

        // Replace href attributes mapping
        $hrefMappings = [

            'facebook',
            'twitter',
            'instagram',
            'linkedin',
            'youtube',
            'tiktok',
        ];

        foreach ($hrefMappings as $attr) {
            if (isset($this->dataMap[$attr])) {
                $replacement = $this->dataMap[$attr];

                // Use preg_quote to safely escape the replacement value
                $escapedReplacement = preg_quote($replacement, '/');

                // Find href attributes and replace their values
                $pattern = '/href="([^"]*'.preg_quote($attr, '/').'[^"]*)"/i';
                $html = preg_replace(
                    $pattern,
                    'href="'.$replacement.'"',
                    $html
                );
            }
        }

        return $html;
    }

    public function mapWithDetection()
    {
        $html = $this->dom->saveHTML();

        // Auto-detect and map common patterns
        $autoMappings = $this->detectCommonPatterns($html);

        foreach ($autoMappings as $pattern => $replacement) {
            $regexPattern = '~<(script|style)[^>]*>.*?<\/\1>|<[^>]+>(*SKIP)(*F)|\b' . preg_quote($pattern, '~') . '\b~is';
            $html = preg_replace($regexPattern, $replacement, $html);
        }

        return $html;
    }

    private function detectCommonPatterns($html)
    {
        $mappings = [];

        // Detect social media links
        $socialPatterns = [
            'facebook' => ['facebook.com', 'fb.com', '@facebook'],
            'twitter' => ['twitter.com', 'x.com', '@twitter'],
            'instagram' => ['instagram.com', 'ig.com', '@instagram'],
            'linkedin' => ['linkedin.com', '@linkedin'],
            'youtube' => ['youtube.com', 'youtu.be', '@youtube'],
            'tiktok' => ['tiktok.com', '@tiktok'],
        ];

        foreach ($socialPatterns as $social => $patterns) {
            foreach ($patterns as $pattern) {
                if (stripos($html, $pattern) !== false && isset($this->dataMap[$social])) {
                    $mappings[$pattern] = $this->dataMap[$social];
                }
            }
        }

        // Detect common text patterns
        $textPatterns = [
            'Phone' => 'phone',
            'Email' => 'email',
            'Address' => 'address',
            'Copyright' => 'copyright-text',
        ];

        foreach ($textPatterns as $pattern => $key) {
            if (stripos($html, $pattern) !== false && isset($this->dataMap[$key])) {
                $mappings[$pattern] = $this->dataMap[$key];
            }
        }

        return $mappings;
    }

    public function getDataMap()
    {
        return $this->dataMap;
    }

    public function setDataMap(array $dataMap)
    {
        $this->dataMap = $dataMap;

        return $this;
    }

    public function getMappingReport()
    {
        return [
            'total_mappings' => count($this->dataMap),
            'mappings' => $this->dataMap,
        ];
    }
}
