<?php

namespace Uiaciel\SuryaCms\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;
use Uiaciel\SuryaCms\Services\RestoreService;

class ProcessRestore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour timeout for large restores

    protected string $zipFilePath;

    /**
     * Create a new job instance.
     */
    public function __construct(string $zipFilePath)
    {
        $this->zipFilePath = $zipFilePath;
    }

    /**
     * Execute the job.
     */
    public function handle(RestoreService $restoreService): void
    {
        cache()->put('suryacms_restore_status', ['step' => 'Starting restore...', 'percentage' => 0], now()->addHours(2));

        try {
            $restoreService->setCacheKey('suryacms_restore_status')->runRestore($this->zipFilePath);
        } catch (Throwable $e) {
            cache()->put('suryacms_restore_status', ['step' => 'Failed: '.$e->getMessage(), 'percentage' => -1], now()->addHours(2));
            $this->fail($e);
        }
    }
}
