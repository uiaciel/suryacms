<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\Themes;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Uiaciel\SuryaCms\Models\Setting;

class SettingTheme extends Component
{
    use WithFileUploads;

    public $themeZip;

    public $themes = [];

    public $activeTheme;

    public $themeToDelete = null;

    public function mount()
    {
        try {
            $setting = Setting::first();
            $this->activeTheme = $setting ? $setting->active_theme : null;
        } catch (\Exception $e) {
            \Log::error('SettingTheme mount error: ' . $e->getMessage());
            $this->activeTheme = null;
        }

        $this->loadThemes();
    }

    protected function loadThemes()
    {
        try {
            $basePath = resource_path('views/frontend');

            if (! File::exists($basePath)) {
                return;
            }

            foreach (File::directories($basePath) as $dir) {
                $configPath = $dir.'/theme.php';

                if (! File::exists($configPath)) {
                    continue;
                }

                $config = include $configPath;

                if (! isset($config['info'])) {
                    continue;
                }

                $info = $config['info'];

                $this->themes[] = [
                    'name' => $info['name'] ?? 'Unnamed Theme',
                    'path' => $info['path'] ?? basename($dir),
                    'version' => $info['version'] ?? '-',
                    'author' => $info['author'] ?? '-',
                    'url' => $info['url'] ?? null,
                    'license' => $info['license'] ?? '-',
                    'description' => $info['description'] ?? '-',
                    'preview' => $info['theme_preview'] ?? null,
                    'is_active' => $this->activeTheme === ($info['path'] ?? basename($dir)),
                ];
            }
        } catch (\Exception $e) {
            \Log::error('SettingTheme loadThemes error: ' . $e->getMessage());
            $this->themes = [];
        }
    }

    public function installTheme(string $path)
    {
        $setting = Setting::first();
        $setting->update([
            'active_theme' => $path,
        ]);

        $this->activeTheme = $path;

        foreach ($this->themes as &$theme) {
            $theme['is_active'] = $theme['path'] === $path;
        }

        session()->flash('success', 'Tema berhasil diaktifkan');
    }

    public function deleteTheme(string $themeName)
    {
        try {
            $setting = Setting::first();

            // Jika tema yang dihapus adalah tema aktif, reset ke default
            if ($setting && $setting->active_theme === $themeName) {
                $setting->update([
                    'active_theme' => 'default', // ganti ke default
                ]);
                $this->activeTheme = 'default';
            }

            // Path ke folder tema
            $resourcePath = resource_path('views/frontend/'.$themeName);
            $publicPath   = public_path('frontend/'.$themeName);

            // Hapus folder jika ada
            if (File::exists($resourcePath)) {
                File::deleteDirectory($resourcePath);
            }

            if (File::exists($publicPath)) {
                File::deleteDirectory($publicPath);
            }

            // Refresh daftar tema
            $this->themes = [];
            $this->loadThemes();

            session()->flash('success', "Tema '{$themeName}' berhasil dihapus.");
        } catch (\Exception $e) {
            \Log::error('SettingTheme deleteTheme error: ' . $e->getMessage());
            session()->flash('error', '❌ Gagal menghapus tema: '.$e->getMessage());
        }
    }

    public function uploadAndInstall()
    {
        $this->validate([
            'themeZip' => 'required|file|mimes:zip|max:41200', // max 50MB
        ]);

        // Simpan ke storage/app/public/
        $fileName = Str::slug(pathinfo($this->themeZip->getClientOriginalName(), PATHINFO_FILENAME)).'.zip';
        $path = $this->themeZip->storeAs('public', $fileName);

        if (! $path) {
            $this->dispatch('alert', type: 'error', message: '❌ Gagal mengupload file tema.');

            return;
        }

        try {
            // Jalankan command theme:install
            Artisan::call('theme:install', [
                'zipfile' => $fileName,
                '--activate' => true,
            ]);

            $output = Artisan::output();
            $this->dispatch('alert', type: 'success', message: "🎉 Tema berhasil diinstal dan diaktifkan!\n\n{$output}");
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: '❌ Terjadi kesalahan saat menginstall tema: '.$e->getMessage());
        }
    }

    public function confirmDeleteTheme(string $themeName)
    {
        $this->themeToDelete = $themeName;
        $this->dispatch('show-delete-modal'); // trigger JS untuk buka modal
    }

    public function deleteConfirmed()
    {
        if ($this->themeToDelete) {
            $this->deleteTheme($this->themeToDelete);
            $this->themeToDelete = null;
        }
    }

    public function render()
    {
        return view('suryacms::livewire.admin.themes.setting-theme')->layout('suryacms::layouts.app');
    }
}
