<?php

namespace Uiaciel\SuryaCms\Livewire\Admin\Post;

use Livewire\Component;
use Uiaciel\SuryaCms\Models\Category;
use Uiaciel\SuryaCms\Models\Post;

class GeneratePost extends Component
{
    public int $quantity = 1;

    public string $importJson = '';

    public $setting;

    public $categories;

    public $selectedCategory = '';

    public string $message = '';

    public string $error = '';

    public function mount()
    {
        $this->setting = setting();

        $this->categories = Category::all();

        $this->selectedCategory = '';
    }

    /**
     * Import hasil JSON dari AI.
     */
    public function importArticles()
    {
        $this->reset(['message', 'error']);

        if (trim($this->importJson) === '') {
            $this->error = 'Silakan masukkan hasil JSON dari AI terlebih dahulu.';
            return;
        }

        $json = trim($this->importJson);

        // Jika AI masih memberikan ```json ... ```
        $json = preg_replace('/^```json\s*/i', '', $json);
        $json = preg_replace('/^```\s*/', '', $json);
        $json = preg_replace('/\s*```$/', '', $json);

        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error = 'Format JSON tidak valid: ' . json_last_error_msg();
            return;
        }

        if (
            !isset($data['articles']) ||
            !is_array($data['articles'])
        ) {
            $this->error = 'Format JSON tidak sesuai. Field "articles" tidak ditemukan.';
            return;
        }

        if (count($data['articles']) === 0) {
            $this->error = 'Tidak ada artikel yang ditemukan dalam JSON.';
            return;
        }

        $imported = 0;

        foreach ($data['articles'] as $article) {

            if (
                empty($article['title']) ||
                empty($article['content'])
            ) {
                continue;
            }

            $categoryId = $this->resolveCategory(
                $article['category'] ?? null
            );

            $slug = $article['slug'] ?? '';

            if (!$slug) {
                $slug = str()->slug($article['title']);
            }

            // Pastikan slug tidak bentrok.
            $slug = $this->uniqueSlug($slug);

            $tags = $article['tags'] ?? [];

            if (is_array($tags)) {
                $tags = implode(', ', $tags);
            }

            Post::create([
                'language_id' => $this->defaultLanguageId(),
                'translation_id' => null,

                'title' => $article['title'],
                'slug' => $slug,
                'content' => $article['content'],

                'category_id' => $categoryId,

                'tags' => $tags,

                'source_url' => null,
                'source_favicon' => null,
                'source_title' => null,

                'feature' => 'No',
                'flash' => 'No',

                'datepublish' => null,

                'view' => 0,

                // PENTING:
                // hasil AI selalu masuk sebagai Draft.
                'status' => 'Draft',

                'user_id' => auth()->id(),
            ]);

            $imported++;
        }

        if ($imported === 0) {
            $this->error = 'Tidak ada artikel valid yang berhasil diimport.';
            return;
        }

        $this->importJson = '';

        $this->message = "{$imported} artikel berhasil diimport sebagai Draft.";
    }

    /**
     * Cari category berdasarkan nama dari hasil AI.
     */
    protected function resolveCategory(?string $categoryName): ?int
    {
        if (!$categoryName) {
            return $this->selectedCategory ?: null;
        }

        $category = $this->categories->first(function ($item) use ($categoryName) {
            return mb_strtolower(trim($item->name))
                === mb_strtolower(trim($categoryName));
        });

        return $category?->id
            ?? ($this->selectedCategory ?: null);
    }

    /**
     * Pastikan slug unik.
     */
    protected function uniqueSlug(string $slug): string
    {
        $original = $slug;
        $counter = 1;

        while (Post::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Ambil language default.
     *
     * Sesuaikan bagian ini dengan mekanisme language
     * yang sudah digunakan SuryaCMS.
     */
    protected function defaultLanguageId(): int
    {
        return (int) (
            config('suryacms.default_language_id')
            ?? 1
        );
    }

    public function render()
    {
        return view('suryacms::livewire.admin.post.generate-post')->layout('suryacms::layouts.app');
    }
}
