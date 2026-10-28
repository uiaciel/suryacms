<?php

namespace Uiaciel\SuryaCms\Services;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Str;

class ThemeNormalizerService
{
    private $dom;

    private $xpath;

    private $htmlContent;

    private $themeName;

    private $usedIds = []; // Track used IDs to avoid duplicates

    public function __construct($htmlContent, $themeName = null)
    {
        $this->htmlContent = $htmlContent;
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

        // Collect existing IDs
        $this->collectExistingIds();
    }

    private function collectExistingIds()
    {
        $elements = $this->xpath->query('//*[@id]');
        if ($elements instanceof \DOMNodeList) {
            foreach ($elements as $element) {
                $this->usedIds[] = $element->getAttribute('id');
            }
        }
    }

    public function normalize()
    {
        try {
            $this->normalizeNavigation();
            $this->normalizeFooter();
            $this->normalizeSections();
            if ($this->themeName) {
                $this->normalizeAssets(); // <-- Diperluas cakupannya
            }
        } catch (\Exception $e) {
            return $this->htmlContent;
        }

        return $this->getHTML();
    }

    private function normalizeNavigation()
    {
        // Find all <nav> elements without id
        $navElements = $this->xpath->query('//nav[not(@id)]');

        if ($navElements instanceof \DOMNodeList) {
            foreach ($navElements as $index => $nav) {
                $id = $index === 0 ? 'navigation' : 'navigation-'.($index + 1);
                $nav->setAttribute('id', $id);
                $this->usedIds[] = $id;
            }
        }

        // Fallback: if no <nav> found at all
        $navQuery = $this->xpath->query('//nav');
        if ($navQuery instanceof \DOMNodeList && $navQuery->length === 0) {
            $this->findAndMarkNavigation();
        }
    }

    private function findAndMarkNavigation()
    {
        $patterns = ['navbar', 'nav', 'menu', 'header-menu', 'main-menu'];

        foreach ($patterns as $pattern) {
            $elements = $this->xpath->query("//div[contains(@class, '{$pattern}')][not(@id)]");

            if ($elements instanceof \DOMNodeList && $elements->length > 0) {
                $elements[0]->setAttribute('id', 'navigation');
                $this->usedIds[] = 'navigation';
                break;
            }
        }
    }

    private function normalizeFooter()
    {
        // Find all <footer> elements without id
        $footerElements = $this->xpath->query('//footer[not(@id)]');

        if ($footerElements instanceof \DOMNodeList) {
            foreach ($footerElements as $index => $footer) {
                $id = $index === 0 ? 'footer' : 'footer-'.($index + 1);
                $footer->setAttribute('id', $id);
                $this->usedIds[] = $id;
            }
        }

        // Fallback: if no <footer> found at all
        $footerQuery = $this->xpath->query('//footer');
        if ($footerQuery instanceof \DOMNodeList && $footerQuery->length === 0) {
            $this->findAndMarkFooter();
        }
    }

    private function findAndMarkFooter()
    {
        $patterns = ['footer', 'site-footer', 'page-footer', 'bottom'];

        foreach ($patterns as $pattern) {
            $elements = $this->xpath->query("//div[contains(@class, '{$pattern}')][not(@id)]");

            if ($elements instanceof \DOMNodeList && $elements->length > 0) {
                $elements[0]->setAttribute('id', 'footer');
                $this->usedIds[] = 'footer';
                break;
            }
        }
    }

    private function normalizeSections()
    {
        // Step 1: Convert direct child divs of body to sections
        $this->convertBodyDivsToSections();

        // Step 2: Add IDs to sections without IDs
        $this->addIdsToSections();
    }

    private function convertBodyDivsToSections()
    {
        // Only get direct child divs from body (not navigation or footer)
        $bodyDivs = $this->xpath->query('//body/div[not(@id="navigation") and not(@id="footer") and not(contains(@class, "navigation")) and not(contains(@class, "footer"))]');

        if ($bodyDivs instanceof \DOMNodeList) {
            foreach ($bodyDivs as $div) {
                // Skip if div already has id that's in usedIds (might be navigation/footer)
                if ($div->hasAttribute('id') && in_array($div->getAttribute('id'), ['navigation', 'footer'])) {
                    continue;
                }

                // Create new section element
                $section = $this->dom->createElement('section');

                // Copy all attributes from div to section
                if ($div->hasAttributes()) {
                    foreach ($div->attributes as $attr) {
                        $section->setAttribute($attr->name, $attr->value);
                    }
                }

                // Move all child nodes from div to section
                while ($div->firstChild) {
                    $section->appendChild($div->firstChild);
                }

                // Replace div with section
                $div->parentNode->replaceChild($section, $div);
            }
        }
    }

    private function addIdsToSections()
    {
        // Find all sections without id
        $sections = $this->xpath->query('//section[not(@id)]');
        $idCounter = [];

        if ($sections instanceof \DOMNodeList) {
            foreach ($sections as $section) {
                $baseId = $this->generateIdFromContent($section);

                // Handle duplicate IDs
                if (! isset($idCounter[$baseId])) {
                    $idCounter[$baseId] = 0;
                }

                $idCounter[$baseId]++;

                // Generate unique ID
                if ($idCounter[$baseId] === 1) {
                    $finalId = $baseId;
                } else {
                    $finalId = $baseId.'-'.$idCounter[$baseId];
                }

                // Ensure truly unique (check against existing IDs)
                $finalId = $this->ensureUniqueId($finalId);

                $section->setAttribute('id', $finalId);
                $this->usedIds[] = $finalId;
            }
        }
    }

    private function generateIdFromContent($element)
    {
        // Try to find heading (h1-h6) inside element
        $headings = $this->xpath->query('.//h1|.//h2|.//h3|.//h4|.//h5|.//h6', $element);

        if ($headings instanceof \DOMNodeList && $headings->length > 0) {
            $headingText = trim($headings[0]->textContent);
            $id = Str::slug($headingText);

            // Limit ID length and ensure it's not empty
            if (strlen($id) > 30) {
                $id = substr($id, 0, 30);
            }

            // Remove trailing hyphens
            $id = rtrim($id, '-');

            return $id ?: 'section';
        }

        // Fallback: check for common class patterns
        if ($element->hasAttribute('class')) {
            $classes = explode(' ', $element->getAttribute('class'));
            foreach ($classes as $class) {
                $cleanClass = Str::slug($class);
                if ($cleanClass && ! in_array($cleanClass, ['container', 'row', 'col', 'section'])) {
                    return $cleanClass;
                }
            }
        }

        return 'section';
    }

    private function ensureUniqueId($id)
    {
        $originalId = $id;
        $counter = 1;

        while (in_array($id, $this->usedIds)) {
            $counter++;
            $id = $originalId.'-'.$counter;
        }

        return $id;
    }

    private function getHTML()
    {
        $html = $this->dom->saveHTML();

        // Fix 1: Kembalikan HTML Entities bawaan Blade ke karakter aslinya
        $html = preg_replace_callback('/\{\{(.*?)\}\}/s', function ($matches) {
            return '{{' . htmlspecialchars_decode($matches[1], ENT_QUOTES) . '}}';
        }, $html);

        // Fix 2: Perbaiki double curly braces yang rusak
        $html = preg_replace('/\}\}\}\}/', '}}', $html);

        // Fix 3: Decode URL encoded characters (%20) HANYA di path tema kita
        if ($this->themeName) {
            $escapedTheme = preg_quote($this->themeName, '/');
            $html = preg_replace_callback('/(\/frontend\/' . $escapedTheme . '\/[^"\'\s>]+)/', function ($matches) {
                return urldecode($matches[1]);
            }, $html);
        }

        return $html;
    }

    public function getStatistics()
    {
        return [
            'total_sections' => $this->xpath->query('//section')->length,
            'navigation_id' => $this->xpath->query('//*[@id="navigation"]')->length > 0 ? 'navigation' : null,
            'footer_id' => $this->xpath->query('//*[@id="footer"]')->length > 0 ? 'footer' : null,
            'sections_list' => $this->getSectionsList(),
        ];
    }

    private function getSectionsList()
    {
        $sections = $this->xpath->query('//section[@id]');
        $list = [];

        if (! $sections instanceof \DOMNodeList) {
            return $list;
        }

        foreach ($sections as $section) {
            $id = $section->getAttribute('id');
            $heading = $this->xpath->query('.//h1|.//h2|.//h3', $section);

            if ($heading instanceof \DOMNodeList) {
                $title = $heading->length > 0 ? trim($heading[0]->textContent) : 'No title';
            } else {
                $title = 'No title';
            }

            $list[] = [
                'id' => $id,
                'title' => $title,
            ];
        }

        return $list;
    }

    private function normalizeAssets()
    {
        if (!$this->themeName) return;

        // 1. Normalize <img> src
        $this->normalizeAttribute('//img[@src]', 'src');

        // 2. Normalize <source> srcset
        $this->normalizeAttribute('//source[@srcset]', 'srcset');

        // 3. Normalize <video> dan <audio> src
        $this->normalizeAttribute('//video[@src]', 'src');
        $this->normalizeAttribute('//audio[@src]', 'src');

        // 4. Normalize <a> href (hanya jika link internal relatif, bukan # atau http)
        $this->normalizeAttribute('//a[@href]', 'href', true);

        // 5. Normalize Inline CSS background-image: url('...')
        $this->normalizeInlineStyles();
    }

    private function normalizeAttribute($xpathQuery, $attributeName, $isLink = false)
    {
        $elements = $this->xpath->query($xpathQuery);
        if ($elements instanceof \DOMNodeList) {
            foreach ($elements as $element) {
                $value = $element->getAttribute($attributeName);
                $newValue = $this->normalizeAssetPath($value, $isLink);

                if ($newValue !== $value) {
                    $element->setAttribute($attributeName, $newValue);
                }
            }
        }
    }

    private function normalizeInlineStyles()
    {
        // Cari semua elemen yang memiliki atribut style
        $elements = $this->xpath->query('//*[@style]');
        if ($elements instanceof \DOMNodeList) {
            foreach ($elements as $element) {
                $style = $element->getAttribute('style');

                // Regex untuk menangkap url('...') atau url("...") di dalam CSS
                $newStyle = preg_replace_callback(
                    '/url\(\s*[\'"]?([^\'"\)]+)[\'"]?\s*\)/i',
                    function ($matches) {
                        $path = trim($matches[1]);
                        $normalized = $this->normalizeAssetPath($path, false);
                        return "url('{$normalized}')";
                    },
                    $style
                );

                if ($newStyle !== $style) {
                    $element->setAttribute('style', $newStyle);
                }
            }
        }
    }

    private function normalizeAssetPath($path, $isLink = false)
    {
        $path = trim($path);

        // 1. Skip jika kosong, external, root-relative, data URI, js, anchor, atau sudah Blade
        if (
            empty($path) ||
            preg_match('/^(https?:)?\/\//i', $path) ||
            strpos($path, '/') === 0 || // Root relative (biarkan Laravel handle atau asumsikan sudah benar)
            strpos($path, 'data:') === 0 ||
            strpos($path, 'javascript:') === 0 ||
            strpos($path, '#') === 0 ||
            strpos($path, '{{') !== false ||
            strpos($path, '[[plugin]]') !== false // Skip shortcode page builder
        ) {
            return $path;
        }

        // 2. Bersihkan leading ./ atau ../
        // Catatan: Jika template menggunakan ../images/ dari folder css/, kita harus hati-hati.
        // Asumsi: ZIP sudah diekstrak ke root, sehingga ../ seharusnya tidak ada, atau kita normalisasi ke root tema.
        $path = ltrim($path, './');
        $path = preg_replace('/^\.\.\//', '', $path);

        // 3. Tambahkan prefix tema
        return "/frontend/{$this->themeName}/{$path}";
    }
}
