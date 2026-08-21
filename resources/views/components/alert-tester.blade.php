<div class="max-w-7xl mx-auto mt-4 mb-4 p-6 bg-gray-50 rounded-xl border border-gray-200">
    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
        </svg>
        Panel Testing Session Status
    </h3>

    <p class="text-sm text-gray-500 mb-4">Klik tombol di bawah ini untuk memicu session flash dan melihat tampilan alertnya.</p>

    <div class="grid grid-cols-1 sm:grid-cols-5 gap-4">
    {{-- Test Success --}}
        <button
            type="button"
            @click="$dispatch('swal', { icon: 'success', title: 'Berhasil!', text: 'File PDF berhasil diupload dan disimpan.' })"
            class="flex items-center justify-center gap-2 px-2 py-2 bg-green-600 hover:bg-green-700 text-white font-small rounded-sm transition-colors shadow-sm"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            Toast Success
        </button>

        {{-- Test Error --}}
        <button
            type="button"
            @click="$dispatch('swal', { icon: 'error', title: 'Gagal!', text: 'Ukuran file melebihi batas maksimal 20MB.' })"
            class="flex items-center justify-center gap-2 px-2 py-2 bg-red-600 hover:bg-red-700 text-white font-small rounded-sm transition-colors shadow-sm"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            Toast Error
        </button>

        {{-- Test Info --}}
        <button
            type="button"
            @click="$dispatch('swal', { icon: 'info', title: 'Informasi', text: 'Harap pastikan format file adalah .pdf sebelum mengupload.' })"
            class="flex items-center justify-center gap-2 px-2 py-2 bg-blue-600 hover:bg-blue-700 text-white font-small rounded-sm transition-colors shadow-sm"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Toast Info
        </button>

        {{-- Test Warning --}}
        <button
            type="button"
            @click="$dispatch('swal', { icon: 'warning', title: 'Peringatan', text: 'File dengan nama ini sudah ada. File lama akan ditimpa.' })"
            class="flex items-center justify-center gap-2 px-2 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-small rounded-sm transition-colors shadow-sm"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            Toast Warning
        </button>

        {{-- Test Success --}}
        <a href="{{ route('admin.test.session', 'success') }}"
           type="button" class="flex items-center justify-center gap-2 p-2 bg-green-600 hover:bg-green-700 text-white font-small rounded-sm transition-colors shadow-sm">
            <i class="fas fa-check-circle"></i>
            Test Success
        </a>

        {{-- Test Error --}}
        <a href="{{ route('admin.test.session', 'error') }}"
           type="button" class="flex items-center justify-center gap-2 p-2 bg-red-600 hover:bg-red-700 text-white font-small rounded-sm transition-colors shadow-sm">
            <i class="fas fa-exclamation-circle"></i>
            Test Error
        </a>

        {{-- Test Info --}}
        <a href="{{ route('admin.test.session', 'info') }}"
           type="button" class="flex items-center justify-center gap-2 px-2 py-2 bg-blue-600 hover:bg-blue-700 text-white font-small rounded-sm transition-colors shadow-sm">
            <i class="fas fa-info-circle"></i>
            Test Info
        </a>

        {{-- Test Warning --}}
        <a href="{{ route('admin.test.session', 'warning') }}"
           type="button" class="flex items-center justify-center gap-2 px-2 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-small rounded-sm transition-colors shadow-sm">
            <i class="fas fa-exclamation-triangle"></i>
            Test Warning
        </a>

        {{-- Test Validation Errors --}}
        <a href="{{ route('admin.test.session', 'validation') }}"
           type="button" class="flex items-center justify-center gap-2 px-2 py-2 bg-orange-500 hover:bg-orange-600 text-white font-small rounded-sm transition-colors shadow-sm sm:col-span-2 lg:col-span-1">
            <i class="fas fa-list-ul"></i>
            Test Validation
        </a>
    </div>

</div>
