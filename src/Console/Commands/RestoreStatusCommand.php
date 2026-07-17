<?php

namespace Uiaciel\SuryaCms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Uiaciel\SuryaCms\Services\BackupManager;

class RestoreStatusCommand extends Command
{
    protected $signature = 'suryacms:restore-status {token? : Optional restore status token}';

    protected $description = 'Show the status of a SuryaCMS restore process or list recent restore status records.';

    public function handle(): int
    {
        $token = $this->argument('token');

        if ($token) {
            $path = BackupManager::getStatusFilePath($token);
            if (! File::exists($path)) {
                $this->error('Status file not found for token: ' . $token);
                return self::FAILURE;
            }

            $status = json_decode(File::get($path), true);
            $this->line(json_encode($status, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNESCAPED_SLASHES));
            return self::SUCCESS;
        }

        $directory = BackupManager::statusDirectory();
        $files = collect(File::files($directory))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->take(20)
            ->map(fn ($file) => [
                'name' => $file->getFilename(),
                'updated_at' => date('Y-m-d H:i:s', $file->getMTime()),
                'size' => $this->formatSize($file->getSize()),
            ])
            ->toArray();

        if (empty($files)) {
            $this->info('No restore status files found.');
            return self::SUCCESS;
        }

        $this->table(['File', 'Updated At', 'Size'], $files);
        return self::SUCCESS;
    }

    protected function formatSize(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        if ($bytes < 1048576) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return round($bytes / 1048576, 2) . ' MB';
    }
}
