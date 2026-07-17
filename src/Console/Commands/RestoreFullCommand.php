<?php

namespace Uiaciel\SuryaCms\Console\Commands;

use Illuminate\Console\Command;
use Uiaciel\SuryaCms\Services\BackupManager;

class RestoreFullCommand extends Command
{
    protected $signature = 'suryacms:restore-full {file : Path to the backup file} {--status= : Optional status JSON file path}';

    protected $description = 'Restore a full SuryaCMS backup archive';

    public function handle(): int
    {
        $file = $this->argument('file');
        $status = $this->option('status');
        $statusPath = null;

        if ($status) {
            if (str_starts_with($status, DIRECTORY_SEPARATOR) || preg_match('/^[A-Za-z]:\\/', $status)) {
                $statusPath = $status;
            } else {
                $statusPath = storage_path('app/' . ltrim($status, '/'));
            }
        }

        try {
            $result = BackupManager::restoreFromBackup($file, $statusPath);
            $this->info($result['message'] ?? 'Restore completed successfully.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Restore failed: ' . $e->getMessage());

            if ($statusPath) {
                file_put_contents($statusPath, json_encode([
                    'status' => 'failed',
                    'message' => $e->getMessage(),
                    'progress' => 0,
                    'updated_at' => now()->toDateTimeString(),
                ], JSON_PRETTY_PRINT));
            }

            return self::FAILURE;
        }
    }
}
