<?php

namespace Uiaciel\SuryaCms\Services;

use ZipArchive;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class RestoreService
{
    protected string $stagingDir;
    protected ?string $cacheKey = null;

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

    public function runRestore(string $zipFilePath): void
    {
        try {
            if (!File::exists($zipFilePath)) {
                throw new \Exception("Backup file not found.");
            }

            $timestamp = date('Y-m-d_H-i-s');
            $this->stagingDir = storage_path('app/private/temp_restore_' . $timestamp);
            
            if (!File::exists($this->stagingDir)) {
                File::makeDirectory($this->stagingDir, 0755, true);
            }

            $this->updateProgress('Extracting zip file...', 5);
            $this->extractZip($zipFilePath);

            $this->updateProgress('Verifying metadata...', 20);
            $this->verifyMetadata();

            $this->updateProgress('Restoring database...', 25);
            $this->restoreDatabase();

            $this->updateProgress('Restoring files and assets...', 70);
            $this->restoreFiles();

            $this->updateProgress('Cleaning up and optimizing...', 90);
            File::deleteDirectory($this->stagingDir);
            
            // Clear caches after restore
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');

            $this->updateProgress('Restore completed successfully.', 100);

        } catch (Throwable $e) {
            $this->updateProgress('Restore failed: ' . $e->getMessage(), -1);
            if (isset($this->stagingDir) && File::exists($this->stagingDir)) {
                File::deleteDirectory($this->stagingDir);
            }
            throw $e;
        }
    }

    protected function extractZip(string $zipFilePath): void
    {
        $zip = new ZipArchive();
        if ($zip->open($zipFilePath) === true) {
            $zip->extractTo($this->stagingDir);
            $zip->close();
        } else {
            throw new \Exception("Failed to open ZIP archive.");
        }
    }

    protected function verifyMetadata(): void
    {
        $infoPath = $this->stagingDir . '/info.json';
        if (!File::exists($infoPath)) {
            throw new \Exception("Invalid backup file: info.json missing.");
        }

        $metadata = json_decode(File::get($infoPath), true);
        if (!isset($metadata['uiaciel_package']) || $metadata['uiaciel_package'] !== true) {
            throw new \Exception("Invalid backup file: not a SuryaCMS backup.");
        }
    }

    protected function restoreDatabase(): void
    {
        $dbStaging = $this->stagingDir . '/database';
        if (!File::exists($dbStaging)) {
            return;
        }

        $files = File::files($dbStaging);
        $totalFiles = count($files);
        $currentFile = 0;

        // Disable foreign key checks during restore
        Schema::disableForeignKeyConstraints();

        $allowedTables = config('suryacms-backup.tables', []);
        $restoreAll = empty($allowedTables);

        foreach ($files as $file) {
            if ($file->getExtension() === 'json') {
                $tableName = $file->getFilenameWithoutExtension();
                
                // Skip if table is not in config (unless config is empty)
                if (!$restoreAll && !in_array($tableName, $allowedTables)) {
                    continue;
                }

                $this->updateProgress("Restoring table: {$tableName}", 25 + (int)(($currentFile / $totalFiles) * 40));
                
                // Clear existing table data
                if (Schema::hasTable($tableName)) {
                    DB::table($tableName)->truncate();
                } else {
                    // Note: This assumes tables exist. If restoring to a truly fresh laravel,
                    // you must run migrations first. The user said: "sehingga saya bisa merestore ke laravel+suryacms fresh"
                    // Fresh installation means SuryaCMS migrations should already be run.
                }

                $data = json_decode(File::get($file->getRealPath()), true);
                
                if (is_array($data) && count($data) > 0) {
                    // Insert in chunks to avoid memory limits
                    $chunks = array_chunk($data, 500);
                    foreach ($chunks as $chunk) {
                        DB::table($tableName)->insert($chunk);
                    }
                }
            }
            $currentFile++;
        }

        Schema::enableForeignKeyConstraints();
    }

    protected function restoreFiles(): void
    {
        $filesStaging = $this->stagingDir . '/files';
        if (!File::exists($filesStaging)) {
            return;
        }

        $directoriesToRestore = [
            $filesStaging . '/storage/app/public' => base_path('storage/app/public'),
            $filesStaging . '/public/frontend' => base_path('public/frontend'),
            $filesStaging . '/resources/views/frontend' => base_path('resources/views/frontend'),
            $filesStaging . '/config' => base_path('config'),
        ];

        $totalDirs = count($directoriesToRestore);
        $currentDir = 0;

        foreach ($directoriesToRestore as $source => $dest) {
            if (File::exists($source)) {
                if (!File::exists($dest)) {
                    File::makeDirectory($dest, 0755, true);
                }
                File::copyDirectory($source, $dest);
            }
            $currentDir++;
            $this->updateProgress("Restoring directory...", 70 + (int)(($currentDir / $totalDirs) * 15));
        }

        // Restore .env
        $envSource = $this->stagingDir . '/.env';
        if (File::exists($envSource)) {
            File::copy($envSource, base_path('.env'));
        }
    }
    
    public function getMetadataFromZip(string $zipFilePath): ?array
    {
        $zip = new ZipArchive();
        if ($zip->open($zipFilePath) === true) {
            $content = $zip->getFromName('info.json');
            $zip->close();
            
            if ($content) {
                return json_decode($content, true);
            }
        }
        
        return null;
    }
}
