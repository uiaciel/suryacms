<div class="container-fluid">
    <x-suryacms::import-export-offcanvas />

    <header class="page-header d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-2">Pages
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="/admin/pages">Pages</a></li>
                <li class="breadcrumb-item active">{{ $titlePage }}</li>
            </ol>
        </nav>
    </header>

    <x-suryacms::session-status />

    <div class="card border-0 shadow rounded">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">{{ $titlePage }}</h4>
            <a href="{{ route('admin.page.create') }}" class="btn btn-primary">New Page</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            @if ($setting->is_multilingual == 'Yes')
                            <th>Lang</th>
                            @endif
                            <th>PDF</th>
                            <th>Datepublish</th>
                            <th>Status</th>

                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($setting->is_multilingual == 'Yes')
                        @foreach ($pages as $page)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ $page->title }} <span><a href="/{{ $page->slug }}" target="_blank"><i class="ti ti-external-link"></i></a></span>
                            </td>
                            <td>
                                @if ($page->language_id == 1)
                                <img src="/assets/images/id.png" width="20">
                                @else
                                <img src="/assets/images/us.png" width="20">
                                @endif
                            </td>
                            <td>
                                <i class="bi bi-file-earmark-pdf text-secondary"></i>

                                @if (!empty($page->pdf))
                                <a href="/storage/{{ $page->pdf }}"><i class="bi bi-file-earmark-pdf text-primary"></i></a>
                                @endif
                            </td>
                            <td>{{ formatDate($page->datepublish) }}</td>

                            <td>{{ $page->status }}</td>
                            <td>
                                @if (Str::startsWith($page->title, 'Homepage'))
                                                                <a href="/admin/homepage-builder/{{ $page->slug }}" class="btn btn-primary btn-sm">Homepage Builder</a>

                                @else
                                <a href="{{ route('admin.page.edit', $page->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                <a href="/{{$page->slug}}" class="btn btn-success btn-sm">View</a>
                                                                <button onclick="confirm('Are you sure you want to delete this Page?') || event.stopImmediatePropagation()" wire:click="deletePage({{ $page->id }})" class="btn btn-danger btn-sm">Delete</button>

                                @endif

                            </td>
                        </tr>
                        @endforeach
                        @else
                        @foreach ($pages->where('language_id', 1) as $page)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $page->title }} @if (!is_null($page->html))
                                <span class="badge bg-primary">Homepage</span>
                                @endif
                            </td>
                            @if ($setting->is_multilingual == 'Yes')
                            <td>
                                @if ($page->language_id = 1)
                                <img src="/assets/img/id.png" width="20">@else<img src="/assets/img/us.png" width="20">
                                @endif
                            </td>
                            @endif
                            <td>
                                @if (!empty($page->pdf))
                                <a href="/storage/{{ $page->pdf }}"><i class="fas fa-file-pdf"></i></a>
                                @endif
                            </td>
                            <td>{{ $page->datepublish }}</td>
                            <td>{{ $page->status }}</td>
                            <td>
                                @if (Str::startsWith($page->title, 'Homepage'))

                                <a href="/admin/homepage-builder/{{ $page->slug }}" class="btn btn-primary btn-sm">Homepage Builder</a>

                                @else
                                                                <a href="{{ route('admin.page.edit', $page->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                                                <a href="/{{$page->slug}}" class="btn btn-success btn-sm">View</a>
                                                                <button onclick="confirm('Are you sure you want to delete this Page?') || event.stopImmediatePropagation()" wire:click="deletePage({{ $page->id }})" class="btn btn-danger btn-sm">Delete</button>

                                @endif

                            </td>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            const modalElement = document.getElementById('importExportModal');
            const bsModal = new bootstrap.Modal(modalElement, {
                keyboard: false // Opsional: mencegah penutupan dengan ESC
            });

            // Listener untuk event Livewire: tampilkan modal
            @this.on('show-ie-modal', () => {
                bsModal.show();
            });

            // Listener untuk event Livewire: sembunyikan modal
            @this.on('hide-ie-modal', () => {
                bsModal.hide();
            });

            // Penting: Memastikan properti Livewire diperbarui ketika modal ditutup
            // melalui interaksi Bootstrap (klik backdrop atau tombol 'x')
            modalElement.addEventListener('hidden.bs.modal', function() {
                @this.call('closeImportExportModal');
            });
        });

    </script>

    @endpush
</div>

