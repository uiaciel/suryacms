<div class="container-fluid bg-light min-vh-100 py-4">
    <div class="container">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h2 class="fw-bold text-dark mb-1">All Themes</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Admin</a>
                        </li>
                        <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Themes</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-md-end mt-md-0 mt-3">
                <a href="#upload-section" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-plus-lg me-2"></i>New Theme
                </a>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 border-0 shadow-sm"
                role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4 mb-5">
            @foreach ($themes as $theme)
                <div class="col-xl-4 col-lg-4 col-md-6">
                    <div
                        class="card h-100 rounded-4 position-relative theme-card overflow-hidden border-0 shadow-sm transition-all">

                        {{-- Status Ribbon --}}
                        @if ($theme['is_active'])
                            <div class="position-absolute z-index-1 end-0 top-0 m-3">
                                <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm">
                                    <i class="bi bi-check-circle-fill me-1"></i> Active
                                </span>
                            </div>
                        @endif

                        {{-- Preview Image --}}
                        <div class="position-relative">
                            @if (!empty($theme['preview']) && file_exists(public_path($theme['preview'])))
                                <img src="{{ asset($theme['preview']) }}" class="card-img-top"
                                    style="height:200px; object-fit:cover" alt="{{ $theme['name'] }}">
                            @else
                                <img src="https://placehold.co/600x400?text=No+Preview" class="card-img-top"
                                    style="height:200px; object-fit:cover" alt="{{ $theme['name'] }}">
                            @endif
                        </div>

                        <div class="card-body d-flex flex-column p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="fw-bold text-dark text-truncate mb-0">{{ $theme['name'] }}</h5>
                                <span class="text-muted small fw-bold text-uppercase">v{{ $theme['version'] }}</span>
                            </div>

                            <p class="text-muted small flex-grow-1 mb-3"
                                style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $theme['description'] }}
                            </p>

                            <div class="row g-0 bg-light rounded-3 mb-4 p-2">
                                <div class="col-6 border-end">
                                    <div class="px-2">
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">AUTHOR</small>
                                        <span class="small text-dark fw-semibold text-truncate d-block">{{ $theme['author'] }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="px-2">
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">LICENSE</small>
                                        <span class="small text-dark fw-semibold text-truncate d-block">{{ $theme['license'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-auto">
                                @if ($theme['is_active'])
                                    <button class="btn btn-light w-100 rounded-pill fw-bold border mb-2" disabled>
                                        <i class="bi bi-check2-all me-1"></i> Active
                                    </button>
                                @else
                                    <button wire:click="installTheme('{{ $theme['path'] }}')"
                                        class="btn btn-primary w-100 rounded-pill fw-bold mb-2 shadow-sm">
                                        Activate Theme
                                    </button>
                                @endif

                                <div class="d-flex gap-2">
                                    @if ($theme['url'])
                                        <a href="{{ $theme['url'] }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill flex-grow-1">
                                            <i class="bi bi-eye me-1"></i> Demo
                                        </a>
                                    @endif
                                    <button class="btn btn-outline-danger btn-sm rounded-pill {{ $theme['url'] ? '' : 'w-100' }}"
                                        wire:click="confirmDeleteTheme('{{ $theme['path'] }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="upload-section" class="row">
            <div class="col-12">
                <div class="card bg-white rounded-4 p-lg-5 border-0 p-4 shadow-sm">
                    <div class="row align-items-center">
                        <div class="col-lg-6 mb-lg-0 mb-4">
                            <h4 class="fw-bold mb-2">Upload & Install New Theme</h4>
                            <p class="text-muted mb-0">Have a custom theme? Upload your <strong>.zip</strong> package
                                here to install it automatically to your dashboard.</p>
                        </div>
                        <div class="col-lg-6">
                            <form wire:submit.prevent="uploadAndInstall"
                                class="bg-light rounded-4 border border-dashed p-4 text-center">
                                <div class="mb-3">
                                    <i class="bi bi-cloud-arrow-up fs-1 text-primary opacity-50"></i>
                                    <input type="file" wire:model="themeZip" class="form-control rounded-pill mt-3">
                                    @error('themeZip')
                                        <div class="text-danger small fw-bold mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div wire:loading wire:target="themeZip" class="mb-3">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                    </div>
                                    <span class="text-muted small">Uploading file, please wait...</span>
                                </div>

                                <button type="submit" wire:loading.attr="disabled"
                                    class="btn btn-primary rounded-pill px-5 shadow-sm">
                                    <span wire:loading.remove wire:target="uploadAndInstall">
                                        <i class="bi bi-magic me-2"></i>Install Now
                                    </span>
                                    <span wire:loading wire:target="uploadAndInstall">
                                        Installing...
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteThemeModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Hapus Tema</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus tema <strong>{{ $themeToDelete }}</strong>?
                        Tindakan ini tidak bisa dibatalkan.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" wire:click="deleteConfirmed"
                            data-bs-dismiss="modal">Ya, Hapus</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('styles')
        <style>
            .theme-card {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .theme-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.1) !important;
            }

            .border-dashed {
                border-style: dashed !important;
                border-width: 2px !important;
                border-color: #dee2e6 !important;
            }

            .transition-all {
                transition: all 0.3s ease;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('alert', (event) => {
                    // Menggunakan SweetAlert jika ada, atau fallback ke native alert
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'info',
                            text: event.message,
                            confirmButtonColor: '#0d6efd'
                        });
                    } else {
                        alert(event.message);
                    }
                });
            });
        </script>

        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('show-delete-modal', () => {
                    var deleteModal = new bootstrap.Modal(document.getElementById('deleteThemeModal'));
                    deleteModal.show();
                });
            });
        </script>
    @endpush

</div>
