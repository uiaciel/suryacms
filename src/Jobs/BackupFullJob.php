<?php

namespace Uiaciel\SuryaCms\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Uiaciel\SuryaCms\Services\BackupManager;

class BackupFullJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?string $label;
    public ?string $statusFile;

    public function __construct(?string $label = null, ?string $statusFile = null)
    {
        $this->label = $label;
        $this->statusFile = $statusFile;
    }

    public function handle(): void
    {
        try {
            BackupManager::makeBackup($this->label, $this->statusFile);
        } catch (\Throwable $e) {
            if ($this->statusFile) {
                BackupManager::updateStatus($this->statusFile, 'failed', 'Backup gagal: ' . $e->getMessage(), 0);
            }
            throw $e;
        }
    }
}
