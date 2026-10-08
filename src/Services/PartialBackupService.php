<?php

namespace Uiaciel\SuryaCms\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;
use Uiaciel\SuryaCms\Exports\GalleryExport;
use Uiaciel\SuryaCms\Exports\InboxExport;
use Uiaciel\SuryaCms\Exports\MenuExport;
use Uiaciel\SuryaCms\Exports\PageExport;
use Uiaciel\SuryaCms\Exports\PostExport;
use Uiaciel\SuryaCms\Exports\SettingExport;
use ZipArchive;

class PartialBackupService
{
    protected string $backupRoot;

    public function __construct()
    {
        $this->backupRoot = storage_path('app/private/backups');
        File::ensureDirectoryExists($this->backupRoot);
    }

    public function run(): string
    {
        $folder = 'backup-'.now()->format('d-m-Y');
        $folderPath = $this->backupRoot.DIRECTORY_SEPARATOR.$folder;
        $stagingPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'suryacms-partial-'.uniqid();

        File::ensureDirectoryExists($folderPath);
        File::deleteDirectory($stagingPath);
        File::ensureDirectoryExists($stagingPath);

        try {
            $this->status('Preparing partial backup...', 5);
            $this->zipPaths($stagingPath.DIRECTORY_SEPARATOR.'vendor.zip', [
                [base_path('vendor'), 'vendor'],
                [base_path('composer.json'), 'composer.json'],
                [base_path('composer.lock'), 'composer.lock'],
            ]);

            $this->status('Backing up storage...', 25);
            $this->zipPaths($stagingPath.DIRECTORY_SEPARATOR.'storage.zip', [
                [storage_path(), 'storage'],
            ], [$this->backupRoot, $stagingPath]);

            $this->status('Backing up core files...', 45);
            $coreEntries = [];
            foreach (['app', 'bootstrap', 'config', 'database', 'public', 'resources', 'routes', 'tests', 'artisan', '.env', '.env.example'] as $path) {
                $source = base_path($path);
                if (File::exists($source)) {
                    $coreEntries[] = [$source, $path];
                }
            }
            $this->zipPaths($stagingPath.DIRECTORY_SEPARATOR.'core.zip', $coreEntries);

            $this->status('Exporting database...', 65);
            $this->createDatabaseArchive($stagingPath.DIRECTORY_SEPARATOR.'database.zip');

            foreach (['vendor.zip', 'storage.zip', 'core.zip', 'database.zip'] as $file) {
                File::copy($stagingPath.DIRECTORY_SEPARATOR.$file, $folderPath.DIRECTORY_SEPARATOR.$file);
            }

            $this->status('Partial backup completed.', 100);

            return $folderPath;
        } catch (Throwable $exception) {
            $this->status('Partial backup failed: '.$exception->getMessage(), -1);
            throw $exception;
        } finally {
            File::deleteDirectory($stagingPath);
        }
    }

    protected function createDatabaseArchive(string $archivePath): void
    {
        $stagingPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'suryacms-database-'.uniqid();
        File::ensureDirectoryExists($stagingPath.DIRECTORY_SEPARATOR.'json');
        File::ensureDirectoryExists($stagingPath.DIRECTORY_SEPARATOR.'exports');
        File::ensureDirectoryExists($stagingPath.DIRECTORY_SEPARATOR.'mysql');

        try {
            $tables = config('suryacms-backup.tables', []);
            if (empty($tables)) {
                $tables = Schema::getTableListing();
            }

            foreach ($tables as $table) {
                if (! Schema::hasTable($table)) {
                    continue;
                }
                $records = DB::table($table)->get()->map(fn ($record) => (array) $record)->all();
                File::put($stagingPath.DIRECTORY_SEPARATOR.'json'.DIRECTORY_SEPARATOR.$table.'.json', json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            }

            $exports = [
                'PostExport.xlsx' => PostExport::class,
                'PageExport.xlsx' => PageExport::class,
                'MenuExport.xlsx' => MenuExport::class,
                'InboxExport.xlsx' => InboxExport::class,
                'GalleryExport.xlsx' => GalleryExport::class,
                'SettingExport.xlsx' => SettingExport::class,
            ];
            foreach ($exports as $filename => $exportClass) {
                if (class_exists($exportClass)) {
                    File::put($stagingPath.DIRECTORY_SEPARATOR.'exports'.DIRECTORY_SEPARATOR.$filename, Excel::raw(new $exportClass, \Maatwebsite\Excel\Excel::XLSX));
                }
            }

            $this->createSqlDump($stagingPath.DIRECTORY_SEPARATOR.'mysql'.DIRECTORY_SEPARATOR.'database.sql', $tables);

            $zip = new ZipArchive;
            if ($zip->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('Unable to create database archive.');
            }
            $this->addDirectoryToZip($zip, $stagingPath, 'database');
            if (! $zip->close()) {
                throw new \RuntimeException('Unable to finalize database archive.');
            }
        } finally {
            File::deleteDirectory($stagingPath);
        }
    }

    protected function createSqlDump(string $dumpPath, array $tables): void
    {
        $lines = ['-- SuryaCMS database export', '-- Generated at '.now()->toDateTimeString(), ''];

        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $lines[] = '-- Table: '.$table;
            foreach (DB::table($table)->get() as $record) {
                $values = [];
                foreach ((array) $record as $value) {
                    if ($value === null) {
                        $values[] = 'NULL';
                    } elseif (is_bool($value)) {
                        $values[] = $value ? '1' : '0';
                    } elseif (is_numeric($value)) {
                        $values[] = (string) $value;
                    } else {
                        $values[] = "'".str_replace("'", "''", (string) $value)."'";
                    }
                }
                $lines[] = 'INSERT INTO `'.str_replace('`', '``', $table).'` VALUES ('.implode(', ', $values).');';
            }
            $lines[] = '';
        }

        File::put($dumpPath, implode(PHP_EOL, $lines));
    }

    protected function zipPaths(string $archivePath, array $entries, array $excludedPaths = []): void
    {
        $zip = new ZipArchive;
        if ($zip->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Unable to create backup archive.');
        }
        foreach ($entries as [$source, $prefix]) {
            if (File::isDirectory($source)) {
                $this->addDirectoryToZip($zip, $source, $prefix, $excludedPaths);
            } elseif (File::isFile($source)) {
                $zipPath = str_replace('\\', '/', $prefix);
                if (! $zip->addFile($source, $zipPath, 0, 0, ZipArchive::FL_OPEN_FILE_NOW)) {
                    $contents = file_get_contents($source);
                    if ($contents === false || ! $zip->addFromString($zipPath, $contents)) {
                        throw new \RuntimeException('Unable to add file to backup archive: '.$zipPath);
                    }
                }
            }
        }
        if (! $zip->close()) {
            throw new \RuntimeException('Unable to finalize backup archive: '.basename($archivePath));
        }
    }

    protected function addDirectoryToZip(ZipArchive $zip, string $directory, string $prefix, array $excludedPaths = []): void
    {
        foreach (File::allFiles($directory) as $file) {
            $realPath = $file->getRealPath();
            if (! $realPath || ! is_file($realPath) || ! is_readable($realPath)) {
                continue;
            }
            foreach ($excludedPaths as $excludedPath) {
                if (str_starts_with($realPath, rtrim($excludedPath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR)) {
                    continue 2;
                }
            }
            $relativePath = str_replace('\\', '/', $file->getRelativePathname());
            $zipPath = trim($prefix.'/'.$relativePath, '/');
            if (! $zip->addFile($realPath, $zipPath, 0, 0, ZipArchive::FL_OPEN_FILE_NOW)) {
                $contents = file_get_contents($realPath);
                if ($contents === false || ! $zip->addFromString($zipPath, $contents)) {
                    throw new \RuntimeException('Unable to add file to backup archive: '.$zipPath);
                }
            }
        }
    }

    protected function status(string $step, int $percentage): void
    {
        cache()->put('suryacms_partial_backup_status', ['step' => $step, 'percentage' => $percentage], now()->addHours(2));
    }
}
