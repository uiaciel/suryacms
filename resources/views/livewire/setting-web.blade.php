<div>
    <x-suryacms::import-export-offcanvas />

<div class="container-fluid">

    <form wire:submit.prevent="updateSettingWeb" enctype="multipart/form-data" @if($showSetupModal) style="display: none;" @endif>

        <div class="container-fluid py-4">

            <!-- HEADER -->
            <header class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                <div>
                    <h2 class="fw-bold text-dark mb-1 d-flex align-items-center">
                        <i class="bi bi-gear-fill me-2"></i> Website Settings
                    </h2>

                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb small mb-0">
                            <li class="breadcrumb-item"><a href="/admin" class="text-decoration-none">Admin</a></li>
                            <li class="breadcrumb-item active">Settings</li>
                        </ol>
                    </nav>
                </div>

                <button type="submit" class="btn btn-primary px-4 shadow-sm fw-bold">
                    <i class="bi bi-save me-1"></i> Save Changes
                </button>
            </header>

            <!-- MAIN CARD -->
            <div class="">
                <div class="">

                    <x-suryacms::session-status />

                    <!-- TABS -->
                    <ul class="nav nav-tabs modern-tabs mb-4" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#seo"><i class="bi bi-search me-1"></i> SEO</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#office"><i class="bi bi-envelope me-1"></i> Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#setting"><i class="bi bi-globe me-1"></i> Website</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#themes"><i class="bi bi-palette me-1"></i> Themes</a>
                        </li>
                    </ul>

                    <!-- TAB CONTENT -->
                    <div class="tab-content">

                        <div class="tab-pane fade show active" id="seo">

                            <div class="row g-4 ">
                                <div class="col-lg-6">
                                    <div class="card border shadow-sm bg-white rounded-4">
                                        <div class="card-body">

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Site Name</label>
                                                <input type="text" class="form-control" wire:model="sitename">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Tagline</label>
                                                <input type="text" class="form-control" wire:model="tagline">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Description</label>
                                                <textarea class="form-control" rows="3" wire:model="description"></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Keywords</label>
                                                <input type="text" class="form-control" wire:model="keywords">
                                            </div>

                                            <!-- Multilingual -->
                                            @if(isset($setting) && $setting->is_multilingual == 'Yes')
                                            <span class="text-secondary small d-block mb-2"><em>Language: English</em></span>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Site Name (EN)</label>
                                                <input type="text" class="form-control" wire:model="sitename_translation">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Tagline (EN)</label>
                                                <input type="text" class="form-control" wire:model="tagline_translation">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Description (EN)</label>
                                                <textarea class="form-control" rows="3" wire:model="description_translation"></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Keywords (EN)</label>
                                                <input type="text" class="form-control" wire:model="keywords_translation">
                                            </div>
                                            @endif

                                            <!-- Logo Upload -->
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Logo</label>

                                                <div class="d-flex gap-3 align-items-start">
                                                    <div>
                                                        @if ($logo instanceof \Livewire\TemporaryUploadedFile)
                                                        <img src="{{ $logo->temporaryUrl() }}" class="img-thumbnail rounded" width="120">
                                                        @elseif(isset($setting) && $setting->logo)
                                                        <img src="{{ asset($setting->logo) }}" class="img-thumbnail rounded" width="120">
                                                        @endif
                                                    </div>

                                                    <div class="flex-grow-1">
                                                        <input type="file" class="form-control" wire:model="logo">

                                                        <div wire:loading wire:target="logo" class="mt-2 text-primary small">
                                                            <div class="spinner-border spinner-border-sm me-1"></div> Uploading...
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Favicon -->
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Favicon</label>

                                                <div class="d-flex gap-3 align-items-start">
                                                    <div>
                                                        @if ($favicon instanceof \Livewire\TemporaryUploadedFile)
                                                        <img src="{{ $favicon->temporaryUrl() }}" class="img-thumbnail" width="50">
                                                        @elseif(isset($setting) && $setting->favicon)
                                                        <img src="{{ asset($setting->favicon) }}" class="img-thumbnail" width="50">
                                                        @endif
                                                    </div>

                                                    <div class="flex-grow-1">
                                                        <input type="file" class="form-control" wire:model="favicon">
                                                        <div wire:loading wire:target="favicon" class="mt-2 small text-primary">
                                                            <div class="spinner-border spinner-border-sm me-1"></div> Uploading...
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Meta Image -->
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Meta Image</label>

                                                <div class="d-flex gap-3 align-items-start">
                                                    <div>
                                                        @if ($images instanceof \Livewire\TemporaryUploadedFile)
                                                        <img src="{{ $images->temporaryUrl() }}" class="img-thumbnail" width="100">
                                                        @elseif(isset($setting) && $setting->images)
                                                        <img src="{{ asset($setting->images) }}" class="img-thumbnail" width="100">
                                                        @endif
                                                    </div>

                                                    <div class="flex-grow-1">
                                                        <input type="file" class="form-control" wire:model="images">
                                                        <div wire:loading wire:target="images" class="mt-2 small text-primary">
                                                            <div class="spinner-border spinner-border-sm me-1"></div> Uploading...
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <!-- SEO Preview -->
                                <div class="col-lg-6">
                                    <div class="card border shadow-sm bg-white rounded-4 mb-3 bg-light">
                                        <div class="card-body">
                                            <label class="form-label fw-semibold">SEO Preview</label>

                                            <div class="bg-white border rounded p-3">
                                                <h5 class="text-primary mb-1">{{ $sitename }} - {{ $tagline }}</h5>
                                                <p class="text-success small mb-2">{{ $url }}</p>
                                                <p class="text-muted small mb-0">{{ Str::limit($description, 160) }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card border-0 shadow-sm rounded-4">
                                        <div class="card-body">
                                            <label class="form-label fw-semibold">Social Share Preview</label>

                                            <div class="d-flex gap-3 border rounded p-3 bg-light">
                                                @if ($images instanceof \Livewire\TemporaryUploadedFile)
                                                <img src="{{ $images->temporaryUrl() }}" width="100" class="rounded">
                                                @elseif(isset($setting) && $setting->images)
                                                <img src="{{ asset($setting->images) }}" width="100" class="rounded">
                                                @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:100px;height:100px;">
                                                    No Image
                                                </div>
                                                @endif

                                                <div>
                                                    <h6 class="mb-1">{{ $sitename }} - {{ $tagline }}</h6>
                                                    <p class="text-muted small">{{ Str::limit($description, 100) }}</p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="tab-pane fade" id="setting" >
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border shadow-sm bg-white rounded-4">
                                        <div class="card-body p-4">
                                            @if(isset($setting) && $dateFormats)

                                            <div class="mb-3">
                                                <label for="date_format" class="form-label fw-bold">Select Date Format</label>

                                                <select id="date_format" class="form-select @error('date_format') is-invalid @enderror" wire:model.live="date_format">

                                                    <option value="" disabled>Please Select...</option>
                                                    @foreach ($dateFormats as $formatKey => $formatDisplay)
                                                    <option value="{{ $formatKey }}">

                                                        {{ Carbon\Carbon::now()->format($formatKey) }} ({{ $formatDisplay }})
                                                    </option>
                                                    @endforeach
                                                </select>

                                                @error('date_format')

                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror

                                                <div class="form-text mt-2">
                                                    Date Format Right Now
                                                    <strong>{{ Carbon\Carbon::now()->format($date_format) }}</strong>

                                                </div>
                                            </div>
                                            @endif

                                            <div class="mb-3">
                                                <label class="form-label fw-bold" for="url">URL</label>
                                                <input type="text" id="url" class="form-control" wire:model="url">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold" for="language">Primary
                                                    Language</label>
                                                <select class="form-select" id="language" wire:model="language" aria-label="Default select example">
                                                    @foreach ($lang as $lang )
                                                    <option value="{{ $lang->code }}">{{ $lang->name }}</option>
                                                    @endforeach
                                                </select>

                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Maintenance Mode</label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="siteMaintenanceOptions" id="siteMaintenanceOn" value="1" wire:model="site_maintenance">
                                                    <label class="form-check-label" for="siteMaintenanceOn">Active</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="siteMaintenanceOptions" id="siteMaintenanceOff" value="0" wire:model="site_maintenance">
                                                    <label class="form-check-label" for="siteMaintenanceOff">Inactive</label>
                                                </div>
                                                @if ($site_maintenance)
                                                <div class="alert alert-warning mt-2" role="alert">
                                                    Your site is currently in maintenance mode. Visitors will see the maintenance page.
                                                </div>
                                                @else
                                                <div class="alert alert-info mt-2" role="alert">
                                                    Your site is currently active. Visitors can access the site as usual.
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border shadow-sm bg-white rounded-4">
                                        <div class="card-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold" for="googlesiteverification">Google
                                                    Site
                                                    Verification</label>
                                                <input type="text" id="googlesiteverification" class="form-control" wire:model="googlesiteverification">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold" for="google_analytics">Google
                                                    Analytics</label>
                                                <input type="text" id="google_analytics" class="form-control" wire:model="google_analytics">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold" for="google_adsense">Google
                                                    AdSense</label>
                                                <input type="text" id="google_adsense" class="form-control" wire:model="google_adsense">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold" for="code">Code</label>
                                                <textarea id="code" class="form-control" wire:model="code"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="office" >
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="card border shadow-sm bg-white rounded-4 mb-3">

                                        <div class="card-body p-4">
                                            <div class="mb-3" wire:ignore>
                                                <label class="form-label fw-bold" for="address">Address</label>
                                                <textarea id="address" class="form-control" wire:model="address">{{ old('address', $address) }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold" for="email">Email</label>
                                                <input type="email" id="email" class="form-control" wire:model="email">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold" for="phone">Phone</label>
                                                <input type="text" id="phone" class="form-control" wire:model="phone">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold" for="whatsapp">WhatsApp</label>
                                                <input type="text" id="whatsapp" wire:model="whatsapp" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="accordion shadow-sm" id="socialMediaAccordion">
                                        <div class="accordion-item border-0 rounded-4 overflow-hidden">
                                            <h2 class="accordion-header" id="headingSocial">
                                                <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSocial" aria-expanded="true" aria-controls="collapseSocial">
                                                    <i class="bi bi-share me-2"></i> Social Media Links
                                                </button>
                                            </h2>
                                            <div id="collapseSocial" class="accordion-collapse collapse show" aria-labelledby="headingSocial" data-bs-parent="#socialMediaAccordion">
                                                <div class="accordion-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold" for="facebook">Facebook</label>
                                                        <input type="text" id="facebook" class="form-control" wire:model="facebook">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold" for="twitter">Twitter</label>
                                                        <input type="text" id="twitter" class="form-control" wire:model="twitter">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold" for="instagram">Instagram</label>
                                                        <input type="text" id="instagram" class="form-control" wire:model="instagram">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold" for="linkedin">LinkedIn</label>
                                                        <input type="text" id="linkedin" class="form-control" wire:model="linkedin">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold" for="youtube">YouTube</label>
                                                        <input type="text" id="youtube" class="form-control" wire:model="youtube">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold" for="tiktok">TikTok</label>
                                                        <input type="text" id="tiktok" class="form-control" wire:model="tiktok">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="tab-pane fade" id="themes" >
                            <div class="card border shadow-sm bg-white rounded-4 mb-4">
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold" for="timezone">Multi Languages</label>
                                        <select class="form-control" id="timezone" wire:model="is_multilingual">
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                    <div class="row g-3 align-items-end mb-3">
                                        {{-- Pilih Tema --}}
                                        <div class="col-12 col-md-6">
                                            <label for="theme" class="form-label fw-bold">Theme</label>
                                            <select wire:model="active_theme" class="form-control">
                                                @foreach ($themes as $theme)
                                                <option value="{{ $theme }}">{{ ucfirst($theme) }}</option>
                                                @endforeach
                                            </select>
                                            @error('active_theme') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>

                                    </div>

                                    <div class="mb-3">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body p-4">
                                                <div class="row mb-0">
                                                    <div class="col-lg-8">
                                                        <table class="table table-borderless table-sm">
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row" class="text-nowrap">Name</th>
                                                                    <td>{{ $themeInfo['info']['name'] ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row" class="text-nowrap">Version</th>
                                                                    <td>{{ $themeInfo['info']['version'] ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row" class="text-nowrap">Author</th>
                                                                    <td>{{ $themeInfo['info']['author'] ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row" class="text-nowrap">Website</th>
                                                                    <td>
                                                                        @if(!empty($themeInfo['info']['url']))
                                                                        <a href="{{ $themeInfo['info']['url'] }}" target="_blank">{{ $themeInfo['info']['url'] }}</a>
                                                                        @else
                                                                        -
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row" class="text-nowrap">License</th>
                                                                    <td>{{ $themeInfo['info']['license'] ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row" class="text-nowrap">Description</th>
                                                                    <td>{{ $themeInfo['info']['description'] ?? '-' }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                        <div class="mb-3">
                                                            <button type="button" class="btn btn-primary btn-sm" wire:click="compressAndDownloadTheme" wire:loading.attr="disabled">
                                                                <span wire:loading.remove wire:target="compressAndDownloadTheme">Download Theme</span>
                                                                <span wire:loading wire:target="compressAndDownloadTheme">Processing...</span>
                                                            </button>
                                                        </div>

                                                    </div>
                                                    <div class="col-lg-4">
                                                        @if(!empty($themeInfo['info']['theme_preview']))
                                                        <div class="mt-3">
                                                            <label class="form-label fw-bold">Theme Preview</label>
                                                            <img src="{{ asset($themeInfo['info']['theme_preview']) }}" alt="Theme Preview" class="img-fluid rounded shadow-sm border w-100">
                                                        </div>
                                                        @endif

                                                    </div>

                                                </div>

                                            </div>
                                        </div>

                                    </div>

                                    <div x-data="{ homepageType: @entangle('homepage_type') }" class="mb-3">
                                        <!-- Select Homepage Type -->
                                        <div class="mb-3">

                                            <label for="theme" class="form-label fw-bold">Homepage Type</label>
                                            <select x-model="homepageType" wire:model="homepage_type" class="form-control">
                                                <option value="index">Default Theme Homepage</option>
                                                <option value="homepage">Custom Homepage</option>
                                            </select>
                                        </div>

                                        <!-- Select Page (tampil jika type == 'homepage') -->
                                        <div x-show="homepageType === 'homepage'" class="mb-3">
                                            <label for="theme" class="form-label fw-bold">Select Homepage</label>
                                            <select wire:model="homepage_id" class="form-control">
                                                <option value="">-- Select Page --</option>
                                                @foreach ($homepagePages as $id => $title)
                                                <option value="{{ $id }}">{{ $title }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card border shadow-sm bg-white rounded-4">
                                <div class="card-header fw-bold bg-white border-bottom p-3">Color Scheme</div>
                                <div class="card-body p-4">

                                    <div class="row">
                                        @php
                                        $colors = [
                                        'color_primary' => 'Primary',
                                        'color_secondary' => 'Secondary',
                                        'color_success' => 'Success',
                                        'color_danger' => 'Danger',
                                        'color_warning' => 'Warning',
                                        'color_info' => 'Info',
                                        'color_light' => 'Light',
                                        'color_dark' => 'Dark',
                                        ];
                                        @endphp

                                        @foreach($colors as $field => $label)
                                        <div class="col-md-3 mb-3">
                                            <label for="{{ $field }}" class="form-label">{{ $label }} Color</label>
                                            <input type="color" id="{{ $field }}" class="form-control form-control-color border" wire:model="{{ $field }}">
                                            @error($field) <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        @endforeach
                                    </div>

                                    {{-- Preview Section --}}
                                    <div class="mt-4 mb-3">
                                        <h6>Preview Color Scheme</h6>
                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            <button type="button" class="btn btn-primary">Primary</button>
                                            <button type="button" class="btn btn-secondary">Secondary</button>
                                            <button type="button" class="btn btn-success">Success</button>
                                            <button type="button" class="btn btn-danger">Danger</button>
                                            <button type="button" class="btn btn-warning text-dark">Warning</button>
                                            <button type="button" class="btn btn-info text-dark">Info</button>
                                            <button type="button" class="btn btn-light">Light</button>
                                            <button type="button" class="btn btn-dark">Dark</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </form>

    <!-- Extra Modern Tab Style -->
    <style>
        .modern-tabs {
            border-bottom: 2px solid #f0f0f0;
        }
        .modern-tabs .nav-link {
            border: none;
            padding: 12px 24px;
            font-weight: 600;
            color: #6c757d;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }
        .modern-tabs .nav-link:hover {
            color: #0d6efd;
            background: #f8f9fa;
        }
        .modern-tabs .nav-link.active {
            background: transparent;
            color: #0d6efd;
            border-bottom: 3px solid #0d6efd;
        }
    </style>

    <!-- Modal Setup Setting (Shown when Setting::first() is empty) -->
    @if($showSetupModal)
    <div class="modal modal-lg d-block" style="display: block; background-color: rgba(0, 0, 0, 0.5);" tabindex="-1" role="dialog" aria-labelledby="setupSettingLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="setupSettingLabel">
                        <i class="bi bi-gear"></i> Initial Setup
                    </h5>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-4">Welcome! Please fill in the basic information to complete the initial setup of your website.</p>

                    <form wire:submit.prevent="saveInitialSetting">
                        <div class="mb-3">
                            <label class="form-label fw-bold" for="setup_sitename">Site Name <span class="text-danger">*</span></label>
                            <input type="text" id="setup_sitename" class="form-control @error('setup_sitename') is-invalid @enderror"
                                   wire:model.live="setup_sitename" placeholder="e.g., My Website">

                            @error('setup_sitename')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold" for="setup_tagline">Tagline <span class="text-muted">(Optional)</span></label>
                            <input type="text" id="setup_tagline" class="form-control"
                                   wire:model.live="setup_tagline" placeholder="e.g., Your website tagline">

                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold" for="setup_email">Email <span class="text-danger">*</span></label>
                            <input type="email" id="setup_email" class="form-control @error('setup_email') is-invalid @enderror"
                                   wire:model.live="setup_email" placeholder="your@email.com">

                            @error('setup_email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold" for="setup_url">Website URL <span class="text-danger">*</span></label>
                            <input type="url" id="setup_url" class="form-control @error('setup_url') is-invalid @enderror"
                                   wire:model.live="setup_url" placeholder="https://example.com">

                            @error('setup_url')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold" for="setup_language">Default Language <span class="text-danger">*</span></label>
                            <select id="setup_language" class="form-select @error('setup_language') is-invalid @enderror"
                                    wire:model.live="setup_language">

                                <option value="id">Bahasa Indonesia</option>
                                <option value="en">English</option>
                            </select>
                            @error('setup_language')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="bi bi-check-circle"></i> Complete Setup
                                </span>
                                <span wire:loading>
                                    <i class="spinner-border spinner-border-sm me-2"></i>Processing...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
@push('scripts')
<script>
    function initTinyMCE() {
        tinymce.remove(); // remove previous instances

        tinymce.init({
            selector: '#address'
            , skin: 'bootstrap'
            , height: 300
            , plugins: 'code link'
            , toolbar: 'code | styles | link bold italic underline forecolor'
            , menubar: false,

            setup: function(editor) {
                editor.on('init change', function() {
                    editor.save();
                });
                editor.on('change', function(e) {
                    @this.set('address', editor.getContent());
                });
            },

        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(function(tab) {
            tab.addEventListener('shown.bs.tab', function(e) {
                localStorage.setItem('activeTabSettingWeb', e.target.getAttribute('href'));
            });
        });

        // Aktifkan tab dari localStorage jika ada
        var activeTab = localStorage.getItem('activeTabSettingWeb');
        if (activeTab) {
            var tabTrigger = document.querySelector('a[data-bs-toggle="tab"][href="' + activeTab + '"]');
            if (tabTrigger) {
                var tab = new bootstrap.Tab(tabTrigger);
                tab.show();
            }
        }

        initTinyMCE();
    });

    document.addEventListener('livewire:initialized', () => {
        Livewire.hook('message.processed', (message, component) => {
            // Aktifkan tab dari localStorage setelah Livewire render
            var activeTab = localStorage.getItem('activeTabSettingWeb');
            if (activeTab) {
                var tabTrigger = document.querySelector('a[data-bs-toggle="tab"][href="' + activeTab + '"]');
                if (tabTrigger) {
                    var tab = new bootstrap.Tab(tabTrigger);
                    tab.show();
                }
            }
            initTinyMCE();
        });
    });

    // Pastikan value textarea diupdate sebelum submit
    document.addEventListener('submit', function(e) {
        if (tinymce.get('address')) {
            @this.set('address', tinymce.get('address').getContent());
        }
    }, true);

</script>
@endpush

</div>
