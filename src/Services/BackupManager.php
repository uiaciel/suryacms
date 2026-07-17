<?php

namespace Uiaciel\SuryaCms\Services;

use Illuminate\Database\Connection;
use Illuminate\Database\QueryException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Uiaciel\SuryaCms\Models\Setting;

class BackupManager
{
    public static function backupDirectory(): string
    {
        $path = storage_path('app/backups');
        File::ensureDirectoryExists($path);

        return $path;
    }

    public static function statusDirectory(): string
    {
        $path = storage_path('app/backup-status');
        File::ensureDirectoryExists($path);

        return $path;
    }

    public static function tempDirectory(string $name = null): string
    {
        $dir = storage_path('app/temp/' . ($name ?? 'backup_' . Str::random(10)));
        File::deleteDirectory($dir);
        File::ensureDirectoryExists($dir);

        return $dir;
    }

    public static function getStatusFilePath(string $token): string
    {
        $path = self::statusDirectory() . DIRECTORY_SEPARATOR . $token . '.json';
        File::ensureDirectoryExists(dirname($path));

        return $path;
    }

    public static function isSupportedBackupFile(string $filePath): bool
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        return in_array($extension, ['zip', 'suryacms'], true);
    }

    public static function makeBackup(string $backupLabel = null, ?string $statusFile = null): string
    {
        if ($statusFile) {
            self::updateStatus($statusFile, 'started', 'Memulai proses backup...', 5);
        }

        $fileName = $backupLabel
            ? Str::slug($backupLabel) . '.suryacms'
            : 'suryacms-backup-' . now()->format('YmdHis') . '.suryacms';

        $backupPath = self::backupDirectory() . DIRECTORY_SEPARATOR . $fileName;
        $tempPath = self::tempDirectory(pathinfo($fileName, PATHINFO_FILENAME));

        // Copy important files and folders
        if (File::exists(base_path('.env'))) {
            File::copy(base_path('.env'), $tempPath . DIRECTORY_SEPARATOR . '.env');
            if ($statusFile) {
                self::updateStatus($statusFile, 'env_copied', 'Menyalin .env ke folder sementara...', 10);
            }
        }

        if ($statusFile) {
            self::updateStatus($statusFile, 'exporting_database', 'Mengekspor data database...', 20);
        }
        self::exportDatabaseData($tempPath . DIRECTORY_SEPARATOR . 'database.json');

        if ($statusFile) {
            self::updateStatus($statusFile, 'copying_files', 'Menyalin file storage dan tema...', 50);
        }
        self::copyDirectoryIfExists(storage_path('app/public'), $tempPath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'public');
        self::copyDirectoryIfExists(public_path('frontend'), $tempPath . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'frontend');
        self::copyDirectoryIfExists(base_path('resources/views/frontend'), $tempPath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'frontend');

        $info = self::buildBackupInfo($fileName, $tempPath);
        File::put($tempPath . DIRECTORY_SEPARATOR . 'info.json', json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        if ($statusFile) {
            self::updateStatus($statusFile, 'zipping', 'Membuat arsip backup...', 80);
        }

        self::zipDirectory($tempPath, $backupPath);
        File::deleteDirectory($tempPath);

        if ($statusFile) {
            self::updateStatus($statusFile, 'completed', 'Backup selesai.', 100, ['path' => $backupPath, 'file_name' => $fileName]);
        }

        return $backupPath;
    }

    public static function extractBackupInfo(string $backupFilePath): array
    {
        if (! File::exists($backupFilePath) || ! self::isSupportedBackupFile($backupFilePath)) {
            throw new \RuntimeException('Backup file not found or invalid format.');
        }

        $zip = new \ZipArchive();
        if ($zip->open($backupFilePath) !== true) {
            throw new \RuntimeException('Unable to open backup archive.');
        }

        $index = $zip->locateName('info.json');
        if ($index === false) {
            $zip->close();
            throw new \RuntimeException('Backup metadata info.json not found.');
        }

        $content = $zip->getFromIndex($index);
        $zip->close();

        $decoded = json_decode($content, true);
        if (! is_array($decoded)) {
            throw new \RuntimeException('Backup info.json could not be decoded.');
        }

        return $decoded;
    }

    public static function restoreFromBackup(string $backupFilePath, ?string $statusFile = null): array
    {
        if ($statusFile) {
            self::updateStatus($statusFile, 'started', 'Memulai proses restore...', 5);
        }

        if (! File::exists($backupFilePath) || ! self::isSupportedBackupFile($backupFilePath)) {
            throw new \RuntimeException('Backup file not found or invalid format.');
        }

        $tempPath = self::tempDirectory('restore_' . Str::random(10));

        $zip = new \ZipArchive();
        if ($zip->open($backupFilePath) !== true) {
            throw new \RuntimeException('Failed to open backup archive.');
        }

        if (! $zip->extractTo($tempPath)) {
            $zip->close();
            throw new \RuntimeException('Failed to extract backup archive.');
        }

        $zip->close();

        $info = self::readJsonFile($tempPath . DIRECTORY_SEPARATOR . 'info.json');
        if ($statusFile) {
            self::updateStatus($statusFile, 'validated', 'Validasi backup selesai.', 10, ['info' => $info]);
        }

        $snapshotPath = $tempPath . DIRECTORY_SEPARATOR . 'snapshot';
        self::prepareRestoreSnapshot($snapshotPath);

        try {
            if (File::exists($tempPath . DIRECTORY_SEPARATOR . '.env')) {
                if ($statusFile) {
                    self::updateStatus($statusFile, 'restoring_env', 'Restore .env sedang berjalan...', 15);
                }
                File::copy($tempPath . DIRECTORY_SEPARATOR . '.env', base_path('.env'));
            }

            if ($statusFile) {
                self::updateStatus($statusFile, 'restoring_database', 'Restore database sedang berjalan...', 25);
            }

            self::restoreDatabase($tempPath . DIRECTORY_SEPARATOR . 'database.json', $statusFile);

            if ($statusFile) {
                self::updateStatus($statusFile, 'restoring_files', 'Restore file storage dan tema...', 60);
            }

            self::copyDirectoryIfExists($tempPath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'public', storage_path('app/public'));
            self::copyDirectoryIfExists($tempPath . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'frontend', public_path('frontend'));
            self::copyDirectoryIfExists($tempPath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'frontend', base_path('resources/views/frontend'));

            if ($statusFile) {
                self::updateStatus($statusFile, 'completed', 'Restore selesai. Membersihkan file sementara...', 95);
            }

            self::cleanupRestoreSnapshot($snapshotPath);
            File::deleteDirectory($tempPath);

            if ($statusFile) {
                self::updateStatus($statusFile, 'completed', 'Restore berhasil diselesaikan.', 100, ['info' => $info]);
            }

            return [
                'status' => 'completed',
                'message' => 'Restore berhasil diselesaikan.',
                'info' => $info,
            ];
        } catch (\Throwable $e) {
            self::rollbackRestoreSnapshot($snapshotPath);
            File::deleteDirectory($tempPath);

            if ($statusFile) {
                self::updateStatus($statusFile, 'failed', 'Restore gagal: ' . $e->getMessage(), 0);
            }

            throw $e;
        }
    }

    protected static function exportDatabaseData(string $destinationPath): void
    {
        $tables = self::getDatabaseTables();
        $handle = fopen($destinationPath, 'w');

        if (! $handle) {
            throw new \RuntimeException('Unable to create database export file.');
        }

        fwrite($handle, "{\n  \"tables\": {\n");
        $tableCount = count($tables);
        $current = 0;

        foreach ($tables as $table) {
            $current++;
            fwrite($handle, '    "' . $table . '": [');

            $firstRowWritten = false;
            DB::table($table)->orderByRaw('1')->chunk(500, function ($rows) use ($handle, &$firstRowWritten) {
                foreach ($rows as $row) {
                    if ($firstRowWritten) {
                        fwrite($handle, ',');
                    }

                    fwrite($handle, '\n      ' . json_encode(json_decode(json_encode($row), true), JSON_UNESCAPED_UNICODE));
                    $firstRowWritten = true;
                }
            });

            fwrite($handle, $firstRowWritten ? '\n    ]' : ']' );
            if ($current < $tableCount) {
                fwrite($handle, ",\n");
            } else {
                fwrite($handle, "\n");
            }
        }

        fwrite($handle, "  }\n}\n");
        fclose($handle);
    }

    protected static function getDatabaseTables(): array
    {
        $driver = DB::getDriverName();

        switch ($driver) {
            case 'mysql':
            case 'pgsql':
                $query = match ($driver) {
                    'mysql' => 'SHOW TABLES',
                    'pgsql' => "SELECT tablename FROM pg_tables WHERE schemaname = 'public'",
                };
                $results = DB::select($query);
                break;
            case 'sqlite':
                $results = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                break;
            case 'sqlsrv':
                $results = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");
                break;
            default:
                throw new \RuntimeException('Unsupported database driver: ' . $driver);
        }

        $tables = [];
        foreach ($results as $row) {
            $values = array_values((array) $row);
            $tables[] = $values[0] ?? null;
        }

        $tables = array_filter($tables);
        $excluded = self::excludedTables();

        return array_values(array_diff($tables, $excluded));
    }

    protected static function excludedTables(): array
    {
        return [
            'migrations',
            'failed_jobs',
            'personal_access_tokens',
            'password_resets',
            'cache',
            'sessions',
        ];
    }

    protected static function restoreDatabase(string $databaseJsonPath, ?string $statusFile = null): void
    {
        if (! File::exists($databaseJsonPath)) {
            throw new \RuntimeException('Database export file not found in backup.');
        }

        $payload = json_decode(File::get($databaseJsonPath), true);
        if (! is_array($payload) || ! isset($payload['tables'])) {
            throw new \RuntimeException('Database export file is invalid or corrupted.');
        }

        $tables = $payload['tables'];
        if ($statusFile) {
            self::updateStatus($statusFile, 'restoring_database', 'Memasukkan data ke dalam database...', 35);
        }

        self::disableForeignKeyChecks();

        foreach ($tables as $table => $rows) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            try {
                DB::table($table)->truncate();
            } catch (QueryException $exception) {
                DB::statement('DELETE FROM ' . DB::getTablePrefix() . $table);
            }

            if (! empty($rows)) {
                foreach (array_chunk($rows, 200) as $chunk) {
                    DB::table($table)->insert($chunk);
                }
            }

            if ($statusFile) {
                self::updateStatus($statusFile, 'restoring_database', "Restore table {$table} selesai.", 35 + random_int(1, 5));
            }
        }

        self::enableForeignKeyChecks();
    }

    protected static function disableForeignKeyChecks(): void
    {
        $driver = DB::getDriverName();

        match ($driver) {
            'mysql' => DB::statement('SET FOREIGN_KEY_CHECKS=0;'),
            'pgsql' => DB::statement('SET CONSTRAINTS ALL DEFERRED;'),
            'sqlite' => DB::statement('PRAGMA foreign_keys = OFF;'),
            'sqlsrv' => DB::statement('EXEC sp_MSforeachtable "ALTER TABLE ? NOCHECK CONSTRAINT all";'),
            default => null,
        };
    }

    protected static function enableForeignKeyChecks(): void
    {
        $driver = DB::getDriverName();

        match ($driver) {
            'mysql' => DB::statement('SET FOREIGN_KEY_CHECKS=1;'),
            'pgsql' => DB::statement('SET CONSTRAINTS ALL IMMEDIATE;'),
            'sqlite' => DB::statement('PRAGMA foreign_keys = ON;'),
            'sqlsrv' => DB::statement('EXEC sp_MSforeachtable "ALTER TABLE ? WITH CHECK CHECK CONSTRAINT all";'),
            default => null,
        };
    }

    protected static function buildBackupInfo(string $fileName, string $tempPath): array
    {
        return [
            'backup_id' => Str::uuid()->toString(),
            'backup_name' => $fileName,
            'created_at' => now()->toDateTimeString(),
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'laravel_version' => app()->version(),
            'active_theme' => Setting::query()->value('active_theme'),
            'database_tables' => self::getDatabaseTables(),
            'env_included' => File::exists(base_path('.env')),
            'include_paths' => [
                'database.json',
                '.env',
                'storage/public',
                'public/frontend',
                'resources/views/frontend',
            ],
            'driver' => DB::getDriverName(),
            'items' => self::countBackupItems($tempPath),
            'backup_file_hash' => sha1($fileName . now()->timestamp),
        ];
    }

    protected static function countBackupItems(string $path): array
    {
        if (! File::exists($path)) {
            return [];
        }

        return [
            'files' => count(File::allFiles($path)),
            'folders' => count(File::allDirectories($path)),
        ];
    }

    protected static function zipDirectory(string $source, string $destination): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($destination, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Unable to create backup archive.');
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($source) + 1);
            $zip->addFile($filePath, $relativePath);
        }

        $zip->close();
    }

    protected static function readJsonFile(string $path): array
    {
        if (! File::exists($path)) {
            throw new \RuntimeException('JSON file not found: ' . $path);
        }

        $decoded = json_decode(File::get($path), true);
        if (! is_array($decoded)) {
            throw new \RuntimeException('JSON file invalid: ' . $path);
        }

        return $decoded;
    }

    protected static function copyDirectoryIfExists(string $source, string $destination): void
    {
        if (! File::isDirectory($source)) {
            return;
        }

        if (File::isDirectory($destination)) {
            File::deleteDirectory($destination);
        }

        File::ensureDirectoryExists(dirname($destination));
        File::copyDirectory($source, $destination);
    }

    protected static function prepareRestoreSnapshot(string $snapshotPath): void
    {
        if (File::isDirectory($snapshotPath)) {
            File::deleteDirectory($snapshotPath);
        }

        File::ensureDirectoryExists($snapshotPath);

        if (File::isDirectory(base_path('resources/views/frontend'))) {
            File::copyDirectory(base_path('resources/views/frontend'), $snapshotPath . DIRECTORY_SEPARATOR . 'resources/views/frontend');
        }

        if (File::isDirectory(public_path('frontend'))) {
            File::ensureDirectoryExists($snapshotPath . DIRECTORY_SEPARATOR . 'public/frontend');
            File::copyDirectory(public_path('frontend'), $snapshotPath . DIRECTORY_SEPARATOR . 'public/frontend');
        }

        if (File::isDirectory(storage_path('app/public'))) {
            File::ensureDirectoryExists($snapshotPath . DIRECTORY_SEPARATOR . 'storage/app/public');
            File::copyDirectory(storage_path('app/public'), $snapshotPath . DIRECTORY_SEPARATOR . 'storage/app/public');
        }

        if (File::exists(base_path('.env'))) {
            File::copy(base_path('.env'), $snapshotPath . DIRECTORY_SEPARATOR . 'env_backup');
        }
    }

    protected static function rollbackRestoreSnapshot(string $snapshotPath): void
    {
        if (! File::isDirectory($snapshotPath)) {
            return;
        }

        if (File::isDirectory(base_path('resources/views/frontend'))) {
            File::deleteDirectory(base_path('resources/views/frontend'));
        }
        if (File::isDirectory($snapshotPath . DIRECTORY_SEPARATOR . 'resources/views/frontend')) {
            File::copyDirectory($snapshotPath . DIRECTORY_SEPARATOR . 'resources/views/frontend', base_path('resources/views/frontend'));
        }

        if (File::isDirectory(public_path('frontend'))) {
            File::deleteDirectory(public_path('frontend'));
        }
        if (File::isDirectory($snapshotPath . DIRECTORY_SEPARATOR . 'public/frontend')) {
            File::copyDirectory($snapshotPath . DIRECTORY_SEPARATOR . 'public/frontend', public_path('frontend'));
        }

        if (File::isDirectory(storage_path('app/public'))) {
            File::deleteDirectory(storage_path('app/public'));
        }
        if (File::isDirectory($snapshotPath . DIRECTORY_SEPARATOR . 'storage/app/public')) {
            File::copyDirectory($snapshotPath . DIRECTORY_SEPARATOR . 'storage/app/public', storage_path('app/public'));
        }

        if (File::exists($snapshotPath . DIRECTORY_SEPARATOR . 'env_backup')) {
            File::copy($snapshotPath . DIRECTORY_SEPARATOR . 'env_backup', base_path('.env'));
        }
    }

    protected static function cleanupRestoreSnapshot(string $snapshotPath): void
    {
        if (File::isDirectory($snapshotPath)) {
            File::deleteDirectory($snapshotPath);
        }
    }

    public static function updateStatus(string $statusFile, string $status, string $message, int $progress, array $payload = []): void
    {
        $data = array_merge([
            'status' => $status,
            'message' => $message,
            'progress' => $progress,
            'updated_at' => now()->toDateTimeString(),
        ], $payload);

        File::put($statusFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
