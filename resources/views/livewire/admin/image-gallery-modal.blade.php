<div x-data="{ showUpload: false }">
    @if ($isOpen)
        <div class="fixed inset-0 z-40 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
            <div
                class="relative w-full max-w-6xl bg-white rounded-2xl shadow-2xl flex flex-col max-h-[90vh] overflow-hidden">

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-4">
                        <h3 class="text-lg font-bold text-gray-900" id="imageGalleryModalLabel">
                            Media Gallery
                        </h3>
                        <button type="button" @click="showUpload = !showUpload"
                            class="px-4 py-1.5 text-sm font-medium bg-blue-50 text-blue-600 rounded-full hover:bg-blue-100 transition-all flex items-center gap-2 border border-blue-100">
                            <i class="fas text-xs" :class="showUpload ? 'fa-times' : 'fa-plus'"></i>
                            <span x-text="showUpload ? 'Close Upload' : 'Upload New'"></span>
                        </button>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors"
                        wire:click="close">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto bg-gray-50/30">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        {{-- Left Column: Gallery --}}
                        <div :class="showUpload ? 'lg:col-span-2' : 'lg:col-span-3'">
                            <div class="mb-6 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" wire:model.live="search" placeholder="Search media by name..."
                                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm">
                            </div>

                            @if (session()->has('message'))
                                <div
                                    class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 flex justify-between items-center rounded">
                                    <span>{{ session('message') }}</span>
                                    <button class="text-green-500 hover:text-green-700"
                                        onclick="this.parentElement.remove()">×</button>
                                </div>
                            @endif

                            <div class="grid grid-cols-2 md:grid-cols-3 gap-5"
                                :class="!showUpload ? 'lg:grid-cols-4' : ''">
                                @forelse ($galleries as $gallery)
                                    <div class="group relative bg-white rounded-xl overflow-hidden border border-gray-200 cursor-pointer transition-all hover:border-blue-400 hover:shadow-xl hover:-translate-y-1"
                                        wire:click="selectImage({{ $gallery->id }})">
                                        <div class="aspect-square w-full overflow-hidden bg-gray-100">
                                            @if (Str::endsWith(strtolower($gallery->image_path), '.pdf'))
                                                <div
                                                    class="w-full h-full flex flex-col items-center justify-center bg-red-50 text-red-400">
                                                    <i class="fas fa-file-pdf text-5xl mb-2"></i>
                                                    <span class="text-[10px] font-bold uppercase">PDF Document</span>
                                                </div>
                                            @else
                                                <img src="{{ Storage::url($gallery->image_path) }}"
                                                    alt="{{ $gallery->name }}"
                                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                            @endif
                                        </div>
                                        <div class="p-3 bg-white">
                                            <p class="text-xs font-medium text-gray-700 truncate">{{ $gallery->name }}
                                            </p>
                                        </div>
                                        <div
                                            class="absolute inset-0 bg-blue-600/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-2">
                                            <span class="text-white text-[10px] sm:text-xs text-center font-medium">
                                                <i class="fas fa-plus-circle mb-1 block text-2xl"></i>
                                                Click to Insert
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-full py-12 text-center">
                                        <p class="text-gray-500 italic">Tidak ada gambar di galeri.</p>
                                        <div class="mt-4 text-gray-400"><i class="far fa-folder-open text-5xl"></i>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Right Column: Upload Form (Collapsible) --}}
                        <div x-show="showUpload" x-transition
                            class="lg:col-span-1 bg-white p-6 rounded-2xl border border-gray-200 h-fit sticky top-0 shadow-sm">
                            <h4 class="font-bold text-gray-900 mb-5 flex items-center gap-2">
                                <i class="fas fa-cloud-upload-alt text-blue-500"></i> Upload Media
                            </h4>
                            @include('suryacms::livewire.admin.image.upload')
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-white text-right">
                    <button wire:click="close"
                        class="px-6 py-2 text-sm font-semibold text-gray-500 hover:text-gray-700 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
