<div wire:listener="updateOrder" x-data x-init="Sortable.create($refs.sortableList, {
    animation: 150,
    handle: '.handle',
    onEnd: (e) => {
        let order = [...$refs.sortableList.children].map(item => item.dataset.id);
        Livewire.dispatch('updateOrder', { order });
    }
});">
    <div x-ref="sortableList">
        @forelse($sections as $section)
            <div class="my-4 border rounded p-3 bg-light" data-id="{{ $section->id }}">
                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">{{ $section->key }}</h5>
                    <div>
                        <button wire:click="deleteSection({{ $section->id }})" class="btn btn-sm btn-danger">
                            Delete
                        </button>
                        <button wire:click="editSection({{ $section->id }})" class="btn btn-sm btn-primary">
                            Edit
                        </button>
                        <span class="ms-2 text-muted handle" style="cursor: move;">
                            <i class="bi bi-arrows-move"></i>
                        </span>
                    </div>
                </div>

                {{-- Content --}}
                <div class="row {{ $section->content['settings']['spacing'] ?? '' }}"
                    data-aos="{{ $section->content['settings']['animation'] ?? '' }}">
                    @foreach ($section->content['columns'] ?? [] as $column)
                        <div class="col-{{ 12 / count($section->content['columns']) }} mb-3">
                            <div class="p-3 border rounded {{ $column['class'] ?? '' }}"
                                style="{{ $column['style'] ?? '' }}">
                                {!! $column['html'] ?? '' !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($editing)
                <div class="card mb-4">
                    <div class="card-header">Edit Section: {{ $editing->key }}</div>
                    <div class="card-body">
                        <form wire:submit.prevent="updateSection">
                            <div class="mb-3">
                                <label class="form-label">Key</label>
                                <input type="text" class="form-control" wire:model.defer="editing.key">
                            </div>

                            {{-- Contoh hanya edit HTML kolom pertama, bisa dikembangkan --}}
                            <div class="mb-3">
                                <label class="form-label">Kolom 1 HTML</label>
                                <textarea class="form-control" rows="5" wire:model.defer="editing.content.columns.column1.html"></textarea>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-success">Update</button>
                                <button type="button" wire:click="$set('editing', null)"
                                    class="btn btn-secondary">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

        @empty
            <p class="text-muted">Belum ada section yang dibuat.</p>
        @endforelse
    </div>
</div>
