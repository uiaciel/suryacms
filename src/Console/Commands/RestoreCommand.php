<?php

namespace Uiaciel\SuryaCms\Console\Commands;

use Illuminate\Console\Command;
use Throwable;
use Uiaciel\SuryaCms\Services\RestoreService;

class RestoreCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suryacms:restore {file : The path to the backup zip file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform a full restore of SuryaCMS from a backup zip file';

    /**
     * Execute the console command.
     */
    public function handle(RestoreService $restoreService)
    {
        $file = $this->argument('file');

        if (! file_exists($file)) {
            $this->error("Backup file not found at path: {$file}");

            return Command::FAILURE;
        }

        $this->info('Starting SuryaCMS Full Restore...');
        $this->warn('This will overwrite current database tables and files. Make sure you know what you are doing!');

        if (! $this->confirm('Do you wish to continue?')) {
            $this->info('Restore cancelled.');

            return Command::SUCCESS;
        }

        try {
            $restoreService->runRestore($file);
            $this->info('Restore completed successfully!');

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Restore failed: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
