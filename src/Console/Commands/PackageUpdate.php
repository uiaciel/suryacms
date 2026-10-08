<?php

namespace Uiaciel\SuryaCms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PackageUpdate extends Command
{
    protected $signature = 'suryacms:package-update
                            {zip : Path ke file archive (tar.gz atau zip)}
                            {--backup : Buat backup sebelum update (dengan checksum)}
                            {--backup-only : Hanya buat backup tanpa melakukan update}
                            {--migrate : Jalankan migrasi database setelah update}
                            {--force : Skip konfirmasi}';

    protected $description = 'Extract dan update vendor, composer.json, composer.lock (Super Fast Native CLI)';

    private $backupPath;

    private $basePath;

    private $tempPath;

    private $isTar;

    private $isWindows;

    public function handle()
    {
        $zipPath = $this->argument('zip');
        $this->basePath = base_path();
        $this->isWindows = PHP_OS_FAMILY === 'Windows';

        // Normalisasi path untuk Windows
        if ($this->isWindows) {
            $zipPath = str_replace('/', '\\', $zipPath);
            $this->info('Server adalah Windows.');
        }

        // Mode backup-only
        if ($this->option('backup-only')) {
            $this->info('📦 Mode backup-only aktif');
            $this->createBackup();
            $this->info('✅ Backup selesai. File tidak diupdate.');

            return 0;
        }

        // Validasi file archive
        if (! file_exists($zipPath)) {
            $this->error("File archive tidak ditemukan: {$zipPath}");

            return 1;
        }

        // Verifikasi checksum jika ada
        $md5Path = $zipPath.'.md5';
        if (file_exists($md5Path)) {
            $expectedChecksum = trim(file_get_contents($md5Path));
            $actualChecksum = md5_file($zipPath);

            if ($expectedChecksum !== $actualChecksum) {
                $this->error('❌ Checksum tidak cocok! File mungkin korup.');
                $this->line("  Expected: {$expectedChecksum}");
                $this->line("  Actual:   {$actualChecksum}");

                return 1;
            }
            $this->info('✅ Checksum verified');

        }

        // Konfirmasi
        if (! $this->option('force')) {
            if (! $this->confirm('Apakah Anda yakin ingin mengupdate packages?')) {
                $this->info('Dibatalkan.');

                return 0;
            }
        }

        // Aktifkan maintenance mode
        $this->info('🔧 Mengaktifkan maintenance mode...');
        Artisan::call('down', ['--retry' => 60]);

        try {
            // 1. Backup jika diminta
            if ($this->option('backup')) {
                $this->createBackup();
            }

            // 2. Extract ke temporary folder
            $this->extractArchive($zipPath);

            // 3. Pindahkan file dari temp ke base path
            $this->replaceFiles();

            // 4. Clear cache
            $this->info('⚡ Clearing cache...');
            Artisan::call('optimize:clear');
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');

            // 5. Migrate jika diminta
            if ($this->option('migrate')) {
                $this->info('🗄️ Menjalankan migrasi...');
                Artisan::call('migrate', ['--force' => true]);
            }

            // 6. Matikan maintenance mode
            $this->info('🟢 Menonaktifkan maintenance mode...');
            Artisan::call('up');

            $this->info('✅ Update berhasil!');

            // Hapus file archive input jika sudah selesai
            if ($this->confirm('Hapus file archive input sekarang?', false)) {
                unlink($zipPath);
                if (file_exists($md5Path)) {
                    unlink($md5Path);
                }
                $this->info('🗑️ File archive input dihapus');
            }

            return 0;

        } catch (\Exception $e) {
            // Rollback jika ada error
            $this->error('❌ Error: '.$e->getMessage());

            if ($this->option('backup') && $this->backupPath && file_exists($this->backupPath)) {
                $this->info('🔄 Melakukan rollback...');
                $this->rollback();
            }

            Artisan::call('up');

            return 1;
        }
    }

    /**
     * Membuat backup menggunakan native CLI tercepat
     */
    private function createBackup()
    {
        $this->backupPath = storage_path('app/backups/package_backup_'.date('Ymd_His'));
        mkdir($this->backupPath, 0755, true);

        $this->info('📦 Membuat backup...');

        $backupArchive = $this->backupPath.DIRECTORY_SEPARATOR.'backup.tar.gz';
        $isTar = str_ends_with($backupArchive, '.tar.gz') || str_ends_with($backupArchive, '.tgz');

        $targets = ['composer.json', 'composer.lock', 'vendor'];
        $existingTargets = [];

        foreach ($targets as $target) {
            $fullPath = $this->basePath.DIRECTORY_SEPARATOR.$target;
            if (file_exists($fullPath)) {
                $existingTargets[] = $target;
            }
        }

        if (empty($existingTargets)) {
            $this->warn('Tidak ada file yang perlu di-backup');

            return;
        }

        $targetsStr = implode(' ', $existingTargets);
        $basePathEsc = escapeshellarg($this->basePath);
        $backupArchiveEsc = escapeshellarg($backupArchive);

        // === LOGIKA SUPER CEPAT SESUAI REQUEST ===
        if ($this->isWindows) {
            if ($isTar) {
                // Windows 10/11 built-in tar. Sangat cepat & aman dari long-path issue.
                $command = "cd /d {$basePathEsc} && tar -czf {$backupArchiveEsc} {$targetsStr}";
            } else {
                // Jika tetap ingin .zip di Windows, WAJIB pakai 7-Zip.
                $sevenZip = '"C:\Program Files\7-Zip\7z.exe"';
                $command = "cd /d {$basePathEsc} && {$sevenZip} a -tzip {$backupArchiveEsc} {$targetsStr}";
            }
        } else {
            // === LOKAL: MAC / LINUX ===
            if ($isTar) {
                $command = "cd {$basePathEsc} && tar -czf {$backupArchiveEsc} {$targetsStr}";
            } else {
                $command = "cd {$basePathEsc} && zip -r -q {$backupArchiveEsc} {$targetsStr}";
            }
        }

        $returnVar = 0;
        $output = [];
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('Gagal membuat backup: '.implode("\n", $output));
        }

        // Generate checksum
        $this->info('🔐 Generating checksum...');
        $checksum = md5_file($backupArchive);
        file_put_contents($backupArchive.'.md5', $checksum);

        $fileSize = number_format(filesize($backupArchive) / 1024 / 1024, 2);
        $this->info("✅ Backup dibuat: {$backupArchive} ({$fileSize} MB)");
        $this->info("🔐 Checksum (MD5): {$checksum}");
    }

    /**
     * Extract archive menggunakan native CLI tercepat
     */
    private function extractArchive($zipPath)
    {
        $this->info('📂 Extracting menggunakan native CLI...');
        $this->tempPath = storage_path('app/temp_package_update_'.time());
        $this->info('Temporary Folder'.$this->tempPath);
        mkdir($this->tempPath, 0755, true);

        $this->isTar = str_ends_with($zipPath, '.tar.gz') || str_ends_with($zipPath, '.tgz');
        $zipPathEsc = escapeshellarg($zipPath);
        $tempPathEsc = escapeshellarg($this->tempPath);
        $command = '';

        if ($this->isWindows) {
            if ($this->isTar) {
                // Windows 10/11 built-in tar
                $command = "tar -xzf {$zipPathEsc} -C {$tempPathEsc}";
            } else {
                // Prioritaskan 7-Zip untuk .zip di Windows (jauh lebih cepat dari PowerShell/tar)
                $sevenZip = '"C:\Program Files\7-Zip\7z.exe"';
                if (file_exists('C:\Program Files\7-Zip\7z.exe')) {
                    // 7z x = extract with full paths, -y = yes to all
                    $command = "{$sevenZip} x -y -o{$tempPathEsc} {$zipPathEsc}";
                } else {
                    // Fallback ke tar built-in Windows 10+
                    $command = "tar -xf {$zipPathEsc} -C {$tempPathEsc}";
                }
            }
        } else {
            if ($this->isTar) {
                $command = "tar -xzf {$zipPathEsc} -C {$tempPathEsc}";
            } else {
                $command = "unzip -o -q {$zipPathEsc} -d {$tempPathEsc}";
            }
        }

        $returnVar = 0;
        $output = [];
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('Gagal extract archive: '.implode("\n", $output));
        }
    }

    /**
     * Replace file lama dengan yang baru (Atomic & Cepat)
     */
    private function replaceFiles()
    {
        $this->info('🔄 Mengganti file lama...');

        // 1. Ganti composer.json dan composer.lock
        $filesToReplace = ['composer.json', 'composer.lock'];
        foreach ($filesToReplace as $file) {
            $source = $this->tempPath.DIRECTORY_SEPARATOR.$file;
            $dest = $this->basePath.DIRECTORY_SEPARATOR.$file;

            if (file_exists($source)) {
                copy($source, $dest);
                if (! $this->isWindows) {
                    chmod($dest, 0644);
                }
                $this->line("  ✓ Replaced {$file}");
            }
        }

        // 2. Ganti folder vendor
        $sourceVendor = $this->tempPath.DIRECTORY_SEPARATOR.'vendor';
        $destVendor = $this->basePath.DIRECTORY_SEPARATOR.'vendor';

        if (file_exists($sourceVendor)) {
            // Hapus vendor lama (Native CLI jauh lebih cepat dari PHP File::deleteDirectory)
            if (file_exists($destVendor)) {
                $this->info('🗑️ Menghapus vendor lama...');
                $this->deleteDirectory($destVendor);
            }

            // Pindahkan vendor baru (Atomic operation)
            $this->info('📦 Memindahkan vendor baru...');
            $this->moveDirectory($sourceVendor, $destVendor);

            // Fix permissions HANYA jika bukan .tar.gz (karena tar sudah preserve permission)
            if (! $this->isWindows && ! $this->isTar) {
                $this->info('🔒 Memperbaiki permissions...');
                $this->fixPermissions($destVendor);
            }
        }

        // 3. Cleanup temp folder
        $this->deleteDirectory($this->tempPath);
    }

    /**
     * Rollback ke kondisi sebelum update
     */
    private function rollback()
    {
        try {
            $this->info('🔄 Restoring from backup...');

            $backupArchive = $this->backupPath.DIRECTORY_SEPARATOR.'backup.tar.gz';
            $backupMd5 = $backupArchive.'.md5';

            if (! file_exists($backupArchive)) {
                throw new \Exception('Backup file tidak ditemukan');
            }

            // Verifikasi checksum backup jika ada
            if (file_exists($backupMd5)) {
                $expectedChecksum = trim(file_get_contents($backupMd5));
                $actualChecksum = md5_file($backupArchive);

                if ($expectedChecksum !== $actualChecksum) {
                    throw new \Exception('Backup file korup! Checksum tidak cocok.');
                }
                $this->info('✅ Backup checksum verified');
            }

            // Hapus file yang rusak
            $destVendor = $this->basePath.DIRECTORY_SEPARATOR.'vendor';
            if (file_exists($destVendor)) {
                $this->deleteDirectory($destVendor);
            }

            foreach (['composer.json', 'composer.lock'] as $file) {
                $filePath = $this->basePath.DIRECTORY_SEPARATOR.$file;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Extract backup (selalu tar.gz)
            $basePathEsc = escapeshellarg($this->basePath);
            $backupArchiveEsc = escapeshellarg($backupArchive);

            if ($this->isWindows) {
                $command = "cd /d {$basePathEsc} && tar -xzf {$backupArchiveEsc}";
            } else {
                $command = "cd {$basePathEsc} && tar -xzf {$backupArchiveEsc}";
            }

            $returnVar = 0;
            $output = [];
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                throw new \Exception('Gagal restore backup: '.implode("\n", $output));
            }

            $this->info('✅ Rollback berhasil');
        } catch (\Exception $e) {

            $this->error('❌ Rollback gagal: '.$e->getMessage());
            $this->error("Backup location: {$this->backupPath}");
        }
    }

    /**
     * Hapus direktori menggunakan native CLI (Super Fast)
     */
    private function deleteDirectory($path)
    {
        if (! file_exists($path)) {
            return;
        }

        $pathEsc = escapeshellarg($path);
        if ($this->isWindows) {
            // Windows: rmdir /s /q (silent, force, recursive)
            $command = "rmdir /s /q {$pathEsc}";
        } else {
            // Linux/Mac: rm -rf
            $command = "rm -rf {$pathEsc}";
        }
        exec($command);
    }

    private function moveDirectory($source, $dest)
    {
        $sourceEsc = escapeshellarg($source);
        $destEsc = escapeshellarg($dest);

        if ($this->isWindows) {
            // Windows: robocopy dengan flag /MOVE (move files and dirs)
            // /E = subdirs, /R:1 /W:1 = retry 1x, /NFL /NDL /NJH /NJS = silent output
            $command = "robocopy {$sourceEsc} {$destEsc} /E /MOVE /R:1 /W:1 /NFL /NDL /NJH /NJS";
            exec($command, $output, $returnVar);

            // robocopy return code 0-7 means success. >7 means error.
            if ($returnVar > 7) {
                // Fallback ke move biasa jika robocopy gagal
                exec("move {$sourceEsc} {$destEsc}");
            }
        } else {
            // Linux/Mac: mv (instant atomic move)
            exec("mv {$sourceEsc} {$destEsc}");
        } // <-- Typo "“}" dihapus dan diganti dengan kurung kurawal yang benar
    }

    /**
     * Fix permissions (Hanya Linux/Mac)
     */
    private function fixPermissions($path)
    {
        if ($this->isWindows) {
            return;
        }

        $pathEsc = escapeshellarg($path);
        exec("find {$pathEsc} -type d -exec chmod 755 {} \\;");
        exec("find {$pathEsc} -type f -exec chmod 644 {} \\;");
    }
}
