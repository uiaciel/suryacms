<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/30 py-8 px-4"
     x-data="{ showDeleteModal: false }"
     x-on:show-delete-modal.window="showDeleteModal = true">

    <div class="max-w-7xl mx-auto">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">All Themes</h2>
                <nav class="flex items-center gap-2 mt-1 text-sm">
                    <a href="#" class="text-slate-400 hover:text-slate-600 transition-colors">Admin</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-indigo-600 font-semibold">Themes</span>
                </nav>
            </div>
            <a href="#upload-section"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-md shadow-indigo-200 hover:shadow-indigo-300 hover:-translate-y-0.5">
                <i class="fa-solid fa-plus-lg"></i> New Theme
            </a>
        </div>

        {{-- SUCCESS ALERT --}}
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-transition
                 class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 mb-6" role="alert">
                <i class="fa-solid fa-check-circle-fill text-lg mt-0.5 shrink-0 text-emerald-500"></i>
                <div class="flex-1 text-sm">{{ session('success') }}</div>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 ml-2 transition-colors">
                    <i class="fa-solid fa-x-lg text-xs"></i>
                </button>
            </div>
        @endif

        {{-- THEME GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-10">
            @foreach ($themes as $theme)
                <div class="group relative bg-white border border-slate-200 rounded-2xl overflow-hidden hover:-translate-y-2 hover:border-indigo-300 hover:shadow-xl hover:shadow-indigo-100/60 transition-all duration-300 shadow-sm">

                    {{-- Active Badge --}}
                    @if ($theme['is_active'])
                        <div class="absolute top-3 right-3 z-10">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-full shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Active
                            </span>
                        </div>
                    @endif

                    {{-- Preview Image --}}
                    <div class="relative overflow-hidden h-48 bg-slate-100">
                        @if (!empty($theme['preview']) && file_exists(public_path($theme['preview'])))
                            <img src="{{ asset($theme['preview']) }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                 alt="{{ $theme['name'] }}">
                        @else
                            <img src="https://placehold.co/600x400/f1f5f9/94a3b8?text=No+Preview"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                 alt="{{ $theme['name'] }}">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>

                    {{-- Card Body --}}
                    <div class="p-5 flex flex-col gap-3">
                        <div class="flex items-start justify-between">
                            <h5 class="font-bold text-slate-800 text-base truncate pr-2">{{ $theme['name'] }}</h5>
                            <span class="text-xs text-indigo-600 font-mono font-bold shrink-0 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded-md">v{{ $theme['version'] }}</span>
                        </div>

                        <p class="text-slate-500 text-sm leading-relaxed line-clamp-2">{{ $theme['description'] }}</p>

                        <div class="grid grid-cols-2 divide-x divide-slate-100 bg-slate-50 border border-slate-100 rounded-xl p-3">
                            <div class="pr-3">
                                <p class="text-slate-400 text-[10px] uppercase tracking-widest font-semibold mb-0.5">Author</p>
                                <p class="text-slate-700 text-xs font-semibold truncate">{{ $theme['author'] }}</p>
                            </div>
                            <div class="pl-3">
                                <p class="text-slate-400 text-[10px] uppercase tracking-widest font-semibold mb-0.5">License</p>
                                <p class="text-slate-700 text-xs font-semibold truncate">{{ $theme['license'] }}</p>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="mt-auto space-y-2 pt-1">
                            @if ($theme['is_active'])
                                <button disabled class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 text-slate-400 text-sm font-semibold rounded-xl cursor-not-allowed border border-slate-200">
                                    <i class="fa-solid fa-check2-all"></i> Currently Active
                                </button>
                            @else
                                <button wire:click="installTheme('{{ $theme['path'] }}')"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-md shadow-indigo-200">
                                    <i class="fa-solid fa-lightning-charge-fill"></i> Activate Theme
                                </button>
                            @endif

                            <div class="flex gap-2">
                                @if ($theme['url'])
                                    <a href="{{ $theme['url'] }}" target="_blank"
                                       class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-600 hover:text-slate-800 text-xs font-medium rounded-xl transition-all duration-200">
                                        <i class="fa-solid fa-eye"></i> Demo
                                    </a>
                                @endif
                                <button wire:click="confirmDeleteTheme('{{ $theme['path'] }}')"
                                        class="flex items-center justify-center gap-1.5 px-3 py-2 border border-rose-200 hover:border-rose-300 hover:bg-rose-50 text-rose-400 hover:text-rose-600 text-xs font-medium rounded-xl transition-all duration-200 {{ $theme['url'] ? '' : 'flex-1' }}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- UPLOAD SECTION --}}
        <div id="upload-section" class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50 border border-indigo-100 text-indigo-600 text-xs font-semibold rounded-lg mb-4 tracking-wide uppercase">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Upload New Theme
                    </div>
                    <h4 class="text-xl font-bold text-slate-800 mb-2">Install a Custom Theme</h4>
                    <p class="text-slate-500 text-sm leading-relaxed">Have a custom theme? Upload your <span class="text-indigo-600 font-mono font-semibold">.zip</span> package here to install it automatically to your dashboard.</p>
                </div>
                <div>
                    <div class="border-2 border-dashed border-slate-200 hover:border-indigo-300 rounded-2xl p-6 text-center transition-colors duration-200 bg-slate-50/50">
                        <i class="fa-solid fa-cloud-arrow-up text-4xl text-indigo-400 mb-3 block"></i>
                        <input type="file" wire:model="themeZip"
                               class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 file:cursor-pointer mb-3 cursor-pointer">
                        @error('themeZip')
                            <p class="text-rose-500 text-xs font-semibold mt-1 mb-3">{{ $message }}</p>
                        @enderror

                        <div wire:loading wire:target="themeZip" class="flex items-center justify-center gap-2 mb-3">
                            <svg class="animate-spin h-4 w-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span class="text-slate-500 text-xs">Uploading file, please wait...</span>
                        </div>

                        <button type="button" wire:click="uploadAndInstall" wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-md shadow-indigo-200">
                            <span wire:loading.remove wire:target="uploadAndInstall">
                                <i class="fa-solid fa-wand-magic-sparkles"></i> Install Now
                            </span>
                            <span wire:loading wire:target="uploadAndInstall" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Installing...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- DELETE MODAL — Pure Alpine.js, zero Bootstrap JS --}}
    <template x-teleport="body">
        <div x-show="showDeleteModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             style="display:none">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-slate-900/30 backdrop-blur-sm" @click="showDeleteModal = false"></div>

            {{-- Panel --}}
            <div x-show="showDeleteModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                 class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl shadow-slate-200/80 border border-slate-200 overflow-hidden z-10"
                 @click.stop>

                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <h5 class="text-slate-800 font-bold flex items-center gap-2 text-base">
                        <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                        Konfirmasi Hapus Tema
                    </h5>
                    <button @click="showDeleteModal = false" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-100">
                        <i class="fa-solid fa-trash-can text-sm"></i>>
                    </button>
                </div>
                <div class="px-6 py-5 text-slate-600 text-sm leading-relaxed">
                    Apakah Anda yakin ingin menghapus tema <strong class="text-slate-800">{{ $themeToDelete }}</strong>?
                    Tindakan ini tidak bisa dibatalkan.
                </div>
                <div class="flex gap-3 justify-end px-6 py-4 border-t border-slate-100 bg-slate-50">
                    <button @click="showDeleteModal = false"
                            class="px-4 py-2 text-slate-600 hover:text-slate-800 border border-slate-200 hover:border-slate-300 hover:bg-white rounded-xl text-sm font-medium transition-all">
                        Batal
                    </button>
                    <button wire:click="deleteConfirmed" @click="showDeleteModal = false"
                            class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-sm font-semibold transition-all shadow-md shadow-rose-200">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </template>

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('alert', (event) => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'info', text: event.message, confirmButtonColor: '#4f46e5' });
                    } else { alert(event.message); }
                });
                Livewire.on('show-delete-modal', () => {
                    window.dispatchEvent(new CustomEvent('show-delete-modal'));
                });
            });
        </script>
    @endpush

</div>
