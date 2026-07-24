<div x-data="{ showUpload: false }">
    @if ($isOpen)
        <div class="fixed inset-0 z-40 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="close"></div>

        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
            <div
                class="relative w-full max-w-6xl bg-white rounded-2xl shadow-2xl flex flex-col max-h-[90vh] overflow-hidden">

                <!-- Header Modal -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-white shrink-0">
                    <div class="flex items-center gap-4">
                        <h3 class="text-lg font-bold text-gray-900">
                            Media Library Selector
                        </h3>
                        <button type="button" @click="showUpload = !showUpload"
                            class="px-4 py-1.5 text-xs font-semibold bg-blue-50 text-blue-600 rounded-full hover:bg-blue-100 transition-all flex items-center gap-2 border border-blue-100">
                            <i class="bi" :class="showUpload ? 'bi-x-lg' : 'bi-plus-lg'"></i>
                            <span x-text="showUpload ? 'Close Upload' : 'Upload File Baru'"></span>
                        </button>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors"
                        wire:click="close">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>

                <!-- Body Modal -->
                <div class="p-6 overflow-y-auto bg-gray-50/50 flex-1">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                        <!-- Main Left Column: Media Grid -->
                        <div :class="showUpload ? 'lg:col-span-8' : 'lg:col-span-12'"
                            class="transition-all duration-300">

                            <!-- Search Field -->
                            <div class="mb-5 relative">
                                <span
                                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" wire:model.live="search"
                                    placeholder="Cari berkas media berdasarkan nama/kategori..."
                                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-sm shadow-xs">
                            </div>

                            <!-- Media Items Grid -->
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4"
                                :class="!showUpload ? 'lg:grid-cols-5' : 'lg:grid-cols-4'">
                                @forelse ($galleries as $gallery)
                                    <div class="group relative bg-white rounded-xl overflow-hidden border border-gray-200 cursor-pointer transition-all hover:border-blue-500 hover:shadow-lg"
                                        wire:click="selectImage({{ $gallery->id }})">

                                        <div class="aspect-square w-full overflow-hidden bg-gray-100 relative">
                                            @if ($gallery->mime_type === 'pdf' || Str::endsWith(strtolower($gallery->image_path), '.pdf'))
                                                <!-- Cover PDF atau Default Badge -->
                                                @if ($gallery->cover_path)
                                                    <img src="{{ Storage::url($gallery->cover_path) }}"
                                                        alt="{{ $gallery->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <div
                                                        class="w-full h-full flex flex-col items-center justify-center bg-red-50 text-red-500 p-2">
                                                        <i class="bi bi-file-earmark-pdf-fill text-4xl mb-1"></i>
                                                        <span class="text-[9px] font-bold uppercase">PDF Document</span>
                                                    </div>
                                                @endif

                                                <div
                                                    class="absolute top-1.5 left-1.5 px-1.5 py-0.5 bg-red-600 text-white font-bold text-[8px] rounded shadow-xs flex items-center gap-1">
                                                    <i class="bi bi-file-pdf"></i> PDF
                                                </div>
                                            @else
                                                <img src="{{ Storage::url($gallery->image_path) }}"
                                                    alt="{{ $gallery->name }}"
                                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                            @endif
                                        </div>

                                        <div class="p-2.5 bg-white border-t border-gray-100">
                                            <p class="text-xs font-semibold text-gray-800 truncate"
                                                title="{{ $gallery->name }}">{{ $gallery->name }}</p>
                                        </div>

                                        <!-- Hover Action Overlay -->
                                        <div
                                            class="absolute inset-0 bg-blue-600/85 backdrop-blur-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-2">
                                            <span
                                                class="text-white text-xs font-semibold text-center flex flex-col items-center gap-1">
                                                <i class="bi bi-plus-circle-fill text-2xl"></i>
                                                Pilih File Ini
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-full py-16 text-center text-gray-400">
                                        <i class="bi bi-folder2-open text-5xl mb-2 block opacity-40"></i>
                                        <p class="text-sm font-light">Tidak ada berkas media ditemukan.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Right Column: Form Upload -->
                        <div x-show="showUpload" x-transition
                            class="lg:col-span-4 bg-white p-5 rounded-2xl border border-gray-200 h-fit sticky top-0 shadow-sm">
                            <h4 class="font-bold text-gray-900 mb-4 text-sm flex items-center gap-2">
                                <i class="bi bi-cloud-arrow-up text-blue-600"></i> Upload Media
                            </h4>
                            @include('suryacms::livewire.admin.image.upload')
                        </div>

                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="px-6 py-3 border-t border-gray-100 bg-white text-right shrink-0">
                    <button wire:click="close" type="button"
                        class="px-5 py-2 text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
