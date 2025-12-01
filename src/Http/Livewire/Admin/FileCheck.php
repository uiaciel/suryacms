<?php
// File: app/Livewire/Admin/FileCheck.php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin;

use Livewire\Component;
use Uiaciel\SuryaCms\Models\Setting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;

class FileCheck extends Component
{
    public array $files = [];
    public $active_theme = 'default';

    public function mount(): void
    {

        $setting = Setting::find(1);
        $this->active_theme = $setting->active_theme;

        $this->files = [
            [
                'label' => 'EN Page (en.html)',
                'path' => public_path('en.html'),
            ],
            [
                'label' => 'ID Page (id.html)',
                'path' => public_path('id.html'),
            ],
            [
                'label' => 'Sitemap (sitemap.xml)',
                'path' => public_path('sitemap.xml'),
            ],
            [
                'label' => 'Robots.txt',
                'path' => public_path('robots.txt'),
            ],
            [
                'label' => 'Theme Config (theme.php)',
                'path' => base_path('resources/views/frontend/' . $this->active_theme . '/theme.php'), // sesuaikan tema aktif
            ],
            [
                'label' => 'Storage Link (public/storage)',
                'path' => public_path('storage'),
            ],
            [
                'label' => 'Log File (laravel.log)',
                'path' => storage_path('logs/laravel.log'),
            ],
            [
                'label' => 'Env File (.env)',
                'path' => base_path('.env'),
            ],
        ];
    }

    public function getStatus($path): array
    {
        if (file_exists($path)) {
            return [
                'status' => 'Ada',
                'size' => format_bytes(filesize($path)),
                'modified' => date('d M Y H:i', filemtime($path))
            ];
        }

        return [
            'status' => 'Tidak Ada',
            'size' => '-',
            'modified' => '-'
        ];
    }

    public function render()
    {
        return view('suryacms::livewire.admin.file-check');
    }
}

if (!function_exists('format_bytes')) {
    function format_bytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
