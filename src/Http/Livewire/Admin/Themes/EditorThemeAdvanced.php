<?php

namespace Uiaciel\Themes\Livewire;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Livewire\Attributes\On;
use Livewire\Component;
use Uiaciel\SuryaCms\Services\ThemeEditorService;

class EditorThemeAdvanced extends Component
{
    public $activeTheme = '';

    public $availableThemes = [];

    public $files = [];

    public $selectedFile = null;

    public $fileContent = '';

    public $fileInfo = [];

    public $fileLanguage = 'css';

    public $isSaving = false;

    public $saveMessage = '';

    public $errorMessage = '';

    // Advanced features
    public $searchQuery = '';

    public $searchResults = [];

    public $isSearching = false;

    public $showFileInfo = false;

    public $showDiff = false;

    public $diffView = [];

    public function mount()
    {
        $this->activeTheme = config('frontend.active', 'default');
        $this->availableThemes = config('frontend.available_themes', []);
        $this->loadThemeFiles();
    }

    /**
     * Load theme files using service
     */
    public function loadThemeFiles()
    {
        try {
            $this->files = ThemeEditorService::scanThemeFiles($this->activeTheme);
            $this->errorMessage = '';
        } catch (\Exception $e) {
            $this->errorMessage = 'Error loading files: '.$e->getMessage();
        }
    }

    /**
     * Select file and load content
     */
    public function selectFile($filePath)
    {
        try {
            $this->fileContent = ThemeEditorService::getFileContent($this->activeTheme, $filePath);
            $this->selectedFile = $filePath;
            $this->fileLanguage = ThemeEditorService::getLanguageMode($filePath);
            $this->fileInfo = ThemeEditorService::getFileInfo($this->activeTheme, $filePath);
            $this->errorMessage = '';
            $this->saveMessage = '';
            $this->showFileInfo = false;
            $this->showDiff = false;

            $this->dispatch('fileChanged',
                content: $this->fileContent,
                language: $this->fileLanguage
            );

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    /**
     * Save file using service
     */
    public function saveFile()
    {
        if (! $this->selectedFile) {
            $this->errorMessage = 'No file selected';

            return;
        }

        $this->isSaving = true;

        try {
            ThemeEditorService::saveFileContent($this->activeTheme, $this->selectedFile, $this->fileContent);

            $this->isSaving = false;
            $this->saveMessage = "✓ File '{$this->selectedFile}' saved successfully!";
            $this->errorMessage = '';

            // Reload file info
            $this->fileInfo = ThemeEditorService::getFileInfo($this->activeTheme, $this->selectedFile);

            // Clear diff view
            $this->showDiff = false;

        } catch (\Exception $e) {
            $this->isSaving = false;
            $this->errorMessage = 'Error saving file: '.$e->getMessage();
        }
    }

    /**
     * Delete file
     */
    public function deleteFile($filePath)
    {
        try {
            ThemeEditorService::deleteFile($this->activeTheme, $filePath);

            $this->selectedFile = null;
            $this->fileContent = '';
            $this->loadThemeFiles();
            $this->saveMessage = "✓ File '{$filePath}' deleted!";

        } catch (\Exception $e) {
            $this->errorMessage = 'Error deleting file: '.$e->getMessage();
        }
    }

    /**
     * Create new file
     */
    public function createNewFile($filename)
    {
        if (empty($filename)) {
            $this->errorMessage = 'Filename cannot be empty';

            return;
        }

        try {
            ThemeEditorService::createFile($this->activeTheme, $filename);
            $this->loadThemeFiles();
            $this->selectFile($filename);
            $this->saveMessage = "✓ File '{$filename}' created!";

        } catch (\Exception $e) {
            $this->errorMessage = 'Error creating file: '.$e->getMessage();
        }
    }

    /**
     * Change theme
     */
    public function changeTheme($theme)
    {
        if (! in_array($theme, $this->availableThemes)) {
            $this->errorMessage = 'Theme not available';

            return;
        }

        $this->activeTheme = $theme;
        $this->selectedFile = null;
        $this->fileContent = '';
        $this->files = [];
        $this->loadThemeFiles();
    }

    /**
     * Update file content from editor
     */
    #[On('updateFileContent')]
    public function updateFileContent($content)
    {
        $this->fileContent = $content;
    }

    /**
     * Search files by content
     */
    public function searchFiles()
    {
        if (empty($this->searchQuery)) {
            $this->searchResults = [];

            return;
        }

        $this->isSearching = true;

        try {
            $this->searchResults = ThemeEditorService::searchFiles($this->activeTheme, $this->searchQuery);
        } catch (\Exception $e) {
            $this->errorMessage = 'Search error: '.$e->getMessage();
        }

        $this->isSearching = false;
    }

    /**
     * Toggle file info panel
     */
    public function toggleFileInfo()
    {
        $this->showFileInfo = ! $this->showFileInfo;
    }

    /**
     * Show file diff
     */
    public function showFileDiff()
    {
        if (! $this->selectedFile) {
            return;
        }

        try {
            $this->diffView = ThemeEditorService::getFileDiff($this->activeTheme, $this->selectedFile, $this->fileContent);
            $this->showDiff = ! $this->showDiff;
        } catch (\Exception $e) {
            $this->errorMessage = 'Error generating diff: '.$e->getMessage();
        }
    }

    /**
     * Download file
     */
    public function downloadFile()
    {
        if (! $this->selectedFile) {
            $this->errorMessage = 'No file selected';

            return;
        }

        try {
            $content = $this->fileContent;
            $filename = basename($this->selectedFile);

            return response()->streamDownload(
                fn () => print ($content),
                $filename,
                [
                    'Content-Type' => 'application/octet-stream',
                ]
            );
        } catch (\Exception $e) {
            $this->errorMessage = 'Error downloading file: '.$e->getMessage();
        }
    }

    /**
     * Clear theme cache
     */
    public function clearCache()
    {
        try {
            ThemeEditorService::clearCache($this->activeTheme);
            $this->loadThemeFiles();
            $this->saveMessage = '✓ Cache cleared!';
        } catch (\Exception $e) {
            $this->errorMessage = 'Error clearing cache: '.$e->getMessage();
        }
    }

    /**
     * Format code
     */
    public function formatCode()
    {
        if (! $this->selectedFile) {
            return;
        }

        try {
            $extension = pathinfo($this->selectedFile, PATHINFO_EXTENSION);

            // Basic formatting for JSON
            if ($extension === 'json') {
                $json = json_decode($this->fileContent, true);
                $this->fileContent = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            }

            $this->saveMessage = '✓ Code formatted!';
        } catch (\Exception $e) {
            $this->errorMessage = 'Error formatting code: '.$e->getMessage();
        }
    }

    /**
     * Copy to clipboard
     */
    public function copyToClipboard()
    {
        if (! $this->selectedFile) {
            return;
        }

        $this->dispatch('copyToClipboard', content: $this->fileContent);
        $this->saveMessage = '✓ Copied to clipboard!';
    }

    public function render()
    {
        return view('themes::livewire.editor-theme-advanced');
    }
}
