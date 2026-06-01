<?php

namespace Uiaciel\SuryaCms\Services;

use DOMDocument;
use DOMXPath;

/**
 * PlaceholderMappingService
 *
 * Service untuk mendeteksi dan melakukan mapping placeholder dalam HTML template tema
 * Menangani:
 * - Social media links
 * - Email links dan text
 * - Logo images
 * - Relative internal links
 * - Copyright text
 * - Contact information
 */
class PlaceholderMappingService
{
    private $html;
    private $themeName;
    private $dom;
    private $xpath;

    public function __construct($html, $themeName = 'default')
    {
        $this->html = $html;
        $this->themeName = $themeName;
        $this->initializeDom();
    }

    private function initializeDom()
    {
        $this->dom = new DOMDocument;
        libxml_use_internal_errors(true);

        // Handle empty HTML
        if (!empty($this->html)) {
            @$this->dom->loadHTML(
                mb_convert_encoding($this->html, 'HTML-ENTITIES', 'UTF-8'),
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
            );
        }

        libxml_clear_errors();
        $this->xpath = new DOMXPath($this->dom);
    }

    /**
     * Scan dan collect semua placeholder dalam HTML
     * Return array of mappings dengan struktur [original => replacement, type => ...]
     */
    public function scan()
    {
        $mappings = [];

        // 1. Scan social media links dalam href
        $mappings = array_merge($mappings, $this->scanSocialMediaLinks());

        // 2. Scan email links (mailto:)
        $mappings = array_merge($mappings, $this->scanEmailLinks());

        // 3. Scan email text addresses
        $mappings = array_merge($mappings, $this->scanEmailAddresses());

        // 4. Scan logo images
        $mappings = array_merge($mappings, $this->scanLogoImages());

        // 5. Scan relative internal links (index.html, pages/about.html, etc)
        $mappings = array_merge($mappings, $this->scanRelativeLinks());

        // 6. Scan phone numbers
        $mappings = array_merge($mappings, $this->scanPhoneNumbers());

        // 7. Scan copyright text
        $mappings = array_merge($mappings, $this->scanCopyright());

        // 8. Scan contact information keywords
        $mappings = array_merge($mappings, $this->scanContactKeywords());

        // Remove duplicates
        $unique = [];
        foreach ($mappings as $original => $mapping) {
            if (!isset($unique[$original])) {
                $unique[$original] = $mapping;
            }
        }

        return $unique;
    }

    /**
     * Scan social media links dalam href attributes
     * <a href="https://facebook.com/...">Facebook</a>
     * atau
     * <a href="https://facebook.com/..."><i class="fa fa-facebook"></i></a>
     */
    private function scanSocialMediaLinks()
    {
        $mappings = [];
        $socialDomains = [
            'facebook.com' => '{{ $setting->facebook }}',
            'twitter.com' => '{{ $setting->twitter }}',
            'x.com' => '{{ $setting->twitter }}',
            'instagram.com' => '{{ $setting->instagram }}',
            'linkedin.com' => '{{ $setting->linkedin }}',
            'youtube.com' => '{{ $setting->youtube }}',
            'tiktok.com' => '{{ $setting->tiktok }}',
            'github.com' => '{{ $setting->github }}',
            'pinterest.com' => '{{ $setting->pinterest }}',
            'whatsapp.com' => '{{ $setting->whatsapp }}',
        ];

        // Query semua <a> tags dengan href
        $anchors = $this->xpath->query('//a[@href]');

        if ($anchors !== false) {
            foreach ($anchors as $anchor) {
                $href = $anchor->getAttribute('href');

                // Check if href contains social domain
                foreach ($socialDomains as $domain => $replacement) {
                    if (strpos($href, $domain) !== false) {
                        // For social media, we replace the entire href
                        $mappings[$href] = [
                            'original' => $href,
                            'replacement' => $replacement,
                            'type' => 'social_link',
                            'enabled' => true,
                        ];
                        break;
                    }
                }
            }
        }

        return $mappings;
    }

    /**
     * Scan email links dalam mailto:
     * <a href="mailto:info@example.com">...</a>
     * Replace href menjadi: <a href="mailto:{{ $setting->email }}">...</a>
     */
    private function scanEmailLinks()
    {
        $mappings = [];

        // Query semua mailto: links
        $mailtos = $this->xpath->query('//a[starts-with(@href, "mailto:")]');

        if ($mailtos !== false) {
            foreach ($mailtos as $mailto) {
                $href = $mailto->getAttribute('href');

                // Extract email dari mailto:email@domain.com
                if (preg_match('/mailto:([^\?]+)/', $href, $matches)) {
                    $email = trim($matches[1]);

                    $mappings[$href] = [
                        'original' => $href,
                        'replacement' => 'mailto:{{ $setting->email }}',
                        'type' => 'email_link',
                        'enabled' => true,
                    ];
                }
            }
        }

        return $mappings;
    }

    /**
     * Scan email addresses sebagai text nodes
     * info@example.com → {{ $setting->email }}
     */
    private function scanEmailAddresses()
    {
        $mappings = [];

        // Match semua email pattern di HTML
        if (preg_match_all('/[\w\.-]+@[\w\.-]+\.\w+/', $this->html, $matches)) {
            foreach (array_unique($matches[0]) as $email) {
                // Skip jika sudah berada di dalam mailto: link (akan di-handle oleh scanEmailLinks)
                if (strpos($this->html, "mailto:{$email}") !== false) {
                    continue;
                }

                $mappings[$email] = [
                    'original' => $email,
                    'replacement' => '{{ $setting->email }}',
                    'type' => 'email_text',
                    'enabled' => true,
                ];
            }
        }

        return $mappings;
    }

    /**
     * Scan logo images
     * <img src="..." alt="logo"> → <img src="{{ $setting->logo ?? 'frontend/themename/img/logo.jpg' }}">
     */
    private function scanLogoImages()
    {
        $mappings = [];

        // Query img tags yang terlihat seperti logo
        $images = $this->xpath->query('//img');

        if ($images !== false) {
            foreach ($images as $img) {
                $src = $img->getAttribute('src');
                $alt = $img->getAttribute('alt');
                $class = $img->getAttribute('class');
                $id = $img->getAttribute('id');

                // Detect if this is a logo based on alt, class, id, atau src
                $isLogo = (
                    stripos($alt, 'logo') !== false ||
                    stripos($class, 'logo') !== false ||
                    stripos($id, 'logo') !== false ||
                    stripos($src, 'logo') !== false ||
                    stripos($src, 'brand') !== false
                );

                if ($isLogo && !empty($src)) {
                    $replacement = "{{ \$setting->logo ?? 'frontend/{$this->themeName}/img/logo.jpg' }}";

                    $mappings[$src] = [
                        'original' => $src,
                        'replacement' => $replacement,
                        'type' => 'logo_image',
                        'enabled' => true,
                    ];
                }
            }
        }

        return $mappings;
    }

    /**
     * Scan relative internal links
     * <a href="index.html"> → <a href="{{ $setting->url }}">
     * <a href="pages/about.html"> → <a href="{{ url('/pages/about') }}">
     * <a href="contact.html"> → <a href="{{ url('/contact') }}">
     */
    private function scanRelativeLinks()
    {
        $mappings = [];

        $anchors = $this->xpath->query('//a[@href]');

        if ($anchors !== false) {
            foreach ($anchors as $anchor) {
                $href = $anchor->getAttribute('href');

                // Check if it's a relative link (not starting with http, /, or #, mailto:, tel:)
                if (
                    $href &&
                    !preg_match('/^(https?:|\/|#|mailto:|tel:)/', $href) &&
                    preg_match('/\.(html?|php|aspx?)$/i', $href)
                ) {
                    // For index.html or similar, replace dengan $setting->url
                    if (preg_match('/^index\.(html?|php)$/i', $href)) {
                        $mappings[$href] = [
                            'original' => $href,
                            'replacement' => "{{ \$setting->url }}",
                            'type' => 'index_link',
                            'enabled' => true,
                        ];
                    } else {
                        // For other pages, extract pathname
                        $pathname = preg_replace('/\.(html?|php|aspx?)$/i', '', $href);

                        $mappings[$href] = [
                            'original' => $href,
                            'replacement' => "{{ url('{$pathname}') }}",
                            'type' => 'relative_link',
                            'enabled' => true,
                        ];
                    }
                }
            }
        }

        return $mappings;
    }

    /**
     * Scan phone numbers
     * +62 812-3456-7890 → {{ $setting->phone }}
     */
    private function scanPhoneNumbers()
    {
        $mappings = [];

        // Match phone number patterns
        if (preg_match_all('/(\+?\d{1,3}[-.\s]?\d{1,14}|\(\d{1,4}\)[-.\s]?\d{1,4}[-.\s]?\d{1,9})/', $this->html, $matches)) {
            foreach (array_unique($matches[0]) as $phone) {
                $phone = trim($phone);

                // Skip if it looks like a CSS class or ID
                if (preg_match('/^[\d\.-]{2,4}$/', $phone)) {
                    continue;
                }

                $mappings[$phone] = [
                    'original' => $phone,
                    'replacement' => '{{ $setting->phone }}',
                    'type' => 'phone_number',
                    'enabled' => true,
                ];
            }
        }

        return $mappings;
    }

    /**
     * Scan copyright text
     * © 2024 Company Name → © {{ date('Y') }} {{ $setting->sitename }}
     */
    private function scanCopyright()
    {
        $mappings = [];

        // Multiple patterns untuk copyright
        $patterns = [
            '/&copy;\s*(\d{4})?\s*(.+?)(?:<|\n|$)/i',           // &copy; 2024 Company
            '/copyright\s*©?\s*(\d{4})?\s*(.+?)(?:<|\n|$)/i',  // Copyright 2024 Company
            '/©\s*(\d{4})?\s*(.+?)(?:all rights|<|\n|$)/i',     // © 2024 Company ... All Rights
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $this->html, $matches)) {
                $fullCopyright = trim($matches[0]);

                // Clean up HTML tags and entities if present
                $fullCopyright = trim(preg_replace('/<[^>]+>/', '', $fullCopyright));
                $fullCopyright = html_entity_decode($fullCopyright, ENT_QUOTES);

                if (!empty($fullCopyright) && strlen($fullCopyright) > 5) {
                    $replacement = '© {{ date("Y") }} {{ $setting->sitename }} . All Rights Reserved.';

                    $mappings[$fullCopyright] = [
                        'original' => $fullCopyright,
                        'replacement' => $replacement,
                        'type' => 'copyright',
                        'enabled' => true,
                    ];
                    break; // Stop after finding first copyright
                }
            }
        }

        return $mappings;
    }

    /**
     * Scan contact information keywords
     * "Address:", "Phone:", "Email:", "WhatsApp:" etc dalam content
     */
    private function scanContactKeywords()
    {
        $mappings = [];
        $keywords = [
            'address' => '{{ $setting->address }}',
            'phone' => '{{ $setting->phone }}',
            'email' => '{{ $setting->email }}',
            'whatsapp' => '{{ $setting->whatsapp }}',
            'website' => '{{ $setting->url }}',
            'company' => '{{ $setting->sitename }}',
            'description' => '{{ $setting->description }}',
            'slogan' => '{{ $setting->slogan }}',
        ];

        foreach ($keywords as $keyword => $replacement) {
            // Search for keyword pattern: "Address: Some Value"
            $pattern = '/(?<![a-zA-Z0-9_-])' . preg_quote($keyword, '/') . '\s*[:=]?\s*([^\n<]{5,100})(?=[<\n]|$)/i';

            if (preg_match_all($pattern, $this->html, $matches)) {
                foreach ($matches[0] as $match) {
                    $trimmed = trim($match);

                    // Skip if too short or looks like CSS
                    if (strlen($trimmed) < 3 || preg_match('/^[\w\-]{2,6}$/', $trimmed)) {
                        continue;
                    }

                    if (!isset($mappings[$trimmed])) {
                        $mappings[$trimmed] = [
                            'original' => $trimmed,
                            'replacement' => $replacement,
                            'type' => 'contact_' . $keyword,
                            'enabled' => false, // Disabled by default karena mungkin false positive
                        ];
                    }
                }
            }
        }

        return $mappings;
    }

    /**
     * Apply mapping ke HTML
     * Hanya replace yang enabled=true
     */
    public function applyMappings($mappings)
    {
        $html = $this->html;

        foreach ($mappings as $mapping) {
            if (!$mapping['enabled']) {
                continue;
            }

            $original = $mapping['original'];
            $replacement = $mapping['replacement'];
            $type = $mapping['type'];

            // Different replacement strategies based on type
            if ($type === 'social_link' || $type === 'email_link' || $type === 'index_link' || $type === 'relative_link') {
                // Replace href attributes
                $html = $this->replaceHrefAttribute($html, $original, $replacement);
            } elseif ($type === 'logo_image') {
                // Replace src attributes
                $html = $this->replaceSrcAttribute($html, $original, $replacement);
            } else {
                // Replace text content (email, phone, copyright, etc)
                $html = $this->replaceTextContent($html, $original, $replacement);
            }
        }

        return $html;
    }

    /**
     * Replace href attribute safely
     */
    private function replaceHrefAttribute($html, $original, $replacement)
    {
        // Match href="original" or href='original'
        $pattern = '/href\s*=\s*["\']' . preg_quote($original, '/') . '["\'](?=[^>]*>)/';

        return preg_replace($pattern, "href=\"{$replacement}\"", $html);
    }

    /**
     * Replace src attribute safely
     */
    private function replaceSrcAttribute($html, $original, $replacement)
    {
        // Match src="original" atau src='original'
        $pattern = '/src\s*=\s*["\']' . preg_quote($original, '/') . '["\'](?=[^>]*>)/';

        return preg_replace($pattern, "src=\"{$replacement}\"", $html);
    }

    /**
     * Replace text content safely (outside HTML tags)
     * Gunakan negative lookbehind/lookahead untuk menghindari tag attributes
     */
    private function replaceTextContent($html, $original, $replacement)
    {
        // Pattern untuk replace text outside tags dan attributes
        $pattern = '~<(script|style)[^>]*>.*?<\/\1>|<[^>]+>(*SKIP)(*F)|\b' . preg_quote($original, '~') . '\b~is';

        return preg_replace($pattern, $replacement, $html);
    }

    /**
     * Scan dan return formatted mapping array untuk Livewire
     */
    public function getScanResults()
    {
        $scanned = $this->scan();
        $formatted = [];

        foreach ($scanned as $mapping) {
            $formatted[] = $mapping;
        }

        return $formatted;
    }

    /**
     * Normalize dan clean main tag untuk index.blade.php generation
     * Hapus <main> dan </main> tag, ganti <div class="section"> dengan <section>
     */
    public function prepareIndexHtml()
    {
        $html = $this->html;

        // Remove <main> and </main> tags tapi preserve content
        $html = preg_replace('/<\s*main[^>]*>/i', '', $html);
        $html = preg_replace('/<\s*\/main\s*>/i', '', $html);

        // Replace <div> tags yang terlihat seperti section dengan <section>
        // Hanya untuk div dengan class="section" atau id="section-*"
        $html = preg_replace_callback(
            '/<div\s+([^>]*(?:class|id)\s*=\s*["\'](?:[^"\']*)?section[^"\']*["\'][^>]*)>/i',
            function ($matches) {
                $attrs = $matches[1];
                return "<section {$attrs}>";
            },
            $html
        );

        // Replace closing </div> untuk section yang dibuka
        // Pattern: </div> yang diikuti oleh section element
        $html = preg_replace(
            '/<\/div>\s*(?=<\/(section|main|article|aside)>|$)/i',
            '</section>',
            $html
        );

        return $html;
    }
}
