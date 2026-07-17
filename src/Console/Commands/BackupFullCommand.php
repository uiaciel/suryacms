<?php

namespace Uiaciel\SuryaCms\Console\Commands;

use Illuminate\Console\Command;
use Uiaciel\SuryaCms\Services\BackupManager;

class BackupFullCommand extends Command
{
    protected $signature = 'suryacms:backup-full {name? : Optional custom backup name}';

    protected $description = 'Create a full SuryaCMS backup including database, storage, themes, and .env';

    public function handle(): int
    {
        try {
            $backupPath = BackupManager::makeBackup($this->argument('name') ?? null);
            $this->info('Backup completed successfully.');
            $this->line('File: ' . $backupPath);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Backup failed: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
