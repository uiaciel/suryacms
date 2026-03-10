<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\Post;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Uiaciel\SuryaCms\Models\Category;
use Uiaciel\SuryaCms\Models\Language;
use Uiaciel\SuryaCms\Models\Post;
use Uiaciel\SuryaCms\Services\KeywordExtractionService;

class PostCreate extends Component
{
    public $titlePage;

    public $title;

    public $konten; // Menggunakan 'konten' sesuai blade

    public $datepublish;

    public $language_id;

    public $translation_id;

    public $status;

    public $tags;

    public $source_url;

    public $source_favicon;

    public $source_title;

    public $feature; // Akan menjadi boolean (true/false) atau 'Yes'/'No'

    public $flash;   // Akan menjadi boolean (true/false) atau 'Yes'/'No'

    public $category_id;

    public $languages;

    public $categories;

    public $setting; // Asumsi ada model Setting untuk is_multilingual

    public $scrapeResult = [];

    public $scrapingInProgress = false;

    protected $listeners = [
        'tinymce_updated' => 'updateKonten',
        'handle-copy-to-editor' => 'copyToEditor',
    ];

    public function mount()
    {
        $this->titlePage = 'Create Post';
        $this->status = 'Publish'; // Default status saat pertama kali load
        $this->datepublish = now()->format('Y-m-d');
        $this->language_id = 1; // Default bahasa
        $this->languages = Language::all();
        $this->translation_id = null;
        $this->categories = Category::all();
        $this->title = '';
        $this->konten = '';
        $this->tags = '';
        $this->source_url = '';
        $this->source_favicon = '';
        $this->source_title = '';
        $this->feature = false; // Menggunakan boolean untuk toggle switch
        $this->flash = false;   // Menggunakan boolean untuk toggle switch
        $this->category_id = '';
        // Asumsi model Setting ada dan memiliki kolom is_multilingual
        // Anda perlu menyesuaikan cara mendapatkan setting ini, misalnya dari database
        $this->setting = (object) ['is_multilingual' => 'No']; // Contoh default, sesuaikan dengan logic aplikasi Anda
    }

    public function scrapeContent()
    {
        if (empty($this->source_url)) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Please enter a valid URL',
            ]);

            return;
        }

        try {
            $url = $this->source_url;
            // Create HTTP client with a common User-Agent and timeout to appear more like a real browser
            $client = HttpClient::create([
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                ],
                'timeout' => 15,
            ]);

            $response = $client->request('GET', $url, ['max_redirects' => 5]);
            $html = $response->getContent();

            // Clean HTML dari script dan style tags sebelum parsing
            $html = $this->cleanHtml($html);

            $crawler = new Crawler($html);

            $title = '';
            $content = '';
            $image = '';

            // Deteksi situs dan atur selector sesuai dengan masing-masing situs
            if (str_contains($url, 'cnnindonesia.com')) {
                if ($crawler->filter('h1.mb-2')->count()) {
                    $title = trim($crawler->filter('h1.mb-2')->first()->text());
                }
                if ($crawler->filter('.detail-text ')->count()) {
                    $content = array_map('trim', $crawler->filter('.detail-text ')->each(function ($node) {
                        return $this->cleanText($node->text());
                    }));
                }
                if ($crawler->filter('.detail-image figure img')->count()) {
                    $image = $crawler->filter('.detail-image figure img')->first()->attr('src');
                }
            } elseif (str_contains($url, 'detik.com')) {
                if ($crawler->filter('h1.detail__title')->count()) {
                    $title = trim($crawler->filter('h1.detail__title')->first()->text());
                }
                if ($crawler->filter('.detail__body-text')->count()) {
                    $content = array_map('trim', $crawler->filter('.detail__body-text')->each(function ($node) {
                        return $this->cleanText($node->text());
                    }));
                }
                if ($crawler->filter('.detail__media-image img')->count()) {
                    $image = $crawler->filter('.detail__media-image img')->first()->attr('src');
                }
            } elseif (str_contains($url, 'tribunnews.com')) {
                if ($crawler->filter('h1.f50')->count()) {
                    $title = trim($crawler->filter('h1.f50')->first()->text());
                }
                if ($crawler->filter('#article_con p')->count()) {
                    $content = array_map('trim', $crawler->filter('#article_con p')->each(function ($node) {
                        return $this->cleanText($node->text());
                    }));
                }
                if ($crawler->filter('.imgfull img')->count()) {
                    $image = $crawler->filter('.imgfull img')->first()->attr('src');
                }
            } elseif (str_contains($url, 'kompas.com')) {
                if ($crawler->filter('h1.read__title')->count()) {
                    $title = trim($crawler->filter('h1.read__title')->first()->text());
                }
                if ($crawler->filter('.read__content p')->count()) {
                    $content = array_map('trim', $crawler->filter('.read__content p')->each(function ($node) {
                        return $this->cleanText($node->text());
                    }));
                }
                if ($crawler->filter('.photo__wrap img')->count()) {
                    $image = $crawler->filter('.photo__wrap img')->first()->attr('src');
                }
                } else {
                    // Jika domain tidak terdaftar dalam selector yang didukung
                    $this->dispatch('swal', [
                        'icon' => 'error',
                        'title' => 'Unsupported Site',
                        'text' => 'Scraping is only supported for Detik, CNN Indonesia, Tribunnews, and Kompas.',
                    ]);

                    return;
                }

            // Normalize image URL if relative
            if ($image && ! preg_match('#^https?://#i', $image)) {
                $parsed = parse_url($url);
                $scheme = $parsed['scheme'] ?? 'https';
                $host = $parsed['host'] ?? '';
                $image = rtrim($scheme.'://'.$host, '/').'/'.ltrim($image, '/');
            }

            $this->scrapeResult = [
                'title' => $title,
                'content' => is_array($content) ? implode("\n\n", $content) : (string) $content,
                'image' => $image,
            ];

            // Auto-generate keywords dari scraped content
            $this->autoGenerateKeywordsFromScraped();

            // Dispatch event to child component
            $this->dispatch('scrape-completed', $this->scrapeResult);

            // Notify UI that scraping finished successfully
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Scraped',
                'text' => 'Content scraped successfully.',
            ]);

        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Failed to scrape content: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Clean HTML dari script, style, dan elements tidak perlu
     */
    private function cleanHtml($html): string
    {
        // Remove script tags dan content
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/is', '', $html);

        // Remove style tags dan content
        $html = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/is', '', $html);

        // Remove noscript tags
        $html = preg_replace('/<noscript\b[^<]*(?:(?!<\/noscript>)<[^<]*)*<\/noscript>/is', '', $html);

        return $html;
    }

    /**
     * Clean text dari JavaScript code dan cleaning
     */
    private function cleanText($text): string
    {
        // Remove common JavaScript patterns
        $text = preg_replace('/var\s+\w+\s*=.*?;/is', '', $text);
        $text = preg_replace('/function\s*\(.*?\)\s*{.*?}/is', '', $text);
        $text = preg_replace('/document\..*?;/is', '', $text);
        $text = preg_replace('/window\..*?;/is', '', $text);
        $text = preg_replace('/addEventListener\(.*?\)/is', '', $text);
        $text = preg_replace('/onclick\s*=\s*["\'].*?["\']/is', '', $text);

        // Remove extra whitespace and newlines
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        // Remove empty lines or lines with only special characters
        if (strlen($text) < 5 || preg_match('/^[\s\W]*$/', $text)) {
            return '';
        }

        return $text;
    }

    // Metode ini diperlukan jika TinyMCE tidak langsung update wire:model
    public function updateKonten($content)
    {
        $this->konten = $content;
    }

    /**
     * Copy scraped content ke TinyMCE editor atau field yang sesuai
     */
    public function copyToEditor($data): void
    {
        $field = $data['field'] ?? '';
        $content = $data['content'] ?? '';

        if (empty($content)) {
            $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => 'Warning',
                'text' => 'Content is empty!',
            ]);

            return;
        }

        try {
            match ($field) {
                'title' => $this->title = $content,
                'content' => $this->konten = $content,
                'image' => $this->konten = '<img src="'.$content.'" alt="Scraped Image" style="max-width: 100%; height: auto; border-radius: 8px;">',
                'content-full' => $this->konten = $content,
                default => null,
            };

            // Dispatch alert success dengan icon yang sesuai
            $successMessage = match ($field) {
                'title' => 'Title copied successfully!',
                'content' => 'Content copied to editor successfully!',
                'image' => 'Image inserted to editor successfully!',
                'content-full' => 'Image and content copied to editor successfully!',
                default => 'Copied successfully!',
            };

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => ucfirst($field),
                'text' => $successMessage,
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Failed to copy content: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Generate keywords secara otomatis dari title dan konten
     * Dapat dipanggil setelah scraping atau kapan saja
     */
    public function generateKeywords(): void
    {
        // Validasi bahwa ada konten untuk di-extract
        if (empty($this->konten) && empty($this->title)) {
            $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => 'Warning',
                'text' => 'Please add title and content first before generating keywords.',
            ]);

            return;
        }

        try {
            $keywordService = new KeywordExtractionService();

            // Generate tags dari title dan konten
            $generatedTags = $keywordService->generateTags(
                $this->title,
                strip_tags($this->konten),
                keywordCount: 5,  // Ambil 5 keywords
                phraseCount: 3    // Ambil 3 phrases
            );

            // Set tags property dengan generated keywords
            $this->tags = $generatedTags;

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Keywords Generated',
                'text' => 'Keywords generated successfully! You can edit them if needed.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Failed to generate keywords: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Auto generate keywords setelah scraping content selesai
     * Dipanggil otomatis dalam scrapeContent
     */
    private function autoGenerateKeywordsFromScraped(): void
    {
        if (! empty($this->scrapeResult['content'])) {
            try {
                $keywordService = new KeywordExtractionService();

                // Generate keywords dari scraped content
                $generatedTags = $keywordService->generateTags(
                    $this->scrapeResult['title'] ?? '',
                    $this->scrapeResult['content'],
                    keywordCount: 5,
                    phraseCount: 3
                );

                // Jika tags belum diisi, gunakan generated keywords
                if (empty($this->tags)) {
                    $this->tags = $generatedTags;
                }
            } catch (\Exception $e) {
                // Silent fail - jangan interrupt scraping process
                logger('Failed to auto-generate keywords: '.$e->getMessage());
            }
        }
    }

    // Metode untuk fetching terjemahan, jika diperlukan oleh blade
    public function getTranslations($languageId)
    {
        // Sesuaikan query jika Anda ingin hanya post yang belum punya terjemahan
        return Post::where('language_id', '!=', $languageId)->get();
    }

    public function savePost()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'konten' => 'required|string',
            'datepublish' => 'required|date',
            'language_id' => 'required|exists:languages,id', // 'required' jika multilingual aktif
            'translation_id' => 'nullable|exists:posts,id',
            'status' => 'required|in:Publish,Draft', // Pastikan hanya 2 nilai ini
            'tags' => 'nullable|string',
            'source_url' => 'nullable|url',
            'source_favicon' => 'nullable|string',
            'source_title' => 'nullable|string',
            'feature' => 'boolean', // Validasi untuk boolean (dari checkbox/toggle)
            'flash' => 'boolean',   // Validasi untuk boolean (dari checkbox/toggle)
            'category_id' => 'required|exists:categories,id',
        ]);

        $post = Post::create([
            'title' => $this->title,
            'content' => $this->konten,
            'datepublish' => $this->datepublish,
            'language_id' => $this->language_id,
            'translation_id' => $this->translation_id, // Bisa null
            'status' => 'Publish', // Pastikan statusnya "Publish"
            'tags' => $this->tags,
            'source_url' => $this->source_url,
            'source_favicon' => $this->source_favicon,
            'source_title' => $this->source_title,
            'feature' => $this->feature ? 'Yes' : 'No', // Konversi boolean ke 'Yes'/'No'
            'flash' => $this->flash ? 'Yes' : 'No',     // Konversi boolean ke 'Yes'/'No'
            'view' => 0,
            'category_id' => $this->category_id,
            'slug' => Str::slug($this->title),
            'user_id' => Auth::id(),
        ]);

        session()->flash('success', 'Post created successfully.');

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Success',
            'text' => 'Post created Successfully!',
        ]);

        // Opsional: reset form setelah publish jika tidak langsung redirect
        $this->resetForm();

        return $this->redirect('/admin/posts/');
    }

    public function saveDraft()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'konten' => 'nullable|string', // Konten bisa kosong untuk draft
            'datepublish' => 'required|date',
            'language_id' => 'required|exists:languages,id',
            'translation_id' => 'nullable|exists:posts,id',
            'tags' => 'nullable|string',
            'source_url' => 'nullable|url',
            'source_favicon' => 'nullable|string',
            'source_title' => 'nullable|string',
            'feature' => 'boolean',
            'flash' => 'boolean',
            'category_id' => 'required|exists:categories,id',
        ]);

        $post = Post::create([
            'title' => $this->title,
            'content' => $this->konten,
            'datepublish' => $this->datepublish,
            'language_id' => $this->language_id,
            'translation_id' => $this->translation_id,
            'status' => 'Draft', // Set status ke "Draft"
            'tags' => $this->tags,
            'source_url' => $this->source_url,
            'source_favicon' => $this->source_favicon,
            'source_title' => $this->source_title,
            'feature' => $this->feature ? 'Yes' : 'No',
            'flash' => $this->flash ? 'Yes' : 'No',
            'view' => 0,
            'category_id' => $this->category_id,
            'slug' => Str::slug($this->title),
            'user_id' => Auth::id(),
        ]);

        session()->flash('info', 'Post saved as draft.');

        $this->dispatch('swal', [
            'icon' => 'info',
            'title' => 'Saved!',
            'text' => 'Post saved as draft!',
        ]);

        // Opsional: reset form setelah save draft
        $this->resetForm();

        return $this->redirect('/admin/posts/'); // Bisa redirect ke halaman daftar post atau tetap di halaman ini
    }

    // Metode untuk mereset form setelah submit
    private function resetForm()
    {
        $this->reset([
            'title',
            'konten',
            'tags',
            'source_url',
            'source_favicon',
            'source_title',
            'translation_id',
            'feature',
            'flash',
            'category_id',
        ]);
        // Reset juga TinyMCE editor (browser event)
        $this->dispatch('tinymce_reset');
    }

    public function render()
    {
        return view('suryacms::livewire.admin.post.post-create', [
            'setting' => $this->setting,
        ])->layout('suryacms::layouts.app');
    }
}
