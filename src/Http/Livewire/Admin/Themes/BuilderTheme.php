<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\Themes;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Uiaciel\SuryaCms\Services\FooterNormalizerService;
use Uiaciel\SuryaCms\Services\HeadNormalizerService;
use Uiaciel\SuryaCms\Services\ThemeLayoutGeneratorService;
use Uiaciel\SuryaCms\Services\ThemeNormalizerService;
use ZipArchive;

class BuilderTheme extends Component
{
    use WithFileUploads;

    // Wizard State
    public $step = 1; // 1 to 5

    // Step 1: Upload & Metadata
    public $themeName = '';
    public $indexFile = null;
    public $sessionId = '';
    public $tempPath = '';
    public $indexPreview = '';

    // Step 2: Component Selectors
    public $selectorNav = '';
    public $selectorFooter = '';
    public $selectorHero = '';

    // Step 3: Variables & Placeholder Mapping
    public $mappings = []; // array of ['original' => ..., 'replacement' => ..., 'enabled' => bool]

    // Step 4: Section Configurator
    public $sections = []; // array of ['id' => ..., 'label' => ..., 'type' => 'homepage'|'block'|'skip', 'content' => ...]

    // Step 5: Preview & Compile
    public $generatedFiles = []; // array of ['path' => ..., 'content' => ...]
    public $activePreviewFile = 'navigation.blade.php';
    public $editingFileContent = ''; // Current file being edited

    // HTML Contents
    public $htmlRaw = '';
    public $htmlNormalized = '';
    public $htmlOriginalContent = ''; // Original HTML before any modifications
    public $htmlSourcePreview = ''; // Original HTML for Step 5 reference
    public $allHtmlFiles = []; // All HTML files from ZIP: ['filename' => 'content']

    // Validation Errors list
    public $validationErrors = [];

    protected $rules = [
        1 => [
            'themeName' => 'required|string|max:50|alpha_dash',
            'indexFile' => 'required|file|mimes:zip|max:20480',
        ],
        2 => [
            'selectorNav' => 'required|string',
            'selectorFooter' => 'required|string',
        ],
    ];

    public function mount()
    {
        $this->sessionId = (string) Str::uuid();
        $this->tempPath = storage_path("app/temp/themes/builder/" . $this->sessionId);
        $this->indexPreview = asset("storage/temp/themes/builder/{$this->sessionId}/index.html");
    }

    public function nextStep()
    {
        $this->reset(['validationErrors']);

        if (isset($this->rules[$this->step])) {
            try {
                $this->validate($this->rules[$this->step]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                $this->validationErrors = $e->errors();
                throw $e;
            }
        }

        if ($this->step === 1) {
            $this->processUpload();
        } elseif ($this->step === 2) {
            $this->processSelectors();
        } elseif ($this->step === 3) {
            $this->processMappings();
        } elseif ($this->step === 4) {
            $this->processSections();
        }

        $this->step++;

        // When transitioning to Step 5, load first file for editing
        if ($this->step === 5) {
            $firstFile = array_key_first($this->generatedFiles);
            if ($firstFile) {
                $this->editingFileContent = $this->generatedFiles[$firstFile];
                $this->activePreviewFile = $firstFile;
            }
        }
    }

    public function prevStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    /**
     * Load file content for editing
     * Called when switching between files in Step 5
     */
    public function loadFileForEditing($filename)
    {
        if (isset($this->generatedFiles[$filename])) {
            $this->editingFileContent = $this->generatedFiles[$filename];
            $this->activePreviewFile = $filename;
        }
    }

    /**
     * Get selected HTML file from allHtmlFiles
     */
    public function getSelectedHtmlFile($filename)
    {
        if (isset($this->allHtmlFiles[$filename])) {
            return $this->allHtmlFiles[$filename]['content'];
        }
        return '';
    }

    /**
     * Step 1: Process upload & extract zip
     */
    private function processUpload()
    {
        try {
            $this->ensureDirectoryExists($this->tempPath);

            // Move uploaded ZIP file to temp directory
            $uploadedFilePath = $this->indexFile->getRealPath();
            $zipDestPath = "{$this->tempPath}/theme.zip";
            if (!copy($uploadedFilePath, $zipDestPath)) {
                throw new \Exception('Gagal menyalin file ZIP ke storage temp');
            }

            // Extract ZIP
            $zip = new ZipArchive;
            if ($zip->open($zipDestPath) === true) {
                $indexPath = null;

                // First pass: find index.html and collect all HTML files
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $name = $zip->getNameIndex($i);

                    // Find index.html
                    if (preg_match('/(^|\/)index\.html$/i', $name)) {
                        $indexPath = $name;
                    }

                    // Collect all HTML files
                    if (preg_match('/\.html?$/i', $name) && !$zip->statIndex($i)['size'] == 0) {
                        $content = $zip->getFromIndex($i);
                        $filename = basename($name);
                        $this->allHtmlFiles[$filename] = [
                            'path' => $name,
                            'content' => $content
                        ];
                    }
                }

                if (!$indexPath) {
                    throw new \Exception('index.html tidak ditemukan dalam file ZIP.');
                }

                $this->htmlRaw = $zip->getFromName($indexPath);

                // Store original content (before any modifications)
                $this->htmlOriginalContent = $this->htmlRaw;

                // Copy index.html to index-original.html in temp directory for reference
                $originalPath = "{$this->tempPath}/index-original.html";
                file_put_contents($originalPath, $this->htmlOriginalContent);

                $zip->close();
            } else {
                throw new \Exception('Gagal membuka file ZIP.');
            }

            // Auto detect selectors
            $this->detectSelectors();

        } catch (\Exception $e) {
            Log::error('Builder Upload failed: ' . $e->getMessage());
            $this->validationErrors = [$e->getMessage()];
            throw $e;
        }
    }

    /**
     * Auto detect main CSS selectors inside index.html
     */
    private function detectSelectors()
    {
        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        @$dom->loadHTML(
            mb_convert_encoding($this->htmlRaw, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);

        // 1. Detect Navigation
        $navNode = $xpath->query('//nav|//*[@id="navigation"]|//*[contains(@class, "navbar") or contains(@class, "menu")]')->item(0);
        if ($navNode) {
            $this->selectorNav = $navNode->hasAttribute('id') ? '#' . $navNode->getAttribute('id') : 'nav';
        } else {
            $this->selectorNav = 'nav';
        }

        // 2. Detect Footer
        $footerNode = $xpath->query('//footer|//*[@id="footer"]|//*[contains(@class, "footer")]')->item(0);
        if ($footerNode) {
            $this->selectorFooter = $footerNode->hasAttribute('id') ? '#' . $footerNode->getAttribute('id') : 'footer';
        } else {
            $this->selectorFooter = 'footer';
        }

        // 3. Detect Hero Section
        $heroNode = $xpath->query('//section[contains(@id, "hero") or contains(@id, "banner") or contains(@class, "hero") or contains(@class, "banner")]')->item(0);
        if ($heroNode) {
            $this->selectorHero = $heroNode->hasAttribute('id') ? '#' . $heroNode->getAttribute('id') : 'section:first-of-type';
        } else {
            $this->selectorHero = 'header';
        }
    }

    /**
     * Step 2: Inject IDs to matched elements & load placeholders
     */
    private function processSelectors()
    {
        try {
            $dom = new \DOMDocument;
            libxml_use_internal_errors(true);
            @$dom->loadHTML(
                mb_convert_encoding($this->htmlRaw, 'HTML-ENTITIES', 'UTF-8'),
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
            );
            libxml_clear_errors();
            $xpath = new \DOMXPath($dom);

            // Mark Navigation
            $navNodes = $xpath->query($this->cssToXpath($this->selectorNav));
            if ($navNodes->length > 0) {
                $navNodes->item(0)->setAttribute('id', 'navigation');
            }

            // Mark Footer
            $footerNodes = $xpath->query($this->cssToXpath($this->selectorFooter));
            if ($footerNodes->length > 0) {
                $footerNodes->item(0)->setAttribute('id', 'footer');
            }

            $this->htmlNormalized = $dom->saveHTML();

            // Detect variable mappings inside text nodes
            $this->mappings = $this->scanPlaceholders($this->htmlNormalized);

        } catch (\Exception $e) {
            Log::error('Builder Selector process failed: ' . $e->getMessage());
            $this->validationErrors = ['Format selector CSS tidak valid atau tidak ditemukan dalam dokumen: ' . $e->getMessage()];
            throw $e;
        }
    }

    /**
     * Map CSS selector string to XPath query
     */
    private function cssToXpath($selector)
    {
        $selector = trim($selector);
        if (strpos($selector, '#') === 0) {
            return '//*[@id="' . substr($selector, 1) . '"]';
        }
        if (strpos($selector, '.') === 0) {
            return '//*[contains(@class, "' . substr($selector, 1) . '")]';
        }
        return '//' . $selector;
    }

    /**
     * Scan HTML text nodes for customizable placeholders
     */
    private function scanPlaceholders($html)
    {
        $placeholders = [];

        // Scan for email addresses
        if (preg_match_all('/[\w\.-]+@[\w\.-]+\.\w+/', $html, $matches)) {
            foreach (array_unique($matches[0]) as $match) {
                $placeholders[$match] = '{{ $setting->email }}';
            }
        }

        // Scan for social links
        $socials = [
            'facebook.com' => '{{ $setting->facebook }}',
            'twitter.com' => '{{ $setting->twitter }}',
            'x.com' => '{{ $setting->twitter }}',
            'instagram.com' => '{{ $setting->instagram }}',
            'linkedin.com' => '{{ $setting->linkedin }}',
            'youtube.com' => '{{ $setting->youtube }}',
            'tiktok.com' => '{{ $setting->tiktok }}',
        ];

        foreach ($socials as $domain => $settingVar) {
            if (preg_match_all('/https?:\/\/(www\.)?' . preg_quote($domain, '/') . '\/[^\s"\'<>]+/i', $html, $matches)) {
                foreach (array_unique($matches[0]) as $match) {
                    $placeholders[$match] = $settingVar;
                }
            }
        }

        // Scan for copyright text
        if (preg_match('/copyright\s*©\s*[^<]+/i', $html, $matches)) {
            $placeholders[trim($matches[0])] = '© {{ date("Y") }} {{ $setting->sitename }}. All Rights Reserved.';
        }

        // Default words to scan
        $keywords = [
            'phone' => '{{ $setting->phone }}',
            'email' => '{{ $setting->email }}',
            'address' => '{{ $setting->address }}',
            'whatsapp' => '{{ $setting->whatsapp }}',
        ];

        foreach ($keywords as $word => $settingVar) {
            if (preg_match_all('/\b' . preg_quote($word, '/') . '\b/i', $html, $matches)) {
                $placeholders[$word] = $settingVar;
            }
        }

        $formatted = [];
        foreach ($placeholders as $original => $replacement) {
            $formatted[] = [
                'original' => $original,
                'replacement' => $replacement,
                'enabled' => true,
            ];
        }
        return $formatted;
    }

    /**
     * Step 3: Run placeholders mapping & prepare section extraction
     */
    private function processMappings()
    {
        $html = $this->htmlNormalized;

        // Apply only enabled mappings
        foreach ($this->mappings as $mapping) {
            if ($mapping['enabled']) {
                // Safely replace placeholders outside tags, script, and styles
                $pattern = '~<(script|style)[^>]*>.*?<\/\1>|<[^>]+>(*SKIP)(*F)|\b' . preg_quote($mapping['original'], '~') . '\b~is';
                $html = preg_replace($pattern, $mapping['replacement'], $html);
            }
        }

        $this->htmlNormalized = $html;

        // Store ORIGINAL HTML source for Step 5 reference (before any normalizer changes)
        // Use the original content extracted from ZIP
        $this->htmlSourcePreview = $this->htmlOriginalContent;

        // Run theme normalizer (for sections/id generation and relative assets)
        $normalizer = new ThemeNormalizerService($this->htmlNormalized, $this->themeName);
        $this->htmlNormalized = $normalizer->normalize();

        // Extract sections for config step
        $this->extractSections();
    }

    /**
     * Extract sections inside body to display on Step 4
     * Detects section elements, divs with section-like structure, articles, etc.
     */
    private function extractSections()
    {
        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $this->htmlNormalized, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);

        $this->sections = [];
        $counter = 1;
        $processedIds = [];

        // Query multiple element types that could be sections
        // 1. Explicit <section> tags
        // 2. <article> tags
        // 3. <div> tags with section-like class/id patterns
        // 4. <main> with direct children divs
        $query = "
            //body/section |
            //body/article |
            //body/div[contains(@class, 'section') or contains(@class, 'container') or contains(@class, 'block') or contains(@id, 'section')] |
            //body/div[count(.//h1|.//h2|.//h3) > 0]
        ";

        $elementsNodes = $xpath->query($query);

        if ($elementsNodes->length > 0) {
            foreach ($elementsNodes as $element) {
                $elementId = $element->getAttribute('id');

                // Skip if already processed (avoid duplicates)
                if ($elementId && in_array($elementId, $processedIds)) {
                    continue;
                }

                // Get heading as preview title
                $heading = $xpath->query('.//h1|.//h2|.//h3', $element)->item(0);
                $title = $heading ? trim($heading->textContent) : null;

                // Fallback: use first paragraph text or element type + counter
                if (!$title) {
                    $firstText = $xpath->query('.//p', $element)->item(0);
                    if ($firstText) {
                        $text = trim($firstText->textContent);
                        $title = strlen($text) > 50 ? substr($text, 0, 50) . '...' : $text;
                    } else {
                        $title = 'Section ' . $counter;
                    }
                }

                $id = $elementId ?: 'section-' . $counter;

                // Avoid duplicate IDs in processing
                if (in_array($id, $processedIds)) {
                    $id = 'section-' . $counter;
                }
                $processedIds[] = $id;

                // Get inner HTML content for config preview
                $nodeDom = new \DOMDocument;
                $nodeDom->appendChild($nodeDom->importNode($element, true));
                $content = preg_replace('/<\?xml[^>]*\?>/', '', $nodeDom->saveHTML());

                $this->sections[] = [
                    'id' => $id,
                    'label' => $title,
                    'type' => 'homepage', // 'homepage' | 'block' | 'skip'
                    'content' => trim($content),
                ];
                $counter++;
            }
        }
    }

    /**
     * Step 4: Compile Layout configs & Generate Preview files
     */
    private function processSections()
    {
        try {
            // Apply other services normalizations
            $html = $this->htmlNormalized;

            $footerNormalizer = new FooterNormalizerService($html, $this->themeName);
            $html = $footerNormalizer->normalize();

            $headNormalizer = new HeadNormalizerService($html, $this->themeName);
            $html = $headNormalizer->normalize();

            // Final arrow operator restoration
            $html = str_replace('-&gt;', '->', $html);
            $this->htmlNormalized = $html;

            // Generate Layout files using configurations
            $generator = new ThemeLayoutGeneratorService($this->htmlNormalized, $this->themeName);

            $this->generatedFiles = [
                'app.blade.php' => $generator->generate(),
                'navigation.blade.php' => $generator->generateNavigation(),
                'editor.blade.php' => $generator->generateEditor(),
                'homepage.blade.php' => $generator->generateHomepage(),
                'index.blade.php' => $generator->generateIndex(),
                'page/show.blade.php' => $generator->generatePageShow(),
                'page/post.blade.php' => $generator->generatePagePost(),
                'page/category.blade.php' => $generator->generatePageCategory(),
                'page/contact.blade.php' => $generator->generatePageContact(),
                'page/acc.blade.php' => $generator->generatePageReport('Accounting Reports'),
                'page/financial.blade.php' => $generator->generatePageReport('Financial Reports'),
                'page/share.blade.php' => $generator->generatePageReport('Share Reports'),
                'page/maintenance.blade.php' => $generator->generatePageMaintenance(),
                'theme.php' => "<?php\n\nreturn " . $generator->generateThemeConfig() . ';'
            ];

        } catch (\Exception $e) {
            Log::error('Builder generate failed: ' . $e->getMessage());
            $this->validationErrors = [$e->getMessage()];
            throw $e;
        }
    }

    /**
     * Step 5: Update a generated file content
     * Allow manual editing before finalization
     */
    public function updateGeneratedFile()
    {
        if (isset($this->generatedFiles[$this->activePreviewFile])) {
            $this->generatedFiles[$this->activePreviewFile] = $this->editingFileContent;
            session()->flash('message', "✅ File '{$this->activePreviewFile}' berhasil diperbarui.");
        }
    }

    /**
     * Step 5: Finalize Install Theme to Production views/public directory
     */
    public function installTheme()
    {
        try {
            $viewPath = resource_path("views/frontend/{$this->themeName}");
            $publicAssetPath = public_path("frontend/{$this->themeName}");

            // Create target folders
            $this->ensureDirectoryExists($viewPath);
            $this->ensureDirectoryExists("{$viewPath}/page");
            $this->ensureDirectoryExists("{$viewPath}/plugin");
            $this->ensureDirectoryExists($publicAssetPath);

            // Write all generated files
            foreach ($this->generatedFiles as $filePath => $content) {
                $targetFile = "{$viewPath}/{$filePath}";
                $this->ensureDirectoryExists(dirname($targetFile));
                File::put($targetFile, $content);
            }

            // Copy assets from Zip to public folder
            $zipPath = "{$this->tempPath}/theme.zip";
            if (file_exists($zipPath)) {
                $zip = new ZipArchive;
                if ($zip->open($zipPath) === true) {
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $filename = $zip->getNameIndex($i);

                        // Skip index.html
                        if (preg_match('/(^|\\/)(index\\.html|index\\.htm)$/i', $filename)) {
                            continue;
                        }

                        // Skip directories
                        if (substr($filename, -1) === '/') {
                            $dirPath = $publicAssetPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $filename);
                            $this->ensureDirectoryExists($dirPath);
                            continue;
                        }

                        $content = $zip->getFromIndex($i);
                        $filePath = $publicAssetPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $filename);

                        $this->ensureDirectoryExists(dirname($filePath));
                        file_put_contents($filePath, $content);
                    }
                    $zip->close();
                }
            }

            // Copy default plugins from default theme views
            $defaultThemePath = resource_path('views/frontend/default');
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
                    File::put("{$viewPath}/plugin/{$plugin}", $content);
                }
            }

            // Cleanup temp workspace
            $this->deleteDirectory($this->tempPath);

            session()->flash('message', "✅ Tema '{$this->themeName}' berhasil di-build dan di-install secara interaktif!");
            return redirect()->route('admin.themes.index');

        } catch (\Exception $e) {
            Log::error('Interactive Theme Builder installation failed: ' . $e->getMessage());
            $this->validationErrors = ['Gagal menginstall tema: ' . $e->getMessage()];
            session()->flash('error', $e->getMessage());
        }
    }

    private function ensureDirectoryExists($path)
    {
        if (!file_exists($path)) {
            if (!mkdir($path, 0755, true)) {
                throw new \Exception('Gagal membuat direktori: ' . $path);
            }
        }
    }

    private function deleteDirectory($path)
    {
        if (!file_exists($path)) {
            return true;
        }
        if (!is_dir($path)) {
            return unlink($path);
        }
        foreach (array_diff(scandir($path), ['.', '..']) as $item) {
            if (!$this->deleteDirectory("{$path}/{$item}")) {
                return false;
            }
        }
        return rmdir($path);
    }

    public function render()
    {
        return view('suryacms::livewire.admin.themes.builder-theme')->layout('suryacms::layouts.app');
    }
}
