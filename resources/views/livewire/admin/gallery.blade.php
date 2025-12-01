@extends('suryacms::layouts.app')
@section('content')
        <div class="mb-3" x-data="{
            search: '',
            category: '',
            showEditModal: false,

            selectedGallery: null,
            galleries: @js($galleries),
            categories: [...new Set(@js($galleries).map(g => g.category))], // Extract unique categories
            filteredGalleries() {
                return this.galleries.filter(g => {
                    let matchCategory = this.category === '' || g.category === this.category;
                    let matchSearch = g.name.toLowerCase().includes(this.search.toLowerCase());
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

        }">

        <header class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
            <div class="d-flex flex-column">
                <h2 class="fw-bolder mb-0 text-primary">
                    <i class="bi bi-images me-2"></i> Media Library
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

            <div class="col-lg-12">

                <div class="card shadow-lg rounded-4 border-0 ">

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

                    <div class="card-body">

                        <!-- Gallery Grid -->
                        {{-- <div class="row">
                            <template x-for="gallery in filteredGalleries()" :key="gallery.id">
                                <div class="col-lg-3 col-md-3  mb-1 d-flex align-items-stretch">
                                    <div class="card w-100 overflow-hidden" style="cursor:pointer;" @click="openEditModal(gallery)">
                                        <img :src="'{{ asset('storage') }}/' + gallery.image_path" class="card-img-top img-fluid" :alt="gallery.name" style="object-fit: cover; height: 200px; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">

                                    </div>

                                </div>
                            </template>
                        </div> --}}
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

        <!-- Edit Modal (Alpine) -->
        <div class="modal fade" :class="{'show d-block': showEditModal}" tabindex="-1" style="background:rgba(0,0,0,0.5);" x-show="showEditModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" x-show="selectedGallery">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Image</h5>
                        <button type="button" class="btn-close" @click="closeEditModal"></button>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-lg-6">
                                <img :src="'{{ asset('storage') }}/' + selectedGallery.image_path" class="img-fluid mt-2">

                            </div>
                            <div class="col-lg-6">
                                <form @submit.prevent="
                                                                    // Inline update logic
                                                                    fetch('{{ url('admin/gallery') }}/' + selectedGallery.id, {
                                                                        method: 'POST',
                                                                        headers: {
                                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                            'Accept': 'application/json'
                                                                        },
                                                                        body: new FormData($event.target)
                                                                    }).then(response => {
                                                                        if(response.ok) {
                                                                            closeEditModal();
                                                                            // Optionally refresh galleries or show success
                                                                        }
                                                                    });
                                                                ">
                                    <div class="form-group mb-3">
                                        <label for="edit_name">Title</label>
                                        <input type="text" class="form-control" id="edit_name" name="name" x-model="selectedGallery.name" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="edit_description">Description</label>
                                        <textarea class="form-control" id="edit_description" name="description" x-model="selectedGallery.description"></textarea>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="edit_image_path">Image</label>
                                        <input type="file" class="form-control" id="edit_image_path" name="image_path">
                                    </div>

                                    <div class="form-group mb-3">
                                        <div class="row align-items-end">
                                            <div class="col-lg-6">
                                                <label for="edit_category">Category</label>
                                                <input type="text" class="form-control" id="edit_category" name="category" x-model="selectedGallery.category">
                                            </div>
                                            <div class="col-lg-6">
                                                <label for="edit_status">Status</label>
                                                <select class="form-control" id="edit_status" name="status" x-model="selectedGallery.status">
                                                    <option value="Publish">Publish</option>
                                                    <option value="Draft">Draft</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3 d-flex justify-content-between">

                                        <button type="submit" class="btn btn-primary">Update</button>
                                        <button type="button" x-on:click="
                                            if (confirm('Are you sure you want to delete this image?')) {
                                                $wire.deleteImage(selectedGallery.id);
                                                closeEditModal();
                                            }
                                        " class="btn btn-danger">Delete</button>

                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>

    <livewire:suryacms::admin.youtube.youtube-list />
@endsection

