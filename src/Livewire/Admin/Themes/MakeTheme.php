<?php

namespace Uiaciel\SuryaCms\Livewire\Admin\Themes;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Uiaciel\SuryaCms\Services\ThemeZipInspectorService;
use Uiaciel\SuryaCms\Services\ThemeFrameworkDetectorService;
use Uiaciel\SuryaCms\Services\ZipInspectorService;
use Uiaciel\SuryaCms\Services\BladeGeneratorService;

class MakeTheme extends Component
{
    use WithFileUploads;

    // ✅ Property ditambahkan agar validasi dan helper berfungsi
    public $themeName;
    public $author;
    public $themeVersion;
    public $zipFile;
    public $sessionId; // Diperlukan untuk redirect
    public $savedSessions = [];

    protected $rules = [
        'themeName'    => 'required|string|max:80',
        'author'       => 'required|string|max:80',
        'themeVersion' => 'required|string|max:30',
        'zipFile'      => 'required|file|mimes:zip|max:51200', // Maks 50MB
    ];

    // ═══════════════════════════════════════════════════════════
    // ONE-STEP: UPLOAD, EKSTRAK, GENERATE, DAN REDIRECT KE EDITOR
    // ═══════════════════════════════════════════════════════════

    public function uploadAndGenerate()
    {
        $this->validate();

        $slug = $this->themeSlug();
        $this->sessionId = $slug . '-' . Str::lower(Str::random(8));

        $tempPath    = storage_path("app/temp/themes/{$this->sessionId}");
        $extractPath = $tempPath . '/source';
        $viewPath    = resource_path("views/frontend/{$slug}");
        $publicPath  = public_path("frontend/{$slug}");
        $zipPath     = $tempPath . '/theme.zip';

        try {
            // 1. Pastikan direktori tujuan ada
            File::ensureDirectoryExists($tempPath);
            File::ensureDirectoryExists($extractPath);
            File::ensureDirectoryExists($viewPath); // PENTING: Buat folder views sebelum generate

            // 2. Salin ZIP ke temp
            if (!copy($this->zipFile->getRealPath(), $zipPath)) {
                throw new \RuntimeException('Gagal menyalin file ZIP ke folder temporary.');
            }

            // 3. Extract & Sanitasi (menghapus junk files)
            $inspector = new ZipInspectorService();
            $result = $inspector->extract($zipPath, $extractPath);
            $indexSource = $result['index_path'];

            // 4. Baca HTML Asli dan Jalankan Pipeline Normalisasi
            $rawHtml = File::get($indexSource);
            $normalizedHtml = $this->runNormalizationPipeline($rawHtml, $slug);

            // 5. Simpan hasil normalisasi sebagai preview.html (Single Source of Truth)
            File::put($tempPath . '/preview.html', $normalizedHtml);

            // Simpan juga original untuk referensi/debugging jika diperlukan
            File::copy($indexSource, $tempPath . '/original-index.html');

            // 6. GENERATE BLADE MENGGUNAKAN HTML YANG SUDAH DINORMALISASI
            // (HAPUS pemanggilan generator yang kedua di kode lama Anda)
            $generator = new BladeGeneratorService($normalizedHtml, $slug);
            $generator->generateAll($viewPath);

            // 7. Pindahkan SEMUA aset yang sudah dibersihkan ke folder public tema
            File::ensureDirectoryExists($publicPath);
            File::copyDirectory($extractPath, $publicPath);

            // 8. Hapus index.html dari folder public (karena akan di-handle oleh routing Blade)
            File::delete($publicPath . '/index.html');
            File::delete($publicPath . '/index.htm');
            File::delete($publicPath . '/home.html');

            // 9. Sukses & Redirect
            session()->flash('success', "Tema '{$slug}' berhasil diupload, dinormalisasi, dan digenerate.");

            // Pastikan nama route ini sesuai dengan yang ada di web.php Anda
            return redirect()->route('admin.themes.make.editor', ['sessionId' => $this->sessionId]);

        } catch (\Throwable $e) {
            Log::error('MakeTheme uploadAndGenerate failed', [
                'slug' => $slug ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->cleanupFailedGeneration($tempPath, $viewPath, $publicPath);
            session()->flash('error', 'Proses gagal: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
 * Menjalankan pipeline normalisasi HTML mentah menjadi HTML siap-Blade
 */
private function runNormalizationPipeline(string $rawHtml, string $themeName): string
{
    try {
        // Step 1: Mapping Database Variables (Opsional, jika SettingMapperService sudah siap)
        // $mapper = new SettingMapperService($rawHtml, $this->dataMap);
        // $html = $mapper->map();
        $html = $rawHtml; // Gunakan ini jika Step 1 belum diimplementasikan penuh

        // Step 2: Normalisasi Body (Sections, IDs, Assets paths: img, video, inline css)
        $bodyNormalizer = new \Uiaciel\SuryaCms\Services\ThemeNormalizerService($html, $themeName);
        $html = $bodyNormalizer->normalize();

        // Step 3: Normalisasi Footer/Scripts (Memindahkan script body ke bawah)
        $footerNormalizer = new \Uiaciel\SuryaCms\Services\FooterNormalizerService($html, $themeName);
        $html = $footerNormalizer->normalize();

        // Step 4: Normalisasi Head (Meta, SEO, CSS, JS paths)
        $headNormalizer = new \Uiaciel\SuryaCms\Services\HeadNormalizerService($html, $themeName);
        $html = $headNormalizer->normalize();

        return $html;

    } catch (\Throwable $e) {
        Log::warning('Normalization pipeline failed, falling back to raw HTML', [
            'error' => $e->getMessage()
        ]);
        // Fallback: Jika normalisasi gagal, kembalikan HTML asli agar proses tidak berhenti total
        return $rawHtml;
    }
}

    public function mount()
    {
        $this->loadSavedSessions();
    }

    public function loadSavedSessions()
    {
        $tempPath = storage_path('app/temp/themes');
        if (File::isDirectory($tempPath)) {
            $directories = File::directories($tempPath);
            $this->savedSessions = collect($directories)->map(function ($dir) {
                $sessionId = basename($dir);
                $zipPath = $dir . DIRECTORY_SEPARATOR . 'theme.zip';
                return [
                    'session_id' => $sessionId,
                    'exists' => File::exists($zipPath),
                    'created_at' => date('Y-m-d H:i:s', File::lastModified($dir)),
                ];
            })->sortByDesc('created_at')->values()->all();
        } else {
            $this->savedSessions = [];
        }
    }

    public function deleteSession($sessionId)
    {
        $dir = storage_path("app/temp/themes/{$sessionId}");
        if (File::isDirectory($dir)) {
            File::deleteDirectory($dir);
        }
        $this->loadSavedSessions();
        session()->flash('message', "Session {$sessionId} berhasil dihapus.");
    }

    private function themeSlug(): string
    {
        return Str::slug($this->themeName) ?: 'theme';
    }

    private function cleanupFailedGeneration(string $tempPath, string $viewPath, string $publicPath): void
    {
        if (File::exists($tempPath)) File::deleteDirectory($tempPath);
        if (File::exists($viewPath)) File::deleteDirectory($viewPath);
        if (File::exists($publicPath)) File::deleteDirectory($publicPath);
    }

    public function render()
    {
        return view('suryacms::livewire.admin.themes.make-theme', [
            'sessions' => $this->savedSessions
        ])->layout('suryacms::layouts.app');
    }
}
