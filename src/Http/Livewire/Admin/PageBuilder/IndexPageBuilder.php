<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\PageBuilder;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Uiaciel\SuryaCms\Models\Page;
use Uiaciel\SuryaCms\Models\Post;
use Uiaciel\SuryaCms\Services\HtmlToGrapeJsConverter;
use Livewire\Attributes\On;

class IndexPageBuilder extends Component
{
    public $titlePage = 'Page Builder';

    public $pagesbuilder;

    public $showModal = false;

    public $selectedPageId = null;

    public $rawHtml = '';

    public $rawCss = '';

    public $isLoading = false;

    public function mount()
    {
        $this->pagesbuilder = Page::where('is_builder', 1)->get();
    }

    public function openInputModal($pageId)
    {
        $page = Page::find($pageId);
        if ($page) {
            $this->selectedPageId = $pageId;
            $this->rawHtml = $page->html ?? '';
            $this->rawCss = $page->css ?? '';
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedPageId = null;
        $this->rawHtml = '';
        $this->rawCss = '';
    }

    public function saveHtmlCss()
    {
        // Validasi input
        if (empty($this->rawHtml)) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Validation Error',
                'text' => 'HTML content cannot be empty'
            ]);
            return;
        }

        try {
            $this->isLoading = true;

            $page = Page::find($this->selectedPageId);
            if (!$page) {
                throw new \Exception('Page not found');
            }

            // Convert HTML to GrapeJS compatible format
            $converter = new HtmlToGrapeJsConverter();
            $convertedHtml = $converter->convert($this->rawHtml);

            // Save to database
            $page->update([
                'html' => $convertedHtml,
                'css' => $this->rawCss,
            ]);

            $this->isLoading = false;

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Success',
                'text' => 'HTML and CSS have been converted and saved. Ready for GrapeJS editing!'
            ]);

            // Refresh the list
            $this->pagesbuilder = Page::where('is_builder', 1)->get();
            $this->closeModal();
        } catch (\Exception $e) {
            $this->isLoading = false;

            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => $e->getMessage()
            ]);
        }
    }

    public function delete($pageId)
    {
        try {
            $page = Page::find($pageId);
            if ($page) {
                $page->delete();
                $this->pagesbuilder = Page::where('is_builder', 1)->get();

                $this->dispatch('swal', [
                    'icon' => 'success',
                    'title' => 'Deleted',
                    'text' => 'Page has been deleted successfully'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('suryacms::livewire.admin.page-builder.index')->layout('suryacms::layouts.app');
    }
}
