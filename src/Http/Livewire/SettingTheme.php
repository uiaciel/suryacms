<?php

namespace Uiaciel\SuryaCms\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class SettingTheme extends Component
{
    use WithFileUploads;

    public $themeZip;

    public function uploadAndInstall()
    {
        $this->validate([
            'themeZip' => 'required|file|mimes:zip|max:41200', // max 50MB
        ]);

        // Simpan ke storage/app/public/
        $fileName = Str::slug(pathinfo($this->themeZip->getClientOriginalName(), PATHINFO_FILENAME)) . '.zip';
        $path = $this->themeZip->storeAs('public', $fileName);

        if (!$path) {
            $this->dispatch('alert', type: 'error', message: '❌ Gagal mengupload file tema.');
            return;
        }

        try {
            // Jalankan command theme:install
            Artisan::call('theme:install', [
                'zipfile' => $fileName,
                '--activate' => true
            ]);

            $output = Artisan::output();
            $this->dispatch('alert', type: 'success', message: "🎉 Tema berhasil diinstal dan diaktifkan!\n\n{$output}");
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: '❌ Terjadi kesalahan saat menginstall tema: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('suryacms::livewire.setting-theme')->layout('suryacms::layouts.app');
    }
}
