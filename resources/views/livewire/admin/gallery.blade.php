@extends('suryacms::layouts.app')
@section('content')
    <div class="mb-6" x-data="{
        search: '',
        category: '',
        showEditModal: false,
        showUploadModal: false,
        successMessage: 0,
        errorMessage: 0,

        selectedGallery: null,
        galleries: @js($galleries),
        categories: [...new Set(@js($galleries).map(g => g.category))],
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

        <!-- Header Section -->
        <header class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 mb-6">
            <div class="flex flex-wrap items-center gap-3">
                <h3 class="font-bold text-lg text-gray-900">Media Library</h3>
                <button type="button" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-full shadow-sm transition-colors" @click="showUploadModal = true">
                    <i class="bi bi-cloud-arrow-up-fill mr-2"></i> Upload Media
                </button>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="hidden sm:block">
                <ol class="flex gap-2 text-sm text-gray-500">
                    <li><a href="/admin" class="hover:text-gray-700 transition-colors">Admin</a></li>
                    <li class="before:content-['/'] before:mr-2">/</li>
                    <li><a href="/admin/galleries" class="hover:text-gray-700 transition-colors">Media Library</a></li>
                    <li class="before:content-['/'] before:mr-2">/</li>
                    <li class="text-gray-700">{{ $titlePage ?? 'Files' }}</li>
                </ol>
            </nav>
        </header>

        <!-- Alerts -->
        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-start gap-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <i class="bi bi-check-circle-fill text-green-600 text-lg mt-0.5"></i>
                <div class="flex-1">
                    <p class="font-semibold text-green-800">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-green-600 hover:text-green-800">
                    <i class="bi bi-x text-lg"></i>
                </button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <i class="bi bi-x-octagon-fill text-red-600 text-lg mt-0.5"></i>
                <div class="flex-1">
                    <p class="font-semibold text-red-800">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="text-red-600 hover:text-red-800">
                    <i class="bi bi-x text-lg"></i>
                </button>
            </div>
        @endif

        <!-- Gallery Section -->
        <div class="grid gap-6">
            <div class="col-span-full">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

                    <!-- Filter Bar -->
                    <div class="border-b border-gray-200 p-4">
                        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">

                            <!-- Category Filter Buttons -->
                            <div class="flex flex-wrap gap-2">
                                <button type="button" class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors" :class="category === '' ? 'bg-blue-600 text-white' : 'border border-blue-600 text-blue-600 hover:bg-blue-50'" @click="filterByCategory('')">
                                    All ({{ count($galleries) }})
                                </button>
                                <template x-for="cat in categories" :key="cat">
                                    <button type="button" class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors" :class="category === cat ? 'bg-blue-600 text-white' : 'border border-blue-600 text-blue-600 hover:bg-blue-50'" @click="filterByCategory(cat)" x-text="cat"></button>
                                </template>
                            </div>

                            <!-- Search Input -->
                            <input
                                type="text"
                                class="flex-1 md:max-w-xs px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="Search images..."
                                x-model="search"
                            >
                        </div>
                    </div>

                    <!-- Gallery Grid -->
                    <div class="p-6">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            <template x-for="gallery in filteredGalleries()" :key="gallery.id">
                                <div class="cursor-pointer group">
                                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-md overflow-hidden transition-shadow h-full flex flex-col border border-gray-100">
                                        <div class="aspect-square bg-gray-50 overflow-hidden flex items-center justify-center relative">

                                            <template x-if="gallery.category.toUpperCase() === 'PDF'">
                                                <div class="flex flex-col items-center justify-center w-full h-full bg-red-50 group-hover:bg-red-100 transition-colors" @click="openEditModal(gallery)">
                                                    <i class="fas fa-file-pdf text-5xl text-red-500"></i>
                                                    <span class="text-[10px] font-bold text-red-600 mt-2">PDF DOCUMENT</span>
                                                    <a :href="'{{ asset('storage') }}/' + gallery.image_path" target="_blank" class="absolute top-2 right-2 p-1.5 bg-white/80 hover:bg-white rounded-full shadow-sm text-gray-600" title="View PDF" @click.stop>
                                                        <i class="bi bi-eye text-sm"></i>
                                                    </a>
                                                </div>
                                            </template>

                                            <template x-if="gallery.category.toUpperCase() !== 'PDF'">
                                                <img
                                                    :src="'{{ asset('storage') }}/' + gallery.image_path"
                                                    :alt="gallery.name"
                                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                                                    @click="openEditModal(gallery)"
                                                >
                                            </template>
                                        </div>

                                        <div class="p-3 border-t border-gray-200 bg-white flex-1 flex flex-col">
                                            <p class="text-sm font-bold text-gray-900 truncate" x-text="gallery.name"></p>
                                            <div class="flex items-center justify-between mt-2">
                                                <span
                                                    class="text-[10px] uppercase tracking-wider font-bold px-2 py-0.5 rounded-full"
                                                    :class="gallery.status === 'Publish' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                                    x-text="gallery.status"
                                                ></span>
                                                <span class="text-[10px] text-gray-400 font-medium" x-text="gallery.category"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Empty State -->
                        <div x-show="filteredGalleries().length === 0" class="text-center py-16 text-gray-500">
                            <i class="bi bi-search text-6xl mb-4 block text-gray-300"></i>
                            <h5 class="font-light text-lg">No images found matching your criteria.</h5>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Upload Modal (Alpine) -->
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center transition-opacity" x-show="showUploadModal" x-transition @click="showUploadModal = false" style="display: none;">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto" @click.stop>
                <!-- Modal Header -->
                <div class="bg-blue-600 text-white p-4 flex items-center justify-between rounded-t-2xl">
                    <h5 class="text-lg font-bold flex items-center gap-2">
                        <i class="bi bi-plus-circle"></i> Upload New Image
                    </h5>
                    <button type="button" class="text-white hover:bg-blue-700 p-1 rounded transition-colors" @click="showUploadModal = false">
                        <i class="bi bi-x text-2xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-bold text-gray-900 mb-2">Title</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="name" name="name" required>
                        </div>
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-bold text-gray-900 mb-2">Description</label>
                            <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all resize-none" id="description" name="description" rows="2"></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="image_path" class="block text-sm font-bold text-gray-900 mb-2">
                                File <span class="text-gray-400 font-normal">(Image or PDF)</span>
                            </label>
                            <input type="file"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                id="image_path" name="image_path"
                                accept=".jpg,.png,.jpeg,.pdf"
                                required>
                            <p class="text-xs text-gray-500 mt-1">Max 2MB. Format: JPG, PNG, or PDF.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="category" class="block text-sm font-bold text-gray-900 mb-2">Category</label>
                                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all uppercase"
                            id="category" name="category" placeholder="e.g., PDF, SLIDER, POST" x-model="categoryHint">
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-bold text-gray-900 mb-2">Status</label>
                                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" id="status" name="status">
                                    <option value="Publish">Publish</option>
                                    <option value="Draft">Draft</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                                <i class="bi bi-upload"></i> Upload & Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal (Alpine) -->
        <div
            class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center transition-opacity"
            x-show="showEditModal"
            x-transition
            @click="closeEditModal()"
        >
            <div
                class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto"
                @click.stop
                x-show="selectedGallery"
            >
                <!-- Modal Header -->
                <div class="border-b border-gray-200 p-6 flex items-center justify-between">
                    <h5 class="text-lg font-bold text-gray-900">Edit Media</h5>
                    <button
                        type="button"
                        class="text-gray-500 hover:text-gray-700 p-1 rounded transition-colors"
                        @click="closeEditModal()"
                    >
                        <i class="bi bi-x text-2xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Image Preview -->
                        <div class="flex items-start justify-center">
                        <template x-if="selectedGallery.category.toUpperCase() === 'PDF'">
                            <embed
                                :src="'{{ asset('storage') }}/' + selectedGallery.image_path"
                                type="application/pdf"
                                class="w-full rounded-lg h-[500px]"
                            >
                        </template>
                        <template x-if="selectedGallery.category.toUpperCase() !== 'PDF'">
                            <img
                                :src="'{{ asset('storage') }}/' + selectedGallery.image_path"
                                class="w-full rounded-lg object-cover max-h-96"
                            >
                        </template>
                        </div>

                        <!-- Edit Form -->
                        <div>
                            <form @submit.prevent="
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
                                        // Show success notification
                                        document.querySelector('[data-success-notification]')?.classList.remove('hidden');
                                        setTimeout(() => {
                                            document.querySelector('[data-success-notification]')?.classList.add('hidden');
                                        }, 3000);
                                    }
                                }).catch(error => console.error(error));
                            ">
                                <!-- Title -->
                                <div class="mb-4">
                                    <label for="edit_name" class="block text-sm font-bold text-gray-900 mb-2">Title</label>
                                    <input
                                        type="text"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                        id="edit_name"
                                        name="name"
                                        x-model="selectedGallery.name"
                                        required
                                    >
                                </div>

                                <!-- Description -->
                                <div class="mb-4">
                                    <label for="edit_description" class="block text-sm font-bold text-gray-900 mb-2">Description</label>
                                    <textarea
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                                        id="edit_description"
                                        name="description"
                                        x-model="selectedGallery.description"
                                        rows="3"
                                    ></textarea>
                                </div>

                                <!-- Image Upload -->
                                <div class="mb-4">
                                    <label for="edit_image_path" class="block text-sm font-bold text-gray-900 mb-2">Update Image (Optional)</label>
                                    <input
                                        type="file"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                        id="edit_image_path"
                                        name="image_path"
                                    >
                                    <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image.</p>
                                </div>

                                <!-- Category & Status -->
                                <div class="grid grid-cols-2 gap-4 mb-6">
                                    <div>
                                        <label for="edit_category" class="block text-sm font-bold text-gray-900 mb-2">Category</label>
                                        <input
                                            type="text"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                            id="edit_category"
                                            name="category"
                                            x-model="selectedGallery.category"
                                        >
                                    </div>
                                    <div>
                                        <label for="edit_status" class="block text-sm font-bold text-gray-900 mb-2">Status</label>
                                        <select
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white"
                                            id="edit_status"
                                            name="status"
                                            x-model="selectedGallery.status"
                                        >
                                            <option value="Publish">Publish</option>
                                            <option value="Draft">Draft</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-3">
                                    <button
                                        type="submit"
                                        class="flex-1 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2"
                                    >
                                        <i class="bi bi-check-circle"></i> Update
                                    </button>
                                    <button
                                        type="button"
                                        @click="
                                            if (confirm('Are you sure you want to delete this image?')) {
                                                $wire.deleteImage(selectedGallery.id);
                                                closeEditModal();
                                            }
                                        "
                                        class="flex-1 px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2"
                                    >
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <livewire:suryacms::admin.youtube.youtube-list />
@endsection
