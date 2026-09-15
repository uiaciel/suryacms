<?php

namespace Uiaciel\SuryaCms\Console\Commands;

use Illuminate\Console\Command;
use ZipArchive;
use Illuminate\Support\Facades\File;

class PackageZip extends Command
{
    protected $signature = 'suryacms:package-zip {output=vendor_update.zip}';
    protected $description = 'Zip vendor, composer.json, dan composer.lock untuk deploy ke server';

    public function handle()
    {
        $output = $this->argument('output');
        $basePath = base_path();

        // Pastikan output berekstensi .tar.gz (Sangat disarankan) atau .zip
        $isTar = str_ends_with($output, '.tar.gz');

        $this->info('📦 Membuat package archive menggunakan native CLI...');

        // Daftar file/folder yang di-archive
        $targets = 'vendor composer.json composer.lock';

        // Escape path untuk keamanan
        $outputPath = escapeshellarg($basePath . '/' . $output);
        $basePathEsc = escapeshellarg($basePath);

        $command = '';

        if (PHP_OS_FAMILY === 'Windows') {
            // === LOKAL: WINDOWS ===
            if ($isTar) {
                // Windows 10/11 sudah punya tar built-in. Sangat cepat & aman dari long-path issue.
                $command = "cd /d {$basePathEsc} && tar -czf {$outputPath} {$targets}";
            } else {
                // Jika tetap ingin .zip di Windows, WAJIB pakai 7-Zip. PowerShell terlalu lambat.
                $sevenZip = '"C:\Program Files\7-Zip\7z.exe"';
                $command = "cd /d {$basePathEsc} && {$sevenZip} a -tzip {$outputPath} {$targets}";
            }
        } else {
            // === LOKAL: MAC / LINUX ===
            if ($isTar) {
                $command = "cd {$basePathEsc} && tar -czf {$outputPath} {$targets}";
            } else {
                $command = "cd {$basePathEsc} && zip -r -q {$outputPath} {$targets}";
            }
        }

        $this->line("Menjalankan: {$command}");

        // Eksekusi command
        $returnVar = 0;
        $outputLog = [];
        exec($command, $outputLog, $returnVar);

        if ($returnVar !== 0) {
            $this->error("❌ Gagal membuat archive. Pastikan 'tar' atau 'zip/7z' terinstall.");
            return 1;
        }

        // Generate checksum
        $checksum = md5_file($basePath . '/' . $output);
        file_put_contents($basePath . '/' . $output . '.md5', $checksum);

        $fileSize = number_format(filesize($basePath . '/' . $output) / 1024 / 1024, 2);
        $this->info("✅ Berhasil membuat {$output} ({$fileSize} MB)");
        $this->info("🔐 Checksum (MD5): {$checksum}");

        return 0;
    }

    private function addFolderToZip($zip, $folderPath, $zipPath)
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($folderPath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $zipPath . '/' . substr($filePath, strlen($folderPath) + 1);

                // Set external attributes untuk permission Linux (644 untuk file)
                $zip->addFile($filePath, $relativePath);
                $zip->setExternalAttributesName($relativePath, \ZipArchive::OPSYS_UNIX, 0100644 << 16);
            }
        }
    }
}
