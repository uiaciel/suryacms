<?php

namespace Uiaciel\SuryaCms\Services;

use DOMDocument;
use DOMXPath;

class HtmlToGrapeJsConverter
{
    /**
     * Convert raw HTML to GrapeJS compatible format
     * GrapeJS uses a specific JSON structure with components and styles
     *
     * @param string $html Raw HTML content
     * @return string Converted HTML safe for GrapeJS
     */
    public function convert(string $html): string
    {
        try {
            $dom = new DOMDocument('1.0', 'UTF-8');

            // Suppress warnings for malformed HTML
            libxml_use_internal_errors(true);

            // Wrap HTML in body tags if needed
            $wrappedHtml = '<?xml encoding="UTF-8">' . $html;
            $dom->loadHTML($wrappedHtml);

            libxml_clear_errors();

            // Process the DOM to make it GrapeJS compatible
            $this->normalizeElements($dom);
            $this->addDataGrapeAttributes($dom);

            $output = $dom->saveHTML();

            // Clean up the output - remove XML declaration and doctype
            $output = preg_replace('/<\?xml.*?\?>/', '', $output);
            $output = preg_replace('/<!DOCTYPE.*?>/i', '', $output);
            $output = str_replace('<html><body>', '', $output);
            $output = str_replace('</body></html>', '', $output);

            return trim($output);
        } catch (\Exception $e) {
            // If DOM parsing fails, return sanitized HTML
            return $this->sanitizeHtml($html);
        }
    }

    /**
     * Add GrapeJS data attributes to make elements editable/draggable
     *
     * @param DOMDocument $dom
     * @return void
     */
    private function addDataGrapeAttributes(DOMDocument $dom): void
    {
        $xpath = new DOMXPath($dom);

        // Find all major container elements
        $containers = $xpath->query('//*[self::div or self::section or self::article or self::main or self::aside]');

        foreach ($containers as $container) {
            if (!$container->hasAttribute('data-gjs-type')) {
                $container->setAttribute('data-gjs-type', 'container');
                $container->setAttribute('data-gjs-draggable', 'true');
            }
        }

        // Add attributes to text elements
        $textElements = $xpath->query('//*[self::p or self::h1 or self::h2 or self::h3 or self::h4 or self::h5 or self::h6 or self::span]');

        foreach ($textElements as $elem) {
            if (!$elem->hasAttribute('data-gjs-type')) {
                $elem->setAttribute('data-gjs-type', 'text');
                $elem->setAttribute('data-gjs-editable', 'true');
            }
        }

        // Add attributes to images
        $images = $xpath->query('//img');

        foreach ($images as $img) {
            if (!$img->hasAttribute('data-gjs-type')) {
                $img->setAttribute('data-gjs-type', 'image');
                $img->setAttribute('data-gjs-draggable', 'true');
            }
        }

        // Add attributes to buttons
        $buttons = $xpath->query('//button | //a[contains(@class, "btn")]');

        foreach ($buttons as $btn) {
            if (!$btn->hasAttribute('data-gjs-type')) {
                $btn->setAttribute('data-gjs-type', 'button');
                $btn->setAttribute('data-gjs-editable', 'true');
            }
        }
    }

    /**
     * Normalize HTML elements for GrapeJS compatibility
     *
     * @param DOMDocument $dom
     * @return void
     */
    private function normalizeElements(DOMDocument $dom): void
    {
        $xpath = new DOMXPath($dom);

        // Convert inline styles to classes where possible
        $elementsWithStyle = $xpath->query('//*[@style]');

        foreach ($elementsWithStyle as $elem) {
            $style = $elem->getAttribute('style');
            if (!empty($style)) {
                // Keep inline styles but add them as data attribute for GrapeJS
                $elem->setAttribute('data-gjs-inline-style', $style);
            }
        }

        // Ensure all divs have proper structure
        $divs = $xpath->query('//div');

        foreach ($divs as $div) {
            if (!$div->hasAttribute('class')) {
                $div->setAttribute('class', 'gjs-container');
            }
        }
    }

    /**
     * Sanitize HTML if DOM parsing fails
     * Removes potentially harmful tags while keeping structure
     *
     * @param string $html
     * @return string
     */
    private function sanitizeHtml(string $html): string
    {
        // Remove script and style tags
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);

        // Remove event handlers
        $html = preg_replace('/\s*on\w+\s*=\s*["\']?[^"\']*["\']?/i', '', $html);

        // Add GrapeJS attributes to major elements
        $html = preg_replace('/(<div[^>]*>)/i', '$1', $html);

        return $html;
    }

    /**
     * Extract CSS from style tags and return them separately
     *
     * @param string $html
     * @return array ['html' => string, 'css' => string]
     */
    public function extractCss(string $html): array
    {
        $css = '';

        // Extract CSS from style tags
        if (preg_match_all('/<style[^>]*>(.*?)<\/style>/is', $html, $matches)) {
            $css = implode('\n', $matches[1]);
        }

        // Remove style tags from HTML
        $html = preg_replace('/<style[^>]*>(.*?)<\/style>/is', '', $html);

        return [
            'html' => $this->convert($html),
            'css' => $css
        ];
    }
}
