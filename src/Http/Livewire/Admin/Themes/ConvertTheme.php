<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\Themes;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Uiaciel\SuryaCms\Services\FooterNormalizerService;
use Uiaciel\SuryaCms\Services\HeadNormalizerService;
use Uiaciel\SuryaCms\Services\SettingMapperService;
use Uiaciel\SuryaCms\Services\ThemeLayoutGeneratorService;
use Uiaciel\SuryaCms\Services\ThemeNormalizerService;
use ZipArchive;

class ConvertTheme extends Component
{
    use WithFileUploads;

    // =====================================================================
    // View State
    // =====================================================================
    public $mode = 'list'; // 'list' atau 'editor'

    public $sessionId = '';

    // =====================================================================
    // Form Properties (Upload Form)
    // =====================================================================
    public $indexFile = null;

    public $themeName = '';

    // =====================================================================
    // State Properties (Editor State)
    // =====================================================================
    public $tempPath = '';

    public $htmlEdited = '';

    public $currentThemeName = '';

    public $isGenerated = false;

    // =====================================================================
    // List Properties
    // =====================================================================
    public $existingIndexFiles = [];

    // =====================================================================
    // Error & Message Properties
    // =====================================================================
    public $validationErrors = [];

    protected array $dataMap = [
        'address' => '{{$setting->address}}',
        'email' => '{{$setting->email}}',
        'phone' => '{{$setting->phone}}',
        'whatsapp' => '{{$setting->whatsapp}}',
        'copyright-text' => '© {{ date("Y") }} {{$setting->sitename}}. All Rights Reserved.',
        'facebook' => '{{$setting->facebook}}',
        'twitter' => '{{$setting->twitter}}',
        'instagram' => '{{$setting->instagram}}',
        'linkedin' => '{{$setting->linkedin}}',
        'youtube' => '{{$setting->youtube}}',
        'tiktok' => '{{$setting->tiktok}}',
    ];

    protected $rules = [
        'themeName' => 'required|string|max:50|alpha_dash',
        'indexFile' => 'required|file|mimes:zip|max:10120',
    ];

    /**
     * =====================================================================
     * Mount & Initialization
     * =====================================================================
     * Handles initial state based on route parameters
     */
    public function mount($sessionId = null)
    {
        // If sessionId provided in URL, load as editor
        if ($sessionId) {
            $this->loadExistingIndexFile($sessionId);
        } else {
            // Otherwise, show list
            $this->mode = 'list';
            $this->getExistingIndexFiles();
        }
    }

    /**
     * =====================================================================
     * LIST MODE: Get existing index files
     * =====================================================================
     * Scan storage/app/private/themes/temp for index.html files
     */
    public function getExistingIndexFiles()
    {
        try {
            $themesPath = storage_path('app/temp/themes');
            $files = [];

            if (! file_exists($themesPath)) {
                $this->existingIndexFiles = $files;

                return;
            }

            $directories = array_filter(
                scandir($themesPath),
                function ($item) use ($themesPath) {
                    return $item !== '.' && $item !== '..' && is_dir("$themesPath/$item");
                }
            );

            foreach ($directories as $dir) {
                $indexPath = "$themesPath/$dir/index.html";
                if (file_exists($indexPath)) {
                    // Try to get theme name from cache
                    $cached = cache()->get('theme_editor_'.$dir);
                    $themeName = $cached['themeName'] ?? ('Tema '.substr($dir, 0, 8));

                    // Check if theme is already generated
                    $isGenerated = file_exists("$themesPath/$dir/app.blade.php");

                    $files[] = [
                        'id' => (string) $dir,
                        'name' => (string) $themeName,
                        'created_at' => (int) filectime($indexPath),
                        'size' => (int) filesize($indexPath),
                        'is_generated' => (bool) $isGenerated,
                    ];
                }
            }

            // Sort by creation date (newest first)
            usort($files, function ($a, $b) {
                return $b['created_at'] - $a['created_at'];
            });

            $this->existingIndexFiles = $files;
        } catch (\Exception $e) {
            Log::error('Error loading existing index files: '.$e->getMessage());
            $this->existingIndexFiles = [];
        }
    }

    /**
     * =====================================================================
     * LIST MODE: Upload and prepare for editor
     * =====================================================================
     * Process: Validate → Extract → Normalize → Redirect to Editor
     */
    public function uploadAndPrepareEditor()
    {
        $this->validate();
        $this->reset(['validationErrors']);

        try {
            // 1. Create new session
            $sessionId = (string) Str::uuid();
            $tempPath = storage_path("app/temp/themes/{$sessionId}");
            $this->sessionId = $sessionId;
            $this->tempPath = $tempPath;
            $this->currentThemeName = (string) $this->themeName;

            // 2. Setup Directory & Extract
            $this->prepareDirectory($tempPath);
            $htmlContent = $this->extractIndexHtml($tempPath);

            // 3. Normalization Pipeline
            $finalHtml = $this->runNormalizationPipeline($htmlContent);

            // 4. Save to storage
            $this->htmlEdited = (string) $finalHtml;
            $this->ensureDirectoryExists($tempPath);
            file_put_contents("{$tempPath}/index.html", $finalHtml);

            // 5. Save to cache
            $this->saveToCache($sessionId, $finalHtml);

            // 6. Clear form
            $this->reset(['indexFile', 'themeName']);

            // 7. Switch to editor mode
            $this->mode = 'editor';
            $this->isGenerated = false;

            session()->flash('message', 'Tema berhasil dinormalisasi! Anda sekarang bisa mengedit atau generate tema.');

            // 8. Dispatch event untuk Alpine
            $this->dispatch('theme-loaded', [
                'content' => $finalHtml,
                'themeName' => $this->currentThemeName,
            ]);

        } catch (\Exception $e) {
            $this->validationErrors = [(string) $e->getMessage()];
            session()->flash('error', 'Gagal: '.$e->getMessage());
        }
    }

    /**
     * =====================================================================
     * EDITOR MODE: Load existing index file
     * =====================================================================
     * Load a theme from existing temp directory
     */
    public function loadExistingIndexFile($sessionId)
    {
        try {
            $sessionId = (string) $sessionId;
            $tempPath = storage_path("app/temp/themes/{$sessionId}");
            $indexPath = "{$tempPath}/index.html";

            if (! file_exists($indexPath)) {
                throw new \Exception('File theme tidak ditemukan');
            }

            // Load content
            $this->sessionId = $sessionId;
            $this->tempPath = $tempPath;
            $this->htmlEdited = (string) file_get_contents($indexPath);

            // Get theme name from cache
            $cached = cache()->get('theme_editor_'.$sessionId);
            $this->currentThemeName = (string) ($cached['themeName'] ?? ('Tema '.substr($sessionId, 0, 8)));

            // Check if already generated
            $this->isGenerated = file_exists("{$tempPath}/app.blade.php");

            // Switch to editor mode
            $this->mode = 'editor';
            $this->validationErrors = [];

            // Dispatch event
            $this->dispatch('theme-loaded', [
                'content' => $this->htmlEdited,
                'themeName' => $this->currentThemeName,
            ]);

            session()->flash('message', "Tema '{$this->currentThemeName}' berhasil dimuat!");

        } catch (\Exception $e) {
            $this->validationErrors = [(string) $e->getMessage()];
            session()->flash('error', 'Gagal memuat tema: '.$e->getMessage());
        }
    }

    /**
     * =====================================================================
     * EDITOR MODE: Save HTML changes
     * =====================================================================
     * Save current editor content to file
     */
    public function saveHtmlChanges()
    {
        if (! $this->tempPath) {
            session()->flash('error', 'Sesi kedaluwarsa');

            return;
        }

        try {
            $indexPath = "{$this->tempPath}/index.html";
            file_put_contents($indexPath, (string) $this->htmlEdited);
            session()->flash('message', 'Perubahan berhasil disimpan!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan: '.$e->getMessage());
        }
    }

    /**
     * =====================================================================
     * EDITOR MODE: Generate theme files (blade files only)
     * =====================================================================
     * Create: app.blade.php, editor.blade.php, homepage.blade.php,
     *         index.blade.php, theme.php config, pages, plugins
     */
    public function generateTheme()
    {
        try {
            if (! $this->tempPath || empty($this->htmlEdited)) {
                throw new \Exception('Konten editor kosong atau sesi kedaluwarsa.');
            }

            $generator = new ThemeLayoutGeneratorService($this->htmlEdited, $this->currentThemeName);

            // Save generated blade files to temp storage
            $this->ensureDirectoryExists($this->tempPath);
            file_put_contents("{$this->tempPath}/app.blade.php", $generator->generate());
            file_put_contents("{$this->tempPath}/editor.blade.php", $generator->generateEditor());
            file_put_contents("{$this->tempPath}/homepage.blade.php", $generator->generateHomepage());
            file_put_contents("{$this->tempPath}/index.blade.php", $generator->generateIndex());
            file_put_contents("{$this->tempPath}/theme.php", "<?php\n\nreturn ".$generator->generateThemeConfig().';');

            // Copy page and plugin files
            $sessionId = basename($this->tempPath);
            $this->copyDefaultPagePlugins($sessionId);

            $this->isGenerated = true;

            session()->flash('message', "✅ File tema '{$this->currentThemeName}' berhasil di-generate! Sekarang Anda bisa menginstall tema.");
            $this->dispatch('generation-completed');

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal Generate: '.$e->getMessage());
            Log::error('Error generating theme: '.$e->getMessage());
        }
    }

    /**
     * =====================================================================
     * EDITOR MODE: Install theme to production
     * =====================================================================
     * Move assets to public/frontend/{themeName}
     * Move blade files to resources/views/frontend/{themeName}
     */
    public function installTheme()
    {
        try {
            if (! $this->isGenerated) {
                throw new \Exception('Silakan generate tema terlebih dahulu');
            }

            if (! $this->tempPath) {
                throw new \Exception('Sesi kedaluwarsa');
            }

            $sessionId = basename($this->tempPath);

            // 1. Copy assets from ZIP to public/frontend/{themeName}
            $this->copyAssetsFromZipToPublic($sessionId);

            // 2. Copy blade files from storage to resources/views/frontend/{themeName}
            $this->copyBladesToViewsFolder($sessionId);

            // 3. Success message and redirect back to list
            session()->flash('message', "✅ Tema '{$this->currentThemeName}' berhasil diinstall!");

            // Cleanup temp files (optional - keep for edit later)
            // $this->cleanupTempFiles($sessionId);

            // Switch back to list mode
            $this->mode = 'list';
            $this->reset(['tempPath', 'htmlEdited', 'sessionId', 'currentThemeName', 'isGenerated']);
            $this->getExistingIndexFiles();

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal Install: '.$e->getMessage());
            Log::error('Error installing theme: '.$e->getMessage());
        }
    }

    /**
     * =====================================================================
     * LIST MODE: Delete theme
     * =====================================================================
     */
    public function deleteFile($sessionId)
    {
        try {
            $sessionId = (string) $sessionId;
            $tempPath = storage_path("app/temp/themes/{$sessionId}");

            // Delete from temp storage
            if (file_exists($tempPath)) {
                $this->deleteDirectory($tempPath);
            }

            // Clear cache
            cache()->forget('theme_editor_'.$sessionId);

            $this->getExistingIndexFiles();
            session()->flash('message', 'Tema berhasil dihapus!');

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus tema: '.$e->getMessage());
        }
    }

    /**
     * =====================================================================
     * EDITOR MODE: Switch back to list
     * =====================================================================
     */
    public function switchToList()
    {
        $this->mode = 'list';
        $this->reset(['tempPath', 'htmlEdited', 'sessionId', 'currentThemeName', 'isGenerated']);
        $this->getExistingIndexFiles();
    }

    /**
     * =====================================================================
     * PRIVATE HELPERS
     * =====================================================================
     */

    /**
     * Prepare directory and extract ZIP
     */
    private function prepareDirectory($tempPath)
    {
        $this->ensureDirectoryExists($tempPath);
        $zipContent = file_get_contents($this->indexFile->getRealPath());
        file_put_contents("{$tempPath}/theme.zip", $zipContent);
    }

    /**
     * Extract index.html from ZIP
     */
    private function extractIndexHtml($tempPath)
    {
        $zip = new ZipArchive;
        $zipPath = "{$tempPath}/theme.zip";

        if ($zip->open($zipPath) === true) {
            $indexPath = null;
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (preg_match('/(^|\/)index\.html$/i', $name)) {
                    $indexPath = $name;
                    break;
                }
            }

            if (! $indexPath) {
                throw new \Exception('index.html tidak ditemukan dalam ZIP.');
            }

            $content = $zip->getFromName($indexPath);
            $zip->close();

            return $content;
        }
        throw new \Exception('Gagal membuka ZIP.');
    }

    /**
     * Run normalization pipeline
     */
    private function runNormalizationPipeline($rawHtml)
    {
        $this->validateBasicHtml($rawHtml);

        // Step 1: Mapping Database Variables
        $mapper = new SettingMapperService($rawHtml, $this->dataMap);
        $html = $mapper->map();

        // Step 2: Normalize Body
        $bodyNormalizer = new ThemeNormalizerService($html);
        $html = $bodyNormalizer->normalize();

        // Step 3: Normalize Footer/Scripts
        $footerNormalizer = new FooterNormalizerService($html, $this->currentThemeName);
        $html = $footerNormalizer->normalize();

        // Step 4: Normalize Head/Styles
        $headNormalizer = new HeadNormalizerService($html, $this->currentThemeName);
        $html = $headNormalizer->normalize();

        return $html;
    }

    /**
     * Validate basic HTML structure
     */
    private function validateBasicHtml($htmlContent)
    {
        if (empty(trim($htmlContent))) {
            throw new \Exception('File HTML kosong');
        }

        if (! preg_match('/<html[^>]*>/i', $htmlContent)) {
            throw new \Exception('File harus memiliki tag <html>');
        }

        if (! preg_match('/<body[^>]*>/i', $htmlContent)) {
            throw new \Exception('File harus memiliki tag <body>');
        }
    }

    /**
     * Save to cache
     */
    private function saveToCache($sessionId, $content)
    {
        cache()->put('theme_editor_'.$sessionId, [
            'normalized' => $content,
            'themeName' => $this->currentThemeName,
            'tempPath' => storage_path("app/temp/themes/{$sessionId}"),
        ], now()->addHours(3));
    }

    /**
     * Copy default page and plugin files from default theme
     */
    private function copyDefaultPagePlugins($sessionId)
    {
        try {
            // Source: default theme location
            $defaultThemePath = resource_path('views/frontend/default');
            $tempPath = storage_path("app/temp/themes/{$sessionId}");

            // 1. Create page dan plugin directories
            $this->ensureDirectoryExists("{$tempPath}/page");
            $this->ensureDirectoryExists("{$tempPath}/plugin");

            // 2. Copy simple pages
            $simplePages = [
                'share.blade.php',
                'acc.blade.php',
                'financial.blade.php',
                'maintenance.blade.php',
                'post.blade.php',
                'category.blade.php',
                'contact.blade.php',
                'show.blade.php',
            ];

            foreach ($simplePages as $page) {
                $sourcePath = "{$defaultThemePath}/page/{$page}";
                if (File::exists($sourcePath)) {
                    $content = File::get($sourcePath);
                    file_put_contents("{$tempPath}/page/{$page}", $content);
                }
            }

            // 3. Copy simple plugins
            $simplePlugins = [
                'announcement.blade.php',
                'slider.blade.php',
                'youtube.blade.php',
                'report.blade.php',
                'stock.blade.php',
                'blog.blade.php',
                'gallery.blade.php',
            ];

            foreach ($simplePlugins as $plugin) {
                $sourcePath = "{$defaultThemePath}/plugin/{$plugin}";
                if (File::exists($sourcePath)) {
                    $content = File::get($sourcePath);
                    file_put_contents("{$tempPath}/plugin/{$plugin}", $content);
                }
            }

            Log::info('Pages and plugins copied successfully');

        } catch (\Exception $e) {
            Log::error('Error copying default page/plugin files: '.$e->getMessage());
        }
    }

    /**
     * Copy assets from ZIP to public/frontend/{themeName}
     */
    private function copyAssetsFromZipToPublic($sessionId)
    {
        try {
            $tempPath = storage_path("app/temp/themes/{$sessionId}");
            $zipPath = "{$tempPath}/theme.zip";
            $publicAssetPath = public_path('frontend'.DIRECTORY_SEPARATOR.$this->currentThemeName);

            if (! file_exists($zipPath)) {
                Log::warning('Theme ZIP file not found for assets extraction', ['zipPath' => $zipPath]);

                return;
            }

            // Create destination directory
            if (! file_exists($publicAssetPath)) {
                mkdir($publicAssetPath, 0755, true);
            }

            // Extract dari ZIP
            $zip = new ZipArchive;
            if ($zip->open($zipPath) === true) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);

                    // Skip index.html
                    if (preg_match('/(^|\\/)(index\\.html|index\\.htm)$/i', $filename)) {
                        continue;
                    }

                    // Skip directory itself
                    if (substr($filename, -1) === '/') {
                        $dirPath = $publicAssetPath.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $filename);
                        if (! file_exists($dirPath)) {
                            mkdir($dirPath, 0755, true);
                        }

                        continue;
                    }

                    // Extract file
                    $content = $zip->getFromIndex($i);
                    $filePath = $publicAssetPath.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $filename);

                    // Create subdirectory if needed
                    $fileDir = dirname($filePath);
                    if (! file_exists($fileDir)) {
                        mkdir($fileDir, 0755, true);
                    }

                    // Write file
                    file_put_contents($filePath, $content);
                }
                $zip->close();

                Log::info('Assets copied to public folder', [
                    'theme_name' => $this->currentThemeName,
                    'asset_path' => $publicAssetPath,
                ]);

            } else {
                Log::warning('Failed to open ZIP for assets extraction');
            }

        } catch (\Exception $e) {
            Log::error('Error copying assets from ZIP: '.$e->getMessage());
            throw new \Exception('Gagal mengekstrak assets dari ZIP: '.$e->getMessage());
        }
    }

    /**
     * Copy blade files from storage to resources/views/frontend/{themeName}
     */
    private function copyBladesToViewsFolder($sessionId)
    {
        try {
            $tempPath = storage_path("app/temp/themes/{$sessionId}");
            $viewPath = resource_path("views/frontend/{$this->currentThemeName}");

            // Create theme view folder
            if (! file_exists($viewPath)) {
                mkdir($viewPath, 0755, true);
            }

            // List of blade files to copy
            $bladeFiles = [
                'app.blade.php',
                'editor.blade.php',
                'homepage.blade.php',
                'index.blade.php',
            ];

            foreach ($bladeFiles as $file) {
                $srcFile = "{$tempPath}/{$file}";
                if (file_exists($srcFile)) {
                    $content = file_get_contents($srcFile);
                    File::put("{$viewPath}/{$file}", $content);
                }
            }

            // Copy theme.php config
            $themeConfigSrc = "{$tempPath}/theme.php";
            if (file_exists($themeConfigSrc)) {
                $content = file_get_contents($themeConfigSrc);
                File::put("{$viewPath}/theme.php", $content);
            }

            // Copy page and plugin directories
            $subDirs = ['page', 'plugin'];
            foreach ($subDirs as $subDir) {
                $srcDir = "{$tempPath}/{$subDir}";
                $destDir = "{$viewPath}/{$subDir}";

                if (file_exists($srcDir)) {
                    if (! file_exists($destDir)) {
                        mkdir($destDir, 0755, true);
                    }

                    // Copy all files from subdirectory
                    $files = array_diff(scandir($srcDir), ['.', '..']);
                    foreach ($files as $file) {
                        if (! is_dir("{$srcDir}/{$file}")) {
                            $content = file_get_contents("{$srcDir}/{$file}");
                            File::put("{$destDir}/{$file}", $content);
                        }
                    }
                }
            }

            Log::info('Blade files copied to views folder', [
                'theme_name' => $this->currentThemeName,
                'view_path' => $viewPath,
            ]);

        } catch (\Exception $e) {
            Log::error('Error copying blade files: '.$e->getMessage());
            throw new \Exception('Gagal menyalin file blade ke views folder: '.$e->getMessage());
        }
    }

    /**
     * Cleanup temp files (optional)
     */
    private function cleanupTempFiles($sessionId)
    {
        try {
            $tempPath = storage_path("app/temp/themes/{$sessionId}");
            if (file_exists($tempPath)) {
                $this->deleteDirectory($tempPath);
            }
            cache()->forget('theme_editor_'.$sessionId);
        } catch (\Exception $e) {
            Log::error('Error cleaning up temp files: '.$e->getMessage());
        }
    }

    /**
     * Helper: Ensure directory exists
     */
    private function ensureDirectoryExists($path)
    {
        if (! file_exists($path)) {
            mkdir($path, 0755, true);
        }
    }

    /**
     * Helper: Recursively delete directory
     */
    private function deleteDirectory($path)
    {
        if (! file_exists($path)) {
            return true;
        }

        if (! is_dir($path)) {
            return unlink($path);
        }

        foreach (array_diff(scandir($path), ['.', '..']) as $item) {
            if (! $this->deleteDirectory("{$path}/{$item}")) {
                return false;
            }
        }

        return rmdir($path);
    }

    public function render()
    {
        return view('suryacms::livewire.admin.themes.convert-theme')->layout('suryacms::layouts.app');
    }
}
