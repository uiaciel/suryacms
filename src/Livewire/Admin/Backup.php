<?php

namespace Uiaciel\SuryaCms\Livewire\Admin;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Uiaciel\SuryaCms\Exports\GalleryExport;
use Uiaciel\SuryaCms\Exports\MenuExport;
use Uiaciel\SuryaCms\Exports\PageExport;
use Uiaciel\SuryaCms\Exports\PostExport;
use Uiaciel\SuryaCms\Exports\SettingExport;
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
        return view('suryacms::livewire.admin.backup', [
            'posts' => $this->posts,
            'pages' => $this->pages,
            'postscount' => $this->postsCount,
            'pagescount' => $this->pagesCount,
        ])->layout('suryacms::layouts.app');
    }
}
