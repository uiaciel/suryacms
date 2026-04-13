<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\Post;

use Illuminate\Support\Str;
use Livewire\Component;
use Uiaciel\SuryaCms\Models\Category;
use Uiaciel\SuryaCms\Models\Language;
use Uiaciel\SuryaCms\Models\Post;
use Uiaciel\SuryaCms\Services\KeywordExtractionService;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class PostEdit extends Component
{
    public $titlePage;

    public $postId;

    public $title;

    public $slug;

    public $konten;

    public $datepublish;

    public $language_id;

    public $translation_id;

    public $category_id;

    public $tags;

    public $source_url;

    public $source_favicon;

    public $source_title;

    public $feature; // Akan menjadi boolean

    public $flash;   // Akan menjadi boolean

    public $status;

    public $languages;

    public $categories;

    public $setting;

    public $scrapeResult = [];

    public $scrapingInProgress = false;

    public $html;

    protected $listeners = [
        'tinymce_updated' => 'updateKonten',
        'handle-copy-to-editor' => 'copyToEditor',
    ];

    public function mount()
    {
        $this->postId = request()->id;
        $post = Post::find($this->postId);

        if ($post) {
            $this->titlePage = 'Edit Post';
            $this->title = $post->title;
            $this->slug = $post->slug;
            $this->konten = $post->content;
            // Pastikan format tanggal sesuai dengan input date HTML (YYYY-MM-DD)
            $this->datepublish = $post->datepublish;
            $this->language_id = $post->language_id;
            $this->translation_id = $post->translation_id;
            $this->category_id = $post->category_id;
            $this->source_url = $post->source_url;
            $this->source_favicon = $post->source_favicon;
            $this->source_title = $post->source_title;
            // Konversi 'Yes'/'No' dari database ke boolean untuk toggle switch
            $this->feature = ($post->feature === 'Yes');
            $this->flash = ($post->flash === 'Yes');
            $this->tags = $post->tags;
            $this->status = $post->status; // Ambil status dari post yang ada
            $this->html = $post->html;

            $this->languages = Language::all();
            $this->categories = Category::all();

            // Ambil setting dari database. Sesuaikan dengan cara Anda mendapatkan setting.
            // Contoh: $this->setting = \Uiaciel\SuryaCms\Models\Setting::first();
            $this->setting = (object) ['is_multilingual' => 'No']; // Dummy, ganti ini
        } else {
            abort(404, 'Post not found');
        }
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

    // Metode ini diperlukan jika TinyMCE tidak langsung update wire:model
    public function updateKonten($content)
    {
        $this->konten = $content;
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
        // dan tidak menyertakan post yang sedang diedit itu sendiri sebagai terjemahan
        return Post::where('language_id', '!=', $languageId)
            ->where('id', '!=', $this->postId) // Exclude current post
            ->get();
    }

    public function updatePost()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'konten' => 'required|string',
            'datepublish' => 'required|date',
            'language_id' => 'required|exists:languages,id',
            'translation_id' => 'nullable|exists:posts,id',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|string',
            'source_url' => 'nullable|url',
            'source_favicon' => 'nullable|string',
            'source_title' => 'nullable|string',
            'feature' => 'boolean', // Validasi untuk boolean
            'flash' => 'boolean',   // Validasi untuk boolean
            'status' => 'required|in:Publish,Draft', // Pastikan hanya 2 nilai ini
        ]);

        $post = Post::find($this->postId);

        if ($post) {
            $post->update([
                'title' => $this->title,
                'content' => $this->konten,
                'datepublish' => $this->datepublish,
                'language_id' => $this->language_id,
                'translation_id' => $this->translation_id,
                'category_id' => $this->category_id,
                'tags' => $this->tags,
                'source_url' => $this->source_url,
                'source_favicon' => $this->source_favicon,
                'source_title' => $this->source_title,
                // Konversi boolean dari toggle switch ke 'Yes'/'No' untuk database
                'feature' => $this->feature ? 'Yes' : 'No',
                'flash' => $this->flash ? 'Yes' : 'No',
                'status' => 'Publish', // Set status ke "Publish" saat tombol Update ditekan
                'slug' => Str::slug($this->title),
                // 'user_id' tidak perlu diupdate karena ini edit
            ]);

            if($post->status === 'Publish') {
                event(new \Uiaciel\SuryaCms\Events\PostPublished($post));
                }

            session()->flash('success', 'Post updated successfully.');
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Success',
                'text' => 'Post updated Successfully!',
            ]);

            return $this->redirect('/admin/posts', navigate: true);
        } else {
            session()->flash('error', 'Post not found.');
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Post not found!',
            ]);

            return $this->redirect('/admin/posts', navigate: true);
        }
    }

    public function saveDraft()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'konten' => 'nullable|string', // Konten bisa kosong untuk draft
            'datepublish' => 'required|date',
            'language_id' => 'required|exists:languages,id',
            'translation_id' => 'nullable|exists:posts,id',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|string',
            'source_url' => 'nullable|url',
            'source_favicon' => 'nullable|string',
            'source_title' => 'nullable|string',
            'feature' => 'boolean',
            'flash' => 'boolean',
        ]);

        $post = Post::find($this->postId);

        if ($post) {
            $post->update([
                'title' => $this->title,
                'content' => $this->konten,
                'datepublish' => $this->datepublish,
                'language_id' => $this->language_id,
                'translation_id' => $this->translation_id,
                'category_id' => $this->category_id,
                'tags' => $this->tags,
                'source_url' => $this->source_url,
                'source_favicon' => $this->source_favicon,
                'source_title' => $this->source_title,
                'feature' => $this->feature ? 'Yes' : 'No',
                'flash' => $this->flash ? 'Yes' : 'No',
                'status' => 'Draft', // Set status ke "Draft"
                'slug' => Str::slug($this->title),
            ]);

            session()->flash('info', 'Post saved as draft.');
            $this->dispatch('swal', [
                'icon' => 'info',
                'title' => 'Saved!',
                'text' => 'Post saved as draft!',
            ]);
        } else {
            session()->flash('error', 'Post not found.');
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Post not found!',
            ]);

            return $this->redirect('/admin/posts', navigate: true);
        }
    }

    public function deletePost()
    {
        $post = Post::find($this->postId);

        if ($post) {
            $post->delete();

            session()->flash('success', 'Post deleted successfully.');
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Deleted!',
                'text' => 'Post deleted Successfully!',
            ]);

            return $this->redirect('/admin/posts', navigate: true);
        } else {
            session()->flash('error', 'Post not found.');
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Post not found!',
            ]);
        }
    }

    public function render()
    {
        return view('suryacms::livewire.admin.post.post-edit', [
            'setting' => $this->setting, // Pastikan $this->setting tersedia
        ])->layout('suryacms::layouts.app');
    }
}
