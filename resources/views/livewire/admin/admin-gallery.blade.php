<x-layouts.app>
    <div x-data="{
            search: '',
            category: '',
            showEditModal: false,
            selectedGallery: null,
            galleries: @js($galleries),
            // Menggunakan Set untuk mendapatkan kategori unik dan mengonversinya menjadi array
            categories: [...new Set(@js($galleries).map(g => g.category).filter(c => c))],

            filteredGalleries() {
                return this.galleries.filter(g => {
                    let matchCategory = this.category === '' || g.category === this.category;
                    let matchSearch = g.name.toLowerCase().includes(this.search.toLowerCase()) ||
                                      (g.description && g.description.toLowerCase().includes(this.search.toLowerCase()));
                    return matchCategory && matchSearch;
                });
            },
            openEditModal(gallery) {
                this.selectedGallery = gallery;
                this.showEditModal = true;
            },
            closeEditModal() {
                this.showEditModal = false;
                this.selectedGallery = null;
            },
            filterByCategory(cat) {
                this.category = cat;
            },
        }" class="container-fluid py-4">

        <header class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
            <div class="d-flex flex-column">
                <h2 class="fw-bolder mb-0 text-primary">
                    <i class="bi bi-images me-2"></i> Media Library aaa
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small text-muted">
                        <li class="breadcrumb-item"><a href="/admin" class="text-decoration-none">Admin</a></li>
                        <li class="breadcrumb-item"><a href="/admin/galleries" class="text-decoration-none">Media Library</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $titlePage ?? 'Files' }}</li>
                    </ol>
                </nav>
            </div>

            <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadImageModal">
                <i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload Image
            </button>
        </header>

        @if (session()->has('success') || session()->has('error'))
        <div class="mb-4">
            @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show fw-semibold" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show fw-semibold" role="alert">
                <i class="bi bi-x-octagon-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
        </div>
        @endif

        <div class="row g-4">

            <div class="col-12">
                <div class="card shadow-lg rounded-4 border-0">
                    <div class="card-header bg-white p-3 border-bottom">
                        <h5 class="card-title fw-bold mb-0 text-dark">
                            <i class="bi bi-image me-2"></i> Image Library
                        </h5>
                    </div>

                    <div class="card-header border-bottom p-3">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">

                            <div class="d-flex flex-wrap gap-2 mb-3 mb-md-0 me-md-3">
                                <button type="button" class="btn btn-sm fw-semibold" :class="category === '' ? 'btn-primary' : 'btn-outline-primary'" @click="filterByCategory('')">All ({{ count($galleries) }})</button>
                                <template x-for="cat in categories" :key="cat">
                                    <button type="button" class="btn btn-sm fw-semibold" :class="category === cat ? 'btn-primary' : 'btn-outline-primary'" @click="filterByCategory(cat)" x-text="cat"></button>
                                </template>
                            </div>

                            <input type="text" class="form-control" style="max-width: 300px;" placeholder="Find image by title or description..." x-model="search">
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
                            <template x-for="gallery in filteredGalleries()" :key="gallery.id">
                                <div class="col">
                                    <div class="card h-100 shadow-sm rounded-3 overflow-hidden" style="cursor:pointer;" @click="openEditModal(gallery)">
                                        <div class="ratio ratio-1x1 bg-light">
                                            <img :src="'{{ asset('storage') }}/' + gallery.image_path" :alt="gallery.name" class="img-fluid w-100 h-100" style="object-fit: cover; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                                        </div>

                                        <div class="p-2 border-top bg-white">
                                            <div class="text-truncate fw-bold small" x-text="gallery.name"></div>
                                            <span class="badge rounded-pill" :class="gallery.status === 'Publish' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'" x-text="gallery.status"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div x-show="filteredGalleries().length === 0" class="text-center p-5 text-muted">
                            <i class="bi bi-search display-4 mb-3"></i>
                            <h5 class="fw-light">No images found matching your criteria.</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card shadow-lg rounded-4 border-0">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white p-3 border-bottom">
                        <h5 class="card-title fw-bold mb-0 text-dark">
                            <i class="bi bi-youtube me-2 text-danger"></i> Video Library (YouTube)
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-danger fw-semibold" data-bs-toggle="modal" data-bs-target="#youtubeModal">
                            <i class="bi bi-plus-circle-fill me-1"></i> Add YT Video
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <div class="list-group list-group-flush">
                            @forelse($videos as $video)
                            <div class="list-group-item d-flex justify-content-between align-items-center py-3 px-0" wire:key="video-{{ $video->id }}">
                                <div class="d-flex align-items-center">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#youtubeVideoModal{{$video->id}}" class="me-3 flex-shrink-0">
                                        <img src="{{ $video->thumbnail_url }}" alt="thumb" class="rounded-3 shadow-sm" style="width: 120px; height: 80px; object-fit: cover;">
                                    </a>

                                    <div class="ms-2">
                                        <div class="fw-bold text-dark mb-1">{{ $video->title }}</div>
                                        <span class="badge bg-secondary me-2">{{ $video->category }}</span>
                                        <button class="badge bg-primary border-0" data-bs-toggle="modal" data-bs-target="#youtubeVideoModal{{$video->id}}">
                                            <i class="bi bi-play-circle me-1"></i> View
                                        </button>
                                        <button class="badge bg-danger border-0" wire:click="delete({{ $video->id }})" onclick="return confirm('Are You Sure To Delete This Video?')">
                                            <i class="bi bi-trash-fill me-1"></i> Delete
                                        </button>
                                    </div>
                                </div>

                                <div class="ms-3 flex-shrink-0">
                                    <select class="form-select form-select-sm" wire:model="statusPerVideo.{{ $video->id }}" wire:change="editStatus({{ $video->id }})">
                                        <option value="published">Published</option>
                                        <option value="draft">Draft</option>
                                    </select>
                                </div>
                            </div>
                            @include('layouts.youtube-modal', ['video' => $video])
                            @empty
                            <div class="text-center p-3 text-muted">
                                <i class="bi bi-film me-2"></i> No YouTube videos found.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="modal fade" :class="{'show d-block': showEditModal}" tabindex="-1" aria-modal="true" role="dialog" style="background:rgba(0,0,0,0.5);" x-show="showEditModal" x-cloak>
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg" x-show="selectedGallery" @click.outside="closeEditModal()">
                    <div class="modal-header bg-light border-bottom">
                        <h5 class="modal-title fw-bold text-primary">
                            <i class="bi bi-pencil-square me-2"></i> Edit Image Details
                        </h5>
                        <button type="button" class="btn-close" @click="closeEditModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <img :src="'{{ asset('storage') }}/' + selectedGallery.image_path" class="img-fluid rounded-3 shadow-sm w-100" :alt="selectedGallery.name">
                                <div class="mt-2 small text-muted text-center">
                                    **Click image to open in new tab**
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <form @submit.prevent="
                                    // Perhatikan: Inline Fetch Update memerlukan penanganan form data yang benar di backend.
                                    // Untuk demo ini, diasumsikan endpoint POST Anda menangani simulasi PATCH/PUT.
                                    fetch('{{ url('admin/gallery') }}/' + selectedGallery.id, {
                                        method: 'POST', // Gunakan POST atau PATCH/PUT jika didukung framework/server Anda
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Content-Type': 'application/json' // Ubah jika menggunakan FormData
                                        },
                                        body: JSON.stringify({
                                            _method: 'PUT', // Simulasikan PUT method
                                            name: selectedGallery.name,
                                            description: selectedGallery.description,
                                            category: selectedGallery.category,
                                            status: selectedGallery.status
                                            // Catatan: File upload (image_path) tidak dapat ditangani melalui JSON fetch.
                                            // Perlu form submit atau Ajax FormData untuk file.
                                        })
                                    }).then(response => {
                                        if(response.ok) {
                                            alert('Image details updated successfully (Simulated)');
                                            closeEditModal();
                                            // Refresh page atau update galleries array secara manual
                                            location.reload(); // Pilihan mudah untuk refresh data setelah update
                                        } else {
                                            alert('Failed to update image details.');
                                        }
                                    }).catch(error => {
                                         alert('Error: ' + error);
                                    });
                                ">
                                    <div class="form-group mb-3">
                                        <label for="edit_name" class="form-label fw-bold">Title</label>
                                        <input type="text" class="form-control rounded-3" id="edit_name" name="name" x-model="selectedGallery.name" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="edit_description" class="form-label fw-bold">Description</label>
                                        <textarea class="form-control rounded-3" id="edit_description" name="description" x-model="selectedGallery.description"></textarea>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label for="edit_category" class="form-label fw-bold">Category</label>
                                            <input type="text" class="form-control rounded-3" id="edit_category" name="category" x-model="selectedGallery.category">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="edit_status" class="form-label fw-bold">Status</label>
                                            <select class="form-select rounded-3" id="edit_status" name="status" x-model="selectedGallery.status">
                                                <option value="Publish">Publish</option>
                                                <option value="Draft">Draft</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between pt-3 border-top mt-4">
                                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                                            <i class="bi bi-save me-1"></i> Update Details
                                        </button>
                                        <button type="button" x-on:click="
                                            if (confirm('Are you sure you want to delete this image permanently?')) {
                                                // Diasumsikan $wire.deleteImage adalah Livewire action
                                                @this.deleteImage(selectedGallery.id);
                                                closeEditModal();
                                            }
                                        " class="btn btn-outline-danger rounded-pill px-4 fw-semibold">
                                            <i class="bi bi-trash3 me-1"></i> Delete
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="uploadImageModal" tabindex="-1" aria-labelledby="uploadImageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-4 shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="uploadImageModalLabel">
                            <i class="bi bi-plus-circle me-2"></i> Upload New Image
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="name" class="form-label fw-bold">Title</label>
                                <input type="text" class="form-control rounded-3" id="name" name="name" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="description" class="form-label fw-bold">Description</label>
                                <textarea class="form-control rounded-3" id="description" name="description"></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label for="image_path" class="form-label fw-bold">Upload Images</label>
                                <input type="file" class="form-control rounded-3" id="image_path" name="image_path" required>
                                <div class="form-text">Max file size 2MB. Allowed formats: JPG, PNG.</div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="category" class="form-label fw-bold">Category</label>
                                    <input type="text" class="form-control rounded-3" id="category" name="category" placeholder="e.g., Slider, Post, Thumbnail">
                                </div>
                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-bold">Status</label>
                                    <select class="form-select rounded-3" id="status" name="status">
                                        <option value="Publish">Publish</option>
                                        <option value="Draft">Draft</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary rounded-pill fw-semibold py-2">
                                    <i class="bi bi-upload me-1"></i> Upload & Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="youtubeModal" tabindex="-1" aria-labelledby="youtubeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content rounded-4 shadow-lg">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold" id="youtubeModalLabel">
                            <i class="bi bi-youtube me-2"></i> Add YouTube Video
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <livewire:suryacms::admin.youtube.youtube-create />
                    </div>
                </div>
            </div>
        </div>

        @foreach($videos as $video)
        <div class="modal fade" id="youtubeVideoModal{{$video->id}}" tabindex="-1" aria-labelledby="youtubeVideoModalLabel{{$video->id}}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-4 shadow-lg">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="youtubeVideoModalLabel{{$video->id}}">Video Preview: {{ $video->title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="ratio ratio-16x9">
                            <iframe id="youtubeVideoIframe{{$video->id}}" src="https://www.youtube.com/embed/{{ $video->video_id }}?autoplay=0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

    </div>
</x-layouts.app>
