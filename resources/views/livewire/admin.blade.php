<div>
    @push('styles')
    <style>
        .info-list li {
            padding: 8px 0;
            border-bottom: 1px dashed #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .info-list li:last-child {
            border-bottom: none;
        }

        .header-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.15;
            height: 80px;
            width: 80px;
        }

        .card-header[data-bs-toggle="collapse"][aria-expanded="false"] .bi-chevron-down {
            transform: rotate(0deg);
            transition: transform 0.3s ease-in-out;
        }

        /* Ikon saat terbuka (show) */
        .card-header[data-bs-toggle="collapse"][aria-expanded="true"] .bi-chevron-down {
            transform: rotate(180deg);
            transition: transform 0.3s ease-in-out;
        }

        .list-group-item .d-flex>div {
            margin-right: auto;
        }

        .maintenance-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #f0f4ff 0%, #ffffff 100%);
            z-index: 1;
            border-radius: calc(0.25rem * 4);
            /* Mencocokkan rounded-4 */
        }

        /* Style untuk list item Category/Language yang lebih rapi (menggantikan tabel) */
        .data-list .list-group-item:hover {
            background-color: #f8f9fa;
        }

    </style>
    @endpush
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <img src="{{$setting->logo}}" alt="Site Logo" class="me-3 rounded-circle shadow-sm" style="height:50px; width:50px; object-fit:cover;">
            <h1 class="h3 mb-0 text-primary fw-bold">{{ $setting->sitename ?? 'Admin Dashboard' }}</h1>
        </div>
        <a href="{{$setting->url}}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center">
            <span class="me-2">{{$setting->url}}</span><i class="bi bi-box-arrow-up-right fs-6"></i>
        </a>
    </div>

    <div class="mb-3"> <x-suryacms::session-status /> </div>

    <div class="row mb-3">
        <div class="col-lg-8">
            <div class="row g-3 mb-3">
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 shadow-sm border-0 rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>

                                    <h6 class="mb-2 f-w-400 text-muted"> Pages</h6>
                                    <h2 class="mb-3">{{ $pages->count() }}</h2>

                                </div>
                                <div><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-news">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M16 6h3a1 1 0 0 1 1 1v11a2 2 0 0 1 -4 0v-13a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1v12a3 3 0 0 0 3 3h11" />
                                        <path d="M8 8l4 0" />
                                        <path d="M8 12l4 0" />
                                        <path d="M8 16l4 0" />
                                    </svg>
                                </div>
                            </div>
                            <p class="mb-0 text-muted  fst-italic text-sm fs-6">Published : {{ $pages->where('status', 'Publish')->count() }} /
                                {{ $pages->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 shadow-sm border-0 rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>

                                    <h6 class="mb-2 f-w-400 text-muted"> Posts</h6>
                                    <h2 class="mb-3">{{ $posts->count() }}</h2>

                                </div>
                                <div><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-description">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        <path d="M9 17h6" />
                                        <path d="M9 13h6" />
                                    </svg>
                                </div>
                            </div>
                            <p class="mb-0 text-muted  fst-italic text-sm fs-6">Published : {{ $posts->where('status', 'Publish')->count() }} /
                                {{ $posts->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 shadow-sm border-0 rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>

                                    <h6 class="mb-2 f-w-400 text-muted"> Gallery</h6>
                                    <h2 class="mb-3">{{ $gallery->count() }}</h2>

                                </div>
                                <div><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-library-photo">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M7 3m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" />
                                        <path d="M4.012 7.26a2.005 2.005 0 0 0 -1.012 1.737v10c0 1.1 .9 2 2 2h10c.75 0 1.158 -.385 1.5 -1" />
                                        <path d="M17 7h.01" />
                                        <path d="M7 13l3.644 -3.644a1.21 1.21 0 0 1 1.712 0l3.644 3.644" />
                                        <path d="M15 12l1.644 -1.644a1.21 1.21 0 0 1 1.712 0l2.644 2.644" />
                                    </svg>
                                </div>
                            </div>
                            <p class="mb-0 text-muted  fst-italic text-sm fs-6">Published : {{ $gallery->where('status', 'Publish')->count() }} /
                                {{ $gallery->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 shadow-sm border-0 rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>

                                    <h6 class="mb-2 f-w-400 text-muted"> Inbox</h6>
                                    <h2 class="mb-3">{{ $contacts->count() }}</h2>

                                </div>
                                <div><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-mail">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M22 7.535v9.465a3 3 0 0 1 -2.824 2.995l-.176 .005h-14a3 3 0 0 1 -2.995 -2.824l-.005 -.176v-9.465l9.445 6.297l.116 .066a1 1 0 0 0 .878 0l.116 -.066l9.445 -6.297z" />
                                        <path d="M19 4c1.08 0 2.027 .57 2.555 1.427l-9.555 6.37l-9.555 -6.37a2.999 2.999 0 0 1 2.354 -1.42l.201 -.007h14z" />
                                    </svg>
                                </div>
                            </div>
                            <p class="mb-0 text-muted  fst-italic text-sm fs-6">Unread : {{ $contacts->where('is_read', false)->count() }} / {{ $contacts->count() }}</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card mb-3 maintenance-card shadow-lg border-0 rounded-4">
                <div class="card-body p-4 position-relative" style="z-index: 2;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title fw-bold mb-0 text-primary">
                            <i class="bi bi-tools me-2"></i>Site Maintenance
                        </h5>
                        <div class="form-check form-switch form-check-lg">
                            <input class="form-check-input" type="checkbox" id="siteMaintenanceToggle" wire:model.live="site_maintenance" style="transform: scale(1.5);">
                            <label class="form-check-label ms-2" for="siteMaintenanceToggle">
                                @if ($site_maintenance)
                                <span class="badge bg-warning  rounded-pill px-3 py-2 shadow-sm">Active</span>
                                @else
                                <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm">Inactive</span>
                                @endif
                            </label>
                        </div>
                    </div>
                    <p class="card-text text-muted mb-3">
                        Control the availability of your website to visitors.
                    </p>
                    @if ($site_maintenance)
                    <div class="alert alert-warning border-0 rounded-3 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Your site is currently in maintenance mode. Visitors will see the maintenance page.
                    </div>
                    @else
                    <div class="alert alert-success border-0 rounded-3 shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>Your site is currently active. Visitors can access the site as usual.
                    </div>
                    @endif
                </div>
            </div>

            <div class="row mb-3">

                <div class="col-md-6">
                    <div class="card w-100 shadow-lg">
                        <a class="text-decoration-none" data-bs-toggle="collapse" href="#collapseCategory" role="button" aria-expanded="true" aria-controls="collapseCategory">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center ">
                                <h5 class="card-title fs-5 fw-bold mb-0 text-white">
                                    <i class="bi bi-tags-fill me-2"></i>Categories
                                </h5>
                                <i class="bi bi-chevron-down fs-5"></i>
                            </div>
                        </a>

                        <div class="collapse" id="collapseCategory">
                            <div class="card-body p-4">
                                <!-- Form Input Kategori -->
                                <form wire:submit.prevent="{{ $isEditMode ? 'updateCategory' : 'saveCategory' }}" class="mb-2">
                                    <label for="categoryName" class="form-label small fw-semibold">{{ $isEditMode ? 'Edit Category' : 'New Category' }}</label>
                                    <div class="mb-3">
                                        <input type="text" id="categoryName" class="form-control" wire:model="categoryName" placeholder="Category Name" aria-label="categoryName">
                                    </div>
                                    <button type="submit" class="btn btn-primary"> <i class="bi bi-plus-lg"></i> {{ $isEditMode ? 'Update' : 'Save' }} </button>

                                </form>
                                @error('categoryName')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror

                                </form>

                                <!-- List Group (Menggantikan Tabel) -->
                                <ul class="list-group list-group-flush data-list small p-3" id="categoryList">
                                    <!-- Data kategori akan di-render oleh JS -->
                                    @foreach ($categories as $category)

                                    <li class="list-group-item border border-bottom d-flex justify-content-between align-items-center py-3">

                                        <div class="d-flex align-items-center">
                                            <span class="fw-bold  me-3">{{ $category->name }}</span>
                                            @if ($category->status == 'Publish')
                                            <span class="badge bg-primary fw-medium rounded-pill shadow-sm">Publish</span>

                                            @else
                                            <span class="badge bg-warning fw-medium rounded-pill shadow-sm">Draft</span>

                                            @endif

                                        </div>
                                        <div>

                                            <!-- Tombol ini hanya dummy, di Livewire/PHP akan memicu fungsi -->
                                            <button wire:click="editCategory({{ $category->id }})" class="btn btn-sm btn-warning rounded-pill px-2" title="Edit"><i class="bi bi-pencil"></i></button>
                                            <button wire:click="changeStatus({{ $category->id }})" class="btn btn-sm btn-primary"><i class="bi bi-toggle-off"></i></button>
                                            <button onclick="confirm('Are you sure you want to delete this category?') || event.stopImmediatePropagation()" wire:click="deleteCategory({{ $category->id }})" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>

                                        </div>

                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-md-6">

                    <div class="card w-100 shadow-lg">
                        <a class="text-decoration-none" data-bs-toggle="collapse" href="#collapseLanguage" role="button" aria-expanded="true" aria-controls="collapseLanguage">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center ">
                                <h5 class="card-title fs-5 fw-bold mb-0 text-white">
                                    <i class="bi bi-translate me-2"></i>Languages
                                </h5>
                                <i class="bi bi-chevron-down fs-5"></i>
                            </div>
                        </a>

                        <div class="collapse" id="collapseLanguage">

                            <div class="card-body p-4">
                                <!-- Form Input Kategori -->
                                <form wire:submit.prevent="{{ $isLanguageEditMode ? 'updateLanguage' : 'saveLanguage' }}" class="mb-2">

                                    <label for="languageName" class="form-label small fw-semibold">{{ $isLanguageEditMode ? 'Edit Language' : 'Add Language' }}</label>

                                    <div class="mb-3">
                                        <input type="text" id="languageName" class="form-control" wire:model="languageName" placeholder="Language Name : English" aria-label="languageName">

                                    </div>
                                    <div class="mb-3">
                                        <input type="text" id="languageCode" placeholder="Language Code : en" class="form-control" wire:model="languageCode">

                                    </div>
                                    <button type="submit" class="btn btn-primary"> <i class="bi bi-plus-lg"></i> {{ $isLanguageEditMode ? 'Update' : 'Save' }} </button>

                                </form>
                                @error('languageName')

                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                                @error('languageCode')

                                <span class="text-danger">{{ $message }}</span>
                                @enderror

                                </form>

                                <!-- List Group (Menggantikan Tabel) -->
                                <ul class="list-group list-group-flush data-list small p-3" id="languageList">
                                    <!-- Data kategori akan di-render oleh JS -->
                                    @foreach ($languages as $language)

                                    <li class="list-group-item border border-bottom d-flex justify-content-between align-items-center py-3">

                                        <div class="d-flex align-items-center">
                                            <span class="fw-bold  me-3">{{ $language->name }} / {{$language->code}} </span>

                                        </div>
                                        <div>

                                            <!-- Tombol ini hanya dummy, di Livewire/PHP akan memicu fungsi -->
                                            <button wire:click="editLanguage({{ $language->id }})" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></button>
                                            <button onclick="confirm('Are you sure you want to delete this language?') || event.stopImmediatePropagation()" wire:click="deleteLanguage({{ $language->id }})" class="btn btn-sm btn-danger"><i class="fas bi-trash"></i></button>

                                        </div>

                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card mb-3">

                <div class="card-body p-4">
                    <div class="mb-4 border-bottom pb-4">
                        <label class="form-label fs-6 fw-bold  mb-3">
                            <i class="bi bi-google me-2 text-primary"></i>Google Search Result Preview
                        </label>
                        <div class="border p-3 rounded-3 bg-light shadow-sm">
                            <h5 class="text-primary mb-1 fw-bold">{{ $setting->sitename }} - {{ $setting->tagline }}</h5>
                            <p class="text-success mb-1 small">{{ $setting->url }}</p>
                            <p class="text-secondary mb-0 small">{{ Str::limit($setting->description, 160) }}</p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fs-6 fw-bold  mb-3">
                            <i class="bi bi-share-fill me-2 text-primary"></i>Social Media Share Preview
                        </label>
                        <div class="border p-3 rounded-3 d-flex align-items-center bg-light shadow-sm">
                            @if (isset($setting) && $setting->images)
                            <img src="{{ asset($setting->images) }}" alt="Current Image" class="img-thumbnail me-3 rounded" width="100" style="object-fit: cover; height: 100px;">
                            @else
                            <div class="bg-white p-3 me-3 rounded d-flex align-items-center justify-content-center border" style="width: 100px; height: 100px;">
                                <i class="bi bi-image-fill text-muted fs-4"></i>
                            </div>
                            @endif
                            <div>
                                <h6 class="mb-1 fw-bold">{{ $setting->sitename }} - {{ $setting->tagline }}</h6>
                                <p class="text-muted mb-1 small">{{ Str::limit($setting->description, 100) }}</p>
                                <small class="text-success">{{ $setting->url }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-4">
            {{-- <livewire:visitor-stats /> --}}

            <div class="card  mb-3 w-100 shadow-lg overflow-hidden">

                <!-- Card Header -->
                <div class="card-header bg-primary text-white position-relative p-4 ">
                    <h5 class="card-title fs-4 fw-bold mb-1">System Information</h5>
                    <p class="card-subtitle text-white-50 small">{{$setting->url}}</p>

                    <!-- Ikon Laptop (diatur dengan opacity rendah) -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M3 19l18 0" />
                        <path d="M5 6m0 1a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1h-12a1 1 0 0 1 -1 -1z" />
                    </svg>
                </div>

                <!-- Card Body -->
                <div class="card-body p-4">

                    <!-- Bagian Profil Situs -->
                    <div class="mb-4">
                        <h6 class="fs-6 fw-semibold  mb-3 border-start border-4 ps-3">
                            <i class="bi bi-info-circle-fill me-2 text-primary"></i>Site Profile
                        </h6>
                        <ul class="list-unstyled info-list small">
                            <!-- Data Mockup -->
                            <li>
                                <strong class="">Sitename:</strong>
                                <span class="fw-medium">{{$setting->sitename}}</span>
                            </li>
                            <li>
                                <strong class="">URL</strong>
                                <a href="#" class="link-primary fw-medium text-decoration-none">{{$setting->url}}</a>
                            </li>
                            <li>
                                <strong class="">Email:</strong>
                                <span class="fw-medium">{{$setting->email}}</span>
                            </li>
                            <li>
                                <strong class="">Primary Language:</strong>
                                <span class="badge text-bg-info  fw-bold rounded-pill shadow-sm">
                                    {{$setting->language}}
                                </span>
                            </li>
                        </ul>
                    </div>

                    <!-- Bagian Konfigurasi Tambahan -->
                    <div>
                        <h6 class="fs-6 fw-semibold  mb-3 border-start border-4  ps-3">
                            <i class="bi bi-gear-fill me-2 text-primary"></i>Confituration
                        </h6>
                        <div class="d-flex flex-column flex-sm-row justify-content-between gap-3">

                            <!-- Theme Name -->
                            <div class="flex-fill bg-light p-3 rounded-3 border border-light-subtle">
                                <p class="small text-uppercase text-dark fw-medium mb-1">Theme Active</p>
                                <span class="text-sm fw-bold">{{ $setting->active_theme }}</span>

                            </div>

                            <!-- Multi Language -->
                            <div class="flex-fill bg-light p-3 rounded-3 border border-light-subtle">
                                <p class="small  text-uppercase text-dark fw-medium mb-1">Multi language</p>
                                <span class="badge text-bg-success fw-bold rounded-pill">
                                    {{ $setting->is_multilingual }}

                                </span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Card Footer (opsional) -->
                <div class="card-footer bg-light border-top text-muted text-center small p-3 rounded-bottom-4">
                    Supported by Kreasi Tek Media
                </div>

            </div>

            <div class="card w-100 shadow-lg overflow-hidden mb-3">
                <a class="text-decoration-none" data-bs-toggle="collapse" href="#collapseDownload" role="button" aria-expanded="true" aria-controls="collapseDownload">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title fs-5 fw-bold mb-0 text-white">
                            <i class="bi bi-download me-2"></i>Download Backups
                        </h5>
                        <i class="bi bi-chevron-down fs-5"></i>
                    </div>
                </a>

                <div class="collapse" id="collapseDownload">
                    <div class="card-body p-0">
                        <livewire:suryacms::admin.backup />

                    </div>
                </div>

            </div>

            <div class="card mb-3">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#collapseGenerate" aria-expanded="false" aria-controls="collapseGenerate" role="button">
                    <h5 class="card-title fs-5 fw-bold mb-0 text-white">
                        <i class="bi bi-lightning-charge me-2"></i>Quick Generate
                    </h5>
                    <i class="bi bi-chevron-down fs-5"></i>
                </div>
                <a class="text-decoration-none">

                </a>

                <div class="collapse" id="collapseGenerate">
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    Generate Offline Page
                                    <small class="text-muted d-block">Create static HTML files for offline access.</small>
                                </div>
                                <button wire:click="generateOffline" wire:loading.attr="disabled" class="btn btn-sm btn-primary">
                                    <span wire:loading.remove wire:target="generateOffline"><i class="bi bi-cloud-arrow-down"></i></span>
                                    <span wire:loading wire:target="generateOffline"><i class="spinner-border spinner-border-sm"></i></span>
                                </button>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    Generate Sitemap.xml
                                    <small class="text-muted d-block">Generate an XML sitemap for search engines.</small>
                                </div>
                                <button wire:click="generateSitemap" wire:loading.attr="disabled" class="btn btn-sm btn-primary">
                                    <span wire:loading.remove wire:target="generateSitemap"><i class="bi bi-file-earmark-code"></i></span>
                                    <span wire:loading wire:target="generateSitemap"><i class="spinner-border spinner-border-sm"></i></span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title fw-bold mb-0">File System Check</h5>
                </div>
                <div class="card-body">

                    <!-- Area untuk menampilkan status dan output command -->
                    <div class="mt-4 text-muted small">
                        <!-- Menampilkan pesan status terakhir -->
                        @if ($statusMessage) {{-- Asumsi $statusMessage berisi pesan sukses atau error --}}
                        <div class="alert alert-{{ str_contains(strtolower($statusMessage), 'success') ? 'success' : (str_contains(strtolower($statusMessage), 'error') ? 'danger' : 'info') }} alert-dismissible fade show" role="alert">
                            <p class="fw-semibold mb-0">{{ $statusMessage }}</p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <!-- Menampilkan output detail dari command jika ada -->
                        @if ($commandOutput)
                        <div class="alert alert-secondary alert-dismissible fade show mt-2" role="alert">
                            <h6 class="alert-heading">Command Output Details:</h6>
                            <div class="bg-light p-2 rounded font-monospace text-break" style="max-height: 200px; overflow-y: auto;">
                                {!! $commandOutput !!}
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>

                        @endif
                    </div>

                    @if (session()->has('message'))
                    <div class="alert alert-success mt-2">
                        {{ session('message') }}
                    </div>
                    @endif

                    <livewire:suryacms::admin.file-check />
                </div>
            </div>

        </div>

    </div>

</div>
