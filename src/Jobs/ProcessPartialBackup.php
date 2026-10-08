<?php

namespace Uiaciel\SuryaCms\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;
use Uiaciel\SuryaCms\Services\PartialBackupService;

class ProcessPartialBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;

    public function handle(PartialBackupService $backupService): void
    {
        try {
            $backupService->run();
        } catch (Throwable $exception) {
            cache()->put('suryacms_partial_backup_status', ['step' => 'Partial backup failed: '.$exception->getMessage(), 'percentage' => -1], now()->addHours(2));
            $this->fail($exception);
        }
    }
}
