<?php

namespace Uiaciel\SuryaCms\Http\Livewire\Admin;

use Uiaciel\SuryaCms\Models\Gallery;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ImageGalleryModal extends Component
{
    public $isOpen = false;
    public $galleries = [];
    public $search = '';
    public $selectedImage = null;

    protected $listeners = ['openGalleryModal' => 'open', 'closeGalleryModal' => 'close'];

    public function mount()
    {
        $this->loadGalleries();
    }

    public function loadGalleries()
    {
        $query = Gallery::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->orWhere('category', 'like', '%' . $this->search . '%');
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
            // Dapatkan URL publik dari gambar
            $this->selectedImage = Storage::url($image->image_path);
            // Dispatch event kembali ke JavaScript dengan URL gambar yang dipilih
            $this->dispatch('imageSelectedFromGallery', ['url' => $this->selectedImage]);
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
