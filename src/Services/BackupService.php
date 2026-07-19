<?php

namespace Uiaciel\SuryaCms\Services;

use ZipArchive;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Maatwebsite\Excel\Facades\Excel;

class BackupService
{
    protected string $stagingDir;
    protected string $backupPath;
    protected ?string $cacheKey = null;

    public function __construct()
    {
        $this->backupPath = storage_path('app/private/suryacms_backups');
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    public function setCacheKey(string $key): self
    {
        $this->cacheKey = $key;
        return $this;
    }

    protected function updateProgress(string $step, int $percentage): void
    {
        if ($this->cacheKey) {
            cache()->put($this->cacheKey, ['step' => $step, 'percentage' => $percentage], now()->addHours(2));
        }
    }

    public function runBackup(): string
    {
        try {
            $timestamp = date('m-d-y_H-i-s');
            
            $setting = \Uiaciel\SuryaCms\Models\Setting::first();
            $siteUrl = $setting && $setting->url ? preg_replace('/^https?:\/\//', '', $setting->url) : 'website';
            $siteUrl = str_replace('/', '-', $siteUrl);
            $zipFileName = $siteUrl . '_suryacms_backup_' . $timestamp . '.zip';

            $this->stagingDir = storage_path('app/private/temp_backup_' . $timestamp);
            
            if (!File::exists($this->stagingDir)) {
                File::makeDirectory($this->stagingDir, 0755, true);
            }

            $this->updateProgress('Preparing backup environment...', 5);

            // 1. Export Database
            $this->exportDatabase();

            // 2. Copy Files
            $this->copyFiles();

            // 3. Create Metadata
            $this->createMetadata($timestamp);

            // 4. Zip Files
            $zipFile = $this->zipBackup($zipFileName);

            // 5. Cleanup
            File::deleteDirectory($this->stagingDir);
            
            $this->updateProgress('Backup completed successfully.', 100);

            return $zipFile;

        } catch (Throwable $e) {
            $this->updateProgress('Backup failed: ' . $e->getMessage(), -1);
            if (isset($this->stagingDir) && File::exists($this->stagingDir)) {
                File::deleteDirectory($this->stagingDir);
            }
            throw $e;
        }
    }

    protected function exportDatabase(): void
    {
        $this->updateProgress('Exporting database...', 10);
        $dbStaging = $this->stagingDir . '/database';
        File::makeDirectory($dbStaging, 0755, true);

        // Get all tables from config
        $tables = config('suryacms-backup.tables', []);
        
        if (empty($tables)) {
            // Fallback to all tables if config is empty or missing
            $tables = Schema::getTableListing();
        }

        $totalTables = count($tables);
        $currentTable = 0;

        foreach ($tables as $table) {
            $this->updateProgress("Exporting table: {$table}", 10 + (int)(($currentTable / $totalTables) * 25));
            
            $filePath = $dbStaging . '/' . $table . '.json';
            $file = fopen($filePath, 'w');
            fwrite($file, '[');
            
            $first = true;
            $columns = Schema::getColumnListing($table);
            $orderByColumn = !empty($columns) ? $columns[0] : 'id';
            
            DB::table($table)->orderBy($orderByColumn)->chunk(500, function ($records) use ($file, &$first) {
                foreach ($records as $record) {
                    if (!$first) {
                        fwrite($file, ',');
                    }
                    fwrite($file, json_encode($record));
                    $first = false;
                }
            });
            
            fwrite($file, ']');
            fclose($file);
            $currentTable++;
        }
        
        $this->updateProgress('Exporting specific Excel files...', 35);
        $exportsStaging = $this->stagingDir . '/exports';
        File::makeDirectory($exportsStaging, 0755, true);

        $exports = [
            'PostExport.xlsx' => \Uiaciel\SuryaCms\Exports\PostExport::class,
            'PageExport.xlsx' => \Uiaciel\SuryaCms\Exports\PageExport::class,
            'MenuExport.xlsx' => \Uiaciel\SuryaCms\Exports\MenuExport::class,
            'InboxExport.xlsx' => \Uiaciel\SuryaCms\Exports\InboxExport::class,
            'GalleryExport.xlsx' => \Uiaciel\SuryaCms\Exports\GalleryExport::class,
            'SettingExport.xlsx' => \Uiaciel\SuryaCms\Exports\SettingExport::class,
        ];

        foreach ($exports as $filename => $exportClass) {
            if (class_exists($exportClass)) {
                $content = Excel::raw(new $exportClass, \Maatwebsite\Excel\Excel::XLSX);
                File::put($exportsStaging . '/' . $filename, $content);
            }
        }
        
        $this->updateProgress('Database export completed.', 40);
    }

    protected function copyFiles(): void
    {
        $this->updateProgress('Copying storage and assets...', 45);
        $filesStaging = $this->stagingDir . '/files';
        File::makeDirectory($filesStaging, 0755, true);

        $directoriesToCopy = [
            'storage/app/public' => $filesStaging . '/storage/app/public',
            'public/frontend' => $filesStaging . '/public/frontend',
            'resources/views/frontend' => $filesStaging . '/resources/views/frontend',
            'config' => $filesStaging . '/config',
        ];

        $totalDirs = count($directoriesToCopy);
        $currentDir = 0;

        foreach ($directoriesToCopy as $source => $dest) {
            $sourcePath = base_path($source);
            if (File::exists($sourcePath)) {
                File::copyDirectory($sourcePath, $dest);
            }
            $currentDir++;
            $this->updateProgress("Copying directory: {$source}", 45 + (int)(($currentDir / $totalDirs) * 20));
        }

        // Copy .env to root of zip
        $this->updateProgress('Copying environment configuration...', 68);
        if (File::exists(base_path('.env'))) {
            File::copy(base_path('.env'), $this->stagingDir . '/.env');
        }
    }

    protected function createMetadata(string $timestamp): void
    {
        $this->updateProgress('Creating metadata...', 70);
        $metadata = [
            'suryacms_version' => config('suryacms.version', '1.0.0'),
            'uiaciel_package' => true,
            'backup_timestamp' => $timestamp,
            'laravel_version' => app()->version(),
        ];

        File::put($this->stagingDir . '/info.json', json_encode($metadata, JSON_PRETTY_PRINT));
    }

    protected function zipBackup(string $zipFileName): string
    {
        $this->updateProgress('Zipping backup files...', 75);
        $zipFilePath = $this->backupPath . '/' . $zipFileName;

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = File::allFiles($this->stagingDir);
            $totalFiles = count($files);
            $currentFile = 0;

            foreach ($files as $file) {
                $relativePath = $file->getRelativePathname();
                $relativePath = str_replace('\\', '/', $relativePath); // Standardize for zip
                $zip->addFile($file->getRealPath(), $relativePath);
                
                $currentFile++;
                if ($currentFile % 50 === 0) {
                    $this->updateProgress("Zipping files...", 75 + (int)(($currentFile / $totalFiles) * 20));
                }
            }
            $zip->close();
        } else {
            throw new \Exception("Failed to create ZIP archive.");
        }

        return $zipFilePath;
    }
}
