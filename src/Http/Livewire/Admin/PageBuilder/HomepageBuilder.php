<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\PageBuilder;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Uiaciel\SuryaCms\Models\Page;
use Uiaciel\SuryaCms\Models\Post;

class HomepageBuilder extends Component
{
    use WithFileUploads;

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

    public $fileUpload;

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

    public function exportPage()
    {
        $page = Page::findOrFail($this->pageId);
        return \Maatwebsite\Excel\Facades\Excel::download(new \Uiaciel\SuryaCms\Exports\PageExport($page), 'page-' . $this->slug . '.xlsx');
    }

    public function exportPageJson()
    {
        $page = Page::findOrFail($this->pageId);

        $data = [
            'title' => $page->title,
            'slug' => $page->slug,
            'html' => $page->html,
            'css' => $page->css,
        ];

        return response()->streamDownload(
            fn () => print(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)),
            'page-' . $this->slug . '.json'
        );
    }

    public function exportAllPages()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \Uiaciel\SuryaCms\Exports\PageExport(), 'pages-backup-' . date('Y-m-d-H-i-s') . '.xlsx');
    }

    public function importPageJson()
    {
        $this->validate([
            'fileUpload' => 'required|file|mimes:json|max:5120', // max 5MB
        ]);

        try {
            $content = file_get_contents($this->fileUpload->getRealPath());
            $data = json_decode($content, true);

            if (!$data || !isset($data['html']) || !isset($data['css'])) {
                $this->dispatch('swal', [
                    'icon' => 'error',
                    'title' => 'Invalid JSON',
                    'text' => 'JSON file harus berisi field "html" dan "css"',
                ]);
                return;
            }

            $page = Page::find($this->pageId);
            if ($page) {
                $page->update([
                    'html' => $data['html'],
                    'css' => $data['css'],
                    'user_id' => Auth::id(),
                ]);

                $this->html = $data['html'];
                $this->css = $data['css'];
                $this->fileUpload = null;

                $this->dispatch('swal', [
                    'icon' => 'success',
                    'title' => 'Success',
                    'text' => 'Page HTML & CSS imported successfully!',
                ]);

                // Reload halaman setelah 1.5 detik
                $this->dispatch('reload-page', delay: 1500);
            }
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Failed to import JSON: ' . $e->getMessage(),
            ]);
        }
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

        return view('suryacms::livewire.admin.homepage-builder.index')->layout('frontend::editor');
    }
}
