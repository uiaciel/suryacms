<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin;

use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Drivers\Gd\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Uiaciel\SuryaCms\Models\Gallery;
use Livewire\WithFileUploads;

class ImageGalleryModal extends Component
{
    use WithFileUploads;
    public $isOpen = false;

    public $galleries = [];

    public $search = '';

    public $selectedImage = null;

    protected $listeners = ['openGalleryModal' => 'open', 'closeGalleryModal' => 'close'];

    public $uploadImage;
    public $uploadName;
    public $uploadCategory = 'POST';

    public function mount()
    {
        $this->loadGalleries();
    }

    public function uploadNewImage()
{
    $this->validate([
        'uploadImage' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:20048',
        'uploadName' => 'required|string|min:3',
        'uploadCategory' => 'required|string',
    ]);

    $file = $this->uploadImage;
    $timestamp = now()->format('YmdHis');
    $slugTitle = str_replace(' ', '_', strtolower($this->uploadName));
    $extension = $file->getClientOriginalExtension();

    try {
        if (strtolower($extension) === 'pdf') {
            $fileName = "{$timestamp}_gallery_{$slugTitle}.pdf";
            $path = $file->storeAs('galleries', $fileName, 'public');
        } else {
            // Proses Gambar ke WebP
            $manager = new ImageManager(new Driver());
            $fileName = "{$timestamp}_gallery_{$slugTitle}.webp";

            $convertedImage = $manager->read($file->getRealPath())
                                      ->encode(new WebpEncoder(quality: 70));

            Storage::disk('public')->put('galleries/' . $fileName, $convertedImage->__toString());
            $path = 'galleries/' . $fileName;
        }

        $gallery = Gallery::create([
            'name' => $this->uploadName,
            'image_path' => $path,
            'category' => strtoupper($this->uploadCategory),
            'status' => $this->status ?? 'Publish', // Pastikan properti status ada
        ]);


        $this->loadGalleries();
        $this->selectImage($gallery->id);
        $this->reset(['selectedImage','uploadImage', 'uploadName']);


        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Success', 'text' => 'File uploaded successfully!']);

    } catch (\Exception $e) {
        $this->dispatch('swal', ['icon' => 'error', 'title' => 'Error', 'text' => $e->getMessage()]);
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

        $this->galleries = $query->latest()->get(); // Urutkan berdasarkan yang terbaru
    }

    public function open()
    {
        $this->isOpen = true;
        $this->loadGalleries(); // Muat ulang galeri setiap kali modal dibuka
        $this->reset(['selectedImage', 'search']); // Reset state, hapus 'uploadName', dll.
    }

    public function close()
    {
        $this->isOpen = false;
        // Tidak perlu dispatch 'imageSelected' saat close jika tidak ada yang dipilih.
        // Event hanya di-dispatch saat selectImage dipanggil.
    }

    public function selectImage($id)
    {
        $image = Gallery::find($id);
        if ($image) {

            $extension = strtolower(pathinfo($image->image_path, PATHINFO_EXTENSION));

            if ($extension === 'pdf') {
                // Ensure PDF URL is always absolute full URL
                $pdfUrl = asset(Storage::url($image->image_path));
                $this->dispatch('pdfSelectedFromGallery', [
                    'url' => $pdfUrl,
                    'name' => $image->name,
                    'path' => $image->image_path
                ]);
                \Log::info('PDF selected from gallery with full URL: ' . $pdfUrl);
            } else {
                $url = asset(Storage::url($image->image_path));
                $this->selectedImage = $url;
                $this->dispatch('imageSelectedFromGallery', ['url' => $this->selectedImage]);
            }

            $this->close(); // Tutup modal setelah memilih
        }
    }

    public function updatedSearch()
    {
        $this->loadGalleries();
    }

    public function deleteImage($id)
    {
        $image = Gallery::find($id);
        if ($image) {
            $image->delete();
            $this->loadGalleries();
            session()->flash('message', 'Image deleted successfully.');
        } else {
            session()->flash('error', 'Image not found.');
        }
    }

    public function render()
    {
        return view('suryacms::livewire.admin.image-gallery-modal');
    }
}
