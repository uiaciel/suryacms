<?php

namespace Uiaciel\SuryaCms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupThemeStorage extends Command
{
    protected $signature = 'suryacms:setup-theme-storage';
    protected $description = 'Setup theme storage directories and permissions';

    public function handle()
    {
        $this->info('🔧 Setting up theme storage directories...');

        $paths = [
            storage_path('app/temp'),
            storage_path('app/temp/themes'),
            storage_path('app/private'),
            storage_path('app/private/themes'),
            public_path('frontend'),
            resource_path('views/frontend'),
        ];

        foreach ($paths as $path) {
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
                $this->line("✅ Created: {$path}");
            } else {
                $this->line("✓ Exists: {$path}");
            }
        }

        // Check permissions
        $this->line('');
        $this->info('📋 Checking permissions...');

        foreach ($paths as $path) {
            $perms = substr(sprintf('%o', fileperms($path)), -4);
            $this->line("  {$path} -> {$perms}");
        }

        $this->newLine();
        $this->info('✨ Theme storage setup complete!');

        return self::SUCCESS;
    }
}
