<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\Themes;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Uiaciel\SuryaCms\Models\Setting;
use Uiaciel\SuryaCms\Services\FooterNormalizerService;
use Uiaciel\SuryaCms\Services\HeadNormalizerService;
use Uiaciel\SuryaCms\Services\SettingMapperService;
use Uiaciel\SuryaCms\Services\ThemeLayoutGeneratorService;
use Uiaciel\SuryaCms\Services\ThemeNormalizerService;
use Uiaciel\SuryaCms\Services\ThemeSnippetExtractorService;
use ZipArchive;

class CreateTheme extends Component
{
    use WithFileUploads;

    public string $themeName = 'my-awesome-theme';
    public string $author = 'uiaciel';
    public string $themeVersion = '1.0.0';
    public string $styleType = 'bootstrap';

    public $zipFile = null;
    public bool $zipExtracted = false;
    public string $uploadStatus = '';
    public string $uploadMessage = '';
    public array $zipInfo = [
        'files' => 0,
        'size' => 0,
        'html' => 0,
        'css' => 0,
        'js' => 0,
    ];

    public string $activeTab = 'upload';
    public string $selectedGeneratedFile = 'app.blade.php';
    public string $selectedVariableGroup = 'page';
    public string $activeBottomTab = 'app.blade.php';
    public string $sessionId = '';
    public string $tempPath = '';
    public string $htmlRaw = '';
    public string $htmlOriginal = '';
    public string $editingFileContent = '';

    public array $assetSnippets = [];
    public array $fileTree = [];
    public array $fileContents = [];
    public array $openTabs = [];
    public array $exportValidation = [];

    protected array $dataMap = [
        'address' => '{{$setting->address}}',
        'email' => '{{$setting->email}}',
        'phone' => '{{$setting->phone}}',
        'whatsapp' => '{{$setting->whatsapp}}',
        'facebook' => '{{$setting->facebook}}',
        'twitter' => '{{$setting->twitter}}',
        'instagram' => '{{$setting->instagram}}',
        'linkedin' => '{{$setting->linkedin}}',
        'youtube' => '{{$setting->youtube}}',
        'copyright-text' => '{{ date("Y") }} {{$setting->sitename}}. All Rights Reserved.',
    ];

    public array $variableGroups = [
        'page' => [
            '{{ $page->title }}' => 'Judul halaman',
            '{!! $page->content !!}' => 'Konten utama halaman',
            '{{ $page->image }}' => 'Gambar halaman',
            '{{ $seo->title ?? $page->title }}' => 'Judul SEO halaman',
        ],
        'post' => [
            '{{ $post->title }}' => 'Judul artikel',
            '{!! $post->content !!}' => 'Konten artikel',
            '{{ $post->excerpt }}' => 'Ringkasan artikel',
            '{{ $post->created_at->format("d M Y") }}' => 'Tanggal artikel',
        ],
        'menu' => [
            '{{ $menus }}' => 'Collection menu',
            '{{ $menu->name }}' => 'Nama menu',
            '{{ $menu->link ?? "/" }}' => 'Link menu',
            '{{ $menu->children }}' => 'Submenu',
        ],
        'site' => [
            '{{ $setting->sitename }}' => 'Nama website',
            '{{ $setting->description }}' => 'Deskripsi website',
            '{{ $setting->logo }}' => 'Logo website',
            '{{ $setting->email }}' => 'Email kontak',
            '{{ $setting->phone }}' => 'Nomor telepon',
            '{{ $setting->address }}' => 'Alamat',
        ],
        'gallery' => [
            '{{ $gallery->title }}' => 'Judul galeri',
            '{{ $gallery->image }}' => 'Gambar galeri',
        ],
        'contact' => [
            '{{ $contact->name }}' => 'Nama pengirim',
            '{{ $contact->email }}' => 'Email pengirim',
            '{{ $contact->message }}' => 'Pesan kontak',
        ],
    ];

    protected $rules = [
        'themeName' => 'required|string|max:80',
        'author' => 'required|string|max:80',
        'themeVersion' => 'required|string|max:30',
        'styleType' => 'required|string|max:40',
        'zipFile' => 'required|file|mimes:zip|max:51200',
    ];

    public function mount(?string $theme = null): void
    {
        if ($theme) {
            $this->loadExistingTheme($theme);
            return;
        }

        $this->refreshFromLatestSession();
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function selectVariableGroup(string $group): void
    {
        $this->selectedVariableGroup = $group;
    }

    public function fakeUpload(): void
    {
        $this->uploadZip();
    }

    public function uploadZip(): void
    {
        $this->validate();
        $slug = $this->themeSlug();
        $this->sessionId = $slug.'-'.Str::lower(Str::random(8));
        $this->tempPath = storage_path("app/temp/themes/{$this->sessionId}");

        try {
            $this->resetBuilderOutput();
            $this->ensureDirectory($this->tempPath);

            $zipPath = $this->tempPath.DIRECTORY_SEPARATOR.'theme.zip';
            if (! copy($this->zipFile->getRealPath(), $zipPath)) {
                throw new \RuntimeException('Gagal menyalin ZIP ke folder temp.');
            }

            $extractPath = $this->tempPath.DIRECTORY_SEPARATOR.'source';
            $this->ensureDirectory($extractPath);
            $indexPath = $this->extractZip($zipPath, $extractPath);
            $sourceRoot = dirname($indexPath);
            $this->copyDirectoryContents($sourceRoot, $this->tempPath, ['theme.zip', 'source']);

            $indexTarget = $this->tempPath.DIRECTORY_SEPARATOR.'index.html';
            $originalTarget = $this->tempPath.DIRECTORY_SEPARATOR.'original-index.html';
            File::copy($indexPath, $indexTarget);
            File::copy($indexPath, $originalTarget);

            $this->htmlRaw = File::get($indexTarget);
            $this->htmlOriginal = File::get($originalTarget);
            $this->zipInfo = $this->buildZipInfo($this->tempPath, $zipPath);
            $this->assetSnippets = $this->extractAssetSnippets($this->htmlRaw);
            $this->zipExtracted = true;
            $this->uploadStatus = 'success';
            $this->uploadMessage = 'ZIP berhasil diekstrak. index.html dan original-index.html sudah dibuat.';
            $this->activeTab = 'upload';

            $this->saveSessionMeta(false);
        } catch (\Throwable $e) {
            $this->uploadStatus = 'error';
            $this->uploadMessage = $e->getMessage();
            Log::error('Create theme upload failed', ['error' => $e->getMessage()]);
        }
    }

    public function saveRawHtml(): void
    {
        if (! $this->tempPath) {
            $this->uploadStatus = 'error';
            $this->uploadMessage = 'Belum ada ZIP yang diekstrak.';
            return;
        }

        File::put($this->tempPath.DIRECTORY_SEPARATOR.'index.html', $this->htmlRaw);
        $this->assetSnippets = $this->extractAssetSnippets($this->htmlRaw);
        $this->uploadStatus = 'success';
        $this->uploadMessage = 'index.html berhasil disimpan.';
        $this->saveSessionMeta(false);
    }

    public function saveCurrentEditor(): void
    {
        if ($this->activeTab === 'generated') {
            $this->saveGeneratedFile();
            return;
        }

        $this->saveRawHtml();
    }

    public function generateTheme(): void
    {
        if (! $this->tempPath || ! File::exists($this->tempPath.DIRECTORY_SEPARATOR.'index.html')) {
            $this->uploadStatus = 'error';
            $this->uploadMessage = 'Upload dan review index.html terlebih dahulu.';
            return;
        }

        try {
            $this->saveRawHtml();
            $theme = $this->themeSlug();
            $publicThemePath = public_path("frontend/{$theme}");
            $viewThemePath = resource_path("views/frontend/{$theme}");

            $normalized = $this->normalizeHtml($this->htmlRaw, $theme);
            File::put($this->tempPath.DIRECTORY_SEPARATOR.'index.html', $normalized);

            $this->ensureDirectory($publicThemePath);
            $this->copyAssetsToPublic($this->tempPath, $publicThemePath);

            $this->ensureDirectory($viewThemePath);
            $generator = new ThemeLayoutGeneratorService($normalized, $theme);
            File::put("{$viewThemePath}/app.blade.php", $this->formatGeneratedFileContent('app.blade.php', $this->normalizeGeneratedAppBlade($generator->generate(), $theme)));
            File::put("{$viewThemePath}/navigation.blade.php", $this->formatGeneratedFileContent('navigation.blade.php', $generator->generateNavigation()));
            File::put("{$viewThemePath}/editor.blade.php", $this->formatGeneratedFileContent('editor.blade.php', $generator->generateEditor()));
            File::put("{$viewThemePath}/homepage.blade.php", $this->formatGeneratedFileContent('homepage.blade.php', $generator->generateHomepage()));
            File::put("{$viewThemePath}/index.blade.php", $this->formatGeneratedFileContent('index.blade.php', $generator->generateIndex()));
            File::put("{$viewThemePath}/theme.php", $this->formatGeneratedFileContent('theme.php', "<?php\n\nreturn ".$generator->generateThemeConfig().';'));

            $this->writeGeneratedSupportFiles($generator, $viewThemePath);
            $this->loadGeneratedFiles();
            $this->buildExportValidation();
            $this->activeTab = 'generated';
            $this->uploadStatus = 'success';
            $this->uploadMessage = "Tema {$theme} berhasil digenerate.";
            $this->saveSessionMeta(true);
        } catch (\Throwable $e) {
            $this->uploadStatus = 'error';
            $this->uploadMessage = 'Generate gagal: '.$e->getMessage();
            Log::error('Create theme generate failed', ['error' => $e->getMessage()]);
        }
    }

    public function selectGeneratedFile(string $filename): void
    {
        if (! isset($this->fileContents[$filename])) {
            return;
        }

        $this->selectedGeneratedFile = $filename;
        $this->activeBottomTab = $filename;
        $this->editingFileContent = $this->fileContents[$filename];

        if (! in_array($filename, $this->openTabs, true)) {
            $this->openTabs[] = $filename;
        }
    }

    public function selectBottomTab(string $filename): void
    {
        $this->selectGeneratedFile($filename);
    }

    public function closeBottomTab(string $filename): void
    {
        $this->openTabs = array_values(array_filter($this->openTabs, fn ($tab) => $tab !== $filename));
        if ($this->activeBottomTab === $filename && ! empty($this->openTabs)) {
            $this->selectGeneratedFile($this->openTabs[0]);
        }
    }

    public function saveGeneratedFile(): void
    {
        if (! $this->selectedGeneratedFile) {
            return;
        }

        $path = $this->generatedFileAbsolutePath($this->selectedGeneratedFile);
        if (! $path) {
            $this->uploadStatus = 'error';
            $this->uploadMessage = 'File generated tidak valid.';
            return;
        }

        File::put($path, $this->editingFileContent);
        $this->fileContents[$this->selectedGeneratedFile] = $this->editingFileContent;
        $this->uploadStatus = 'success';
        $this->uploadMessage = "{$this->selectedGeneratedFile} berhasil disimpan.";
        $this->buildExportValidation();
    }

    public function resetUpload(): void
    {
        $this->zipFile = null;
        $this->zipExtracted = false;
        $this->uploadStatus = '';
        $this->uploadMessage = '';
        $this->htmlRaw = '';
        $this->assetSnippets = [];
        $this->fileTree = [];
        $this->fileContents = [];
        $this->openTabs = [];
        $this->editingFileContent = '';
        $this->selectedGeneratedFile = 'app.blade.php';
        $this->activeBottomTab = 'app.blade.php';
    }

    public function activateTheme(): void
    {
        $theme = $this->themeSlug();
        if (! File::isDirectory(resource_path("views/frontend/{$theme}"))) {
            $this->uploadStatus = 'error';
            $this->uploadMessage = 'Generate tema terlebih dahulu sebelum aktivasi.';
            return;
        }

        $setting = Setting::first();
        if (! $setting) {
            $this->uploadStatus = 'error';
            $this->uploadMessage = 'Data setting belum tersedia.';
            return;
        }

        $setting->update(['active_theme' => $theme]);
        $this->uploadStatus = 'success';
        $this->uploadMessage = "Tema {$theme} berhasil diaktifkan.";
    }

    public function downloadTheme()
    {
        $theme = $this->themeSlug();
        $viewPath = resource_path("views/frontend/{$theme}");
        $publicPath = public_path("frontend/{$theme}");

        if (! File::isDirectory($viewPath)) {
            $this->uploadStatus = 'error';
            $this->uploadMessage = 'Generate tema terlebih dahulu sebelum download.';
            return null;
        }

        $exportPath = storage_path("app/temp/theme-exports");
        $this->ensureDirectory($exportPath);
        $zipPath = $exportPath.DIRECTORY_SEPARATOR.$theme.'-'.now()->format('Ymd-His').'.zip';

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->uploadStatus = 'error';
            $this->uploadMessage = 'Gagal membuat file ZIP export.';
            return null;
        }

        $this->addDirectoryToZip($zip, $viewPath, "resources/views/frontend/{$theme}");
        if (File::isDirectory($publicPath)) {
            $this->addDirectoryToZip($zip, $publicPath, "public/frontend/{$theme}");
        }
        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    private function refreshFromLatestSession(): void
    {
        $base = storage_path('app/temp/themes');
        if (! File::isDirectory($base)) {
            return;
        }

        $directories = collect(File::directories($base))->sortByDesc(fn ($dir) => File::lastModified($dir));
        $latest = $directories->first(fn ($dir) => File::exists($dir.DIRECTORY_SEPARATOR.'index.html'));

        if (! $latest) {
            return;
        }

        $meta = [];
        $metaPath = $latest.DIRECTORY_SEPARATOR.'builder-meta.json';
        if (File::exists($metaPath)) {
            $meta = json_decode(File::get($metaPath), true) ?: [];
        }

        $this->tempPath = $latest;
        $this->sessionId = basename($latest);
        $this->themeName = $meta['themeName'] ?? $this->themeName;
        $this->author = $meta['author'] ?? $this->author;
        $this->themeVersion = $meta['themeVersion'] ?? $this->themeVersion;
        $this->styleType = $meta['styleType'] ?? $this->styleType;
        $this->htmlRaw = File::get($latest.DIRECTORY_SEPARATOR.'index.html');
        $originalPath = $latest.DIRECTORY_SEPARATOR.'original-index.html';
        $this->htmlOriginal = File::exists($originalPath) ? File::get($originalPath) : $this->htmlRaw;
        $this->zipExtracted = true;
        $this->assetSnippets = $this->extractAssetSnippets($this->htmlRaw);
        $this->zipInfo = $this->buildZipInfo($latest, $latest.DIRECTORY_SEPARATOR.'theme.zip');

        if (! empty($meta['generated'])) {
            $this->loadGeneratedFiles();
            $this->buildExportValidation();
        }
    }

    private function resetBuilderOutput(): void
    {
        $this->zipExtracted = false;
        $this->uploadStatus = '';
        $this->uploadMessage = '';
        $this->assetSnippets = [];
        $this->fileTree = [];
        $this->fileContents = [];
        $this->openTabs = [];
        $this->exportValidation = [];
    }

    private function extractZip(string $zipPath, string $extractPath): string
    {
        $zip = new ZipArchive;
        if ($zip->open($zipPath) !== true) {
            throw new \RuntimeException('ZIP tidak dapat dibuka.');
        }

        $indexPath = '';
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            $normalized = str_replace('\\', '/', $entry);
            if (str_contains($normalized, '../') || str_starts_with($normalized, '/')) {
                continue;
            }

            if (str_ends_with($normalized, '/')) {
                $this->ensureDirectory($extractPath.DIRECTORY_SEPARATOR.$normalized);
                continue;
            }

            $target = $extractPath.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $normalized);
            $this->ensureDirectory(dirname($target));
            File::put($target, $zip->getFromIndex($i));

            if (preg_match('/(^|\/)index\.html$/i', $normalized)) {
                $indexPath = $target;
            }
        }
        $zip->close();

        if (! $indexPath || ! File::exists($indexPath)) {
            throw new \RuntimeException('index.html tidak ditemukan di dalam ZIP.');
        }

        return $indexPath;
    }

    private function normalizeHtml(string $html, string $theme): string
    {
        $mapper = new SettingMapperService($html, $this->dataMap);
        $html = $mapper->map();
        $html = (new ThemeNormalizerService($html, $theme))->normalize();
        $html = (new FooterNormalizerService($html, $theme))->normalize();
        return (new HeadNormalizerService($html, $theme))->normalize();
    }

    private function extractAssetSnippets(string $html): array
    {
        $assetSnippets = [];
        if (class_exists(ThemeSnippetExtractorService::class)) {
            $extracted = (new ThemeSnippetExtractorService($html))->extractAll();
            foreach ($extracted as $group => $items) {
                $assetSnippets[$group] = array_values(array_filter(array_map(
                    fn ($item) => $this->makeSnippetReadyToCopy(is_array($item) ? ($item['copy'] ?? $item['tag'] ?? '') : (string) $item),
                    $items
                )));
            }
        }

        return [
            'inline_css' => $assetSnippets['inline_css'] ?? array_map(fn ($snippet) => $this->makeSnippetReadyToCopy($snippet), $this->matchTags($html, '/<style\b[^>]*>.*?<\/style>/is')),
            'css_links' => $assetSnippets['css_links'] ?? array_map(fn ($snippet) => $this->makeSnippetReadyToCopy($snippet), $this->matchTags($html, '/<link\b[^>]*(?:rel=["\'][^"\']*stylesheet[^"\']*["\']|href=["\'][^"\']+\.css(?:\?[^"\']*)?["\'])[^>]*>/is')),
            'fonts' => $assetSnippets['fonts'] ?? [],
            'icons' => $assetSnippets['icons'] ?? [],
            'images' => array_map(fn ($snippet) => $this->makeSnippetReadyToCopy($snippet), $this->matchTags($html, '/<(img|source)\b[^>]*(?:src|srcset)=["\'][^"\']+["\'][^>]*>/is', 50)),
            'inline_js' => $assetSnippets['inline_js'] ?? array_map(fn ($snippet) => $this->makeSnippetReadyToCopy($snippet), $this->matchTags($html, '/<script\b(?![^>]*\bsrc=)[^>]*>.*?<\/script>/is')),
            'js_links' => $assetSnippets['js_links'] ?? array_map(fn ($snippet) => $this->makeSnippetReadyToCopy($snippet), $this->matchTags($html, '/<script\b[^>]*\bsrc=["\'][^"\']+["\'][^>]*>.*?<\/script>/is')),
            'nav' => array_map(fn ($snippet) => $this->makeSnippetReadyToCopy($snippet), $this->matchTags($html, '/<(nav|header)\b[^>]*>.*?<\/\1>/is', 4)),
            'main' => [$this->makeSnippetReadyToCopy($this->extractBodyMain($html))],
            'footer' => array_map(fn ($snippet) => $this->makeSnippetReadyToCopy($snippet), $this->matchTags($html, '/<footer\b[^>]*>.*?<\/footer>/is', 4)),
        ];
    }

    private function makeSnippetReadyToCopy(string $snippet): string
    {
        $theme = $this->themeSlug();

        return preg_replace_callback('/\b(src|href|srcset)=([\'"])(.*?)\2/i', function ($matches) use ($theme) {
            $value = trim($matches[3]);
            if (
                $value === '' ||
                str_contains($value, '{{') ||
                preg_match('/^(https?:)?\/\//i', $value) ||
                preg_match('/^(data:|mailto:|tel:|javascript:|#)/i', $value) ||
                str_starts_with($value, '/frontend/')
            ) {
                return $matches[0];
            }

            if (str_starts_with($value, '/')) {
                $value = ltrim($value, '/');
            } elseif (str_starts_with($value, './')) {
                $value = substr($value, 2);
            }

            return $matches[1].'='.$matches[2]."/frontend/{$theme}/{$value}".$matches[2];
        }, $snippet) ?? $snippet;
    }

    private function matchTags(string $html, string $pattern, int $limit = 30): array
    {
        preg_match_all($pattern, $html, $matches);
        return array_slice(array_values(array_unique($matches[0] ?? [])), 0, $limit);
    }

    private function extractBodyMain(string $html): string
    {
        if (! preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $matches)) {
            return '';
        }

        $body = $matches[1];
        $body = preg_replace('/<(nav|header|footer)\b[^>]*>.*?<\/\1>/is', '', $body);
        $body = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $body);
        return trim($body);
    }

    private function copyAssetsToPublic(string $sourcePath, string $destinationPath): void
    {
        foreach (File::directories($sourcePath) as $directory) {
            if (basename($directory) === 'source') {
                continue;
            }
            File::copyDirectory($directory, $destinationPath.DIRECTORY_SEPARATOR.basename($directory));
        }

        foreach (File::files($sourcePath) as $file) {
            if (in_array($file->getFilename(), ['index.html', 'original-index.html', 'theme.zip', 'builder-meta.json'], true)) {
                continue;
            }
            File::copy($file->getPathname(), $destinationPath.DIRECTORY_SEPARATOR.$file->getFilename());
        }
    }

    private function writeGeneratedSupportFiles(ThemeLayoutGeneratorService $generator, string $viewThemePath): void
    {
        $this->ensureDirectory("{$viewThemePath}/page");
        $this->ensureDirectory("{$viewThemePath}/plugin");
        $this->ensureDirectory("{$viewThemePath}/category");

        File::put("{$viewThemePath}/page/show.blade.php", $this->formatGeneratedFileContent('page/show.blade.php', $generator->generatePageShow()));
        File::put("{$viewThemePath}/page/post.blade.php", $this->formatGeneratedFileContent('page/post.blade.php', $generator->generatePagePost()));
        File::put("{$viewThemePath}/page/category.blade.php", $this->formatGeneratedFileContent('page/category.blade.php', $generator->generatePageCategory()));
        File::put("{$viewThemePath}/page/contact.blade.php", $this->formatGeneratedFileContent('page/contact.blade.php', $generator->generatePageContact()));
        File::put("{$viewThemePath}/page/acc.blade.php", $this->formatGeneratedFileContent('page/acc.blade.php', $generator->generatePageReport('Accounting Reports')));
        File::put("{$viewThemePath}/page/financial.blade.php", $this->formatGeneratedFileContent('page/financial.blade.php', $generator->generatePageReport('Financial Reports')));
        File::put("{$viewThemePath}/page/share.blade.php", $this->formatGeneratedFileContent('page/share.blade.php', $generator->generatePageReport('Share Reports')));
        File::put("{$viewThemePath}/page/maintenance.blade.php", $this->formatGeneratedFileContent('page/maintenance.blade.php', $generator->generatePageMaintenance()));

        $defaultPlugin = resource_path('views/frontend/default/plugin');
        if (File::isDirectory($defaultPlugin)) {
            foreach (File::files($defaultPlugin) as $file) {
                File::copy($file->getPathname(), "{$viewThemePath}/plugin/".$file->getFilename());
            }
        }

        $defaultCategory = resource_path('views/frontend/default/category');
        if (File::isDirectory($defaultCategory)) {
            File::copyDirectory($defaultCategory, "{$viewThemePath}/category");
        }
    }

    private function normalizeGeneratedAppBlade(string $content, string $theme): string
    {
        return str_replace(
            "@include('frontend::navigation')",
            "@include('frontend.{$theme}.navigation')",
            $content
        );
    }

    private function formatGeneratedFileContent(string $filename, string $content): string
    {
        $content = str_replace(["\r\n", "\r"], "\n", trim($content));
        $content = preg_replace("/\n{3,}/", "\n\n", $content) ?? $content;
        $content = preg_replace("/[ \t]+$/m", '', $content) ?? $content;

        if (Str::endsWith($filename, ['.blade.php', '.html'])) {
            $content = preg_replace('/>\s+</', ">\n<", $content) ?? $content;
            $content = preg_replace('/\n\s*(@(?:if|foreach|else|elseif|endif|endforeach|hasSection|yield|include|stack|push|endpush|csrf|method)\b)/', "\n    $1", $content) ?? $content;
        }

        if (Str::endsWith($filename, '.php') && ! str_starts_with($content, '<?php')) {
            $content = "<?php\n\n".$content;
        }

        return $content."\n";
    }

    public function loadGeneratedFiles(): void
    {
        $themePath = resource_path('views/frontend/'.$this->themeSlug());
        if (! File::isDirectory($themePath)) {
            return;
        }

        $this->fileTree = [];
        $this->fileContents = [];
        foreach (File::allFiles($themePath) as $file) {
            $relative = str_replace('\\', '/', $file->getRelativePathname());
            $this->fileTree[] = [
                'name' => $relative,
                'type' => 'file',
                'icon' => $this->iconForFile($relative),
            ];
            if ($file->getSize() <= 262144) {
                $this->fileContents[$relative] = File::get($file->getPathname());
            }
        }

        $this->openTabs = array_slice(array_keys($this->fileContents), 0, 4);
        if (! empty($this->fileContents)) {
            $first = array_key_first($this->fileContents);
            $this->selectGeneratedFile($this->fileContents['app.blade.php'] ?? null ? 'app.blade.php' : $first);
        }
    }

    public function loadExistingTheme(string $theme): void
    {
        $theme = Str::slug($theme);
        $themePath = resource_path("views/frontend/{$theme}");
        if (! File::isDirectory($themePath)) {
            $this->uploadStatus = 'error';
            $this->uploadMessage = "Tema {$theme} tidak ditemukan.";
            return;
        }

        $this->themeName = $theme;
        $this->sessionId = 'existing-'.$theme;
        $this->tempPath = storage_path("app/temp/themes/{$this->sessionId}");
        $this->ensureDirectory($this->tempPath);
        $this->zipExtracted = true;
        $this->activeTab = 'generated';
        $this->htmlRaw = File::exists("{$themePath}/app.blade.php") ? File::get("{$themePath}/app.blade.php") : '';
        $this->htmlOriginal = $this->htmlRaw;
        $this->loadGeneratedFiles();
        $this->buildExportValidation();
        $this->uploadStatus = 'success';
        $this->uploadMessage = "Tema {$theme} dibuka di Developer Mode.";
        $this->saveSessionMeta(true);
    }

    private function buildExportValidation(): void
    {
        $required = ['app.blade.php', 'editor.blade.php', 'homepage.blade.php', 'navigation.blade.php', 'index.blade.php', 'theme.php'];
        $this->exportValidation = [];
        foreach ($required as $file) {
            $exists = isset($this->fileContents[$file]);
            $this->exportValidation[] = [
                'file' => $file,
                'status' => $exists ? 'ok' : 'error',
                'message' => $exists ? '' : 'File wajib belum dibuat.',
            ];
        }
        if (! File::isDirectory(public_path('frontend/'.$this->themeSlug()))) {
            $this->exportValidation[] = [
                'file' => 'public/frontend/'.$this->themeSlug(),
                'status' => 'warning',
                'message' => 'Folder public frontend belum ditemukan.',
            ];
        }
    }

    private function generatedFileAbsolutePath(string $relative): ?string
    {
        $base = realpath(resource_path('views/frontend/'.$this->themeSlug()));
        if (! $base) {
            return null;
        }

        $path = $base.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
        $directory = dirname($path);
        if (! str_starts_with(realpath($directory) ?: $directory, $base)) {
            return null;
        }

        return $path;
    }

    private function addDirectoryToZip(ZipArchive $zip, string $directory, string $zipPrefix): void
    {
        foreach (File::allFiles($directory) as $file) {
            $relative = str_replace('\\', '/', $file->getRelativePathname());
            $zip->addFile($file->getPathname(), trim($zipPrefix, '/').'/'.$relative);
        }
    }

    private function copyDirectoryContents(string $from, string $to, array $skip = []): void
    {
        foreach (File::directories($from) as $directory) {
            if (in_array(basename($directory), $skip, true)) {
                continue;
            }
            File::copyDirectory($directory, $to.DIRECTORY_SEPARATOR.basename($directory));
        }

        foreach (File::files($from) as $file) {
            if (in_array($file->getFilename(), $skip, true)) {
                continue;
            }
            File::copy($file->getPathname(), $to.DIRECTORY_SEPARATOR.$file->getFilename());
        }
    }

    private function buildZipInfo(string $path, string $zipPath): array
    {
        $files = File::isDirectory($path) ? File::allFiles($path) : [];
        return [
            'files' => count($files),
            'size' => File::exists($zipPath) ? File::size($zipPath) : 0,
            'html' => collect($files)->filter(fn ($file) => Str::endsWith(Str::lower($file->getFilename()), ['.html', '.htm']))->count(),
            'css' => collect($files)->filter(fn ($file) => Str::endsWith(Str::lower($file->getFilename()), '.css'))->count(),
            'js' => collect($files)->filter(fn ($file) => Str::endsWith(Str::lower($file->getFilename()), '.js'))->count(),
        ];
    }

    private function iconForFile(string $filename): string
    {
        return match (pathinfo($filename, PATHINFO_EXTENSION)) {
            'php' => 'fa-file-code',
            'css' => 'fa-file-lines',
            'js' => 'fa-file-code',
            default => 'fa-file',
        };
    }

    private function themeSlug(): string
    {
        return Str::slug($this->themeName) ?: 'theme';
    }

    private function ensureDirectory(string $path): void
    {
        if (! File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    private function saveSessionMeta(bool $generated): void
    {
        File::put($this->tempPath.DIRECTORY_SEPARATOR.'builder-meta.json', json_encode([
            'themeName' => $this->themeSlug(),
            'author' => $this->author,
            'themeVersion' => $this->themeVersion,
            'styleType' => $this->styleType,
            'generated' => $generated,
            'updated_at' => now()->toDateTimeString(),
        ], JSON_PRETTY_PRINT));
    }

    public function render()
    {
        return view('suryacms::livewire.admin.themes.create-theme')
            ->layout('suryacms::layouts.app')
            ->layoutData([
                'sidebarMinimized' => true,
            ]);
    }
}
