<div>
    @if ($showPdfModal)
    <div class="fixed inset-0 z-40 bg-black/50 transition-opacity" wire:click="closePdfModal"></div>

    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl flex flex-col max-h-[90vh]">

            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Upload and Manage PDF</h3>
                </div>
                <button type="button"
                    class="text-gray-400 hover:text-gray-600 transition-colors p-1"
                    wire:click="closePdfModal">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-6 space-y-6">

                <!-- Upload Section -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Upload New PDF
                    </h4>

                    <div class="space-y-4">
                        <div>
                            <label for="pdf_file" class="block text-sm font-medium text-gray-700 mb-2">
                                Select PDF File
                            </label>
                            <input
                                type="file"
                                id="pdf_file"
                                wire:model="pdfFile"
                                accept=".pdf"
                                class="block w-full px-4 py-3 text-sm border-2 border-dashed border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer hover:border-blue-400 transition"
                            >
                            @error('pdfFile')
                                <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- File Info -->
                        @if ($pdfFile)
                        <div class="p-4 bg-white rounded-lg border border-green-200 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded bg-red-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $pdfFile->getClientOriginalName() }}</p>
                                    <p class="text-xs text-gray-500">{{ round($pdfFile->getSize() / 1024, 2) }} KB</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        @endif

                        <!-- Upload Button -->
                        <button
                            type="button"
                            wire:click="uploadPdf"
                            wire:loading.attr="disabled"
                            class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2"
                        >
                            <span wire:loading.remove>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Upload PDF
                            </span>
                            <span wire:loading>
                                <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Uploading...
                            </span>
                        </button>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"></path>
                        </svg>
                        Your PDF Gallery
                    </h4>

                    <p class="text-sm text-gray-500 mb-4">Browse and select from your previously uploaded PDFs.</p>

                    @if ($galleriesPdf->count())
                    <div class="space-y-3">
                        @foreach ($galleriesPdf as $pdf)
                        <div class="bg-white rounded-lg p-4 border border-gray-200 hover:border-blue-300 transition">
                            <div class="flex items-start justify-between gap-4 mb-3">
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-10 h-10 rounded bg-red-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"></path>
                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 line-clamp-1">{{ $pdf->name }}</p>
                                        <p class="text-xs text-gray-500">{{ basename($pdf->image_path) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <button
                                    type="button"
                                    wire:click="insertPdfToContent({{ $pdf->id }})"
                                    class="py-2 px-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors flex items-center justify-center gap-1"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Add to Content
                                </button>
                                <a href="/storage/{{ $pdf->image_path }}" target="_blank"
                                    class="py-2 px-3 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded transition-colors flex items-center justify-center gap-1 text-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    View PDF
                                </a>
                                <button
                                    type="button"
                                    wire:click="insertPdfToPage({{ $pdf->id }})"
                                    class="py-2 px-3 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded transition-colors flex items-center justify-center gap-1"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Attach to Page
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-sm text-gray-500">No PDFs uploaded yet</p>
                    </div>
                    @endif
                </div>

            </div>

            <!-- Footer -->
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-2xl">
                <button
                    type="button"
                    wire:click="closePdfModal"
                    class="w-full py-2 px-6 bg-white border border-gray-300 text-gray-900 font-medium rounded-lg hover:bg-gray-50 transition-colors"
                >
                    Close
                </button>

        </div>
    </div>
    @endif
</div>
