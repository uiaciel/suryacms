<?php

namespace Uiaciel\SuryaCms\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class ThemeEditorService
{
    /**
     * Get all editable extensions
     */
    public static function getEditableExtensions(): array
    {
        return [
            'css', 'scss', 'sass',
            'js',
            'html', 'htm',
            'php', 'phtml',
            'json',
            'xml',
            'yaml', 'yml',
            'md', 'markdown',
            'txt',
        ];
    }

    /**
     * Get excluded patterns for files
     */
    public static function getExcludedPatterns(): array
    {
        return [
            '.map',
            '.min.js',
            '.min.css',
            'node_modules',
            '.git',
            '__pycache__',
            '.DS_Store',
        ];
    }

    /**
     * Check if file is editable
     */
    public static function isEditable(string $filePath): bool
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $filename = basename($filePath);

        // Check extension
        if (! in_array($extension, self::getEditableExtensions())) {
            return false;
        }

        // Check excluded patterns
        foreach (self::getExcludedPatterns() as $pattern) {
            if (strpos($filename, $pattern) !== false || strpos($filePath, $pattern) !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get language mode for CodeMirror
     */
    public static function getLanguageMode(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        $languageMap = [
            'css' => 'css',
            'scss' => 'css',
            'sass' => 'sass',
            'js' => 'javascript',
            'html' => 'html',
            'htm' => 'html',
            'php' => 'php',
            'phtml' => 'php',
            'json' => 'json',
            'xml' => 'xml',
            'yaml' => 'yaml',
            'yml' => 'yaml',
            'md' => 'markdown',
            'markdown' => 'markdown',
            'txt' => 'text',
        ];

        return $languageMap[$extension] ?? 'text';
    }

    /**
     * Scan theme files with caching
     */
    public static function scanThemeFiles(string $theme, bool $useCache = true): array
    {
        $cacheKey = "theme_files_{$theme}";

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $themePath = public_path("frontend/{$theme}");

        if (! File::isDirectory($themePath)) {
            return [];
        }

        $files = [];
        $allFiles = File::allFiles($themePath);

        foreach ($allFiles as $file) {
            if (self::isEditable($file->getPathname())) {
                $relativePath = str_replace(public_path('frontend/'), '', $file->getPathname());
                $relativePath = str_replace('\\', '/', $relativePath);

                $files[] = [
                    'path' => $relativePath,
                    'name' => $file->getFilename(),
                    'extension' => $file->getExtension(),
                    'size' => $file->getSize(),
                    'modified' => filemtime($file->getPathname()),
                ];
            }
        }

        // Sort by path
        usort($files, fn ($a, $b) => strcmp($a['path'], $b['path']));

        // Cache for 1 hour
        Cache::put($cacheKey, $files, 3600);

        return $files;
    }

    /**
     * Get file content safely
     */
    public static function getFileContent(string $theme, string $filePath): ?string
    {
        $themePath = public_path("frontend/{$theme}");
        $fullPath = $themePath.'/'.str_replace('/', DIRECTORY_SEPARATOR, $filePath);

        // Prevent directory traversal
        if (! str_starts_with(realpath($fullPath) ?: '', realpath($themePath) ?: '')) {
            throw new \Exception('Invalid file path');
        }

        if (! File::exists($fullPath)) {
            throw new \Exception('File not found');
        }

        return File::get($fullPath);
    }

    /**
     * Save file content safely
     */
    public static function saveFileContent(string $theme, string $filePath, string $content): bool
    {
        $themePath = public_path("frontend/{$theme}");
        $fullPath = $themePath.'/'.str_replace('/', DIRECTORY_SEPARATOR, $filePath);

        // Prevent directory traversal
        if (! str_starts_with(realpath($fullPath) ?: '', realpath($themePath) ?: '')) {
            throw new \Exception('Invalid file path');
        }

        // Create directory if needed
        $directory = dirname($fullPath);
        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Write file
        File::put($fullPath, $content);

        // Clear cache
        Cache::forget("theme_files_{$theme}");

        return true;
    }

    /**
     * Delete file safely
     */
    public static function deleteFile(string $theme, string $filePath): bool
    {
        $themePath = public_path("frontend/{$theme}");
        $fullPath = $themePath.'/'.str_replace('/', DIRECTORY_SEPARATOR, $filePath);

        // Prevent directory traversal
        if (! str_starts_with(realpath($fullPath) ?: '', realpath($themePath) ?: '')) {
            throw new \Exception('Invalid file path');
        }

        if (! File::exists($fullPath)) {
            throw new \Exception('File not found');
        }

        File::delete($fullPath);

        // Clear cache
        Cache::forget("theme_files_{$theme}");

        return true;
    }

    /**
     * Create file safely
     */
    public static function createFile(string $theme, string $filePath, string $content = ''): bool
    {
        $themePath = public_path("frontend/{$theme}");
        $fullPath = $themePath.'/'.str_replace('/', DIRECTORY_SEPARATOR, $filePath);

        // Prevent directory traversal
        if (! str_starts_with(realpath($fullPath) ?: '', realpath($themePath) ?: '')) {
            throw new \Exception('Invalid file path');
        }

        if (File::exists($fullPath)) {
            throw new \Exception('File already exists');
        }

        // Check if extension is editable
        if (! self::isEditable($filePath)) {
            throw new \Exception('File type not allowed');
        }

        // Create directory if needed
        $directory = dirname($fullPath);
        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        File::put($fullPath, $content);

        // Clear cache
        Cache::forget("theme_files_{$theme}");

        return true;
    }

    /**
     * Get file info
     */
    public static function getFileInfo(string $theme, string $filePath): array
    {
        $themePath = public_path("frontend/{$theme}");
        $fullPath = $themePath.'/'.str_replace('/', DIRECTORY_SEPARATOR, $filePath);

        if (! File::exists($fullPath)) {
            throw new \Exception('File not found');
        }

        $file = new \SplFileInfo($fullPath);

        return [
            'path' => $filePath,
            'name' => $file->getFilename(),
            'extension' => $file->getExtension(),
            'size' => $file->getSize(),
            'size_formatted' => self::formatBytes($file->getSize()),
            'modified' => filemtime($fullPath),
            'modified_formatted' => date('d M Y H:i', filemtime($fullPath)),
            'lines' => substr_count(File::get($fullPath), "\n") + 1,
            'language' => self::getLanguageMode($filePath),
        ];
    }

    /**
     * Format bytes to human readable
     */
    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }

    /**
     * Get directory structure
     */
    public static function getDirectoryStructure(string $theme): array
    {
        $themePath = public_path("frontend/{$theme}");

        if (! File::isDirectory($themePath)) {
            return [];
        }

        $structure = [];
        $allItems = File::allFiles($themePath);

        foreach ($allItems as $item) {
            if (self::isEditable($item->getPathname())) {
                $relativePath = str_replace(public_path('frontend/'), '', $item->getPathname());
                $relativePath = str_replace('\\', '/', $relativePath);
                $parts = explode('/', $relativePath);

                // Build nested structure
                $current = &$structure;
                foreach ($parts as $part) {
                    if (! isset($current[$part])) {
                        $current[$part] = [];
                    }
                    $current = &$current[$part];
                }
            }
        }

        return $structure;
    }

    /**
     * Search files by content
     */
    public static function searchFiles(string $theme, string $query): array
    {
        $files = self::scanThemeFiles($theme, false);
        $results = [];

        foreach ($files as $file) {
            try {
                $content = self::getFileContent($theme, $file['path']);
                if (stripos($content, $query) !== false) {
                    $results[] = [
                        'file' => $file,
                        'matches' => substr_count($content, $query, 0, stripos($content, $query)) + 1,
                    ];
                }
            } catch (\Exception $e) {
                // Skip files that can't be read
                continue;
            }
        }

        return $results;
    }

    /**
     * Get file diff
     */
    public static function getFileDiff(string $theme, string $filePath, string $newContent): array
    {
        try {
            $oldContent = self::getFileContent($theme, $filePath);
        } catch (\Exception $e) {
            $oldContent = '';
        }

        $oldLines = explode("\n", $oldContent);
        $newLines = explode("\n", $newContent);

        $diff = [];
        $maxLines = max(count($oldLines), count($newLines));

        for ($i = 0; $i < $maxLines; $i++) {
            $oldLine = $oldLines[$i] ?? '';
            $newLine = $newLines[$i] ?? '';

            if ($oldLine === $newLine) {
                $diff[] = [
                    'type' => 'unchanged',
                    'line' => $i + 1,
                    'old' => $oldLine,
                    'new' => $newLine,
                ];
            } else {
                $diff[] = [
                    'type' => 'changed',
                    'line' => $i + 1,
                    'old' => $oldLine,
                    'new' => $newLine,
                ];
            }
        }

        return $diff;
    }

    /**
     * Clear theme cache
     */
    public static function clearCache(?string $theme = null): void
    {
        if ($theme) {
            Cache::forget("theme_files_{$theme}");
        } else {
            // Clear all theme caches
            $themes = config('frontend.available_themes', []);
            foreach ($themes as $themeName) {
                Cache::forget("theme_files_{$themeName}");
            }
        }
    }
}
