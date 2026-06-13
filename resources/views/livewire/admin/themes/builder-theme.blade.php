<div class="min-h-screen bg-[#f0f3fb] py-6 px-4">

    {{-- ALERTS & LOADING --}}
    @foreach (['message' => 'success', 'error' => 'danger'] as $key => $type)
        @if (session($key))
            <div class="max-w-7xl mx-auto mb-4" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                <div
                    class="flex items-start gap-3 {{ $type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-rose-50 border-rose-200 text-rose-700' }} border rounded-xl px-4 py-3 shadow-sm">
                    <i
                        class="fa-solid fa-{{ $type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' }} text-lg mt-0.5 shrink-0"></i>
                    <div class="flex-1 text-sm font-medium">{!! session($key) !!}</div>
                    <button @click="show = false" class="ml-2 transition-colors hover:opacity-70">
                        <i class="fa-solid fa-x-lg text-xs"></i>
                    </button>
                </div>
            </div>
        @endif
    @endforeach

    @if (!empty($validationErrors))
        <div class="max-w-7xl mx-auto mb-4" x-data="{ show: true }" x-show="show" x-transition>
            <div
                class="flex items-start gap-3 bg-amber-50 border border-amber-200 text-amber-700 rounded-xl px-4 py-3 shadow-sm">
                <i class="fa-solid fa-exclamation-circle-fill text-lg mt-0.5 shrink-0"></i>
                <div>
                    <p class="text-sm font-semibold mb-1">⚠️ Peringatan Validasi:</p>
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

    {{-- LOADING OVERLAY --}}
    <div wire:loading.flex class="fixed inset-0 z-50 items-center justify-center bg-black/20 backdrop-blur-xs">
        <div class="bg-white rounded-2xl shadow-2xl p-8 text-center">
            <i class="fa-solid fa-circle-notch fa-spin text-4xl text-indigo-600 mb-4"></i>
            <p class="text-slate-700 font-semibold">Processing...</p>
            <p class="text-slate-400 text-xs mt-1">Jangan tutup halaman ini</p>
        </div>
    </div>

    {{-- HEADER --}}
    <header class="max-w-7xl mx-auto flex items-center justify-between mb-6 pb-5 border-b border-slate-200">
        <div class="flex items-center gap-3">
            <div class="p-2.5 bg-blue-600 rounded-xl shadow-lg shadow-blue-200">
                <i class="fa-solid fa-wand-magic-sparkles text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-slate-800 leading-none tracking-tight">Interactive Theme Builder</h2>
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
                        5 => ['Preview & Install', 'fa-check-double'],
                    ];
                @endphp
                @foreach ($stepNames as $num => $info)
                    <div
                        class="flex items-center gap-3 {{ $step === $num ? 'text-indigo-600 font-semibold' : ($step > $num ? 'text-emerald-600' : 'text-slate-400') }}">
                        <div
                            class="w-9 h-9 rounded-xl flex items-center justify-center text-xs border-2 transition-all duration-300 {{ $step === $num ? 'border-indigo-600 bg-indigo-600 text-white shadow-md' : ($step > $num ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-slate-200 bg-slate-50') }}">
                            @if ($step > $num)
                                <i class="fa-solid fa-check text-xs"></i>
                            @else
                                <span>{{ $num }}</span>
                            @endif
                        </div>
                        <div class="text-left hidden lg:block">
                            <p class="text-xs uppercase tracking-wider text-slate-400 font-medium leading-none">Langkah
                                {{ $num }}</p>
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
                        <p class="text-slate-400 text-xs mt-1">Unggah berkas ZIP tema Anda (yang berisi index.html) dan
                            tentukan identitas tema.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-slate-700 text-sm font-semibold mb-2">Nama Tema (Slug)</label>
                                <input type="text" wire:model="themeName" placeholder="contoh: tema-baru-perusahaan"
                                    class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                                @error('themeName')
                                    <p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-slate-700 text-sm font-semibold mb-2">File ZIP Tema</label>
                                <div class="relative" x-data="{ uploading: false }"
                                    x-on:livewire-upload-start="uploading = true"
                                    x-on:livewire-upload-finish="uploading = false"
                                    x-on:livewire-upload-error="uploading = false">
                                    <input type="file" wire:model="indexFile" accept=".zip"
                                        class="block w-full text-sm text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 file:cursor-pointer cursor-pointer border border-slate-200 rounded-xl p-2 bg-white focus:outline-none focus:border-indigo-400">

                                    <div x-show="uploading"
                                        class="mt-2 flex items-center gap-2 text-indigo-600 text-xs font-medium">
                                        <i class="fa-solid fa-circle-notch fa-spin"></i> Mengunggah file...
                                    </div>
                                </div>
                                @error('indexFile')
                                    <p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p>
                                @enderror
                                @if ($indexFile)
                                    <p class="text-slate-500 text-xs mt-1.5">
                                        📦 File: <span
                                            class="font-semibold">{{ $indexFile->getClientOriginalName() }}</span>
                                        ({{ number_format($indexFile->getSize() / 1024 / 1024, 2) }} MB)
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div
                            class="bg-indigo-50/50 border border-indigo-100 rounded-2xl p-5 text-slate-600 text-xs space-y-3">
                            <strong class="text-indigo-800 font-semibold block text-sm">💡 Persyaratan ZIP
                                Tema:</strong>
                            <ul class="list-disc list-inside space-y-1 text-slate-500">
                                <li>Wajib terdapat file <code
                                        class="bg-indigo-100/80 text-indigo-700 px-1 rounded font-mono">index.html</code>
                                    di root ZIP atau dalam satu subfolder.</li>
                                <li>Pastikan link aset gambar, css, dan js bersifat relatif (seperti <code
                                        class="bg-indigo-100/80 text-indigo-700 px-1 rounded font-mono">css/style.css</code>).
                                </li>
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
                        <h3 class="text-base font-bold text-slate-800">Langkah 2: Mapping Selektor</h3>
                        <p class="text-slate-400 text-xs mt-1">Klik elemen di kolom kiri untuk melihat di preview
                            (tengah), lalu tentukan selector di kolom kanan.</p>
                    </div>

                    {{-- ROW 1: INPUT FORM --}}
                    <div
                        class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <div>
                            <label class="block text-slate-700 text-[11px] font-bold uppercase mb-1.5">
                                <i class="fa-solid fa-bars text-green-600 mr-1"></i> Selektor Navigasi
                            </label>
                            <input type="text" wire:model="selectorNav" placeholder="#nav atau .navbar"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-800 text-xs focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all font-mono">
                            @error('selectorNav')
                                <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-slate-700 text-[11px] font-bold uppercase mb-1.5">
                                <i class="fa-solid fa-shoe-prints text-blue-600 mr-1"></i> Selektor Footer
                            </label>
                            <input type="text" wire:model="selectorFooter" placeholder="#footer atau .footer"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-800 text-xs focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all font-mono">
                            @error('selectorFooter')
                                <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-slate-700 text-[11px] font-bold uppercase mb-1.5">
                                <i class="fa-solid fa-image text-amber-600 mr-1"></i> Selektor Hero (Opsional)
                            </label>
                            <input type="text" wire:model="selectorHero" placeholder="#hero atau .banner"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-800 text-xs focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all font-mono">
                        </div>
                    </div>

                    {{-- ROW 2: SIDEBARS & CODE --}}
                    <div class="flex flex-col lg:flex-row gap-4 items-stretch h-[600px]" x-data="{ scrollPos: 0, selectedLine: {{ $selectedIndexItem ?? 0 }} }">

                        {{-- COLUMN 1: HTML Structure Indexing --}}
                        <div
                            class="w-full lg:w-64 flex flex-col border border-slate-200 rounded-xl bg-white overflow-hidden shadow-sm">
                            <div class="p-3 bg-slate-50 border-b border-slate-200">
                                <p class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Struktur HTML
                                </p>
                            </div>
                            <div class="flex-1 overflow-y-auto p-2 space-y-1 bg-slate-50/30">
                                <div
                                    class="bg-indigo-50/50 border border-indigo-100 rounded-lg p-2 text-slate-600 text-[9px] mb-2">
                                    <strong class="text-indigo-700 font-semibold block mb-1">💡 Tips:</strong>
                                    <ul class="list-disc list-inside space-y-0.5 text-slate-500">
                                        <li><code>#id</code> untuk ID</li>
                                        <li><code>.class</code> untuk class</li>
                                        <li>Klik elemen untuk preview</li>
                                    </ul>
                                </div>
                                @forelse ($htmlStructureIndex as $item)
                                    <button wire:click="selectIndexItem({{ $item['line'] }})"
                                        @click="selectedLine = {{ $item['line'] }}; document.getElementById('codePreview').scrollTop = ({{ $item['line'] }} - 1) * 18"
                                        class="w-full text-left px-3 py-2 rounded-lg text-xs font-mono transition-all {{ $selectedIndexItem == $item['line'] ? 'bg-indigo-100 border border-indigo-300 text-indigo-700' : 'hover:bg-slate-100 text-slate-600' }}">
                                        <span class="inline-block w-12 text-slate-400">L{{ $item['line'] }}</span>
                                        <span class="inline-block px-2 py-0.5 rounded text-[9px] font-bold mr-2"
                                            :class="{
                                                'bg-blue-100 text-blue-700': '{{ $item['type'] }}'
                                                === 'head',
                                                'bg-purple-100 text-purple-700': '{{ $item['type'] }}'
                                                === 'header',
                                                'bg-green-100 text-green-700': '{{ $item['type'] }}'
                                                === 'nav',
                                                'bg-orange-100 text-orange-700': '{{ $item['type'] }}'
                                                === 'section',
                                                'bg-red-100 text-red-700': '{{ $item['type'] }}'
                                                === 'footer',
                                            }">
                                            {{ strtoupper($item['type']) }}
                                        </span>
                                        <span class="truncate">{{ $item['label'] }}</span>
                                    </button>
                                @empty
                                    <p class="text-xs text-slate-400 p-3">Tidak ada elemen terdeteksi</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- COLUMN 2: Code Preview with Line Numbers --}}
                        <div
                            class="flex-1 min-w-0 flex flex-col border border-slate-200 rounded-xl bg-slate-900 overflow-hidden shadow-lg">
                            <div class="p-3 bg-slate-800 border-b border-slate-700 flex justify-between items-center">
                                <p class="text-[11px] font-bold text-slate-300 uppercase tracking-wider">Preview:
                                    index.html</p>
                            </div>
                            <div id="codePreview"
                                class="flex-1 overflow-y-auto font-mono text-[11px] leading-relaxed">
                                @if ($htmlOriginalContent)
                                    @php
                                        $lines = explode("\n", $htmlOriginalContent);
                                    @endphp
                                    <table class="w-full">
                                        @foreach ($lines as $lineNum => $line)
                                            <tr
                                                class="hover:bg-slate-800 {{ $lineNum + 1 === $selectedIndexItem ? 'bg-indigo-900/50' : '' }}">
                                                <td
                                                    class="text-slate-500 select-none px-2 py-0.5 border-r border-slate-700 w-12 text-right">
                                                    {{ $lineNum + 1 }}</td>
                                                <td
                                                    class="text-emerald-400 px-3 py-0.5 whitespace-pre-wrap break-words">
                                                    {!! htmlspecialchars($line) !!}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- STEP 3: VARIABLE MAPPING --}}
            @if ($step === 3)
                <div class="p-6 space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="text-base font-bold text-slate-800">Langkah 3: Pemetaan Variabel Dinamis</h3>
                        <p class="text-slate-400 text-xs mt-1">Hubungkan teks statis HTML ke variabel database. Sebelah
                            kanan: preview kode untuk referensi.</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 h-[600px]" x-data="{ selectedLine: {{ $selectedIndexItem ?? 0 }} }">

                        {{-- COLUMN 1: Variable Mapping Table with Select All --}}
                        <div class="border border-slate-200 rounded-xl bg-white overflow-hidden flex flex-col">
                            <div class="p-3 bg-slate-50 border-b border-slate-200 sticky top-0 z-10">
                                <div class="flex items-center gap-2 mb-3">
                                    <input type="search" placeholder="Cari variabel..."
                                        @input="let searchVal = $event.target.value; document.querySelectorAll('[data-mapping-row]').forEach(row => row.style.display = row.textContent.toLowerCase().includes(searchVal.toLowerCase()) ? '' : 'none')"
                                        class="flex-1 px-3 py-1.5 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs"></i>
                                </div>
                            </div>
                            <div class="flex-1 overflow-y-auto">
                                @if (empty($mappings))
                                    <div class="text-center py-10 text-slate-400 text-sm">
                                        <i class="fa-solid fa-info-circle text-slate-300 text-3xl mb-3 block"></i>
                                        <p>Tidak ada placeholder terdeteksi.</p>
                                    </div>
                                @else
                                    <table class="w-full text-sm text-left text-slate-500">
                                        <thead
                                            class="text-xs text-slate-700 uppercase bg-slate-50 border-b border-slate-100 sticky top-12 z-10">
                                            <tr>
                                                <th class="px-4 py-3 w-12 text-center">
                                                    <input type="checkbox"
                                                        @change="document.querySelectorAll('[data-mapping-checkbox]').forEach(cb => cb.checked = $event.target.checked); Livewire.dispatch('toggleAllMappings', {checked: $event.target.checked})"
                                                        class="w-4 h-4 text-indigo-600 bg-white border-slate-300 rounded cursor-pointer">
                                                </th>
                                                <th class="px-4 py-3 flex-1 min-w-0">Teks Asli</th>
                                                <th class="px-4 py-3 w-20 text-center">Tipe</th>
                                                <th class="px-4 py-3 flex-1 min-w-0">Variabel</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($mappings as $index => $map)
                                                <tr data-mapping-row
                                                    class="bg-white border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                                                    <td class="px-4 py-3 text-center">
                                                        <input type="checkbox" data-mapping-checkbox
                                                            wire:model="mappings.{{ $index }}.enabled"
                                                            class="w-4 h-4 text-indigo-600 bg-white border-slate-300 rounded cursor-pointer">
                                                    </td>
                                                    <td class="px-4 py-3 font-mono text-xs text-slate-700 truncate"
                                                        title="{{ $map['original'] }}">
                                                        {{ substr($map['original'], 0, 30) }}{{ strlen($map['original']) > 30 ? '...' : '' }}
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <span
                                                            class="inline-block px-2 py-1 rounded text-[10px] font-bold"
                                                            :class="{
                                                                'bg-blue-100 text-blue-700': '{{ $map['type'] }}'
                                                                === 'link',
                                                                'bg-purple-100 text-purple-700': '{{ $map['type'] }}'
                                                                === 'logo',
                                                                'bg-red-100 text-red-700': '{{ $map['type'] }}'
                                                                === 'email',
                                                                'bg-green-100 text-green-700': '{{ $map['type'] }}'
                                                                === 'phone',
                                                                'bg-amber-100 text-amber-700': '{{ $map['type'] }}'
                                                                === 'social',
                                                                'bg-slate-100 text-slate-700': '{{ $map['type'] }}'
                                                                === 'text',
                                                            }">
                                                            {{ strtoupper($map['type'] ?? 'text') }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <input type="text"
                                                            wire:model="mappings.{{ $index }}.replacement"
                                                            class="w-full px-2 py-1.5 border border-slate-200 rounded text-xs font-mono text-indigo-600 focus:outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>

                            {{-- Manual Variable Input --}}
                            <div class="p-4 bg-slate-50 border-t border-slate-200">
                                <p class="text-xs font-semibold text-slate-600 mb-3 uppercase">Tambah Variabel Manual
                                </p>
                                <div class="space-y-2" x-data="{ newOriginal: '', newReplacement: '', newType: 'text' }">
                                    <input type="text" x-model="newOriginal" placeholder="Teks asli di HTML"
                                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs font-mono focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                                    <div class="flex gap-2">
                                        <input type="text" x-model="newReplacement"
                                            placeholder="Variabel pengganti"
                                            class="flex-1 px-3 py-2 border border-slate-200 rounded-lg text-xs font-mono focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                                        <select x-model="newType"
                                            class="px-2 py-2 border border-slate-200 rounded-lg text-xs bg-white focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                                            <option value="text">Text</option>
                                            <option value="link">Link</option>
                                            <option value="logo">Logo</option>
                                            <option value="email">Email</option>
                                            <option value="phone">Phone</option>
                                            <option value="social">Social</option>
                                        </select>
                                        <button
                                            @click="if(newOriginal.trim() && newReplacement.trim()) { Livewire.dispatch('addManualMapping', {original: newOriginal, replacement: newReplacement, type: newType}); newOriginal = ''; newReplacement = ''; newType = 'text'; }"
                                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition-all">
                                            <i class="fa-solid fa-plus mr-1"></i> Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- COLUMN 2: Code Preview (Same as Step 2) --}}
                        <div class="border border-slate-200 rounded-xl bg-slate-900 overflow-hidden flex flex-col">
                            <div class="p-3 bg-slate-800 border-b border-slate-700 sticky top-0">
                                <p class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Preview:
                                    index.html</p>
                            </div>
                            <div id="codePreviewStep3"
                                class="flex-1 overflow-y-auto font-mono text-[10px] leading-relaxed">
                                @if ($htmlOriginalContent)
                                    @php
                                        $lines = explode("\n", $htmlOriginalContent);
                                    @endphp
                                    <table class="w-full">
                                        @foreach ($lines as $lineNum => $line)
                                            <tr
                                                class="hover:bg-slate-800 {{ $lineNum + 1 === $selectedIndexItem ? 'bg-indigo-900/50' : '' }}">
                                                <td
                                                    class="text-slate-500 select-none px-2 py-0.5 border-r border-slate-700 w-12 text-right">
                                                    {{ $lineNum + 1 }}</td>
                                                <td
                                                    class="text-emerald-400 px-3 py-0.5 whitespace-pre-wrap break-words">
                                                    {!! htmlspecialchars($line) !!}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            @endif

            {{-- STEP 4: HOMEPAGE CONFIG --}}
            @if ($step === 4)
                <div class="p-6 space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="text-base font-bold text-slate-800">Langkah 4: Konfigurasi Seksi & Tata Letak</h3>
                        <p class="text-slate-400 text-xs mt-1">Semua seksi HTML terdeteksi (section, article, div
                            dengan struktur konten). Atur tipe seksi atau keluarkan dari tema.</p>
                    </div>

                    @if (empty($sections))
                        <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            <i class="fa-solid fa-circle-exclamation text-slate-300 text-3xl mb-3 block"></i>
                            <p class="text-slate-400 text-sm">Tidak ada seksi HTML (&lt;section&gt;) yang terdeteksi.
                            </p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($sections as $index => $sec)
                                <div
                                    class="border border-slate-200 rounded-xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white hover:shadow-sm transition-all">
                                    <div class="flex-1 space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="px-2 py-0.5 bg-indigo-50 border border-indigo-100 text-indigo-700 text-[10px] font-mono rounded font-semibold">{{ $sec['id'] }}</span>
                                            <strong
                                                class="text-sm font-semibold text-slate-800">{{ $sec['label'] }}</strong>
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
                <div class="p-6 space-y-4">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="text-base font-bold text-slate-800">Langkah 5: Review & Install Tema</h3>
                        <p class="text-slate-400 text-xs mt-1">Kiri: Original HTML untuk referensi. Kanan: File
                            generated yang bisa di-edit sebelum install.</p>
                    </div>

                    <div class="space-y-6" x-data="{ selectedHtmlFile: 'index.html', selectedGenFile: 'index.blade.php', loadingCode: false }">

                        {{-- ROW 1: Original HTML Source Code --}}
                        <div class="flex flex-col lg:flex-row gap-4 h-[400px]">
                            <div
                                class="flex-1 border border-slate-200 rounded-xl bg-slate-900 overflow-hidden flex flex-col">
                                <div
                                    class="p-3 bg-slate-800 border-b border-slate-700 flex justify-between items-center">
                                    <p class="text-[11px] font-bold text-slate-300 uppercase tracking-wider">Source
                                        Code: <span x-text="selectedHtmlFile" class="text-amber-400"></span></p>
                                    <p class="text-[10px] text-slate-400">Original HTML</p>
                                </div>
                                <div
                                    class="flex-1 overflow-y-auto font-mono text-[10px] bg-slate-800 text-emerald-400 p-4 leading-relaxed">
                                    <pre x-show="selectedHtmlFile === 'index.html'" class="whitespace-pre-wrap break-words">{!! htmlspecialchars($htmlSourcePreview, ENT_QUOTES, 'UTF-8') !!}</pre>
                                    @foreach ($allHtmlFiles as $filename => $filedata)
                                        <pre x-show="selectedHtmlFile === '{{ $filename }}'" class="whitespace-pre-wrap break-words">{!! htmlspecialchars($filedata['content'], ENT_QUOTES, 'UTF-8') !!}</pre>
                                    @endforeach
                                </div>
                            </div>
                            <div
                                class="w-full lg:w-64 border border-slate-200 rounded-xl bg-white overflow-hidden flex flex-col shadow-sm">
                                <div class="p-3 bg-slate-50 border-b border-slate-200">
                                    <p class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">File HTML
                                    </p>
                                </div>
                                <div class="flex-1 overflow-y-auto p-2 space-y-1">
                                    <button @click="selectedHtmlFile = 'index.html'"
                                        :class="selectedHtmlFile === 'index.html' ?
                                            'bg-indigo-50 text-indigo-700 border-indigo-200' :
                                            'text-slate-600 hover:bg-slate-50'"
                                        class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium border border-transparent transition-all">📄
                                        index.html</button>
                                    @foreach ($allHtmlFiles as $filename => $filedata)
                                        <button @click="selectedHtmlFile = '{{ $filename }}'"
                                            :class="selectedHtmlFile === '{{ $filename }}' ?
                                                'bg-indigo-50 text-indigo-700 border-indigo-200' :
                                                'text-slate-600 hover:bg-slate-50'"
                                            class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium border border-transparent transition-all truncate"
                                            title="{{ $filename }}">📄 {{ $filename }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- ROW 2: Generated Blade/PHP Files (Code Only) --}}
                        <div class="flex flex-col lg:flex-row gap-4 h-[500px]">
                            <div
                                class="flex-1 border border-slate-200 rounded-xl bg-slate-900 overflow-hidden flex flex-col shadow-lg">
                                <div
                                    class="p-3 bg-slate-800 border-b border-slate-700 flex justify-between items-center">
                                    <p class="text-[11px] font-bold text-slate-300 uppercase tracking-wider">Generated:
                                        <span x-text="selectedGenFile" class="text-purple-400"></span>
                                    </p>
                                    <div x-data="{ saved: false, saving: false }" class="flex items-center gap-2">
                                        <div x-show="saving"
                                            class="flex items-center gap-1.5 text-[10px] text-slate-400">
                                            <i class="fa-solid fa-circle-notch fa-spin"></i> Saving...
                                        </div>
                                        <button type="button"
                                            @click="saving = true; $wire.updateGeneratedFile().then(() => { saved = true; saving = false; setTimeout(() => { saved = false; }, 2000); })"
                                            class="px-2.5 py-1 bg-emerald-600/80 hover:bg-emerald-600 text-white text-[10px] font-bold rounded-lg transition-all">
                                            <i class="fa-solid fa-floppy-disk mr-1"></i> <span
                                                x-text="saved ? '✓ Saved' : 'Save'"></span>
                                        </button>
                                    </div>
                                </div>
                                <textarea wire:model="editingFileContent"
                                    class="flex-1 font-mono text-[11px] p-4 bg-slate-900 text-emerald-400 border-0 resize-none focus:outline-none focus:ring-0 focus:bg-slate-800 transition-colors leading-relaxed"
                                    placeholder="Pilih file untuk diedit..."></textarea>
                            </div>
                            <div
                                class="w-full lg:w-64 border border-slate-200 rounded-xl bg-white overflow-hidden flex flex-col shadow-sm">
                                <div class="p-3 bg-slate-50 border-b border-slate-200">
                                    <p class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Generated
                                        Files</p>
                                </div>
                                <div class="flex-1 overflow-y-auto p-2 space-y-1">
                                    @foreach ($generatedFiles as $filename => $content)
                                        <button
                                            @click="selectedGenFile = '{{ $filename }}'; loadingCode = true; $wire.loadFileForEditing('{{ $filename }}').then(() => { loadingCode = false; })"
                                            :class="selectedGenFile === '{{ $filename }}' ?
                                                'bg-indigo-50 text-indigo-700 border-indigo-200' :
                                                'text-slate-600 hover:bg-slate-50'"
                                            class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium border border-transparent transition-all truncate"
                                            title="{{ $filename }}">
                                            @if (strpos($filename, '.blade.php') !== false)
                                                🎨 {{ $filename }}
                                            @elseif ($filename === 'theme.php')
                                                ⚙️ {{ $filename }}
                                            @else
                                                📄 {{ $filename }}
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- PANEL FOOTER NAVIGATION --}}
            <div class="bg-slate-50 px-6 py-5 border-t border-slate-100 flex items-center justify-between">
                @if ($step > 1)
                    <button type="button" wire:click="prevStep"
                        class="inline-flex items-center gap-2 px-6 py-2.5 border border-slate-200 hover:border-slate-300 text-slate-600 hover:text-slate-800 text-sm font-bold rounded-xl hover:bg-white transition-all">
                        <i class="fa-solid fa-arrow-left text-xs"></i> Sebelumnya
                    </button>
                @else
                    <div class="text-xs text-slate-400 font-medium italic"><i
                            class="fa-solid fa-info-circle mr-1"></i> Lengkapi data untuk lanjut</div>
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

    </div>
