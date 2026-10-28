<div class="p-6 space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Generate Tema HTML</h1>
            <p class="text-sm text-gray-500 mt-1">Konversi template HTML menjadi tema SuryaCMS yang siap pakai</p>
        </div>
    </div>

    @if (session()->has('error'))
        <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-md text-sm text-red-700 flex items-start gap-3">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="font-semibold">Proses Gagal</p>
                <p class="mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border-l-4 border-green-500 rounded-md text-sm text-green-700 flex items-start gap-3">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="font-semibold">Berhasil</p>
                <p class="mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- KOLOM KIRI: FORM UPLOAD & GENERATE (2/3 width)               --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div class="lg:col-span-2">
            <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-violet-50">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Upload Template HTML
                    </h2>
                    <p class="text-xs text-gray-600 mt-1">Lengkapi informasi tema dan unggah file ZIP template Anda</p>
                </div>

                <form wire:submit.prevent="uploadAndGenerate" class="p-6 space-y-5">
                    <!-- Grid 2 kolom untuk informasi tema -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nama Tema -->
                        <div>
                            <label for="themeName" class="flex items-center gap-1.5 text-sm font-semibold text-gray-700 mb-1.5">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 14V7a4 4 0 014-4z"></path>
                                </svg>
                                Nama Tema <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="themeName" wire:model="themeName"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                placeholder="Contoh: Kreasi Modern">
                            @error('themeName') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            <p class="text-[11px] text-gray-500 mt-1">Akan digunakan sebagai slug folder & identifier tema</p>
                        </div>

                        <!-- Author -->
                        <div>
                            <label for="author" class="flex items-center gap-1.5 text-sm font-semibold text-gray-700 mb-1.5">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Author <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="author" wire:model="author"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                placeholder="Contoh: SuryaCMS Team">
                            @error('author') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Versi Tema -->
                        <div class="md:col-span-2">
                            <label for="themeVersion" class="flex items-center gap-1.5 text-sm font-semibold text-gray-700 mb-1.5">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Versi Tema <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="themeVersion" wire:model="themeVersion"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                placeholder="Contoh: 1.0.0">
                            @error('themeVersion') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Separator -->
                    <div class="border-t border-gray-100 pt-5">
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                            File ZIP Template <span class="text-red-500">*</span>
                        </label>

                        <!-- Custom File Drop Zone -->
                        <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-400 hover:bg-blue-50/30 transition-colors cursor-pointer">
                            <input type="file" id="zipFile" wire:model="zipFile" accept=".zip"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                onchange="this.parentElement.querySelector('.file-name').textContent = this.files[0]?.name || 'Klik atau seret file ZIP ke sini'">

                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Klik untuk pilih atau seret file ZIP</p>
                                    <p class="text-xs text-gray-500 mt-1 file-name">Maksimal 50MB • Hanya file .zip</p>
                                </div>
                            </div>
                        </div>

                        @error('zipFile') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror

                        <!-- Progress Upload -->
                        <div wire:loading wire:target="zipFile" class="mt-3">
                            <div class="flex items-center gap-2 text-sm text-blue-600">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Mengupload file...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="pt-4 border-t border-gray-100 flex items-center justify-between gap-4">
                        <p class="text-xs text-gray-500 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Baca panduan di sebelah kanan sebelum mengunggah
                        </p>

                        <button type="submit" wire:loading.attr="disabled" wire:target="uploadAndGenerate"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-violet-600 hover:from-blue-500 hover:to-violet-500 text-white font-semibold rounded-lg shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center gap-2">
                            <span wire:loading.remove wire:target="uploadAndGenerate" class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                Generate Tema
                            </span>
                            <span wire:loading wire:target="uploadAndGenerate" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Memproses...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- KOLOM KANAN: PANDUAN & SYARAT (1/3 width)                   --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Card: Syarat Wajib ZIP --}}
            <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-100 flex items-center gap-2">
                    <div class="w-7 h-7 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800">Syarat Wajib ZIP</h3>
                </div>
                <div class="p-4 space-y-3 text-sm">
                    <div class="flex items-start gap-2">
                        <span class="flex-shrink-0 w-5 h-5 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-[10px] font-bold">1</span>
                        <div>
                            <p class="text-gray-800 font-medium">Wajib ada <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs text-pink-600">index.html</code></p>
                            <p class="text-xs text-gray-500 mt-0.5">File utama yang akan dijadikan <code class="text-[10px] bg-gray-100 px-1 py-0.5 rounded">index.blade.php</code></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="flex-shrink-0 w-5 h-5 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-[10px] font-bold">2</span>
                        <div>
                            <p class="text-gray-800 font-medium">Path relatif harus benar</p>
                            <p class="text-xs text-gray-500 mt-0.5">Contoh: <code class="text-[10px] bg-gray-100 px-1 py-0.5 rounded text-pink-600">&lt;link href="css/style.css"&gt;</code></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="flex-shrink-0 w-5 h-5 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-[10px] font-bold">3</span>
                        <div>
                            <p class="text-gray-800 font-medium">Semua aset dalam 1 ZIP</p>
                            <p class="text-xs text-gray-500 mt-0.5">CSS, JS, gambar, font harus ikut di-zip bersama</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="flex-shrink-0 w-5 h-5 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-[10px] font-bold">4</span>
                        <div>
                            <p class="text-gray-800 font-medium">Ukuran maksimal <strong>50MB</strong></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card: Struktur ZIP yang Benar --}}
            <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-cyan-50 border-b border-blue-100 flex items-center gap-2">
                    <div class="w-7 h-7 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800">Struktur ZIP yang Benar</h3>
                </div>
                <div class="p-4 bg-slate-900 text-slate-300 font-mono text-[11px] leading-relaxed overflow-x-auto">
<pre class="whitespace-pre m-0"><span class="text-amber-400">📦 my-theme.zip</span>
<span class="text-slate-500">┣</span> <span class="text-emerald-400">📄 index.html</span>        <span class="text-slate-500"># ← Wajib ada</span>
<span class="text-slate-500">┣</span> <span class="text-blue-400">📁 css/</span>
<span class="text-slate-500">┃ ┗</span> <span class="text-blue-300">📄 style.css</span>
<span class="text-slate-500">┣</span> <span class="text-yellow-400">📁 js/</span>
<span class="text-slate-500">┃ ┗</span> <span class="text-yellow-300">📄 script.js</span>
<span class="text-slate-500">┣</span> <span class="text-violet-400">📁 images/</span>
<span class="text-slate-500">┃ ┗</span> <span class="text-violet-300">🖼️  hero.jpg</span>
<span class="text-slate-500">┗</span> <span class="text-cyan-400">📁 fonts/</span>
  <span class="text-slate-500">┗</span> <span class="text-cyan-300">🔤 custom.woff2</span></pre>
                    <p class="mt-3 text-slate-500 text-[10px] italic border-t border-slate-800 pt-2">
                        💡 Boleh dibungkus 1 folder pembungkus (misal: <code>template-v1/</code>)
                    </p>
                </div>
            </div>



        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- BAGIAN BAWAH: LIST SESSION / DRAFT TEMA                     --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h2 class="text-lg font-bold text-gray-800">Draf Tema Tersimpan</h2>
                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">
                    {{ count($sessions) }}
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                        <th class="px-6 py-3 text-left font-semibold">Session ID</th>
                        <th class="px-6 py-3 text-left font-semibold">Terakhir Diubah</th>
                        <th class="px-6 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sessions as $sess)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-xs text-gray-700 bg-gray-100 px-2 py-1 rounded">
                                    {{ $sess['session_id'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-xs">
                                <div class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($sess['created_at'])->format('d M Y, H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center space-x-2">
                                <a href="{{ route('admin.themes.make.editor', $sess['session_id']) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-semibold transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>

                                <button wire:click="deleteSession('{{ $sess['session_id'] }}')"
                                        wire:confirm="Apakah Anda yakin ingin menghapus draf ini? Tindakan ini tidak dapat dibatalkan."
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 text-red-700 hover:bg-red-100 rounded-lg text-xs font-semibold transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">Belum ada draf tema tersimpan</p>
                                    <p class="text-gray-400 text-xs mt-1">Upload template HTML pertama Anda untuk memulai</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-update nama file yang dipilih di drop zone
    document.addEventListener('DOMContentLoaded', () => {
        const fileInput = document.getElementById('zipFile');
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                const fileName = this.files[0]?.name;
                const fileNameEl = this.parentElement.querySelector('.file-name');
                if (fileName && fileNameEl) {
                    fileNameEl.textContent = fileName;
                    fileNameEl.classList.add('text-blue-600', 'font-medium');
                }
            });
        }
    });
</script>
@endpush
