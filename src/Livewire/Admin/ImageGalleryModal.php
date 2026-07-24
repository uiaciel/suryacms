<?php

namespace Uiaciel\SuryaCms\Livewire\Admin;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Uiaciel\SuryaCms\Models\Gallery;
use Livewire\WithFileUploads;
use Spatie\PdfToImage\Pdf;
use Illuminate\Support\Str;

class ImageGalleryModal extends Component
{
    use WithFileUploads;

    public $isOpen = false;
    public $galleries = [];
    public $search = '';
    public $selectedImage = null;

    protected $listeners = [
        'openGalleryModal' => 'open',
        'closeGalleryModal' => 'close'
    ];

    public $uploadImage;
    public $uploadName;
    public $uploadCategory = 'POST';
    public $status = 'Publish';

    public function mount()
    {
        $this->loadGalleries();
    }

    public function uploadNewImage()
    {
        $this->validate([
            'uploadImage' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:30024',
            'uploadName' => 'required|string|min:3',
            'uploadCategory' => 'required|string',
        ]);

        $file = $this->uploadImage;
        $timestamp = now()->format('YmdHis');
        $slugTitle = Str::slug($this->uploadName);
        $extension = strtolower($file->getClientOriginalExtension());

        try {
            $fileSize = round($file->getSize() / 1024, 2) . ' KB';

            if ($extension === 'pdf') {
                $fileName = "{$timestamp}_gallery_{$slugTitle}.pdf";
                $path = $file->storeAs('galleries', $fileName, 'public');
                $mimeType = 'pdf';

                // Generate cover
                $coverPath = $this->generatePdfCover($path, $this->uploadName, $timestamp);
            } else {
                $manager = new ImageManager(new Driver());
                $fileName = "{$timestamp}_gallery_{$slugTitle}.webp";

                $convertedImage = $manager->read($file->getRealPath())
                                         ->encode(new WebpEncoder(quality: 70));

                Storage::disk('public')->put('galleries/' . $fileName, $convertedImage->__toString());
                $path = 'galleries/' . $fileName;
                $coverPath = $path;
                $mimeType = 'image';
            }

            $gallery = Gallery::create([
                'name' => $this->uploadName,
                'image_path' => $path,
                'cover_path' => $coverPath,
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
                'category' => strtoupper($this->uploadCategory),
                'status' => $this->status ?? 'Publish',
                'is_tinymce_upload' => false,
                'alt_text' => $this->uploadName,
            ]);

            $this->loadGalleries();
            $this->selectImage($gallery->id);
            $this->reset(['selectedImage', 'uploadImage', 'uploadName']);

            $this->dispatch('swal', ['icon' => 'success', 'title' => 'Success', 'text' => 'File uploaded successfully!']);

        } catch (\Exception $e) {
            Log::error('Modal upload error: ' . $e->getMessage());
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Error', 'text' => $e->getMessage()]);
        }
    }

    private function generatePdfCover(string $pdfPath, string $originalFilename, string $timestamp): ?string
    {
        try {
            $pdfFullPath = Storage::disk('public')->path($pdfPath);
            $slugName = Str::slug($originalFilename);
            $imageRelativePath = "galleries/covers/{$slugName}_{$timestamp}_cover.jpg";
            $imageFullPath = Storage::disk('public')->path($imageRelativePath);

            if (!file_exists(dirname($imageFullPath))) {
                mkdir(dirname($imageFullPath), 0755, true);
            }

            (new Pdf($pdfFullPath))
                ->selectPage(1)
                ->format(\Spatie\PdfToImage\Enums\OutputFormat::Jpg)
                ->save($imageFullPath);

            return $imageRelativePath;
        } catch (\Exception $e) {
            Log::warning('PDF Cover generation skipped: ' . $e->getMessage());
            return null;
        }
    }

    public function loadGalleries()
    {
        $query = Gallery::query();

        if ($this->search) {
            $query->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('description', 'like', '%'.$this->search.'%')
                ->orWhere('category', 'like', '%'.$this->search.'%');
        }

        $this->galleries = $query->latest()->get();
    }

    public function open()
    {
        $this->isOpen = true;
        $this->loadGalleries();
        $this->reset(['selectedImage', 'search']);
    }

    public function close()
    {
        $this->isOpen = false;
    }

    public function selectImage($id)
    {
        $image = Gallery::find($id);
        if ($image) {
            $isPdf = $image->mime_type === 'pdf' || Str::endsWith(strtolower($image->image_path), '.pdf');
            $fileUrl = asset(Storage::url($image->image_path));
            $coverUrl = $image->cover_path ? asset(Storage::url($image->cover_path)) : null;

            if ($isPdf) {
                $this->dispatch('pdfSelectedFromGallery', [
                    'url' => $fileUrl,
                    'cover_url' => $coverUrl,
                    'name' => $image->name,
                    'path' => $image->image_path
                ]);
            } else {
                $this->selectedImage = $fileUrl;
                $this->dispatch('imageSelectedFromGallery', [
                    'url' => $this->selectedImage,
                    'name' => $image->name,
                    'alt_text' => $image->alt_text ?? $image->name
                ]);
            }

            $this->close();
        }
    }

    public function updatedSearch()
    {
        $this->loadGalleries();
    }

    public function render()
    {
        return view('suryacms::livewire.admin.image-gallery-modal');
    }
}
