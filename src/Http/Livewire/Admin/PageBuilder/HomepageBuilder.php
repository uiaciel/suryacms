<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\PageBuilder;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Uiaciel\SuryaCms\Models\Page;
use Uiaciel\SuryaCms\Models\Post;

class HomepageBuilder extends Component
{
    public $html;

    public $css;

    public $pageId;

    public $title;

    public $slug;

    public $datepublish;

    public $status = 'Publish';

    public $language_id;

    public $pageSlug;

    public $blogs;

    public function mount($pageSlug)
    {
        // logger('Slug yang diterima:', [$pageId]);

        $this->blogs = Post::All();
        $page = Page::where('slug', $pageSlug)->firstOrFail();
        $this->pageId = $page->id;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->datepublish = $page->datepublish;
        $this->status = $page->status;
        $this->language_id = $page->language_id;
        $this->html = $page->html;
        $this->css = $page->css;
    }

    public function renderPageHtml($html)
    {

        $processedHtml = preg_replace_callback('/\[\[(.*?)\]\]/', function ($matches) {
            $plugin = $matches[1];

            $path = "frontend::plugin.{$plugin}";

            if (view()->exists($path)) {
                return view($path)->render();
            }

            return $matches[0]; // biarkan tetap [[plugin]] jika view tidak ditemukan
        }, $html);

        return $processedHtml;
    }

    public function savePage()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,'.$this->pageId,
        ]);

        $page = Page::find($this->pageId);

        if ($page) {
            $page->update([
                'title' => $this->title,

                'datepublish' => $this->datepublish,
                'status' => $this->status,
                'html' => $this->html,
                'css' => $this->css,
                'user_id' => Auth::id(),
            ]);
        } else {
            abort(404, 'Page not found');
        }

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Success',
            'text' => 'Page Updated Successfully!',
        ]);

        session()->flash('success', 'Page updated successfully.');
    }

    public function savePageWithContent($html, $css)
    {
        $this->html = $html;
        $this->css = $css;
        $this->savePage();
    }

    public function render()
    {

        return view('suryacms::livewire.admin.homepage-builder')->layout('frontend::editor');
    }
}
