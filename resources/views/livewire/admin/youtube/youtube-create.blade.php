<div>
    <form wire:submit.prevent="save" x-data="{
        video_url: @entangle('video_url'),
        get videoId() {
            const url = this.video_url;
            if (!url) return '';
            const regex = /(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([^&?/]+)/;
            const m = url.match(regex);
            return m ? m[1] : '';
        }
    }">

        <!-- YouTube URL Input -->
        <div class="mb-4">
            <label class="block text-sm font-bold text-gray-900 mb-2">YouTube URL</label>
            <input
                x-model="video_url"
                x-on:input="$wire.set('video_url', video_url)"
                type="url"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                placeholder="https://www.youtube.com/watch?v=xxxx"
                required
            >
        </div>

        <!-- Preview Embed (Alpine) -->
        <template x-if="videoId">
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-900 mb-2">Preview</label>
                <div class="aspect-video bg-gray-900 rounded-lg overflow-hidden">
                    <iframe :src="'https://www.youtube.com/embed/' + videoId" class="w-full h-full" allowfullscreen></iframe>
                </div>
            </div>
        </template>

        <!-- Data from Livewire -->
        @if ($thumbnail_url)
        <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-1">
                <img src="{{ $thumbnail_url }}" class="w-full rounded-lg border border-gray-300 shadow-sm">
            </div>
            <div class="md:col-span-2 space-y-3">
                <input
                    wire:model.defer="title"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    placeholder="Video Title"
                >
                <textarea
                    wire:model.defer="description"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                    rows="3"
                    placeholder="Description (fill manually if not available)"
                ></textarea>
            </div>
        </div>
        @endif

        <!-- Category Input -->
        <div class="mb-4">
            <label class="block text-sm font-bold text-gray-900 mb-2">Category</label>
            <input
                wire:model.defer="category"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                placeholder="Profil, Product, Review"
            >
        </div>

        <!-- Status Select -->
        <div class="mb-4">
            <label class="block text-sm font-bold text-gray-900 mb-2">Status</label>
            <select
                wire:model.defer="status"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white"
            >
                <option value="draft">Draft</option>
                <option value="published">Publish</option>
            </select>
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit" class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                <i class="bi bi-save"></i> Save
            </button>
        </div>
    </form>
</div>


