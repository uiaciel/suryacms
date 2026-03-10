<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Uiaciel\SuryaCms\Models\Gallery;

class PdfModal extends Component
{
    use WithFileUploads;

    public bool $showPdfModal = false;

    public $pdfFile = null;

    public ?string $selectedPdfUrl = null;

    public string $pdfUploadStatus = '';

    public string $pageTitle = '';

    protected $listeners = ['openPdfModal' => 'openModal', 'closePdfModal' => 'closeModal'];

    public function openModal()
    {
        $this->showPdfModal = true;
    }

    public function closeModal()
    {
        $this->showPdfModal = false;
        $this->pdfFile = null;
        $this->pdfUploadStatus = '';
    }

    public function uploadPdf()
    {
        $this->validate([
            'pdfFile' => 'required|mimes:pdf|max:20480',
        ], [
            'pdfFile.required' => 'Pilih file PDF terlebih dahulu',
            'pdfFile.mimes' => 'File harus berformat PDF',
            'pdfFile.max' => 'Ukuran file tidak boleh melebihi 20MB',
        ]);

        try {
            $fileName = 'pdf_' . time() . '_' . Str::slug($this->pageTitle ?: 'untitled') . '.pdf';
            $path = 'pdfs/' . $fileName;

            Storage::disk('public')->put($path, file_get_contents($this->pdfFile->getRealPath()));

            Gallery::create([
                'name' => $this->pageTitle ?: 'PDF File',
                'description' => 'PDF uploaded for page content',
                'image_path' => $path,
                'category' => 'PDF',
                'status' => 'Publish',
            ]);

            $this->selectedPdfUrl = Storage::url($path);
            $this->pdfUploadStatus = 'success';
            $this->pdfFile = null;

            $this->dispatch('pdfUploaded', [
                'url' => $this->selectedPdfUrl,
                'name' => basename($this->selectedPdfUrl),
            ]);

        } catch (\Exception $e) {
            $this->pdfUploadStatus = 'error';
            \Log::error('PDF Upload Error: ' . $e->getMessage());
        }
    }

    public function insertPdfToContent($pdfUrl = null, $pdfName = null): void
    {
        if (! $pdfUrl && ! $this->selectedPdfUrl) {
            return;
        }

        $url = $pdfUrl ?? $this->selectedPdfUrl;
        $name = $pdfName ?? basename($url);

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

        $this->dispatch('pdfInsertedToContent', [
            'url' => $url,
            'name' => $name,
            'html' => $embedHtml,
        ]);

        $this->closePdfModal();

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Success',
            'text' => 'PDF inserted to content!',
        ]);
    }

    public function insertPdfToPage($pdfUrl = null): void
    {
        if (! $pdfUrl && ! $this->selectedPdfUrl) {
            return;
        }

        $url = $pdfUrl ?? $this->selectedPdfUrl;
        $this->pdf = $url;
        $this->selectedPdfUrl = null;

        $this->closePdfModal();

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Success',
            'text' => 'PDF attached to page successfully!',
        ]);
    }

    public function removePdf()
    {
        $this->selectedPdfUrl = null;
    }

    public function render()
    {
        return view('suryacms::livewire.admin.pdf-modal');
    }
}
