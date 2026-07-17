<?php

namespace Uiaciel\SuryaCms\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Uiaciel\SuryaCms\Services\BackupManager;

class RestoreFullJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $backupPath;
    public string $statusFile;

    public function __construct(string $backupPath, string $statusFile)
    {
        $this->backupPath = $backupPath;
        $this->statusFile = $statusFile;
    }

    public function handle(): void
    {
        BackupManager::updateStatus($this->statusFile, 'running', 'Restore job dijalankan.', 10);
        BackupManager::restoreFromBackup($this->backupPath, $this->statusFile);
    }

    public function failed(\Throwable $exception): void
    {
        BackupManager::updateStatus($this->statusFile, 'failed', 'Restore job gagal: ' . $exception->getMessage(), 0);
    }
}
