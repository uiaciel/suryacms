<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-6">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <x-suryacms::toast-alert />
    <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        {{ $titlePage }}
                    </h1>

                </div>
                <nav aria-label="breadcrumb" class="mt-4 sm:mt-0">
                    <ol class="flex flex-wrap gap-2 text-sm text-gray-600">
                        <li><a href="/admin" class="text-blue-600 hover:text-blue-700 hover:underline">Admin</a></li>
                        <li class="text-gray-400">/</li>
                        <li><a href="/admin/posts" class="text-blue-600 hover:text-blue-700 hover:underline">Posts</a></li>
                        <li class="text-gray-400">/</li>
                        <li class="text-gray-600 font-medium">{{ $titlePage }}</li>
                    </ol>
                </nav>
            </div>
    </div>

   <x-suryacms::session-status />

    {{-- Main Form (assuming this is wrapped in a Livewire component for update) --}}
    <form wire:submit.prevent="updatePost" x-data="{ languageId: @entangle('language_id') }">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content Column --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Post Information</h3>
                        </div>

                    <div class="space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">Title</label>
                            <input type="text" id="title" wire:model="title" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter post title">
                            @error('title')
                            <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        @if ($setting->is_multilingual == 'Yes')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="language_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Language
                                    </label>
                                    <select
                                        id="language_id"
                                        wire:model="language_id"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    >
                                        @foreach ($languages as $lang)
                                            <option value="{{ $lang->id }}">{{ $lang->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="translation_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Translate From
                                    </label>
                                    <select
                                        id="translation_id"
                                        wire:model="translation_id"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    >
                                        <option value="">-- None --</option>
                                        @foreach ($posts as $post)
                                            <option value="{{ $post->id }}">{{ $post->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">

                    <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Post Content</h3>
                    </div>

                     <div class="space-y-3">

                        <div class="mb-3 flex gap-2">
                                    <button type="button" class="flex-1 px-4 py-2 border border-blue-500 text-blue-600 rounded-lg hover:bg-blue-50 font-semibold"
                                        wire:click="$dispatch('openGalleryModal')"><i class="fas fa-images mr-2"></i>Insert
                                        Media</button>
                                    <button type="button" class="flex-1 px-4 py-2 border border-red-500 text-red-600 rounded-lg hover:bg-red-50 font-semibold"
                                        wire:click="$dispatch('openYoutubeVideoModal')"><i
                                            class="fab fa-youtube mr-2"></i>Insert Video</button>
                            </div>

                            <div wire:ignore>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Content</label>
                                <textarea id="content" wire:model="konten" name="konten"
                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('konten', $konten) }}</textarea>
                                @error('konten')
                                <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                    </div>

            </div>

            {{-- Sidebar Column --}}
            <div class="space-y-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-5">
                            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Publication Settings</h3>
                        </div>
                    <div class="space-y-4">
                        <div>
                            <label for="datepublish" class="block text-sm font-semibold text-gray-700 mb-2">Date Publish</label>
                            <input type="date" id="datepublish" wire:model="datepublish" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                            <select id="category_id" wire:model="category_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                            <label for="feature" class="text-sm font-medium text-gray-700">Feature Post</label>
                            <button type="button"
                                @click="$wire.set('feature', $wire.feature === 'Yes' ? 'No' : 'Yes')"
                                :class="$wire.feature === 'Yes' ? 'bg-blue-600' : 'bg-gray-200'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none">
                                <span :class="$wire.feature === 'Yes' ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                            </button>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                            <label for="flash" class="text-sm font-medium text-gray-700">Flash Post</label>
                            <button type="button"
                                @click="$wire.set('flash', $wire.flash === 'Yes' ? 'No' : 'Yes')"
                                :class="$wire.flash === 'Yes' ? 'bg-yellow-500' : 'bg-gray-200'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none">
                                <span :class="$wire.flash === 'Yes' ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                            </button>
                        </div>

                        <div hidden>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                            <select id="status" wire:model="status" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="Publish">Publish</option>
                                <option value="Draft">Draft</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">SEO & Source</h3>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="tags" class="block text-sm font-semibold text-gray-700">Tags / Keywords</label>
                                <button type="button" class="text-xs px-3 py-1 bg-purple-100 text-purple-700 hover:bg-purple-200 rounded-full font-medium transition-colors"
                                    wire:click="generateKeywords"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-not-allowed"
                                    title="Auto-generate keywords from title and content">
                                    <span wire:loading.remove wire:target="generateKeywords"><i class="fas fa-magic"></i> Auto-Generate</span>
                                    <span wire:loading wire:target="generateKeywords"><i class="fas fa-spinner animate-spin"></i> Generating...</span>
                                </button>
                            </div>
                            <input type="text" id="tags" wire:model="tags" placeholder="Keyword1, Keyword2, Tema, Lainnya" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Separate keywords with commas.</p>
                            @error('tags')
                            <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div x-data="{
                                favicon: '',
                                domain: '',
                                alertMessage: '',
                                alertType: '',
                                showAlert: false,
                                // helper to set alert from browser events
                                setAlert(detail) { this.alertType = detail.icon || 'info'; this.alertMessage = detail.text || detail.title || 'Info'; this.showAlert = true; setTimeout(() => this.showAlert = false, 4000); }
                            }"
                            @swal.window="(e) => { if (e && e.detail) this.setAlert(e.detail) }"
                            @content-scraped.window="(e) => { /* handled by entangle in card */ }">

                            <label for="source_url" class="block text-sm font-semibold text-gray-700 mb-2">Source URL</label>
                            <div>
                                <div class="flex gap-2">
                                    <input x-ref="sourceInput" type="text" id="source_url" placeholder="https://news.detik.com/berita/judul-berita-dari-url" wire:model.defer="source_url" name="source_url" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        x-on:input.debounce.500ms="
                                            const v = $event.target.value;
                                            if (v) {
                                                try {
                                                    const url = new URL(v);
                                                    domain = url.hostname;
                                                    favicon = url.origin + '/favicon.ico';
                                                    $wire.set('source_favicon', favicon);
                                                    $wire.set('source_title', domain);
                                                } catch (err) {
                                                    // ignore invalid URL until user finishes typing
                                                }
                                            }
                                        ">

                                    <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold whitespace-nowrap transition-colors"
                                        x-bind:disabled="$wire.get('source_url') === ''"
                                        wire:click="scrapeContent"
                                        wire:loading.attr="disabled"
                                        wire:loading.class="opacity-50 cursor-not-allowed">
                                        <span wire:loading.remove wire:target="scrapeContent"><i class="fas fa-search"></i></span>
                                    </button>

                                </div>
                                <span wire:loading wire:target="scrapeContent"><i class="fas fa-spinner animate-spin"></i> Scraping...</span>
                                <p class="text-xs text-gray-500 mt-1">Support detik.com, cnn, tribunnews.</p>

                            </div>

                            <div class="mt-3">
                                <template x-if="showAlert">
                                    <div x-bind:class="{
                                        'bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg': alertType === 'success',
                                        'bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg': alertType === 'error',
                                        'bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg': alertType === 'info' || alertType === 'warning'
                                    }" role="alert">
                                        <strong x-text="alertType.toUpperCase() + ':'"></strong>
                                        <span x-text="alertMessage"></span>
                                    </div>
                                </template>
                                @error('source_url')
                                <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div hidden>
                            <label for="source_favicon" class="block text-sm font-semibold text-gray-700 mb-2">Source Favicon</label>
                            <input type="text" id="source_favicon" placeholder="Terisi otomatis" wire:model="source_favicon" name="source_favicon" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div hidden>
                            <label for="source_title" class="block text-sm font-semibold text-gray-700 mb-2">Source Domain</label>
                            <input type="text" id="source_title" placeholder="Terisi otomatis" wire:model="source_title" name="source_title" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div x-show="$wire.source_title" x-cloak class="mt-3 flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100 transition-all animate-fadeIn">
                            <div class="flex-shrink-0 w-10 h-10 bg-white rounded-lg shadow-sm border border-gray-100 flex items-center justify-center overflow-hidden">
                                <template x-if="$wire.source_favicon">
                                    <img :src="$wire.source_favicon" class="w-6 h-6 object-contain" alt="Source Favicon" onerror="this.onerror=null;this.src='https://www.google.com/s2/favicons?domain='+$wire.source_title;">
                                </template>
                                <template x-if="!$wire.source_favicon">
                                    <i class="fas fa-globe text-gray-400"></i>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Detected Source</p>
                                <p class="text-sm font-bold text-gray-900 truncate" x-text="$wire.source_title"></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="w-full space-y-3">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">Publish Post</button>
                    <button type="button" wire:click="saveDraft" class="w-full px-4 py-2 border border-gray-300 text-gray-700 bg-white rounded-lg hover:bg-gray-50 font-semibold">Save Draft</button>
                </div>

            </div>

        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Scraped Content</h3>
                    </div>
                    @livewire('suryacms::admin.post.scraped-content-display', ['scrapeResult' => $this->scrapeResult])
                </div>
            </div>

        </div>

    </form>

         @livewire('suryacms::admin.image-gallery-modal')
         @livewire('suryacms::admin.youtube-video-modal')

    @push('scripts')
    <script src="https://cdn.tiny.cloud/1/{{ env('TINY_EDITOR_KEY', 'plcsua64qzb9xyaxabkhv7wtjcpr0vzounlnexchn12g0x7a') }}/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        const server_image_upload_handler = (blobInfo, progress) => new Promise((resolve, reject) => {
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', '{{ route('admin.upload') }}');
                        xhr.setRequestHeader('X-CSRF-Token', '{{ csrf_token() }}');

                        xhr.upload.onprogress = (e) => {
                            progress(e.loaded / e.total * 100);
                        };

                        xhr.onload = () => {
                            if (xhr.status === 403 || xhr.status < 200 || xhr.status >= 300) {
                                reject('HTTP Error: ' + xhr.status);
                                return;
                            }

                            const json = JSON.parse(xhr.responseText);
                            if (!json || typeof json.location != 'string') {
                                reject('Invalid JSON: ' + xhr.responseText);
                                return;
                            }

                            resolve(json.location);
                        };

                        xhr.onerror = () => {
                            reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
                        };

                        const formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());
                        xhr.send(formData);
                    });

                    function initTinyMCE() {
                        console.log("initTinyMCE dipanggil");
                        tinymce.remove();

                        tinymce.init({
                            selector: '#content',
                            skin: 'bootstrap',
                            height: 500,
                            plugins: 'code advlist autolink lists link image charmap anchor searchreplace visualblocks fullscreen insertdatetime media table',
                            toolbar1: 'customGalleryButton image youtubeVideo',
                            toolbar2: 'fontfamily styles | link bold italic underline forecolor backcolor | numlist bullist alignleft aligncenter alignright alignjustify table code preview',
                            menubar: false,
                            image_title: true,
                            convert_urls: true,
                            relative_urls: false,
                            remove_script_host: false,
                            extended_valid_elements: 'iframe[src|width|height|frameborder|allow|allowfullscreen|sandbox]',
                            protect: [/<iframe[\s\S]*?<\/iframe>/g],
                            image_class_list: [
                                { title: 'Image Responsive', value: 'img-fluid' },
                                { title: 'Responsive Rounded-4', value: 'img-fluid rounded-4' },
                                { title: 'Responsive Rounded-5', value: 'img-fluid rounded-5' },
                                { title: 'Thumbnail', value: 'img-thumbnail' }
                            ],
                            paste_data_images: true,
                            automatic_uploads: true,
                            file_picker_types: 'image',
                            images_upload_handler: server_image_upload_handler,
                            file_picker_callback: function(cb, value, meta) {
                                if (meta.filetype === 'image') {
                                    const input = document.createElement('input');
                                    input.setAttribute('type', 'file');
                                    input.setAttribute('accept', 'image/*');
                                    input.onchange = function() {
                                        const file = this.files[0];
                                        const reader = new FileReader();
                                        reader.onload = function() {
                                            const id = 'blobid' + (new Date()).getTime();
                                            const blobCache = tinymce.activeEditor.editorUpload.blobCache;
                                            const base64 = reader.result.split(',')[1];
                                            const blobInfo = blobCache.create(id, file, base64);
                                            blobCache.add(blobInfo);
                                            cb(blobInfo.blobUri(), { title: file.name, alt: '' });
                                        };
                                        reader.readAsDataURL(file);
                                    };
                                    input.click();
                                }
                            },

                            mobile: {
                                toolbar_mode: 'floating',
                            },
                            contextmenu: 'selectall | copy paste | link image ',
                            setup: function(editor) {

                                editor.ui.registry.addButton('youtubeVideo', {
                                    text: 'YouTube Video',
                                    tooltip: 'Insert YouTube Video',
                                    icon: 'embed',
                                    onAction: function() {
                                        @this.dispatch('openYoutubeVideoModal');
                                    }
                                }),

                                editor.on('PastePostProcess', function(e) {
                                const pastedText = e.node.textContent.trim();

                                const youtubeRegex =
                                /(?:https?:\/\/)?(?:www\.)?(?:m\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=|embed\/|v\/|)([a-zA-Z0-9_-]{11})/;
                                const match = pastedText.match(youtubeRegex);

                                if (match) {
                                const videoId = match[1];
                                if (videoId) {
                                const embedHtml = `<div class="ratio ratio-16x9 my-3"><iframe src="https://www.youtube.com/embed/${videoId}"
                                        frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen></iframe></div>`;
                                editor.execCommand('mceInsertContent', false, embedHtml);
                                e.preventDefault();
                                console.log("✅ Video YouTube dimasukkan:", pastedText);
                                return;
                                }
                                }

                                const isLocalPdfPath = pastedText.startsWith('/storage/') && pastedText.toLowerCase().endsWith('.pdf');
                                if (isLocalPdfPath) {
                                    const baseUrl = window.location.origin;
                                    const fullUrl = baseUrl + pastedText;
                                    const pdfHtml = `<div class="pdf-container my-3"><iframe src="${fullUrl}" width="100%" height="600px" frameborder="0" sandbox=""></iframe><p class="mt-2"><a href="${fullUrl}" target="_blank" class="text-blue-600 underline">Download PDF</a></p></div>`;
                                    editor.execCommand('mceInsertContent', false, pdfHtml);
                                    e.preventDefault();
                                    console.log("✅ PDF lokal dimasukkan dengan full URL:", fullUrl);
                                    return;
                                }

                                const isLocalStoragePath = pastedText.startsWith('/storage/') && pastedText.match(/\.(jpeg|jpg|png|gif|webp)$/i);
                                if (isLocalStoragePath) {
                                const baseUrl = "{{ asset('') }}".replace(/\/$/, '');
                                const fullUrl = baseUrl + pastedText;
                                const imgTag = `<img src="${fullUrl}" class="img-fluid" alt="Pasted Image">`;
                                editor.execCommand('mceInsertContent', false, imgTag);
                                e.preventDefault();
                                console.log("✅ Gambar lokal dimasukkan dari teks:", fullUrl);
                                return;
                                }

                                if (e.clipboardData && e.clipboardData.files.length > 0) {
                                console.log("Deteksi paste file gambar dari clipboardData.");
                                } else if (e.clipboardData && e.clipboardData.getData('text/plain')) {
                                const clipboardText = e.clipboardData.getData('text/plain').trim();
                                const isLocalStoragePathFromClipboard = clipboardText.startsWith('/storage/') &&
                                clipboardText.match(/\.(jpeg|jpg|png|gif|webp)$/i);

                                if (isLocalStoragePathFromClipboard && clipboardText !== pastedText) {
                                const baseUrl = "{{ asset('') }}".replace(/\/$/, '');
                                const fullUrl = baseUrl + clipboardText;
                                const imgTag = `<img src="${fullUrl}" class="img-fluid" alt="Pasted Image">`;
                                editor.execCommand('mceInsertContent', false, imgTag);
                                e.preventDefault();
                                console.log("✅ Gambar lokal dimasukkan dari clipboardData teks:", fullUrl);
                                }
                                }
                                });

                                editor.ui.registry.addButton('customGalleryButton', {
                                    text: 'Choose From Gallery',
                                    tooltip: 'Choose From Gallery',
                                    icon: 'gallery',
                                    onAction: function() {
                                        @this.dispatch('openGalleryModal');
                                    }
                                });

                                editor.on('init change', function() {
                                    if (window.selectedImageUrlForTinyMCE) {
                                        this.execCommand('mceInsertContent', false, '<img src="' + window.selectedImageUrlForTinyMCE + '" alt="Gambar dari Galeri" class="img-fluid" />');
                                        console.log('Gambar tertunda berhasil disisipkan:', window.selectedImageUrlForTinyMCE);
                                        window.selectedImageUrlForTinyMCE = null;
                                    }
                                });

                                editor.on('change', function(e) {
                                    @this.set('konten', editor.getContent());
                                });
                            },
                            init_instance_callback: function(editor) {
                                let content = editor.getContent();
                                const target = editor.editorContainer.querySelector('.tox-toolbar__group button[aria-label="Insert/edit image"]');

                                if (target) {
                                    const icon = target.querySelector('.tox-icon');
                                    if (icon) {
                                        const label = document.createElement('span');
                                        label.className = 'tox-tbtn__select-label';
                                        label.textContent = 'Upload Image';

                                        target.appendChild(label);
                                        target.style.width = 'auto';
                                    }
                                }

                                content = content
                                    .replace(/\.\.\/\.\.\/\.\.\//g, '/')
                                    .replace(/\.\.\/\.\.\//g, '/')
                                    .replace(/\.\.\//g, '/');
                                editor.setContent(content);
                            }
                        });
                    }

                    document.addEventListener('DOMContentLoaded', () => {
                        initTinyMCE();
                    });

                    document.addEventListener('livewire:updated', (event) => {
                        console.log('📡 Livewire updated, syncing TinyMCE...');

                        setTimeout(() => {
                            const editor = tinymce.get('content');
                            if (editor) {

                                const libeireKonten = @this.get('konten');
                                const editorContent = editor.getContent();

                                if (libeireKonten && libeireKonten !== editorContent) {
                                    console.log('✅ Syncing content to TinyMCE editor');
                                    editor.setContent(libeireKonten);
                                }
                            }
                        }, 100);
                    });

                    Livewire.hook('message.processed', (message, component) => {
                        console.log('Livewire processed update, re-initialize TinyMCE');
                        initTinyMCE();

                        if (window.selectedImageUrlForTinyMCE) {
                            const editor = tinymce.get('content');
                            if (editor) {
                                editor.execCommand('mceInsertContent', false, '<img src="' + window.selectedImageUrlForTinyMCE + '" alt="Gambar dari Galeri" class="img-fluid" />');
                                console.log('Gambar disisipkan setelah Livewire render:', window.selectedImageUrlForTinyMCE);
                                window.selectedImageUrlForTinyMCE = null;
                            }
                        }
                    });

                    Livewire.on('imageSelectedFromGallery', (data) => {
                        console.log('Event dari Galeri:', data);

                        const imageData = Array.isArray(data) ? data[0] : data;

                        if (imageData && imageData.url) {

                            setTimeout(() => {
                                const editor = tinymce.get('content');
                                if (editor) {
                                    const imgHtml = '<img src="' + imageData.url + '" alt="Gambar dari Galeri" class="img-fluid" />';
                                    editor.execCommand('mceInsertContent', false, imgHtml);
                                    console.log('Gambar langsung disisipkan:', imageData.url);
                                } else {
                                    console.warn('Editor belum siap, simpan gambar untuk disisipkan nanti');
                                    window.selectedImageUrlForTinyMCE = imageData.url;
                                }
                            }, 100);
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

                                    const pdfHtml = `<div class="pdf-container my-3"><iframe src="${pdfUrl}" width="100%" height="600px" frameborder="0" sandbox=""></iframe><p class="mt-2"><a href="${pdfUrl}" target="_blank" class="text-blue-600 underline">Download PDF</a></p></div>`;
                                    editor.execCommand('mceInsertContent', false, pdfHtml);
                                    console.log('✅ PDF Embed dengan full URL disisipkan:', pdfUrl);
                                } else {
                                    console.warn('Editor belum siap untuk menyisipkan PDF');
                                }
                            }, 100);
                        }
                    });

                    Livewire.on('videoSelectedFromYoutube', (data) => {
                        console.log('Event dari YouTube:', data);

                        const videoData = Array.isArray(data) ? data[0] : data;

                        if (videoData && videoData.embedHtml) {

                            setTimeout(() => {
                                const editor = tinymce.get('content');
                                if (editor) {
                                    editor.execCommand('mceInsertContent', false, videoData.embedHtml);
                                    console.log('Video YouTube langsung disisipkan');
                                } else {
                                    console.warn('Editor belum siap');
                                }
                            }, 100);
                        }
                    });

                    const alertDiv = document.querySelector('[x-data*="showAlert"]');

                    Livewire.on('swal', (detail) => {
                        if (alertDiv && alertDiv.__x) {
                            const alpineData = alertDiv.__x.$data;
                            alpineData.alertType = detail.icon || 'info';
                            alpineData.alertTitle = detail.title || '';
                            alpineData.alertMessage = detail.text || detail.message || '';
                            alpineData.showAlert = true;

                            setTimeout(() => {
                                alpineData.showAlert = false;
                            }, 5000);

                            console.log('🔔 Alert shown:', { title: detail.title, message: detail.text });
                        }
                    });

                    Livewire.on('handle-copy-to-editor', (data) => {
                        const editor = tinymce.get('content');
                        const payload = Array.isArray(data) ? data[0] : data;
                        const field = payload.field || '';
                        const content = payload.content || '';

                        console.log('💾 Copy to editor event received:', { field, content: content.substring(0, 100) });

                        if (field === 'title') {
                            console.log('✅ Title akan di-set oleh Livewire directly');
                            return;
                        }

                        if (!editor) {
                            console.warn('⚠️ TinyMCE editor not found, akan di-update saat editor siap');
                            return;
                        }

                        if (field === 'content') {

                            editor.execCommand('mceInsertContent', false, content);
                            console.log('✅ Content inserted to editor');
                        } else if (field === 'image') {

                            const imgHtml = content;
                            editor.execCommand('mceInsertContent', false, imgHtml);
                            console.log('✅ Image inserted to editor');
                        } else if (field === 'content-full') {

                            editor.execCommand('mceInsertContent', false, content);
                            console.log('✅ Full content (image + text) inserted to editor');
                        }
                    });

                    document.addEventListener('livewire:updated', () => {
                        console.log('Livewire updated, checking TinyMCE editor...');
                        const editor = tinymce.get('content');
                        if (!editor) {
                            console.log('Reinitializing TinyMCE...');
                            initTinyMCE();
                        }
                    });

    </script>
    @endpush
</div>
</div>
