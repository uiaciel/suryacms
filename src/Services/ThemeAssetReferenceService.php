<?php

namespace Uiaciel\SuryaCms\Services;

class ThemeAssetReferenceService
{
    private const ASSET_EXTENSIONS = [
        'avif', 'css', 'eot', 'gif', 'ico', 'jpeg', 'jpg', 'js', 'json',
        'mjs', 'mp3', 'mp4', 'ogg', 'otf', 'png', 'svg', 'ttf', 'wasm',
        'webm', 'webmanifest', 'webp', 'woff', 'woff2',
    ];

    private const ASSET_DIRECTORIES = [
        'asset', 'assets', 'css', 'font', 'fonts', 'img', 'images', 'js', 'static', 'vendor',
    ];

    public function rebaseToArchiveRoot(string $html, string $indexRelativePath): string
    {
        $directory = dirname(str_replace('\\', '/', $indexRelativePath));
        if ($directory === '.') {
            return $html;
        }

        return $this->rewriteReferences($html, static function (string $url, string $attribute) use ($directory): string {
            if (! self::isAssetReference($url, $attribute) || self::isExternal($url)) {
                return $url;
            }

            $path = parse_url($url, PHP_URL_PATH);
            if (! is_string($path) || $path === '' || str_starts_with($path, '/')) {
                return $url;
            }

            $segments = array_merge(explode('/', $directory), explode('/', $path));
            $resolved = [];
            foreach ($segments as $segment) {
                if ($segment === '' || $segment === '.') {
                    continue;
                }
                if ($segment === '..') {
                    if ($resolved === []) {
                        return $url;
                    }
                    array_pop($resolved);

                    continue;
                }
                $resolved[] = $segment;
            }

            return implode('/', $resolved).substr($url, strlen($path));
        });
    }

    public function prefixThemeAssets(string $html, string $theme): string
    {
        return $this->rewriteReferences($html, static function (string $url, string $attribute) use ($theme): string {
            if (! self::isAssetReference($url, $attribute) || self::isExternal($url)) {
                return $url;
            }

            $path = parse_url($url, PHP_URL_PATH);
            if (! is_string($path) || $path === '' || str_starts_with($path, '/frontend/')) {
                return $url;
            }

            return "/frontend/{$theme}/".ltrim($path, '/').substr($url, strlen($path));
        });
    }

    public function prefixRootRelativeCssAssets(string $css, string $theme): string
    {
        return preg_replace_callback('/url\(\s*([\'"]?)(\/(?!\/)[^\'")]+)\1\s*\)/i', static function (array $match) use ($theme): string {
            if (str_starts_with($match[2], '/frontend/')) {
                return $match[0];
            }

            return 'url('.$match[1]."/frontend/{$theme}/".ltrim($match[2], '/').$match[1].')';
        }, $css) ?? $css;
    }

    private function rewriteReferences(string $html, callable $rewrite): string
    {
        $html = preg_replace_callback('/\b(src|href|poster|srcset)\s*=\s*([\'"])(.*?)\2/is', static function (array $match) use ($rewrite): string {
            $attribute = strtolower($match[1]);
            $value = $match[3];
            if ($attribute === 'srcset') {
                $value = preg_replace_callback('/(^|,\s*)([^\s,]+)(?=\s|,|$)/', static function (array $candidate) use ($rewrite, $attribute): string {
                    return $candidate[1].$rewrite($candidate[2], $attribute);
                }, $value) ?? $value;
            } else {
                $value = $rewrite($value, $attribute);
            }

            return $match[1].'='.$match[2].$value.$match[2];
        }, $html) ?? $html;

        return preg_replace_callback('/url\(\s*([\'"]?)([^\'")]+)\1\s*\)/i', static function (array $match) use ($rewrite): string {
            return 'url('.$match[1].$rewrite(trim($match[2]), 'css').$match[1].')';
        }, $html) ?? $html;
    }

    public static function isAssetReference(string $url, string $attribute): bool
    {
        if (in_array($attribute, ['src', 'srcset', 'poster', 'css'], true)) {
            return true;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (! is_string($path) || $path === '') {
            return false;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $firstSegment = strtolower(explode('/', ltrim($path, '/'))[0] ?? '');

        return in_array($extension, self::ASSET_EXTENSIONS, true)
            || in_array($firstSegment, self::ASSET_DIRECTORIES, true);
    }

    private static function isExternal(string $url): bool
    {
        return $url === ''
            || str_contains($url, '{{')
            || preg_match('/^(?:https?:)?\/\//i', $url) === 1
            || preg_match('/^(?:data:|blob:|javascript:|mailto:|tel:|#)/i', $url) === 1;
    }
}
