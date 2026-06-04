<form wire:submit.prevent="uploadNewImage" class="space-y-4">
    <div>
        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Title</label>
        <input type="text" wire:model="uploadName"
            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none transition-all"
            placeholder="Media title..." required>
    </div>

    <div>
        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
            File <span class="text-gray-400 font-normal">(Image or PDF)</span>
        </label>
        <input type="file" wire:model="uploadImage"
            class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm"
            accept=".jpg,.png,.jpeg,.webp,.pdf" required>
        @error('uploadImage')
            <span class="text-red-500 text-xs">{{ $message }}</span>
        @enderror
        <input type="hidden" wire:model="status" value="Publish">
    </div>

    <div>
        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Category</label>
        <input type="text" wire:model="uploadCategory"
            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl uppercase text-xs"
            placeholder="POST, PDF...">
    </div>

    <button type="submit" wire:loading.attr="disabled"
        class="w-full px-6 py-3 bg-blue-600 text-white font-bold rounded-xl flex items-center justify-center gap-2 disabled:opacity-50">
        <span wire:loading.remove wire:target="uploadNewImage">
            <i class="fas fa-upload"></i> Upload & Save
        </span>
        <span wire:loading wire:target="uploadNewImage"><i class="fas fa-spinner fa-spin"></i> Processing...</span>
    </button>
</form>
