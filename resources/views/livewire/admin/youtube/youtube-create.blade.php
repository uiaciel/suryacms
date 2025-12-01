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
          }" class="mb-4">

        {{-- VIDEO URL --}}
        <div class="mb-3">
            <label class="form-label">URL YouTube</label>
            <input x-model="video_url" x-on:input="$wire.set('video_url', video_url)" type="url" class="form-control" placeholder="https://www.youtube.com/watch?v=xxxx" required>
        </div>

        {{-- PREVIEW EMBED (Alpine) --}}
        <template x-if="videoId">
            <div class="mb-3">
                <label class="form-label">Preview</label>
                <div class="ratio ratio-16x9">
                    <iframe :src="'https://www.youtube.com/embed/' + videoId" allowfullscreen>
                    </iframe>
                </div>
            </div>
        </template>

        {{-- DATA DARI LIVEWIRE --}}
        @if ($thumbnail_url)
        <div class="mb-3 row g-3">
            <div class="col-md-4">
                <img src="{{ $thumbnail_url }}" class="img-fluid rounded border shadow-sm">
            </div>
            <div class="col-md-8">
                <input wire:model.defer="title" class="form-control mb-2" placeholder="Judul Video">
                <textarea wire:model.defer="description" class="form-control" rows="3" placeholder="Deskripsi (isi manual jika tidak tersedia)">
                    </textarea>
            </div>
        </div>
        @endif

        {{-- CATEGORY --}}
        <div class="mb-3">
            <label class="form-label">Kategori</label>
            <input wire:model.defer="category" type="text" class="form-control" placeholder="Profil, Product, Review">
        </div>

        {{-- STATUS --}}
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select wire:model.defer="status" class="form-select">
                <option value="draft">Draft</option>
                <option value="published">Publish</option>
            </select>
        </div>

        {{-- SUBMIT --}}
        <div class="mt-3">
            <button class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>

