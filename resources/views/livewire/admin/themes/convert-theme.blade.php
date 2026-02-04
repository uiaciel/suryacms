<div class="container-fluid py-3">

    {{-- ALERTS --}}
    @foreach (['message' => 'success', 'error' => 'danger'] as $key => $type)
        @if (session($key))
            <div class="alert alert-{{ $type }} alert-dismissible fade show shadow-sm">
                {!! session($key) !!}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    @if (!empty($validationErrors))
        <div class="alert alert-warning shadow-sm">
            <strong>Peringatan:</strong>
            <ul class="small mb-0 mt-2">
                @foreach ($validationErrors as $error)
                    <li>{{ is_array($error) ? implode(', ', $error) : $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- HEADER --}}
    <header class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h2 class="fw-bold text-primary mb-0">
            <i class="bi bi-magic me-2"></i> Theme Engine
            <span class="text-muted fw-light fs-6">Builder v3.0</span>
        </h2>

        @if($mode === 'editor')
            <button class="btn btn-outline-secondary btn-sm rounded-pill"
                    wire:click="switchToList">
                <i class="bi bi-arrow-left"></i> Back to Themes
            </button>
        @endif
    </header>

    {{-- ===================================================================== --}}
    {{-- MODE: LIST - Upload Form + Existing Files --}}
    {{-- ===================================================================== --}}
    @if($mode === 'list')
    <div class="row g-4">

        {{-- UPLOAD FORM --}}
        <div class="col-12">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-header bg-primary text-white">
                    <strong><i class="bi bi-cloud-upload"></i> Generate Tema Baru</strong>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div x-data="uploadProgress">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Nama Tema</label>
                                    <input type="text" wire:model="themeName"
                                           class="form-control form-control-lg @error('themeName') is-invalid @enderror"
                                           placeholder="contoh-tema">
                                    @error('themeName') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">File ZIP</label>
                                    <input type="file" wire:model="indexFile"
                                           class="form-control form-control-lg @error('indexFile') is-invalid @enderror"
                                           accept=".zip">
                                    @error('indexFile') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>

                                <div x-show="uploading" class="progress mb-3">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                                         :style="`width:${progress}%`"></div>
                                </div>

                                <button class="btn btn-primary btn-lg w-100"
                                        wire:click="uploadAndPrepareEditor"
                                        wire:loading.attr="disabled"
                                        :disabled="uploading">
                                    <span wire:loading.remove>
                                        <i class="bi bi-shuffle"></i> Mulai Normalisasi
                                    </span>
                                    <span wire:loading>
                                        <i class="spinner-border spinner-border-sm me-2"></i> Memproses…
                                    </span>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-white border-primary">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">
                                        <i class="bi bi-info-circle"></i> Petunjuk Upload
                                    </h6>
                                    <ul class="small mb-0">
                                        <li>Upload file <strong>.ZIP</strong> yang berisi tema</li>
                                        <li>File harus memiliki <strong>index.html</strong></li>
                                        <li>Semua aset (CSS, JS, images) harus ada</li>
                                        <li>Nama tema hanya alfanumerik dan dash (-)</li>
                                        <li>Ukuran maksimal file: <strong>10 MB</strong></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- EXISTING FILES LIST --}}
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong><i class="bi bi-collection"></i> File index.html yang Tersimpan</strong>
                        <span class="badge bg-white text-info">{{ count($existingIndexFiles) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if(empty($existingIndexFiles))
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem;" class="text-muted"></i>
                        <p class="text-muted mt-3">Belum ada file tema yang tersimpan. <br>
                           Mulai dengan upload file ZIP baru di atas.</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Tema</th>
                                    <th>Ukuran</th>
                                    <th>Status</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($existingIndexFiles as $file)
                                <tr>
                                    <td>
                                        <strong>{{ $file['name'] }}</strong>
                                        <br>
                                        <small class="text-muted">ID: {{ substr($file['id'], 0, 8) }}...</small>
                                    </td>
                                    <td>
                                        <small>{{ number_format($file['size'] / 1024, 1) }} KB</small>
                                    </td>
                                    <td>
                                        @if($file['is_generated'])
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Generated
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-pencil"></i> Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ \Carbon\Carbon::createFromTimestamp($file['created_at'])->format('d M Y, H:i') }}</small>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary"
                                                wire:click="loadExistingIndexFile('{{ $file['id'] }}')">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger"
                                                wire:click="deleteFile('{{ $file['id'] }}')"
                                                wire:confirm="Hapus tema '{{ $file['name'] }}'?">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
    @endif

    {{-- ===================================================================== --}}
    {{-- MODE: EDITOR - CodeMirror Editor + Buttons --}}
    {{-- ===================================================================== --}}
    @if($mode === 'editor')
    <div class="row g-4" x-data="themeEditor()"
         x-init="init(@js($htmlEdited), @js($currentThemeName))">

        {{-- EDITOR --}}
        <div class="col-lg-9">

            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong><i class="bi bi-file-code"></i> index.html</strong>
                            @if($currentThemeName)
                            <span class="badge bg-info ms-2">{{ $currentThemeName }}</span>
                            @endif
                        </div>
                        <div class="btn-group" role="group">
                            <button class="btn btn-success btn-sm" @click="save()"
                                    title="Ctrl+S">
                                <i class="bi bi-check-circle"></i> Simpan
                            </button>
                            <button class="btn btn-primary btn-sm"
                                    wire:click="generateTheme"
                                    wire:loading.attr="disabled"
                                    title="Generate blade files">
                                <span wire:loading.remove>
                                    <i class="bi bi-shuffle"></i> Generate
                                </span>
                                <span wire:loading>
                                    <i class="spinner-border spinner-border-sm me-1"></i> Generating...
                                </span>
                            </button>
                            <button class="btn btn-success btn-sm @if(!$isGenerated) disabled @endif"
                                    wire:click="installTheme"
                                    @if(!$isGenerated) title="Generate tema terlebih dahulu" @endif
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="bi bi-cloud-check"></i> Install
                                </span>
                                <span wire:loading>
                                    <i class="spinner-border spinner-border-sm me-1"></i> Installing...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0" wire:ignore>
                    <textarea x-ref="htmlEditor" style="display: none;"></textarea>
                </div>

                <div class="card-footer small text-muted d-flex justify-content-between">
                    <span><i class="bi bi-keyboard"></i> Ctrl + S untuk simpan</span>
                    <span>
                        @if($isGenerated)
                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Generated</span>
                        @else
                            <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i> Draft</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="col-lg-3">

            {{-- FLOW STATUS --}}
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-light fw-bold small">
                    <i class="bi bi-diagram-3"></i> Status Proses
                </div>
                <div class="card-body small">
                    <div class="d-flex align-items-center mb-3">
                        <div class="badge bg-success me-2">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <strong>1. Upload</strong><br>
                            <small class="text-muted">File ZIP diekstrak & dinormalisasi</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <div class="badge @if($isGenerated) bg-success @else bg-secondary @endif me-2">
                            <i class="bi bi-arrow-right"></i>
                        </div>
                        <div>
                            <strong>2. Generate</strong><br>
                            <small class="text-muted">
                                @if($isGenerated)
                                    ✅ Blade files sudah dibuat
                                @else
                                    Klik tombol "Generate" →
                                @endif
                            </small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="badge @if($isGenerated) bg-secondary @else bg-light text-dark @endif me-2">
                            <i class="bi bi-download"></i>
                        </div>
                        <div>
                            <strong>3. Install</strong><br>
                            <small class="text-muted">
                                @if($isGenerated)
                                    Siap untuk diinstall
                                @else
                                    Tersedia setelah generate
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- THEME INFO --}}
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header fw-bold small bg-light">
                    <i class="bi bi-info-circle"></i> Informasi
                </div>
                <div class="card-body small">
                    <p class="mb-2">
                        <strong>Nama Tema:</strong> <br>
                        <span x-text="'{{ $currentThemeName }}'"></span>
                    </p>
                    <p class="mb-2">
                        <strong>Ukuran:</strong> <br>
                        <span x-text="editorStats.lines + ' baris, ' + editorStats.chars + ' karakter'"></span>
                    </p>
                    <hr>
                    <small class="text-muted">
                        💡 Simpan perubahan sebelum generate untuk memastikan semua modifikasi tersimpan.
                    </small>
                </div>
            </div>

            {{-- HELP CARD --}}
            <div class="card shadow-sm border-0">
                <div class="card-header fw-bold small bg-light">
                    <i class="bi bi-question-circle"></i> Bantuan
                </div>
                <div class="card-body small">
                    <p><strong>Simpan:</strong> Menyimpan perubahan HTML ke storage sementara</p>
                    <p><strong>Generate:</strong> Membuat file blade dan konfigurasi tema</p>
                    <p><strong>Install:</strong> Memindahkan tema ke folder produksi</p>
                    <hr class="my-2">
                    <p class="mb-0"><em>Tema akan dipindahkan ke:</em></p>
                    <small class="text-monospace">
                        📁 public/frontend/{name}/ <br>
                        📁 resources/views/frontend/{name}/
                    </small>
                </div>
            </div>

        </div>

    </div>
    @endif

</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/monokai.min.css">
<style>
    .CodeMirror {
        height: 100% !important;
        font-family: 'Fira Code', monospace;
        font-size: 13px;
    }

</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/css/css.min.js"></script>

<script>
    document.addEventListener('alpine:init', () => {

        /**
        * Upload Progress Handler
        */
        Alpine.data('uploadProgress', () => ({
            uploading: false,
            progress: 0,

            init() {
                window.addEventListener('livewire-upload-start', () => this.uploading = true);
                window.addEventListener('livewire-upload-progress', e => this.progress = e.detail.progress);
                window.addEventListener('livewire-upload-finish', () => this.uploading = false);
                window.addEventListener('livewire-upload-error', () => this.uploading = false);
            }
        }));

        /**
        * Theme Editor
        */
        Alpine.data('themeEditor', () => ({
            editor: null,
            content: '',
            themeName: '',
            editorStats: { lines: 0, chars: 0 },
            updateDebounce: null,

            init(content = '', themeName = '') {
                this.content = content;
                this.themeName = themeName;
                this.$nextTick(() => this.initEditor());
            },

            initEditor() {
                try {
                    const textarea = this.$refs.htmlEditor;
                    if (!textarea) {
                        console.error('Textarea element not found');
                        return;
                    }

                        if (!textarea) return;

                        // 1. CEK & HAPUS instance lama jika sudah ada
                        if (this.editor) {
                            this.editor.toTextArea(); // Mengembalikan textarea ke kondisi semula
                        }

                    this.editor = CodeMirror.fromTextArea(textarea, {

                        theme: 'monokai',
                        lineNumbers: true,
                        lineWrapping: true,
                        tabSize: 4,
                        indentUnit: 4,
                        indentWithTabs: false,
                        extraKeys: {
                            'Ctrl-S': () => this.save(),
                            'Cmd-S': () => this.save(),
                        },
                        viewportMargin: Infinity,
                        // height: 'auto',
                        // minHeight: '800px'
                    });

                    if (this.content) {
                        this.editor.setValue(this.content);
                    }

                    this.updateStats();
                    this.bindChanges();

                    // Refresh multiple times untuk memastikan CodeMirror render
                    setTimeout(() => {
                        this.editor.refresh();
                    }, 50);

                    setTimeout(() => {
                        this.editor.refresh();
                    }, 200);

                    // Listen for Livewire theme-loaded event
                    Livewire.on('theme-loaded', (data) => {
                        if (data && data.content) {
                            this.loadThemeContent(data.content, data.themeName);
                        }
                    });

                } catch (e) {
                    console.error('Failed to initialize CodeMirror:', e);
                }
            },

            loadThemeContent(content, themeName) {
                if (this.editor) {
                    this.editor.setValue(content);
                    this.content = content;
                    this.themeName = themeName;
                    this.updateStats();

                    // Refresh untuk memastikan display benar
                    setTimeout(() => {
                        this.editor.refresh();
                    }, 50);

                    this.editor.focus();
                }
            },

            bindChanges() {
                if (!this.editor) return;

                this.editor.on('change', (cm) => {
                    clearTimeout(this.updateDebounce);

                    this.updateDebounce = setTimeout(() => {
                        const newContent = cm.getValue();
                        this.updateStats();
                        this.$wire.set('htmlEdited', newContent, false);
                        console.log('Content synced to Livewire');
                    }, 500);
                });

                this.editor.on('cursorActivity', () => this.updateStats());
            },

            updateStats() {
                if (!this.editor) return;
                const content = this.editor.getValue();
                this.editorStats = {
                    lines: this.editor.lineCount(),
                    chars: content.length
                };
            },

            save() {
                if (!this.editor) return;
                const content = this.editor.getValue();
                this.$wire.set('htmlEdited', content);
                this.$wire.saveHtmlChanges();
                console.log('Changes saved');
            }
        }));

    });
</script>
@endpush
