<?php

namespace Uiaciel\SuryaCms\Livewire\Admin\Themes;

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
use Uiaciel\SuryaCms\Services\ThemeAssetReferenceService;
use Uiaciel\SuryaCms\Services\ThemeFrameworkDetectorService;
use Uiaciel\SuryaCms\Services\ThemeZipInspectorService;
use ZipArchive;

class CreateTheme extends Component
{
    use WithFileUploads;

    private const REQUIRED_THEME_FILES = [
        'app.blade.php',
        'editor.blade.php',
        'homepage.blade.php',
        'index.blade.php',
        'navigation.blade.php',
        'theme.php',

        'page/category.blade.php',
        'page/contact.blade.php',
        'page/maintenance.blade.php',
        'page/post.blade.php',
        'page/show.blade.php',

        'plugin/blog.blade.php',
        'plugin/gallery.blade.php',
        'plugin/slider.blade.php',
        'plugin/youtube.blade.php',
    ];

    public string $themeName = 'my-awesome-theme';

    public string $author = 'uiaciel';

    public string $themeVersion = '1.0.0';

    public string $styleType = 'bootstrap';

    public array $frameworkDetection = [
        'framework' => 'unknown',
        'confidence' => 'low',
        'evidence' => [],
    ];

    public array $fileDirty = [];

    public array $archiveWarnings = [];

    public string $selectedIndexRelativePath = '';

    public $zipFile = null;

    public bool $zipExtracted = false;

    public bool $themeGenerated = false;

    public bool $sourcePreviewAvailable = false;

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

    public string $selectedVariableGroup = 'Global';

    public string $previewMode = 'source';

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

    public function editorPalette(): array
    {
        return [
            'Global' => [
                ['label' => 'Tema aktif', 'snippet' => '{{ $activeTheme }}'],
                ['label' => 'Nama website', 'snippet' => '{{ $setting?->sitename ?? "Nama Website" }}'],
                ['label' => 'Logo website', 'snippet' => '@if($setting?->logo)<img src="{{ asset($setting->logo) }}" alt="{{ $setting->sitename ?? "Logo" }}">@endif'],
                ['label' => 'Navigasi menu', 'snippet' => '@foreach($menus as $menu)<a href="{{ url($menu->link ?? "/") }}">{{ $menu->name }}</a>@endforeach'],
                ['label' => 'Latest posts loop', 'snippet' => $this->latestPostsLoopSnippet()],
                ['label' => 'Semua post', 'snippet' => '@foreach($posts as $post)<a href="{{ url("/".$post->slug) }}">{{ $post->title }}</a>@endforeach'],
                ['label' => 'Top posts', 'snippet' => '@foreach($topposts as $post)<a href="{{ url("/".$post->slug) }}">{{ $post->title }}</a>@endforeach'],
                ['label' => 'Kategori', 'snippet' => '@foreach($categories as $category)<a href="{{ url("/category/".$category->slug) }}">{{ $category->name }}</a>@endforeach'],
                ['label' => 'Halaman website', 'snippet' => '@foreach($pages as $page)<a href="{{ url("/".$page->slug) }}">{{ $page->title }}</a>@endforeach'],
                ['label' => 'Galeri', 'snippet' => '@foreach($gallery as $item)<img src="{{ asset("storage/".$item->cover_path) }}" alt="{{ $item->alt_text ?? "Gallery" }}">@endforeach'],
                ['label' => 'Slider', 'snippet' => '@foreach($sliders as $slide)<img src="{{ asset("storage/".$slide->image_path) }}" alt="{{ $slide->alt_text }}">@endforeach'],
                ['label' => 'Tag cloud', 'snippet' => '@foreach($tagcloud as $tag => $count)<a href="{{ url("/tag/".$tag) }}">{{ $tag }}</a>@endforeach'],
                ['label' => 'Video YouTube', 'snippet' => '@foreach($youtubes as $youtube)<a href="{{ $youtube->url ?? "#" }}">{{ $youtube->title ?? "Video" }}</a>@endforeach'],
                ['label' => 'Bahasa', 'snippet' => '@foreach($lang as $language)<a href="{{ url("/lang/".$language->code) }}">{{ $language->name }}</a>@endforeach'],
                ['label' => 'Pesan kontak', 'snippet' => '@foreach($contacts as $contact)<p>{{ $contact->name }}</p>@endforeach'],
            ],
            'Page' => [
                ['label' => 'Judul halaman', 'snippet' => '{{ $page->title ?? "Judul halaman" }}'],
                ['label' => 'Konten halaman', 'snippet' => '{!! $page->content ?? "" !!}'],
                ['label' => 'Gambar halaman', 'snippet' => '@if($page->image)<img src="{{ asset($page->image) }}" alt="{{ $page->title ?? "Halaman" }}">@endif'],
                ['label' => 'Homepage builder HTML', 'snippet' => '{!! $html ?? "" !!}'],
                ['label' => 'Homepage builder CSS', 'snippet' => '<style>{!! $css ?? "" !!}</style>'],
                ['label' => 'Judul SEO', 'snippet' => '{{ $seo->title ?? $page->title ?? "Judul halaman" }}'],
                ['label' => 'Judul post', 'snippet' => '{{ $post->title ?? "Judul artikel" }}'],
                ['label' => 'Konten post', 'snippet' => '{!! $post->content ?? "" !!}'],
                ['label' => 'Kategori post', 'snippet' => '{{ $category->name ?? $post->category->name ?? "Kategori" }}'],
                ['label' => 'Post terbaru lainnya', 'snippet' => '@foreach($recentpost as $recent)<a href="{{ url("/".$recent->slug) }}">{{ $recent->title }}</a>@endforeach'],
            ],
            'Snippets Variable' => [
                ['label' => 'Loop latest posts', 'snippet' => $this->latestPostsLoopSnippet()],
                ['label' => 'Loop menu + submenu', 'snippet' => '@foreach($menus as $menu)<li><a href="{{ url($menu->link ?? "/") }}">{{ $menu->name }}</a>@if($menu->children?->isNotEmpty())<ul>@foreach($menu->children as $child)<li><a href="{{ url($child->link ?? "/") }}">{{ $child->name }}</a></li>@endforeach</ul>@endif</li>@endforeach'],
                ['label' => 'Loop halaman', 'snippet' => '@foreach($pages as $page)<article><h2><a href="{{ url("/".$page->slug) }}">{{ $page->title }}</a></h2><div>{!! $page->content !!}</div></article>@endforeach'],
            ],
        ];
    }

    public function savePreview(): void
    {
        if (! $this->sessionId || ! File::exists(storage_path("app/temp/themes/{$this->sessionId}/builder-meta.json"))) {
            $this->uploadStatus = 'error';
            $this->uploadMessage = 'Sesi preview tidak ditemukan.';

            return;
        }

        if (isset($this->fileContents[$this->selectedGeneratedFile])) {
            $this->saveGeneratedFile();
            if ($this->uploadStatus === 'error') {
                return;
            }
        }

        $this->dispatch('theme-preview-refresh');
    }

    private function latestPostsLoopSnippet(): string
    {
        return <<<'BLADE'
@foreach($latestposts as $post)
    <article class="post-card">
        @foreach($post->gambar() as $postImage)
            @if($loop->first)<img src="{{ $postImage }}" alt="{{ $post->title }}">@endif
        @endforeach
        <h2><a href="{{ url('/'.$post->slug) }}">{{ $post->title }}</a></h2>
        <p>{{ \Illuminate\Support\Str::limit(strip_tags($post->content), 140) }}</p>
        <time>{{ $post->datepublish }}</time>
    </article>
@endforeach
BLADE;
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
            $archive = (new ThemeZipInspectorService)->extract($zipPath, $extractPath);
            $indexPath = $archive['index_path'];
            $sourceRoot = $extractPath;
            $this->selectedIndexRelativePath = $archive['index_relative_path'];
            $this->archiveWarnings = $archive['warnings'];
            $this->sourcePreviewAvailable = true;
            $this->themeGenerated = false;

            $indexTarget = $this->tempPath.DIRECTORY_SEPARATOR.'index.html';
            $originalTarget = $this->tempPath.DIRECTORY_SEPARATOR.'original-index.html';
            File::copy($indexPath, $indexTarget);
            File::copy($indexPath, $originalTarget);

            $this->htmlRaw = File::get($indexTarget);
            $this->htmlOriginal = File::get($originalTarget);
            $this->frameworkDetection = (new ThemeFrameworkDetectorService)->detect($this->htmlOriginal, $sourceRoot);
            $this->styleType = $this->frameworkDetection['framework'];
            $this->zipInfo = $this->buildZipInfo($sourceRoot, $zipPath);
            $this->assetSnippets = $this->extractAssetSnippets($this->htmlRaw);
            $this->zipExtracted = true;
            $this->themeGenerated = false;
            $this->sourcePreviewAvailable = File::isDirectory($this->tempPath.DIRECTORY_SEPARATOR.'source');
            $this->uploadStatus = 'success';
            $this->uploadMessage = 'ZIP berhasil diperiksa dan diekstrak. Framework terdeteksi: '.$this->frameworkDetection['framework'].'.';
            if ($this->archiveWarnings !== []) {
                $this->uploadMessage .= ' Periksa peringatan struktur ZIP sebelum melanjutkan.';
            }
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
            $this->copyAssetsToPublic($this->tempPath.DIRECTORY_SEPARATOR.'source', $publicThemePath);

            $this->ensureDirectory($viewThemePath);
            $generator = new ThemeLayoutGeneratorService($normalized, $theme);
            File::put("{$viewThemePath}/app.blade.php", $this->formatGeneratedFileContent('app.blade.php', $this->normalizeGeneratedAppBlade($generator->generate(), $theme)));
            File::put("{$viewThemePath}/navigation.blade.php", $this->formatGeneratedFileContent('navigation.blade.php', $generator->generateNavigation()));
            File::put("{$viewThemePath}/editor.blade.php", $this->formatGeneratedFileContent('editor.blade.php', $generator->generateEditor()));
            File::put("{$viewThemePath}/homepage.blade.php", $this->formatGeneratedFileContent('homepage.blade.php', $generator->generateHomepage()));
            File::put("{$viewThemePath}/index.blade.php", $this->formatGeneratedFileContent('index.blade.php', $generator->generateIndex()));
            File::put("{$viewThemePath}/theme.php", $this->formatGeneratedFileContent('theme.php', "<?php\n\nreturn ".$generator->generateThemeConfig($this->styleType).';'));

            $this->writeGeneratedSupportFiles($generator, $viewThemePath);
            $this->loadGeneratedFiles();
            $this->buildExportValidation();
            $this->themeGenerated = true;
            $this->activeTab = 'generated';
            $this->uploadStatus = 'success';
            $this->uploadMessage = "Tema {$theme} berhasil digenerate.";
            $this->saveSessionMeta(true);

            $this->dispatch('toast',
                type: 'success',
                message: 'sudah berhasil digenerate!'
            );
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

        $this->dispatch('theme-file-selected', content: $this->editingFileContent);
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

    public function updatedEditingFileContent()
    {
        if ($this->selectedGeneratedFile) {
            $original = $this->fileContents[$this->selectedGeneratedFile] ?? '';
            $this->fileDirty[$this->selectedGeneratedFile] =
                $this->editingFileContent !== $original;
        }
    }

    public function saveGeneratedFile(): void
    {

        try {
            $this->fileContents[$this->selectedGeneratedFile] = $this->editingFileContent;

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

            $this->fileDirty[$this->selectedGeneratedFile] = false;

            $this->dispatch('toast',
                type: 'success',
                message: basename($this->selectedGeneratedFile) . ' berhasil disimpan!'
            );

            $this->dispatch('theme-file-selected', content: $this->editingFileContent);

        } catch (\Exception $e) {
            $this->uploadStatus = 'error';
            $this->uploadMessage = 'Gagal menyimpan file: '.$e->getMessage();
            $this->dispatch('toast', type: 'error', message: $this->uploadMessage);
        }
    }

    public function resetUpload(): void
    {
        $this->zipFile = null;
        $this->zipExtracted = false;
        $this->themeGenerated = false;
        $this->sourcePreviewAvailable = false;
        $this->uploadStatus = '';
        $this->uploadMessage = '';
        $this->htmlRaw = '';
        $this->assetSnippets = [];
        $this->archiveWarnings = [];
        $this->selectedIndexRelativePath = '';
        $this->frameworkDetection = ['framework' => 'unknown', 'confidence' => 'low', 'evidence' => []];
        $this->fileTree = [];
        $this->fileContents = [];
        $this->openTabs = [];
        $this->editingFileContent = '';
        $this->selectedGeneratedFile = 'app.blade.php';
        $this->activeBottomTab = 'app.blade.php';
        $this->previewMode = 'source';

        $this->dispatch('toast',
                type: 'success',
                message: 'berhasil direset!'
            );
    }

    public function activateTheme(): void
    {
        $theme = $this->themeSlug();
        if (! $this->hasValidThemeContract() || ! File::isDirectory(resource_path("views/frontend/{$theme}"))) {
            $this->uploadStatus = 'error';
            $this->uploadMessage = 'Generate tema dan lengkapi 15 file wajib sebelum aktivasi.';

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

        if (! File::isDirectory($viewPath) || ! $this->hasValidThemeContract()) {
            $this->uploadStatus = 'error';
            $this->uploadMessage = 'Generate tema dan lengkapi 15 file wajib sebelum download.';

            return null;
        }

        $exportPath = storage_path('app/temp/theme-exports');
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
        $this->frameworkDetection = $meta['frameworkDetection'] ?? $this->frameworkDetection;
        $this->selectedIndexRelativePath = $meta['indexRelativePath'] ?? $this->selectedIndexRelativePath;
        $this->htmlRaw = File::get($latest.DIRECTORY_SEPARATOR.'index.html');
        $originalPath = $latest.DIRECTORY_SEPARATOR.'original-index.html';
        $this->htmlOriginal = File::exists($originalPath) ? File::get($originalPath) : $this->htmlRaw;
        $this->zipExtracted = true;
        $this->themeGenerated = ! empty($meta['generated']);
        $this->sourcePreviewAvailable = File::isDirectory($latest.DIRECTORY_SEPARATOR.'source');
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
        $this->themeGenerated = false;
        $this->sourcePreviewAvailable = false;
        $this->archiveWarnings = [];
        $this->selectedIndexRelativePath = '';
        $this->uploadStatus = '';
        $this->uploadMessage = '';
        $this->assetSnippets = [];
        $this->fileTree = [];
        $this->fileContents = [];
        $this->openTabs = [];
        $this->exportValidation = [];
    }

    private function normalizeHtml(string $html, string $theme): string
    {
        $assetReferences = new ThemeAssetReferenceService;
        $html = $assetReferences->rebaseToArchiveRoot($html, $this->selectedIndexRelativePath);
        $mapper = new SettingMapperService($html, $this->dataMap);
        $html = $mapper->map();
        $html = (new ThemeNormalizerService($html, $theme))->normalize();
        $html = (new FooterNormalizerService($html, $theme))->normalize();

        $html = (new HeadNormalizerService($html, $theme))->normalize();

        return $assetReferences->prefixThemeAssets($html, $theme);
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
        $assetReferences = new ThemeAssetReferenceService;
        foreach (File::allFiles($sourcePath) as $file) {
            $relative = str_replace('\\', '/', $file->getRelativePathname());
            if ($relative === 'index.html') {
                continue;
            }

            $destination = $destinationPath.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
            $this->ensureDirectory(dirname($destination));
            $content = File::get($file->getPathname());
            if (strtolower($file->getExtension()) === 'css') {
                $content = $assetReferences->prefixRootRelativeCssAssets($content, $this->themeSlug());
            }
            File::put($destination, $content);
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

        $defaultPluginPaths = [
            resource_path('views/frontend/default/plugin'),
            dirname(__DIR__, 4).DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'frontend'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'plugin',
        ];
        foreach ($defaultPluginPaths as $defaultPlugin) {
            if (! File::isDirectory($defaultPlugin)) {
                continue;
            }
            foreach (File::files($defaultPlugin) as $file) {
                $destination = "{$viewThemePath}/plugin/".$file->getFilename();
                if (! File::exists($destination)) {
                    File::copy($file->getPathname(), $destination);
                }
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
        try {
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
            $this->dispatch('toast', type: 'success', message: 'File berhasil direload!');
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Gagal menyimpan file');
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
        $this->themeGenerated = true;
        $this->sourcePreviewAvailable = false;
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
        $this->exportValidation = [];
        foreach (self::REQUIRED_THEME_FILES as $file) {
            $path = $this->generatedFileAbsolutePath($file);
            $exists = $path !== null && File::isFile($path);
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

    private function hasValidThemeContract(): bool
    {
        foreach (self::REQUIRED_THEME_FILES as $file) {
            $path = $this->generatedFileAbsolutePath($file);
            if ($path === null || ! File::isFile($path)) {
                return false;
            }
        }

        return true;
    }

    private function generatedFileAbsolutePath(string $relative): ?string
    {
        $base = realpath(resource_path('views/frontend/'.$this->themeSlug()));
        $relative = str_replace('\\', '/', $relative);
        if (
            ! $base ||
            $relative === '' ||
            str_starts_with($relative, '/') ||
            str_contains($relative, "\0") ||
            preg_match('/^[a-z]:/i', $relative) ||
            in_array('..', explode('/', $relative), true)
        ) {
            return null;
        }

        $path = $base.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
        $directory = realpath(dirname($path));
        $basePrefix = rtrim($base, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if (
            ! $directory ||
            ($directory !== $base && ! str_starts_with($directory, $basePrefix)) ||
            is_link($path)
        ) {
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
            'frameworkDetection' => $this->frameworkDetection,
            'indexRelativePath' => $this->selectedIndexRelativePath,
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
