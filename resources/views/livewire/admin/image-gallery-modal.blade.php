<div>
    @if ($isOpen)
    {{-- Backdrop untuk modal --}}
    <div class="modal-backdrop fade show"></div>

    {{-- Modal itu sendiri --}}
    <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-labelledby="imageGalleryModalLabel"
        aria-hidden="true" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageGalleryModalLabel">Select Image
                    </h5>
                    <button type="button" class="btn-close" aria-label="Close" wire:click="close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" wire:model.live="search" placeholder="Cari gambar..." class="form-control">
                    </div>

                    @if (session()->has('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3"
                        style="max-height: 50vh; overflow-y: auto;">
                        @forelse ($galleries as $gallery)
                        <div class="col">
                            <div class="card h-100 cursor-pointer"
                                onclick="copyImageUrlToClipboard('{{ Storage::url($gallery->image_path) }}')"
                                style="cursor: pointer;">
                                <img src="{{ Storage::url($gallery->image_path) }}" class="card-img-top"
                                    alt="{{ $gallery->name }}" style="height: 150px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <p class="card-text text-truncate small">{{ $gallery->name }}</p>
                                </div>
                                {{-- Overlay visual feedback --}}
                                <div class="card-img-overlay d-flex align-items-center justify-content-center bg-dark bg-opacity-50 text-white opacity-0 hover-opacity-100 transition-opacity"
                                    style="opacity:0; transition: opacity 0.2s ease-in-out;">
                                    <span class="text-center"><i class="fas fa-copy"></i> Copy and Paste <i
                                            class="fas fa-paste"></i> to Editor</span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <p class="text-center text-muted">Tidak ada gambar di galeri.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                {{-- Hapus modal-footer jika tidak diperlukan tombol lain --}}
            </div>
        </div>
    </div>
    @endif

    <style>
        /* Custom CSS untuk overlay hover pada gambar kartu */
        .card:hover .hover-opacity-100 {
            opacity: 1 !important;
        }
    </style>
</div>
