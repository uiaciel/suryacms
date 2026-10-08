<?php

namespace Uiaciel\SuryaCms\Console\Commands;

use Illuminate\Console\Command;
use Throwable;
use Uiaciel\SuryaCms\Services\BackupService;

class BackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suryacms:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform a full backup of SuryaCMS (database, assets, storage, config)';

    /**
     * Execute the console command.
     */
    public function handle(BackupService $backupService)
    {
        $this->info('Starting SuryaCMS Full Backup...');

        try {
            $zipFile = $backupService->runBackup();
            $this->info('Backup completed successfully!');
            $this->line('File saved at: '.$zipFile);

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Backup failed: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
