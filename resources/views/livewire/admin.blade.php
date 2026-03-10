<div x-data="{ open: false, langOpen: false, backupOpen: false, genOpen: false }">
    @push('styles')
    <style>
        .info-list li {
            @apply py-2 border-b border-dashed border-gray-300 flex justify-between items-center;
        }

        .info-list li:last-child {
            @apply border-b-0;
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

        .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.08); }

        /* Data list hover */
        .data-list .:hover {
            @apply bg-gray-50;
        }
    </style>
    @endpush

    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <img src="{{$setting->logo ?? '/frontend/default/www.png'}}" alt="Site Logo" class="w-12 h-12 rounded-full shadow-sm object-cover">
            <div>
                <h1 class="text-2xl font-bold text-blue-600">{{ $setting->sitename ?? 'Admin Dashboard' }}</h1>
                <p class="text-xs text-gray-400 mt-1">Selamat datang — {{ now()->format('l, j F Y') }}</p>
            </div>
        </div>
        <a href="{{$setting->url}}" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-white border border-blue-200 text-blue-600 rounded-full hover:bg-blue-50 transition shadow-sm text-sm font-semibold">
            <span>{{$setting->url}}</span><i class="fas fa-external-link-alt text-sm"></i>
        </a>
    </div>

    <div class="mb-4"> <x-suryacms::session-status /> </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <!-- Main Content (Left) -->
        <div class="lg:col-span-2">
            <!-- Stat Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="stat-card bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1">Pages</p>
                            <h3 class="text-3xl font-bold text-gray-800">{{ $pages->count() }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file text-blue-600"></i>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400">Published: {{ $pages->where('status', 'Publish')->count() }}/{{ $pages->count() }}</p>
                </div>

                <div class="stat-card bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1">Posts</p>
                            <h3 class="text-3xl font-bold text-gray-800">{{ $posts->count() }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-alt text-purple-600"></i>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400">Published: {{ $posts->where('status', 'Publish')->count() }}/{{ $posts->count() }}</p>
                </div>

                <div class="stat-card bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1">Gallery</p>
                            <h3 class="text-3xl font-bold text-gray-800">{{ $gallery->count() }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-images text-amber-600"></i>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400">Published: {{ $gallery->where('status', 'Publish')->count() }}/{{ $gallery->count() }}</p>
                </div>

                <div class="stat-card bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1">Inbox</p>
                            <h3 class="text-3xl font-bold text-gray-800">{{ $contacts->count() }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-envelope text-emerald-600"></i>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400">Unread: {{ $contacts->where('is_read', false)->count() }}/{{ $contacts->count() }}</p>
                </div>
            </div>

            <!-- Maintenance Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-tools text-blue-600"></i> Site Maintenance
                    </h3>
                    <label class="flex items-center cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" wire:model.live="site_maintenance" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </div>
                        <span class="ml-3 text-sm font-medium text-gray-700">@if ($site_maintenance) <span class="px-3 py-1 text-xs font-bold bg-yellow-100 text-yellow-800 rounded-full">Active</span> @else <span class="px-3 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full">Inactive</span> @endif</span>
                    </label>
                </div>
                <p class="text-sm text-gray-600 mb-3">Kontrol ketersediaan website Anda kepada pengunjung.</p>
                @if ($site_maintenance)
                    <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Website Anda sedang dalam mode maintenance. Pengunjung akan melihat halaman maintenance.
                    </div>
                @else
                    <div class="p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">
                        <i class="fas fa-check-circle mr-2"></i>Website Anda sedang aktif. Pengunjung dapat mengakses website dengan normal.
                    </div>
                @endif
            </div>

            <!-- Categories Section -->
            <div class="mb-6">
                <!-- Categories Card -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <button @click="open = !open" class="w-full px-5 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white flex justify-between items-center hover:shadow-lg transition">
                        <h3 class="text-lg font-bold flex items-center gap-2">
                            <i class="fas fa-tags"></i> Categories
                        </h3>
                        <i class="fas fa-chevron-down transition-transform" :class="open && 'rotate-180'"></i>
                    </button>
                    <div x-show="open" class="p-5">
                        <div class="grid md:grid-cols-2 gap-8">
                            <div>
                                <form wire:submit.prevent="{{ $isEditMode ? 'updateCategory' : 'saveCategory' }}">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">{{ $isEditMode ? 'Edit Category' : 'New Category' }}</label>
                                    <div class="flex gap-2">
                                        <input type="text" wire:model="categoryName" placeholder="Category Name" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition"><i class="fas fa-save mr-1"></i> {{ $isEditMode ? 'Update' : 'Save' }}</button>
                                    </div>
                                    @error('categoryName') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                                </form>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Category List</label>
                                <ul class="space-y-2 data-list">
                                    @foreach ($categories as $category)
                                        <li class=" flex justify-between items-center px-3 py-2 rounded-lg border border-transparent hover:border-gray-200 transition">
                                            <div class="flex items-center gap-3">
                                                <span class="font-semibold text-gray-800">{{ $category->name }}</span>
                                                @if ($category->status == 'Publish')
                                                    <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">Published</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">Draft</span>
                                                @endif
                                            </div>
                                            <div class="flex gap-2">
                                                <button wire:click="editCategory({{ $category->id }})" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition"><i class="fas fa-edit"></i></button>
                                                <button wire:click="changeStatus({{ $category->id }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"><i class="fas fa-toggle-off"></i></button>
                                                <button onclick="confirm('Hapus kategori ini?') || event.stopImmediatePropagation()" wire:click="deleteCategory({{ $category->id }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Languages Section -->
            <div class="mb-6">
                <!-- Languages Card -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <button @click="langOpen = !langOpen" class="w-full px-5 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white flex justify-between items-center hover:shadow-lg transition">
                        <h3 class="text-lg font-bold flex items-center gap-2">
                            <i class="fas fa-language"></i> Languages
                        </h3>
                        <i class="fas fa-chevron-down transition-transform" :class="langOpen && 'rotate-180'"></i>
                    </button>
                    <div x-show="langOpen" class="p-5">
                        <div class="grid md:grid-cols-2 gap-8">
                            <div>
                                <form wire:submit.prevent="{{ $isLanguageEditMode ? 'updateLanguage' : 'saveLanguage' }}">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">{{ $isLanguageEditMode ? 'Edit Language' : 'Add Language' }}</label>
                                    <div class="space-y-2 mb-2">
                                        <input type="text" wire:model="languageName" placeholder="Language Name (e.g. English)" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <input type="text" wire:model="languageCode" placeholder="Language Code (e.g. en)" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition"><i class="fas fa-save mr-1"></i> {{ $isLanguageEditMode ? 'Update' : 'Save' }}</button>
                                    @error('languageName') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                    @error('languageCode') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                </form>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Language List</label>
                                <ul class="space-y-2 data-list">
                                    @foreach ($languages as $language)
                                        <li class=" flex justify-between items-center px-3 py-2 rounded-lg border border-transparent hover:border-gray-200 transition">
                                            <span class="font-semibold text-gray-800">{{ $language->name }} <span class="text-gray-500">/ {{ $language->code }}</span></span>
                                            <div class="flex gap-2">
                                                <button wire:click="editLanguage({{ $language->id }})" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition"><i class="fas fa-edit"></i></button>
                                                <button onclick="confirm('Hapus bahasa ini?') || event.stopImmediatePropagation()" wire:click="deleteLanguage({{ $language->id }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO Preview Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <label class="block text-sm font-bold text-gray-800 mb-3">
                        <i class="fas fa-google text-blue-500 mr-2"></i> Google Search Result Preview
                    </label>
                    <div class="border border-gray-200 p-4 rounded-lg bg-gray-50">
                        <h4 class="text-blue-600 font-semibold mb-1">{{ $setting->sitename }} - {{ $setting->tagline }}</h4>
                        <p class="text-green-600 text-xs mb-1">{{ $setting->url }}</p>
                        <p class="text-gray-600 text-sm">{{ Str::limit($setting->description, 160) }}</p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-3">
                        <i class="fas fa-share-alt text-blue-500 mr-2"></i> Social Media Share Preview
                    </label>
                    <div class="border border-gray-200 p-4 rounded-lg bg-gray-50 flex gap-4">
                        @if (isset($setting) && $setting->images)
                            <img src="{{ asset($setting->images) }}" alt="Preview" class="w-24 h-24 rounded object-cover">
                        @else
                            <div class="w-24 h-24 bg-white rounded border border-gray-200 flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        @endif
                        <div class="flex-1">
                            <h5 class="font-semibold text-gray-800 mb-1">{{ $setting->sitename }} - {{ $setting->tagline }}</h5>
                            <p class="text-gray-600 text-xs mb-1">{{ Str::limit($setting->description, 100) }}</p>
                            <p class="text-green-600 text-xs">{{ $setting->url }}</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Download Backups -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mt-6">
                    <button @click="backupOpen = !backupOpen" class="w-full px-5 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white flex justify-between items-center hover:shadow-lg transition">
                        <h3 class="font-bold flex items-center gap-2">
                            <i class="fas fa-download"></i> Download Backups
                        </h3>
                        <i class="fas fa-chevron-down transition-transform" :class="backupOpen && 'rotate-180'"></i>
                    </button>
                    <div x-show="backupOpen" class="p-5 border-t border-gray-200">
                        <livewire:suryacms::admin.backup />
                    </div>
                </div>
        </div>

        <!-- Sidebar (Right) -->
        <div class="lg:col-span-1">
            <!-- System Information Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-5">
                    <h3 class="text-lg font-bold mb-1">System Information</h3>
                    <p class="text-blue-100 text-sm">{{ $setting->url }}</p>
                </div>
                <div class="p-5 space-y-6">
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 mb-3 pb-3 border-b border-gray-200">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i> Site Profile
                        </h4>
                        <ul class="info-list text-xs space-y-2">
                            <li>
                                <div class="flex justify-between items-center">
                                    <strong>Sitename:</strong>
                                    <span class="font-medium">{{ $setting->sitename }}</span>
                                </div>
                            </li>
                            <li>
                                <div class="flex justify-between items-center">
                                    <strong>URL:</strong>
                                    <a href="{{ $setting->url }}" target="_blank" class="text-blue-600 hover:underline">{{ $setting->url }}</a>
                                </div>
                            </li>
                            <li>
                                <div class="flex justify-between items-center">
                                    <strong>Email:</strong>
                                    <span class="font-medium">{{ $setting->email }}</span>
                                </div>
                            </li>
                            <li>
                                <div class="flex justify-between items-center">

                                    <strong>Language:</strong>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">{{ $setting->language }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-sm font-bold text-gray-800 mb-3 pb-3 border-b border-gray-200">
                            <i class="fas fa-cog text-blue-600 mr-2"></i> Configuration
                        </h4>
                        <div class="space-y-2">
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-xs text-gray-600 uppercase font-semibold mb-1">Active Theme</p>
                                <p class="text-sm font-bold text-gray-800">{{ $setting->active_theme }}</p>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-xs text-gray-600 uppercase font-semibold mb-1">Multi Language</p>
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">{{ $setting->is_multilingual }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 border-t border-gray-200 text-center py-3 text-xs text-gray-600">
                    Supported by Kreasi Tek Media
                </div>
            </div>

            <!-- Quick Generate -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
                <button @click="genOpen = !genOpen" class="w-full px-5 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white flex justify-between items-center hover:shadow-lg transition">
                    <h3 class="font-bold flex items-center gap-2">
                        <i class="fas fa-bolt"></i> Quick Generate
                    </h3>
                    <i class="fas fa-chevron-down transition-transform" :class="genOpen && 'rotate-180'"></i>
                </button>
                <div x-show="genOpen" class="divide-y divide-gray-200">
                    <div class="p-4 flex justify-between items-start hover:bg-gray-50">
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">Generate Offline Page</p>
                            <p class="text-xs text-gray-500 mt-1">Create static HTML files for offline access</p>
                        </div>
                        <button wire:click="generateOffline" wire:loading.attr="disabled" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded font-semibold hover:bg-blue-700 disabled:opacity-50">
                            <span wire:loading.remove><i class="fas fa-cloud-download-alt"></i></span>
                            <span wire:loading><i class="fas fa-spinner animate-spin"></i></span>
                        </button>
                    </div>
                    <div class="p-4 flex justify-between items-start hover:bg-gray-50">
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">Generate Sitemap.xml</p>
                            <p class="text-xs text-gray-500 mt-1">Generate an XML sitemap for search engines</p>
                        </div>
                        <button wire:click="generateSitemap" wire:loading.attr="disabled" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded font-semibold hover:bg-blue-700 disabled:opacity-50">
                            <span wire:loading.remove><i class="fas fa-file-code"></i></span>
                            <span wire:loading><i class="fas fa-spinner animate-spin"></i></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- File System Check -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
        <h3 class="font-bold flex items-center gap-2">
            <i class="fas fa-shield-check"></i> File System Check
        </h3>
    </div>
    <div class="p-5 space-y-4">
          @if ($statusMessage)
            <div class="p-3 rounded-xl border text-sm font-medium flex items-center gap-2 {{ str_contains(strtolower($statusMessage), 'success') ? 'bg-green-50 border-green-100 text-green-700' : (str_contains(strtolower($statusMessage), 'error') ? 'bg-red-50 border-red-100 text-red-700' : 'bg-blue-50 border-blue-100 text-blue-700') }}">
                <i class="fas {{ str_contains(strtolower($statusMessage), 'success') ? 'fa-check-circle' : 'fa-info-circle' }}"></i>
                {{ $statusMessage }}
            </div>
            @endif

            @if ($commandOutput)
            <div class="bg-slate-900 rounded-xl p-4 overflow-hidden">
                <h5 class="text-slate-400 text-[10px] uppercase tracking-widest font-bold mb-2">Output Details</h5>
                <div class="text-xs font-mono text-slate-200 overflow-y-auto max-h-40 sidebar-scroll leading-relaxed">
                    {!! $commandOutput !!}
                </div>
            </div>
            @endif

            @if (session()->has('message'))
            <div class="p-3 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl text-sm flex items-center gap-2">
                <i class="fas fa-check-circle"></i> {{ session('message') }}
            </div>
            @endif
        <livewire:suryacms::admin.file-check />
    </div>
</div>
        </div>
    </div>
</div>
