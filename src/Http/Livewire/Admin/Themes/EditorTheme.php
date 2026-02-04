<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\Themes;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Uiaciel\SuryaCms\Models\Setting;

class EditorTheme extends Component
{
    public $activeTheme = '';

    public $availableThemes = [];

    public $files = [];

    public $selectedFile = null;

    public $fileContent = '';

    public $fileLanguage = 'css';

    public $isSaving = false;

    public $saveMessage = '';

    public $errorMessage = '';

    public $currentLocation = 'public'; // 'public' or 'views'

    // Temporary file management (Locked to persist across component updates)
    #[Locked]
    public $tempDirId = null;

    private $tempFilePath = null;

    public function mount()
    {
        // Get active theme from database or config
        $this->activeTheme = $this->getActiveThemeFromSettings();

        // Get available themes (must exist in both public and views)
        $this->availableThemes = $this->getAvailableThemes();

        // If active theme is not in available themes, use first available
        if (! in_array($this->activeTheme, $this->availableThemes)) {
            $this->activeTheme = ! empty($this->availableThemes) ? $this->availableThemes[0] : 'default';
        }

        // Initialize temporary directory
        $this->initializeTempDirectory();

        $this->loadThemeFiles();
    }

    /**
     * Get active theme from database settings or config
     */
    private function getActiveThemeFromSettings(): string
    {
        try {
            if (Schema::hasTable('settings')) {
                $theme = Setting::value('active_theme');
                if ($theme) {
                    return $theme;
                }
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        return config('frontend.active', 'default');
    }

    /**
     * Get available themes that exist in both public and resources/views
     */
    private function getAvailableThemes(): array
    {
        $viewThemes = glob(resource_path('views/frontend/*'), GLOB_ONLYDIR);
        $publicThemes = glob(public_path('frontend/*'), GLOB_ONLYDIR);

        // Extract folder names only
        $viewThemes = array_map('basename', $viewThemes);
        $publicThemes = array_map('basename', $publicThemes);

        // Return only themes that exist in both locations
        $availableThemes = array_values(array_intersect($viewThemes, $publicThemes));

        return ! empty($availableThemes) ? $availableThemes : ['default'];
    }

    /**
     * Initialize temporary directory for file editing
     */
    private function initializeTempDirectory(): void
    {
        if (! $this->tempDirId) {
            $this->tempDirId = 'editor-theme-'.auth()->id().'-'.uniqid();
            // Also save to session as backup
            session(['editor_theme_tempDirId' => $this->tempDirId]);
        }

        $tempDir = storage_path('app'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.$this->tempDirId);

        if (! File::isDirectory($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        \Log::debug('Temp directory initialized', [
            'path' => $tempDir,
            'id' => $this->tempDirId,
            'sessionId' => session('editor_theme_tempDirId'),
        ]);
    }

    /**
     * Get temporary directory path (with lazy initialization)
     */
    private function getTempDir(): string
    {
        // Initialize if not yet set
        if (! $this->tempDirId) {
            // Try to restore from session first
            $sessionId = session('editor_theme_tempDirId');
            if ($sessionId) {
                $this->tempDirId = $sessionId;
                \Log::debug('getTempDir - restored tempDirId from session', ['tempDirId' => $this->tempDirId]);
            } else {
                \Log::debug('getTempDir - tempDirId not set, initializing', ['tempDirId' => $this->tempDirId]);
                $this->initializeTempDirectory();
            }
        }

        // Normalize path separators
        $tempDir = storage_path('app'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.$this->tempDirId);

        \Log::debug('getTempDir - checking directory', [
            'tempDirId' => $this->tempDirId,
            'tempDir' => $tempDir,
            'exists' => File::isDirectory($tempDir),
        ]);

        // Ensure directory exists (in case it was deleted externally)
        if (! File::isDirectory($tempDir)) {
            \Log::warning('getTempDir - directory missing, recreating', ['tempDir' => $tempDir]);
            File::makeDirectory($tempDir, 0755, true);
            \Log::debug('Temp directory recreated', ['path' => $tempDir]);
        }

        return $tempDir;
    }

    /**
     * Get temporary file path for a given file
     */
    private function getTempFilePath($originalPath): string
    {
        $tempDir = $this->getTempDir();
        // Create a safe filename from the path
        $safeFileName = str_replace(['/', '\\'], '_', $originalPath);
        $tempPath = $tempDir.DIRECTORY_SEPARATOR.$safeFileName;

        \Log::debug('getTempFilePath called', [
            'originalPath' => $originalPath,
            'safeFileName' => $safeFileName,
            'tempDir' => $tempDir,
            'tempPath' => $tempPath,
        ]);

        return $tempPath;
    }

    /**
     * Copy file from original location to temporary storage
     */
    private function copyToTemp($originalPath): bool
    {
        try {
            // Determine base path based on location
            if ($this->currentLocation === 'views') {
                $themePath = resource_path('views/frontend/'.$this->activeTheme);
            } else {
                $themePath = public_path('frontend/'.$this->activeTheme);
            }

            // Construct full path
            $fullPath = $themePath.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $originalPath);
            $fullPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $fullPath);

            // Security check
            $realThemePath = realpath($themePath);
            $realFullPath = realpath($fullPath);

            if ($realThemePath === false || $realFullPath === false) {
                return false;
            }

            if (strpos($realFullPath, $realThemePath) !== 0) {
                return false;
            }

            // Copy to temp
            $tempPath = $this->getTempFilePath($originalPath);
            $tempDirPath = dirname($tempPath);

            if (! File::isDirectory($tempDirPath)) {
                File::makeDirectory($tempDirPath, 0755, true);
            }

            File::copy($realFullPath, $tempPath);
            $this->tempFilePath = $tempPath;

            \Log::debug('File copied to temp', [
                'original' => $realFullPath,
                'temp' => $tempPath,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Error copying file to temp', [
                'error' => $e->getMessage(),
                'originalPath' => $originalPath,
            ]);

            return false;
        }
    }

    /**
     * Cleanup temporary files
     */
    private function cleanupTemp(): void
    {
        try {
            if ($this->tempDirId) {
                $tempDir = storage_path('app'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.$this->tempDirId);
                if (File::isDirectory($tempDir)) {
                    File::deleteDirectory($tempDir);
                    \Log::debug('Temp directory cleaned up', ['path' => $tempDir, 'id' => $this->tempDirId]);
                }
                $this->tempDirId = null;
            }
        } catch (\Exception $e) {
            \Log::warning('Error cleaning up temp directory', [
                'error' => $e->getMessage(),
                'tempDirId' => $this->tempDirId,
            ]);
        }
    }

    /**
     * Load all files from the active theme
     */
    public function loadThemeFiles()
    {
        // Validate that active theme exists in both locations
        if (! in_array($this->activeTheme, $this->getAvailableThemes())) {
            $this->errorMessage = "Tema '{$this->activeTheme}' tidak ditemukan atau tidak lengkap";
            $this->files = [];
            $this->selectedFile = null;
            $this->fileContent = '';

            return;
        }

        // Determine base path based on location
        if ($this->currentLocation === 'views') {
            $themePath = resource_path('views/frontend/'.$this->activeTheme);
        } else {
            $themePath = public_path('frontend/'.$this->activeTheme);
        }

        // Normalize theme path
        $themePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $themePath);

        if (! File::isDirectory($themePath)) {
            $this->errorMessage = "Path tema tidak ditemukan: {$themePath}";
            $this->files = [];
            $this->selectedFile = null;
            $this->fileContent = '';

            return;
        }

        $this->files = [];
        $this->errorMessage = '';

        // Scan all files recursively
        $allFiles = File::allFiles($themePath);

        foreach ($allFiles as $file) {
            $filePathname = $file->getPathname();

            // Skip binary files and build artifacts
            if ($this->shouldIncludeFile($file)) {
                // Double-check file still exists with realpath
                if (File::exists($filePathname) && realpath($filePathname) !== false) {
                    // Calculate relative path from theme root
                    $relativePath = str_replace($themePath.DIRECTORY_SEPARATOR, '', $filePathname);
                    $relativePath = str_replace('\\', '/', $relativePath);

                    $this->files[] = [
                        'path' => $relativePath,
                        'name' => $file->getFilename(),
                        'extension' => $file->getExtension(),
                        'size' => $file->getSize(),
                        'modified' => filemtime($filePathname),
                    ];
                }
            }
        }

        // Sort files by path
        usort($this->files, function ($a, $b) {
            return strcmp($a['path'], $b['path']);
        });

        // Select first file if available
        if (! empty($this->files)) {
            $this->selectFile($this->files[0]['path']);
        } else {
            $this->selectedFile = null;
            $this->fileContent = '';
            $this->errorMessage = 'Tidak ada file editable di tema ini';
        }
    }

    /**
     * Check if file should be included in editor
     */
    private function shouldIncludeFile($file)
    {
        $extension = $file->getExtension();
        $filename = $file->getFilename();
        $pathname = $file->getPathname();

        // Include editable files only
        $editableExtensions = ['css', 'js', 'html', 'php', 'json', 'xml', 'yaml', 'yml', 'md', 'txt', 'scss', 'sass'];

        // Exclude certain files
        $excludePatterns = ['.map', '.min.js', '.min.css', '.zip'];

        foreach ($excludePatterns as $pattern) {
            if (strpos($filename, $pattern) !== false) {
                return false;
            }
        }

        // Special handling for blade.php files
        if (strpos($filename, '.blade.php') !== false) {
            return true;
        }

        return in_array(strtolower($extension), $editableExtensions);
    }

    /**
     * Select a file to view/edit (copy to temp first)
     */
    public function selectFile($filePath)
    {
        // Sanitize file path - remove any directory traversal attempts
        $filePath = str_replace('..', '', $filePath);
        $filePath = trim($filePath);

        // Determine base path based on location
        if ($this->currentLocation === 'views') {
            $themePath = resource_path('views/frontend/'.$this->activeTheme);
        } else {
            $themePath = public_path('frontend/'.$this->activeTheme);
        }

        // Construct full path with proper directory separator
        $fullPath = $themePath.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $filePath);

        // Normalize path for consistent comparison
        $fullPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $fullPath);
        $themePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $themePath);

        // Security check: ensure file is within theme directory
        $realThemePath = realpath($themePath);
        $realFullPath = realpath($fullPath);

        if ($realThemePath === false) {
            $this->errorMessage = 'Direktori tema tidak valid';
            $this->selectedFile = null;
            $this->fileContent = '';

            return;
        }

        if ($realFullPath === false || strpos($realFullPath, $realThemePath) !== 0) {
            $this->errorMessage = "File path tidak valid atau di luar direktori tema: {$filePath}";
            $this->selectedFile = null;
            $this->fileContent = '';

            return;
        }

        // Validate file exists
        if (! File::exists($realFullPath)) {
            // Try to reload file list in case file was deleted
            $this->loadThemeFiles();

            if (! empty($this->selectedFile)) {
                // Successfully loaded another file
                return;
            }

            $this->errorMessage = "File tidak ditemukan: {$filePath}";
            $this->selectedFile = null;
            $this->fileContent = '';

            return;
        }

        // Validate file is readable
        if (! is_readable($realFullPath)) {
            $this->errorMessage = "File tidak dapat dibaca (permission denied): {$filePath}";
            $this->selectedFile = null;
            $this->fileContent = '';

            return;
        }

        // Validate file is a regular file
        if (! is_file($realFullPath)) {
            $this->errorMessage = "Path bukan file: {$filePath}";
            $this->selectedFile = null;
            $this->fileContent = '';

            return;
        }

        try {
            // Copy file to temporary storage
            if (! $this->copyToTemp($filePath)) {
                throw new \Exception('Gagal meng-copy file ke folder temporary');
            }

            // Read content from temporary file
            $tempPath = $this->getTempFilePath($filePath);
            $content = File::get($tempPath);

            $this->selectedFile = $filePath;
            $this->fileContent = $content;
            $this->fileLanguage = $this->detectLanguage($filePath);
            $this->errorMessage = '';
            $this->saveMessage = '';

            // Dispatch event untuk notify Alpine.js bahwa file telah berubah
            $this->dispatch('filechanged');

            \Log::debug('File selected and copied to temp', [
                'filePath' => $filePath,
                'selectedFile' => $this->selectedFile,
                'tempPath' => $tempPath,
                'tempPathExists' => File::exists($tempPath),
                'tempDirId' => $this->tempDirId,
                'contentLength' => strlen($content),
            ]);
        } catch (\Exception $e) {
            $this->errorMessage = 'Error membaca file: '.$e->getMessage();
            $this->selectedFile = null;
            $this->fileContent = '';
            \Log::error('selectFile error', [
                'filePath' => $filePath,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Detect file language for CodeMirror
     */
    private function detectLanguage($filePath)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        $languageMap = [
            'css' => 'css',
            'scss' => 'css',
            'sass' => 'sass',
            'js' => 'javascript',
            'html' => 'html',
            'php' => 'php',
            'blade' => 'php',
            'json' => 'json',
            'xml' => 'xml',
            'yaml' => 'yaml',
            'yml' => 'yaml',
            'md' => 'markdown',
            'txt' => 'text',
        ];

        return $languageMap[strtolower($extension)] ?? 'text';
    }

    /**
     * Update file content from editor (via Alpine.js entangle)
     * This is handled automatically by @entangle in Alpine.js
     */
    public function updated_fileContent($value)
    {
        // Auto-save dapat diaktifkan di sini jika diperlukan
        // Namun sesuai requirement, hanya save saat tombol di-klik
    }

    #[On('updateFileContent')]
    public function updateFileContent($content)
    {
        $this->fileContent = $content;
    }

    /**
     * Update file content directly (for quick sync before save)
     */
    public function updateContent($content)
    {
        \Log::debug('updateContent called with content length: '.strlen($content));
        $this->fileContent = $content;
    }

    /**
     * Save file changes: Backup original -> Replace with temp -> Cleanup
     */
    public function saveFile()
    {
        if (! $this->selectedFile || empty($this->fileContent)) {
            $this->errorMessage = 'Konten kosong atau file belum dipilih.';

            return;
        }

        $this->isSaving = true;

        try {
            // 1. Tentukan Path Asli
            $themePath = ($this->currentLocation === 'views')
                ? resource_path('views/frontend/'.$this->activeTheme)
                : public_path('frontend/'.$this->activeTheme);

            $originalPath = $themePath.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $this->selectedFile);

            // 2. Backup File (Safety First)
            if (File::exists($originalPath)) {
                File::copy($originalPath, $originalPath.'.bak');
            }

            // 3. Tulis Konten Langsung dari Properti $fileContent
            // Ini memastikan apa yang ada di editor itulah yang tersimpan
            File::put($originalPath, $this->fileContent);

            // 4. Update juga file di folder Temp (agar sinkron jika user lanjut mengedit)
            $tempPath = $this->getTempFilePath($this->selectedFile);
            File::put($tempPath, $this->fileContent);

            // Hapus backup setelah sukses
            if (File::exists($originalPath.'.bak')) {
                File::delete($originalPath.'.bak');
            }

            $this->saveMessage = "✓ File '{$this->selectedFile}' berhasil disimpan!";
            $this->isSaving = false;

        } catch (\Exception $e) {
            $this->errorMessage = 'Gagal menyimpan: '.$e->getMessage();
            // Restore dari backup jika gagal
            if (File::exists($originalPath.'.bak')) {
                File::move($originalPath.'.bak', $originalPath);
            }
        }
    }

    /**
     * Change active theme
     */
    public function changeTheme($theme)
    {
        // Cleanup old temp directory before switching
        $this->cleanupTemp();

        // Refresh available themes list
        $availableThemes = $this->getAvailableThemes();

        if (! in_array($theme, $availableThemes)) {
            $this->errorMessage = "Tema '{$theme}' tidak tersedia atau tidak lengkap";

            return;
        }

        $this->activeTheme = $theme;
        $this->selectedFile = null;
        $this->fileContent = '';
        $this->files = [];
        $this->errorMessage = '';

        // Initialize new temp directory for new theme
        $this->initializeTempDirectory();

        $this->loadThemeFiles();

        // Dispatch event untuk update Alpine.js
        $this->dispatch('filechanged');
    }

    /**
     * Create new file in theme
     */
    public function createNewFile($filename)
    {
        if (empty($filename)) {
            $this->errorMessage = 'Nama file tidak boleh kosong';

            return;
        }

        // Determine base path based on location
        if ($this->currentLocation === 'views') {
            $themePath = resource_path('views/frontend/'.$this->activeTheme);
        } else {
            $themePath = public_path('frontend/'.$this->activeTheme);
        }

        $fullPath = $themePath.'/'.str_replace('/', DIRECTORY_SEPARATOR, $filename);

        if (File::exists($fullPath)) {
            $this->errorMessage = "File sudah ada: {$filename}";

            return;
        }

        try {
            $directory = dirname($fullPath);
            if (! File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            File::put($fullPath, '');

            $this->loadThemeFiles();
            $this->selectFile($filename);
            $this->saveMessage = "✓ File baru '{$filename}' berhasil dibuat!";

        } catch (\Exception $e) {
            $this->errorMessage = 'Error membuat file: '.$e->getMessage();
        }
    }

    /**
     * Delete file
     */
    public function deleteFile($filePath)
    {
        // Determine base path based on location
        if ($this->currentLocation === 'views') {
            $themePath = resource_path('views/frontend/'.$this->activeTheme);
        } else {
            $themePath = public_path('frontend/'.$this->activeTheme);
        }

        $fullPath = $themePath.'/'.str_replace('/', DIRECTORY_SEPARATOR, $filePath);

        if (! File::exists($fullPath)) {
            $this->errorMessage = "File tidak ditemukan: {$filePath}";

            return;
        }

        try {
            File::delete($fullPath);
            $this->selectedFile = null;
            $this->fileContent = '';
            $this->loadThemeFiles();
            $this->saveMessage = "✓ File '{$filePath}' berhasil dihapus!";

        } catch (\Exception $e) {
            $this->errorMessage = 'Error menghapus file: '.$e->getMessage();
        }
    }

    /**
     * Switch file location (public/frontend vs resources/views/frontend)
     */
    public function switchLocation($location)
    {
        if (! in_array($location, ['public', 'views'])) {
            $this->errorMessage = 'Lokasi tidak valid';

            return;
        }

        // Cleanup old temp directory before switching
        $this->cleanupTemp();

        $this->currentLocation = $location;
        $this->selectedFile = null;
        $this->fileContent = '';
        $this->files = [];
        $this->errorMessage = '';
        $this->saveMessage = '';

        // Initialize new temp directory for new location
        $this->initializeTempDirectory();

        $this->loadThemeFiles();

        // Dispatch event untuk update Alpine.js
        $this->dispatch('filechanged');
    }

    public function render()
    {
        return view('suryacms::livewire.admin.themes.editor-theme')->layout('suryacms::layouts.app');
    }

    /**
     * Cleanup when component is destroyed
     */
    public function __destruct()
    {
        // Cleanup temp directory on destruction
        if ($this->tempDirId) {
            $this->cleanupTemp();
        }
    }
}
