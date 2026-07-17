<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Uiaciel\SuryaCms\Jobs\BackupFullJob;
use Maatwebsite\Excel\Facades\Excel;
use Uiaciel\SuryaCms\Exports\GalleryExport;
use Uiaciel\SuryaCms\Exports\MenuExport;
use Uiaciel\SuryaCms\Exports\PageExport;
use Uiaciel\SuryaCms\Exports\PostExport;
use Uiaciel\SuryaCms\Exports\SettingExport;
use Uiaciel\SuryaCms\Services\BackupManager;
use Uiaciel\SuryaCms\Models\Menu;
use Uiaciel\SuryaCms\Models\Page;
use Uiaciel\SuryaCms\Models\Post;
use Uiaciel\SuryaCms\Models\Setting;

class Backup extends Component
{
    public $setting;

    public $backupProgress;

    public $posts;

    public $menus;

    public $postsCount;

    public $pages;

    public $pagesCount;

    public $backupFiles = [];

    public $isBackingUp = false;

    public $backupMessage = '';

    public $titlePage = 'Backup';

    public $date;

    public function mount()
    {
        $this->date = Carbon::now()->format('d-m-Y');
        $this->posts = Post::all();
        $this->postsCount = Post::count();
        $this->pages = Page::all();
        $this->pagesCount = Page::count();
        $this->menus = Menu::all();
        $this->setting = Setting::where('id', 1)->first();
        $this->loadBackupFiles();
    }

    public function loadBackupFiles()
    {
        $directory = BackupManager::backupDirectory();
        if (! File::isDirectory($directory)) {
            $this->backupFiles = [];
            return;
        }

        $this->backupFiles = collect(File::files($directory))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->map(fn ($file) => [
                'name' => $file->getFilename(),
                'path' => $file->getRealPath(),
                'updated_at' => date('Y-m-d H:i:s', $file->getMTime()),
                'size' => $this->formatSize($file->getSize()),
            ])
            ->toArray();
    }

    public function formatSize(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        if ($bytes < 1048576) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return round($bytes / 1048576, 2) . ' MB';
    }

    public function exportFullBackup()
    {
        // Dispatch the backup job and keep the UI responsive.
        BackupFullJob::dispatch()->onQueue('default');

        $this->isBackingUp = true;
        $this->backupMessage = 'Backup sedang berjalan di latar belakang. Silakan cek kembali halaman ini nanti untuk melihat file backup yang tersedia.';
    }

    public function downloadBackup(string $fileName)
    {
        $path = BackupManager::backupDirectory() . DIRECTORY_SEPARATOR . $fileName;
        if (! File::exists($path)) {
            session()->flash('error', 'File backup tidak ditemukan.');
            return;
        }

        return response()->download($path)->deleteFileAfterSend(true);
    }

    // public function exportStorage()
    // {
    //     session()->flash('message', 'Storage Export is Downloading ...');
    //     $sanitizedUrl = str_replace('/', '-', $this->setting->url);

    //     $zipFileName = 'backup-Storage-' . $sanitizedUrl . '-' . $this->date . '.zip';
    //     $zipFilePath = storage_path('app/public/' . $zipFileName);

    //     // Define the specific subdirectories to include
    //     $directoriesToZip = [
    //         'favicons',
    //         'galleries',
    //         'images',
    //     ];

    //     $zip = new \ZipArchive();
    //     if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
    //         $storagePath = storage_path('app/public');

    //         foreach ($directoriesToZip as $dir) {
    //             $fullDirPath = $storagePath . DIRECTORY_SEPARATOR . $dir;
    //             if (File::isDirectory($fullDirPath)) {
    //                 $files = File::allFiles($fullDirPath);
    //                 foreach ($files as $file) {
    //                     $relativePath = 'storage/' . $dir . '/' . $file->getRelativePathname();
    //                     $zip->addFile($file->getRealPath(), $relativePath);
    //                 }
    //             } else {
    //                 session()->flash('warning', "Directory '{$dir}' not found in storage/app/public. Skipping.");
    //             }
    //         }

    //         $zip->close();

    //         return response()->download($zipFilePath)->deleteFileAfterSend(true);
    //     } else {
    //         session()->flash('error', 'Failed to create storage backup.');
    //     }
    // }

    public function exportPost()
    {
        session()->flash('message', 'Post Export is Downloading ...');
        $sanitizedUrl = str_replace('/', '-', $this->setting->url);

        return Excel::download(new PostExport, 'backup-Posts-'.$sanitizedUrl.'-'.$this->date.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportPage()
    {
        session()->flash('message', 'Page Export is Downloading ...');
        $sanitizedUrl = str_replace('/', '-', $this->setting->url);

        return Excel::download(new PageExport, 'backup-Pages-'.$sanitizedUrl.'-'.$this->date.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportGallery()
    {
        session()->flash('message', 'Gallery Export is Downloading ...');
        $sanitizedUrl = str_replace('/', '-', $this->setting->url);

        return Excel::download(new GalleryExport, 'backup-Gallery-'.$sanitizedUrl.'-'.$this->date.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportSetting()
    {
        session()->flash('message', 'Setting Export is Downloading ...');
        $sanitizedUrl = str_replace('/', '-', $this->setting->url);

        return Excel::download(new SettingExport, 'backup-Settings-'.$sanitizedUrl.'-'.$this->date.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportMenu()
    {
        session()->flash('message', 'Menu Export is Downloading ...');
        $sanitizedUrl = str_replace('/', '-', $this->setting->url);

        return Excel::download(new MenuExport, 'backup-Menus-'.$sanitizedUrl.'-'.$this->date.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportTheme()
    {
        session()->flash('message', 'Theme Export is Downloading ...');
        $sanitizedUrl = str_replace('/', '-', $this->setting->url);
        $themeName = $this->setting->active_theme;

        $publicThemePath = public_path('frontend/'.$themeName);
        $resourcesThemePath = base_path('resources/views/frontend/'.$themeName);

        $zipFileName = 'backup-Theme-'.$themeName.'-'.$sanitizedUrl.'-'.$this->date.'.zip';
        $zipFilePath = storage_path('app/public/'.$zipFileName);

        $zip = new \ZipArchive;
        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            // Add public theme files
            if (File::isDirectory($publicThemePath)) {
                $files = File::allFiles($publicThemePath);
                foreach ($files as $file) {
                    $relativePath = 'public/frontend/'.$themeName.'/'.$file->getRelativePathname();
                    $zip->addFile($file->getRealPath(), $relativePath);
                }
            }

            // Add resources theme files
            if (File::isDirectory($resourcesThemePath)) {
                $files = File::allFiles($resourcesThemePath);
                foreach ($files as $file) {
                    $relativePath = 'resources/views/frontend/'.$themeName.'/'.$file->getRelativePathname();
                    $zip->addFile($file->getRealPath(), $relativePath);
                }
            }
            $zip->close();

            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        } else {
            session()->flash('error', 'Failed to create theme backup.');
        }
    }

    public function render()
    {
        return view('suryacms::livewire.admin.backup', [
            'posts' => $this->posts,
            'pages' => $this->pages,
            'postscount' => $this->postsCount,
            'pagescount' => $this->pagesCount,
        ])->layout('suryacms::layouts.app');
    }
}
