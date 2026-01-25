<?php

namespace Uiaciel\SuryaCms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Uiaciel\SuryaCms\Models\Setting;
use ZipArchive;

class InstallThemeCommand extends Command
{
    protected $signature = 'theme:install {zipfile} {--activate}';

    protected $description = 'Install theme from storage/app/public and optionally activate it';

    public function handle()
    {
        $zipFileName = $this->argument('zipfile');
        $zipFilePath = storage_path('app/private/public/'.$zipFileName);

        if (! File::exists($zipFilePath)) {
            $this->error("❌ File not found: {$zipFilePath}");

            return Command::FAILURE;
        }

        $tempExtract = storage_path('app/temp_theme_extract');
        File::deleteDirectory($tempExtract);
        File::ensureDirectoryExists($tempExtract);

        $zip = new ZipArchive;
        if ($zip->open($zipFilePath) !== true) {
            $this->error('❌ Failed to open ZIP file. Check if it is a valid archive.');

            return Command::FAILURE;
        }

        $zip->extractTo($tempExtract);
        $zip->close();

        // Cari folder tema di dalam ekstraksi (harus ada folder view/frontend atau langsung file blade)
        $themeFolder = $this->detectThemeFolder($tempExtract);
        if (! $themeFolder) {
            $this->error('❌ Could not find a valid theme folder (missing views/frontend).');
            File::deleteDirectory($tempExtract);

            return Command::FAILURE;
        }

        $themeName = basename($themeFolder);

        // 1️⃣ Copy Views
        $frontendPath = base_path('resources/views/frontend/'.$themeName);
        File::deleteDirectory($frontendPath);
        File::ensureDirectoryExists(dirname($frontendPath));
        File::copyDirectory($themeFolder, $frontendPath);
        $this->info("✅ Views copied to: resources/views/frontend/{$themeName}");

        // 2️⃣ Copy Assets (jika ada public/frontend di dalam zip)
        $assetsSource = $tempExtract.'/public/frontend/'.$themeName;
        if (File::isDirectory($assetsSource)) {
            $assetsDest = public_path('frontend/'.$themeName);
            File::deleteDirectory($assetsDest);
            File::ensureDirectoryExists(dirname($assetsDest));
            File::copyDirectory($assetsSource, $assetsDest);
            $this->info("🖼️ Assets copied to: public/frontend/{$themeName}");
        }

        // 3️⃣ Optional: Activate theme
        if ($this->option('activate')) {
            $setting = Setting::find(1);
            if ($setting) {
                $setting->active_theme = $themeName;
                $setting->save();
                $this->info("🎯 Theme '{$themeName}' activated successfully!");
            } else {
                $this->warn('⚠️ Setting record with id=1 not found. Skipping activation.');
            }
        }

        // 4️⃣ Cleanup
        File::deleteDirectory($tempExtract);
        $this->info('🎉 Theme installation completed successfully!');

        return Command::SUCCESS;
    }

    private function detectThemeFolder($basePath)
    {
        // Kasus 1: Tema langsung ada di root (misal: index.blade.php)
        if (File::exists($basePath.'/index.blade.php')) {
            return $basePath;
        }

        // Kasus 2: Folder tema ada di resources/views/frontend/{tema}
        $possible = glob($basePath.'/resources/views/frontend/*', GLOB_ONLYDIR);
        if (! empty($possible)) {
            return $possible[0];
        }

        // Kasus 3: Folder tema langsung di root zip
        $dirs = glob($basePath.'/*', GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            if (File::exists($dir.'/index.blade.php')) {
                return $dir;
            }
        }

        return null;
    }
}
