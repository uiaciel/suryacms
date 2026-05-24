<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/30 py-6 px-4">

    {{-- ALERTS --}}
    @foreach (['message' => 'success', 'error' => 'danger'] as $key => $type)
        @if (session($key))
            <div class="max-w-7xl mx-auto mb-4" x-data="{ show: true }" x-show="show" x-transition>
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
        <div class="max-w-7xl mx-auto mb-4" x-data="{ show: true }" x-show="show" x-transition>
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
                <i class="fa-solid fa-wand-magic-sparkles text-indigo-600 text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800 leading-none">Interactive Theme Builder</h2>
                <p class="text-slate-400 text-xs mt-0.5">Konversi ZIP HTML ke Tema SuryaCMS Secara Interaktif</p>
            </div>
        </div>

        <a href="{{ route('admin.themes.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-600 hover:text-slate-800 text-sm font-medium rounded-xl transition-all duration-200">
            <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Tema
        </a>
    </header>

    <div class="max-w-7xl mx-auto">
        {{-- WIZARD STEPS INDICATOR --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 mb-6 shadow-sm">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                @php
                    $stepNames = [
                        1 => ['Upload & Info', 'fa-cloud-upload'],
                        2 => ['Selector Mapping', 'fa-crosshairs'],
                        3 => ['Variable Mapping', 'fa-tags'],
                        4 => ['Homepage Config', 'fa-layer-group'],
                        5 => ['Preview & Install', 'fa-check-double']
                    ];
                @endphp
                @foreach ($stepNames as $num => $info)
                    <div class="flex items-center gap-3 {{ $step === $num ? 'text-indigo-600 font-semibold' : ($step > $num ? 'text-emerald-600' : 'text-slate-400') }}">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs border-2 {{ $step === $num ? 'border-indigo-600 bg-indigo-50' : ($step > $num ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 bg-slate-50') }}">
                            @if ($step > $num)
                                <i class="fa-solid fa-check text-xs"></i>
                            @else
                                <span>{{ $num }}</span>
                            @endif
                        </div>
                        <div class="text-left">
                            <p class="text-xs uppercase tracking-wider text-slate-400 font-medium leading-none">Langkah {{ $num }}</p>
                            <p class="text-sm mt-0.5 flex items-center gap-1.5 font-semibold">
                                <i class="fa-solid {{ $info[1] }} text-xs opacity-75"></i>
                                {{ $info[0] }}
                            </p>
                        </div>
                    </div>
                    @if ($num < 5)
                        <div class="hidden md:block flex-1 h-0.5 bg-slate-100 rounded"></div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- WIZARD PANELS --}}
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">

            {{-- STEP 1: UPLOAD & INFO --}}
            @if ($step === 1)
                <div class="p-6 space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="text-base font-bold text-slate-800">Langkah 1: Upload File & Informasi Tema</h3>
                        <p class="text-slate-400 text-xs mt-1">Unggah berkas ZIP tema Anda (yang berisi index.html) dan tentukan identitas tema.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-slate-700 text-sm font-semibold mb-2">Nama Tema (Slug)</label>
                                <input type="text" wire:model="themeName" placeholder="contoh: tema-baru-perusahaan"
                                       class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                                @error('themeName') <p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-slate-700 text-sm font-semibold mb-2">File ZIP Tema</label>
                                <input type="file" wire:model="indexFile" accept=".zip"
                                       class="block w-full text-sm text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 file:cursor-pointer cursor-pointer border border-slate-200 rounded-xl p-2 bg-white focus:outline-none focus:border-indigo-400">
                                @error('indexFile') <p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                                @if ($indexFile)
                                    <p class="text-slate-500 text-xs mt-1.5">
                                        📦 File: <span class="font-semibold">{{ $indexFile->getClientOriginalName() }}</span> ({{ number_format($indexFile->getSize() / 1024 / 1024, 2) }} MB)
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="bg-indigo-50/50 border border-indigo-100 rounded-2xl p-5 text-slate-600 text-xs space-y-3">
                            <strong class="text-indigo-800 font-semibold block text-sm">💡 Persyaratan ZIP Tema:</strong>
                            <ul class="list-disc list-inside space-y-1 text-slate-500">
                                <li>Wajib terdapat file <code class="bg-indigo-100/80 text-indigo-700 px-1 rounded font-mono">index.html</code> di root ZIP atau dalam satu subfolder.</li>
                                <li>Pastikan link aset gambar, css, dan js bersifat relatif (seperti <code class="bg-indigo-100/80 text-indigo-700 px-1 rounded font-mono">css/style.css</code>).</li>
                                <li>Semua stylesheet dan script di dalam layout akan di-adjust secara otomatis.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- STEP 2: SELECTOR MAPPING --}}
            @if ($step === 2)
                <div class="p-6 space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="text-base font-bold text-slate-800">Langkah 2: Petakan Selektor Navigasi & Footer</h3>
                        <p class="text-slate-400 text-xs mt-1">Bantu sistem menemukan pembungkus navbar, footer, dan banner utama di dalam file HTML.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-slate-700 text-sm font-semibold mb-2">Selektor Navigasi (Navbar)</label>
                                <div class="relative">
                                    <input type="text" wire:model="selectorNav" placeholder="contoh: #mainNav atau .navbar"
                                           class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                                    <i class="fa-solid fa-location-crosshairs absolute left-3.5 top-3.5 text-slate-400"></i>
                                </div>
                                <p class="text-slate-400 text-[10px] mt-1">Selektor CSS dari elemen <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">&lt;nav&gt;</code> atau menu container.</p>
                                @error('selectorNav') <p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-slate-700 text-sm font-semibold mb-2">Selektor Footer</label>
                                <div class="relative">
                                    <input type="text" wire:model="selectorFooter" placeholder="contoh: #footer atau .site-footer"
                                           class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                                    <i class="fa-solid fa-location-dot absolute left-3.5 top-3.5 text-slate-400"></i>
                                </div>
                                <p class="text-slate-400 text-[10px] mt-1">Selektor CSS dari elemen <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">&lt;footer&gt;</code> tema.</p>
                                @error('selectorFooter') <p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-slate-700 text-sm font-semibold mb-2">Selektor Hero Banner (Opsional)</label>
                                <div class="relative">
                                    <input type="text" wire:model="selectorHero" placeholder="contoh: #hero atau .header-banner"
                                           class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                                    <i class="fa-solid fa-image absolute left-3.5 top-3.5 text-slate-400"></i>
                                </div>
                                <p class="text-slate-400 text-[10px] mt-1">Section banner utama. Ini digunakan untuk mengenerate header halaman dinamis otomatis.</p>
                            </div>
                        </div>

                        <div class="bg-indigo-50/50 border border-indigo-100 rounded-2xl p-5 text-slate-500 text-xs space-y-3">
                            <strong class="text-indigo-800 font-semibold block text-sm">💡 Tips Penulisan Selektor CSS:</strong>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Gunakan tanda <code class="bg-indigo-100/85 text-indigo-700 px-1 rounded font-mono">#id-nama</code> jika mencari elemen berdasarkan ID.</li>
                                <li>Gunakan tanda <code class="bg-indigo-100/85 text-indigo-700 px-1 rounded font-mono">.class-nama</code> jika mencari berdasarkan class CSS.</li>
                                <li>Gunakan nama tag seperti <code class="bg-indigo-100/85 text-indigo-700 px-1 rounded font-mono">nav</code> atau <code class="bg-indigo-100/85 text-indigo-700 px-1 rounded font-mono">footer</code> langsung.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- STEP 3: VARIABLE MAPPING --}}
            @if ($step === 3)
                <div class="p-6 space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="text-base font-bold text-slate-800">Langkah 3: Pemetaan Variabel Dinamis (Placeholders)</h3>
                        <p class="text-slate-400 text-xs mt-1">Tinjau data teks statis dalam HTML yang terdeteksi dan hubungkan secara dinamis ke setelan database.</p>
                    </div>

                    @if (empty($mappings))
                        <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            <i class="fa-solid fa-info-circle text-slate-300 text-3xl mb-3 block"></i>
                            <p class="text-slate-400 text-sm">Tidak ada placeholder teks statis umum yang berhasil terdeteksi otomatis.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto border border-slate-100 rounded-xl">
                            <table class="w-full text-sm text-left text-slate-500">
                                <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b border-slate-100">
                                    <tr>
                                        <th class="px-6 py-4 w-12 text-center">Aktif</th>
                                        <th class="px-6 py-4">Teks Asli di HTML</th>
                                        <th class="px-6 py-4">Variabel Database Pengganti</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mappings as $index => $map)
                                        <tr class="bg-white border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                                            <td class="px-6 py-4 text-center">
                                                <input type="checkbox" wire:model="mappings.{{ $index }}.enabled"
                                                       class="w-4 h-4 text-indigo-600 bg-white border-slate-300 rounded focus:ring-indigo-500">
                                            </td>
                                            <td class="px-6 py-4 font-mono text-xs text-slate-700 max-w-sm truncate">
                                                {{ $map['original'] }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <input type="text" wire:model="mappings.{{ $index }}.replacement"
                                                       class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-mono text-indigo-600 focus:outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif

            {{-- STEP 4: HOMEPAGE CONFIG --}}
            @if ($step === 4)
                <div class="p-6 space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="text-base font-bold text-slate-800">Langkah 4: Konfigurasi Seksi & Tata Letak</h3>
                        <p class="text-slate-400 text-xs mt-1">Semua seksi HTML terdeteksi (section, article, div dengan struktur konten). Atur tipe seksi atau keluarkan dari tema.</p>
                    </div>

                    @if (empty($sections))
                        <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            <i class="fa-solid fa-circle-exclamation text-slate-300 text-3xl mb-3 block"></i>
                            <p class="text-slate-400 text-sm">Tidak ada seksi HTML (&lt;section&gt;) yang terdeteksi.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($sections as $index => $sec)
                                <div class="border border-slate-200 rounded-xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white hover:shadow-sm transition-all">
                                    <div class="flex-1 space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 bg-indigo-50 border border-indigo-100 text-indigo-700 text-[10px] font-mono rounded font-semibold">{{ $sec['id'] }}</span>
                                            <strong class="text-sm font-semibold text-slate-800">{{ $sec['label'] }}</strong>
                                        </div>
                                        <p class="text-[10px] text-slate-400 font-mono truncate max-w-lg">
                                            Preview: {{ substr(strip_tags($sec['content']), 0, 120) }}...
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0">
                                        <label class="text-xs font-medium text-slate-500">Tipe Seksi:</label>
                                        <select wire:model="sections.{{ $index }}.type"
                                                class="px-3 py-2 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-400 bg-white">
                                            <option value="homepage">Seksi Homepage Utama</option>
                                            <option value="block">Ekstrak Custom Block CMS</option>
                                            <option value="skip">Abaikan / Hapus dari Tema</option>
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            {{-- STEP 5: PREVIEW & INSTALL --}}
            @if ($step === 5)
                <div class="p-0 flex flex-col min-h-screen bg-white" x-data="{ activeTab: 'generated' }">

                    {{-- TAB NAVIGATION --}}
                    <div class="border-b border-slate-200 bg-slate-50/50 px-6 py-3 flex gap-4">
                        <button @click="activeTab = 'generated'"
                                :class="activeTab === 'generated' ? 'border-b-2 border-indigo-600 text-indigo-600 font-semibold' : 'text-slate-500 hover:text-slate-700'"
                                class="text-sm pb-3 px-1 transition-colors">
                            <i class="fa-solid fa-file-code mr-2"></i>File Generate (Editable)
                        </button>
                        <button @click="activeTab = 'source'"
                                :class="activeTab === 'source' ? 'border-b-2 border-indigo-600 text-indigo-600 font-semibold' : 'text-slate-500 hover:text-slate-700'"
                                class="text-sm pb-3 px-1 transition-colors">
                            <i class="fa-solid fa-code mr-2"></i>Original HTML Source (Reference)
                        </button>
                    </div>

                    {{-- TAB 1: GENERATED FILES EDITOR --}}
                    <div x-show="activeTab === 'generated'" class="flex-1 flex flex-col p-0 lg:flex-row">

                        {{-- File selector sidebar --}}
                        <div class="w-full lg:w-72 border-r border-slate-200 bg-slate-50/80 p-4 space-y-4 lg:max-h-[600px] lg:overflow-y-auto">
                            <div>
                                <strong class="text-xs uppercase text-slate-500 tracking-wider font-semibold block mb-3">Daftar File Hasil Generate</strong>
                                <div class="space-y-1.5">
                                    @foreach ($generatedFiles as $filename => $content)
                                        <button type="button"
                                                wire:click="loadFileForEditing('{{ $filename }}')"
                                                class="w-full text-left px-3 py-2.5 text-xs font-medium rounded-lg flex items-center gap-2.5 transition-all {{ $activePreviewFile === $filename ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-white' }}">
                                            <i class="fa-solid fa-file-code text-sm {{ $activePreviewFile === $filename ? '' : 'text-slate-400' }}"></i>
                                            <span class="truncate">{{ $filename }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Code Editor Editable Pane --}}
                        <div class="flex-1 flex flex-col p-6 bg-slate-900 text-slate-300">
                            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                                <div>
                                    <span class="text-sm font-semibold font-mono text-indigo-400">📄 {{ $activePreviewFile }}</span>
                                    <p class="text-[10px] text-slate-500 mt-1">Edit dan perbaiki kode di bawah, lalu klik "Simpan Perubahan"</p>
                                </div>
                            </div>

                            <div class="flex-1 flex flex-col" x-data="{ saving: false, saved: false }">
                                <textarea
                                    wire:model="editingFileContent"
                                    class="flex-1 font-mono text-xs p-4 bg-slate-800 text-emerald-400 border border-slate-700 rounded-lg resize-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="Kode file akan ditampilkan di sini..."></textarea>

                                <div class="flex items-center gap-3 mt-4">
                                    <button type="button"
                                            @click="saving = true; $wire.updateGeneratedFile().then(() => { saved = true; saving = false; setTimeout(() => { saved = false; }, 3000); })"
                                            :disabled="saving"
                                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-600 text-white text-sm font-semibold rounded-lg transition-all">
                                        <i :class="saving ? 'fa-solid fa-spinner fa-spin' : 'fa-solid fa-floppy-disk'"></i>
                                        <span x-text="saving ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                                    </button>
                                    <div x-show="saved" class="text-sm text-emerald-400 flex items-center gap-2">
                                        <i class="fa-solid fa-check-circle"></i> Tersimpan!
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: ORIGINAL HTML SOURCE --}}
                    <div x-show="activeTab === 'source'" class="flex-1 flex flex-col p-0 lg:flex-row bg-slate-900" x-data="{ selectedHtmlFile: 'index.html' }">

                        {{-- HTML Files List Sidebar --}}
                        <div class="w-full lg:w-72 border-r border-slate-700 bg-slate-800/50 p-4 space-y-4 lg:max-h-[600px] lg:overflow-y-auto">
                            <div>
                                <strong class="text-xs uppercase text-slate-400 tracking-wider font-semibold block mb-3">
                                    <i class="fa-solid fa-file-html mr-2"></i>Original HTML Files
                                </strong>
                                <div class="space-y-1.5">
                                    @if (!empty($allHtmlFiles))
                                        @foreach ($allHtmlFiles as $filename => $filedata)
                                            <button type="button"
                                                    @click="selectedHtmlFile = '{{ $filename }}'"
                                                    :class="selectedHtmlFile === '{{ $filename }}' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-700'"
                                                    class="w-full text-left px-3 py-2.5 text-xs font-medium rounded-lg flex items-center gap-2.5 transition-all">
                                                <i class="fa-solid fa-file text-sm"></i>
                                                <span class="truncate">{{ $filename }}</span>
                                            </button>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- HTML File Content Display --}}
                        <div class="flex-1 flex flex-col p-6 overflow-hidden">
                            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-700">
                                <div>
                                    <span class="text-sm font-semibold font-mono text-indigo-400">
                                        <i class="fa-solid fa-file-html mr-2"></i>
                                        <span x-text="selectedHtmlFile"></span>
                                    </span>
                                    <p class="text-[10px] text-slate-500 mt-1">Referensi kode asli dari file HTML di dalam ZIP. Copy dari sini jika perlu dimasukkan ke file generate.</p>
                                </div>
                                <span class="px-2 py-1 bg-slate-700 border border-slate-600 text-slate-400 text-[10px] rounded font-mono">READ ONLY</span>
                            </div>

                            {{-- File Content Viewer --}}
                            <div class="flex-1 font-mono text-xs text-emerald-400 overflow-y-auto bg-slate-800 rounded-lg p-4 border border-slate-700">
                                <pre x-show="selectedHtmlFile === 'index.html'" class="whitespace-pre-wrap break-words">{!! $htmlSourcePreview !!}</pre>

                                @foreach ($allHtmlFiles as $filename => $filedata)
                                    <pre x-show="selectedHtmlFile === '{{ $filename }}'" class="whitespace-pre-wrap break-words">{!! htmlspecialchars($filedata['content'], ENT_QUOTES, 'UTF-8') !!}</pre>
                                @endforeach
                            </div>
                        </div>

                    </div>

                </div>
            @endif

            {{-- PANEL FOOTER NAVIGATION --}}
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-between">
                @if ($step > 1)
                    <button type="button" wire:click="prevStep"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-slate-200 hover:border-slate-300 text-slate-600 hover:text-slate-800 text-sm font-medium rounded-xl hover:bg-white transition-all">
                        <i class="fa-solid fa-arrow-left text-xs"></i> Sebelumnya
                    </button>
                @else
                    <div></div>
                @endif

                @if ($step < 5)
                    <button type="button" wire:click="nextStep"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow transition-all">
                        Lanjutkan <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                @else
                    <button type="button" wire:click="installTheme"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow transition-all">
                        <i class="fa-solid fa-circle-check text-xs"></i> Finalisasi & Install Tema
                    </button>
                @endif
            </div>

        </div>
