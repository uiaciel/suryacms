<?php

namespace Uiaciel\SuryaCms\Livewire\System;


use Livewire\Component;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Uiaciel\SuryaCms\Jobs\ProcessRestore;
use Uiaciel\SuryaCms\Jobs\ProcessBackup;
use Uiaciel\SuryaCms\Services\RestoreService;
use Carbon\Carbon;

class FullRestore extends Component
{
    public $backupFiles = [];
    public $selectedBackup = null;
    public $backupMetadata = null;
    public $currentSystemVersion = null;

    public $isRestoring = false;
    public $progressStep = '';
    public $progressPercentage = 0;

    public $isDatabaseEmpty = true;
    public $isRestoreComplete = false;

    public $isBackingUp = false;
    public $backupProgressStep = '';
    public $backupProgressPercentage = 0;

    public function mount()
    {
        $this->checkDatabaseStatus();
        $this->loadBackupFiles();
        $this->currentSystemVersion = config('suryacms.version', '1.0.0');
    }

    protected function checkDatabaseStatus()
    {
        $prefix = config('suryacms.table_prefix', '');
        $testTable = $prefix . 'settings';

        if (Schema::hasTable($testTable)) {
            $count = \Illuminate\Support\Facades\DB::table($testTable)->count();
            $this->isDatabaseEmpty = $count === 0;
        } else {
            $this->isDatabaseEmpty = true;
        }
    }

    public function loadBackupFiles()
    {
        $path = storage_path('app/private/suryacms_backups');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $files = File::files($path);

        $this->backupFiles = collect($files)->map(function($file) {
            return [
                'name' => $file->getFilename(),
                'path' => $file->getRealPath(),
                'size' => $this->formatBytes($file->getSize()),
                'modified' => Carbon::createFromTimestamp($file->getMTime())->diffForHumans(),
            ];
        })->sortByDesc('modified')->values()->toArray();
    }

    public function updatedSelectedBackup($value)
    {
        if ($value) {
            $fileInfo = collect($this->backupFiles)->firstWhere('name', $value);
            if ($fileInfo) {
                $restoreService = app(RestoreService::class);
                $this->backupMetadata = $restoreService->getMetadataFromZip($fileInfo['path']);
            }
        } else {
            $this->backupMetadata = null;
        }
    }

    public function startRestore()
    {
        if (!$this->selectedBackup) {
            $this->addError('selectedBackup', 'Please select a backup file first.');
            return;
        }

        $fileInfo = collect($this->backupFiles)->firstWhere('name', $this->selectedBackup);

        if (!$fileInfo) {
            $this->addError('selectedBackup', 'Selected file is invalid.');
            return;
        }

        $this->isRestoring = true;
        $this->isRestoreComplete = false;
        $this->progressStep = 'Initializing restore...';
        $this->progressPercentage = 0;

        cache()->forget('suryacms_restore_status');

        ProcessRestore::dispatch($fileInfo['path']);
    }

    public function checkProgress()
    {
        if (!$this->isRestoring) {
            return;
        }

        $status = cache('suryacms_restore_status');

        if ($status) {
            $this->progressStep = $status['step'];
            $this->progressPercentage = $status['percentage'];

            if ($this->progressPercentage == 100) {
                $this->isRestoring = false;
                $this->isRestoreComplete = true;
                cache()->forget('suryacms_restore_status');
                session()->flash('message', 'System has been successfully restored.');
            } else if ($this->progressPercentage == -1) {
                $this->isRestoring = false;
                $this->addError('restore', 'Restore failed: ' . $this->progressStep);
                cache()->forget('suryacms_restore_status');
            }
        }
    }

    public function generateBackup()
    {
        $this->isBackingUp = true;
        $this->backupProgressStep = 'Initializing backup...';
        $this->backupProgressPercentage = 0;
        cache()->forget('suryacms_backup_status');
        ProcessBackup::dispatch();
    }

    public function checkBackupProgress()
    {
        if (!$this->isBackingUp) {
            return;
        }

        $status = cache('suryacms_backup_status');

        if ($status) {
            $this->backupProgressStep = $status['step'];
            $this->backupProgressPercentage = $status['percentage'];

            if ($this->backupProgressPercentage == 100) {
                $this->isBackingUp = false;
                cache()->forget('suryacms_backup_status');
                session()->flash('message', 'Backup generated successfully.');
                $this->loadBackupFiles();
            } else if ($this->backupProgressPercentage == -1) {
                $this->isBackingUp = false;
                $this->addError('backup', 'Backup failed: ' . $this->backupProgressStep);
                cache()->forget('suryacms_backup_status');
            }
        }
    }

    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function render()
    {
        return view('suryacms::livewire.system.full-restore')->layout('suryacms::layouts.app');
    }
}
