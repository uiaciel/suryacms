<?php

namespace Uiaciel\SuryaCms\Services;

use RuntimeException;
use ZipArchive;

class ThemeZipInspectorService
{
    private const MAX_FILES = 2000;

    private const MAX_UNCOMPRESSED_BYTES = 104857600;

    public function extract(string $zipPath, string $extractPath): array
    {
        $zip = new ZipArchive;
        if ($zip->open($zipPath) !== true) {
            throw new RuntimeException('ZIP tidak dapat dibuka.');
        }

        if ($zip->numFiles > self::MAX_FILES) {
            $zip->close();
            throw new RuntimeException('ZIP melewati batas 2.000 file.');
        }

        if (! is_dir($extractPath) && ! mkdir($extractPath, 0755, true) && ! is_dir($extractPath)) {
            $zip->close();
            throw new RuntimeException('Folder ekstraksi tidak dapat dibuat.');
        }

        $candidates = [];
        $warnings = [];
        $totalBytes = 0;
        $fileCount = $zip->numFiles;
        $base = realpath($extractPath);
        if (! $base) {
            $zip->close();
            throw new RuntimeException('Folder ekstraksi tidak valid.');
        }

        try {
            for ($index = 0; $index < $zip->numFiles; $index++) {
                $entry = $zip->getNameIndex($index);
                if (! is_string($entry)) {
                    continue;
                }

                $normalized = str_replace('\\', '/', $entry);
                $parts = explode('/', $normalized);
                if (
                    $normalized === '' ||
                    str_starts_with($normalized, '/') ||
                    preg_match('/^[a-z]:/i', $normalized) ||
                    in_array('..', $parts, true) ||
                    in_array('.', $parts, true) ||
                    str_contains($normalized, "\0")
                ) {
                    $warnings[] = "Path ZIP tidak aman dilewati: {$entry}";
                    continue;
                }

                $stat = $zip->statIndex($index);
                $entrySize = (int) ($stat['size'] ?? 0);
                $totalBytes += $entrySize;
                if ($totalBytes > self::MAX_UNCOMPRESSED_BYTES) {
                    throw new RuntimeException('Ukuran hasil ekstrak ZIP melebihi 100 MB.');
                }

                $target = $base.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $normalized);
                $parent = dirname($target);
                if (! is_dir($parent) && ! mkdir($parent, 0755, true) && ! is_dir($parent)) {
                    throw new RuntimeException("Folder ZIP tidak dapat dibuat: {$entry}");
                }

                if (str_ends_with($normalized, '/')) {
                    continue;
                }

                if (! $this->isRegularFile($zip, $index)) {
                    $warnings[] = "Entri non-file dilewati: {$entry}";
                    continue;
                }

                $content = $zip->getFromIndex($index);
                if (! is_string($content)) {
                    throw new RuntimeException("File ZIP tidak dapat dibaca: {$entry}");
                }
                if (file_put_contents($target, $content) === false) {
                    throw new RuntimeException("File ZIP tidak dapat disimpan: {$entry}");
                }

                if (preg_match('/(^|\/)index\.html?$/i', $normalized)) {
                    $candidates[] = [
                        'path' => $target,
                        'relative' => $normalized,
                        'depth' => substr_count(trim($normalized, '/'), '/'),
                        'html' => $content,
                    ];
                }
            }
        } finally {
            $zip->close();
        }

        if ($candidates === []) {
            throw new RuntimeException('index.html tidak ditemukan di dalam ZIP.');
        }

        foreach ($candidates as &$candidate) {
            $candidate['reference_score'] = $this->countResolvedReferences($candidate['html'], dirname($candidate['path']), $base);
            unset($candidate['html']);
        }
        unset($candidate);

        usort($candidates, static function (array $left, array $right): int {
            return ($right['reference_score'] <=> $left['reference_score'])
                ?: ($left['depth'] <=> $right['depth'])
                ?: strcmp($left['relative'], $right['relative']);
        });

        if (count($candidates) > 1) {
            $warnings[] = 'Beberapa file index ditemukan; file dengan referensi aset lokal terbanyak dipilih.';
        }

        return [
            'index_path' => $candidates[0]['path'],
            'index_relative_path' => $candidates[0]['relative'],
            'candidates' => array_column($candidates, 'relative'),
            'file_count' => $fileCount,
            'uncompressed_size' => $totalBytes,
            'warnings' => $warnings,
        ];
    }

    private function countResolvedReferences(string $html, string $directory, string $root): int
    {
        preg_match_all('/(?:src|href)\s*=\s*([\'"])(.*?)\1/i', $html, $matches);
        $count = 0;
        foreach ($matches[2] ?? [] as $reference) {
            $path = parse_url(html_entity_decode($reference, ENT_QUOTES | ENT_HTML5), PHP_URL_PATH);
            if (! is_string($path) || $path === '' || str_starts_with($path, '//') || preg_match('/^(?:https?:|data:|mailto:|#)/i', $path)) {
                continue;
            }

            $candidate = str_starts_with($path, '/')
                ? $root.DIRECTORY_SEPARATOR.ltrim(str_replace('/', DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR)
                : $directory.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $path);
            if (is_file($candidate)) {
                $count++;
            }
        }

        return $count;
    }

    private function isRegularFile(ZipArchive $zip, int $index): bool
    {
        if (! method_exists($zip, 'getExternalAttributesIndex')) {
            return true;
        }

        $opsys = 0;
        $attributes = 0;
        if (! $zip->getExternalAttributesIndex($index, $opsys, $attributes)) {
            return true;
        }
        if ($opsys !== ZipArchive::OPSYS_UNIX) {
            return true;
        }

        $fileType = ($attributes >> 16) & 0170000;

        return $fileType === 0 || $fileType === 0100000;
    }
}
