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

    {{-- Main Form --}}
    <form wire:submit.prevent="savePost" x-data="{ languageId: @entangle('language_id') }">
        <div class="row mb-3">
            {{-- Main Content Column --}}
            <div class="col-md-8">
                <div class="card border-0 shadow border border-primary rounded mb-3">

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
                                    if (!languageId) return; // Skip fetch if languageId is empty
                                    fetch(`{{ url('admin/posts/translations') }}/${languageId}`)
                                        .then(response => {
                                            if (!response.ok) throw new Error('Failed to fetch translations');
                                            return response.json();
                                        })
                                        .then(data => this.translations = data)
                                        .catch(error => console.error('Error fetching translations:', error));
                                }
                            }" x-init="$watch('languageId', value => fetchTranslations(value))">
                                <select id="translation" x-model="translation" class="form-control"
                                    wire:model.defer="translation_id">
                                    <option value="">Select Translate</option>
                                    <template x-for="translation in translations" :key="translation.id">
                                        <option :value="translation.id" x-text="translation.title"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="mb-3" wire:ignore>
                            <label class="form-label fw-bold">Content</label>
                            <textarea id="content" wire:model="konten" name="konten"
                                class="form-control">{{ old('konten', $konten) }}</textarea>
                            @error('konten')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>

            {{-- Sidebar Column --}}
            <div class="col-md-4">
                <div class="card border-0 shadow rounded mb-3">
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
                            <input class="form-check-input" type="checkbox" id="feature" wire:model="feature"
                                value="Yes" {{ $feature=='Yes' ? 'checked' : '' }}>
                            <label class="form-check-label" for="feature">Feature Post</label>
                        </div>
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="flash" wire:model="flash" value="Yes" {{
                                $flash=='Yes' ? 'checked' : '' }}>
                            <label class="form-check-label" for="flash">Flash Post</label>
                        </div>

                        <div class="mb-3" hidden>
                            <label for="status" class="form-label fw-bold">Status</label>
                            <select id="status" wire:model="status" class="form-control">
                                <option value="Publish">Publish</option>
                                <option value="Draft">Draft</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Publish Post</button>
                    <button type="button" wire:click="saveDraft" class="btn btn-outline-secondary">Save
                        Draft</button>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                {{-- SEO & Source Information Card --}}
                <div class="card border-0 shadow rounded mb-3">
                    <div class="card-header bg-primary">
                        <h5 class="card-title text-white mb-0">SEO & Source Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="tags" class="form-label fw-bold">Tags</label>
                            <input type="text" id="tags" wire:model="tags" placeholder="Keyword1, Keyword2, Tema, Lainnya" class="form-control">
                            @error('tags')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3" x-data="{
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

                            <label for="source_url" class="form-label fw-bold">Source URL</label>

                            <div class="input-group">
                                <input x-ref="sourceInput" type="text" id="source_url" placeholder="https://news.detik.com/berita/judul-berita-dari-url" wire:model.defer="source_url" name="source_url" class="form-control"
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

                                <button type="button" class="btn btn-primary"
                                    x-bind:disabled="$wire.get('source_url') === ''"
                                    x-on:click.prevent="() => { $wire.call('scrapeContent') }">
                                    <span x-show="!$wire.__pendingRequests || $wire.__pendingRequests.length === 0"><i class="bi bi-search"></i> Scrape</span>
                                    <span x-show="$wire.__pendingRequests && $wire.__pendingRequests.length > 0"><i class="spinner-border spinner-border-sm" role="status"></i> Scraping...</span>
                                </button>
                            </div>

                            <div class="mt-2">
                                <template x-if="showAlert">
                                    <div x-bind:class="{
                                        'alert alert-success': alertType === 'success',
                                        'alert alert-danger': alertType === 'error',
                                        'alert alert-info': alertType === 'info' || alertType === 'warning'
                                    }" role="alert">
                                        <strong x-text="alertType.toUpperCase() + ':'"></strong>
                                        <span x-text="alertMessage"></span>
                                    </div>
                                </template>
                                @error('source_url')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="source_favicon" class="form-label fw-bold">Source Favicon</label>
                            <input type="text" id="source_favicon" placeholder="Terisi otomatis" wire:model="source_favicon" name="source_favicon" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="source_title" class="form-label fw-bold">Source Domain</label>
                            <input type="text" id="source_title" placeholder="Terisi otomatis" wire:model="source_title" name="source_title" class="form-control">
                        </div>
                    </div>
                </div>

            </div>

<div class="col-md-4">

  <div class="card border-0 shadow rounded mb-3">
      <div class="card-header bg-primary">
          <h5 class="card-title text-white mb-0">Scraped Content</h5>
      </div>
      <div class="card-body" x-data="{
                        scraped: @entangle('scrapeResult'),
                        copyToEditor(content) {
                            const editor = tinymce.get('content');
                            if (editor) {
                                editor.setContent(content);
                                this.$dispatch('swal', {
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Content copied to editor!'
                                });
                            }
                        }
                    }" @content-scraped.window="scraped = $event.detail">
          <template x-if="scraped.title">
              <div>
                  <div class="mb-3">
                      <label class="form-label fw-bold">Scraped Title:</label>
                      <div class="d-flex gap-2">
                          <input type="text" class="form-control" x-model="scraped.title" readonly>
                          <button type="button" class="btn btn-primary" @click="$wire.set('title', scraped.title)">
                              <i class="bi bi-clipboard"></i> Copy to Title
                          </button>
                      </div>
                  </div>

                  <div class="mb-3">
                      <label class="form-label fw-bold">Scraped Content:</label>
                      <div class="position-relative">
                          <textarea class="form-control" rows="5" x-text="scraped.content" readonly></textarea>
                          <button type="button" class="btn btn-primary position-absolute top-0 end-0 m-2" @click="copyToEditor(scraped.content)">
                              <i class="bi bi-clipboard"></i> Copy to Editor
                          </button>
                      </div>
                  </div>

                  <template x-if="scraped.image">
                      <div class="mb-3">
                          <label class="form-label fw-bold">Featured Image:</label>
                          <img :src="scraped.image" class="img-fluid rounded mb-2">
                          <button type="button" class="btn btn-primary btn-sm" @click="copyToEditor(`<img src='${scraped.image}' class='img-fluid' alt='Featured Image'>`)">
                              <i class="bi bi-clipboard"></i> Insert Image to Editor
                          </button>
                      </div>
                  </template>
              </div>
          </template>
          <template x-if="!scraped.title">
              <div class="text-center text-muted">
                  <p>No content scraped yet. Enter a URL and click "Scrape" to get content.</p>
                  <p class="small">Supported sites: CNN Indonesia, Detik.com, Tribunnews, Kompas.com</p>
              </div>
          </template>
      </div>
  </div>

</div>

        </div>
    </form>

         @livewire('suryacms::admin.image-gallery-modal')
         @livewire('suryacms::admin.youtube-video-modal')

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
