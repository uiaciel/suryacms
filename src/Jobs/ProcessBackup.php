<?php

namespace Uiaciel\SuryaCms\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Uiaciel\SuryaCms\Services\BackupService;
use Throwable;

class ProcessBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour timeout for large backups

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(BackupService $backupService): void
    {
        cache()->put('suryacms_backup_status', ['step' => 'Starting backup...', 'percentage' => 0], now()->addHours(2));
        
        try {
            $backupService->setCacheKey('suryacms_backup_status')->runBackup();
        } catch (Throwable $e) {
            cache()->put('suryacms_backup_status', ['step' => 'Failed: ' . $e->getMessage(), 'percentage' => -1], now()->addHours(2));
            $this->fail($e);
        }
    }
}
