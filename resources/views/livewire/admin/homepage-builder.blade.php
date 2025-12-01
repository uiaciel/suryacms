<div style="background-color: #2c2c2c; height: 100vh; display: flex; flex-direction: column;">
    <div class="bg-dark p-2 text-white d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <a href="/admin" class="btn btn-primary btn-sm me-2" title="Kembali"><i class="fas fa-arrow-left"></i></a>
            <h5 class="mb-0">Homepage Editor</h5>
        </div>
        <div>
            <form wire:submit.prevent="savePage" class="d-flex align-items-center">
                <input type="text" wire:model.defer="title" class="form-control form-control-sm me-2"
                    placeholder="Judul Halaman">
                <a href="/" class="btn btn-info btn-sm me-2" title="Lihat Halaman"><i class="fas fa-eye"></i></a>
                <button type="submit" id="saveBtn" class="btn btn-success btn-sm" title="Simpan Perubahan"><i
                        class="fas fa-upload"></i> Save</button>

                {{-- Elemen input tersembunyi --}}
                <input type="text" id="htmldata" wire:model="html" hidden>
                <input type="text" id="cssdata" wire:model="css" hidden>
                <input type="text" wire:model="slug" placeholder="Slug" hidden>
                <select wire:model="status" hidden>
                    <option value="Publish">Publish</option>
                    <option value="Draft">Draft</option>
                </select>
            </form>
        </div>
    </div>

    @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-2"
        role="alert" style="z-index: 1050; width: auto;">
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div wire:ignore id="gjs-editor-container" style="flex-grow: 1;">
        <link href="https://unpkg.com/grapesjs/dist/css/grapes.min.css" rel="stylesheet">
        <script src="https://unpkg.com/grapesjs"></script>

        <div id="gjs" style="height: 100%; border: none;">
            {!! $this->renderPageHtml($html) !!}
        </div>
    </div>

    @push('scripts')
    @include('suryacms::livewire.admin.scripts')
    @endpush
</div>
