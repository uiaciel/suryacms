<div>
        {{-- <x-suryacms::import-export-offcanvas /> --}}

        <form wire:submit.prevent="updateSettingWeb" enctype="multipart/form-data"
            @if ($showSetupModal) style="display: none;" @endif>

            <!-- HEADER -->
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="rounded-2xl border border-gray-100 bg-white p-3 shadow-sm">
                        <i class="fas fa-cog text-lg text-blue-600"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-extrabold text-slate-800">Website Settings</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="mt-0.5 flex items-center gap-1.5 text-xs text-gray-400">
                                <li><a href="/admin" class="text-blue-500 hover:underline">Admin</a></li>
                                <li><i class="fas fa-chevron-right text-[9px]"></i></li>
                                <li class="font-medium text-gray-600">Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <button type="submit"
                    class="flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700">
                    <i class="fas fa-save text-xs"></i>
                    <span wire:loading.remove wire:target="updateSettingWeb">Save Changes</span>
                    <span wire:loading wire:target="updateSettingWeb">Saving...</span>
                </button>
            </div>

            <x-suryacms::session-status />

            <div class="mb-6 flex gap-1 overflow-x-auto rounded-2xl border border-gray-100 bg-white p-1.5 shadow-sm">

                <button type="button" wire:click="$set('activeTab', 'seo')" @class([
                    'flex items-center gap-2 whitespace-nowrap px-4 py-2 text-sm font-semibold rounded-xl transition',
                    'bg-blue-600 text-white shadow-sm' => $activeTab === 'seo',
                    'text-gray-500 hover:bg-gray-100 hover:text-gray-700' =>
                        $activeTab !== 'seo',
                ])>
                    <i class="fas fa-search text-xs"></i> SEO
                </button>

                <button type="button" wire:click="$set('activeTab', 'office')" @class([
                    'flex items-center gap-2 whitespace-nowrap px-4 py-2 text-sm font-semibold rounded-xl transition',
                    'bg-blue-600 text-white shadow-sm' => $activeTab === 'office',
                    'text-gray-500 hover:bg-gray-100 hover:text-gray-700' =>
                        $activeTab !== 'office',
                ])>
                    <i class="fas fa-envelope text-xs"></i> Contact
                </button>

                <button type="button" wire:click="$set('activeTab', 'setting')" @class([
                    'flex items-center gap-2 whitespace-nowrap px-4 py-2 text-sm font-semibold rounded-xl transition',
                    'bg-blue-600 text-white shadow-sm' => $activeTab === 'setting',
                    'text-gray-500 hover:bg-gray-100 hover:text-gray-700' =>
                        $activeTab !== 'setting',
                ])>
                    <i class="fas fa-globe text-xs"></i> Website
                </button>

                <button type="button" wire:click="$set('activeTab', 'themes')" @class([
                    'flex items-center gap-2 whitespace-nowrap px-4 py-2 text-sm font-semibold rounded-xl transition',
                    'bg-blue-600 text-white shadow-sm' => $activeTab === 'themes',
                    'text-gray-500 hover:bg-gray-100 hover:text-gray-700' =>
                        $activeTab !== 'themes',
                ])>
                    <i class="fas fa-palette text-xs"></i> Themes
                </button>

            </div>

            @if ($activeTab === 'seo')
                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

                    {{-- Left: Form Fields --}}
                    <div class="space-y-5">

                        {{-- General SEO --}}
                        <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                            <div class="flex items-center gap-2 border-b border-gray-50 px-5 py-4">
                                <i class="fas fa-language text-sm text-blue-500"></i>
                                <h2 class="text-sm font-bold text-slate-800">General SEO</h2>
                            </div>
                            <div class="space-y-4 p-5">

                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Site
                                        Name</label>
                                    <input type="text"
                                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm placeholder-gray-300 transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        wire:model="sitename" placeholder="Nama website...">
                                    @error('sitename')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Tagline</label>
                                    <input type="text"
                                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm placeholder-gray-300 transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        wire:model="tagline" placeholder="Tagline singkat...">
                                </div>

                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Description</label>
                                    <textarea
                                        class="w-full resize-none rounded-xl border border-gray-200 px-4 py-2.5 text-sm placeholder-gray-300 transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        rows="3" wire:model="description" placeholder="Deskripsi meta website..."></textarea>
                                </div>

                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Keywords</label>
                                    <input type="text"
                                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm placeholder-gray-300 transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        wire:model="keywords" placeholder="keyword1, keyword2, keyword3...">
                                    <p class="mt-1.5 text-[11px] text-gray-400">Pisahkan keywords dengan koma</p>
                                </div>

                                {{-- Multilingual Fields --}}
                                @if (isset($setting) && $setting->is_multilingual == 'Yes')
                                    <div class="border-t border-gray-100 pt-3">
                                        <div class="mb-3 flex items-center gap-2">
                                            <span
                                                class="flex h-5 w-5 items-center justify-center rounded-lg bg-blue-50 text-[10px] text-blue-500">
                                                <i class="fas fa-flag"></i>
                                            </span>
                                            <span
                                                class="text-xs font-bold uppercase tracking-wide text-gray-500">English
                                                Version</span>
                                        </div>
                                        <div class="space-y-4">
                                            <div>
                                                <label
                                                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Site
                                                    Name (EN)</label>
                                                <input type="text"
                                                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                    wire:model="sitename_translation">
                                            </div>
                                            <div>
                                                <label
                                                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Tagline
                                                    (EN)</label>
                                                <input type="text"
                                                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                    wire:model="tagline_translation">
                                            </div>
                                            <div>
                                                <label
                                                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Description
                                                    (EN)</label>
                                                <textarea
                                                    class="w-full resize-none rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                    rows="3" wire:model="description_translation"></textarea>
                                            </div>
                                            <div>
                                                <label
                                                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Keywords
                                                    (EN)</label>
                                                <input type="text"
                                                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                                    wire:model="keywords_translation">
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>

                        {{-- Brand Assets --}}
                        <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                            <div class="flex items-center gap-2 border-b border-gray-50 px-5 py-4">
                                <i class="fas fa-image text-sm text-amber-500"></i>
                                <h2 class="text-sm font-bold text-slate-800">Brand Assets</h2>
                            </div>
                            <div class="space-y-5 p-5">

                                {{-- Logo --}}
                                <div>
                                    <label
                                        class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">Logo</label>
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="flex h-20 w-24 flex-shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-200 bg-gray-50">
                                            @if ($logo instanceof \Livewire\TemporaryUploadedFile)
                                                <img src="{{ $logo->temporaryUrl() }}"
                                                    class="h-full w-full object-contain p-1">
                                            @elseif(isset($setting) && $setting->logo)
                                                <img src="{{ asset($setting->logo) }}"
                                                    class="h-full w-full object-contain p-1">
                                            @else
                                                <i class="fas fa-image text-2xl text-gray-300"></i>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <input type="file"
                                                class="w-full cursor-pointer rounded-xl border border-dashed border-gray-300 px-3 py-2 text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-blue-600 hover:file:bg-blue-100"
                                                wire:model="logo" accept="image/*">
                                            <p class="mt-1.5 text-[11px] text-gray-400">PNG, SVG, JPG. Rekomendasi:
                                                200×60px</p>
                                            <div wire:loading wire:target="logo"
                                                class="mt-2 flex items-center gap-1.5 text-xs text-blue-600">
                                                <i class="fas fa-spinner animate-spin"></i> Uploading...
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Favicon --}}
                                <div>
                                    <label
                                        class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">Favicon</label>
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="flex h-14 w-14 flex-shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-200 bg-gray-50">
                                            @if ($favicon instanceof \Livewire\TemporaryUploadedFile)
                                                <img src="{{ $favicon->temporaryUrl() }}"
                                                    class="h-full w-full object-contain p-1">
                                            @elseif(isset($setting) && $setting->favicon)
                                                <img src="{{ asset($setting->favicon) }}"
                                                    class="h-full w-full object-contain p-1">
                                            @else
                                                <i class="fas fa-star text-xl text-gray-300"></i>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <input type="file"
                                                class="w-full cursor-pointer rounded-xl border border-dashed border-gray-300 px-3 py-2 text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-blue-600 hover:file:bg-blue-100"
                                                wire:model="favicon" accept="image/*">
                                            <p class="mt-1.5 text-[11px] text-gray-400">ICO, PNG. Rekomendasi: 32×32px
                                            </p>
                                            <div wire:loading wire:target="favicon"
                                                class="mt-2 flex items-center gap-1.5 text-xs text-blue-600">
                                                <i class="fas fa-spinner animate-spin"></i> Uploading...
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Meta Image --}}
                                <div>
                                    <label
                                        class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">Meta
                                        Image (OG Image)</label>
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="flex h-16 w-24 flex-shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-200 bg-gray-50">
                                            @if ($images instanceof \Livewire\TemporaryUploadedFile)
                                                <img src="{{ $images->temporaryUrl() }}"
                                                    class="h-full w-full object-cover">
                                            @elseif(isset($setting) && $setting->images)
                                                <img src="{{ asset($setting->images) }}"
                                                    class="h-full w-full object-cover">
                                            @else
                                                <i class="fas fa-photo-video text-xl text-gray-300"></i>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <input type="file"
                                                class="w-full cursor-pointer rounded-xl border border-dashed border-gray-300 px-3 py-2 text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-blue-600 hover:file:bg-blue-100"
                                                wire:model="images" accept="image/*">
                                            <p class="mt-1.5 text-[11px] text-gray-400">JPG, PNG. Rekomendasi:
                                                1200×630px</p>
                                            <div wire:loading wire:target="images"
                                                class="mt-2 flex items-center gap-1.5 text-xs text-blue-600">
                                                <i class="fas fa-spinner animate-spin"></i> Uploading...
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    {{-- Right: SEO Preview --}}
                    <div class="space-y-5">

                        {{-- Google Preview --}}
                        <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                            <div class="flex items-center gap-2 border-b border-gray-50 px-5 py-4">
                                <i class="fab fa-google text-sm text-blue-500"></i>
                                <h2 class="text-sm font-bold text-slate-800">Google Search Preview</h2>
                            </div>
                            <div class="p-5">
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                    <p class="mb-1.5 text-xs text-gray-400">{{ $url ?? 'https://yourwebsite.com' }}</p>
                                    <h5 class="mb-1 text-base font-semibold leading-tight text-blue-700">
                                        {{ $sitename ?? 'Site Name' }} — {{ $tagline ?? 'Tagline' }}
                                    </h5>
                                    <p class="text-xs leading-relaxed text-gray-600">
                                        {{ Str::limit($description ?? 'Deskripsi website akan muncul di sini. Maksimal 160 karakter.', 160) }}
                                    </p>
                                </div>
                                <p class="mt-2 flex items-center gap-1 text-[11px] text-gray-400">
                                    <i class="fas fa-info-circle text-blue-400"></i>
                                    Preview berdasarkan data yang diisi di form
                                </p>
                            </div>
                        </div>

                        {{-- Social Share Preview --}}
                        <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                            <div class="flex items-center gap-2 border-b border-gray-50 px-5 py-4">
                                <i class="fas fa-share-alt text-sm text-purple-500"></i>
                                <h2 class="text-sm font-bold text-slate-800">Social Share Preview</h2>
                            </div>
                            <div class="p-5">
                                <div class="overflow-hidden rounded-xl border border-gray-200">
                                    @if ($images instanceof \Livewire\TemporaryUploadedFile)
                                        <img src="{{ $images->temporaryUrl() }}" class="h-36 w-full object-cover">
                                    @elseif(isset($setting) && $setting->images)
                                        <img src="{{ asset($setting->images) }}" class="h-36 w-full object-cover">
                                    @else
                                        <div
                                            class="flex h-36 w-full items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                            <i class="fas fa-image text-3xl text-gray-300"></i>
                                        </div>
                                    @endif
                                    <div class="border-t border-gray-200 bg-gray-50 p-3">
                                        <p class="mb-0.5 text-[10px] uppercase tracking-wider text-gray-400">
                                            {{ parse_url($url ?? '#', PHP_URL_HOST) }}
                                        </p>
                                        <h6 class="mb-0.5 text-sm font-bold leading-tight text-gray-800">
                                            {{ $sitename ?? 'Site Name' }} — {{ $tagline ?? 'Tagline' }}
                                        </h6>
                                        <p class="text-xs text-gray-500">{{ Str::limit($description ?? '-', 100) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            @endif

            @if ($activeTab === 'office')
                <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

                    {{-- Left: Office Info --}}
                    <div class="space-y-5 lg:col-span-2">
                        <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                            <div class="flex items-center gap-2 border-b border-gray-50 px-5 py-4">
                                <i class="fas fa-building text-sm text-blue-500"></i>
                                <h2 class="text-sm font-bold text-slate-800">Office Information</h2>
                            </div>
                            <div class="space-y-4 p-5">

                                <div wire:ignore>
                                    <label
                                        class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Address</label>
                                    <textarea id="address"
                                        class="w-full resize-none rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        wire:model="address" rows="4">{{ old('address', $address) }}</textarea>
                                </div>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label
                                            class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            <i class="fas fa-envelope mr-1 text-blue-400"></i> Email
                                        </label>
                                        <input type="email" id="email"
                                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            wire:model="email" placeholder="email@perusahaan.com">
                                        @error('email')
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label
                                            class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            <i class="fas fa-phone mr-1 text-blue-400"></i> Phone
                                        </label>
                                        <input type="text" id="phone"
                                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            wire:model="phone" placeholder="+62 21 000 0000">
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        <i class="fab fa-whatsapp mr-1 text-emerald-500"></i> WhatsApp
                                    </label>
                                    <input type="text" id="whatsapp"
                                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        wire:model="whatsapp" placeholder="+62 812 0000 0000">
                                </div>

                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="rounded-2xl border border-gray-100 bg-white shadow-sm" x-data="{ open: true }">
                            <button type="button" @click="open = !open"
                                class="flex w-full items-center justify-between rounded-t-2xl border-b border-gray-50 px-5 py-4 transition hover:bg-gray-50">
                                <span class="flex items-center gap-2 text-sm font-bold text-slate-800">
                                    <i class="fas fa-share-alt text-purple-500"></i> Social Media
                                </span>
                                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200"
                                    :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="space-y-4 p-5">
                                @foreach ([['id' => 'facebook', 'label' => 'Facebook', 'icon' => 'fab fa-facebook', 'color' => 'text-blue-600', 'wire' => 'facebook'], ['id' => 'twitter', 'label' => 'Twitter / X', 'icon' => 'fab fa-twitter', 'color' => 'text-sky-500', 'wire' => 'twitter'], ['id' => 'instagram', 'label' => 'Instagram', 'icon' => 'fab fa-instagram', 'color' => 'text-pink-500', 'wire' => 'instagram'], ['id' => 'linkedin', 'label' => 'LinkedIn', 'icon' => 'fab fa-linkedin', 'color' => 'text-blue-700', 'wire' => 'linkedin'], ['id' => 'youtube', 'label' => 'YouTube', 'icon' => 'fab fa-youtube', 'color' => 'text-red-500', 'wire' => 'youtube'], ['id' => 'tiktok', 'label' => 'TikTok', 'icon' => 'fab fa-tiktok', 'color' => 'text-gray-800', 'wire' => 'tiktok']] as $social)
                                    <div>
                                        <label
                                            class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500"
                                            for="{{ $social['id'] }}">
                                            <i class="{{ $social['icon'] }} {{ $social['color'] }} mr-1"></i>
                                            {{ $social['label'] }}
                                        </label>
                                        <input type="text" id="{{ $social['id'] }}"
                                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            wire:model="{{ $social['wire'] }}" placeholder="https://...">
                                        @error($social['wire'])
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            @endif

            @if ($activeTab === 'setting')
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    {{-- Left: General Settings --}}
                    <div class="space-y-5">
                        <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                            <div class="flex items-center gap-2 border-b border-gray-50 px-5 py-4">
                                <i class="fas fa-sliders-h text-sm text-blue-500"></i>
                                <h2 class="text-sm font-bold text-slate-800">General Settings</h2>
                            </div>
                            <div class="space-y-4 p-5">

                                @if (isset($setting) && $dateFormats)
                                    <div>
                                        <label for="date_format"
                                            class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Date
                                            Format</label>
                                        <select id="date_format"
                                            class="@error('date_format') border-red-400 ring-2 ring-red-200 @else border-gray-200 @enderror w-full rounded-xl border bg-white px-4 py-2.5 text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            wire:model.live="date_format">
                                            <option value="" disabled>Pilih format...</option>
                                            @foreach ($dateFormats as $formatKey => $formatDisplay)
                                                <option value="{{ $formatKey }}">
                                                    {{ Carbon\Carbon::now()->format($formatKey) }}
                                                    ({{ $formatDisplay }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('date_format')
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                        <p class="mt-1.5 text-xs text-gray-400">
                                            Saat ini: <strong
                                                class="text-gray-600">{{ Carbon\Carbon::now()->format($date_format) }}</strong>
                                        </p>
                                    </div>
                                @endif

                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500"
                                        for="url">
                                        Website URL
                                    </label>
                                    <input type="text" id="url"
                                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        wire:model="url" placeholder="https://yourwebsite.com">
                                    @error('url')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500"
                                        for="language">
                                        Primary Language
                                    </label>
                                    <select
                                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        id="language" wire:model="language">
                                        @foreach ($lang as $l)
                                            <option value="{{ $l->code }}">{{ $l->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Maintenance Mode --}}
                                <div>
                                    <label
                                        class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Maintenance Mode
                                    </label>
                                    <div class="mb-3 flex gap-6">
                                        <label class="flex cursor-pointer items-center gap-2">
                                            <input type="radio" name="siteMaintenanceOptions" value="1"
                                                wire:model.live="site_maintenance"
                                                class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="text-sm font-medium text-gray-700">Active</span>
                                        </label>
                                        <label class="flex cursor-pointer items-center gap-2">
                                            <input type="radio" name="siteMaintenanceOptions" value="0"
                                                wire:model.live="site_maintenance"
                                                class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="text-sm font-medium text-gray-700">Inactive</span>
                                        </label>
                                    </div>
                                    @if ($site_maintenance)
                                        <div
                                            class="flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
                                            <i class="fas fa-exclamation-triangle mt-0.5 text-amber-500"></i>
                                            <span>Situs sedang dalam <strong>maintenance mode</strong>. Pengunjung akan
                                                melihat halaman maintenance.</span>
                                        </div>
                                    @else
                                        <div
                                            class="flex items-start gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-800">
                                            <i class="fas fa-check-circle mt-0.5 text-emerald-500"></i>
                                            <span>Situs <strong>aktif</strong> dan dapat diakses pengunjung.</span>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Right: Integrations --}}
                    <div class="space-y-5">
                        <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                            <div class="flex items-center gap-2 border-b border-gray-50 px-5 py-4">
                                <i class="fas fa-plug text-sm text-emerald-500"></i>
                                <h2 class="text-sm font-bold text-slate-800">Integrations & Tracking</h2>
                            </div>
                            <div class="space-y-4 p-5">

                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500"
                                        for="googlesiteverification">
                                        <i class="fab fa-google mr-1 text-blue-500"></i> Google Site Verification
                                    </label>
                                    <input type="text" id="googlesiteverification"
                                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 font-mono text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        wire:model="googlesiteverification" placeholder="Verification code...">
                                </div>

                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500"
                                        for="google_analytics">
                                        <i class="fas fa-chart-line mr-1 text-orange-500"></i> Google Analytics ID
                                    </label>
                                    <input type="text" id="google_analytics"
                                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 font-mono text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        wire:model="google_analytics" placeholder="G-XXXXXXXXXX">
                                </div>

                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500"
                                        for="google_adsense">
                                        <i class="fas fa-dollar-sign mr-1 text-yellow-500"></i> Google AdSense
                                    </label>
                                    <input type="text" id="google_adsense"
                                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 font-mono text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        wire:model="google_adsense" placeholder="ca-pub-XXXXXXXXXXXXXXXXX">
                                </div>

                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500"
                                        for="code">
                                        <i class="fas fa-code mr-1 text-gray-500"></i> Custom Code (Head)
                                    </label>
                                    <textarea id="code"
                                        class="w-full resize-none rounded-xl border border-gray-200 px-4 py-2.5 font-mono text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        wire:model="code" rows="4" placeholder="<!-- custom scripts / meta tags -->"></textarea>
                                    <p class="mt-1.5 text-[11px] text-gray-400">Script akan disisipkan di bagian
                                        &lt;head&gt; halaman</p>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            @endif

            @if ($activeTab === 'themes')
                <div class="space-y-5">

                    {{-- Theme & Layout --}}
                    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                        <div class="flex items-center gap-2 border-b border-gray-50 px-5 py-4">
                            <i class="fas fa-palette text-sm text-purple-500"></i>
                            <h2 class="text-sm font-bold text-slate-800">Theme & Layout</h2>
                        </div>
                        <div class="p-5">
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                                {{-- Left: Theme Config --}}
                                <div class="space-y-4">

                                    <div>
                                        <label
                                            class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            Multi Languages
                                        </label>
                                        <select
                                            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            wire:model="is_multilingual">
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="active_theme"
                                            class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            Active Theme
                                        </label>
                                        <select wire:model.live="active_theme"
                                            class="@error('active_theme') border-red-400 @else border-gray-200 @enderror w-full rounded-xl border bg-white px-4 py-2.5 text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                            @foreach ($themes as $theme)
                                                <option value="{{ $theme }}">{{ ucfirst($theme) }}</option>
                                            @endforeach
                                        </select>
                                        @error('active_theme')
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label
                                            class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            Homepage Type
                                        </label>
                                        {{-- wire:model.live agar kondisi @if di bawahnya langsung reaktif --}}
                                        <select wire:model.live="homepage_type"
                                            class="mb-3 w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                            <option value="index">Default Theme Homepage</option>
                                            <option value="homepage">Custom Homepage</option>
                                        </select>

                                        @if ($homepage_type === 'homepage')
                                            <label
                                                class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                Select Homepage
                                            </label>
                                            <select wire:model="homepage_id"
                                                class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                                <option value="">-- Select Page --</option>
                                                @foreach ($homepagePages as $id => $title)
                                                    <option value="{{ $id }}">{{ $title }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>

                                </div>

                                {{-- Right: Theme Info --}}
                                <div>
                                    <div class="h-full rounded-xl border border-gray-100 bg-gray-50 p-4">
                                        <div class="mb-3 flex items-center justify-between">
                                            <h3 class="text-sm font-bold text-gray-700">Theme Info</h3>
                                            <button type="button"
                                                class="flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:border-blue-400 hover:text-blue-600"
                                                wire:click="compressAndDownloadTheme" wire:loading.attr="disabled">
                                                <span wire:loading.remove wire:target="compressAndDownloadTheme">
                                                    <i class="fas fa-download mr-1"></i> Download
                                                </span>
                                                <span wire:loading wire:target="compressAndDownloadTheme">
                                                    <i class="fas fa-spinner mr-1 animate-spin"></i> Processing...
                                                </span>
                                            </button>
                                        </div>

                                        <div class="space-y-2.5">
                                            @foreach ([['label' => 'Name', 'key' => 'name'], ['label' => 'Version', 'key' => 'version'], ['label' => 'Author', 'key' => 'author'], ['label' => 'License', 'key' => 'license'], ['label' => 'Description', 'key' => 'description']] as $row)
                                                <div class="flex items-start gap-3">
                                                    <span
                                                        class="w-20 flex-shrink-0 pt-0.5 text-xs text-gray-400">{{ $row['label'] }}</span>
                                                    <span class="text-xs font-semibold text-gray-700">
                                                        {{ $themeInfo['info'][$row['key']] ?? '-' }}
                                                    </span>
                                                </div>
                                            @endforeach
                                            @if (!empty($themeInfo['info']['url']))
                                                <div class="flex items-start gap-3">
                                                    <span
                                                        class="w-20 flex-shrink-0 pt-0.5 text-xs text-gray-400">Website</span>
                                                    <a href="{{ $themeInfo['info']['url'] }}" target="_blank"
                                                        class="truncate text-xs text-blue-500 hover:underline">
                                                        {{ $themeInfo['info']['url'] }}
                                                    </a>
                                                </div>
                                            @endif
                                        </div>

                                        @if (!empty($themeInfo['info']['theme_preview']))
                                            <div class="mt-4 border-t border-gray-200 pt-4">
                                                <p class="mb-2 text-xs font-semibold text-gray-500">Theme Preview</p>
                                                <img src="{{ asset($themeInfo['info']['theme_preview']) }}"
                                                    alt="Theme Preview"
                                                    class="w-full rounded-lg border border-gray-200 shadow-sm">
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Color Scheme --}}
                    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                        <div class="flex items-center gap-2 border-b border-gray-50 px-5 py-4">
                            <i class="fas fa-fill-drip text-sm text-pink-500"></i>
                            <h2 class="text-sm font-bold text-slate-800">Color Scheme</h2>
                        </div>
                        <div class="p-5">
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
                            <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                                @foreach ($colors as $field => $label)
                                    <div class="flex flex-col items-center gap-2">
                                        <label for="{{ $field }}"
                                            class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            {{ $label }}
                                        </label>
                                        <input type="color" id="{{ $field }}"
                                            class="h-14 w-14 cursor-pointer rounded-xl border-2 border-gray-200 bg-white p-1 transition hover:border-blue-400"
                                            wire:model="{{ $field }}">
                                        @error($field)
                                            <span class="text-[10px] text-red-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>

                            {{-- Live color preview using inline style from Livewire properties --}}
                            <div class="border-t border-gray-50 pt-4">
                                <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Preview
                                    Color Scheme</p>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" style="background-color: {{ $color_primary }}"
                                        class="rounded-xl px-4 py-2 text-xs font-semibold text-white">Primary</button>
                                    <button type="button" style="background-color: {{ $color_secondary }}"
                                        class="rounded-xl px-4 py-2 text-xs font-semibold text-white">Secondary</button>
                                    <button type="button" style="background-color: {{ $color_success }}"
                                        class="rounded-xl px-4 py-2 text-xs font-semibold text-white">Success</button>
                                    <button type="button" style="background-color: {{ $color_danger }}"
                                        class="rounded-xl px-4 py-2 text-xs font-semibold text-white">Danger</button>
                                    <button type="button" style="background-color: {{ $color_warning }}"
                                        class="rounded-xl px-4 py-2 text-xs font-semibold text-white">Warning</button>
                                    <button type="button" style="background-color: {{ $color_info }}"
                                        class="rounded-xl px-4 py-2 text-xs font-semibold text-white">Info</button>
                                    <button type="button" style="background-color: {{ $color_light }}"
                                        class="rounded-xl border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-700">Light</button>
                                    <button type="button" style="background-color: {{ $color_dark }}"
                                        class="rounded-xl px-4 py-2 text-xs font-semibold text-white">Dark</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @endif

        </form>

        @if ($showSetupModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
                <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">

                    <div class="flex items-center gap-3 bg-gradient-to-r from-[#1e2356] to-[#343a8d] px-6 py-5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/20">
                            <i class="fas fa-cog text-sm text-white"></i>
                        </div>
                        <div>
                            <h5 class="font-bold text-white">Initial Setup</h5>
                            <p class="text-xs text-blue-200">Selesaikan pengaturan awal website Anda</p>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="mb-5 flex items-start gap-3 rounded-xl border border-blue-200 bg-blue-50 p-4">
                            <i class="fas fa-info-circle mt-0.5 flex-shrink-0 text-blue-500"></i>
                            <p class="text-sm text-blue-700">
                                Selamat datang! Lengkapi informasi dasar berikut untuk menyelesaikan pengaturan awal
                                website Anda.
                            </p>
                        </div>

                        <form wire:submit.prevent="saveInitialSetting" class="space-y-4">

                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500"
                                    for="setup_sitename">
                                    Site Name <span class="font-normal normal-case text-red-500">*</span>
                                </label>
                                <input type="text" id="setup_sitename"
                                    class="@error('setup_sitename') border-red-400 bg-red-50 @else border-gray-200 @enderror w-full rounded-xl border px-4 py-2.5 text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    wire:model.live="setup_sitename" placeholder="e.g., My Website">
                                @error('setup_sitename')
                                    <p class="mt-1 flex items-center gap-1 text-xs text-red-500">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500"
                                    for="setup_tagline">
                                    Tagline <span
                                        class="text-[11px] font-normal normal-case text-gray-400">(Opsional)</span>
                                </label>
                                <input type="text" id="setup_tagline"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    wire:model.live="setup_tagline" placeholder="e.g., Your website tagline">
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500"
                                    for="setup_email">
                                    Email <span class="font-normal normal-case text-red-500">*</span>
                                </label>
                                <input type="email" id="setup_email"
                                    class="@error('setup_email') border-red-400 bg-red-50 @else border-gray-200 @enderror w-full rounded-xl border px-4 py-2.5 text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    wire:model.live="setup_email" placeholder="your@email.com">
                                @error('setup_email')
                                    <p class="mt-1 flex items-center gap-1 text-xs text-red-500">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500"
                                    for="setup_url">
                                    Website URL <span class="font-normal normal-case text-red-500">*</span>
                                </label>
                                <input type="url" id="setup_url"
                                    class="@error('setup_url') border-red-400 bg-red-50 @else border-gray-200 @enderror w-full rounded-xl border px-4 py-2.5 text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    wire:model.live="setup_url" placeholder="https://example.com">
                                @error('setup_url')
                                    <p class="mt-1 flex items-center gap-1 text-xs text-red-500">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500"
                                    for="setup_language">
                                    Default Language <span class="font-normal normal-case text-red-500">*</span>
                                </label>
                                <select id="setup_language"
                                    class="@error('setup_language') border-red-400 bg-red-50 @else border-gray-200 @enderror w-full rounded-xl border bg-white px-4 py-2.5 text-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    wire:model.live="setup_language">
                                    <option value="id">Bahasa Indonesia</option>
                                    <option value="en">English</option>
                                </select>
                                @error('setup_language')
                                    <p class="mt-1 flex items-center gap-1 text-xs text-red-500">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 py-3 font-bold text-white transition hover:bg-blue-700"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="saveInitialSetting">
                                        <i class="fas fa-check-circle mr-1"></i> Complete Setup
                                    </span>
                                    <span wire:loading wire:target="saveInitialSetting"
                                        class="flex items-center gap-2">
                                        <i class="fas fa-spinner animate-spin"></i> Processing...
                                    </span>
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        @endif

        @push('scripts')
        <script>
            function initTinyMCE() {
                if (typeof tinymce === 'undefined') return;
                tinymce.remove('#address');
                tinymce.init({
                    selector: '#address',
                    skin: 'bootstrap',
                    height: 300,
                    plugins: 'code link',
                    toolbar: 'code | styles | link bold italic underline forecolor',
                    menubar: false,
                    setup: function (editor) {
                        editor.on('init change', function () { editor.save(); });
                        editor.on('change', function () {
                            @this.set('address', editor.getContent());
                        });
                    },
                });
            }

            document.addEventListener('DOMContentLoaded', () => {
                initTinyMCE();

                // Re-init after Livewire updates (e.g. tab switching)
                Livewire.hook('morph.updated', ({ el, component }) => {
                    if (document.querySelector('#address') && !tinymce.get('address')) {
                        initTinyMCE();
                    }
                });
            });
        </script>
        @endpush

    </div>
