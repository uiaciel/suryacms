<?php

namespace Uiaciel\SuryaCms\Services;

use Illuminate\Support\Facades\File;
use ZipArchive;

class ZipInspectorService
{
    /**
     * Daftar folder/file yang HARUS DIHAPUS (Junk/Development files)
     */
    private array $junkItems = [
        '__MACOSX', '.DS_Store', '.git', '.svn', '.hg',
        'node_modules', 'bower_components', 'vendor',
        'package.json', 'package-lock.json', 'composer.json',
        'README.md', 'README.txt', 'LICENSE', 'LICENSE.md',
        '.env', 'Thumbs.db', 'desktop.ini'
    ];

    /**
     * Daftar folder aset yang VALID (untuk referensi/logging jika diperlukan)
     */
    private array $validAssetFolders = [
        'css', 'js', 'images', 'img', 'fonts', 'webfonts',
        'assets', 'scss', 'sass', 'media', 'icons', 'svg', 'vendor'
    ];

    /**
     * Extract ZIP ke $destination, bersihkan junk, dan kembalikan info index.html.
     */
    public function extract(string $zipPath, string $destination): array
    {
        if (!File::isDirectory($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new \RuntimeException('Gagal membuka file ZIP.');
        }

        // 1. Deteksi apakah ada single top-level folder
        $topLevel = $this->detectTopLevelFolder($zip);

        // 2. Extract semua file terlebih dahulu
        $zip->extractTo($destination);
        $zip->close();

        // 3. Tentukan root folder yang sebenarnya
        $root = $destination;
        if ($topLevel && File::isDirectory($destination . '/' . $topLevel)) {
            $root = $destination . '/' . $topLevel;

            // Opsional: Jika semua file ada di dalam 1 folder, kita bisa "unwrap"
            // agar strukturnya lebih rapi, tapi membiarkannya juga tidak masalah.
            // Untuk keamanan, kita biarkan $root menunjuk ke folder tersebut.
        }

        // 4. SANITASI: Hapus file/folder junk yang tidak diperlukan
        $this->sanitizeFolder($root);

        // 5. Cari index.html
        $indexPath = $this->findIndexHtml($root);
        if (!$indexPath) {
            throw new \RuntimeException('File index.html (atau home.html) tidak ditemukan di dalam ZIP.');
        }

        $indexRelative = ltrim(str_replace('\\', '/', substr($indexPath, strlen($destination))), '/');

        return [
            'root'                => $root,
            'index_path'          => $indexPath,
            'index_relative_path' => $indexRelative,
        ];
    }

    /**
     * Hapus file dan folder junk dari direktori yang telah diekstrak
     */
    private function sanitizeFolder(string $path): void
    {
        if (!File::isDirectory($path)) {
            return;
        }

        $items = File::files($path);
        $dirs = File::directories($path);

        // Hapus file junk di root
        foreach ($items as $file) {
            if (in_array($file->getFilename(), $this->junkItems, true)) {
                File::delete($file->getPathname());
            }
        }

        // Hapus folder junk
        foreach ($dirs as $dir) {
            $dirName = basename($dir);
            if (in_array($dirName, $this->junkItems, true)) {
                File::deleteDirectory($dir);
            } else {
                // Rekursif untuk membersihkan sub-folder (misal: assets/.git)
                $this->sanitizeFolder($dir);
            }
        }
    }

    private function detectTopLevelFolder(ZipArchive $zip): ?string
    {
        $topLevel = null;
        $hasRootFile = false;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = str_replace('\\', '/', $zip->getNameIndex($i));

            // Skip folder entry
            if (str_ends_with($name, '/')) continue;

            $parts = explode('/', $name);
            if (count($parts) === 1) {
                $hasRootFile = true;
                break;
            }

            $first = $parts[0];
            if ($topLevel === null) {
                $topLevel = $first;
            } elseif ($topLevel !== $first) {
                return null; // Multi top-level, tidak dibungkus 1 folder
            }
        }

        return $hasRootFile ? null : $topLevel;
    }

    private function findIndexHtml(string $root): ?string
    {
        $candidates = [
            $root . '/index.html',
            $root . '/index.htm',
            $root . '/home.html',
            $root . '/home.htm',
        ];

        foreach ($candidates as $path) {
            if (File::exists($path)) return $path;
        }

        // Fallback: Recursive search jika index.html ada di subfolder
        foreach (File::allFiles($root) as $file) {
            if (in_array(strtolower($file->getFilename()), ['index.html', 'index.htm'])) {
                return $file->getPathname();
            }
        }

        return null;
    }
}
