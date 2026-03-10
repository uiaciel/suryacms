<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\Post;

use Livewire\Attributes\On;
use Livewire\Component;

class ScrapedContentDisplay extends Component
{
    public array $scrapeResult = [];

    #[On('scrape-completed')]
    public function handleScrapedData($data): void
    {
        $this->scrapeResult = $data;
    }

    public function copyToEditor($field): void
    {
        $content = $this->scrapeResult[$field] ?? '';

        if (empty($content)) {
            return;
        }

        // Dispatch event ke parent component
        $this->dispatch('handle-copy-to-editor', [
            'field' => $field,
            'content' => $content,
        ]);
    }

    /**
     * Copy image + content sekaligus ke editor
     */
    public function copyFullToEditor(): void
    {
        $image = $this->scrapeResult['image'] ?? '';
        $content = $this->scrapeResult['content'] ?? '';

        if (empty($content) && empty($image)) {
            return;
        }

        // Combine image dan content
        $fullContent = '';

        if (!empty($image)) {
            $fullContent .= '<figure style="text-align: center; margin: 20px 0;"><img src="'.$image.'" alt="Featured Image" style="max-width: 100%; height: auto; border-radius: 8px;"></figure>';
        }

        if (!empty($content)) {
            $fullContent .= '<p>'.$content.'</p>';
        }

        // Dispatch event ke parent component
        $this->dispatch('handle-copy-to-editor', [
            'field' => 'content-full',
            'content' => $fullContent,
        ]);
    }

    public function render()
    {
        return view('suryacms::livewire.admin.post.scraped-content-display');
    }
}

