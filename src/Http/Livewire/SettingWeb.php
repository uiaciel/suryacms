<?php

namespace Uiaciel\SuryaCms\Http\Livewire;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Uiaciel\SuryaCms\Imports\SettingImport;
use Uiaciel\SuryaCms\Models\Gallery;
use Uiaciel\SuryaCms\Models\Page;
use Uiaciel\SuryaCms\Models\Setting;

class SettingWeb extends Component
{
    use WithFileUploads;

    public $title = 'Settings';

    public $sitename;

    public $tagline;

    public $description;

    public $keywords;

    public $sitename_translation;

    public $tagline_translation;

    public $description_translation;

    public $keywords_translation;

    public $googlesiteverification;

    public $address;

    public $email;

    public $phone;

    public $whatsapp;

    public $facebook;

    public $twitter;

    public $instagram;

    public $linkedin;

    public $youtube;

    public $tiktok;

    public $url;

    public $logo;

    public $favicon;

    public $images;

    public $google_analytics;

    public $google_adsense;

    public $language;

    public $is_multilingual = 'No';

    public $code;

    public $color_primary;

    public $color_secondary;

    public $color_success;

    public $color_danger;

    public $color_warning;

    public $color_info;

    public $color_light;

    public $color_dark;

    public $site_maintenance;

    public $email_forwarder;

    public $date_format;

    public $active_theme;

    public string $homepage_type = 'index';

    public ?int $homepage_id = null;

    public $themes = [];

    public array $pages = [];

    public array $homepagePages = [];

    public $themeInfo = [];

    public $importFile;

    public $date;

    public $dateFormats = [];

    public $theme_zip;

    // Modal setup setting properties
    public bool $showSetupModal = false;

    public bool $settingExists = false;

    public string $setup_sitename = '';

    public string $setup_url = '';

    public string $setup_language = 'id';

    public string $setup_email = '';

    public string $setup_tagline = '';

    public function getAvailableThemes()
    {
        $viewThemes = glob(resource_path('views/frontend/*'), GLOB_ONLYDIR);
        $publicThemes = glob(public_path('frontend/*'), GLOB_ONLYDIR);

        // Ambil nama foldernya saja
        $viewThemes = array_map('basename', $viewThemes);
        $publicThemes = array_map('basename', $publicThemes);

        // Ambil yang tersedia di dua tempat sekaligus (view + public)
        return array_values(array_intersect($viewThemes, $publicThemes));
    }

    public function updatedActiveTheme($value)
    {
        $themeConfigPath = resource_path("views/frontend/{$value}/theme.php");
        if (File::exists($themeConfigPath)) {
            $themeConfig = require $themeConfigPath;
            $this->themeInfo = $themeConfig; // Simpan info theme ke property
            if (isset($themeConfig['info']['theme_preview'])) {
                $this->dispatch('updateThemePreview', asset("frontend/{$value}".$themeConfig['info']['theme_preview']));
            } else {
                $this->dispatch('updateThemePreview', null);
            }
        } else {
            $this->themeInfo = [];
            $this->dispatch('updateThemePreview', null);
        }
    }

    public function mount()
    {
        $this->dateFormats = [
            'd/m/Y' => 'dd/mm/yyyy',
            'Y-m-d' => 'yyyy-mm-dd',
            'm/d/Y' => 'mm/dd/yyyy',
            'd-M-Y' => 'dd-Mon-yyyy',
        ];

        $setting = Setting::first();

        // Check if setting doesn't exist and show setup modal
        if (! $setting) {
            $this->showSetupModal = true;
            $this->settingExists = false;
            $this->setup_language = 'id';
            $this->setup_sitename = '';
            $this->setup_url = config('app.url');
            $this->setup_email = '';
            $this->setup_tagline = '';

            return;
        }

        $this->settingExists = true;
        // Rest of the existing mount() code

        if ($setting) {
            $this->sitename = $setting->sitename;
            $this->tagline = $setting->tagline;
            $this->description = $setting->description;
            $this->keywords = $setting->keywords;
            $this->sitename_translation = $setting->sitename_translation;
            $this->tagline_translation = $setting->tagline_translation;
            $this->description_translation = $setting->description_translation;
            $this->keywords_translation = $setting->keywords_translation;
            $this->googlesiteverification = $setting->googlesiteverification;
            $this->address = $setting->address;
            $this->email = $setting->email;
            $this->phone = $setting->phone;
            $this->whatsapp = $setting->whatsapp;
            $this->facebook = $setting->facebook;
            $this->twitter = $setting->twitter;
            $this->instagram = $setting->instagram;
            $this->linkedin = $setting->linkedin;
            $this->youtube = $setting->youtube;
            $this->tiktok = $setting->tiktok;
            $this->url = $setting->url;
            $this->themes = $this->getAvailableThemes();
            $this->active_theme = $setting->active_theme;
            $this->homepage_type = $setting->homepage_type ?? 'index';
            $this->homepage_id = $setting->homepage_id;
            $this->pages = Page::pluck('title', 'id')->toArray();
            $this->homepagePages = Page::where('title', 'like', 'Homepage%')
                ->limit(10) // Limit to avoid fetching too many
                ->pluck('title', 'id')->toArray();
            $this->google_analytics = $setting->google_analytics;
            $this->google_adsense = $setting->google_adsense;
            $this->language = $setting->language;
            $this->is_multilingual = $setting->is_multilingual;
            $this->code = $setting->code;
            $this->color_primary = $setting->color_primary ?? '#0d6efd';
            $this->color_secondary = $setting->color_secondary ?? '#6c757d';
            $this->color_success = $setting->color_success ?? '#198754';
            $this->color_danger = $setting->color_danger ?? '#dc3545';
            $this->color_warning = $setting->color_warning ?? '#ffc107';
            $this->color_info = $setting->color_info ?? '#0dcaf0';
            $this->color_light = $setting->color_light ?? '#f8f9fa';
            $this->color_dark = $setting->color_dark ?? '#212529';
            $this->site_maintenance = (bool) ($setting->site_maintenance ?? false);
            $this->email_forwarder = $setting->email_forwarder;
            $this->date_format = $setting->date_format ?? 'd/m/Y';

            if (! array_key_exists($this->date_format, $this->dateFormats)) {
                $this->date_format = array_key_first($this->dateFormats);
            }

            $this->active_theme = $setting->active_theme;

            $themeConfigPath = resource_path("views/frontend/{$this->active_theme}/theme.php");
            if (File::exists($themeConfigPath)) {
                $this->themeInfo = require $themeConfigPath;
            } else {
                $this->themeInfo = [];
            }
        }

        $this->date = now()->format('d-m-Y');
    }

    public function dataImport()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        // Ambil data pengaturan yang ada, atau buat instance baru jika tidak ada.
        $setting = Setting::firstOrNew(['id' => 1]);

        // Buat backup sebelum melakukan perubahan. Ini adalah jaring pengaman.
        $this->dataExport();

        try {
            // Gunakan ToModel concern yang sudah dimodifikasi untuk melakukan update.
            Excel::import(new SettingImport($setting), $this->importFile);

            session()->flash('success', 'Settings imported successfully!');
            $this->redirect(route('admin.setting'), navigate: true);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            // Anda bisa menampilkan pesan error yang lebih spesifik di sini jika mau.
            session()->flash('error', 'Failed to import settings. Please check your file. Error on row: '.$failures[0]->row().' - '.$failures[0]->errors()[0]);
        } catch (\Exception $e) {
            session()->flash('error', 'An unexpected error occurred during import: '.$e->getMessage());
        }
    }

    public function dataExport()
    {
        $sanitizedUrl = str_replace('/', '-', $this->url);
        $fileName = 'backup-Setting-'.$sanitizedUrl.'-'.$this->date.'.xlsx';

        return Excel::download(new SettingImport, $fileName, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportTheme()
    {
        $setting = Setting::where('id', 1)->first();
        $date = Carbon::now()->format('d-m-Y');

        session()->flash('message', 'Theme Export is Downloading ...');
        $sanitizedUrl = str_replace('/', '-', $setting->url);
        $themeName = $setting->active_theme;

        $publicThemePath = public_path('frontend/'.$themeName);
        $resourcesThemePath = base_path('resources/views/frontend/'.$themeName);

        $zipFileName = 'backup-Theme-'.$themeName.'-'.$sanitizedUrl.'-'.$date.'.zip';
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

    public function updateSettingWeb()
    {

        if (empty($this->dateFormats)) {
            $this->dateFormats = [
                'd/m/Y' => 'dd/mm/yyyy',
                'Y-m-d' => 'yyyy-mm-dd',
                'm/d/Y' => 'mm/dd/yyyy',
                'd-M-Y' => 'dd-Mon-yyyy',

            ];
        }

        if (empty($this->date_format)) {
            $this->date_format = 'd/m/Y';
        }

        $this->validate([
            'sitename' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'keywords' => 'nullable|string',
            'sitename_translation' => 'nullable|string|max:255',
            'tagline_translation' => 'nullable|string|max:255',
            'description_translation' => 'nullable|string',
            'keywords_translation' => 'nullable|string',
            'googlesiteverification' => 'nullable|string',
            'address' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'instagram' => 'nullable|url',
            'linkedin' => 'nullable|url',
            'youtube' => 'nullable|url',
            'tiktok' => 'nullable|url',
            'url' => 'nullable|url',
            'logo' => 'nullable|image|max:10024', // Max 1MB
            'favicon' => 'nullable|image|max:10024', // Max 512KB
            'images' => 'nullable|image|max:10024', // Max 1MB per image
            'active_theme' => 'nullable|string',
            'homepage_type' => 'nullable|string',
            'homepage_id' => 'nullable|integer',
            'google_analytics' => 'nullable|string',
            'google_adsense' => 'nullable|string',
            'language' => 'nullable|string|max:10',
            'is_multilingual' => 'nullable|in:Yes,No',
            'code' => 'nullable|string',
            'color_primary' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'color_secondary' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'color_success' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'color_danger' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'color_warning' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'color_info' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'color_light' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'color_dark' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],

            'site_maintenance' => ['boolean'],

            'email_forwarder' => ['nullable', 'email'],

            'date_format' => 'in:'.implode(',', array_keys($this->dateFormats)),

        ]);

        $setting = Setting::first();
        $setting->sitename = $this->sitename ?? $this->sitename_translation;
        $setting->tagline = $this->tagline ?? $this->tagline_translation;
        $setting->description = $this->description ?? $this->description_translation;
        $setting->keywords = $this->keywords ?? $this->keywords_translation;
        $setting->sitename_translation = $this->sitename_translation ?? $this->sitename;
        $setting->tagline_translation = $this->tagline_translation ?? $this->tagline;
        $setting->description_translation = $this->description_translation ?? $this->description;
        $setting->keywords_translation = $this->keywords_translation ?? $this->keywords;
        $setting->googlesiteverification = $this->googlesiteverification;
        $setting->address = $this->address;
        $setting->email = $this->email;
        $setting->phone = $this->phone;
        $setting->whatsapp = $this->whatsapp;
        $setting->facebook = $this->facebook;
        $setting->twitter = $this->twitter;
        $setting->instagram = $this->instagram;
        $setting->linkedin = $this->linkedin;
        $setting->youtube = $this->youtube;
        $setting->tiktok = $this->tiktok;
        $setting->url = $this->url;
        $setting->active_theme = $this->active_theme;
        $setting->homepage_type = $this->homepage_type;
        $setting->homepage_id = $this->homepage_id;
        $setting->google_analytics = $this->google_analytics;
        $setting->google_adsense = $this->google_adsense;
        $setting->language = $this->language;
        $setting->is_multilingual = $this->is_multilingual;
        $setting->code = $this->code;
        $setting->color_primary = $this->color_primary;
        $setting->color_secondary = $this->color_secondary;
        $setting->color_success = $this->color_success;
        $setting->color_danger = $this->color_danger;
        $setting->color_warning = $this->color_warning;
        $setting->color_info = $this->color_info;
        $setting->color_light = $this->color_light;
        $setting->color_dark = $this->color_dark;
        $setting->site_maintenance = $this->site_maintenance ?? false;
        $setting->email_forwarder = $this->email_forwarder;
        $setting->date_format = $this->date_format ?? 'd/m/Y';

        // Handle logo upload
        if ($this->logo instanceof UploadedFile) {
            $logoPath = $this->logo->storeAs(
                '', // store directly in storage/app/public
                'logo.png',
                ['disk' => 'public']
            );
            $setting->logo = 'storage/'.$logoPath;

            $gallery = new Gallery;
            $gallery->name = pathinfo($this->logo->getClientOriginalName(), PATHINFO_FILENAME);
            $gallery->description = 'Logo';
            $gallery->image_path = 'images/'.$setting->logo;
            $gallery->category = 'Logo';
            $gallery->status = 'Publish';
            $gallery->is_tinymce_upload = true;
            $gallery->save();
        }

        // Handle favicon upload
        if ($this->favicon instanceof UploadedFile) {
            $manager = new ImageManager(new Driver);
            $image = $manager->read($this->favicon->getRealPath());

            // Resize image to 64x64 pixels
            $image->resize(64, 64);

            // Define the path for the favicon
            $faviconFileName = 'favicon.png';
            $faviconPath = 'favicons/'.$faviconFileName;

            // Save the processed image to storage
            \Illuminate\Support\Facades\Storage::disk('public')->put($faviconPath, $image->encode());

            $setting->favicon = 'storage/'.$faviconPath; // Update the setting with the new path

            $gallery = new Gallery;
            $gallery->name = pathinfo($this->favicon->getClientOriginalName(), PATHINFO_FILENAME);
            $gallery->description = 'Favicon';
            $gallery->image_path = 'images/'.$setting->favicon;
            $gallery->category = 'Favicon';
            $gallery->status = 'Publish';
            $gallery->is_tinymce_upload = true;
            $gallery->save();
        }

        // Handle images upload
        if ($this->images instanceof UploadedFile) {
            $imagesPath = $this->images->storeAs(
                '',
                'images.png',
                ['disk' => 'public']
            );
            $setting->images = 'storage/'.$imagesPath;

            $gallery = new Gallery;
            $gallery->name = pathinfo($this->images->getClientOriginalName(), PATHINFO_FILENAME);
            $gallery->description = 'Images';
            $gallery->image_path = 'images/'.$setting->images;
            $gallery->category = 'Images';
            $gallery->status = 'Publish';
            $gallery->is_tinymce_upload = true;
            $gallery->save();
        }

        $setting->save();

        cache()->forget('app_date_format');
        session()->flash('message', 'Settings updated successfully.');

        $this->redirect(route('admin.setting'), navigate: true);

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Success',
            'text' => 'Settings Updated Successfully!',
        ]);
    }

    public function compressAndDownloadTheme()
    {
        $setting = Setting::find(1);
        $date = now()->format('d-m-Y');

        $sanitizedUrl = preg_replace('/[^A-Za-z0-9\-]/', '_', $setting->url);
        $themeName = $setting->active_theme;

        $publicThemePath = public_path("frontend/{$themeName}");
        $resourcesThemePath = base_path("resources/views/frontend/{$themeName}");

        $zipFileName = "compressed-Theme-{$themeName}-{$sanitizedUrl}-{$date}.zip";
        $zipFilePath = storage_path("app/public/{$zipFileName}");

        // Pastikan folder ada
        if (! File::exists(storage_path('app/public'))) {
            File::makeDirectory(storage_path('app/public'), 0775, true);
        }

        $tempZipPath = tempnam(sys_get_temp_dir(), 'theme_zip_');
        $zip = new \ZipArchive;

        if ($zip->open($tempZipPath, \ZipArchive::OVERWRITE) === true) {
            if (File::isDirectory($publicThemePath)) {
                foreach (File::allFiles($publicThemePath) as $file) {
                    $relative = 'public/frontend/'.$themeName.'/'.$file->getRelativePathname();
                    $zip->addFile($file->getRealPath(), $relative);
                }
            }

            if (File::isDirectory($resourcesThemePath)) {
                foreach (File::allFiles($resourcesThemePath) as $file) {
                    $relative = 'resources/views/frontend/'.$themeName.'/'.$file->getRelativePathname();
                    $zip->addFile($file->getRealPath(), $relative);
                }
            }

            $zip->close();
            clearstatcache();

            File::move($tempZipPath, $zipFilePath);

            if (! File::exists($zipFilePath)) {
                session()->flash('error', 'Compressed theme file was not created successfully.');

                return;
            }

            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        } else {
            session()->flash('error', 'Failed to create compressed theme file.');
        }
    }

    public function uploadAndInstall()
    {
        $this->validate([
            'theme_zip' => 'required|max:51200', // max 50MB
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

    /**
     * Save initial setting (used for first-time setup)
     * Creates a new Setting record with minimum required data
     */
    public function saveInitialSetting()
    {
        $this->validate([
            'setup_sitename' => 'required|string|max:255',
            'setup_url' => 'required|url',
            'setup_email' => 'required|email|max:255',
            'setup_language' => 'required|in:id,en',
            'setup_tagline' => 'nullable|string|max:255',
        ]);

        try {
            // Create new setting record
            Setting::create([
                'sitename' => $this->setup_sitename,
                'tagline' => $this->setup_tagline ?? '',
                'description' => '',
                'keywords' => '',
                'sitename_translation' => $this->setup_sitename,
                'tagline_translation' => $this->setup_tagline ?? '',
                'description_translation' => '',
                'keywords_translation' => '',
                'email' => $this->setup_email,
                'address' => '',
                'phone' => '',
                'whatsapp' => '',
                'facebook' => '',
                'twitter' => '',
                'instagram' => '',
                'linkedin' => '',
                'youtube' => '',
                'tiktok' => '',
                'url' => $this->setup_url,
                'language' => $this->setup_language,
                'is_multilingual' => 'No',
                'active_theme' => 'default',
                'homepage_type' => 'index',
                'date_format' => 'd/m/Y',
                'site_maintenance' => false,
            ]);

            // Reset modal and reload component
            $this->showSetupModal = false;
            $this->settingExists = true;

            // Dispatch success event
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Success',
                'text' => 'Initial settings created successfully! Redirecting...',
            ]);

            // Redirect to reload the page
            $this->redirect(route('admin.setting'), navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Failed to create settings: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Close setup modal
     */
    public function closeSetupModal()
    {
        $this->showSetupModal = false;
    }

    public function render()
    {
        return view('suryacms::livewire.setting-web')->layout('suryacms::layouts.app');
    }
}
