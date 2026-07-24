<form wire:submit.prevent="uploadNewImage" class="space-y-4 relative">

    <!-- Title -->
    <div>
        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Judul Media</label>
        <input type="text" wire:model="uploadName"
            class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
            placeholder="Masukkan judul media..." required>
        @error('uploadName')
            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
        @enderror
    </div>

    <!-- File Input Box -->
    <div>
        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
            Berkas <span class="text-gray-400 font-normal">(Gambar / PDF)</span>
        </label>

        <div class="relative">
            <input type="file" wire:model="uploadImage"
                class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none"
                accept=".jpg,.png,.jpeg,.webp,.pdf" required>

            <!-- Loading Spinner saat memilih file -->
            <div wire:loading wire:target="uploadImage"
                class="text-xs text-blue-600 font-semibold mt-1.5 flex items-center gap-1.5">
                <i class="bi bi-arrow-repeat animate-spin"></i> Membaca & memproses file...
            </div>
        </div>

        @error('uploadImage')
            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
        @enderror
    </div>

    <!-- Category -->
    <div>
        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Kategori</label>
        <input type="text" wire:model="uploadCategory"
            class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl uppercase text-xs outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
            placeholder="POST, SLIDER, PDF...">
        @error('uploadCategory')
            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
        @enderror
    </div>

    <!-- Submit Button -->
    <button type="submit" wire:loading.attr="disabled" wire:target="uploadImage, uploadNewImage"
        class="w-full px-5 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-300 text-white font-semibold rounded-xl text-xs flex items-center justify-center gap-2 transition-all shadow-xs">

        <span wire:loading.remove wire:target="uploadNewImage">
            <i class="bi bi-cloud-arrow-up-fill"></i> Upload & Simpan
        </span>
        <span wire:loading wire:target="uploadNewImage" class="flex items-center gap-1.5">
            <i class="bi bi-arrow-repeat animate-spin"></i> Menyimpan...
        </span>
    </button>
</form>
