<?php

namespace Uiaciel\SuryaCms\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToImage\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Uiaciel\SuryaCMS\Models\Gallery as ModelsGallery;

class Gallery extends Component
{
    use WithFileUploads;

    public $selected_id;
    public $filename;
    public $filealt_text;
    public $filedescription;
    public $fileimage_path; // Menampung temporary file upload
    public $existing_file_path; // Path file aktual di DB
    public $filecategory;
    public $filestatus = 'Publish';
    public $cover_path;
    public $coverPreview;
    public $mime_type;

    public $isEdit = false;

    protected function rules()
    {
        return [
            'filename' => 'required|string|max:255',
            'filedescription' => 'nullable|string',
            'fileimage_path' => $this->isEdit ? 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,pdf|max:30024' : 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,pdf|max:30024',
            'filecategory' => 'nullable|string|max:255',
            'filestatus' => 'in:Publish,Draft',
            'filealt_text' => 'nullable|string|max:255',
        ];
    }

    // Auto-fill nama file jika field name masih kosong saat upload
    public function updatedFileimagePath()
    {
        $this->validateOnly('fileimage_path');

        if ($this->fileimage_path && empty($this->filename)) {
            $originalName = $this->fileimage_path->getClientOriginalName();
            $this->filename = pathinfo($originalName, PATHINFO_FILENAME);
        }
    }

    public function resetFields()
    {
        $this->reset([
            'selected_id',
            'filename',
            'filealt_text',
            'filedescription',
            'fileimage_path',
            'existing_file_path',
            'filecategory',
            'cover_path',
            'coverPreview',
            'mime_type',
            'isEdit',
        ]);
        $this->filestatus = 'Publish';
    }

    public function saveGallery()
    {
        $this->validate();

        try {
            $gallery = new ModelsGallery();
            $gallery->name = $this->filename;
            $gallery->description = $this->filedescription;
            $gallery->category = $this->filecategory;
            $gallery->status = $this->filestatus;
            $gallery->alt_text = $this->filealt_text ?: $this->filename;

            if ($this->fileimage_path) {
                $file = $this->fileimage_path;
                $originalFilename = $file->getClientOriginalName();
                $extension = strtolower($file->getClientOriginalExtension());
                $time = time();
                $slugName = Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME));

                $filename = "{$slugName}_{$time}.{$extension}";
                $path = $file->storeAs('galleries', $filename, 'public');

                $gallery->image_path = $path;
                $gallery->file_size = round($file->getSize() / 1024, 2) . ' KB';

                if ($extension === 'pdf') {
                    $gallery->mime_type = 'pdf';
                    $generatedCover = $this->generatePdfCover($path, $originalFilename, $time);
                    $gallery->cover_path = $generatedCover;
                } else {
                    $gallery->mime_type = 'image';
                    $gallery->cover_path = $path;
                }
            }

            $gallery->save();

            $this->resetFields();
            $this->dispatch('notify', type: 'success', message: 'Gallery created successfully.');
        } catch (\Exception $e) {
            Log::error('Save Gallery Error: ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to save gallery: ' . $e->getMessage());
        }
    }

    public function editGallery($id)
    {
        $gallery = ModelsGallery::findOrFail($id);

        $this->selected_id = $gallery->id;
        $this->filename = $gallery->name;
        $this->filealt_text = $gallery->alt_text;
        $this->filedescription = $gallery->description;
        $this->filecategory = $gallery->category;
        $this->filestatus = $gallery->status;
        $this->existing_file_path = $gallery->image_path;
        $this->cover_path = $gallery->cover_path;
        $this->mime_type = $gallery->mime_type;
        $this->isEdit = true;
    }

    public function updateGallery()
    {
        $this->validate();

        try {
            $gallery = ModelsGallery::findOrFail($this->selected_id);
            $gallery->name = $this->filename;
            $gallery->description = $this->filedescription;
            $gallery->category = $this->filecategory;
            $gallery->status = $this->filestatus;
            $gallery->alt_text = $this->filealt_text ?: $this->filename;

            if ($this->fileimage_path) {
                // Hapus file lama di storage
                if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
                    Storage::disk('public')->delete($gallery->image_path);
                }
                if ($gallery->cover_path && $gallery->cover_path !== $gallery->image_path && Storage::disk('public')->exists($gallery->cover_path)) {
                    Storage::disk('public')->delete($gallery->cover_path);
                }

                $file = $this->fileimage_path;
                $originalFilename = $file->getClientOriginalName();
                $extension = strtolower($file->getClientOriginalExtension());
                $time = time();
                $slugName = Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME));

                $filename = "{$slugName}_{$time}.{$extension}";
                $path = $file->storeAs('galleries', $filename, 'public');

                $gallery->image_path = $path;
                $gallery->file_size = round($file->getSize() / 1024, 2) . ' KB';

                if ($extension === 'pdf') {
                    $gallery->mime_type = 'pdf';
                    $gallery->cover_path = $this->generatePdfCover($path, $originalFilename, $time);
                } else {
                    $gallery->mime_type = 'image';
                    $gallery->cover_path = $path;
                }
            }

            $gallery->save();

            $this->resetFields();
            $this->dispatch('notify', type: 'success', message: 'Gallery updated successfully.');
        } catch (\Exception $e) {
            Log::error('Update Gallery Error: ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to update gallery: ' . $e->getMessage());
        }
    }

    // Method baru untuk meregenerasi cover PDF secara manual jika terjadi kegagalan
    public function regenerateCover($id = null)
    {
        $galleryId = $id ?? $this->selected_id;
        if (!$galleryId) return;

        $gallery = ModelsGallery::findOrFail($galleryId);

        if ($gallery->mime_type !== 'pdf' || !$gallery->image_path) {
            $this->dispatch('notify', type: 'warning', message: 'File is not a PDF.');
            return;
        }

        $time = time();
        $originalFilename = basename($gallery->image_path);

        // Hapus cover lama jika ada
        if ($gallery->cover_path && Storage::disk('public')->exists($gallery->cover_path)) {
            Storage::disk('public')->delete($gallery->cover_path);
        }

        $newCoverPath = $this->generatePdfCover($gallery->image_path, $originalFilename, $time);

        if ($newCoverPath) {
            $gallery->cover_path = $newCoverPath;
            $gallery->save();
            $this->cover_path = $newCoverPath;
            $this->dispatch('notify', type: 'success', message: 'PDF cover re-generated successfully.');
        } else {
            $this->dispatch('notify', type: 'error', message: 'Failed to re-generate PDF cover.');
        }
    }

    public function deleteGallery($id)
    {
        try {
            $gallery = ModelsGallery::findOrFail($id);

            if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
                Storage::disk('public')->delete($gallery->image_path);
            }
            if ($gallery->cover_path && $gallery->cover_path !== $gallery->image_path && Storage::disk('public')->exists($gallery->cover_path)) {
                Storage::disk('public')->delete($gallery->cover_path);
            }

            $gallery->delete();
            $this->dispatch('notify', type: 'success', message: 'Gallery deleted successfully.');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Failed to delete gallery.');
        }
    }

    private function generatePdfCover(string $pdfPath, string $originalFilename, int $time): ?string
    {
        try {
            $pdfFullPath = Storage::disk('public')->path($pdfPath);
            $slugName = Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME));
            $imageRelativePath = "galleries/covers/{$slugName}_{$time}_cover.jpg";
            $imageFullPath = Storage::disk('public')->path($imageRelativePath);

            if (!file_exists(dirname($imageFullPath))) {
                mkdir(dirname($imageFullPath), 0755, true);
            }

            (new Pdf($pdfFullPath))
                ->selectPage(1)
                ->format(\Spatie\PdfToImage\Enums\OutputFormat::Jpg)
                ->save($imageFullPath);

            $this->coverPreview = Storage::url($imageRelativePath);
            return $imageRelativePath;

        } catch (\Exception $e) {
            Log::warning('PDF cover image generation failed', [
                'error' => $e->getMessage(),
                'pdf_path' => $pdfPath,
            ]);

            $this->dispatch('notify', type: 'warning', message: 'PDF uploaded, but cover image generation failed.');
            return null;
        }
    }

    public function render()
    {
        return view('suryacms::livewire.admin.gallery', [
            'galleries' => ModelsGallery::latest()->get(),
            'isEdit' => $this->isEdit,
        ])->layout('suryacms::layouts.app');
    }
}
