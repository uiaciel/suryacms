{{-- resources/views/livewire/theme-builder.blade.php --}}

<div
    x-data="themeBuilder({
        activeStep: @entangle('activeStep'),
        previewMode: @entangle('previewMode'),
    })"
    class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950"
>
    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- TOAST NOTIFICATION                                          --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div
        x-data="{ show: false, type: 'success', message: '' }"
        x-on:toast.window="
            type = $event.detail.type;
            message = $event.detail.message;
            show = true;
            setTimeout(() => show = false, 3500);
        "
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed top-6 right-6 z-[100] w-[min(380px,calc(100vw-2rem))]"
        x-cloak
    >
        <div class="flex items-start gap-3 rounded-2xl border backdrop-blur-xl px-4 py-3.5 shadow-2xl"
            :class="{
                'bg-emerald-500/10 border-emerald-500/30 shadow-emerald-900/20': type === 'success',
                'bg-rose-500/10 border-rose-500/30 shadow-rose-900/20': type === 'error',
                'bg-amber-500/10 border-amber-500/30 shadow-amber-900/20': type === 'warning',
                'bg-blue-500/10 border-blue-500/30 shadow-blue-900/20': type === 'info',
            }">
            <div class="flex-none w-8 h-8 rounded-full flex items-center justify-center"
                :class="{
                    'bg-emerald-500 text-emerald-400': type === 'success',
                    'bg-rose-500 text-rose-400': type === 'error',
                    'bg-amber-500 text-amber-400': type === 'warning',
                    'bg-blue-500 text-blue-400': type === 'info',
                }">
                <i class="fa-solid text-sm"
                    :class="{
                        'fa-circle-check': type === 'success',
                        'fa-circle-xmark': type === 'error',
                        'fa-triangle-exclamation': type === 'warning',
                        'fa-circle-info': type === 'info',
                    }"></i>
            </div>
            <div class="flex-1 min-w-0 pt-1">
                <p class="text-sm font-semibold"
                    :class="{
                        'text-emerald-300': type === 'success',
                        'text-rose-300': type === 'error',
                        'text-amber-300': type === 'warning',
                        'text-blue-300': type === 'info',
                    }"
                    x-text="type === 'success' ? 'Berhasil' :
                            type === 'error' ? 'Gagal' :
                            type === 'warning' ? 'Peringatan' : 'Info'"></p>
                <p class="text-xs text-slate-300 mt-0.5 leading-relaxed" x-text="message"></p>
            </div>
            <button @click="show = false" class="flex-none text-slate-500 hover:text-slate-300 transition">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- HEADER                                                      --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <header class="sticky top-0 z-40 backdrop-blur-xl bg-slate-950/80 border-b border-slate-800/60">
        <div class="max-w-7xl mx-auto px-6 py-3.5 flex items-center justify-between gap-4">
            {{-- Brand --}}
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-gradient-to-br from-blue-500 via-violet-500 to-fuchsia-500 rounded-xl flex items-center justify-center shadow-lg shadow-violet-900/30">
                    <i class="fa-solid fa-wand-magic-sparkles text-white text-sm"></i>
                </div>
                <div class="leading-tight">
                    <h1 class="text-white text-sm font-bold tracking-tight">Theme Builder</h1>
                    <p class="text-slate-500 text-[10px]">{{ $themeName ?: 'Untitled Theme' }}</p>
                </div>
            </div>

            {{-- Center: Status --}}
            <div class="hidden md:flex items-center gap-2">
                @if ($themeGenerated)
                    <span class="flex items-center gap-1.5 px-3 py-1 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-[10px] rounded-full font-medium">
                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                        Generated
                    </span>
                @elseif ($zipExtracted)
                    <span class="flex items-center gap-1.5 px-3 py-1 bg-blue-500/10 border border-blue-500/30 text-blue-400 text-[10px] rounded-full font-medium">
                        <span class="w-1.5 h-1.5 bg-blue-400 rounded-full"></span>
                        ZIP Ready
                    </span>
                @else
                    <span class="flex items-center gap-1.5 px-3 py-1 bg-slate-500/10 border border-slate-500/30 text-slate-400 text-[10px] rounded-full font-medium">
                        <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                        Draft
                    </span>
                @endif
            </div>

            {{-- Right: Actions --}}
            <div class="flex items-center gap-2">
                {{-- Save Button (only in preview step) --}}
                <button
                    wire:click="saveCurrentEditor"
                    wire:loading.attr="disabled"
                    x-show="activeStep === 'preview'"
                    x-cloak
                    class="flex items-center gap-1.5 px-3 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 hover:border-slate-600 text-slate-300 hover:text-white text-xs font-medium rounded-xl transition-all disabled:opacity-50"
                >
                    <i class="fa-solid fa-floppy-disk text-xs" wire:loading.remove wire:target="saveCurrentEditor"></i>
                    <i class="fa-solid fa-spinner fa-spin text-xs" wire:loading wire:target="saveCurrentEditor"></i>
                    <span>Save</span>
                </button>

                {{-- Generate Button --}}
                <button wire:click="resetUpload"
                                class="w-full flex items-center justify-center gap-1.5 px-3 py-1.5 bg-transparent hover:bg-slate-700/60 border border-slate-700 text-slate-500 hover:text-slate-300 text-xs rounded-lg transition-all duration-150">
                    <i class="fa-solid fa-rotate-left text-xs"></i> Reset
                </button>

                <button
                    wire:click="generateTheme"
                    wire:loading.attr="disabled"
                    @click="activeStep = 'generate'"
                    class="flex items-center gap-1.5 px-3 py-2 bg-gradient-to-r from-blue-600 to-violet-600 hover:from-blue-500 hover:to-violet-500 disabled:opacity-50 text-white text-xs font-semibold rounded-xl transition-all shadow-lg shadow-violet-900/30"
                >
                    <i class="fa-solid fa-bolt text-xs" wire:loading.remove wire:target="generateTheme"></i>
                    <i class="fa-solid fa-spinner fa-spin text-xs" wire:loading wire:target="generateTheme"></i>
                    <span>Generate</span>
                </button>



                {{-- Close --}}
                <a href="{{ route('admin.themes.index') }}"
                    class="flex items-center justify-center w-8 h-8 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-400 hover:text-white rounded-xl transition-all">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </a>
            </div>
        </div>
    </header>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- STEP INDICATOR                                              --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="flex items-center justify-center gap-4">
            {{-- Step 1: Upload --}}
            <button
                @click="activeStep = 'upload'"
                class="flex items-center gap-3 group"
            >
                <div class="flex-none w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all"
                    :class="{
                        'bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-900/40': activeStep === 'upload',
                        'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30': activeStep !== 'upload' && {{ $zipExtracted ? 'true' : 'false' }},
                        'bg-slate-800 text-slate-500 border border-slate-700': activeStep !== 'upload' && !{{ $zipExtracted ? 'true' : 'false' }},
                    }">
                    <i x-show="activeStep !== 'upload' && {{ $zipExtracted ? 'true' : 'false' }}" class="fa-solid fa-check text-xs"></i>
                    <span x-show="activeStep === 'upload' || !{{ $zipExtracted ? 'true' : 'false' }}">1</span>
                </div>
                <div class="text-left hidden sm:block">
                    <p class="text-xs font-semibold transition-colors"
                        :class="activeStep === 'upload' ? 'text-white' : 'text-slate-400'">Upload ZIP</p>
                    <p class="text-[10px] text-slate-600">Upload file tema</p>
                </div>
            </button>

            {{-- Connector --}}
            <div class="w-12 h-px transition-colors"
                :class="{{ $zipExtracted ? 'true' : 'false' }} ? 'bg-emerald-500/40' : 'bg-slate-800'"></div>

            {{-- Step 2: Generate --}}
            <button
                @click="activeStep = 'generate'"
                :disabled="!{{ $zipExtracted ? 'true' : 'false' }}"
                class="flex items-center gap-3 group disabled:opacity-40 disabled:cursor-not-allowed"
            >
                <div class="flex-none w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all"
                    :class="{
                        'bg-gradient-to-br from-violet-500 to-violet-600 text-white shadow-lg shadow-violet-900/40': activeStep === 'generate',
                        'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30': activeStep !== 'generate' && {{ $themeGenerated ? 'true' : 'false' }},
                        'bg-slate-800 text-slate-500 border border-slate-700': activeStep !== 'generate' && !{{ $themeGenerated ? 'true' : 'false' }},
                    }">
                    <i x-show="activeStep !== 'generate' && {{ $themeGenerated ? 'true' : 'false' }}" class="fa-solid fa-check text-xs"></i>
                    <span x-show="activeStep === 'generate' || !{{ $themeGenerated ? 'true' : 'false' }}">2</span>
                </div>
                <div class="text-left hidden sm:block">
                    <p class="text-xs font-semibold transition-colors"
                        :class="activeStep === 'generate' ? 'text-white' : 'text-slate-400'">Generate</p>
                    <p class="text-[10px] text-slate-600">Buat file Blade</p>
                </div>
            </button>

            {{-- Connector --}}
            <div class="w-12 h-px transition-colors"
                :class="{{ $themeGenerated ? 'true' : 'false' }} ? 'bg-emerald-500/40' : 'bg-slate-800'"></div>

            {{-- Step 3: Preview --}}
            <button
                @click="activeStep = 'preview'"
                :disabled="!{{ $themeGenerated ? 'true' : 'false' }}"
                class="flex items-center gap-3 group disabled:opacity-40 disabled:cursor-not-allowed"
            >
                <div class="flex-none w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all"
                    :class="{
                        'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-lg shadow-emerald-900/40': activeStep === 'preview',
                        'bg-slate-800 text-slate-500 border border-slate-700': activeStep !== 'preview',
                    }">
                    3
                </div>
                <div class="text-left hidden sm:block">
                    <p class="text-xs font-semibold transition-colors"
                        :class="activeStep === 'preview' ? 'text-white' : 'text-slate-400'">Preview</p>
                    <p class="text-[10px] text-slate-600">Lihat & edit hasil</p>
                </div>
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- MAIN CONTENT                                                --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <main class="max-w-7xl mx-auto px-6 pb-12">

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- STEP 1: UPLOAD                                          --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <div x-show="activeStep === 'upload'" x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- Upload Zone --}}
                <div class="lg:col-span-3">
                    <div class="bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-800/60">
                            <h2 class="text-white text-sm font-semibold flex items-center gap-2">
                                <i class="fa-solid fa-cloud-arrow-up text-blue-400"></i>
                                Upload Theme Package
                            </h2>
                            <p class="text-slate-500 text-xs mt-1">Upload file ZIP yang berisi HTML, CSS, dan JS tema Anda</p>
                        </div>

                        <div class="p-5 space-y-5">
                            {{-- Drop Zone --}}
                            <label class="block cursor-pointer group">
                                <div class="relative border-2 border-dashed border-slate-700 group-hover:border-blue-500/60 rounded-2xl p-10 transition-all duration-300 bg-slate-950/40 group-hover:bg-blue-500/5">
                                    <input wire:model="zipFile" type="file" accept=".zip" class="hidden" />

                                    <div class="flex flex-col items-center text-center">
                                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500/20 to-violet-500/20 border border-blue-500/30 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                            <i class="fa-solid fa-file-zipper text-blue-400 text-2xl"></i>
                                        </div>
                                        <p class="text-slate-300 text-sm font-medium mb-1">
                                            Klik untuk upload atau drag & drop
                                        </p>
                                        <p class="text-slate-500 text-xs">File ZIP maksimal 50MB</p>

                                        {{-- Selected file --}}
                                        @if ($zipFile)
                                            <div class="mt-4 flex items-center gap-2 px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/30 rounded-full">
                                                <i class="fa-solid fa-check-circle text-emerald-400 text-xs"></i>
                                                <span class="text-emerald-300 text-xs font-mono">
                                                    {{ is_object($zipFile) ? $zipFile->getClientOriginalName() : $zipFile }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Loading overlay --}}
                                    <div wire:loading wire:target="zipFile" class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                        <div class="flex items-center gap-2 text-blue-400">
                                            <i class="fa-solid fa-spinner fa-spin"></i>
                                            <span class="text-xs">Mengupload...</span>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            @error('zipFile')
                                <div class="flex items-start gap-2 px-3 py-2 bg-rose-500/10 border border-rose-500/30 rounded-lg">
                                    <i class="fa-solid fa-circle-exclamation text-rose-400 text-xs mt-0.5"></i>
                                    <p class="text-rose-300 text-xs">{{ $message }}</p>
                                </div>
                            @enderror

                            {{-- ZIP Info --}}
                            @if ($zipExtracted)
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @foreach ([
                                        ['label' => 'Files', 'value' => $zipInfo['files'] ?? 0, 'icon' => 'fa-file', 'color' => 'blue'],
                                        ['label' => 'HTML', 'value' => $zipInfo['html'] ?? 0, 'icon' => 'fa-code', 'color' => 'violet'],
                                        ['label' => 'CSS', 'value' => $zipInfo['css'] ?? 0, 'icon' => 'fa-paintbrush', 'color' => 'pink'],
                                        ['label' => 'JS', 'value' => $zipInfo['js'] ?? 0, 'icon' => 'fa-bolt', 'color' => 'amber'],
                                    ] as $stat)
                                        <div class="bg-slate-950/60 border border-slate-800 rounded-xl p-3">
                                            <div class="flex items-center gap-2 mb-1">
                                                <i class="fa-solid {{ $stat['icon'] }} text-{{ $stat['color'] }}-400 text-xs"></i>
                                                <span class="text-slate-500 text-[10px] uppercase tracking-wider">{{ $stat['label'] }}</span>
                                            </div>
                                            <p class="text-white text-lg font-bold font-mono">{{ $stat['value'] }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Framework detection --}}
                                <div class="bg-slate-950/60 border border-slate-800 rounded-xl p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-microchip text-blue-400 text-xs"></i>
                                            <span class="text-slate-300 text-xs font-semibold">Framework Detection</span>
                                        </div>
                                        <span class="px-2 py-0.5 bg-blue-500/20 border border-blue-500/30 text-blue-300 text-[10px] rounded-full font-mono uppercase">
                                            {{ $frameworkDetection['framework'] ?? 'unknown' }}
                                        </span>
                                    </div>
                                    <p class="text-slate-500 text-[10px]">
                                        Confidence: <span class="text-slate-400 font-mono">{{ $frameworkDetection['confidence'] ?? 'low' }}</span>
                                        · Index: <span class="text-slate-400 font-mono">{{ $selectedIndexRelativePath ?: 'index.html' }}</span>
                                    </p>
                                </div>

                                {{-- Warnings --}}
                                @if (!empty($archiveWarnings))
                                    @foreach ($archiveWarnings as $warning)
                                        <div class="flex items-start gap-2 px-3 py-2 bg-amber-500/10 border border-amber-500/30 rounded-lg">
                                            <i class="fa-solid fa-triangle-exclamation text-amber-400 text-xs mt-0.5"></i>
                                            <p class="text-amber-300 text-xs">{{ $warning }}</p>
                                        </div>
                                    @endforeach
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Metadata --}}
                <div class="lg:col-span-2 space-y-5">
                    <div class="bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-800/60">
                            <h2 class="text-white text-sm font-semibold flex items-center gap-2">
                                <i class="fa-solid fa-tag text-violet-400"></i>
                                Theme Metadata
                            </h2>
                            <p class="text-slate-500 text-xs mt-1">Informasi tema Anda</p>
                        </div>

                        <div class="p-5 space-y-4">
                            @foreach ([
                                ['model' => 'themeName', 'label' => 'Theme Name', 'placeholder' => 'My Awesome Theme'],
                                ['model' => 'author', 'label' => 'Author', 'placeholder' => 'Admin'],
                                ['model' => 'themeVersion', 'label' => 'Version', 'placeholder' => '1.0.0'],
                            ] as $field)
                                <div>
                                    <label class="block text-slate-400 text-[10px] font-semibold uppercase tracking-wider mb-2">
                                        {{ $field['label'] }}
                                    </label>
                                    <input
                                        wire:model.live="{{ $field['model'] }}"
                                        type="text"
                                        placeholder="{{ $field['placeholder'] }}"
                                        class="w-full bg-slate-950 border border-slate-800 focus:border-blue-500/60 focus:ring-2 focus:ring-blue-500/20 text-slate-200 text-sm rounded-xl px-4 py-2.5 placeholder-slate-600 transition-all outline-none"
                                    />
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Next Button --}}
                    <button
                        wire:click="generateTheme"
                        wire:loading.attr="disabled"
                        @click="activeStep = 'generate'"
                        class="w-full flex items-center justify-center gap-2 px-6 py-4 bg-gradient-to-r from-blue-600 to-violet-600 hover:from-blue-500 hover:to-violet-500 disabled:opacity-50 text-white text-sm font-semibold rounded-2xl transition-all shadow-lg shadow-violet-900/30 group"
                    >
                        <span wire:loading.remove wire:target="generateTheme">Lanjut ke Generate</span>
                        <span wire:loading wire:target="generateTheme">Memproses...</span>
                        <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform" wire:loading.remove wire:target="generateTheme"></i>
                        <i class="fa-solid fa-spinner fa-spin text-xs" wire:loading wire:target="generateTheme"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- STEP 2: GENERATE                                        --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <div x-show="activeStep === 'generate'" x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0">

            @if ($themeGenerated)
                {{-- Success State --}}
                <div class="bg-gradient-to-br from-emerald-500/10 via-slate-900/50 to-slate-900/50 border border-emerald-500/30 rounded-2xl p-10 text-center">
                    <div class="w-20 h-20 mx-auto mb-5 rounded-full bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-3xl"></i>
                    </div>
                    <h3 class="text-white text-xl font-bold mb-2">Tema Berhasil Digenerate!</h3>
                    <p class="text-slate-400 text-sm mb-6 max-w-md mx-auto">
                        Semua file Blade telah dibuat dari template HTML Anda. Sekarang Anda bisa preview dan edit hasilnya.
                    </p>

                    <div class="flex items-center justify-center gap-3">
                        <button
                            @click="activeStep = 'preview'"
                            class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-emerald-900/30"
                        >
                            <i class="fa-solid fa-eye text-xs"></i>
                            Lihat Preview
                        </button>
                        <button
                            wire:click="generateTheme"
                            class="flex items-center gap-2 px-6 py-3 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 hover:text-white text-sm font-semibold rounded-xl transition-all"
                        >
                            <i class="fa-solid fa-rotate text-xs"></i>
                            Generate Ulang
                        </button>
                    </div>
                </div>
            @else
                {{-- Generating State --}}
                <div class="bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl p-10 text-center">
                    <div class="w-20 h-20 mx-auto mb-5 rounded-full bg-violet-500/20 border border-violet-500/30 flex items-center justify-center">
                        <i class="fa-solid fa-bolt text-violet-400 text-3xl animate-pulse"></i>
                    </div>
                    <h3 class="text-white text-xl font-bold mb-2">Siap Generate?</h3>
                    <p class="text-slate-400 text-sm mb-6 max-w-md mx-auto">
                        Klik tombol di bawah untuk mengubah template HTML Anda menjadi file Blade Laravel.
                    </p>

                    <button
                        wire:click="generateTheme"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 disabled:opacity-50 text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-violet-900/30"
                    >
                        <i class="fa-solid fa-bolt text-xs" wire:loading.remove wire:target="generateTheme"></i>
                        <i class="fa-solid fa-spinner fa-spin text-xs" wire:loading wire:target="generateTheme"></i>
                        <span wire:loading.remove wire:target="generateTheme">Generate Theme</span>
                        <span wire:loading wire:target="generateTheme">Generating...</span>
                    </button>
                </div>
            @endif
        </div>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- STEP 3: PREVIEW — REDESIGNED                                      --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        <div x-show="activeStep === 'preview'" x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0">

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-4">

                {{-- ═══════════════════════════════════════════════════════════ --}}
                {{-- LEFT PANEL: FILE TREE + SNIPPETS                            --}}
                {{-- ═══════════════════════════════════════════════════════════ --}}
                <div class="xl:col-span-3 space-y-3">

                    {{-- ─── FILE TREE ─────────────────────────────────────────── --}}
                    <div class="bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl overflow-hidden">
                        {{-- Header --}}
                        <div class="px-4 py-3 border-b border-slate-800/60 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-folder-tree text-blue-400 text-xs"></i>
                                <span class="text-white text-xs font-semibold">Explorer</span>
                                <span class="text-[10px] bg-slate-800 text-slate-400 px-1.5 py-0.5 rounded font-mono">
                                    {{ count($fileTree) }}
                                </span>
                            </div>
                            <div class="flex items-center gap-0.5">
                                <button wire:click="loadGeneratedFiles"
                                    class="w-6 h-6 flex items-center justify-center text-slate-500 hover:text-slate-300 hover:bg-slate-800 rounded transition"
                                    title="Refresh">
                                    <i class="fa-solid fa-rotate text-[10px]"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Search --}}
                        <div class="px-3 py-2 border-b border-slate-800/60">
                            <div class="relative">
                                <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-600 text-[10px]"></i>
                                <input
                                    x-data
                                    x-on:input="$dispatch('file-search', { q: $event.target.value })"
                                    type="text"
                                    placeholder="Cari file..."
                                    class="w-full bg-slate-950 border border-slate-800 focus:border-blue-500/50 text-slate-300 text-[11px] rounded-lg pl-7 pr-2 py-1.5 placeholder-slate-600 transition outline-none"
                                />
                            </div>
                        </div>

                        {{-- Tree --}}
                        <div
                            x-data="fileTree()"
                            x-on:file-search.window="query = $event.detail.q"
                            class="max-h-[320px] overflow-y-auto p-1.5"
                        >
                            @php
                                // Group files by folder structure
                                $groupedFiles = collect($fileTree)->groupBy(function ($file) {
                                    $parts = explode('/', $file['name']);
                                    array_pop($parts);
                                    return implode('/', $parts) ?: '/';
                                });
                            @endphp

                            @foreach ($groupedFiles as $folder => $files)
                                @if ($folder !== '/')
                                    <div x-data="{ open: true }" x-show="!query || '{{ strtolower($folder) }}'.includes(query.toLowerCase()) || {{ $files->filter(fn($f) => str_contains(strtolower($f['name']), strtolower(request('q', ''))))->count() ? 'true' : 'false' }}"
                                        class="mb-0.5">
                                        <button
                                            @click="open = !open"
                                            class="w-full flex items-center gap-1.5 px-2 py-1.5 text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 rounded-lg text-xs transition"
                                        >
                                            <i class="fa-solid fa-chevron-right text-[8px] transition-transform"
                                                :class="open ? 'rotate-90' : ''"></i>
                                            <i class="fa-solid fa-folder text-amber-400/80 text-[11px]"></i>
                                            <span class="font-medium truncate">{{ $folder }}</span>
                                            <span class="ml-auto text-[9px] text-slate-600 font-mono">{{ $files->count() }}</span>
                                        </button>

                                        <div x-show="open" x-collapse class="ml-3 mt-0.5 space-y-0.5">
                                            @foreach ($files as $file)
                                                @php
                                                    $baseName = basename($file['name']);
                                                    $ext = pathinfo($baseName, PATHINFO_EXTENSION);
                                                    $iconColor = match ($ext) {
                                                        'php', 'blade' => 'text-violet-400',
                                                        'css' => 'text-blue-400',
                                                        'js' => 'text-yellow-400',
                                                        'html' => 'text-orange-400',
                                                        default => 'text-slate-400',
                                                    };
                                                @endphp
                                                <button
                                                    wire:click="selectGeneratedFile('{{ $file['name'] }}')"
                                                    x-show="!query || '{{ strtolower($baseName) }}'.includes(query.toLowerCase())"
                                                    @class([
                                                        'w-full flex items-center gap-2 px-2 py-1.5 rounded-lg text-xs transition-all text-left group',
                                                        'bg-blue-500/15 text-blue-300 border border-blue-500/30' => $selectedGeneratedFile === $file['name'],
                                                        'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border border-transparent' => $selectedGeneratedFile !== $file['name'],
                                                    ])
                                                >
                                                    <i class="fa-solid {{ $file['icon'] ?? 'fa-file' }} {{ $iconColor }} text-[11px]"></i>
                                                    <span class="truncate flex-1 font-mono">{{ $baseName }}</span>
                                                    @if ($selectedGeneratedFile === $file['name'])
                                                        <i class="fa-solid fa-circle text-[5px] text-blue-400"></i>
                                                    @endif
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    @foreach ($files as $file)
                                        @php
                                            $baseName = basename($file['name']);
                                            $ext = pathinfo($baseName, PATHINFO_EXTENSION);
                                            $iconColor = match ($ext) {
                                                'php', 'blade' => 'text-violet-400',
                                                'css' => 'text-blue-400',
                                                'js' => 'text-yellow-400',
                                                'html' => 'text-orange-400',
                                                default => 'text-slate-400',
                                            };
                                        @endphp
                                        <button
                                            wire:click="selectGeneratedFile('{{ $file['name'] }}')"
                                            x-show="!query || '{{ strtolower($baseName) }}'.includes(query.toLowerCase())"
                                            @class([
                                                'w-full flex items-center gap-2 px-2 py-1.5 rounded-lg text-xs transition-all text-left',
                                                'bg-blue-500/15 text-blue-300 border border-blue-500/30' => $selectedGeneratedFile === $file['name'],
                                                'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border border-transparent' => $selectedGeneratedFile !== $file['name'],
                                            ])
                                        >
                                            <i class="fa-solid {{ $file['icon'] ?? 'fa-file' }} {{ $iconColor }} text-[11px]"></i>
                                            <span class="truncate flex-1 font-mono">{{ $baseName }}</span>
                                        </button>
                                    @endforeach
                                @endif
                            @endforeach

                            @if (empty($fileTree))
                                <div class="text-center py-8">
                                    <i class="fa-solid fa-folder-open text-slate-700 text-2xl mb-2"></i>
                                    <p class="text-slate-600 text-xs">Tidak ada file</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ─── SNIPPETS PANEL ────────────────────────────────────── --}}
                    <div class="bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl overflow-hidden">
                        {{-- Header --}}
                        <div class="px-4 py-3 border-b border-slate-800/60 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-wand-magic-sparkles text-violet-400 text-xs"></i>
                                <span class="text-white text-xs font-semibold">Snippets</span>
                            </div>
                            <button
                                x-on:click="$dispatch('toggle-snippets')"
                                class="w-6 h-6 flex items-center justify-center text-slate-500 hover:text-slate-300 hover:bg-slate-800 rounded transition"
                                title="Collapse">
                                <i class="fa-solid fa-chevron-up text-[10px]"></i>
                            </button>
                        </div>

                        <div x-data="{ collapsed: false }" x-on:toggle-snippets.window="collapsed = !collapsed">
                            <div x-show="!collapsed" x-collapse class="p-2">

                                {{-- Category Pills --}}
                                <div class="flex flex-wrap gap-1 px-1 pb-2 border-b border-slate-800/60 mb-2">
                                    @php
                                        $palette = $this->editorPalette();
                                        $categories = array_keys($palette);
                                        $catColors = [
                                            'page' => 'blue',
                                            'post' => 'violet',
                                            'category' => 'pink',
                                            'gallery' => 'amber',
                                            'menu' => 'emerald',
                                            'site' => 'cyan',
                                            'flashnews' => 'orange',
                                            'contact' => 'rose',
                                        ];
                                    @endphp
                                    @foreach ($categories as $cat)
                                        @php $color = $catColors[$cat] ?? 'slate'; @endphp
                                        <button
                                            wire:click="selectVariableGroup('{{ $cat }}')"
                                            @class([
                                                'px-2 py-1 rounded-md text-[10px] font-medium transition-all',
                                                "bg-{$color}-500/20 text-{$color}-300 border border-{$color}-500/30" => $selectedVariableGroup === $cat,
                                                'bg-slate-800/60 text-slate-500 hover:text-slate-300 border border-transparent' => $selectedVariableGroup !== $cat,
                                            ])
                                        >
                                            {{ ucfirst($cat) }}
                                        </button>
                                    @endforeach
                                </div>

                                {{-- Snippets List --}}
                                <div class="max-h-[280px] overflow-y-auto space-y-0.5 pr-0.5">
                                    @forelse (($palette[$selectedVariableGroup] ?? []) as $item)
                                        <button
                                            x-on:click="insertSnippet(@js($item['snippet']))"
                                            class="w-full group text-left px-2.5 py-2 rounded-lg hover:bg-slate-800/60 transition-all"
                                        >
                                            <div class="flex items-center justify-between gap-2 mb-1">
                                                <span class="text-slate-300 text-[11px] font-semibold truncate">
                                                    {{ $item['label'] }}
                                                </span>
                                                <i class="fa-solid fa-plus text-[9px] text-slate-600 group-hover:text-blue-400 opacity-0 group-hover:opacity-100 transition"></i>
                                            </div>
                                            <code class="block text-[9px] text-slate-500 font-mono truncate group-hover:text-slate-400 transition">
                                                {{ $item['snippet'] }}
                                            </code>
                                        </button>
                                    @empty
                                        <div class="text-center py-6">
                                            <i class="fa-solid fa-ghost text-slate-700 text-lg mb-1"></i>
                                            <p class="text-slate-600 text-[10px]">Tidak ada snippet</p>
                                        </div>
                                    @endforelse
                                </div>

                                {{-- Quick Actions --}}
                                <div class="pt-2 mt-2 border-t border-slate-800/60 space-y-1">
                                    <button
                                        x-on:click="convertSelectedCard()"
                                        class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg bg-violet-500/10 hover:bg-violet-500/20 border border-violet-500/20 text-violet-300 text-[10px] font-medium transition-all"
                                    >
                                        <i class="fa-solid fa-arrows-rotate text-[10px]"></i>
                                        Convert selection → loop
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ═══════════════════════════════════════════════════════════ --}}
                {{-- RIGHT PANEL: EDITOR TOOLBAR + CODE AREA                     --}}
                {{-- ═══════════════════════════════════════════════════════════ --}}
                <div class="xl:col-span-9 space-y-3">

                    {{-- ─── MODE TABS ─────────────────────────────────────────── --}}
                    <div class="flex items-center gap-1 p-1 bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-xl">
                        <button
                            wire:click="$set('previewMode', 'editor')"
                            @click="previewMode = 'editor'"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-xs font-medium transition-all"
                            :class="previewMode === 'editor'
                                ? 'bg-gradient-to-r from-blue-600 to-violet-600 text-white shadow-lg shadow-violet-900/20'
                                : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60'"
                        >
                            <i class="fa-solid fa-code text-xs"></i>
                            Editor
                        </button>
                        <button
                            wire:click="$set('previewMode', 'rendered')"
                            @click="previewMode = 'rendered'"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-xs font-medium transition-all"
                            :class="previewMode === 'rendered'
                                ? 'bg-gradient-to-r from-blue-600 to-violet-600 text-white shadow-lg shadow-violet-900/20'
                                : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60'"
                        >
                            <i class="fa-solid fa-eye text-xs"></i>
                            Preview
                        </button>
                        <button
                            wire:click="$set('previewMode', 'source')"
                            @click="previewMode = 'source'"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-xs font-medium transition-all"
                            :class="previewMode === 'source'
                                ? 'bg-gradient-to-r from-blue-600 to-violet-600 text-white shadow-lg shadow-violet-900/20'
                                : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60'"
                        >
                            <i class="fa-solid fa-image text-xs"></i>
                            Original
                        </button>
                    </div>

                    {{-- ─── EDITOR VIEW ───────────────────────────────────────── --}}
                    <div x-show="previewMode === 'editor'" x-cloak
                        x-data="editorToolbar()"
                        class="bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl overflow-hidden">

                        {{-- ═══ TOOLBAR ═══ --}}
                        <div class="flex items-center justify-between gap-2 px-3 py-2 bg-slate-950/60 border-b border-slate-800/60">

                            {{-- LEFT: File info --}}
                            <div class="flex items-center gap-2 min-w-0 flex-1">
                                @if ($selectedGeneratedFile)
                                    @php
                                        $ext = pathinfo($selectedGeneratedFile, PATHINFO_EXTENSION);
                                        $iconColor = match ($ext) {
                                            'php', 'blade' => 'text-violet-400',
                                            'css' => 'text-blue-400',
                                            'js' => 'text-yellow-400',
                                            default => 'text-slate-400',
                                        };
                                        $langLabel = match ($ext) {
                                            'php', 'blade' => 'PHP',
                                            'css' => 'CSS',
                                            'js' => 'JavaScript',
                                            default => ucfirst($ext),
                                        };
                                    @endphp
                                    <div class="flex items-center gap-2 min-w-0">
                                        <i class="fa-solid fa-file-code {{ $iconColor }} text-xs flex-none"></i>
                                        <span class="text-slate-200 text-xs font-mono font-medium truncate">
                                            {{ basename($selectedGeneratedFile) }}
                                        </span>
                                        <span class="flex-none px-1.5 py-0.5 bg-slate-800 border border-slate-700 text-slate-400 text-[9px] rounded font-mono uppercase">
                                            {{ $langLabel }}
                                        </span>
                                        @if (isset($fileDirty[$selectedGeneratedFile]) && $fileDirty[$selectedGeneratedFile])
                                            <span class="flex-none w-1.5 h-1.5 bg-amber-400 rounded-full" title="Unsaved changes"></span>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-file text-slate-600 text-xs"></i>
                                        <span class="text-slate-600 text-xs italic">Belum ada file dipilih</span>
                                    </div>
                                @endif
                            </div>

                            {{-- RIGHT: Actions --}}
                            <div class="flex items-center gap-1">
                                {{-- Search in file --}}
                                <button
                                    x-on:click="toggleSearch()"
                                    :class="searchOpen ? 'bg-blue-500/20 text-blue-300' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800'"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg transition"
                                    title="Find (Ctrl+F)"
                                >
                                    <i class="fa-solid fa-magnifying-glass text-[11px]"></i>
                                </button>

                                {{-- Format --}}
                                <button
                                    x-on:click="formatCode()"
                                    class="w-7 h-7 flex items-center justify-center text-slate-500 hover:text-slate-300 hover:bg-slate-800 rounded-lg transition"
                                    title="Format code"
                                >
                                    <i class="fa-solid fa-align-left text-[11px]"></i>
                                </button>

                                {{-- Toggle wrap --}}
                                <button
                                    x-on:click="toggleWrap()"
                                    :class="wordWrap ? 'bg-blue-500/20 text-blue-300' : 'text-slate-500 hover:text-slate-300 hover:bg-slate-800'"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg transition"
                                    title="Toggle word wrap"
                                >
                                    <i class="fa-solid fa-text-width text-[11px]"></i>
                                </button>

                                {{-- Divider --}}
                                <div class="w-px h-5 bg-slate-800 mx-0.5"></div>

                                {{-- Save --}}
                                <button
                                    wire:click="saveGeneratedFile"
                                    wire:loading.attr="disabled"
                                    @disabled(!$selectedGeneratedFile)
                                    class="flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-blue-600 to-violet-600 hover:from-blue-500 hover:to-violet-500 disabled:opacity-40 disabled:cursor-not-allowed text-white text-[11px] font-semibold rounded-lg transition-all shadow-lg shadow-violet-900/20"
                                >
                                    <i class="fa-solid fa-floppy-disk text-[10px]" wire:loading.remove wire:target="saveGeneratedFile"></i>
                                    <i class="fa-solid fa-spinner fa-spin text-[10px]" wire:loading wire:target="saveGeneratedFile"></i>
                                    <span>Save</span>
                                    <kbd class="hidden sm:inline px-1 py-0.5 bg-white/10 rounded text-[9px] font-mono">⌘S</kbd>
                                </button>

                                {{-- More menu --}}
                                <div x-data="{ open: false }" class="relative">
                                    <button
                                        @click="open = !open"
                                        class="w-7 h-7 flex items-center justify-center text-slate-500 hover:text-slate-300 hover:bg-slate-800 rounded-lg transition"
                                    >
                                        <i class="fa-solid fa-ellipsis-vertical text-[11px]"></i>
                                    </button>
                                    <div
                                        x-show="open"
                                        @click.outside="open = false"
                                        x-transition
                                        x-cloak
                                        class="absolute right-0 top-full mt-1 w-44 bg-slate-900 border border-slate-700 rounded-xl shadow-2xl overflow-hidden z-30"
                                    >
                                        <button class="w-full flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-slate-800 text-xs transition">
                                            <i class="fa-solid fa-rotate-left text-[10px]"></i> Undo
                                        </button>
                                        <button class="w-full flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-slate-800 text-xs transition">
                                            <i class="fa-solid fa-rotate-right text-[10px]"></i> Redo
                                        </button>
                                        <div class="border-t border-slate-800"></div>
                                        <button class="w-full flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-slate-800 text-xs transition">
                                            <i class="fa-solid fa-copy text-[10px]"></i> Copy all
                                        </button>
                                        <button class="w-full flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-slate-800 text-xs transition">
                                            <i class="fa-solid fa-download text-[10px]"></i> Download file
                                        </button>
                                        <div class="border-t border-slate-800"></div>
                                        <button class="w-full flex items-center gap-2 px-3 py-2 text-rose-400 hover:bg-rose-500/10 text-xs transition">
                                            <i class="fa-solid fa-trash text-[10px]"></i> Delete file
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ═══ SEARCH BAR (collapsible) ═══ --}}
                        <div
                            x-show="searchOpen"
                            x-collapse
                            x-cloak
                            class="flex items-center gap-2 px-3 py-2 bg-slate-950/40 border-b border-slate-800/60"
                        >
                            <div class="flex-1 relative">
                                <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-600 text-[10px]"></i>
                                <input
                                    x-ref="searchInput"
                                    x-on:input="search($event.target.value)"
                                    x-on:keydown.enter.prevent="findNext()"
                                    x-on:keydown.escape="toggleSearch()"
                                    type="text"
                                    placeholder="Cari di file..."
                                    class="w-full bg-slate-900 border border-slate-800 focus:border-blue-500/50 text-slate-300 text-xs rounded-lg pl-7 pr-2 py-1.5 placeholder-slate-600 outline-none transition"
                                />
                            </div>
                            <span class="text-slate-500 text-[10px] font-mono whitespace-nowrap" x-text="matchInfo"></span>
                            <button @click="findPrev()" class="w-6 h-6 flex items-center justify-center text-slate-500 hover:text-slate-300 rounded hover:bg-slate-800 transition">
                                <i class="fa-solid fa-chevron-up text-[10px]"></i>
                            </button>
                            <button @click="findNext()" class="w-6 h-6 flex items-center justify-center text-slate-500 hover:text-slate-300 rounded hover:bg-slate-800 transition">
                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </button>
                            <button @click="toggleSearch()" class="w-6 h-6 flex items-center justify-center text-slate-500 hover:text-slate-300 rounded hover:bg-slate-800 transition">
                                <i class="fa-solid fa-xmark text-[10px]"></i>
                            </button>
                        </div>

                        {{-- ═══ CODE EDITOR ═══ --}}
                        <div class="h-[560px] bg-slate-950 relative"
                            wire:ignore
                            x-data="codeEditor({ content: @js($editingFileContent) })">
                            <textarea id="theme-code-editor" x-ref="codeSource"
                                class="w-full h-full bg-slate-950 text-slate-300 text-xs font-mono leading-5 p-4 border-0 resize-none focus:outline-none focus:ring-0"
                                spellcheck="false"
                                placeholder="// Pilih file dari explorer"></textarea>
                        </div>

                        {{-- ═══ STATUS BAR ═══ --}}
                        <div class="flex items-center justify-between px-4 py-1.5 bg-slate-950/80 border-t border-slate-800/60 text-[10px] font-mono">
                            <div class="flex items-center gap-3 text-slate-500">
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-code-branch text-[9px]"></i>
                                    theme/dev
                                </span>
                                <span class="text-slate-700">•</span>
                                <span class="text-emerald-500 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                    Ready
                                </span>
                            </div>
                            <div class="flex items-center gap-3 text-slate-500">
                                @if ($selectedGeneratedFile)
                                    <span>{{ count(explode("\n", $fileContents[$selectedGeneratedFile] ?? '')) }} lines</span>
                                    <span class="text-slate-700">•</span>
                                    <span>UTF-8</span>
                                    <span class="text-slate-700">•</span>
                                    <span class="text-violet-400">Laravel Blade</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ─── PREVIEW VIEW ──────────────────────────────────────── --}}
                    <div x-show="previewMode === 'rendered'" x-cloak
                        x-on:theme-preview-refresh.window="const url = new URL($refs.renderFrame.src); url.searchParams.set('v', Date.now()); $refs.renderFrame.src = url.toString()"
                        class="bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-800/60 bg-slate-950/40">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                                <span class="text-slate-300 text-xs font-semibold">Live Preview</span>
                                <span class="text-slate-600 text-[10px]">Konten aktual dari database</span>
                            </div>
                            <button
                                wire:click="savePreview"
                                wire:loading.attr="disabled"
                                class="flex items-center gap-1.5 px-2.5 py-1 bg-slate-800 hover:bg-slate-700 disabled:opacity-50 text-slate-400 hover:text-white text-[10px] rounded-lg transition"
                            >
                                <i class="fa-solid fa-rotate text-[10px]" wire:loading.remove wire:target="savePreview"></i>
                                <i class="fa-solid fa-spinner fa-spin text-[10px]" wire:loading wire:target="savePreview"></i>
                                Simpan & Refresh
                            </button>
                        </div>
                        @if ($themeGenerated)
                            <iframe
                                x-ref="renderFrame"
                                src="{{ route('admin.themes.preview', ['theme' => \Illuminate\Support\Str::slug($themeName) ?: 'theme']) }}"
                                sandbox="allow-scripts"
                                title="Preview website dengan tema yang sedang diedit"
                                class="w-full h-[640px] bg-white"
                            ></iframe>
                        @else
                            <div class="h-64 flex items-center justify-center text-slate-400 text-sm">
                                Generate tema terlebih dahulu untuk menguji file Blade.
                            </div>
                        @endif
                    </div>

                    {{-- ─── SOURCE VIEW ───────────────────────────────────────── --}}
                    <div x-show="previewMode === 'source'" x-cloak
                        class="bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-800/60 bg-slate-950/40">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-blue-400 rounded-full"></span>
                                <span class="text-slate-300 text-xs font-semibold">Original Design</span>
                            </div>
                            <button
                                x-on:click="$refs.sourceFrame.contentWindow.location.reload()"
                                class="flex items-center gap-1.5 px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-[10px] rounded-lg transition"
                            >
                                <i class="fa-solid fa-rotate text-[10px]"></i>
                                Refresh
                            </button>
                        </div>
                        <iframe
                            x-ref="sourceFrame"
                            src="{{ route('admin.themes.preview.source', ['sessionId' => $sessionId]) }}"
                            sandbox="allow-scripts"
                            title="Original design"
                            class="w-full h-[640px] bg-white"
                        ></iframe>
                    </div>
                </div>
            </div>
        </div>
    </main>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- STYLES                                                          --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/monokai.min.css">

<style>
    .CodeMirror {
        height: 100%;
        font-size: 12px;
        line-height: 1.6;
        background: #020617 !important;
    }
    .CodeMirror-gutters { background: #020617 !important; border-right: 1px solid #1e293b; }

    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #0f172a; }
    ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
    ::-webkit-scrollbar-thumb:hover { background: #475569; }

    [x-cloak] { display: none !important; }
</style>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- SCRIPTS                                                        --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/php/php.min.js"></script>

<script>
    // ── Alpine: Theme Builder ──────────────────────────────────────
    function themeBuilder(config = {}) {
        return {
            activeStep: config.activeStep ?? 'upload',
            previewMode: config.previewMode ?? 'editor',
        };
    }

    // ── Alpine: Code Editor ────────────────────────────────────────
    function codeEditor(config = {}) {
        return {
            init(initialContent) {
                const textarea = this.$refs.codeSource;
                if (!window.CodeMirror || window.themeBuilderEditor) return;

                textarea.value = initialContent || '';
                window.themeBuilderEditor = CodeMirror.fromTextArea(textarea, {
                    mode: 'application/x-httpd-php',
                    theme: 'monokai',
                    lineNumbers: true,
                    lineWrapping: true,
                    tabSize: 4,
                    indentUnit: 4,
                });

                const wire = this.$wire;
                window.themeBuilderEditor.on('change', (editor) => {
                    wire.set('editingFileContent', editor.getValue(), false);
                });

                window.addEventListener('theme-file-selected', (event) => {
                    if (window.themeBuilderEditor) {
                        window.themeBuilderEditor.setValue(event.detail.content || '');
                    }
                });
            },
        };
    }

    // ── Alpine: File Tree ─────────────────────────────────────────
    function fileTree() {
        return {
            query: '',
        };
    }

    // ── Alpine: Editor Toolbar ────────────────────────────────────
    function editorToolbar() {
        return {
            searchOpen: false,
            wordWrap: true,
            matchInfo: '',

            toggleSearch() {
                this.searchOpen = !this.searchOpen;
                if (this.searchOpen) {
                    this.$nextTick(() => this.$refs.searchInput?.focus());
                } else {
                    // Clear search highlight
                    if (window.themeBuilderEditor) {
                        window.themeBuilderEditor.setOption('highlightSelectionMatches', false);
                    }
                    this.matchInfo = '';
                }
            },

            search(query) {
                const editor = window.themeBuilderEditor;
                if (!editor) return;

                if (!query) {
                    this.matchInfo = '';
                    return;
                }

                // Simple search using CodeMirror's cursor API
                const content = editor.getValue();
                const matches = content.split(query).length - 1;
                this.matchInfo = matches > 0 ? `${matches} hasil` : 'Tidak ada';

                // Jump to first match
                const idx = content.indexOf(query);
                if (idx !== -1) {
                    const line = content.substr(0, idx).split('\n').length - 1;
                    editor.setCursor({ line, ch: 0 });
                    editor.scrollIntoView({ line, ch: 0 }, 100);
                }
            },

            findNext() {
                const editor = window.themeBuilderEditor;
                if (!editor) return;
                const cursor = editor.getSearchCursor(this.$refs.searchInput.value, editor.getCursor());
                if (cursor.findNext()) {
                    editor.setSelection(cursor.from(), cursor.to());
                    editor.scrollIntoView(cursor.from(), 100);
                }
            },

            findPrev() {
                const editor = window.themeBuilderEditor;
                if (!editor) return;
                const cursor = editor.getSearchCursor(this.$refs.searchInput.value, editor.getCursor());
                if (cursor.findPrevious()) {
                    editor.setSelection(cursor.from(), cursor.to());
                    editor.scrollIntoView(cursor.from(), 100);
                }
            },

            formatCode() {
                const editor = window.themeBuilderEditor;
                if (!editor) return;

                // Simple indentation based on tags
                const value = editor.getValue();
                const lines = value.split('\n');
                let indent = 0;
                const formatted = lines.map(line => {
                    const trimmed = line.trim();
                    if (trimmed.match(/^<\//) || trimmed.match(/^}/)) indent = Math.max(0, indent - 1);
                    const result = '    '.repeat(indent) + trimmed;
                    if (trimmed.match(/<[^/][^>]*[^/]>$/) && !trimmed.match(/\/>$/)) indent++;
                    if (trimmed.match(/^\s*@(if|foreach|for|while|section|push|stack|component)/)) indent++;
                    if (trimmed.match(/^\s*@(endif|endforeach|endfor|endwhile|endsection|endpush|endstack|endcomponent)/)) indent = Math.max(0, indent - 1);
                    return result;
                }).join('\n');

                editor.setValue(formatted);
            },

            toggleWrap() {
                const editor = window.themeBuilderEditor;
                if (!editor) return;
                this.wordWrap = !this.wordWrap;
                editor.setOption('lineWrapping', this.wordWrap);
            },
        };
    }

    // ── Keyboard shortcuts ────────────────────────────────────────
    document.addEventListener('keydown', (e) => {
        // Cmd/Ctrl + S = Save
        if ((e.metaKey || e.ctrlKey) && e.key === 's') {
            e.preventDefault();
            const saveBtn = document.querySelector('[wire\\:click="saveGeneratedFile"]');
            saveBtn?.click();
        }
        // Cmd/Ctrl + F = Find (handled by Alpine toggleSearch via toolbar)
    });

    // ── Cleanup ────────────────────────────────────────────────────
    document.addEventListener('livewire:navigating', () => {
        if (window.themeBuilderEditor) {
            window.themeBuilderEditor.toTextArea();
            window.themeBuilderEditor = null;
        }
    });
</script>
</div>
