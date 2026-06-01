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
            $mainContent = $this->extractMainContent($xpath);
            $footerHtml = $this->extractFooter($xpath);
            $scriptsHtml = $this->extractScripts($xpath);

            // Build Blade template
            $bladeTemplate = $this->buildBladeTemplate(
                $headContent,
                $mainContent,
                $footerHtml,
                $scriptsHtml
            );

            return $this->cleanBladeSyntax($bladeTemplate);
        } catch (\Exception $e) {
            throw new \Exception('Error generating app.blade.php: '.$e->getMessage());
        }
    }

    /**
     * Generate navigation.blade.php dari normalized HTML
     *
     * @return string Blade template content
     */
    public function generateNavigation()
    {
        try {
            $dom = new DOMDocument;
            @$dom->loadHTML('<?xml encoding="UTF-8">'.$this->htmlContent);
            $xpath = new DOMXPath($dom);

            return $this->cleanBladeSyntax($this->extractNavigation($xpath));
        } catch (\Exception $e) {
            throw new \Exception('Error generating navigation.blade.php: '.$e->getMessage());
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
        $dom = new DOMDocument;
        // Load the navigation fragment safely with UTF-8 encoding
        @$dom->loadHTML('<?xml encoding="UTF-8"><div>' . $navHtml . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new DOMXPath($dom);

        $container = null;

        // 1. Try to find <ul> elements
        $ulNodes = $xpath->query('//ul');
        if ($ulNodes->length > 0) {
            foreach ($ulNodes as $node) {
                $class = $node->getAttribute('class');
                if (preg_match('/(nav|menu|navbar)/i', $class)) {
                    $container = $node;
                    break;
                }
            }
            if (!$container) {
                $container = $ulNodes->item(0);
            }
        }

        // 2. Try to find <div> with class containing navbar-nav or menu
        if (!$container) {
            $divNodes = $xpath->query('//div');
            foreach ($divNodes as $node) {
                $class = $node->getAttribute('class');
                if (preg_match('/(navbar-nav|menu-container|main-menu)/i', $class)) {
                    $anchors = $xpath->query('.//a', $node);
                    if ($anchors->length > 0) {
                        $container = $node;
                        break;
                    }
                }
            }
        }

        // 3. Fallback: If no specific container, use the first child div of our loaded fragment
        if (!$container) {
            $container = $xpath->query('//div')->item(0);
        }

        if (!$container) {
            return $navHtml;
        }

        $regularItem = null;
        $dropdownItem = null;
        $isLiBased = false;

        // Identify if it's <li> based
        $liNodes = $xpath->query('.//li', $container);
        if ($liNodes->length > 0) {
            $isLiBased = true;
            foreach ($liNodes as $li) {
                if ($li->parentNode !== $container) {
                    continue;
                }

                $hasDropdownClass = false;
                $class = $li->getAttribute('class');
                if (preg_match('/(dropdown|has-children|menu-item-has-children)/i', $class)) {
                    $hasDropdownClass = true;
                }

                $nestedUl = $xpath->query('.//ul', $li);
                $hasNestedUl = $nestedUl->length > 0;

                if ($hasDropdownClass || $hasNestedUl) {
                    if (!$dropdownItem) {
                        $dropdownItem = $li;
                    }
                } else {
                    if (!$regularItem) {
                        $anchors = $xpath->query('.//a', $li);
                        if ($anchors->length > 0) {
                            $regularItem = $li;
                        }
                    }
                }
            }
        } else {
            // Div/A-based navigation
            $anchorNodes = $xpath->query('./a', $container);
            if ($anchorNodes->length > 0) {
                foreach ($anchorNodes as $a) {
                    if (!$regularItem) {
                        $regularItem = $a;
                    }
                }
            }

            // Check for dropdown divs
            $dropdownDivs = $xpath->query('./div', $container);
            foreach ($dropdownDivs as $div) {
                $class = $div->getAttribute('class');
                if (preg_match('/(dropdown|nav-item)/i', $class)) {
                    if (!$dropdownItem) {
                        $dropdownItem = $div;
                    }
                }
            }
        }

        // If we didn't find a regular item, search recursively for any anchor tag
        if (!$regularItem) {
            $anyAnchor = $xpath->query('.//a', $container)->item(0);
            if ($anyAnchor) {
                $parent = $anyAnchor->parentNode;
                while ($parent && $parent !== $container) {
                    if (strtolower($parent->nodeName) === 'li') {
                        $regularItem = $parent;
                        $isLiBased = true;
                        break;
                    }
                    $parent = $parent->parentNode;
                }
                if (!$regularItem) {
                    $regularItem = $anyAnchor;
                }
            }
        }

        // Synthesize dropdown item if not found but we have a regular item
        if (!$dropdownItem && $regularItem) {
            $dropdownItem = $this->synthesizeDropdownItem($dom, $regularItem, $isLiBased);
        }

        if (!$regularItem) {
            return $navHtml;
        }

        // Clear all existing children of $container
        while ($container->firstChild) {
            $container->removeChild($container->firstChild);
        }

        // Build the dynamic structures as DOM nodes
        $this->buildDynamicMenuNodes($dom, $container, $regularItem, $dropdownItem, $isLiBased);

        $html = $dom->saveHTML();

        // Strip the wrapper <?xml> and <div> tags
        $html = preg_replace('/<\?xml[^>]*\?>/', '', $html);
        $html = preg_replace('/^.*?<div>/s', '', $html);
        $html = preg_replace('/<\/div>\s*$/s', '', $html);

        // FIX: Decode syntax blade Indonesia/English Loop dan Lang Switch yang rusak karena saveHTML
        $html = preg_replace_callback('/\{\{(.*?)\}\}/s', function ($matches) {
            return '{{' . htmlspecialchars_decode($matches[1], ENT_QUOTES) . '}}';
        }, $html);

        $html = preg_replace('/\}\}\}\}/', '}}', $html);

        // FIX: Bersihkan %20 pada link asset gambar bendera (id.png / us.png) di menu multilanguage
        if ($this->themeName) {
            $escapedTheme = preg_quote($this->themeName, '/');
            $html = preg_replace_callback('/(\/frontend\/' . $escapedTheme . '\/[^"\'\s>]+)/', function ($matches) {
                return urldecode($matches[1]);
            }, $html);
        }

        return trim($html);
    }

    private function synthesizeDropdownItem($dom, $regularItem, $isLiBased)
    {
        $dropdownItem = $regularItem->cloneNode(true);
        if ($isLiBased) {
            $class = $dropdownItem->getAttribute('class');
            $dropdownItem->setAttribute('class', trim($class . ' dropdown'));

            $anchor = $dropdownItem->getElementsByTagName('a')->item(0);
            if ($anchor) {
                $aClass = $anchor->getAttribute('class');
                $anchor->setAttribute('class', trim($aClass . ' dropdown-toggle'));
                $anchor->setAttribute('data-bs-toggle', 'dropdown');
                $anchor->setAttribute('href', '#');
            }

            $subUl = $dom->createElement('ul');
            $subUl->setAttribute('class', 'dropdown-menu');

            $subLi = $regularItem->cloneNode(true);
            $subAnchor = $subLi->getElementsByTagName('a')->item(0);
            if ($subAnchor) {
                $subAnchor->setAttribute('class', 'dropdown-item');
            }
            $subUl->appendChild($subLi);
            $dropdownItem->appendChild($subUl);
        } else {
            $dropdownItem = $dom->createElement('div');
            $dropdownItem->setAttribute('class', 'nav-item dropdown');

            $toggle = $regularItem->cloneNode(true);
            $tClass = $toggle->getAttribute('class');
            $toggle->setAttribute('class', trim($tClass . ' dropdown-toggle'));
            $toggle->setAttribute('data-bs-toggle', 'dropdown');
            $toggle->setAttribute('href', '#');

            $menu = $dom->createElement('div');
            $menu->setAttribute('class', 'dropdown-menu');

            $subItem = $regularItem->cloneNode(true);
            $subItem->setAttribute('class', 'dropdown-item');
            $menu->appendChild($subItem);

            $dropdownItem->appendChild($toggle);
            $dropdownItem->appendChild($menu);
        }

        return $dropdownItem;
    }

    private function buildDynamicMenuNodes($dom, $container, $regularItem, $dropdownItem, $isLiBased)
    {
        // Indonesian Loop
        $container->appendChild($dom->createTextNode("\n        @if (session('locale', 'id') === 'id')\n"));
        $container->appendChild($dom->createTextNode("        @foreach (\$menus->where('category', 'Indonesia') as \$menu)\n"));
        $container->appendChild($dom->createTextNode("        @if (\$menu->children->count())\n"));

        if ($dropdownItem) {
            $idDropdown = $this->prepareDropdownNode($dom, $dropdownItem, '$menu', '$submenu', $isLiBased);
            $container->appendChild($idDropdown);
            $container->appendChild($dom->createTextNode("\n"));
        }

        $container->appendChild($dom->createTextNode("        @else\n"));

        $idRegular = $this->prepareRegularNode($dom, $regularItem, '$menu');
        $container->appendChild($idRegular);
        $container->appendChild($dom->createTextNode("\n"));

        $container->appendChild($dom->createTextNode("        @endif\n"));
        $container->appendChild($dom->createTextNode("        @endforeach\n"));

        // English Loop
        $container->appendChild($dom->createTextNode("        @elseif (session('locale', 'id') === 'en')\n"));
        $container->appendChild($dom->createTextNode("        @foreach (\$menus->where('category', 'English') as \$menu)\n"));
        $container->appendChild($dom->createTextNode("        @if (\$menu->children->count())\n"));

        if ($dropdownItem) {
            $enDropdown = $this->prepareDropdownNode($dom, $dropdownItem, '$menu', '$submenu', $isLiBased);
            $container->appendChild($enDropdown);
            $container->appendChild($dom->createTextNode("\n"));
        }

        $container->appendChild($dom->createTextNode("        @else\n"));

        $enRegular = $this->prepareRegularNode($dom, $regularItem, '$menu');
        $container->appendChild($enRegular);
        $container->appendChild($dom->createTextNode("\n"));

        $container->appendChild($dom->createTextNode("        @endif\n"));
        $container->appendChild($dom->createTextNode("        @endforeach\n"));
        $container->appendChild($dom->createTextNode("        @endif\n"));

        // Multilingual switch
        $container->appendChild($dom->createTextNode("        @if(\$setting->is_multilingual === 'Yes')\n"));

        if ($dropdownItem) {
            $langDropdown = $this->prepareLangDropdownNode($dom, $dropdownItem, $isLiBased);
            $container->appendChild($langDropdown);
            $container->appendChild($dom->createTextNode("\n"));
        }

        $container->appendChild($dom->createTextNode("        @endif\n"));
    }

    private function prepareRegularNode($dom, $regularItem, $varName)
    {
        $node = $regularItem->cloneNode(true);
        $this->stripActiveAttributes($node);

        $anchor = null;
        if (strtolower($node->nodeName) === 'a') {
            $anchor = $node;
        } else {
            $anchor = $node->getElementsByTagName('a')->item(0);
        }

        if ($anchor) {
            $anchor->setAttribute('href', '{{ ' . $varName . '->link ?? \'/\' }}');
            $this->replaceAnchorText($anchor, '{{ ' . $varName . '->name }}');
        }

        return $node;
    }

    private function prepareDropdownNode($dom, $dropdownItem, $parentVar, $childVar, $isLiBased)
    {
        $node = $dropdownItem->cloneNode(true);
        $this->stripActiveAttributes($node);

        $toggleAnchor = $node->getElementsByTagName('a')->item(0);
        if ($toggleAnchor) {
            $toggleAnchor->setAttribute('href', '{{ ' . $parentVar . '->link ?? \'#\' }}');
            $this->replaceAnchorText($toggleAnchor, '{{ ' . $parentVar . '->name }}');
        }

        $submenuContainer = null;
        $subnodes = $node->getElementsByTagName('*');
        foreach ($subnodes as $sub) {
            $class = $sub->getAttribute('class');
            if (preg_match('/(dropdown-menu|submenu|sub-menu|dropdown-content)/i', $class) || strtolower($sub->nodeName) === 'ul') {
                if ($sub !== $node) {
                    $submenuContainer = $sub;
                    break;
                }
            }
        }

        if (!$submenuContainer) {
            foreach (array_reverse(iterator_to_array($node->childNodes)) as $child) {
                if ($child->nodeType === 1 && in_array(strtolower($child->nodeName), ['ul', 'div'])) {
                    $submenuContainer = $child;
                    break;
                }
            }
        }

        if ($submenuContainer) {
            $subItemTemplate = null;
            $anchors = $submenuContainer->getElementsByTagName('a');
            if ($anchors->length > 0) {
                $firstAnchor = $anchors->item(0);
                $parent = $firstAnchor->parentNode;
                if ($parent && $parent !== $submenuContainer && strtolower($parent->nodeName) === 'li') {
                    $subItemTemplate = $parent;
                } else {
                    $subItemTemplate = $firstAnchor;
                }
            }

            if (!$subItemTemplate && $submenuContainer->firstElementChild) {
                $subItemTemplate = $submenuContainer->firstElementChild;
            }

            if ($subItemTemplate) {
                $clonedSubItem = $subItemTemplate->cloneNode(true);
                $this->stripActiveAttributes($clonedSubItem);

                while ($submenuContainer->firstChild) {
                    $submenuContainer->removeChild($submenuContainer->firstChild);
                }

                $subAnchor = null;
                if (strtolower($clonedSubItem->nodeName) === 'a') {
                    $subAnchor = $clonedSubItem;
                } else {
                    $subAnchor = $clonedSubItem->getElementsByTagName('a')->item(0);
                }

                if ($subAnchor) {
                    $subAnchor->setAttribute('href', '{{ ' . $childVar . '->link ?? \'#\' }}');
                    $this->replaceAnchorText($subAnchor, '{{ ' . $childVar . '->name }}');
                }

                $submenuContainer->appendChild($dom->createTextNode("\n                @foreach (" . $parentVar . "->children as " . $childVar . ")\n                "));
                $submenuContainer->appendChild($clonedSubItem);
                $submenuContainer->appendChild($dom->createTextNode("\n                @endforeach\n            "));
            }
        }

        return $node;
    }

    private function prepareLangDropdownNode($dom, $dropdownItem, $isLiBased)
    {
        $node = $dropdownItem->cloneNode(true);
        $this->stripActiveAttributes($node);

        $toggleAnchor = $node->getElementsByTagName('a')->item(0);
        if ($toggleAnchor) {
            $toggleAnchor->setAttribute('href', '#');
            $toggleAnchor->setAttribute('data-bs-toggle', 'dropdown');

            while ($toggleAnchor->firstChild) {
                $toggleAnchor->removeChild($toggleAnchor->firstChild);
            }

            $toggleAnchor->appendChild($dom->createTextNode("@if (session('locale', 'id') === 'id')\n"));
            $idImg = $dom->createElement('img');
            $idImg->setAttribute('src', '/frontend/default/img/id.png');
            $idImg->setAttribute('alt', 'Indonesia');
            $idImg->setAttribute('width', '20');
            $idImg->setAttribute('class', 'align-middle me-1');
            $toggleAnchor->appendChild($idImg);
            $toggleAnchor->appendChild($dom->createTextNode("\n@else\n"));
            $usImg = $dom->createElement('img');
            $usImg->setAttribute('src', '/frontend/default/img/us.png');
            $usImg->setAttribute('alt', 'English');
            $usImg->setAttribute('width', '20');
            $usImg->setAttribute('class', 'align-middle me-1');
            $toggleAnchor->appendChild($usImg);
            $toggleAnchor->appendChild($dom->createTextNode("\n@endif\n"));
        }

        $submenuContainer = null;
        $subnodes = $node->getElementsByTagName('*');
        foreach ($subnodes as $sub) {
            $class = $sub->getAttribute('class');
            if (preg_match('/(dropdown-menu|submenu|sub-menu|dropdown-content)/i', $class) || strtolower($sub->nodeName) === 'ul') {
                if ($sub !== $node) {
                    $submenuContainer = $sub;
                    break;
                }
            }
        }

        if (!$submenuContainer) {
            foreach (array_reverse(iterator_to_array($node->childNodes)) as $child) {
                if ($child->nodeType === 1 && in_array(strtolower($child->nodeName), ['ul', 'div'])) {
                    $submenuContainer = $child;
                    break;
                }
            }
        }

        if ($submenuContainer) {
            $subItemTemplate = null;
            $anchors = $submenuContainer->getElementsByTagName('a');
            if ($anchors->length > 0) {
                $firstAnchor = $anchors->item(0);
                $parent = $firstAnchor->parentNode;
                if ($parent && $parent !== $submenuContainer && strtolower($parent->nodeName) === 'li') {
                    $subItemTemplate = $parent;
                } else {
                    $subItemTemplate = $firstAnchor;
                }
            }

            if (!$subItemTemplate && $submenuContainer->firstElementChild) {
                $subItemTemplate = $submenuContainer->firstElementChild;
            }

            if ($subItemTemplate) {
                while ($submenuContainer->firstChild) {
                    $submenuContainer->removeChild($submenuContainer->firstChild);
                }

                $idItem = $subItemTemplate->cloneNode(true);
                $this->stripActiveAttributes($idItem);
                $idAnchor = (strtolower($idItem->nodeName) === 'a') ? $idItem : $idItem->getElementsByTagName('a')->item(0);
                if ($idAnchor) {
                    $idAnchor->setAttribute('href', '{{ url(\'/id/\') }}');
                    while ($idAnchor->firstChild) {
                        $idAnchor->removeChild($idAnchor->firstChild);
                    }
                    $idIcon = $dom->createElement('img');
                    $idIcon->setAttribute('src', '/frontend/default/img/id.png');
                    $idIcon->setAttribute('alt', 'Indonesia');
                    $idIcon->setAttribute('width', '20');
                    $idIcon->setAttribute('class', 'align-middle me-1');
                    $idAnchor->appendChild($idIcon);
                    $idAnchor->appendChild($dom->createTextNode(' Indonesia'));
                }

                $enItem = $subItemTemplate->cloneNode(true);
                $this->stripActiveAttributes($enItem);
                $enAnchor = (strtolower($enItem->nodeName) === 'a') ? $enItem : $enItem->getElementsByTagName('a')->item(0);
                if ($enAnchor) {
                    $enAnchor->setAttribute('href', '{{ url(\'/en/\') }}');
                    while ($enAnchor->firstChild) {
                        $enAnchor->removeChild($enAnchor->firstChild);
                    }
                    $enIcon = $dom->createElement('img');
                    $enIcon->setAttribute('src', '/frontend/default/img/us.png');
                    $enIcon->setAttribute('alt', 'English');
                    $enIcon->setAttribute('width', '20');
                    $enIcon->setAttribute('class', 'align-middle me-1');
                    $enAnchor->appendChild($enIcon);
                    $enAnchor->appendChild($dom->createTextNode(' English'));
                }

                $submenuContainer->appendChild($idItem);
                $submenuContainer->appendChild($dom->createTextNode("\n"));
                $submenuContainer->appendChild($enItem);
            }
        }

        return $node;
    }

    private function replaceAnchorText($anchorNode, $replacementText)
    {
        $xpath = new DOMXPath($anchorNode->ownerDocument);
        $textNodes = $xpath->query('.//text()', $anchorNode);

        $replaced = false;
        if ($textNodes instanceof \DOMNodeList) {
            foreach ($textNodes as $textNode) {
                $val = $textNode->nodeValue;
                if (trim($val) !== '') {
                    preg_match('/^(\s*).*?(\s*)$/u', $val, $spaces);
                    $leading = $spaces[1] ?? '';
                    $trailing = $spaces[2] ?? '';
                    $textNode->nodeValue = $leading . $replacementText . $trailing;
                    $replaced = true;
                    break;
                }
            }
        }

        if (!$replaced) {
            $anchorNode->appendChild($anchorNode->ownerDocument->createTextNode($replacementText));
        }
    }

    private function stripActiveAttributes($node)
    {
        if ($node->nodeType !== 1) {
            return;
        }

        $class = $node->getAttribute('class');
        if (!empty($class)) {
            $classes = explode(' ', $class);
            $classes = array_filter($classes, function($c) {
                return strtolower($c) !== 'active';
            });
            $node->setAttribute('class', implode(' ', $classes));
        }

        foreach ($node->childNodes as $child) {
            $this->stripActiveAttributes($child);
        }
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
    private function buildBladeTemplate($head, $main, $footer, $scripts)
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
    @include('frontend::navigation')

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

        // FIX: Kembalikan HTML Entities di dalam sintaks Blade/Kurung Kurawal ke aslinya
        $html = preg_replace_callback('/\{\{(.*?)\}\}/s', function ($matches) {
            return '{{' . htmlspecialchars_decode($matches[1], ENT_QUOTES) . '}}';
        }, $html);

        $html = preg_replace_callback('/\{!!(.*?)!!\}/s', function ($matches) {
            return '{!!' . htmlspecialchars_decode($matches[1], ENT_QUOTES) . '!!}';
        }, $html);

        // FIX: Bersihkan double curly braces yang rusak akibat parsing DOM (jika ada)
        $html = preg_replace('/\}\}\}\}/', '}}', $html);

        // FIX: Kembalikan %20 menjadi spasi untuk asset/url internal tema
        if ($this->themeName) {
            $escapedTheme = preg_quote($this->themeName, '/');
            $html = preg_replace_callback('/(\/frontend\/' . $escapedTheme . '\/[^"\'\s>]+)/', function ($matches) {
                return urldecode($matches[1]);
            }, $html);
        }

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

            return $this->cleanBladeSyntax($editorTemplate);
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

        return $this->cleanBladeSyntax($template);
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

            return $this->cleanBladeSyntax($template);
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
                    "style" => "tailwindcss", // Default ke Bootstrap, bisa diubah ke tailwindcss
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
                        'preview' => '',
                    ],
                    [
                        'id' => 'blogs-block',
                        'label' => 'Blogs',
                        'category' => 'Plugin',
                        'icon' => '📝',
                        'content' => '[[blog]]',
                        'preview' => '',
                    ],
                    [
                        'id' => 'youtube-block',
                        'label' => 'Youtube',
                        'category' => 'Plugin',
                        'icon' => '📺',
                        'content' => '[[youtube]]',
                        'preview' => '',
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

    /**
     * Clean HTML entity encodings and URL-escaped spaces in Blade files
     */
    private function cleanBladeSyntax($html)
    {
        // Decode HTML entities globally (restores -> and other characters)
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Ensure standard -> operator is restored
        $html = str_replace('-&gt;', '->', $html);

        // Decode URL-encoded characters (like %20, %2D%3E) inside Blade brackets
        $html = preg_replace_callback('/\{\{(.*?)\}\}/s', function($matches) {
            return '{{' . urldecode($matches[1]) . '}}';
        }, $html);

        $html = preg_replace_callback('/\{!!(.*?)!!\}/s', function($matches) {
            return '{!!' . urldecode($matches[1]) . '!!}';
        }, $html);

        return $html;
    }

    /**
     * Extract hero/banner section to use as general page header
     */
    private function extractHeroSection($xpath)
    {
        $patterns = [
            '//section[contains(@id, "hero") or contains(@id, "banner") or contains(@class, "hero") or contains(@class, "banner")]',
            '//div[contains(@id, "hero") or contains(@id, "banner") or contains(@class, "hero") or contains(@class, "banner")]',
            '//header',
            '//body/section'
        ];

        foreach ($patterns as $pattern) {
            $nodes = $xpath->query($pattern);
            if ($nodes->length > 0) {
                $node = $nodes->item(0);
                if ($node->getAttribute('id') !== 'navigation' && !preg_match('/(nav|menu)/i', $node->getAttribute('class'))) {
                    return $node;
                }
            }
        }

        return null;
    }

    /**
     * Generate dynamic page header markup based on theme's hero section style
     */
    private function generatePageHeaderHtml()
    {
        $dom = new DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$this->htmlContent);
        $xpath = new DOMXPath($dom);

        $heroNode = $this->extractHeroSection($xpath);
        if (!$heroNode) {
            // Fallback header
            return <<<HTML
<section class="py-5 bg-dark text-white text-center">
    <div class="container">
        <h1 class="display-4">@yield('page_title', 'Halaman')</h1>
    </div>
</section>
HTML;
        }

        // Clone the hero node
        $doc = new DOMDocument('1.0', 'UTF-8');
        $cloned = $doc->importNode($heroNode, true);
        $doc->appendChild($cloned);
        $clonedXpath = new DOMXPath($doc);

        // Find main headings
        $headings = $clonedXpath->query('.//h1|.//h2|.//h3');
        if ($headings->length > 0) {
            $mainHeading = $headings->item(0);
            while ($mainHeading->firstChild) {
                $mainHeading->removeChild($mainHeading->firstChild);
            }
            $mainHeading->appendChild($doc->createTextNode("@yield('page_title', 'Halaman')"));
        }

        // Remove button, input, form, and anchor button elements to keep it clean
        $elementsToRemove = $clonedXpath->query('.//button|.//input|.//form|.//a[contains(@class, "btn")]');
        foreach ($elementsToRemove as $el) {
            $el->parentNode->removeChild($el);
        }

        $html = $doc->saveHTML();
        $html = preg_replace('/<\?xml[^>]*\?>/', '', $html);
        return trim($html);
    }

    public function generatePageShow()
    {
        $pageHeader = $this->generatePageHeaderHtml();
        $template = <<<BLADE
@extends('frontend::app')
@section('seo')
@php
\$seo = (object)[
    'title' => \$page->title,
    'keyword' => \$page->keyword,
    'description' => \Illuminate\Support\Str::limit(strip_tags(\$page->content), 155, ''),
    'image' => \$page->thumbnail ?? (\$page->gambar()[0] ?? null),
    'url' => url()->current(),
];
@endphp
@endsection

@section('page_title', \$page->title)

@section('content')
    {$pageHeader}

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-12">
                <div class="mb-4">
                    {!! \$page->content !!}
                </div>

                @if (\$page->pdf)
                <p>
                    <a class="btn btn-primary" data-bs-toggle="collapse" href="#contentId" role="button"
                        aria-expanded="false" aria-controls="contentId">
                        Tampilkan PDF
                    </a>
                </p>
                <div class="collapse" id="contentId">
                    <div class="card card-body">
                        <div class="ratio ratio-1x1">
                            <iframe src="/storage/{{ \$page->pdf }}"></iframe>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
BLADE;
        return $this->cleanBladeSyntax($template);
    }

    public function generatePagePost()
    {
        $pageHeader = $this->generatePageHeaderHtml();
        $template = <<<BLADE
@extends('frontend::app')
@section('seo')
@php
\$seo = (object)[
    'title' => \$post->title,
    'keyword' => \$post->keyword,
    'description' => \Illuminate\Support\Str::limit(strip_tags(\$post->content), 155, ''),
    'image' => \$post->thumbnail ?? (\$post->gambar()[0] ?? null),
    'url' => url()->current(),
];
@endphp
@endsection

@section('page_title', \$post->title)

@section('content')
    {$pageHeader}

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                @if(\$post->thumbnail)
                <img src="/storage/{{ \$post->thumbnail }}" class="img-fluid rounded mb-4" alt="{{ \$post->title }}">
                @endif

                <div class="mb-4">
                    {!! \$post->content !!}
                </div>

                @if(\$post->tags)
                <div class="mt-4 mb-4">
                    <strong>Tags:</strong>
                    @foreach(explode(',', \$post->tags) as \$tag)
                    <span class="badge bg-secondary me-1">{{ trim(\$tag) }}</span>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">Latest Posts</div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            @foreach(\$latestposts->take(5) as \$lp)
                            <li class="mb-2">
                                <a href="/post/{{ \$lp->slug }}">{{ \$lp->title }}</a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
BLADE;
        return $this->cleanBladeSyntax($template);
    }

    public function generatePageCategory()
    {
        $pageHeader = $this->generatePageHeaderHtml();
        $template = <<<BLADE
@extends('frontend::app')
@section('page_title', \$category->name ?? 'Category')

@section('content')
    {$pageHeader}

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                @if(isset(\$posts) && \$posts->count() > 0)
                <div class="row">
                    @foreach(\$posts as \$post)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            @if(\$post->thumbnail)
                            <img src="/storage/{{ \$post->thumbnail }}" class="card-img-top" alt="{{ \$post->title }}">
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{ \$post->title }}</h5>
                                <p class="card-text">{{ \Illuminate\Support\Str::limit(strip_tags(\$post->content), 100) }}</p>
                                <a href="/post/{{ \$post->slug }}" class="btn btn-primary btn-sm">Read More</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p>No posts found in this category.</p>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">Categories</div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            @foreach(\$categories as \$cat)
                            <li class="mb-2">
                                <a href="/category/{{ \$cat->slug }}">{{ \$cat->name }}</a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
BLADE;
        return $this->cleanBladeSyntax($template);
    }

    public function generatePageContact()
    {
        $pageHeader = $this->generatePageHeaderHtml();
        $template = <<<BLADE
@extends('frontend::app')
@section('page_title', 'Contact Us')

@section('content')
    {$pageHeader}

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <h3>Get In Touch</h3>
                <form action="/contact" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>

            <div class="col-lg-6">
                <h3>Contact Info</h3>
                <p><strong>Address:</strong> {{ \$setting->address }}</p>
                <p><strong>Email:</strong> {{ \$setting->email }}</p>
                <p><strong>Phone:</strong> {{ \$setting->phone }}</p>
                @if(\$setting->whatsapp)
                <p><strong>WhatsApp:</strong> <a href="https://wa.me/{{ \$setting->whatsapp }}">{{ \$setting->whatsapp }}</a></p>
                @endif
            </div>
        </div>
    </div>
@endsection
BLADE;
        return $this->cleanBladeSyntax($template);
    }

    public function generatePageReport($title = 'Report')
    {
        $pageHeader = $this->generatePageHeaderHtml();
        $template = <<<BLADE
@extends('frontend::app')
@section('page_title', \$page->title ?? '{$title}')

@section('content')
    {$pageHeader}

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-12">
                <div class="mb-4">
                    {!! \$page->content !!}
                </div>
                @if (\$page->pdf)
                <div class="ratio ratio-1x1 mt-4">
                    <iframe src="/storage/{{ \$page->pdf }}"></iframe>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
BLADE;
        return $this->cleanBladeSyntax($template);
    }

    public function generatePageMaintenance()
    {
        $template = <<<BLADE
@extends('frontend::app')
@section('page_title', 'Under Maintenance')

@section('content')
    <div class="container py-5 text-center">
        <h1 class="display-1">🛠️</h1>
        <h1 class="display-4">Under Maintenance</h1>
        <p class="lead">We will be back shortly. Thank you for your patience.</p>
    </div>
@endsection
BLADE;
        return $this->cleanBladeSyntax($template);
    }
}

