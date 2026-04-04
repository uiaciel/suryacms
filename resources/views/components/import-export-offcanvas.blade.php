<div x-data="{ open: false }" x-cloak>
    <!-- Floating Button Bottom Right -->
    <button
        @click="open = true"
        class="fixed bottom-6 right-6 z-40 flex items-center justify-center w-14 h-14 bg-yellow-500 text-white rounded-full shadow-2xl hover:bg-yellow-600 transition-all transform hover:scale-110 focus:outline-none"
        type="button"
    >
        <i class="fa-solid fa-file-import text-2xl"></i>
    </button>

    <!-- Backdrop -->
    <div
        x-show="open"
        x-transition:opacity
        @click="open = false"
        class="fixed inset-0 bg-black bg-opacity-50 z-50"
    ></div>

    <!-- Offcanvas Panel (Tailwind) -->
    <div
        x-show="open"
        x-transition:enter="transition ease-in-out duration-300 transform"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in-out duration-300 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 w-80 md:w-96 bg-white shadow-2xl z-[60] overflow-y-auto"
    >
        <div class="flex items-center justify-between p-4 bg-blue-600 text-white">
            <h5 class="font-bold">Manage Data & Support</h5>
            <button @click="open = false" class="text-white hover:text-gray-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-6">
            <h6 class="text-blue-600 font-bold mb-4 uppercase text-xs tracking-wider">Import & Export</h6>

            <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden mb-6">
                <div class="p-4">
                    <form wire:submit.prevent="dataImport">
                        <h5 class="text-sm font-semibold mb-3">Import Data</h5>

                        <div class="flex flex-col gap-2">
                            <input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('importFile') border-red-500 @enderror" wire:model="importFile" accept=".xls,.xlsx,.csv">

                            <button class="w-full mt-2 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition disabled:opacity-50" type="submit" wire:loading.attr="disabled" wire:target="importFile">
                                <span wire:loading.remove wire:target="importFile, dataImport">Upload</span>
                                <span wire:loading wire:target="importFile"><i class="fas fa-spinner fa-spin mr-2"></i>Sedang mengecek file...</span>
                                <span wire:loading wire:target="dataImport"><i class="fas fa-spinner fa-spin mr-2"></i>Import sekarang...</span>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Format: xls, xlsx, csv</p>

                        @error('importFile')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror

                        <div x-data="{ isUploading: false, progress: 0 }" x-on:livewire-upload-start="isUploading = true" x-on:livewire-upload-finish="isUploading = false" x-on:livewire-upload-error="isUploading = false" x-on:livewire-upload-progress="progress = $event.detail.progress">
                            <div x-show="isUploading" class="w-full bg-gray-200 rounded-full h-1.5 mt-3">
                                <div class="bg-blue-600 h-1.5 rounded-full" :style="`width: ${progress}%`"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="p-4 bg-gray-100 border-t border-gray-200">
                    <button wire:click="dataExport" class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition flex items-center justify-center gap-2 disabled:opacity-50" wire:loading.attr="disabled" wire:target="dataExport">
                        <span wire:loading.remove wire:target="dataExport">
                            <i class="fas fa-download"></i> Export Data
                        </span>
                        <span wire:loading wire:target="dataExport">
                            <i class="fas fa-spinner fa-spin"></i> Preparing...
                        </span>
                    </button>
                </div>
            </div>

            <h6 class="text-blue-600 font-bold mb-4 uppercase text-xs tracking-wider">Help & Support</h6>
            <div class="flex flex-col gap-3">
                <a href="http://localhost:8001/admin/documentation" class="w-full text-center py-2 px-4 border border-cyan-500 text-cyan-600 rounded-lg hover:bg-cyan-50 transition font-medium">User Documentation</a>
                <a href="https://wa.me/6285693749533?text={{ urlencode('Halo, saya butuh dukungan untuk website ' . ($setting->url ?? '')) }}" target="_blank" class="w-full text-center py-2 px-4 border border-green-500 text-green-600 rounded-lg hover:bg-green-50 transition font-medium">Contact Support (Live Chat)</a>
            </div>
        </div>
    </div>
</div>
