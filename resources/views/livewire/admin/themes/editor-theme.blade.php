<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/30 py-6 px-4"
     x-data="{ showVariableModal: false, mobileSidebar: false, fullscreenEditor: false }">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h3 class="text-xl font-bold text-slate-800 tracking-tight">Theme Editor</h3>
            <p class="text-slate-500 text-sm mt-0.5">
                Kustomisasi tampilan frontend dan aset tema
                <span class="text-indigo-600 font-semibold font-mono">({{ strtoupper($activeTheme) }})</span>
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">

            {{-- Theme Switcher Dropdown — Pure Alpine --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @keydown.escape="open = false"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 border border-slate-200 hover:border-slate-300 text-slate-700 text-sm font-medium rounded-xl transition-all shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                    Tema: <strong class="text-slate-800">{{ strtoupper($activeTheme) }}</strong>
                    <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200"
                       :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open"
                     @click.outside="open = false"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                     class="absolute right-0 top-full mt-2 w-48 bg-white border border-slate-200 rounded-xl shadow-xl shadow-slate-200/60 overflow-hidden z-50"
                     style="display:none">
                    @foreach($availableThemes as $theme)
                        <a href="#"
                           wire:click.prevent="changeTheme('{{ $theme }}')"
                           @click="open = false"
                           class="flex items-center gap-2 px-4 py-2.5 text-sm transition-colors
                                  {{ $activeTheme === $theme
                                     ? 'bg-indigo-50 text-indigo-700 font-semibold'
                                     : 'text-slate-600 hover:text-slate-900 hover:bg-indigo-50' }}">
                            @if($activeTheme === $theme)
                                <i class="fa-solid fa-check2 text-indigo-500 text-xs"></i>
                            @else
                                <span class="w-3.5 inline-block"></span>
                            @endif
                            {{ strtoupper($theme) }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Variables Button --}}
            <button @click="showVariableModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50 text-slate-600 hover:text-indigo-700 text-sm font-medium rounded-xl transition-all shadow-sm">
                <i class="fas fa-code text-xs"></i> Variables
            </button>

            {{-- Save --}}
            <button wire:click="saveFile" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 text-white text-sm font-semibold rounded-xl transition-all shadow-md shadow-emerald-200">
                <span wire:loading.remove wire:target="saveFile" class="flex items-center gap-2">
                    <i class="fas fa-save text-xs"></i> Simpan
                </span>
                <span wire:loading wire:target="saveFile" class="flex items-center gap-2">
                    <svg class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Menyimpan...
                </span>
            </button>
        </div>
    </div>

    {{-- ALERTS --}}
    @if($saveMessage)
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-4">
            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3">
                <i class="fas fa-check-circle shrink-0 text-emerald-500"></i>
                <span class="text-sm flex-1">{{ $saveMessage }}</span>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 transition-colors">
                    <i class="fa-solid fa-x-lg text-xs"></i>
                </button>
            </div>
        </div>
    @endif
    @if($errorMessage)
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-4">
            <div class="flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl px-4 py-3">
                <i class="fas fa-exclamation-triangle shrink-0 text-rose-500"></i>
                <span class="text-sm flex-1">{{ $errorMessage }}</span>
                <button @click="show = false" class="text-rose-400 hover:text-rose-600 transition-colors">
                    <i class="fa-solid fa-x-lg text-xs"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- MAIN LAYOUT --}}
    <div class="flex flex-col lg:flex-row gap-4 items-start relative">

        {{-- EDITOR AREA --}}
        <div class="flex-1 min-w-0 w-full order-1 mb-24 lg:mb-0">
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">

                {{-- Toolbar --}}
                <div class="flex items-center justify-between px-4 py-3 bg-slate-50 border-b border-slate-200">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="flex gap-1.5 shrink-0">
                            <span class="w-3 h-3 rounded-full bg-rose-400"></span>
                            <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                            <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                        </div>
                        <code class="text-slate-600 text-sm font-mono truncate">
                            {{ $selectedFile ?? 'Pilih file dari sidebar...' }}
                        </code>
                    </div>
                    <div class="flex items-center gap-2 shrink-0 ml-3">
                        <button wire:click="switchLocation('views')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all
                                       {{ $currentLocation === 'views'
                                          ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200'
                                          : 'border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 hover:bg-white' }}">
                            <i class="fas fa-eye text-[10px]"></i> Views (Blade)
                        </button>
                        <button wire:click="switchLocation('public')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all
                                       {{ $currentLocation === 'public'
                                          ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200'
                                          : 'border border-slate-200 text-slate-500 hover:text-slate-700 hover:border-slate-300 hover:bg-white' }}">
                            <i class="fas fa-globe text-[10px]"></i> Public (Assets)
                        </button>
                    </div>
                </div>

                {{-- CodeMirror --}}
                @if ($selectedFile)
                    <div class="p-0"
                         x-data="{
                             editor: null,
                             content: @entangle('fileContent'),
                             initEditor() {
                                 const container = this.$refs.codeeditor;
                                 this.editor = CodeMirror.fromTextArea(container, {
                                     lineNumbers: true,
                                     mode: getModeForLanguage('{{ $fileLanguage }}'),
                                     theme: 'monokai',
                                     lineWrapping: true,
                                     indentUnit: 4,
                                     extraKeys: {
                                         'Ctrl-S': () => { this.save(); },
                                         'Cmd-S': () => { this.save(); }
                                     }
                                 });
                                 this.editor.setValue(this.content || '');
                                 this.editor.on('change', (cm) => { this.content = cm.getValue(); });
                                 setTimeout(() => this.editor.refresh(), 50);
                             },
                             save() {
                                 this.content = this.editor.getValue();
                                 $wire.saveFile();
                             }
                         }"
                         x-init="initEditor()"
                         x-effect="if(content) { if(editor && editor.getValue() !== content) { editor.setValue(content); } }">
                        <div wire:ignore>
                            <textarea x-ref="codeeditor" class="hidden"></textarea>
                        </div>
                        <div class="flex items-center justify-between px-4 py-2 bg-slate-50 border-t border-slate-100">
                            <span class="text-slate-400 text-xs font-mono uppercase tracking-wide">
                                {{ strtoupper($fileLanguage) }} Mode
                            </span>
                            <span class="text-emerald-600 text-xs flex items-center gap-1.5 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Auto-sync enabled
                            </span>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-24 text-center">
                        <div class="p-5 bg-slate-100 rounded-2xl mb-4">
                            <i class="fa-solid fa-file-earmark-code text-5xl text-slate-400"></i>
                        </div>
                        <h5 class="text-slate-600 font-semibold mb-1">Pilih file untuk diedit</h5>
                        <p class="text-slate-400 text-sm">Pilih file dari sidebar di sebelah kanan</p>
                    </div>
                @endif

            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="fixed bottom-0 left-0 right-0 z-40 lg:relative lg:bottom-auto lg:w-56 lg:shrink-0 p-4 lg:p-0 bg-white/80 backdrop-blur-md lg:bg-transparent border-t lg:border-t-0 border-slate-200">
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xl lg:shadow-sm lg:sticky lg:top-6">

                <div class="hidden lg:flex items-center gap-2 px-4 py-3 border-b border-slate-100 bg-slate-50">
                    <i class="fas fa-folder-tree text-amber-500 text-sm"></i>
                    <span class="text-slate-700 text-xs font-semibold">Daftar File</span>
                </div>

                {{-- Desktop List --}}
                <div class="hidden lg:block overflow-y-auto" style="max-height: calc(100vh - 14rem);">
                    @forelse($files as $file)
                        <button wire:click="selectFile('{{ $file['path'] }}')"
                                class="w-full text-left px-3 py-2.5 border-b border-slate-50 transition-all border-l-2
                                       {{ $selectedFile === $file['path']
                                          ? 'bg-indigo-50 border-l-indigo-500'
                                          : 'border-l-transparent hover:bg-slate-50 hover:border-l-slate-200' }}">
                            <div class="flex items-center gap-2">
                                <i class="fas {{ in_array($file['extension'], ['php','blade.php']) ? 'fa-code' : 'fa-file-alt' }} text-xs shrink-0
                                           {{ $selectedFile === $file['path'] ? 'text-indigo-500' : 'text-slate-400' }}"></i>
                                <span class="text-xs truncate
                                             {{ $selectedFile === $file['path'] ? 'text-indigo-700 font-semibold' : 'text-slate-600' }}">
                                    {{ $file['name'] }}
                                </span>
                            </div>
                            <p class="text-[10px] mt-0.5 pl-4
                                      {{ $selectedFile === $file['path'] ? 'text-indigo-400' : 'text-slate-400' }}">
                                {{ number_format($file['size'] / 1024, 1) }} KB
                            </p>
                        </button>
                    @empty
                        <div class="flex flex-col items-center justify-center py-8 text-center px-4">
                            <i class="fas fa-search text-2xl text-slate-300 mb-2"></i>
                            <p class="text-slate-400 text-xs">Kosong</p>
                        </div>
                    @endforelse
                </div>

                {{-- Mobile Select & Actions --}}
                <div class="p-3 lg:border-t border-slate-100 bg-slate-50 flex flex-row lg:flex-col gap-2 items-center">
                    <div class="flex-1 lg:hidden">
                        <select class="w-full text-xs border-slate-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                x-on:change="$wire.selectFile($event.target.value)">
                            <option value="">-- Pilih File --</option>
                            @foreach($files as $file)
                                <option value="{{ $file['path'] }}" {{ $selectedFile === $file['path'] ? 'selected' : '' }}>
                                    {{ $file['name'] }} ({{ number_format($file['size'] / 1024, 1) }} KB)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button wire:click="saveFile" wire:loading.attr="disabled"
                            class="lg:w-full flex items-center justify-center gap-1.5 px-4 lg:px-0 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 text-white text-xs font-semibold rounded-lg transition-all shadow-sm shadow-emerald-100">
                        <span wire:loading.remove wire:target="saveFile" class="flex items-center gap-1">
                            <i class="fas fa-save text-[10px]"></i> Save Changes
                        </span>
                        <span wire:loading wire:target="saveFile" class="flex items-center gap-1.5">
                            <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Saving...
                        </span>
                    </button>
                    <button onclick="window.location.reload()"
                            class="lg:w-full flex items-center justify-center gap-1.5 px-3 lg:px-0 py-2 border border-slate-200 hover:border-slate-300 hover:bg-white text-slate-500 hover:text-slate-700 text-xs font-medium rounded-lg transition-all">
                        <i class="fas fa-sync-alt text-[10px]"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- VARIABLE MODAL — 100% Alpine.js, zero Bootstrap JS                --}}
    {{-- ================================================================= --}}
    <template x-teleport="body">
        <div x-show="showVariableModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             @keydown.escape.window="showVariableModal = false"
             style="display:none">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-slate-900/20 backdrop-blur-sm"
                 @click="showVariableModal = false"></div>

            {{-- Panel --}}
            <div x-show="showVariableModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                 x-data="{ activeTab: 'basic', loopTab: 'posts' }"
                 class="relative w-full max-w-3xl bg-white rounded-2xl shadow-2xl shadow-slate-200/80 border border-slate-200 overflow-hidden z-10 max-h-[90vh] flex flex-col"
                 @click.stop>

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50 shrink-0">
                    <h5 class="text-slate-800 font-bold flex items-center gap-2">
                        <i class="fas fa-book text-indigo-500"></i> Cheat Sheet Variable
                    </h5>
                    <button @click="showVariableModal = false"
                            class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-200 rounded-lg transition-all">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6 overflow-y-auto flex-1">

                    {{-- Main Tab Pills --}}
                    <div class="flex gap-1 mb-5 p-1 bg-slate-100 rounded-xl w-fit">
                        <button @click="activeTab = 'basic'"
                                :class="activeTab === 'basic'
                                    ? 'bg-white text-indigo-700 shadow-sm font-semibold'
                                    : 'text-slate-500 hover:text-slate-700'"
                                class="px-4 py-1.5 text-xs rounded-lg transition-all duration-150">
                            Basic Setting
                        </button>
                        <button @click="activeTab = 'social'"
                                :class="activeTab === 'social'
                                    ? 'bg-white text-indigo-700 shadow-sm font-semibold'
                                    : 'text-slate-500 hover:text-slate-700'"
                                class="px-4 py-1.5 text-xs rounded-lg transition-all duration-150">
                            Social Media
                        </button>
                        <button @click="activeTab = 'loops'"
                                :class="activeTab === 'loops'
                                    ? 'bg-white text-indigo-700 shadow-sm font-semibold'
                                    : 'text-slate-500 hover:text-slate-700'"
                                class="px-4 py-1.5 text-xs rounded-lg transition-all duration-150">
                            Looping Examples
                        </button>
                    </div>

                    {{-- TAB: Basic Setting --}}
                    <div x-show="activeTab === 'basic'"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-x-1"
                         x-transition:enter-end="opacity-100 translate-x-0">
                        <p class="text-slate-400 text-xs mb-3">Variabel setting dasar untuk template</p>
                        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden divide-y divide-slate-50">
                            @foreach([
                                ['$settings->sitename',    'Nama situs'],
                                ['$settings->description', 'Deskripsi situs'],
                                ['$settings->url',         'URL situs'],
                                ['$settings->logo',        'Path logo'],
                                ['$settings->images',      'Path gambar utama'],
                                ['$settings->email',       'Email kontak'],
                                ['$settings->address',     'Alamat'],
                                ['$settings->phone',       'Nomor telepon'],
                            ] as [$var, $label])
                            <div class="flex items-center justify-between px-4 py-2.5 hover:bg-slate-50/70 transition-colors group">
                                <div>
                                    <code class="text-indigo-600 text-xs font-mono">@{{ {{ $var }} }}</code>
                                    <span class="text-slate-400 text-xs ml-2">— {{ $label }}</span>
                                </div>
                                <button onclick="copyVar(this, '@{{ {{ $var }} }}')"
                                        class="opacity-0 group-hover:opacity-100 px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-600 text-[10px] font-semibold rounded-lg transition-all flex items-center gap-1">
                                    <i class="fa-solid fa-clipboard"></i> Copy
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- TAB: Social Media --}}
                    <div x-show="activeTab === 'social'"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-x-1"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         style="display:none">
                        <p class="text-slate-400 text-xs mb-3">Variabel sosial media untuk template</p>
                        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden divide-y divide-slate-50">
                            @foreach([
                                ['$settings->whatsapp',  'bi-whatsapp',  'text-green-500',  'WhatsApp'],
                                ['$settings->facebook',  'bi-facebook',  'text-blue-500',   'Facebook'],
                                ['$settings->instagram', 'bi-instagram', 'text-pink-500',   'Instagram'],
                                ['$settings->linkedin',  'bi-linkedin',  'text-blue-600',   'LinkedIn'],
                                ['$settings->youtube',   'bi-youtube',   'text-red-500',    'YouTube'],
                                ['$settings->tiktok',    'bi-tiktok',    'text-slate-700',  'TikTok'],
                            ] as [$var, $icon, $iconColor, $platform])
                            <div class="flex items-center justify-between px-4 py-2.5 hover:bg-slate-50/70 transition-colors group">
                                <div class="flex items-center gap-3">
                                    <i class="bi {{ $icon }} {{ $iconColor }} text-base w-5 text-center"></i>
                                    <div>
                                        <code class="text-indigo-600 text-xs font-mono">@{{ {{ $var }} }}</code>
                                        <span class="text-slate-400 text-xs ml-2">— {{ $platform }}</span>
                                    </div>
                                </div>
                                <button onclick="copyVar(this, '@{{ {{ $var }} }}')"
                                        class="opacity-0 group-hover:opacity-100 px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-600 text-[10px] font-semibold rounded-lg transition-all flex items-center gap-1">
                                    <i class="fa-solid fa-clipboard"></i> Copy
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- TAB: Looping Examples --}}
                    <div x-show="activeTab === 'loops'"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-x-1"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         style="display:none">
                        <p class="text-slate-400 text-xs mb-3">Contoh looping siap pakai, salin ke editor</p>

                        {{-- Loop Sub-tabs --}}
                        <div class="flex gap-1.5 mb-4">
                            <button @click="loopTab = 'posts'"
                                    :class="loopTab === 'posts'
                                        ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200'
                                        : 'border border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
                                <i class="fa-solid fa-newspaper text-[10px]"></i> Latest Posts
                            </button>
                            <button @click="loopTab = 'gallery'"
                                    :class="loopTab === 'gallery'
                                        ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200'
                                        : 'border border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
                                <i class="fa-solid fa-images text-[10px]"></i> Gallery
                            </button>
                            <button @click="loopTab = 'youtube'"
                                    :class="loopTab === 'youtube'
                                        ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200'
                                        : 'border border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">
                                <i class="fa-solid fa-youtube text-[10px]"></i> Youtube
                            </button>
                        </div>

                        {{-- Posts Code --}}
                        <div x-show="loopTab === 'posts'">
                            <div class="bg-slate-900 rounded-xl overflow-hidden">
                                <div class="flex items-center justify-between px-4 py-2.5 border-b border-white/10">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                        <span class="text-slate-400 text-xs font-mono">blade / latest-posts</span>
                                    </div>
                                    <button onclick="copyCode('code-posts', this)"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/10 hover:bg-white/20 text-slate-200 text-[10px] font-semibold rounded-md transition-all">
                                        <i class="fa-solid fa-clipboard"></i> Copy
                                    </button>
                                </div>
                                <pre class="p-4 text-amber-300 text-xs leading-relaxed overflow-x-auto font-mono" id="code-posts">@@forelse ($latestposts as $post)
    @@forelse ($post->gambar() as $gam)
        @@if ($loop->first)
            &lt;img src="@{{ $gam }}" alt="@{{ $post->title }}" loading="lazy"&gt;
        @@endif
    @@empty
        &lt;img src="/frontend/img/default.webp"&gt;
    @@endforelse
    @{{ formatDate($post->datepublish) }}
    @{{ $post->title }}
    @{{ $post->category->name }}
@@empty
    &lt;p&gt;Belum ada Berita&lt;/p&gt;
@@endforelse</pre>
                            </div>
                        </div>

                        {{-- Gallery Code --}}
                        <div x-show="loopTab === 'gallery'" style="display:none">
                            <div class="bg-slate-900 rounded-xl overflow-hidden">
                                <div class="flex items-center justify-between px-4 py-2.5 border-b border-white/10">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                                        <span class="text-slate-400 text-xs font-mono">blade / gallery</span>
                                    </div>
                                    <button onclick="copyCode('code-gallery', this)"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/10 hover:bg-white/20 text-slate-200 text-[10px] font-semibold rounded-md transition-all">
                                        <i class="fa-solid fa-clipboard"></i> Copy
                                    </button>
                                </div>
                                <pre class="p-4 text-amber-300 text-xs leading-relaxed overflow-x-auto font-mono" id="code-gallery">@@foreach ($gallery as $galleries)
    &lt;div class="col-lg-3 col-md-12"&gt;
        &lt;a href="/storage/@{{ $galleries->image_path }}" class="glightbox"&gt;
            &lt;img src="/storage/@{{ $galleries->image_path }}"
                 alt="@{{ $galleries->name }}"&gt;
        &lt;/a&gt;
    &lt;/div&gt;
@@endforeach</pre>
                            </div>
                        </div>

                        {{-- Youtube Code --}}
                        <div x-show="loopTab === 'youtube'" style="display:none">
                            <div class="bg-slate-900 rounded-xl overflow-hidden">
                                <div class="flex items-center justify-between px-4 py-2.5 border-b border-white/10">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                                        <span class="text-slate-400 text-xs font-mono">blade / youtube</span>
                                    </div>
                                    <button onclick="copyCode('code-youtube', this)"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/10 hover:bg-white/20 text-slate-200 text-[10px] font-semibold rounded-md transition-all">
                                        <i class="fa-solid fa-clipboard"></i> Copy
                                    </button>
                                </div>
                                <pre class="p-4 text-amber-300 text-xs leading-relaxed overflow-x-auto font-mono" id="code-youtube">@@foreach ($youtubes as $video)
    &lt;div class="col"&gt;
        &lt;div class="video-item" data-video-id="@{{ $video->video_id }}"&gt;
            &lt;img src="@{{ $video->thumbnail_url }}" alt="Thumbnail"&gt;
            &lt;i class="fab fa-youtube"&gt;&lt;/i&gt;
        &lt;/div&gt;
    &lt;/div&gt;
@@endforeach</pre>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex justify-end px-6 py-4 border-t border-slate-100 bg-slate-50 shrink-0">
                    <button @click="showVariableModal = false"
                            class="px-4 py-2 text-slate-600 hover:text-slate-800 border border-slate-200 hover:border-slate-300 hover:bg-white rounded-xl text-sm font-medium transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>

</div>

@once
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css">
<style>
    .CodeMirror {
        height: 100% !important;
        min-height: 600px;
        font-family: 'Fira Code', monospace;
        font-size: 13px;
    }
</style>
@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/php/php.min.js"></script>
<script>
    function getModeForLanguage(lang) {
        const modes = {
            'js': 'javascript', 'javascript': 'javascript',
            'css': 'text/css', 'php': 'application/x-httpd-php',
            'html': 'text/html', 'blade': 'application/x-httpd-php'
        };
        return modes[lang] || 'text/plain';
    }

    function copyVar(btn, text) {
        navigator.clipboard.writeText(text).then(() => {
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-check2"></i> Copied!';
            btn.classList.replace('bg-indigo-50','bg-emerald-50');
            btn.classList.replace('border-indigo-200','border-emerald-200');
            btn.classList.replace('text-indigo-600','text-emerald-600');
            setTimeout(() => {
                btn.innerHTML = orig;
                btn.classList.replace('bg-emerald-50','bg-indigo-50');
                btn.classList.replace('border-emerald-200','border-indigo-200');
                btn.classList.replace('text-emerald-600','text-indigo-600');
            }, 1500);
        });
    }

    function copyCode(elementId, btn) {
        const el = document.getElementById(elementId);
        if (!el) return;
        const text = el.innerText.replace(/@@/g, '@');
        navigator.clipboard.writeText(text).then(() => {
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-check2"></i> Copied!';
            btn.classList.add('bg-emerald-600');
            setTimeout(() => {
                btn.innerHTML = orig;
                btn.classList.remove('bg-emerald-600');
            }, 1500);
        });
    }
</script>
@endpush
@endonce
