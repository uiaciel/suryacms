<div>
    @if ($showPdfModal)
    {{-- Backdrop --}}
    <div class="fixed inset-0 z-40 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="closePdfModal"></div>

    {{-- Modal Container --}}
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl flex flex-col max-h-[90vh] overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-red-50 border border-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Manage PDF Files</h3>
                </div>
                <button type="button" wire:click="closePdfModal" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-1.5 transition-colors" aria-label="Close modal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Content (Scrollable) --}}
            <div class="flex-1 overflow-y-auto p-6 space-y-6">

                {{-- 1. Upload Section --}}
                <div class="space-y-4">
                    <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Upload New PDF
                    </h4>

                    {{-- File Input (Styled) --}}
                    <div class="relative">
                        <input
                            type="file"
                            id="pdf_file"
                            wire:model="pdfFile"
                            accept=".pdf"
                            class="block w-full text-sm text-gray-500
                                   file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0
                                   file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700
                                   hover:file:bg-blue-100 file:cursor-pointer cursor-pointer
                                   border border-gray-200 rounded-lg p-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition"
                        >
                        @error('pdfFile')
                            <p class="text-red-500 text-xs mt-2 flex items-center gap-1.5">
                                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- File Preview (Jika ada file dipilih) --}}
                    @if ($pdfFile)
                    <div class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded bg-red-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"></path>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $pdfFile->getClientOriginalName() }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($pdfFile->getSize() / 1024, 2) }} KB</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>

                    {{-- Upload Button --}}
                    <button
                        type="button"
                        wire:click="uploadPdf"
                        wire:loading.attr="disabled"
                        class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-lg transition-colors flex items-center justify-center gap-2 shadow-sm"
                    >
                        <span wire:loading.remove wire:target="uploadPdf" class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            Upload PDF
                        </span>
                        <span wire:loading wire:target="uploadPdf" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Uploading...
                        </span>
                    </button>
                    @endif
                </div>

                {{-- Divider --}}
                <div class="border-t border-gray-100"></div>

                {{-- 2. Gallery Section --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Your PDF Gallery
                        </h4>
                        <span class="text-xs font-medium text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $galleriesPdf->count() }} Files</span>
                    </div>

                    @if ($galleriesPdf->count())
                        <div class="space-y-2">
                            @foreach ($galleriesPdf as $pdf)
                            <div class="group flex items-center justify-between gap-4 p-3 bg-white border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-sm transition-all">

                                {{-- Left: Info --}}
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <div class="w-9 h-9 rounded bg-red-50 border border-red-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"></path>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate">{{ $pdf->name }}</p>
                                        <p class="text-xs text-gray-400 truncate">{{ basename($pdf->image_path) }}</p>
                                    </div>
                                </div>

                                {{-- Right: Actions --}}
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <button
                                        type="button"
                                        wire:click="insertPdfToContent({{ $pdf->id }})"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-md transition-colors"
                                        title="Insert to Content"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <a
                                        href="/storage/{{ $pdf->image_path }}"
                                        target="_blank"
                                        class="p-2 text-gray-500 hover:bg-gray-100 rounded-md transition-colors"
                                        title="View PDF"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                    <button
                                        type="button"
                                        wire:click="insertPdfToPage({{ $pdf->id }})"
                                        class="p-2 text-green-600 hover:bg-green-50 rounded-md transition-colors"
                                        title="Attach to Page"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Empty State --}}
                        <div class="text-center py-10 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-sm text-gray-500 font-medium">No PDFs uploaded yet</p>
                            <p class="text-xs text-gray-400 mt-1">Upload your first PDF using the form above.</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
    @endif
</div>
