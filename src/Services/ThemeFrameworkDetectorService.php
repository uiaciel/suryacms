<?php

namespace Uiaciel\SuryaCms\Services;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use FilesystemIterator;
use RuntimeException;

class ThemeFrameworkDetectorService
{
    public function detect(string $html, string $sourceRoot): array
    {
        $css = '';
        $evidence = [
            'bootstrap' => [],
            'tailwind' => [],
        ];

        preg_match_all('/<link\b[^>]*>/i', $html, $links);
        foreach ($links[0] ?? [] as $link) {
            if (! preg_match('/href\s*=\s*([\'"])(.*?)\1/i', $link, $match)) {
                continue;
            }

            $href = html_entity_decode($match[2], ENT_QUOTES | ENT_HTML5);
            if (preg_match('/^(?:https?:)?\/\//i', $href)) {
                if (preg_match('/bootstrap(?:@|\/|\.min)?/i', $href)) {
                    $evidence['bootstrap'][] = 'stylesheet URL: '.$href;
                }
                if (preg_match('/tailwind(?:css)?(?:@|\/|\.min)?/i', $href)) {
                    $evidence['tailwind'][] = 'stylesheet URL: '.$href;
                }

                continue;
            }

            $relative = parse_url($href, PHP_URL_PATH);
            if (! is_string($relative)) {
                continue;
            }

            $path = realpath($sourceRoot.DIRECTORY_SEPARATOR.str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($relative, '/\\')));
            $base = realpath($sourceRoot);
            if ($path && $base && str_starts_with($path, $base.DIRECTORY_SEPARATOR) && is_file($path) && strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'css') {
                $css .= "\n".$this->readFile($path);
                if (preg_match('/bootstrap/i', basename($path))) {
                    $evidence['bootstrap'][] = 'CSS asset: '.basename($path);
                }
                if (preg_match('/tailwind/i', basename($path))) {
                    $evidence['tailwind'][] = 'CSS asset: '.basename($path);
                }
            }
        }

        $classNames = [];
        preg_match_all('/\bclass\s*=\s*([\'"])(.*?)\1/is', $html, $classMatches);
        foreach ($classMatches[2] ?? [] as $classList) {
            $classNames = array_merge($classNames, preg_split('/\s+/', trim($classList)) ?: []);
        }

        $bootstrapClasses = ['container', 'container-fluid', 'row', 'col', 'btn', 'navbar', 'nav-item', 'dropdown-menu', 'form-control', 'text-center', 'img-fluid', 'd-flex'];
        $bootstrapClassMatches = array_values(array_intersect($classNames, $bootstrapClasses));
        foreach ($classNames as $className) {
            if (preg_match('/^(?:col-(?:xs|sm|md|lg|xl|xxl)-\d+|btn-[\w-]+|navbar-[\w-]+)$/i', $className)) {
                $bootstrapClassMatches[] = $className;
            }
        }
        if ($bootstrapClassMatches !== []) {
            $evidence['bootstrap'][] = 'HTML classes: '.implode(', ', array_slice($bootstrapClassMatches, 0, 8));
        }
        if (preg_match('/\bdata-bs-[\w-]+\s*=/i', $html)) {
            $evidence['bootstrap'][] = 'Bootstrap data-bs attributes';
        }

        $tailwindClasses = array_values(array_filter(
            $classNames,
            fn (string $class): bool => $this->looksLikeTailwindUtility($class)
        ));
        if ($tailwindClasses !== []) {
            $evidence['tailwind'][] = 'HTML utility classes: '.implode(', ', array_slice($tailwindClasses, 0, 8));
        }
        if (preg_match('/(?:cdn\.tailwindcss\.com|tailwindcss)/i', $html)) {
            $evidence['tailwind'][] = 'Tailwind stylesheet or CDN reference';
        }

        $cssFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sourceRoot, FilesystemIterator::SKIP_DOTS));
        foreach ($cssFiles as $file) {
            if (! $file->isFile() || strtolower($file->getExtension()) !== 'css') {
                continue;
            }
            if (preg_match('/bootstrap/i', $file->getFilename())) {
                $evidence['bootstrap'][] = 'CSS file: '.str_replace($sourceRoot.DIRECTORY_SEPARATOR, '', $file->getPathname());
            }
            if (preg_match('/tailwind/i', $file->getFilename())) {
                $evidence['tailwind'][] = 'CSS file: '.str_replace($sourceRoot.DIRECTORY_SEPARATOR, '', $file->getPathname());
            }
            $css .= "\n".$this->readFile($file->getPathname());
        }

        if (preg_match('/@tailwind\s+(?:base|components|utilities)|@import\s+[^;]*tailwindcss|--tw-[\w-]+\s*:/i', $css)) {
            $evidence['tailwind'][] = 'Tailwind directives or generated variables in CSS';
        }
        if (preg_match('/\.container\s*\{[^}]*max-width|\.row\s*\{[^}]*display\s*:\s*flex/i', $css)) {
            $evidence['bootstrap'][] = 'Bootstrap layout rules in CSS';
        }

        $evidence = array_map(static fn (array $items): array => array_values(array_unique($items)), $evidence);
        $bootstrapDetected = $evidence['bootstrap'] !== [];
        $tailwindDetected = $evidence['tailwind'] !== [];

        if ($bootstrapDetected && $tailwindDetected) {
            $framework = 'mixed';
        } elseif ($bootstrapDetected) {
            $framework = 'bootstrap';
        } elseif ($tailwindDetected) {
            $framework = 'tailwind';
        } else {
            $framework = trim($css) === '' ? 'unknown' : 'custom';
        }

        $evidenceCount = count($evidence['bootstrap']) + count($evidence['tailwind']);
        $confidence = $framework === 'unknown' ? 'low' : ($evidenceCount >= 3 ? 'high' : 'medium');

        return [
            'framework' => $framework,
            'confidence' => $confidence,
            'evidence' => $evidence,
        ];
    }

    private function readFile(string $path): string
    {
        $content = file_get_contents($path);
        if ($content === false) {
            throw new RuntimeException("File CSS tidak dapat dibaca: {$path}");
        }

        return $content;
    }

    private function looksLikeTailwindUtility(string $class): bool
    {
        $utility = preg_replace('/^(?:sm|md|lg|xl|2xl|hover|focus|dark|active|disabled|group-hover):/', '', $class) ?? $class;

        return preg_match('/^(?:flex|grid|block|hidden|inline-flex|items-[\w-]+|justify-[\w-]+|gap-[\w-]+|space-[xy]-[\w-]+|p[trblxy]?-\d+|m[trblxy]?-\d+|w-(?:full|screen|\d+)|h-(?:full|screen|\d+)|text-(?:xs|sm|base|lg|xl|[2-9]xl|(?:slate|gray|red|blue|green|emerald|rose|indigo)-\d{2,3})|bg-(?:gradient-to-[\w-]+|(?:slate|gray|red|blue|green|emerald|rose|indigo)-\d{2,3})|rounded-(?:sm|md|lg|xl|2xl|3xl|full)|font-(?:thin|normal|medium|semibold|bold)|leading-[\w-]+)$/i', $utility) === 1;
    }
}
