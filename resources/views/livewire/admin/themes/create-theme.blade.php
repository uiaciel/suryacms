<div x-data="{
    activeTab: @entangle('activeTab'),
    copiedKey: null,

    copyText(text) {
        navigator.clipboard.writeText(text).then(() => {
            this.copiedKey = text;
            setTimeout(() => { this.copiedKey = null; }, 1800);
        });
    },

    tabs: [
        { id: 'upload', label: '1. Upload ZIP', icon: 'fa-upload' },
        { id: 'assets', label: '2. Assets', icon: 'fa-layer-group' },
        { id: 'variables', label: '3. Variables', icon: 'fa-code' },
        { id: 'generated', label: '4. Generated Files', icon: 'fa-folder-open' },
        { id: 'preview', label: '5. Preview', icon: 'fa-eye' },
        { id: 'export', label: '6. Export', icon: 'fa-box-archive' },
    ]
}" class="flex flex-col bg-slate-950" style="height: calc(100vh - 110px);">

    @if ($uploadStatus)
        <div x-data="{ show: true }" x-show="show" x-transition
            class="fixed top-20 right-6 z-[70] w-[min(420px,calc(100vw-2rem))]">
            <div
                class="flex items-start gap-3 rounded-xl border px-4 py-3 shadow-2xl {{ $uploadStatus === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700 shadow-emerald-900/10' : 'bg-rose-50 border-rose-200 text-rose-700 shadow-rose-900/10' }}">
                <i
                    class="fa-solid {{ $uploadStatus === 'success' ? 'fa-circle-check text-emerald-500' : 'fa-circle-xmark text-rose-500' }} mt-0.5"></i>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold">{{ $uploadStatus === 'success' ? 'Berhasil' : 'Gagal' }}</p>
                    <p class="text-xs mt-0.5 leading-relaxed">{{ $uploadMessage }}</p>
                </div>
                <button type="button" @click="show = false" class="opacity-60 hover:opacity-100 transition">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- HEADER TOOLBAR                                                 --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div
        class="flex-none flex items-center justify-between px-4 py-2 bg-slate-900 border-b border-slate-700 shadow-xl z-30">

        {{-- Left: Theme info --}}
        <div class="flex items-center gap-4 min-w-0">
            <div class="flex items-center gap-2">
                <div
                    class="w-7 h-7 bg-gradient-to-br from-blue-500 to-violet-600 rounded-lg flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-wand-magic-sparkles text-white text-xs"></i>
                </div>
                <div class="leading-none">
                    <p class="text-white text-xs font-bold tracking-tight">{{ $themeName }}</p>
                    <p class="text-slate-400 text-[10px]">Theme Developer Mode</p>
                </div>
            </div>

            {{-- Breadcrumb --}}
            <div class="hidden md:flex items-center gap-1.5 text-slate-500 text-xs">
                <span class="text-slate-600">admin/themes</span>
                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                <span class="text-blue-400 font-medium">{{ $selectedGeneratedFile ?: 'create' }}</span>
            </div>
        </div>

        {{-- Center: Status badge --}}
        <div class="hidden lg:flex items-center gap-2">
            @if ($zipExtracted)
                <span
                    class="flex items-center gap-1.5 px-3 py-1 bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 text-xs rounded-full">
                    <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                    ZIP Extracted — Ready
                </span>
            @else
                <span
                    class="flex items-center gap-1.5 px-3 py-1 bg-amber-500/15 border border-amber-500/30 text-amber-400 text-xs rounded-full">
                    <span class="w-1.5 h-1.5 bg-amber-400 rounded-full"></span>
                    Awaiting Upload
                </span>
            @endif
            <span class="text-slate-600 text-[10px] font-mono">v{{ $themeVersion }}</span>
        </div>

        {{-- Right: Action buttons --}}
        <div class="flex items-center gap-2">
            <button wire:click="saveCurrentEditor"
                class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 bg-slate-700 hover:bg-slate-600 border border-slate-600 hover:border-slate-500 text-slate-300 hover:text-white text-xs font-medium rounded-lg transition-all duration-150 group">
                <i class="fa-solid fa-floppy-disk text-xs group-hover:scale-110 transition-transform"></i>
                <span class="hidden md:inline">Save</span>
            </button>
            <button wire:click="generateTheme"
                class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 bg-slate-700 hover:bg-slate-600 border border-slate-600 hover:border-slate-500 text-slate-300 hover:text-white text-xs font-medium rounded-lg transition-all duration-150 group">
                <i class="fa-solid fa-bolt text-xs group-hover:scale-110 transition-transform"></i>
                <span class="hidden md:inline">Generate</span>
            </button>
            <button wire:click="switchTab('preview')" @click="activeTab = 'preview'"
                class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-medium rounded-lg transition-all duration-150 group shadow-lg shadow-blue-900/30">
                <i class="fa-solid fa-eye text-xs group-hover:scale-110 transition-transform"></i>
                <span class="hidden sm:inline">Preview</span>
            </button>
            <button wire:click="switchTab('export')" @click="activeTab = 'export'"
                class="flex items-center gap-1.5 px-3 py-1.5 bg-violet-600 hover:bg-violet-500 text-white text-xs font-medium rounded-lg transition-all duration-150 group shadow-lg shadow-violet-900/30">
                <i class="fa-solid fa-box-archive text-xs group-hover:scale-110 transition-transform"></i>
                <span class="hidden sm:inline">Export</span>
            </button>
            <a href="{{ route('admin.themes.index') }}"
                class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-400 hover:text-slate-200 text-xs rounded-lg transition-all duration-150">
                <i class="fa-solid fa-xmark text-xs"></i>
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- WORKSPACE AREA (main scrollable content)                       --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="flex-1 overflow-hidden">

        {{-- =================== TAB: UPLOAD ZIP ======================== --}}
        <div x-show="activeTab === 'upload'" x-cloak class="h-full overflow-y-auto p-4 bg-slate-950">
            <div class="grid grid-cols-12 gap-4 h-full">

                {{-- LEFT: Form Upload (3 cols) --}}
                <div class="col-span-12 lg:col-span-3 flex flex-col gap-3">

                    {{-- Upload Card --}}
                    <div class="bg-slate-900 border border-slate-700/60 rounded-xl shadow-2xl overflow-hidden">
                        <div class="flex items-center gap-2 px-4 py-3 bg-slate-800/80 border-b border-slate-700/60">
                            <div class="flex gap-1.5">
                                <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                                <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                            </div>
                            <span class="text-slate-400 text-xs font-mono ml-2">upload-theme.zip</span>
                        </div>
                        <div class="p-4 space-y-3">
                            <div>
                                <label
                                    class="block text-slate-400 text-[10px] font-semibold uppercase tracking-wider mb-1.5">Theme
                                    Name</label>
                                <input wire:model.live="themeName" type="text"
                                    class="w-full bg-slate-800 border border-slate-600 text-slate-200 text-xs rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500/30 placeholder-slate-500 transition"
                                    placeholder="My Theme Name" />
                            </div>
                            <div>
                                <label
                                    class="block text-slate-400 text-[10px] font-semibold uppercase tracking-wider mb-1.5">Author</label>
                                <input wire:model.live="author" type="text"
                                    class="w-full bg-slate-800 border border-slate-600 text-slate-200 text-xs rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500/30 placeholder-slate-500 transition"
                                    placeholder="Admin" />
                            </div>
                            <div>
                                <label
                                    class="block text-slate-400 text-[10px] font-semibold uppercase tracking-wider mb-1.5">Version</label>
                                <input wire:model.live="themeVersion" type="text"
                                    class="w-full bg-slate-800 border border-slate-600 text-slate-200 text-xs rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500/30 placeholder-slate-500 transition"
                                    placeholder="1.0.0" />
                            </div>

                            <div>
                                <label
                                    class="block text-slate-400 text-[10px] font-semibold uppercase tracking-wider mb-1.5">Upload
                                    ZIP</label>
                                <input wire:model="zipFile" type="file" accept=".zip"
                                    class="block w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500 file:cursor-pointer cursor-pointer bg-slate-800 border border-slate-600 rounded-lg p-2 focus:outline-none focus:border-blue-500" />
                                @error('zipFile')
                                    <p class="text-rose-400 text-[10px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Zip Info dummy --}}
                            @if ($zipExtracted)
                                <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-lg p-3 space-y-1">
                                    <p class="text-emerald-400 text-[10px] font-bold uppercase tracking-wider">✓ ZIP
                                        Info</p>
                                    <div class="space-y-1">
                                        <div class="flex justify-between text-[10px]">
                                            <span class="text-slate-400">Files</span>
                                            <span class="text-slate-200 font-mono">{{ $zipInfo['files'] ?? 0 }}</span>
                                        </div>
                                        <div class="flex justify-between text-[10px]">
                                            <span class="text-slate-400">Size</span>
                                            <span
                                                class="text-slate-200 font-mono">{{ number_format(($zipInfo['size'] ?? 0) / 1024 / 1024, 2) }}
                                                MB</span>
                                        </div>
                                        <div class="flex justify-between text-[10px]">
                                            <span class="text-slate-400">HTML files</span>
                                            <span class="text-slate-200 font-mono">{{ $zipInfo['html'] ?? 0 }}</span>
                                        </div>
                                        <div class="flex justify-between text-[10px]">
                                            <span class="text-slate-400">CSS files</span>
                                            <span class="text-slate-200 font-mono">{{ $zipInfo['css'] ?? 0 }}</span>
                                        </div>
                                        <div class="flex justify-between text-[10px]">
                                            <span class="text-slate-400">JS files</span>
                                            <span class="text-slate-200 font-mono">{{ $zipInfo['js'] ?? 0 }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Action Buttons --}}
                            <div class="flex gap-2 pt-1">
                                <button wire:click="uploadZip" wire:loading.attr="disabled"
                                    class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 bg-blue-600 hover:bg-blue-500 disabled:opacity-60 text-white text-xs font-semibold rounded-lg transition-all duration-150">
                                    <i class="fa-solid fa-upload text-xs"></i> Upload
                                </button>
                                <button wire:click="generateTheme" wire:loading.attr="disabled"
                                    class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 bg-slate-700 hover:bg-slate-600 disabled:opacity-60 text-slate-300 text-xs font-semibold rounded-lg transition-all duration-150">
                                    <i class="fa-solid fa-bolt text-xs"></i> Generate
                                </button>
                            </div>
                            <button wire:click="resetUpload"
                                class="w-full flex items-center justify-center gap-1.5 px-3 py-1.5 bg-transparent hover:bg-slate-700/60 border border-slate-700 text-slate-500 hover:text-slate-300 text-xs rounded-lg transition-all duration-150">
                                <i class="fa-solid fa-rotate-left text-xs"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Preview Editor (9 cols) --}}
                <div class="col-span-12 lg:col-span-9 flex flex-col">
                    <div class="bg-slate-900 border border-slate-700/60 rounded-xl shadow-2xl overflow-hidden flex flex-col h-full"
                        style="min-height: 500px;">
                        {{-- Editor tab bar --}}
                        <div class="flex items-center bg-slate-800/80 border-b border-slate-700/60 px-2 gap-0">
                            <div class="flex gap-1.5 px-2 py-3">
                                <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                                <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                            </div>
                            <button
                                class="flex items-center gap-2 px-4 py-2.5 bg-slate-900 border-r border-slate-700/60 text-slate-300 text-xs font-mono transition">
                                <i class="fa-solid fa-file-code text-blue-400 text-[10px]"></i>
                                original-index.html
                                <i class="fa-solid fa-circle text-blue-500 text-[6px]"></i>
                            </button>
                            <div class="ml-auto flex items-center gap-2 px-3">
                                <span class="text-slate-600 text-[10px] font-mono">HTML • UTF-8</span>
                            </div>
                        </div>

                        {{-- Editable index.html --}}
                        <div class="flex-1 overflow-hidden flex">
                            <textarea wire:model.defer="htmlRaw"
                                class="w-full min-h-[520px] bg-slate-950 text-slate-200 text-xs font-mono leading-5 p-4 border-0 resize-none focus:outline-none focus:ring-0"
                                spellcheck="false" placeholder="Upload ZIP untuk menampilkan index.html di sini..."></textarea>
                        </div>

                        {{-- Status bar --}}
                        <div
                            class="flex items-center justify-between px-4 py-1 bg-blue-600/90 text-blue-100 text-[10px] font-mono">
                            <div class="flex items-center gap-4">
                                <span><i class="fa-solid fa-code-branch mr-1"></i>main</span>
                                <span><i class="fa-solid fa-triangle-exclamation mr-1 text-yellow-300"></i>0 errors · 2
                                    warnings</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <span>Ln 1, Col 1</span>
                                <span>HTML</span>
                                <span>UTF-8</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- =================== TAB: ASSETS ======================== --}}
        <div x-show="activeTab === 'assets'" x-cloak class="h-full overflow-y-auto p-4 bg-slate-950">
            @if (!empty($assetSnippets))
                <div class="grid grid-cols-12 gap-4 mb-4">
                    @foreach ($assetSnippets as $group => $snippets)
                        <div
                            class="col-span-12 md:col-span-6 bg-slate-900 border border-slate-700/60 rounded-xl overflow-hidden">
                            <div
                                class="flex items-center justify-between px-4 py-2.5 bg-slate-800/80 border-b border-slate-700/60">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-code text-blue-400 text-sm"></i>
                                    <span
                                        class="text-slate-300 text-xs font-semibold">{{ strtoupper(str_replace('_', ' ', $group)) }}</span>
                                    <span
                                        class="text-[10px] bg-slate-700 text-slate-400 px-1.5 py-0.5 rounded font-mono">{{ count(array_filter($snippets)) }}</span>
                                </div>
                                <button x-on:click="copyText(@js(implode("\n\n", array_filter($snippets))))"
                                    class="flex items-center gap-1 px-2 py-1 bg-slate-700 hover:bg-slate-600 text-slate-400 hover:text-white text-[10px] rounded transition">
                                    <i class="fa-regular fa-copy"></i> Copy All
                                </button>
                            </div>
                            <div class="p-3 font-mono text-xs leading-5 overflow-auto" style="max-height:220px;">
                                @forelse(array_filter($snippets) as $snippet)
                                    <div class="mb-3 last:mb-0 group">
                                        <button x-on:click="copyText(@js($snippet))"
                                            class="mb-1 text-[10px] text-slate-500 hover:text-blue-400 transition">
                                            <i class="fa-regular fa-copy"></i> Copy snippet
                                        </button>
                                        <pre class="text-slate-300 whitespace-pre-wrap break-words">{{ $snippet }}</pre>
                                    </div>
                                @empty
                                    <p class="text-slate-600 text-xs">Tidak ada snippet terdeteksi.</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>

        {{-- =================== TAB: VARIABLES ======================== --}}
        <div x-show="activeTab === 'variables'" x-cloak class="h-full overflow-hidden flex bg-slate-950">

            {{-- Sidebar kategori --}}
            <div class="flex-none w-44 bg-slate-900 border-r border-slate-700/60 flex flex-col overflow-y-auto">
                <div class="px-3 py-3 border-b border-slate-700/60">
                    <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest">Categories</p>
                </div>
                <nav class="p-2 space-y-0.5 flex-1">
                    @foreach (array_keys($variableGroups) as $group)
                        <button wire:click="selectVariableGroup('{{ $group }}')"
                            class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-all text-left
                        {{ $selectedVariableGroup === $group ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800' }}">
                            @php
                                $groupIcons = [
                                    'page' => 'fa-file-lines',
                                    'post' => 'fa-newspaper',
                                    'category' => 'fa-tag',
                                    'gallery' => 'fa-images',
                                    'menu' => 'fa-bars',
                                    'site' => 'fa-globe',
                                    'flashnews' => 'fa-bolt',
                                    'contact' => 'fa-envelope',
                                ];
                            @endphp
                            <i class="fa-solid {{ $groupIcons[$group] ?? 'fa-code' }} text-[11px]"></i>
                            {{ ucfirst($group) }}
                        </button>
                    @endforeach
                </nav>
            </div>

            {{-- Variable list --}}
            <div class="flex-1 overflow-y-auto p-4">
                <div class="mb-3 flex items-center gap-2">
                    <h3 class="text-slate-300 text-sm font-semibold capitalize">{{ $selectedVariableGroup }} Variables
                    </h3>
                    <span
                        class="text-[10px] bg-slate-800 border border-slate-700 text-slate-400 px-2 py-0.5 rounded-full font-mono">
                        {{ count($variableGroups[$selectedVariableGroup] ?? []) }} vars
                    </span>
                </div>
                <div class="space-y-2">
                    @foreach ($variableGroups[$selectedVariableGroup] ?? [] as $variable => $description)
                        <div x-data="{}"
                            class="flex items-center justify-between gap-3 bg-slate-900 border border-slate-700/60 hover:border-blue-500/40 rounded-xl px-4 py-3 transition-all duration-150 group">
                            <div class="flex-1 min-w-0">
                                <code class="text-blue-300 text-xs font-mono">{{ $variable }}</code>
                                <p class="text-slate-500 text-[10px] mt-0.5 truncate">{{ $description }}</p>
                            </div>
                            <button x-on:click="copyText('{{ addslashes($variable) }}')"
                                class="flex-none flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-800 hover:bg-blue-600 border border-slate-700 hover:border-blue-500 text-slate-400 hover:text-white text-[10px] rounded-lg transition-all duration-150 opacity-0 group-hover:opacity-100"
                                :class="copiedKey === '{{ addslashes($variable) }}' ?
                                    'bg-emerald-600 border-emerald-500 text-white opacity-100' : ''">
                                <i class="fa-regular"
                                    :class="copiedKey === '{{ addslashes($variable) }}' ? 'fa-circle-check' : 'fa-copy'"></i>
                                <span
                                    x-text="copiedKey === '{{ addslashes($variable) }}' ? 'Copied!' : 'Copy'"></span>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- =================== TAB: GENERATED FILES ======================== --}}
        <div x-show="activeTab === 'generated'" x-cloak class="h-full overflow-hidden flex bg-slate-950">

            {{-- File Explorer (sidebar) --}}
            <div class="flex-none w-52 bg-slate-900 border-r border-slate-700/60 flex flex-col">
                <div class="flex items-center justify-between px-3 py-2.5 border-b border-slate-700/60">
                    <span class="text-slate-500 text-[10px] font-bold uppercase tracking-widest">Explorer</span>
                    <div class="flex items-center gap-1">
                        <button class="text-slate-500 hover:text-slate-300 transition p-1" title="New File">
                            <i class="fa-solid fa-plus text-[10px]"></i>
                        </button>
                        <button wire:click="loadGeneratedFiles"
                            class="text-slate-500 hover:text-slate-300 transition p-1" title="Refresh">
                            <i class="fa-solid fa-rotate text-[10px]"></i>
                        </button>
                    </div>
                </div>

                {{-- Theme folder tree --}}
                <div class="flex-1 overflow-y-auto p-2">
                    <div class="mb-1">
                        <button
                            class="w-full flex items-center gap-1.5 px-2 py-1 text-slate-400 text-xs rounded hover:bg-slate-800 transition">
                            <i class="fa-solid fa-chevron-down text-[9px]"></i>
                            <i class="fa-solid fa-folder-open text-blue-400 text-[11px]"></i>
                            <span class="font-semibold">{{ $themeName }}</span>
                        </button>
                        <div class="ml-3 space-y-0.5 mt-0.5">
                            @foreach ($fileTree as $file)
                                <button wire:click="selectGeneratedFile('{{ $file['name'] }}')"
                                    class="w-full flex items-center gap-1.5 px-2 py-1.5 rounded text-xs transition-all text-left
                                {{ $selectedGeneratedFile === $file['name']
                                    ? 'bg-blue-600/25 text-blue-300 border-l-2 border-blue-500'
                                    : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800' }}">
                                    @if ($file['type'] === 'dir')
                                        <i class="fa-solid fa-folder text-yellow-400/80 text-[11px]"></i>
                                    @else
                                        @php
                                            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                                            $iconColor = match ($ext) {
                                                'php', 'blade' => 'text-violet-400',
                                                'css' => 'text-blue-400',
                                                'js' => 'text-yellow-400',
                                                default => 'text-slate-400',
                                            };
                                        @endphp
                                        <i class="fa-solid {{ $file['icon'] }} {{ $iconColor }} text-[11px]"></i>
                                    @endif
                                    <span class="truncate">{{ $file['name'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Editor Area --}}
            <div class="flex-1 overflow-hidden flex flex-col">

                {{-- Open file tabs (top of editor) --}}
                <div class="flex-none flex items-center bg-slate-900 border-b border-slate-700/60 overflow-x-auto">
                    @foreach ($openTabs as $tab)
                        @php
                            $tabExt = pathinfo($tab, PATHINFO_EXTENSION);
                            $tabColor = match ($tabExt) {
                                'php' => 'text-violet-400',
                                'css' => 'text-blue-400',
                                'js' => 'text-yellow-400',
                                default => 'text-slate-400',
                            };
                        @endphp
                        <button wire:click="selectBottomTab('{{ $tab }}')"
                            class="flex items-center gap-2 px-4 py-2.5 text-xs font-mono transition-all border-r border-slate-700/60 whitespace-nowrap
                        {{ $activeBottomTab === $tab
                            ? 'bg-slate-950 text-slate-200 border-t-2 border-t-blue-500'
                            : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800/50' }}">
                            <i class="fa-solid fa-file-code {{ $tabColor }} text-[10px]"></i>
                            {{ $tab }}
                            @if ($activeBottomTab === $tab)
                                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                            @endif
                        </button>
                    @endforeach
                </div>

                {{-- Editor content --}}
                <div class="flex-1 overflow-hidden flex">
                    {{-- Line numbers --}}
                    <div
                        class="flex-none w-12 bg-slate-950 border-r border-slate-800 py-4 text-right pr-3 select-none overflow-hidden">
                        @foreach (explode("\n", $fileContents[$selectedGeneratedFile] ?? '// Empty file') as $i => $line)
                            <div class="text-slate-700 text-[10px] font-mono leading-5">{{ $i + 1 }}</div>
                        @endforeach
                    </div>
                    {{-- Code area --}}
                    <div class="flex-1 overflow-hidden bg-slate-950">
                        <textarea wire:model.defer="editingFileContent"
                            class="w-full h-full bg-slate-950 text-slate-300 text-xs font-mono leading-5 p-4 border-0 resize-none focus:outline-none focus:ring-0"
                            spellcheck="false" placeholder="// Empty file - select a file from the explorer"></textarea>
                    </div>
                </div>

                {{-- Status bar --}}
                <div
                    class="flex items-center justify-between px-4 py-1 bg-blue-600/80 text-blue-100 text-[10px] font-mono flex-none">
                    <div class="flex items-center gap-4">
                        <span><i class="fa-solid fa-code-branch mr-1"></i>theme/dev</span>
                        <span>{{ $selectedGeneratedFile }}</span>
                        <button wire:click="saveGeneratedFile"
                            class="px-2 py-0.5 bg-blue-700/60 hover:bg-blue-700 rounded text-blue-50">
                            <i class="fa-solid fa-floppy-disk mr-1"></i>Save
                        </button>
                    </div>
                    <div class="flex items-center gap-4">
                        <span>@php
                            $linesCount = count(explode("\n", $fileContents[$selectedGeneratedFile] ?? ''));
                            echo $linesCount . ' lines';
                        @endphp</span>
                        @php
                            $ext = pathinfo($selectedGeneratedFile, PATHINFO_EXTENSION);
                            $lang = match ($ext) {
                                'php' => 'PHP/Blade',
                                'css' => 'CSS',
                                'js' => 'JavaScript',
                                default => 'Text',
                            };
                            echo $lang;
                        @endphp
                        <span>UTF-8</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- =================== TAB: PREVIEW ======================== --}}
        <div x-show="activeTab === 'preview'" x-cloak class="h-full flex flex-col bg-slate-950 overflow-hidden">

            {{-- Preview toolbar --}}
            <div class="flex-none flex items-center gap-3 px-4 py-2.5 bg-slate-900 border-b border-slate-700/60">
                <div class="flex items-center bg-slate-800 border border-slate-700 rounded-lg p-0.5 gap-0.5">
                    <button x-data="{}" @click
                        class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white text-xs rounded-md font-medium transition">
                        <i class="fa-solid fa-desktop text-xs"></i> Desktop
                    </button>
                    <button
                        class="flex items-center gap-1.5 px-3 py-1.5 text-slate-400 hover:text-slate-200 text-xs rounded-md transition hover:bg-slate-700">
                        <i class="fa-solid fa-tablet-screen-button text-xs"></i> Tablet
                    </button>
                    <button
                        class="flex items-center gap-1.5 px-3 py-1.5 text-slate-400 hover:text-slate-200 text-xs rounded-md transition hover:bg-slate-700">
                        <i class="fa-solid fa-mobile-screen text-xs"></i> Mobile
                    </button>
                </div>

                <div
                    class="flex-1 flex items-center gap-2 bg-slate-800 border border-slate-700 rounded-lg px-3 py-1.5">
                    <i class="fa-solid fa-lock text-slate-500 text-xs"></i>
                    <span
                        class="text-slate-400 text-xs font-mono">http://localhost/themes/preview/{{ Str::slug($themeName) }}</span>
                </div>

                <button
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-400 hover:text-white text-xs rounded-lg transition">
                    <i class="fa-solid fa-rotate-right text-xs"></i> Refresh
                </button>
                <button
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-400 hover:text-white text-xs rounded-lg transition">
                    <i class="fa-solid fa-up-right-from-square text-xs"></i>
                </button>
            </div>

            {{-- Preview frame --}}
            <div class="flex-1 overflow-auto flex items-start justify-center p-6 bg-slate-950">
                <div class="w-full max-w-6xl bg-slate-900 border border-slate-700 rounded-xl overflow-hidden shadow-2xl"
                    style="min-height: 600px;">
                    {{-- Browser chrome --}}
                    <div class="flex items-center gap-2 px-4 py-3 bg-slate-800 border-b border-slate-700">
                        <div class="flex gap-1.5">
                            <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                            <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                            <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                        </div>
                        <div class="flex-1 bg-slate-700 rounded-md px-3 py-1.5 mx-2">
                            <span
                                class="text-slate-400 text-xs font-mono">{{ Str::slug($themeName) }}.preview.local</span>
                        </div>
                    </div>
                    {{-- Placeholder preview --}}
                    <div class="flex flex-col items-center justify-center" style="min-height: 560px;">
                        <div class="text-center space-y-3">
                            <div
                                class="w-16 h-16 bg-slate-800 border border-slate-700 rounded-2xl flex items-center justify-center mx-auto">
                                <i class="fa-solid fa-eye text-slate-500 text-xl"></i>
                            </div>
                            <p class="text-slate-400 text-sm font-medium">Preview belum tersedia</p>
                            <p class="text-slate-600 text-xs max-w-xs">Generate theme terlebih dahulu dari tab <span
                                    class="text-blue-400">Generated Files</span>, lalu klik <span
                                    class="text-blue-400">Preview</span> untuk melihat hasilnya.</p>
                            <button wire:click="generateTheme"
                                class="mt-2 flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs rounded-lg transition mx-auto">
                                <i class="fa-solid fa-bolt"></i> Generate & Preview
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- =================== TAB: EXPORT ======================== --}}
        <div x-show="activeTab === 'export'" x-cloak class="h-full overflow-y-auto p-4 bg-slate-950">
            <div class="max-w-3xl mx-auto space-y-4">

                {{-- Validation Header --}}
                <div class="bg-slate-900 border border-slate-700/60 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-700/60 bg-slate-800/60">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 bg-violet-600/20 border border-violet-500/30 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-shield-check text-violet-400 text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-slate-200 text-sm font-semibold">Validation Results</h3>
                                <p class="text-slate-500 text-[10px] mt-0.5">Pemeriksaan file sebelum export</p>
                            </div>
                            <div class="ml-auto flex items-center gap-2">
                                <span
                                    class="px-2.5 py-1 bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 text-[10px] rounded-full font-semibold">
                                    7 OK
                                </span>
                                <span
                                    class="px-2.5 py-1 bg-amber-500/15 border border-amber-500/30 text-amber-400 text-[10px] rounded-full font-semibold">
                                    1 Warning
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="divide-y divide-slate-800">
                        @foreach ($exportValidation as $item)
                            <div class="flex items-center gap-4 px-5 py-3">
                                @if ($item['status'] === 'ok')
                                    <div
                                        class="flex-none w-5 h-5 bg-emerald-500/20 border border-emerald-500/30 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-check text-emerald-400 text-[9px]"></i>
                                    </div>
                                @elseif($item['status'] === 'warning')
                                    <div
                                        class="flex-none w-5 h-5 bg-amber-500/20 border border-amber-500/30 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-exclamation text-amber-400 text-[9px]"></i>
                                    </div>
                                @else
                                    <div
                                        class="flex-none w-5 h-5 bg-red-500/20 border border-red-500/30 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-xmark text-red-400 text-[9px]"></i>
                                    </div>
                                @endif

                                <div class="flex-1 min-w-0">
                                    <p class="text-slate-300 text-xs font-mono">{{ $item['file'] }}</p>
                                    @if ($item['message'])
                                        <p class="text-amber-400/80 text-[10px] mt-0.5">⚠ {{ $item['message'] }}</p>
                                    @endif
                                </div>

                                <span
                                    class="flex-none text-[10px] font-semibold px-2 py-0.5 rounded-full
                            {{ $item['status'] === 'ok' ? 'text-emerald-400 bg-emerald-500/10' : '' }}
                            {{ $item['status'] === 'warning' ? 'text-amber-400 bg-amber-500/10' : '' }}
                            {{ $item['status'] === 'error' ? 'text-red-400 bg-red-500/10' : '' }}
                        ">
                                    {{ strtoupper($item['status']) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Export Info --}}
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-slate-900 border border-slate-700/60 rounded-xl p-4 text-center">
                        <p class="text-slate-500 text-[10px] font-semibold uppercase tracking-wider mb-1">Theme Name
                        </p>
                        <p class="text-slate-200 text-sm font-bold font-mono">{{ $themeName }}</p>
                    </div>
                    <div class="bg-slate-900 border border-slate-700/60 rounded-xl p-4 text-center">
                        <p class="text-slate-500 text-[10px] font-semibold uppercase tracking-wider mb-1">Author</p>
                        <p class="text-slate-200 text-sm font-bold font-mono">{{ $author }}</p>
                    </div>
                    <div class="bg-slate-900 border border-slate-700/60 rounded-xl p-4 text-center">
                        <p class="text-slate-500 text-[10px] font-semibold uppercase tracking-wider mb-1">Version</p>
                        <p class="text-slate-200 text-sm font-bold font-mono">{{ $themeVersion }}</p>
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="flex flex-col sm:flex-row gap-3">
                    <button wire:click="generateTheme"
                        class="flex-1 flex items-center justify-center gap-2 px-6 py-3.5 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-xl transition-all duration-150 shadow-lg shadow-violet-900/30 group">
                        <i class="fa-solid fa-bolt group-hover:scale-110 transition-transform"></i>
                        Generate Theme Package
                    </button>
                    <button
                        class="flex-1 flex items-center justify-center gap-2 px-6 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold rounded-xl transition-all duration-150 shadow-lg shadow-emerald-900/30 group">
                        <i class="fa-solid fa-file-zipper group-hover:scale-110 transition-transform"></i>
                        Download ZIP
                    </button>
                </div>

                {{-- Warning note --}}
                <div class="flex items-start gap-3 bg-amber-500/10 border border-amber-500/30 rounded-xl p-4">
                    <i class="fa-solid fa-triangle-exclamation text-amber-400 mt-0.5"></i>
                    <div>
                        <p class="text-amber-300 text-xs font-semibold">Perhatian</p>
                        <p class="text-amber-500/80 text-[10px] mt-0.5">Pastikan semua file sudah divalidasi sebelum
                            melakukan export. File dengan status warning masih bisa di-export namun memerlukan perbaikan
                            manual.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- BOTTOM TAB BAR (Main Navigation Tabs)                          --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="flex-none flex items-stretch bg-slate-900 border-t border-slate-700/80 overflow-x-auto z-20"
        style="min-height: 38px;">
        <template x-for="tab in tabs" :key="tab.id">
            <button @click="activeTab = tab.id; $wire.switchTab(tab.id)"
                class="flex items-center gap-2 px-5 text-xs font-medium border-r border-slate-700/60 transition-all duration-150 whitespace-nowrap"
                :class="activeTab === tab.id ?
                    'bg-slate-800 text-white border-t-2 border-t-blue-500' :
                    'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60'">
                <i class="fa-solid text-[10px]" :class="tab.icon"></i>
                <span x-text="tab.label"></span>
            </button>
        </template>

        {{-- Right side: current file indicator --}}
        <div
            class="ml-auto flex items-center px-4 gap-3 text-[10px] text-slate-600 font-mono border-l border-slate-700/60">
            <span class="hidden md:inline">{{ $themeName }}</span>
            <span class="text-slate-700">|</span>
            <span class="text-slate-500">{{ $selectedGeneratedFile }}</span>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- BOTTOM IDE FILE TABS (Like VSCode open files bar)              --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="flex-none flex items-center bg-slate-950 border-t border-slate-800 overflow-x-auto z-20"
        style="min-height: 30px;">
        <div class="flex items-center h-full overflow-x-auto">
            @foreach ($openTabs as $tab)
                @php
                    $tabExt = pathinfo($tab, PATHINFO_EXTENSION);
                    $tabIconColor = match ($tabExt) {
                        'php' => 'text-violet-400',
                        'css' => 'text-blue-400',
                        'js' => 'text-yellow-400',
                        default => 'text-slate-400',
                    };
                @endphp
                <div class="flex items-center gap-0 border-r border-slate-800 group">
                    <button wire:click="selectBottomTab('{{ $tab }}')"
                        class="flex items-center gap-1.5 pl-3 pr-1 py-1.5 text-[10px] font-mono transition-all whitespace-nowrap
                    {{ $activeBottomTab === $tab
                        ? 'text-slate-200 bg-slate-900 border-t border-t-blue-500'
                        : 'text-slate-600 hover:text-slate-400 hover:bg-slate-900/50' }}">
                        <i class="fa-solid fa-file-code {{ $tabIconColor }} text-[9px]"></i>
                        {{ $tab }}
                    </button>
                    <button wire:click="closeBottomTab('{{ $tab }}')"
                        class="px-1.5 py-1.5 text-[9px] transition text-slate-700 hover:text-slate-400 opacity-0 group-hover:opacity-100">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endforeach
        </div>

        {{-- Status bar right --}}
        <div
            class="ml-auto flex items-center gap-3 px-3 text-[10px] text-slate-700 font-mono border-l border-slate-800 whitespace-nowrap">
            <span class="text-emerald-600"><i class="fa-solid fa-circle text-[6px] mr-1"></i>Ready</span>
            <span>PHP 8.2</span>
            <span>Laravel 12</span>
            <span>Livewire 3</span>
        </div>
    </div>

    <style>
        /* Custom scrollbar for the dark theme */
        .overflow-x-auto::-webkit-scrollbar,
        .overflow-y-auto::-webkit-scrollbar,
        .overflow-auto::-webkit-scrollbar {
            height: 4px;
            width: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-track,
        .overflow-y-auto::-webkit-scrollbar-track,
        .overflow-auto::-webkit-scrollbar-track {
            background: #0f172a;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb,
        .overflow-y-auto::-webkit-scrollbar-thumb,
        .overflow-auto::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 2px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover,
        .overflow-y-auto::-webkit-scrollbar-thumb:hover,
        .overflow-auto::-webkit-scrollbar-thumb:hover {
            background: #4a6cf7;
        }

        /* Override the main layout padding for this full-screen IDE */
        main.flex-1 {
            padding: 0 !important;
            background: #020817 !important;
        }
    </style>
