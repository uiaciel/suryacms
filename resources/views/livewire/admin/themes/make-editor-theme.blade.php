{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- 1. INJECT CSS UNTUK SEMBUNYIKAN LAYOUT DEFAULT              --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
@push('styles')
<style>
    /* Sembunyikan sidebar, header admin, dan footer global */
    body > .flex > aside,
    body > .flex > .flex-1 > header,
    body > .flex > .flex-1 > footer {
        display: none !important;
    }

    /* Hilangkan padding main content agar editor full width */
    body > .flex > .flex-1 > main {
        padding: 0 !important;
        background-color: #0f172a !important;
    }

    body > .flex {
        height: 100vh;
    }

    body > .flex > .flex-1 {
        margin-left: 0 !important;
        width: 100% !important;
    }

    /* Custom Scrollbar untuk Editor */
    .editor-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
    .editor-scroll::-webkit-scrollbar-track { background: transparent; }
    .editor-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
    .editor-scroll::-webkit-scrollbar-thumb:hover { background: #475569; }

    .theme-editor-context-menu {
        position: fixed;
        z-index: 100;
        width: min(20rem, calc(100vw - 1rem));
        max-height: calc(100vh - 1rem);
        overflow: hidden;
        border: 1px solid #334155;
        border-radius: 0.75rem;
        background: #0f172a;
        box-shadow: 0 20px 45px rgb(0 0 0 / 45%);
    }

    .theme-editor-context-menu-list {
        max-height: calc(100vh - 7rem);
        overflow-y: auto;
    }

    [x-cloak] { display: none !important; }
</style>
@endpush

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- 2. SINGLE ROOT WRAPPER (PENTING: Hanya 1 x-data di sini!)   --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div x-data="editorApp()" class="h-screen flex flex-col bg-slate-950 text-slate-300 w-full" @click.away="closeContextMenu()">

    {{-- ─── TOP BAR CUSTOM ─────────────────────────────────────── --}}
    <div class="h-14 bg-slate-900 border-b border-slate-800 flex items-center justify-between px-5 shadow-lg shrink-0 z-50">

        {{-- KIRI: Logo & Back Button --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.themes.make') }}"
                class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 hover:text-white text-xs font-medium transition-all group">
                <i class="fa-solid fa-arrow-left text-[10px] group-hover:-translate-x-0.5 transition-transform"></i>
                <span>Back</span>
            </a>
            <div class="w-px h-6 bg-slate-700"></div>
            <div class="flex items-center gap-2.5">
                @if ($setting?->logo)
                    <img src="{{ asset($setting->logo) }}" class="w-7 h-7 rounded" alt="Logo" />
                @else
                    <div class="w-7 h-7 bg-gradient-to-br from-blue-500 to-violet-600 rounded flex items-center justify-center text-white font-black text-xs">S</div>
                @endif
                <div class="flex flex-col">
                    <span class="text-white text-sm font-bold leading-tight">Theme Editor</span>
                    <span class="text-slate-500 text-[10px] font-mono leading-tight">{{ $theme ?? 'Unknown' }}</span>
                </div>
            </div>
        </div>

        {{-- TENGAH: Mode Tabs --}}
        <div class="hidden md:flex items-center gap-1 p-1 bg-slate-800/50 rounded-lg border border-slate-700/50">
            <button @click="previewMode = 'editor'"
                :class="previewMode === 'editor' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-700'"
                class="flex items-center gap-2 px-4 py-1.5 rounded-md text-xs font-medium transition-all">
                <i class="fa-solid fa-code text-[10px]"></i> Editor
            </button>
            <button @click="previewMode = 'rendered'"
                :class="previewMode === 'rendered' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-700'"
                class="flex items-center gap-2 px-4 py-1.5 rounded-md text-xs font-medium transition-all">
                <i class="fa-solid fa-eye text-[10px]"></i> Live Preview
            </button>
            <button @click="previewMode = 'source'"
                :class="previewMode === 'source' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-700'"
                class="flex items-center gap-2 px-4 py-1.5 rounded-md text-xs font-medium transition-all">
                <i class="fa-solid fa-image text-[10px]"></i> Original
            </button>
        </div>

        {{-- KANAN: Actions --}}
        <div class="flex items-center gap-3">
            <button @click="saveContent()" :disabled="!selectedFile || !isDirty"
                class="flex items-center gap-1.5 px-3.5 py-1.5 bg-gradient-to-r from-blue-600 to-violet-600 hover:from-blue-500 hover:to-violet-500 disabled:opacity-40 disabled:cursor-not-allowed text-white text-xs font-semibold rounded-lg transition-all shadow-lg shadow-blue-900/30">
                <i class="fa-solid fa-floppy-disk text-[10px]"></i>
                <span>Save</span>
            </button>
        </div>
    </div>

    {{-- ─── MAIN CONTENT AREA ──────────────────────────────────── --}}
    <div class="flex-1 flex overflow-hidden">

        {{-- LEFT PANEL: FILE TREE --}}
        <div class="w-72 flex flex-col h-full border-r border-slate-800 bg-slate-900/30">
            <div class="px-4 py-3 border-b border-slate-800/60 flex items-center justify-between shrink-0">
                <span class="text-white text-xs font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-folder-tree text-blue-400"></i> Explorer
                </span>
                <button wire:click="loadFileTree" class="w-6 h-6 flex items-center justify-center text-slate-500 hover:text-slate-300 hover:bg-slate-800 rounded transition">
                    <i class="fa-solid fa-rotate text-[10px]"></i>
                </button>
            </div>

            <div class="px-3 py-2 border-b border-slate-800/60 shrink-0">
                <input x-model="searchQuery" type="text" placeholder="Cari file..."
                    class="w-full bg-slate-950 border border-slate-800 focus:border-blue-500/50 text-slate-300 text-[11px] rounded-lg px-3 py-1.5 placeholder-slate-600 transition outline-none" />
            </div>

            <div class="flex-1 overflow-y-auto p-2 editor-scroll">
                @foreach ($fileTree as $item)
                    @php
                        $isDir = $item['type'] === 'dir';
                        $iconClass = $isDir ? 'fa-folder text-amber-400/80' : 'fa-file-code';
                        if (!$isDir) {
                            $iconClass .= ' ' . match ($item['ext']) {
                                'php', 'blade' => 'text-violet-400', 'css' => 'text-blue-400',
                                'js' => 'text-yellow-400', 'html' => 'text-orange-400', default => 'text-slate-400',
                            };
                        }
                    @endphp
                    <div x-show="!searchQuery || '{{ strtolower($item['name']) }}'.includes(searchQuery.toLowerCase())"
                        style="padding-left: {{ $item['depth'] * 12 }}px;" class="mb-0.5">

                        @if ($isDir)
                            <div x-data="{ open: {{ $item['is_root'] ? 'true' : 'false' }} }">
                                <button @click="open = !open" class="w-full flex items-center gap-1.5 px-2 py-1.5 text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 rounded-lg text-xs transition">
                                    <i class="fa-solid fa-chevron-right text-[8px] transition-transform duration-200" :class="open ? 'rotate-90' : ''"></i>
                                    <i class="fa-solid {{ $iconClass }} text-[11px]"></i>
                                    <span class="font-medium truncate">{{ $item['name'] }}</span>
                                </button>
                            </div>
                        @else
                            <button wire:click="selectFile('{{ $item['path'] }}')"
                                @class([
                                    'w-full flex items-center gap-2 px-2 py-1.5 rounded-lg text-xs transition-all text-left',
                                    'bg-blue-500/15 text-blue-300 border border-blue-500/30' => $selectedFile === $item['path'],
                                    'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border border-transparent' => $selectedFile !== $item['path'],
                                ])>
                                <i class="fa-solid {{ $iconClass }} text-[11px]"></i>
                                <span class="truncate flex-1 font-mono">{{ $item['name'] }}</span>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- RIGHT PANEL: EDITOR / PREVIEW / SOURCE --}}
        <div class="flex-1 flex flex-col h-full bg-slate-950 relative">

            {{-- 1. EDITOR VIEW --}}
            <div x-show="previewMode === 'editor'" x-cloak class="flex-1 flex flex-col overflow-hidden">
                <div class="flex items-center justify-between px-4 py-2 bg-slate-900/80 border-b border-slate-800/60 shrink-0">
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                        @if ($selectedFile)
                            <span class="text-slate-200 text-xs font-mono font-medium truncate">{{ $selectedFile }}</span>
                            <span x-show="isDirty" class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse" title="Unsaved"></span>
                        @else
                            <span class="text-slate-600 text-xs italic">Pilih file untuk mengedit</span>
                        @endif
                    </div>
                    <div class="text-[10px] text-slate-500 font-mono">
                        <span x-text="content ? content.split('\n').length : 0"></span> lines • UTF-8
                    </div>
                </div>
                <div class="flex-1 relative" wire:ignore>
                    <textarea
                        x-ref="codeEditor"
                        x-model="content"
                        @keydown.meta.s.prevent="saveContent()"
                        @keydown.ctrl.s.prevent="saveContent()"
                        @contextmenu.prevent="showContextMenu($event)"
                        class="w-full h-full bg-slate-950 text-slate-300 text-xs font-mono leading-5 p-4 border-0 resize-none focus:outline-none focus:ring-0 editor-scroll"
                        spellcheck="false"
                        placeholder="// Pilih file dari explorer... (Klik kanan untuk Variables & Snippets)"
                    ></textarea>
                </div>

                {{-- CUSTOM CONTEXT MENU --}}
                <div
                    x-ref="contextMenu"
                    x-show="contextMenu.show"
                    x-cloak
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    :style="`top: ${contextMenu.y}px; left: ${contextMenu.x}px`"
                    class="theme-editor-context-menu"
                    @click.away="closeContextMenu()"
                    role="menu"
                    aria-label="Sisipkan kode tema">
                    <div class="flex items-center justify-between px-3 py-2.5 border-b border-slate-800">
                        <template x-if="contextMenu.section">
                            <button @click="openSnippetSection(null)" class="flex items-center gap-2 text-slate-400 hover:text-white text-xs transition">
                                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                                <span>Kembali</span>
                            </button>
                        </template>
                        <span x-show="!contextMenu.section" class="text-slate-200 text-xs font-semibold">Sisipkan ke editor</span>
                        <span x-show="contextMenu.section === 'variables'" class="text-slate-200 text-xs font-semibold">Pengaturan situs</span>
                        <span x-show="contextMenu.section === 'globalData'" class="text-slate-200 text-xs font-semibold">Variabel global</span>
                        <span x-show="contextMenu.section === 'blade'" class="text-slate-200 text-xs font-semibold">Blade snippets</span>
                        <button @click="closeContextMenu()" class="w-6 h-6 flex items-center justify-center rounded text-slate-500 hover:text-white hover:bg-slate-800 transition" aria-label="Tutup menu">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>

                    <div x-show="!contextMenu.section" class="p-1.5">
                        <button @click="openSnippetSection('variables')" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-left hover:bg-slate-800 transition">
                            <span class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center"><i class="fa-solid fa-sliders text-xs"></i></span>
                            <span class="flex-1">
                                <span class="block text-xs text-slate-200">Pengaturan situs</span>
                                <span class="block text-[10px] text-slate-500">Nama, kontak, warna, dan lainnya</span>
                            </span>
                            <span class="text-[10px] text-slate-500" x-text="snippets.variables.length"></span>
                            <i class="fa-solid fa-chevron-right text-[9px] text-slate-500"></i>
                        </button>
                        <button @click="openSnippetSection('globalData')" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-left hover:bg-slate-800 transition">
                            <span class="w-8 h-8 rounded-lg bg-violet-500/10 text-violet-400 flex items-center justify-center"><i class="fa-solid fa-database text-xs"></i></span>
                            <span class="flex-1">
                                <span class="block text-xs text-slate-200">Variabel global</span>
                                <span class="block text-[10px] text-slate-500">Menu, artikel, kategori, dan lainnya</span>
                            </span>
                            <span class="text-[10px] text-slate-500" x-text="snippets.globalData.length"></span>
                            <i class="fa-solid fa-chevron-right text-[9px] text-slate-500"></i>
                        </button>
                        <button @click="openSnippetSection('blade')" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-left hover:bg-slate-800 transition">
                            <span class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center"><i class="fa-solid fa-code text-xs"></i></span>
                            <span class="flex-1">
                                <span class="block text-xs text-slate-200">Blade snippets</span>
                                <span class="block text-[10px] text-slate-500">Loop, kondisi, SEO, dan asset</span>
                            </span>
                            <span class="text-[10px] text-slate-500" x-text="snippets.blade.length"></span>
                            <i class="fa-solid fa-chevron-right text-[9px] text-slate-500"></i>
                        </button>
                    </div>

                    <div x-show="contextMenu.section" class="theme-editor-context-menu-list p-1.5">
                        <template x-for="item in (contextMenu.section ? snippets[contextMenu.section] : [])" :key="item.label">
                            <button @click="insertSnippet(item.snippet)" class="w-full flex items-start gap-3 px-3 py-2 rounded-lg text-left hover:bg-slate-800 transition">
                                <i class="fa-solid fa-plus text-[10px] text-blue-400 mt-1"></i>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-xs text-slate-200" x-text="item.label"></span>
                                    <code class="block mt-1 text-[10px] text-slate-500 font-mono truncate" x-text="item.variable || item.snippet"></code>
                                </span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- 2. LIVE PREVIEW VIEW --}}
            <div x-show="previewMode === 'rendered'" x-cloak class="flex-1 flex flex-col overflow-hidden">
                <div class="flex items-center justify-between px-4 py-2 border-b border-slate-800/60 bg-slate-900/40 shrink-0">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        <span class="text-slate-300 text-xs font-semibold">Live Preview</span>
                        <div class="flex items-center gap-1 bg-slate-800/50 rounded p-0.5 border border-slate-700/50">
                            <button @click="deviceMode = 'desktop'" :class="deviceMode === 'desktop' ? 'bg-slate-700 text-blue-400' : 'text-slate-500'" class="p-1.5 rounded transition-all"><i class="fa-solid fa-desktop text-xs"></i></button>
                            <button @click="deviceMode = 'tablet'" :class="deviceMode === 'tablet' ? 'bg-slate-700 text-blue-400' : 'text-slate-500'" class="p-1.5 rounded transition-all"><i class="fa-solid fa-tablet-screen-button text-xs"></i></button>
                            <button @click="deviceMode = 'mobile'" :class="deviceMode === 'mobile' ? 'bg-slate-700 text-blue-400' : 'text-slate-500'" class="p-1.5 rounded transition-all"><i class="fa-solid fa-mobile-screen-button text-xs"></i></button>
                        </div>
                    </div>
                    <button @click="$refs.renderFrame.src = $refs.renderFrame.src" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-400 text-[10px] rounded transition">
                        <i class="fa-solid fa-rotate text-[10px]"></i> Refresh
                    </button>
                </div>
                <div class="flex-1 w-full bg-slate-800/30 flex items-start justify-center overflow-y-auto p-4 editor-scroll">
                    <iframe x-ref="renderFrame" src="{{ route('admin.themes.preview', $theme)}}"
                        class="bg-white border border-slate-700 rounded shadow-2xl transition-all duration-300 ease-in-out"
                        :class="{
                            'w-full h-full max-w-full': deviceMode === 'desktop',
                            'w-full h-full max-w-[768px]': deviceMode === 'tablet',
                            'w-full h-full max-w-[375px]': deviceMode === 'mobile'
                        }" title="Live Preview"></iframe>
                </div>
            </div>

            {{-- 3. ORIGINAL SOURCE VIEW --}}
            <div x-show="previewMode === 'source'" x-cloak class="flex-1 flex flex-col overflow-hidden">
                <div class="flex items-center justify-between px-4 py-2 border-b border-slate-800/60 bg-slate-900/40 shrink-0">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 bg-blue-400 rounded-full"></span>
                        <span class="text-slate-300 text-xs font-semibold">Original Design</span>
                        <div class="flex items-center gap-1 bg-slate-800/50 rounded p-0.5 border border-slate-700/50">
                            <button @click="deviceMode = 'desktop'" :class="deviceMode === 'desktop' ? 'bg-slate-700 text-blue-400' : 'text-slate-500'" class="p-1.5 rounded transition-all"><i class="fa-solid fa-desktop text-xs"></i></button>
                            <button @click="deviceMode = 'tablet'" :class="deviceMode === 'tablet' ? 'bg-slate-700 text-blue-400' : 'text-slate-500'" class="p-1.5 rounded transition-all"><i class="fa-solid fa-tablet-screen-button text-xs"></i></button>
                            <button @click="deviceMode = 'mobile'" :class="deviceMode === 'mobile' ? 'bg-slate-700 text-blue-400' : 'text-slate-500'" class="p-1.5 rounded transition-all"><i class="fa-solid fa-mobile-screen-button text-xs"></i></button>
                        </div>
                    </div>
                    <button @click="$refs.sourceFrame.contentWindow.location.reload()" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-400 text-[10px] rounded transition">
                        <i class="fa-solid fa-rotate text-[10px]"></i> Refresh
                    </button>
                </div>
                <div class="flex-1 w-full bg-slate-800/30 flex items-start justify-center overflow-y-auto p-4 editor-scroll">
                    <iframe x-ref="sourceFrame" src="{{ route('admin.themes.preview.source', ['sessionId' => $sessionId]) }}"
                        class="bg-white border border-slate-700 rounded shadow-2xl transition-all duration-300 ease-in-out"
                        :class="{
                            'w-full h-full max-w-full': deviceMode === 'desktop',
                            'w-full h-full max-w-[768px]': deviceMode === 'tablet',
                            'w-full h-full max-w-[375px]': deviceMode === 'mobile'
                        }" title="Original Design"></iframe>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- 3. ALPINE.JS LOGIC (Hanya didefinisikan 1 kali)             --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
@push('scripts')
<script>
    function editorApp() {
        return {
            previewMode: 'editor',
            deviceMode: 'desktop',
            searchQuery: '',
            content: @js($fileContent),
            savedContent: @js($fileContent),
            selectedFile: @js($selectedFile),
            isDirty: false,

            contextMenu: { show: false, x: 0, y: 0, section: null },
            savedSelection: { start: 0, end: 0 },

            snippets: @js($this->editorSnippets),

            init() {
                this.$watch('content', (value) => {
                    this.isDirty = (value !== this.savedContent);
                });

                this.$wire.on('file-loaded', (data) => {
                    this.content = data.content;
                    this.savedContent = data.content;
                    this.selectedFile = data.file;
                    this.isDirty = false;
                });

                this.$wire.on('file-saved', (data) => {
                    this.savedContent = data.content;
                    this.isDirty = false;
                });
            },

            showContextMenu(e) {
                if (!this.selectedFile) return;

                const textarea = this.$refs.codeEditor;
                this.savedSelection.start = textarea.selectionStart;
                this.savedSelection.end = textarea.selectionEnd;

                this.contextMenu.x = e.clientX;
                this.contextMenu.y = e.clientY;
                this.contextMenu.section = null;
                this.contextMenu.show = true;

                this.$nextTick(() => this.fitContextMenu());
            },

            fitContextMenu() {
                const menu = this.$refs.contextMenu;
                if (!menu) return;

                const margin = 8;
                this.contextMenu.x = Math.max(
                    margin,
                    Math.min(this.contextMenu.x, window.innerWidth - menu.offsetWidth - margin)
                );
                this.contextMenu.y = Math.max(
                    margin,
                    Math.min(this.contextMenu.y, window.innerHeight - menu.offsetHeight - margin)
                );
            },

            openSnippetSection(section) {
                this.contextMenu.section = section;
                this.$nextTick(() => this.fitContextMenu());
            },

            closeContextMenu() {
                this.contextMenu.show = false;
                this.contextMenu.section = null;
            },

            // ═══════════════════════════════════════════════════════════
            // PERBAIKAN: Menggunakan execCommand agar Undo (Ctrl+Z) berfungsi
            // ═══════════════════════════════════════════════════════════
            insertSnippet(snippet) {
                if (!this.selectedFile) return;

                const textarea = this.$refs.codeEditor;

                // 1. Fokuskan textarea terlebih dahulu (Wajib agar execCommand bekerja)
                textarea.focus();

                // 2. Gunakan execCommand untuk menyisipkan teks.
                // Ini secara otomatis mencatat aksi ke dalam history Undo/Redo browser.
                if (document.queryCommandSupported('insertText')) {
                    document.execCommand('insertText', false, snippet);
                } else {
                    // Fallback untuk browser yang sangat lama
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    const text = textarea.value;
                    textarea.value = text.substring(0, start) + snippet + text.substring(end);
                    textarea.selectionStart = textarea.selectionEnd = start + snippet.length;

                    // Trigger event 'input' secara manual agar Alpine x-model terupdate
                    textarea.dispatchEvent(new Event('input', { bubbles: true }));
                }

                // 3. Sinkronisasi state Alpine (x-model seharusnya sudah otomatis terupdate
                // karena execCommand memicu event 'input', tapi ini untuk memastikan)
                this.content = textarea.value;
                this.isDirty = true;

                // 4. Tutup menu
                this.closeContextMenu();
            },

            saveContent() {
                if (!this.isDirty || !this.selectedFile) return;
                this.$wire.call('saveFile', this.content);
            }
        }
    }
</script>
@endpush
