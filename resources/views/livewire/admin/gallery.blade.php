<div class="mb-6" x-data="{
    search: '',
    showModal: false,
}" x-on:notify.window="showModal = false;">

    <!-- Header Section -->
    <header class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <div>
            <h3 class="font-bold text-2xl text-gray-900">Media Library</h3>
            <p class="text-sm text-gray-500">Kelola berkas gambar dan dokumen PDF perusahaan Anda.</p>
        </div>

        <button type="button"
            class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl shadow-sm transition-colors gap-2 cursor-pointer"
            @click="$wire.resetFields(); showModal = true;">
            <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
            <span>Upload Media</span>
        </button>
    </header>

    <x-suryacms::session-status />
    <x-suryacms::toast-alert />

    <!-- Global Search & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <div class="relative w-full max-w-md">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fa fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text"
                class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm transition-all"
                placeholder="Cari media berdasarkan judul..." x-model="search">
        </div>
    </div>

    <!-- MAIN CONTENT GRID: 2 CARD TERPISAH -->
    <div class="space-y-8">

        <!-- CARD 1: IMAGE GALLERY -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                        <i class="fa-solid fa-image text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">Image Gallery</h4>
                        <p class="text-xs text-gray-500">Koleksi berkas gambar (JPG, PNG, WEBP, GIF)</p>
                    </div>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-blue-50 text-blue-600 rounded-full">
                    {{ $galleries->where('mime_type', 'image')->count() }} Files
                </span>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @forelse($galleries->where('mime_type', 'image') as $gallery)
                        <div class="cursor-pointer group relative"
                            x-show="!search || '{{ strtolower($gallery->name) }}'.includes(search.toLowerCase())"
                            @click="$wire.editGallery({{ $gallery->id }}); showModal = true;">

                            <div
                                class="bg-white rounded-xl shadow-xs hover:shadow-md overflow-hidden transition-all duration-200 h-full flex flex-col border border-gray-100 group-hover:border-blue-200">
                                <div class="aspect-square bg-gray-50 overflow-hidden relative">
                                    <img src="{{ asset('storage/' . $gallery->image_path) }}"
                                        alt="{{ $gallery->alt_text ?? $gallery->name }}"
                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">

                                    <!-- Quick Actions Overlay -->
                                    <div
                                        class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                        <a href="{{ asset('storage/' . $gallery->image_path) }}" target="_blank"
                                            class="p-2 bg-white/90 hover:bg-white text-gray-800 rounded-lg text-xs font-medium shadow-sm transition-transform hover:scale-105"
                                            title="Lihat Gambar Full" @click.stop>
                                            <i class="fa-solid fa-arrows-alt"></i>
                                        </a>
                                        <button type="button"
                                            class="p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium shadow-sm transition-transform hover:scale-105"
                                            title="Edit Media">
                                            <i class="fa-solid fa-pencil"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-3 bg-white flex-1 flex flex-col justify-between border-t border-gray-50">
                                    <p class="text-xs font-bold text-gray-800 truncate" title="{{ $gallery->name }}">
                                        {{ $gallery->name }}
                                    </p>
                                    <div class="flex items-center justify-between mt-2 text-[10px]">
                                        <span
                                            class="px-2 py-0.5 rounded-md font-bold {{ $gallery->status === 'Publish' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $gallery->status }}
                                        </span>
                                        <span
                                            class="text-gray-400 font-medium truncate max-w-[70px]">{{ $gallery->file_size ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12 text-gray-400">
                            <i class="fa-solid fa-image text-5xl block mb-2 opacity-40"></i>
                            <p class="text-sm font-light">Belum ada gambar terunggah.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- CARD 2: PDF DOCUMENT GALLERY -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-red-100 text-red-600 rounded-lg">
                        <i class="fa-solid fa-file-pdf text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">PDF Documents</h4>
                        <p class="text-xs text-gray-500">Koleksi dokumen berkas PDF & Cover Preview</p>
                    </div>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-red-50 text-red-600 rounded-full">
                    {{ $galleries->where('mime_type', 'pdf')->count() }} Documents
                </span>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @forelse($galleries->where('mime_type', 'pdf') as $gallery)
                        <div class="cursor-pointer group relative"
                            x-show="!search || '{{ strtolower($gallery->name) }}'.includes(search.toLowerCase())"
                            @click="$wire.editGallery({{ $gallery->id }}); showModal = true;">

                            <div
                                class="bg-white rounded-xl shadow-xs hover:shadow-md overflow-hidden transition-all duration-200 h-full flex flex-col border border-gray-100 group-hover:border-red-200">
                                <div
                                    class="aspect-square bg-gray-100 overflow-hidden relative flex items-center justify-center">
                                    @if ($gallery->cover_path)
                                        <img src="{{ asset('storage/' . $gallery->cover_path) }}"
                                            alt="{{ $gallery->name }}"
                                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                    @else
                                        <div
                                            class="flex flex-col items-center justify-center text-red-400 p-2 text-center">
                                            <i class="fa-solid fa-file-pdf text-4xl mb-1"></i>
                                            <span class="text-[10px] text-gray-400">No Cover Preview</span>
                                        </div>
                                    @endif

                                    <!-- Tag Badge PDF -->
                                    <div
                                        class="absolute top-2 left-2 px-1.5 py-0.5 bg-red-600/90 backdrop-blur-xs text-white font-bold text-[9px] rounded shadow-xs flex items-center gap-1">
                                        <i class="fa-solid fa-file-pdf"></i> PDF
                                    </div>

                                    <!-- Quick Actions Overlay -->
                                    <div
                                        class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1.5 p-2">
                                        <a href="{{ asset('storage/' . $gallery->image_path) }}" target="_blank"
                                            class="p-2 bg-white/90 hover:bg-white text-gray-800 rounded-lg text-xs font-medium shadow-sm transition-transform hover:scale-105"
                                            title="Buka PDF" @click.stop>
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                        <button type="button" wire:click.stop="regenerateCover({{ $gallery->id }})"
                                            class="p-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-medium shadow-sm transition-transform hover:scale-105"
                                            title="Re-generate Cover PDF">
                                            <i class="fa-solid fa-rotate-right" wire:loading.remove
                                                wire:target="regenerateCover({{ $gallery->id }})"></i>
                                            <i class="fa-solid fa-repeat animate-spin" wire:loading
                                                wire:target="regenerateCover({{ $gallery->id }})"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-3 bg-white flex-1 flex flex-col justify-between border-t border-gray-50">
                                    <p class="text-xs font-bold text-gray-800 truncate" title="{{ $gallery->name }}">
                                        {{ $gallery->name }}
                                    </p>
                                    <div class="flex items-center justify-between mt-2 text-[10px]">
                                        <span
                                            class="px-2 py-0.5 rounded-md font-bold {{ $gallery->status === 'Publish' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $gallery->status }}
                                        </span>
                                        <span
                                            class="text-gray-400 font-medium truncate max-w-[70px]">{{ $gallery->file_size ?? 'PDF' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12 text-gray-400">
                            <i class="fa-solid fa-file-pdf text-5xl block mb-2 opacity-40"></i>
                            <p class="text-sm font-light">Belum ada dokumen PDF terunggah.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    <!-- WIDE RESPONSIVE MODAL (UPLOAD & EDIT) -->
    <div class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 transition-opacity"
        x-show="showModal" x-transition style="display: none;">

        <!-- Modal Container Layout Melebar (Max Width 4XL) -->
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[92vh] flex flex-col overflow-hidden"
            @click.away="showModal = false">

            <!-- Modal Header -->
            <div class="bg-gray-900 text-white px-6 py-4 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2">
                    <i class="bi bi-cloud-arrow-up text-xl text-blue-400" x-show="!$wire.isEdit"></i>
                    <i class="fa-solid fa-pen-to-square text-xl text-amber-400" x-show="$wire.isEdit"></i>
                    <h5 class="text-base font-bold" x-text="$wire.isEdit ? 'Edit Media Details' : 'Upload New Media'">
                    </h5>
                </div>
                <button type="button"
                    class="text-gray-400 hover:text-white p-1 rounded-lg transition-colors cursor-pointer"
                    @click="showModal = false">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Body (Grid 2 Kolom di Desktop, Tumpuk di Mobile) -->
            <div class="p-6 overflow-y-auto flex-1">
                <form wire:submit.prevent="{{ $isEdit ? 'updateGallery' : 'saveGallery' }}">

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">

                        <!-- KANVAS KIRI (5 Kolom): File Input / Dropzone & Preview Dynamic -->
                        <div class="md:col-span-5 space-y-4">
                            <label class="block text-sm font-bold text-gray-800">Media File & Preview</label>

                            <!-- Dropzone Box -->
                            <div
                                class="relative border-2 border-dashed border-gray-300 hover:border-blue-500 rounded-2xl p-4 transition-all text-center bg-gray-50 hover:bg-blue-50/30 flex flex-col items-center justify-center min-h-[230px]">
                                <input type="file"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    wire:model="fileimage_path" accept=".jpg,.png,.jpeg,.webp,.gif,.pdf">

                                <!-- STATE PREVIEW REALTIME -->
                                @if ($fileimage_path)
                                    @php
                                        $ext = strtolower($fileimage_path->getClientOriginalExtension());
                                    @endphp

                                    @if (in_array($ext, ['jpg', 'png', 'jpeg', 'webp', 'gif']))
                                        <!-- Temporary Preview Image Baru -->
                                        <div class="relative z-0 flex flex-col items-center">
                                            <img src="{{ $fileimage_path->temporaryUrl() }}"
                                                class="max-h-44 rounded-lg object-contain shadow-sm mb-2">
                                            <span
                                                class="text-[11px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">New
                                                Image Selected</span>
                                        </div>
                                    @elseif($ext === 'pdf')
                                        <!-- Temporary Preview PDF Baru -->
                                        <div class="relative z-0 flex flex-col items-center text-red-600 p-3">
                                            <i class="fa-solid fa-file-pdf-fill text-5xl mb-2"></i>
                                            <p class="text-xs font-bold text-gray-800 max-w-[200px] truncate">
                                                {{ $fileimage_path->getClientOriginalName() }}</p>
                                            <span class="text-[10px] text-gray-500 mt-1">PDF File Ready (Cover will
                                                auto-generate)</span>
                                        </div>
                                    @endif
                                @elseif($isEdit)
                                    <!-- Preview File Eksisting dari Database -->
                                    <div class="relative z-0 flex flex-col items-center w-full">
                                        @if ($mime_type === 'pdf')
                                            @if ($cover_path)
                                                <img src="{{ asset('storage/' . $cover_path) }}"
                                                    class="max-h-40 rounded-lg object-contain shadow-xs mb-2">
                                            @else
                                                <div class="p-4 text-center">
                                                    <i class="fa-solid fa-file-pdf text-5xl text-red-500 mb-1"></i>
                                                    <p class="text-xs text-gray-500">Cover preview unavailable</p>
                                                </div>
                                            @endif
                                            <span
                                                class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-bold">PDF
                                                Document</span>
                                        @else
                                            <img src="{{ asset('storage/' . ($cover_path ?? $existing_file_path)) }}"
                                                class="max-h-44 rounded-lg object-contain shadow-xs mb-2">
                                            <span
                                                class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-bold">Current
                                                Image</span>
                                        @endif
                                    </div>
                                @else
                                    <!-- Default Upload Placeholder -->
                                    <div class="flex flex-col items-center justify-center py-4">
                                        <div
                                            class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-2">
                                            <i class="bi bi-cloud-arrow-up text-2xl"></i>
                                        </div>
                                        <p class="text-xs font-bold text-gray-700 mb-1">Click or Drop File Here</p>
                                        <p class="text-[10px] text-gray-400">Image (JPG, PNG, WEBP) or PDF (Max 30MB)
                                        </p>
                                    </div>
                                @endif

                                <!-- STATE PROGRESS & UPLOADING INDIKATOR -->
                                <div wire:loading wire:target="fileimage_path"
                                    class="absolute inset-0 bg-white/95 z-20 rounded-2xl flex flex-col items-center justify-center p-4">
                                    <div
                                        class="w-10 h-10 border-3 border-blue-600 border-t-transparent rounded-full animate-spin mb-3">
                                    </div>
                                    <p class="text-xs font-bold text-gray-800">Uploading File...</p>
                                    <p class="text-[10px] text-gray-500 mt-1">Please wait until processing completes
                                    </p>
                                </div>
                            </div>

                            @error('fileimage_path')
                                <span class="text-red-500 text-xs font-medium block">{{ $message }}</span>
                            @enderror

                            <!-- PANEL INFORMASI & AKSI KHUSUS EDIT PDF -->
                            @if ($isEdit && $mime_type === 'pdf')
                                <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-200 space-y-2.5">
                                    <div class="flex items-center justify-between text-xs text-gray-700">
                                        <span class="font-medium">PDF File Actions:</span>
                                        <a href="{{ asset('storage/' . $existing_file_path) }}" target="_blank"
                                            class="text-blue-600 hover:underline flex items-center gap-1 font-semibold">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Open PDF
                                        </a>
                                    </div>
                                    <button type="button" wire:click="regenerateCover" wire:loading.attr="disabled"
                                        class="w-full py-2 px-3 bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-800 rounded-lg text-xs font-semibold flex items-center justify-center gap-2 transition-colors cursor-pointer">
                                        <i class="fa-solid fa-rotate-right" wire:loading.remove
                                            wire:target="regenerateCover"></i>
                                        <i class="fa-solid fa-repeat animate-spin" wire:loading
                                            wire:target="regenerateCover"></i>
                                        <span>Re-generate Cover Image</span>
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- KANAN (7 Kolom): Form Inputs Metadatas -->
                        <div class="md:col-span-7 space-y-4">

                            <!-- Title Input -->
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Title
                                    <span class="text-red-500">*</span></label>
                                <input type="text"
                                    class="w-full px-3.5 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm transition-all"
                                    wire:model="filename" placeholder="Masukkan judul berkas">
                                @error('filename')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Alt Text & Category (2 Kolom Sejajar) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label
                                        class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Alt
                                        Text / Caption (SEO)</label>
                                    <input type="text"
                                        class="w-full px-3.5 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm transition-all"
                                        wire:model="filealt_text" placeholder="Alt deskripsi gambar">
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Category</label>
                                    <input type="text"
                                        class="w-full px-3.5 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm transition-all uppercase"
                                        wire:model="filecategory" placeholder="Contoh: SLIDER, PDF, BANNER">
                                </div>
                            </div>

                            <!-- Description Input -->
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Description</label>
                                <textarea
                                    class="w-full px-3.5 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm transition-all resize-none"
                                    wire:model="filedescription" rows="3" placeholder="Deskripsi ringkas file..."></textarea>
                            </div>

                            <div>
                             <label
                                    class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">PATH</label>

                                    <input type="text"
                                        class="w-full px-3.5 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm transition-all uppercase"
                                        wire:model="existing_file_path" readonly>


                            </div>

                            <!-- Status Selection -->
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Status</label>
                                <div class="flex gap-4 items-center">
                                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="radio" wire:model="filestatus" value="Publish"
                                            class="text-blue-600 focus:ring-blue-500">
                                        <span class="font-medium">Publish</span>
                                    </label>
                                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="radio" wire:model="filestatus" value="Draft"
                                            class="text-blue-600 focus:ring-blue-500">
                                        <span class="font-medium">Draft</span>
                                    </label>
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- Modal Footer Actions (Sticky at bottom) -->
                    <div
                        class="mt-8 pt-4 border-t border-gray-100 flex flex-col sm:flex-row gap-3 justify-end items-center">
                        <button type="button" @click="showModal = false"
                            class="w-full sm:w-auto px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors text-sm cursor-pointer">
                            Cancel
                        </button>

                        <template x-if="$wire.isEdit">
                            <button type="button"
                                @click="if(confirm('Apakah Anda yakin ingin menghapus media ini?')) { $wire.deleteGallery($wire.selected_id); showModal = false; }"
                                class="w-full sm:w-auto px-5 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 font-medium rounded-xl transition-colors text-sm cursor-pointer">
                                Delete Media
                            </button>
                        </template>

                        <!-- Button Submit -->
                        <button type="submit" wire:loading.attr="disabled"
                            wire:target="fileimage_path, saveGallery, updateGallery, regenerateCover"
                            class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-300 text-white font-semibold rounded-xl transition-colors text-sm flex items-center justify-center gap-2 cursor-pointer">
                            <span wire:loading.remove
                                wire:target="fileimage_path, saveGallery, updateGallery, regenerateCover">
                                <i class="fa-solid fa-circle-check"></i> Save Media
                            </span>
                            <span wire:loading
                                wire:target="fileimage_path, saveGallery, updateGallery, regenerateCover"
                                class="flex items-center gap-2">
                                <i class="fa-solid fa-repeat animate-spin"></i> Processing...
                            </span>
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

</div>
