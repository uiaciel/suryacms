<div class="container-fluid py-4 bg-light min-vh-100">
    <div class="container">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">All Themes</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Admin</a></li>
                        <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Themes</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="#upload-section" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-plus-lg me-2"></i>New Theme
                </a>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
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
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative theme-card transition-all">

                        {{-- Status Ribbon --}}
                        @if ($theme['is_active'])
                            <div class="position-absolute top-0 end-0 m-3 z-index-1">
                                <span class="badge bg-success shadow-sm px-3 py-2 rounded-pill">
                                    <i class="bi bi-check-circle-fill me-1"></i> Active
                                </span>
                            </div>
                        @endif

                        {{-- Preview Image --}}
                        <div class="position-relative">
                            @if ($theme['preview'])
                                <img src="{{ asset($theme['preview']) }}" class="card-img-top"
                                     style="height:200px; object-fit:cover" alt="{{ $theme['name'] }}">
                            @else
                                <div class="bg-secondary bg-gradient d-flex flex-column align-items-center justify-content-center text-white"
                                     style="height:200px">
                                    <i class="bi bi-image fs-1 opacity-25"></i>
                                    <small class="opacity-50 mt-2">No Preview Available</small>
                                </div>
                            @endif
                        </div>

                        <div class="card-body d-flex flex-column p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="fw-bold mb-0 text-dark text-truncate">{{ $theme['name'] }}</h5>
                                <span class="text-muted small fw-bold text-uppercase">v{{ $theme['version'] }}</span>
                            </div>

                            <p class="text-muted small mb-3 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $theme['description'] }}
                            </p>

                            <div class="bg-light rounded-3 p-3 mb-4">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-person-circle me-2 text-primary"></i>
                                    <span class="small text-dark fw-semibold">{{ $theme['author'] }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-shield-check me-2 text-secondary"></i>
                                    <span class="small text-muted">{{ $theme['license'] }}</span>
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-auto">
                                @if ($theme['is_active'])
                                    <button class="btn btn-light rounded-pill fw-bold border" disabled>
                                        Currently Active
                                    </button>
                                @else
                                    <button wire:click="installTheme('{{ $theme['path'] }}')"
                                            class="btn btn-outline-primary rounded-pill fw-bold">
                                        Activate Theme
                                    </button>
                                @endif

                                @if ($theme['url'])
                                    <a href="{{ $theme['url'] }}" target="_blank" class="btn btn-link btn-sm text-decoration-none text-muted">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> View Website
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="upload-section" class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5">
                    <div class="row align-items-center">
                        <div class="col-lg-6 mb-4 mb-lg-0">
                            <h4 class="fw-bold mb-2">Upload & Install New Theme</h4>
                            <p class="text-muted mb-0">Have a custom theme? Upload your <strong>.zip</strong> package here to install it automatically to your dashboard.</p>
                        </div>
                        <div class="col-lg-6">
                            <form wire:submit.prevent="uploadAndInstall" class="bg-light p-4 rounded-4 border border-dashed text-center">
                                <div class="mb-3">
                                    <i class="bi bi-cloud-arrow-up fs-1 text-primary opacity-50"></i>
                                    <input type="file" wire:model="themeZip" class="form-control mt-3 rounded-pill">
                                    @error('themeZip') <div class="text-danger small mt-2 fw-bold">{{ $message }}</div> @enderror
                                </div>

                                <div wire:loading wire:target="themeZip" class="mb-3">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                    <span class="text-muted small">Uploading file, please wait...</span>
                                </div>

                                <button type="submit" wire:loading.attr="disabled" class="btn btn-primary px-5 rounded-pill shadow-sm">
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
    </div>

    @push('styles')
        <style>
    .theme-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .theme-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,0.1) !important;
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
@endpush

</div>
