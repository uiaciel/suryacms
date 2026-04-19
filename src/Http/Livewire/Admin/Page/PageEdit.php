<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin\Page;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Uiaciel\SuryaCms\Models\Gallery;
use Uiaciel\SuryaCms\Models\Language;
use Uiaciel\SuryaCms\Models\Page;
use Uiaciel\SuryaCms\Models\Setting;

class PageEdit extends Component
{
    use WithFileUploads;

    public $pageId;

    public $titlePage;

    public $languages;

    public $setting;

    public $html;

    public $title;

    public $slug;

    public $konten;

    public $datepublish;

    public $language_id;

    public $status;

    public $pages;

    public $translation_id;

    public $pdf;

    public $pdfFile;

    public $showPdfModal = false;

    public $pdfUploadProgress = 0;

    public $pdfUploadStatus = '';

    public $galleriesPdf;

    public function getTranslations($languageId)
    {
        return response()->json(Page::where('language_id', '!=', $languageId)->get());
    }

    public function mount(): void
    {
        $this->pageId = request()->id;
        $page = Page::find($this->pageId);

        if ($page) {
            $this->titlePage = 'Edit Page';
            $this->title = $page->title;
            $this->slug = $page->slug;
            $this->konten = $page->content;
            $this->datepublish = $page->datepublish;
            $this->language_id = $page->language_id;
            $this->translation_id = $page->translation_id;
            $this->pdf = $page->pdf;
            $this->status = $page->status;
            $this->html = $page->html;
            $this->setting = Setting::first();
            $this->languages = Language::all();
            $this->galleriesPdf = Gallery::where('category', 'PDF')->get();
        } else {
            abort(404, 'Page not found');
        }
    }

    public function openPdfModal(): void
    {
        $this->showPdfModal = true;
    }

    public function closePdfModal(): void
    {
        $this->showPdfModal = false;
        $this->pdfFile = null;
        $this->pdfUploadStatus = '';
    }

    public function uploadPdf(): void
    {
        $this->validate([
            'pdfFile' => 'required|mimes:pdf|max:20480',
        ], [
            'pdfFile.required' => 'Please select a PDF file',
            'pdfFile.mimes' => 'File must be a PDF',
            'pdfFile.max' => 'PDF file cannot exceed 20MB',
        ]);

        try {
            $fileName = 'pdf_' . time() . '_' . Str::slug($this->title ?: 'untitled') . '.pdf';
            $path = 'pdfs/' . $fileName;

            Storage::disk('public')->put($path, file_get_contents($this->pdfFile->getRealPath()));

            Gallery::create([
                'name' => $this->title ?: 'Untitled PDF',
                'description' => 'PDF uploaded for page content',
                'image_path' => $path,
                'category' => 'PDF',
                'status' => 'Publish',
            ]);

            $this->pdfUploadStatus = 'success';
            $this->pdfFile = null;

            // Reload gallery list
            $this->galleriesPdf = Gallery::where('category', 'PDF')->get();

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Success',
                'text' => 'PDF uploaded successfully!',
            ]);
        } catch (\Exception $e) {
            $this->pdfUploadStatus = 'error';
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Failed to upload PDF: ' . $e->getMessage(),
            ]);
        }
    }

    public function insertPdfToContent(int $galleryId): void
    {
        $gallery = Gallery::find($galleryId);
        if (! $gallery) {
            return;
        }

        $url = Storage::url($gallery->image_path);
        $name = $gallery->name;

        $embedHtml = '<div class="pdf-embed my-4 p-4 border-2 border-red-200 rounded-lg bg-red-50 flex items-center gap-3 justify-between">'
            . '<div class="flex items-center gap-3">'
            . '<svg class="w-8 h-8 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">'
            . '<path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"></path>'
            . '</svg>'
            . '<span class="text-sm font-medium text-gray-900">PDF: ' . $name . '</span>'
            . '</div>'
            . '<a href="' . $url . '" target="_blank" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded transition-colors">'
            . 'Download'
            . '</a></div>';

        $this->konten .= $embedHtml;
        $this->closePdfModal();

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Success',
            'text' => 'PDF inserted to content!',
        ]);
    }

    public function insertPdfToPage(int $galleryId): void
    {
        $gallery = Gallery::find($galleryId);
        if (! $gallery) {
            return;
        }

        $this->pdf = Storage::url($gallery->image_path);
        $this->closePdfModal();

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Success',
            'text' => 'PDF attached to page successfully!',
        ]);
    }

    public function removePdf(): void
    {
        $this->pdf = '';
    }

    public function updatePage()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'konten' => 'required|string',
            'datepublish' => 'required|date',
            'language_id' => 'required|exists:languages,id',
            'translation_id' => 'nullable|exists:pages,id',
            'status' => 'required',
        ]);

        try {
            $page = Page::find($this->pageId);

            if ($page) {
                $page->update([
                    'title' => $this->title,
                    'content' => $this->konten,
                    'pdf' => $this->pdf ?: $page->pdf,
                    'datepublish' => $this->datepublish,
                    'language_id' => $this->language_id,
                    'translation_id' => $this->translation_id ?: null,
                    'status' => $this->status,
                    'slug' => Str::slug($this->title),
                    'user_id' => Auth::id(),
                ]);
            } else {
                abort(404, 'Page not found');
            }

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Success',
                'text' => 'Page updated successfully!',
            ]);

            session()->flash('success', 'Page updated successfully.');

            return $this->redirect('/admin/pages');
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Failed to update page: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('suryacms::livewire.admin.page.page-edit')->layout('suryacms::layouts.app');
    }
}
