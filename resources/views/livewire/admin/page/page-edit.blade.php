<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Section --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <div
                            class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                        </div>
                        {{ $titlePage }}
                    </h1>

                </div>

                {{-- Breadcrumb --}}
                <nav aria-label="breadcrumb" class="mt-4 sm:mt-0">
                    <ol class="flex flex-wrap gap-2 text-sm text-gray-600">
                        <li><a href="/admin" class="text-blue-600 hover:text-blue-700 hover:underline">Admin</a></li>
                        <li class="text-gray-400">/</li>
                        <li><a href="/admin/pages" class="text-blue-600 hover:text-blue-700 hover:underline">Pages</a>
                        </li>
                        <li class="text-gray-400">/</li>
                        <li class="text-gray-600 font-medium">{{ $titlePage }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <x-suryacms::session-status />

        @if ($is_builder)

            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center hover:shadow-md transition-shadow">
                <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Page Builder Content</h2>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                    This page was designed using the visual Page Builder. Standard editor is disabled to prevent
                    layout conflicts.
                </p>
                <a href="{{ route('admin.homepage.builder', $slug) }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                    <i class="fas fa-magic"></i>
                    Edit with Page Builder
                </a>
            </div>
        @else
            {{-- Main Form --}}
            <form wire:submit.prevent="updatePage" x-data="{ languageId: @entangle('language_id') }" class="space-y-6">

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- Main Content Column --}}
                    <div class="lg:col-span-2 space-y-6">

                        {{-- Page Information Section --}}
                        <div
                            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Page Information</h3>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                        Page Title
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="title" wire:model.live="title"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all hover:bg-white"
                                        placeholder="Enter an engaging page title...">
                                    @error('title')
                                        <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Multilingual Support --}}
                                @if ($setting->is_multilingual == 'Yes')
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label for="language_id"
                                                class="block text-sm font-medium text-gray-700 mb-2">
                                                Language
                                            </label>
                                            <select id="language_id" wire:model="language_id"
                                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                                <option value="">Select Language</option>
                                                @foreach ($languages as $language)
                                                    <option value="{{ $language->id }}">{{ $language->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('language_id')
                                                <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="translation_id"
                                                class="block text-sm font-medium text-gray-700 mb-2">
                                                Translate From
                                            </label>
                                            <select id="translation_id" wire:model="translation_id"
                                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                                <option value="">-- None --</option>
                                                <template x-data="{ translations: [] }" x-init="fetch(`{{ url('admin/pages/translations') }}/${$wire.language_id || ''}`).then(r => r.ok ? r.json() : []).then(data => translations = data).catch(() => translations = [])">
                                                    <template x-for="translation in translations"
                                                        :key="translation.id">
                                                        <option :value="translation.id" x-text="translation.title">
                                                        </option>
                                                    </template>
                                                </template>
                                            </select>
                                            @error('translation_id')
                                                <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>

                        {{-- Content Editor Section --}}
                        <div
                            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Page Content</h3>
                            </div>

                            <div class="space-y-3">
                                <div class="flex flex-wrap gap-2">

                                    <button type="button"
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2"
                                        onclick="Livewire.dispatch('openGalleryModal');">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        Insert Media
                                    </button>
                                    <button type="button"
                                        class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2"
                                        onclick="Livewire.dispatch('openYoutubeVideoModal');">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6z">
                                            </path>
                                        </svg>
                                        Insert Video
                                    </button>
                                </div>

                                <div wire:ignore
                                    class="border-4 border-dashed border-gray-300 rounded-xl overflow-hidden bg-gray-50 hover:bg-white transition-colors">
                                    <textarea id="content" wire:model="konten" name="konten" rows="12"
                                        class="w-full p-4 border-0 focus:ring-0 bg-transparent text-gray-800 resize-none"
                                        placeholder="Start typing your page content here... You can use bold, italic, lists, and more...">{{ old('konten', $konten) }}</textarea>
                                </div>

                                @error('konten')
                                    <p class="text-red-500 text-sm flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- PDF Section --}}

                        <div
                            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">Attach File PDF</h3>
                                </div>
                                <button type="button" wire:click="openPdfModal"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z">
                                        </path>
                                    </svg>
                                    Upload PDF
                                </button>
                            </div>
                            @if ($pdf)
                                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div class="flex items-center gap-3 flex-1">
                                            <div
                                                class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-6 h-6 text-green-600" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-xs font-medium text-green-600 uppercase tracking-wide">
                                                    📄 Attached PDF</p>
                                                <p class="text-sm text-gray-700 truncate break-all">
                                                    {{ basename($pdf) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ $pdf }}" target="_blank"
                                            class="flex-1 py-2 px-3 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded transition-colors text-center">
                                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                </path>
                                            </svg>
                                            View
                                        </a>
                                        <button type="button" wire:click="removePdf"
                                            class="flex-1 py-2 px-3 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded transition-colors">
                                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>

                    {{-- Right Column: Page Settings & Actions --}}
                    <div class="space-y-6">

                        {{-- Status & Publishing Settings --}}
                        <div
                            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3 mb-5">
                                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Publication Settings</h3>
                            </div>

                            <div class="space-y-4">

                                <div>
                                    <label for="status"
                                        class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select id="status" wire:model="status"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                                        <option value="Publish">✓ Publish</option>
                                        <option value="Draft">✎ Draft</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="datepublish"
                                        class="block text-sm font-medium text-gray-700 mb-2">Publish Date</label>
                                    <input type="date" id="datepublish" wire:model="datepublish"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                                    @error('datepublish')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Save & Cancel Buttons --}}
                        <div class="space-y-3">
                            <button type="submit"
                                class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2 group">
                                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Update Page
                            </button>

                            <a href="/admin/pages"
                                class="w-full py-3 px-4 bg-gray-200 hover:bg-gray-300 text-gray-900 font-semibold rounded-xl transition-colors text-center block">
                                Cancel
                            </a>
                        </div>

                        {{-- Info Card --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <div class="flex gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-blue-900">Pro Tip</p>
                                    <p class="text-xs text-blue-700 mt-1">Use multimedia content to make your pages
                                        more engaging. You can add images and videos.</p>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </form>

        @endif

    </div>

    {{-- Modals --}}
    @include('suryacms::livewire.admin.pdf-modal')
    @livewire('suryacms::admin.image-gallery-modal')
    @livewire('suryacms::admin.youtube-video-modal')

    @push('scripts')
        <script
            src="https://cdn.tiny.cloud/1/{{ env('TINY_EDITOR_KEY', 'plcsua64qzb9xyaxabkhv7wtjcpr0vzounlnexchn12g0x7a') }}/tinymce/7/tinymce.min.js"
            referrerpolicy="origin"></script>

        @php
            $themeStyles = get_theme_config('assets.styles') ?? [];

            // Mengolah path agar menjadi URL penuh
            $cssUrls = array_map(function ($path) {
                // Jika path adalah eksternal (http/https), biarkan. Jika lokal, gunakan themeAsset
                if (filter_var($path, FILTER_VALIDATE_URL)) {
                    return $path;
                }
                return themeAsset($path);
            }, $themeStyles);

            // Mengubah array menjadi string dipisahkan koma
            $contentCss = implode(',', $cssUrls);
        @endphp

        <script>
            function initTinyMCE() {
                console.log("Initializing TinyMCE");

                // Tunggu sampai tinymce tersedia
                if (typeof tinymce === 'undefined') {
                    console.error('TinyMCE library not loaded');
                    setTimeout(initTinyMCE, 500);
                    return;
                }

                tinymce.remove();

                tinymce.init({
                    selector: '#content',
                    skin: 'oxide',
                    content_css: "{{ $contentCss }}",
                    height: 500,
                    plugins: 'code paste link fullscreen lists',
                    menubar: false,
                    toolbar: 'undo redo | bold italic underline link  | alignleft aligncenter alignright | bullist numlist | code fullscreen',
                    paste_data_images: true,
                    setup: function(editor) {
                        editor.on('change', function(e) {
                            if (@this !== undefined) {
                                @this.set('konten', editor.getContent());
                            }
                        });
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', () => {
                if (document.getElementById('content')) {
                    setTimeout(initTinyMCE, 500);
                }
            });

            // Re-initialize when Livewire updates the page
            if (typeof Livewire !== 'undefined') {
                Livewire.hook('message.processed', (message, component) => {
                    console.log('Livewire update processed, reinitializing TinyMCE');
                    setTimeout(initTinyMCE, 300);
                });
            }

            // Handle image insertion
            if (typeof Livewire !== 'undefined') {
                Livewire.on('imageSelectedFromGallery', (data) => {
                    console.log('Image insertion event received:', data);
                    const imageData = Array.isArray(data) ? data[0] : data;
                    if (imageData && imageData.url && typeof tinymce !== 'undefined') {
                        const editor = tinymce.get('content');
                        if (editor) {
                            const imgHtml = '<img src="' + imageData.url +
                                '" alt="Gallery Image" style="max-width: 100%; height: auto;" />';
                            editor.execCommand('mceInsertContent', false, imgHtml);
                        }
                    }
                });

                Livewire.on('videoSelectedFromYoutube', (data) => {
                    console.log('Video insertion event received:', data);
                    const videoData = Array.isArray(data) ? data[0] : data;
                    if (videoData && videoData.embedHtml && typeof tinymce !== 'undefined') {
                        const editor = tinymce.get('content');
                        if (editor) {
                            editor.execCommand('mceInsertContent', false, videoData.embedHtml);
                        }
                    }
                });

                Livewire.on('pdfSelectedFromGallery', (data) => {
                    console.log('PDF Event dari Galeri:', data);

                    const pdfData = Array.isArray(data) ? data[0] : data;

                    if (pdfData && pdfData.url) {

                        setTimeout(() => {
                            const editor = tinymce.get('content');
                            if (editor) {

                                let pdfUrl = pdfData.url;
                                if (!pdfUrl.startsWith('http://') && !pdfUrl.startsWith('https://')) {
                                    if (pdfUrl.startsWith('/')) {
                                        pdfUrl = window.location.origin + pdfUrl;
                                    } else {
                                        pdfUrl = window.location.origin + '/' + pdfUrl;
                                    }
                                }

                                const pdfHtml =
                                    `<div class="pdf-container my-3"><iframe src="${pdfUrl}" width="100%" height="600px" frameborder="0" sandbox=""></iframe><p class="mt-2"><a href="${pdfUrl}" target="_blank" class="text-blue-600 underline">Download PDF</a></p></div>`;
                                editor.execCommand('mceInsertContent', false, pdfHtml);
                                console.log('✅ PDF Embed dengan full URL disisipkan:', pdfUrl);
                            } else {
                                console.warn('Editor belum siap untuk menyisipkan PDF');
                            }
                        }, 100);
                    }
                });
            }
        </script>
    @endpush
</div>
