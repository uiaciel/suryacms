<div>
    <div class="container-fluid">
        {{-- Breadcrumb and Title Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">📝 {{ $titlePage }}</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/admin" class="text-decoration-none">Admin</a></li>
                    <li class="breadcrumb-item"><a href="/admin/pages" class="text-decoration-none">Pages</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $titlePage }}</li>
                </ol>
            </nav>
        </div>

        <x-suryacms::session-status />

        {{-- Main Form using a two-column layout --}}
        <form wire:submit.prevent="updatePage" x-data="{ languageId: @entangle('language_id') }">
            <div class="row g-4">
                {{-- Left Column: Page Content --}}
                <div class="col-lg-8">
                    <div class="card border border-primary shadow rounded">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Page Details</h5>
                            <div class="mb-3">
                                <label for="title" class="form-label fw-bold">Title</label>
                                <input type="text" id="title" wire:model.defer="title" class="form-control"
                                    placeholder="Enter page title...">
                                @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            {{-- Multilingual Translation Section --}}
                            @if ($setting->is_multilingual == 'Yes')
                            {{-- Language Dropdown --}}

                            <div class="mb-3">
                                <label for="language_id" class="form-label fw-bold">Language</label>
                                <select id="language_id" wire:model.defer="language_id" class="form-select" x-on:change="$wire.set('language_id', $event.target.value)">
                                    <option value="">Select Language</option>
                                    @foreach ($languages as $language)
                                    <option value="{{ $language->id }}">{{ $language->name }}</option>
                                    @endforeach
                                </select>
                                @error('language_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="translation" class="form-label fw-bold">
                                    {{ $language_id == 1 ? 'Translation Page' : 'Translation Page' }}
                                </label>
                                <div x-data="{
                                        translations: [],
                                        fetchTranslations(languageId) {
                                            if (!languageId) { this.translations = []; return; }
                                            fetch(`{{ url('admin/pages/translations') }}/${languageId}`)
                                                .then(response => {
                                                    if (!response.ok) throw new Error('Failed to fetch translations');
                                                    return response.json();
                                                })
                                                .then(data => this.translations = data)
                                                .catch(error => console.error('Error fetching translations:', error));
                                        }
                                    }" x-init="$watch('languageId', value => fetchTranslations(value))">
                                    <select id="translation" wire:model.defer="translation_id" class="form-control">
                                        <option value="">Select Translate</option>
                                        <template x-for="translation in translations" :key="translation.id">
                                            <option :value="translation.id" x-text="translation.title"></option>
                                        </template>
                                    </select>
                                    @error('translation_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            @endif

                            {{-- Image Gallery and YouTube Video Modals --}}
                            <div class="mb-3 d-flex gap-2">
                                <button type="button" class="btn btn-outline-info flex-grow-1"
                                    wire:click="$dispatch('openGalleryModal')">
                                    <i class="bi bi-images me-2"></i>Insert Image
                                </button>
                                <button type="button" class="btn btn-outline-danger flex-grow-1"
                                    wire:click="$dispatch('openGalleryModal')"><i class="bi bi-youtube me-2"></i>Insert
                                    Video</button>
                                <livewire:suryacms::admin.image-gallery-modal />
                                <livewire:suryacms::admin.youtube-video-modal />
                            </div>

                            {{-- Main Content Area with Rich Text Editor --}}
                            <div class="mb-3" wire:ignore>
                                <label for="content" class="form-label fw-bold">Content</label>
                                <textarea id="content" wire:model.defer="konten" name="konten" class="form-control"
                                    rows="10">{{ old('konten', $konten) }}</textarea>
                                @error('konten') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            {{-- PDF Upload and Viewer --}}
                            <div class="input-group mb-3">

                                <span class="input-group-text">PDF File</span>
                                <input type="file" id="pdf" wire:model="pdf" class="form-control">

                                @if (isset($pdf) && $pdf != null)
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#PdfModal">
                                    <i class="bi bi-file-pdf me-1"></i> View Existing PDF
                                </button>
                                @else
                                <em class="d-block mt-2 text-muted">(No PDF file exists)</em>
                                @endif
                            </div>
                            @error('pdf') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Right Column: Page Settings & Actions --}}
                @if (isset($pdf) && $pdf != null)
                <div class="col-lg-4">
                    <div class="card border border-primary shadow rounded mb-3">
                        <div class="card-body">
                            <h5 class="card-title  mb-3">Page Settings</h5>

                            {{-- Status Dropdown --}}
                            <div class="mb-3">
                                <label for="status" class="form-label fw-bold">Status</label>
                                <select id="status" wire:model="status" class="form-select">
                                    <option value="Publish">Publish</option>
                                    <option value="Draft">Draft</option>
                                </select>
                            </div>

                            {{-- Date Publish Input --}}
                            <div class="mb-3">
                                <label for="datepublish" class="form-label fw-bold">Date Publish</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                    <input type="date" id="datepublish" wire:model="datepublish" class="form-control">
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit"
                                    class="btn btn-primary d-flex align-items-center justify-content-center">
                                    <i class="fas fa-save me-2"></i> Update Page
                                </button>
                                <a href="/admin/pages" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>
                    <div class="card border border-warning shadow rounded">
                        <div class="card-body">
                            <h5 class="card-title  mb-3">Pdf in Page</h5>

                            <embed src="{{ asset('storage/' . $pdf) }}" type="application/pdf" width="100%" height="400px" />

                        </div>
                    </div>
                </div>
                @endif

            </div>
        </form>

        <!-- Modal for PDF Viewer -->
        <div class="modal fade" id="PdfModal" tabindex="-1" aria-labelledby="PdfModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="PdfModalLabel">PDF Viewer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if (isset($pdf) && $pdf != null)
                        <embed src="{{ asset('storage/' . $pdf) }}" type="application/pdf" width="100%"
                            height="600px" />
                        @else
                        <p class="text-center">No PDF file to display.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

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
