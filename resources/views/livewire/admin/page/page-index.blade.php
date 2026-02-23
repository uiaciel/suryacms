<div class="container-fluid">
    <x-suryacms::import-export-offcanvas />

    <header class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <h3 class="fw-bold mb-0 text-dark">Pages</h3>
            <a href="{{ route('admin.page.create') }}" class="btn btn-primary rounded-pill px-3 shadow-sm btn-sm btn-md-base">
                <i class="bi bi-plus-lg me-2"></i>Create New Page
            </a>
        </div>

        <nav aria-label="breadcrumb" class="d-none d-sm-block">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Admin</a></li>
                <li class="breadcrumb-item"><a href="/admin/pages" class="text-decoration-none">Pages</a></li>
                <li class="breadcrumb-item active">{{ $titlePage }}</li>
            </ol>
        </nav>
    </header>
    <x-suryacms::session-status />

    <div class="card border-0 shadow rounded">
        <div class="card-body" x-data="{
                search: '',
                phpFormat: '{{ get_date_format() ?? 'm/d/Y' }}',
                format_tanggal(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    if (isNaN(date)) return dateString;
                    const pad = (num) => String(num).padStart(2, '0');
                    const Y = date.getFullYear();
                    const m = pad(date.getMonth() + 1);
                    const d = pad(date.getDate());
                    const M = date.toLocaleDateString('id-ID', { month: 'short' });
                    switch (this.phpFormat) {
                        case 'd/m/Y': return `${d}/${m}/${Y}`;
                        case 'Y-m-d': return `${Y}-${m}-${d}`;
                        case 'm/d/Y': return `${m}/${d}/${Y}`;
                        case 'd-M-Y': return `${d}-${M}-${Y}`;
                        default: return date.toLocaleDateString('id-ID');
                    }
                },
                pages: {{ Js::from($pages) }},
                filteredPages() {
                    return this.pages.filter(page => {
                        return this.search === '' ||
                               page.title.toLowerCase().includes(this.search.toLowerCase()) ||
                               page.status.toLowerCase().includes(this.search.toLowerCase());
                    });
                }
            }">

            {{-- Search Section --}}
            <div class="mb-4">
                <div class="input-group" style="max-width: 400px;">
                    <span class="input-group-text border-end-0 bg-transparent"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0 ps-1" placeholder="Search pages..." x-model.debounce.300ms="search">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 50px;">#</th>
                            <th scope="col">Title</th>
                            @if ($setting->is_multilingual == 'Yes')
                            <th scope="col" class="text-center">Lang</th>
                            @endif
                            <th scope="col" class="text-center">PDF</th>
                            <th scope="col">Date Publish</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(page, index) in filteredPages()" :key="page.id">
                            <tr>
                                <td x-text="index + 1"></td>
                                <td>
                                    <div class="fw-bold text-dark" x-text="page.title"></div>
                                    <small class="text-muted" x-text="'/' + page.slug"></small>
                                </td>
                                @if ($setting->is_multilingual == 'Yes')
                                <td class="text-center">
                                    <template x-if="page.language_id == 1">
                                        <img src="/assets/images/id.png" width="20" alt="ID">
                                    </template>
                                    <template x-if="page.language_id == 2">
                                        <img src="/assets/images/us.png" width="20" alt="US">
                                    </template>
                                </td>
                                @endif
                                <td class="text-center">
                                    <template x-if="page.pdf">
                                        <a :href="'/storage/' + page.pdf" target="_blank" class="text-danger fs-5">
                                            <i class="bi bi-file-earmark-pdf-fill"></i>
                                        </a>
                                    </template>
                                    <template x-if="!page.pdf">
                                        <i class="bi bi-dash text-muted"></i>
                                    </template>
                                </td>
                                <td x-text="format_tanggal(page.datepublish)"></td>
                                <td>
                                    <span :class="page.status === 'Publish' ? 'badge bg-primary' : 'badge bg-secondary'" x-text="page.status"></span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group shadow-sm">
                                        <template x-if="page.title.startsWith('Homepage')">
                                            <a :href="'/admin/homepage-builder/' + page.slug" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-palette me-1"></i> Builder
                                            </a>
                                        </template>
                                        <template x-if="!page.title.startsWith('Homepage')">
                                            <div class="d-flex gap-1">
                                                <a :href="'/admin/pages/edit/' + page.id" class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a :href="'/' + page.slug" target="_blank" class="btn btn-sm btn-outline-success" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <button @click="if(confirm('Are you sure?')) $wire.deletePage(page.id)" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredPages().length === 0">
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No pages found.</td>
                            </tr>
                        </template>
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
