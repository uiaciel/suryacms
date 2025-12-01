<div>
    <div class="d-flex justify-content-between mb-3">
        <h3>{{ $titlePage }}</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                <li class="breadcrumb-item"><a href="/admin/posts">Post</a></li>
                <li class="breadcrumb-item active"><a href="#">{{ $titlePage }}</a></li>
            </ol>
        </nav>
    </div>

    <x-suryacms::session-status />

    {{-- Main Form (assuming this is wrapped in a Livewire component for update) --}}
    <form wire:submit.prevent="updatePost" x-data="{ languageId: @entangle('language_id') }">
        <div class="row">
            {{-- Main Content Column --}}
            <div class="col-md-8">
                <div class="card  shadow rounded border border-primary mb-3">
                    <div class="card-body">

                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Title</label>
                            <input type="text" id="title" wire:model="title" class="form-control"
                                placeholder="Enter post title">
                            @error('title')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        @if ($setting->is_multilingual == 'Yes')
                        <div class="mb-3">
                            <label for="translation" class="form-label fw-bold">
                                {{ $language_id == 1 ? 'Translation Post' : 'Translation Post' }}
                            </label>
                            <div x-data="{
                                translations: [],
                                fetchTranslations(languageId) {
                                    if (!languageId) return;
                                    fetch(`{{ url('admin/posts/translations') }}/${languageId}`)
                                        .then(response => {
                                            if (!response.ok) throw new Error('Failed to fetch translations');
                                            return response.json();
                                        })
                                        .then(data => this.translations = data)
                                        .catch(error => console.error('Error fetching translations:', error));
                                }
                            }"
                                x-init="$watch('languageId', value => fetchTranslations(value)); fetchTranslations(languageId);">
                                {{-- fetchTranslations(languageId) dipanggil di x-init untuk load awal --}}
                                <select id="translation" x-model="translation_id" class="form-control"
                                    wire:model.defer="translation_id">
                                    <option value="">Select Translate</option>
                                    <template x-for="translation in translations" :key="translation.id">
                                        <option :value="translation.id" x-text="translation.title"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        @endif

                        <div class="mb-3">
                            <livewire:suryacms::admin.image-gallery-modal />
                            <livewire:suryacms::admin.youtube-video-modal />

                        </div>

                        <div class="mb-3" wire:ignore>
                            <label class="form-label fw-bold">Content</label>
                            <textarea id="content" wire:model="konten" name="konten" class="form-control"></textarea>
                            @error('konten')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- SEO & Source Information Card --}}
                <div class="card  shadow rounded border border-primary mb-3">
                    <div class="card-header bg-primary">
                        <h5 class="card-title text-white mb-0">SEO & Source Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="tags" class="form-label fw-bold">Tags</label>
                            <input type="text" id="tags" wire:model="tags"
                                placeholder="Keyword1, Keyword2, Tema, Lainnya" class="form-control">
                            @error('tags')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="source_url" class="form-label fw-bold">Source URL</label>
                            <input type="text" id="source_url"
                                placeholder="https://news.detik.com/berita/judul-berita-dari-url"
                                wire:model="source_url" name="source_url" class="form-control"
                                x-data="{ favicon: @entangle('source_favicon'), domain: @entangle('source_title') }"
                                x-on:input.debounce.500ms="
                                if ($event.target.value) {
                                    try {
                                        const url = new URL($event.target.value);
                                        domain = url.hostname;
                                        favicon = url.origin + '/favicon.ico';
                                        $wire.set('source_favicon', favicon);
                                        $wire.set('source_title', domain);
                                    } catch (e) {
                                        console.error('Invalid URL');
                                    }
                                }
                            ">
                            @error('source_url')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="source_favicon" class="form-label fw-bold">Source Favicon</label>
                            <input type="text" id="source_favicon" placeholder="Terisi otomatis"
                                wire:model="source_favicon" name="source_favicon" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="source_title" class="form-label fw-bold">Source Domain</label>
                            <input type="text" id="source_title" placeholder="Terisi otomatis" wire:model="source_title"
                                name="source_title" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Column --}}
            <div class="col-md-4">
                <div class="card  shadow rounded border border-primary mb-3">
                    <div class="card-header bg-primary">
                        <h5 class="card-title text-white mb-0">Post Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="datepublish" class="form-label fw-bold">Date Publish</label>
                            <input type="date" id="datepublish" wire:model="datepublish" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label fw-bold">Category</label>
                            <select id="category_id" wire:model="category_id" class="form-select">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="feature" wire:model.live="feature"
                                value="Yes">
                            <label class="form-check-label" for="feature">Feature Post</label>
                        </div>
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="flash" wire:model.live="flash"
                                value="Yes">
                            <label class="form-check-label" for="flash">Flash Post</label>
                        </div>
                        {{-- Status select, can be set to the current post's status --}}
                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">Status</label>
                            <select id="status" wire:model="status" class="form-select">
                                <option value="Publish">Publish</option>
                                <option value="Draft">Draft</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary ">Update Post</button>
                    <button type="button" wire:click="saveDraft" class="btn btn-outline-secondary ">Save as
                        Draft</button>
                    {{-- Add a delete button if needed --}}
                    <button type="button" wire:click="deletePost" class="btn btn-danger  mt-2">Delete
                        Post</button>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
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

                    function copyImageUrlToClipboard(url) {
                    navigator.clipboard.writeText(url).then(function () {
                    alert("✅ URL gambar berhasil disalin!\nSilakan paste (Ctrl+V) ke editor.");

                    // Tutup modal setelah user menutup alert
                    Livewire.dispatch('closeGalleryModal');
                    }).catch(function (err) {
                    console.error('❌ Gagal menyalin URL:', err);
                    });
                    }

                    function initTinyMCE() {
                        console.log("initTinyMCE dipanggil");
                        tinymce.remove();

                        tinymce.init({
                            selector: '#content',
                            skin: 'bootstrap',
                            height: 500,
                            plugins: 'code advlist autolink lists link image charmap anchor searchreplace visualblocks fullscreen insertdatetime media table contextmenu',
                            toolbar1: 'customGalleryButton image youtubeVideo',
                            toolbar2: 'fontfamily styles | link bold italic underline forecolor backcolor | numlist bullist alignleft aligncenter alignright alignjustify table code preview',
                            menubar: false,
                            image_title: true,
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

                                // Regex untuk mendeteksi URL YouTube
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
                                e.preventDefault(); // Mencegah paste teks URL mentah
                                console.log("✅ Video YouTube dimasukkan:", pastedText);
                                return; // Hentikan pemrosesan lebih lanjut
                                }
                                }

                                // Logika PastePostProcess Anda yang sudah ada untuk gambar lokal
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

                                // Fallback jika paste bukan YouTube URL atau gambar lokal
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
                                // const target = editor.editorContainer.querySelector('button[aria-label="Insert/edit image"]');
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

                    // Inisialisasi pertama saat dokumen dimuat
                    document.addEventListener('DOMContentLoaded', () => {
                        initTinyMCE();
                    });

                    // Inisialisasi ulang editor setelah Livewire update
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

                    // Tangkap event dari Livewire untuk sisipkan gambar
                    Livewire.on('imageSelectedFromGallery', (data) => {
                        console.log('Event dari Galeri:', data);
                        if (data && data.url) {
                            const editor = tinymce.get('content');
                            if (editor) {
                                editor.execCommand('mceInsertContent', false, '<img src="' + data.url + '" alt="Gambar dari Galeri" class="img-fluid" />');
                                console.log('Gambar langsung disisipkan:', data.url);
                            } else {
                                console.warn('Editor belum siap, simpan gambar untuk disisipkan nanti');
                                window.selectedImageUrlForTinyMCE = data.url;
                            }
                        }
                    });
    </script>
    @endpush
</div>
