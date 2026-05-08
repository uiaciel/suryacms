<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/30 py-6 px-4">

    {{-- ALERTS --}}
    @foreach (['message' => 'success', 'error' => 'danger'] as $key => $type)
        @if (session($key))
            <div class="max-w-7xl mx-auto mb-4"
                 x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex items-start gap-3 {{ $type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-rose-50 border-rose-200 text-rose-700' }} border rounded-xl px-4 py-3">
                    <i class="fa-solid fa-{{ $type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' }} text-lg mt-0.5 shrink-0"></i>
                    <div class="flex-1 text-sm">{!! session($key) !!}</div>
                    <button @click="show = false" class="ml-2 transition-colors hover:opacity-70">
                        <i class="fa-solid fa-x-lg text-xs"></i>
                    </button>
                </div>
            </div>
        @endif
    @endforeach

    @if (!empty($validationErrors))
        <div class="max-w-7xl mx-auto mb-4"
             x-data="{ show: true }" x-show="show" x-transition>
            <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 text-amber-700 rounded-xl px-4 py-3">
                <i class="fa-solid fa-exclamation-circle-fill text-lg mt-0.5 shrink-0"></i>
                <div>
                    <p class="text-sm font-semibold mb-1">Peringatan:</p>
                    <ul class="text-xs space-y-0.5 list-disc list-inside">
                        @foreach ($validationErrors as $error)
                            <li>{{ is_array($error) ? implode(', ', $error) : $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button @click="show = false" class="ml-auto transition-colors hover:opacity-70">
                    <i class="fa-solid fa-x-lg text-xs"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- HEADER --}}
    <header class="max-w-7xl mx-auto flex items-center justify-between mb-6 pb-5 border-b border-slate-200">
        <div class="flex items-center gap-3">
            <div class="p-2.5 bg-indigo-50 border border-indigo-100 rounded-xl">
                <i class="fa-solid fa-magic text-indigo-600 text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800 leading-none">Theme Engine</h2>
                <p class="text-slate-400 text-xs mt-0.5">Builder v3.0</p>
            </div>
        </div>

        @if($mode === 'editor')
            <button wire:click="switchToList"
                    class="inline-flex items-center gap-2 px-4 py-2 border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-600 hover:text-slate-800 text-sm font-medium rounded-xl transition-all duration-200">
                <i class="fa-solid fa-arrow-left text-xs"></i> Back to Themes
            </button>
        @endif
    </header>

    <div class="max-w-7xl mx-auto">

        {{-- ================================================================= --}}
        {{-- MODE: LIST --}}
        {{-- ================================================================= --}}
        @if($mode === 'list')
        <div class="space-y-6">

            {{-- UPLOAD FORM --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="flex items-center gap-3 px-6 py-4 bg-indigo-50 border-b border-indigo-100">
                    <i class="fa-solid fa-cloud-upload text-indigo-500"></i>
                    <strong class="text-indigo-700 text-sm font-semibold">Generate Tema Baru</strong>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div x-data="uploadProgress">
                            <div class="mb-4">
                                <label class="block text-slate-700 text-sm font-semibold mb-2">Nama Tema</label>
                                <input type="text" wire:model="themeName"
                                       placeholder="contoh-tema"
                                       class="w-full px-4 py-3 bg-white border @error('themeName') border-rose-400 @else border-slate-200 @enderror rounded-xl text-slate-800 text-sm placeholder-slate-400 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                                @error('themeName') <p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-slate-700 text-sm font-semibold mb-2">File ZIP</label>
                                <input type="file" wire:model="indexFile" accept=".zip"
                                       class="block w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 file:cursor-pointer cursor-pointer border @error('indexFile') border-rose-400 @else border-slate-200 @enderror rounded-xl p-2 bg-white focus:outline-none focus:border-indigo-400">
                                @error('indexFile') <p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>

                            <div x-show="uploading" class="mb-4">
                                <div class="flex justify-between text-xs text-slate-500 mb-1.5">
                                    <span>Uploading...</span>
                                    <span x-text="progress + '%'"></span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5">
                                    <div class="bg-indigo-500 h-1.5 rounded-full transition-all duration-300" :style="`width:${progress}%`"></div>
                                </div>
                            </div>

                            <button wire:click="uploadAndPrepareEditor"
                                    wire:loading.attr="disabled"
                                    :disabled="uploading"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-md shadow-indigo-200">
                                <span wire:loading.remove>
                                    <i class="fa-solid fa-shuffle"></i> Mulai Normalisasi
                                </span>
                                <span wire:loading class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Memproses…
                                </span>
                            </button>
                        </div>

                        <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-5">
                            <h6 class="text-indigo-700 font-semibold text-sm flex items-center gap-2 mb-3">
                                <i class="fa-solid fa-info-circle"></i> Petunjuk Upload
                            </h6>
                            <ul class="space-y-2 text-slate-600 text-xs">
                                <li class="flex items-start gap-2"><span class="text-indigo-400 mt-0.5">•</span> Upload file <strong class="text-slate-700">.ZIP</strong> yang berisi tema</li>
                                <li class="flex items-start gap-2"><span class="text-indigo-400 mt-0.5">•</span> File harus memiliki <strong class="text-slate-700">index.html</strong></li>
                                <li class="flex items-start gap-2"><span class="text-indigo-400 mt-0.5">•</span> Semua aset (CSS, JS, images) harus ada</li>
                                <li class="flex items-start gap-2"><span class="text-indigo-400 mt-0.5">•</span> Nama tema hanya alfanumerik dan dash (-)</li>
                                <li class="flex items-start gap-2"><span class="text-indigo-400 mt-0.5">•</span> Ukuran maksimal file: <strong class="text-slate-700">10 MB</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- EXISTING FILES LIST --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="flex items-center justify-between px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-collection text-sky-500"></i>
                        <strong class="text-slate-700 text-sm font-semibold">File index.html yang Tersimpan</strong>
                    </div>
                    <span class="px-2.5 py-1 bg-sky-50 border border-sky-200 text-sky-600 text-xs font-bold rounded-full">{{ count($existingIndexFiles) }}</span>
                </div>
                <div class="p-6">
                    @if(empty($existingIndexFiles))
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <div class="p-4 bg-slate-100 rounded-2xl mb-4">
                                <i class="fa-solid fa-inbox text-4xl text-slate-400"></i>
                            </div>
                            <p class="text-slate-500 text-sm">Belum ada file tema yang tersimpan.</p>
                            <p class="text-slate-400 text-xs mt-1">Mulai dengan upload file ZIP baru di atas.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left border-b border-slate-100">
                                        <th class="text-slate-400 text-xs uppercase tracking-wider font-semibold pb-3 pr-4">Nama Tema</th>
                                        <th class="text-slate-400 text-xs uppercase tracking-wider font-semibold pb-3 pr-4">Ukuran</th>
                                        <th class="text-slate-400 text-xs uppercase tracking-wider font-semibold pb-3 pr-4">Status</th>
                                        <th class="text-slate-400 text-xs uppercase tracking-wider font-semibold pb-3 pr-4">Dibuat</th>
                                        <th class="text-slate-400 text-xs uppercase tracking-wider font-semibold pb-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @foreach($existingIndexFiles as $file)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="py-3.5 pr-4">
                                            <p class="text-slate-800 font-semibold">{{ $file['name'] }}</p>
                                            <p class="text-slate-400 text-xs font-mono mt-0.5">ID: {{ substr($file['id'], 0, 8) }}...</p>
                                        </td>
                                        <td class="py-3.5 pr-4">
                                            <span class="text-slate-600 text-xs">{{ number_format($file['size'] / 1024, 1) }} KB</span>
                                        </td>
                                        <td class="py-3.5 pr-4">
                                            @if($file['is_generated'])
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-full">
                                                    <i class="fa-solid fa-check-circle"></i> Generated
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-semibold rounded-full">
                                                    <i class="fa-solid fa-pencil"></i> Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 pr-4">
                                            <span class="text-slate-500 text-xs">{{ \Carbon\Carbon::createFromTimestamp($file['created_at'])->format('d M Y, H:i') }}</span>
                                        </td>
                                        <td class="py-3.5">
                                            <div class="flex items-center gap-2">
                                                <button wire:click="loadExistingIndexFile('{{ $file['id'] }}')"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition-all shadow-sm shadow-indigo-200">
                                                    <i class="fa-solid fa-pencil-square"></i> Edit
                                                </button>
                                                <button wire:click="deleteFile('{{ $file['id'] }}')"
                                                        wire:confirm="Hapus tema '{{ $file['name'] }}'?"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-rose-200 hover:bg-rose-50 text-rose-500 hover:text-rose-700 text-xs font-semibold rounded-lg transition-all">
                                                    <i class="fa-solid fa-trash"></i> Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
        @endif

        {{-- ================================================================= --}}
        {{-- MODE: EDITOR --}}
        {{-- ================================================================= --}}
        @if($mode === 'editor')
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5"
             x-data="themeEditor()"
             x-init="init(@js($htmlEdited), @js($currentThemeName))">

            {{-- EDITOR PANEL --}}
            <div class="lg:col-span-3">
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    {{-- Editor Header --}}
                    <div class="flex items-center justify-between px-4 py-3 bg-slate-50 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="flex gap-1.5">
                                <span class="w-3 h-3 rounded-full bg-rose-400"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-file-code text-slate-400 text-sm"></i>
                                <span class="text-slate-700 text-sm font-semibold">index.html</span>
                                @if($currentThemeName)
                                    <span class="px-2 py-0.5 bg-sky-50 border border-sky-200 text-sky-600 text-xs rounded-md">{{ $currentThemeName }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="save()" title="Ctrl+S"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition-all shadow-sm shadow-emerald-200">
                                <i class="fa-solid fa-check-circle"></i> Simpan
                            </button>
                            <button wire:click="generateTheme" wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-xs font-semibold rounded-lg transition-all shadow-sm shadow-indigo-200">
                                <span wire:loading.remove><i class="fa-solid fa-shuffle"></i> Generate</span>
                                <span wire:loading class="flex items-center gap-1.5">
                                    <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    Generating...
                                </span>
                            </button>
                            <button wire:click="installTheme" wire:loading.attr="disabled"
                                    @if(!$isGenerated) disabled title="Generate tema terlebih dahulu" @endif
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 {{ $isGenerated ? 'bg-teal-600 hover:bg-teal-700 shadow-sm shadow-teal-200' : 'bg-slate-200 cursor-not-allowed text-slate-400' }} text-white text-xs font-semibold rounded-lg transition-all">
                                <span wire:loading.remove><i class="fa-solid fa-cloud-check"></i> Install</span>
                                <span wire:loading class="flex items-center gap-1.5">
                                    <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    Installing...
                                </span>
                            </button>
                        </div>
                    </div>

                    {{-- CodeMirror --}}
                    <div class="p-0" wire:ignore>
                        <textarea x-ref="htmlEditor" style="display: none;"></textarea>
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-between px-4 py-2.5 bg-slate-50 border-t border-slate-100">
                        <span class="text-slate-400 text-xs flex items-center gap-1.5">
                            <i class="fa-solid fa-keyboard"></i> Ctrl + S untuk simpan
                        </span>
                        <span>
                            @if($isGenerated)
                                <span class="inline-flex items-center gap-1.5 text-emerald-600 text-xs font-semibold">
                                    <i class="fa-solid fa-check-circle-fill"></i> Generated
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-amber-500 text-xs font-semibold">
                                    <i class="fa-solid fa-exclamation-triangle-fill"></i> Draft
                                </span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            {{-- SIDEBAR --}}
            <div class="lg:col-span-1 space-y-4">

                {{-- FLOW STATUS --}}
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
                        <p class="text-slate-600 text-xs font-semibold uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-diagram-3 text-indigo-500"></i> Status Proses
                        </p>
                    </div>
                    <div class="p-4 space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 w-6 h-6 rounded-full bg-emerald-100 border border-emerald-300 flex items-center justify-center">
                                <i class="fa-solid fa-check text-emerald-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-slate-700 text-xs font-semibold">1. Upload</p>
                                <p class="text-slate-400 text-xs mt-0.5">File ZIP diekstrak & dinormalisasi</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 w-6 h-6 rounded-full {{ $isGenerated ? 'bg-emerald-100 border-emerald-300' : 'bg-slate-100 border-slate-300' }} border flex items-center justify-center">
                                <i class="fa-solid fa-arrow-right {{ $isGenerated ? 'text-emerald-600' : 'text-slate-400' }} text-xs"></i>
                            </div>
                            <div>
                                <p class="text-slate-700 text-xs font-semibold">2. Generate</p>
                                <p class="text-slate-400 text-xs mt-0.5">{{ $isGenerated ? '✅ Blade files sudah dibuat' : 'Klik tombol "Generate" →' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 w-6 h-6 rounded-full {{ $isGenerated ? 'bg-teal-100 border-teal-300' : 'bg-slate-100 border-slate-200' }} border flex items-center justify-center">
                                <i class="fa-solid fa-download {{ $isGenerated ? 'text-teal-600' : 'text-slate-300' }} text-xs"></i>
                            </div>
                            <div>
                                <p class="text-slate-700 text-xs font-semibold">3. Install</p>
                                <p class="text-slate-400 text-xs mt-0.5">{{ $isGenerated ? 'Siap untuk diinstall' : 'Tersedia setelah generate' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- THEME INFO --}}
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
                        <p class="text-slate-600 text-xs font-semibold uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-info-circle text-indigo-500"></i> Informasi
                        </p>
                    </div>
                    <div class="p-4 space-y-3 text-xs">
                        <div>
                            <p class="text-slate-400 uppercase tracking-wider text-[10px] font-semibold mb-1">Nama Tema</p>
                            <p class="text-slate-700 font-medium" x-text="'{{ $currentThemeName }}'"></p>
                        </div>
                        <div>
                            <p class="text-slate-400 uppercase tracking-wider text-[10px] font-semibold mb-1">Ukuran</p>
                            <p class="text-slate-700 font-medium" x-text="editorStats.lines + ' baris, ' + editorStats.chars + ' karakter'"></p>
                        </div>
                        <div class="pt-2 border-t border-slate-100">
                            <p class="text-slate-400 text-[11px] leading-relaxed">💡 Simpan perubahan sebelum generate untuk memastikan semua modifikasi tersimpan.</p>
                        </div>
                    </div>
                </div>

                {{-- HELP --}}
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
                        <p class="text-slate-600 text-xs font-semibold uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-question-circle text-indigo-500"></i> Bantuan
                        </p>
                    </div>
                    <div class="p-4 space-y-2 text-xs text-slate-500">
                        <p><strong class="text-slate-700">Simpan:</strong> Menyimpan perubahan HTML ke storage sementara</p>
                        <p><strong class="text-slate-700">Generate:</strong> Membuat file blade dan konfigurasi tema</p>
                        <p><strong class="text-slate-700">Install:</strong> Memindahkan tema ke folder produksi</p>
                        <div class="pt-2 border-t border-slate-100">
                            <p class="text-slate-400 mb-1">Tema akan dipindahkan ke:</p>
                            <p class="font-mono text-[10px] text-indigo-500 leading-relaxed">
                                📁 public/frontend/{name}/<br>
                                📁 resources/views/frontend/{name}/
                            </p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        @endif

    </div>

</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/monokai.min.css">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/css/css.min.js"></script>

<script>
    document.addEventListener('alpine:init', () => {

        Alpine.data('uploadProgress', () => ({
            uploading: false,
            progress: 0,
            init() {
                window.addEventListener('livewire-upload-start', () => this.uploading = true);
                window.addEventListener('livewire-upload-progress', e => this.progress = e.detail.progress);
                window.addEventListener('livewire-upload-finish', () => this.uploading = false);
                window.addEventListener('livewire-upload-error', () => this.uploading = false);
            }
        }));

        Alpine.data('themeEditor', () => ({
            editor: null, content: '', themeName: '',
            editorStats: { lines: 0, chars: 0 }, updateDebounce: null,

            init(content = '', themeName = '') {
                console.log('themeEditor.init() called with content length:', content?.length, 'themeName:', themeName);
                this.content = content;
                this.themeName = themeName;

                // Register event listener early
                Livewire.on('theme-loaded', (data) => {
                    console.log('theme-loaded event received:', data);
                    if (data && data.content) {
                        this.loadThemeContent(data.content, data.themeName);
                    }
                });

                this.$nextTick(() => {
                    console.log('Calling initEditor()');
                    this.initEditor();
                });
            },

            initEditor() {
                try {
                    const textarea = this.$refs.htmlEditor;
                    console.log('textarea ref found:', !!textarea);
                    if (!textarea) {
                        console.error('textarea ref not found!');
                        return;
                    }

                    if (this.editor) {
                        this.editor.toTextArea();
                    }

                    this.editor = CodeMirror.fromTextArea(textarea, {
                        mode: 'htmlmixed',
                        theme: 'monokai',
                        lineNumbers: true,
                        lineWrapping: true,
                        tabSize: 4,
                        indentUnit: 4,
                        indentWithTabs: false,
                        extraKeys: {
                            'Ctrl-S': () => this.save(),
                            'Cmd-S': () => this.save()
                        },
                        viewportMargin: Infinity,
                    });

                    console.log('CodeMirror instance created:', !!this.editor);

                    // Set initial content if available
                    if (this.content && this.content.trim()) {
                        console.log('Setting initial content, length:', this.content.length);
                        this.editor.setValue(this.content);
                    } else {
                        console.warn('No initial content available');
                    }

                    this.updateStats();
                    this.bindChanges();

                    // Refresh CodeMirror with delays
                    setTimeout(() => {
                        if (this.editor) {
                            this.editor.refresh();
                            console.log('CodeMirror refreshed (1st)');
                        }
                    }, 50);

                    setTimeout(() => {
                        if (this.editor) {
                            this.editor.refresh();
                            console.log('CodeMirror refreshed (2nd)');
                        }
                    }, 200);

                } catch (e) {
                    console.error('Failed to initialize CodeMirror:', e);
                }
            },

            loadThemeContent(content, themeName) {
                console.log('loadThemeContent() called with content length:', content?.length, 'themeName:', themeName);
                if (this.editor) {
                    this.editor.setValue(content);
                    this.content = content;
                    this.themeName = themeName;
                    this.updateStats();
                    setTimeout(() => {
                        if (this.editor) {
                            this.editor.refresh();
                            this.editor.focus();
                            console.log('CodeMirror refreshed and focused');
                        }
                    }, 50);
                } else {
                    console.warn('Editor not yet initialized, storing content for later');
                    this.content = content;
                    this.themeName = themeName;
                }
            },

            bindChanges() {
                if (!this.editor) return;
                this.editor.on('change', (cm) => {
                    clearTimeout(this.updateDebounce);
                    this.updateDebounce = setTimeout(() => {
                        this.updateStats();
                        this.$wire.set('htmlEdited', cm.getValue(), false);
                    }, 500);
                });
                this.editor.on('cursorActivity', () => this.updateStats());
            },

            updateStats() {
                if (!this.editor) return;
                const content = this.editor.getValue();
                this.editorStats = { lines: this.editor.lineCount(), chars: content.length };
            },

            save() {
                if (!this.editor) return;
                this.$wire.set('htmlEdited', this.editor.getValue());
                this.$wire.saveHtmlChanges();
            }
        }));
    });
</script>
@endpush
