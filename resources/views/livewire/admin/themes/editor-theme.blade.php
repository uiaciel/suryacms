<div class="container-fluid py-4" style="min-height: 100vh;">
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <h3 class="fw-bold mb-0">Theme Editor</h3>
            <p class="text-muted mb-0">Kustomisasi tampilan frontend dan aset tema ({{ strtoupper($activeTheme) }})</p>
        </div>
        <div class="col-md-5 d-flex justify-content-md-end align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    Tema: <strong>{{ strtoupper($activeTheme) }}</strong>
                </button>
                <ul class="dropdown-menu">
                    @foreach($availableThemes as $theme)
                    <li><a class="dropdown-item" href="#" wire:click.prevent="changeTheme('{{ $theme }}')">{{ strtoupper($theme) }}</a></li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#variableModal">
                <i class="fas fa-code me-1"></i> Variables
            </button>

            <button wire:click="saveFile" wire:loading.attr="disabled" class="btn btn-success px-4">
                <span wire:loading wire:target="saveFile" class="spinner-border spinner-border-sm me-1"></span>
                <i class="fas fa-save me-1"></i> Simpan
            </button>
        </div>
    </div>

    @if($saveMessage)
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ $saveMessage }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if($errorMessage)
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i> {{ $errorMessage }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-3">
        <div class="col-lg-10 col-md-9">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">

                    <div class="text-truncate" style="max-width: 70%;">
                        <h6 class="mb-0"><code>{{ $selectedFile ?? 'Pilih file dari sidebar...' }}</code></h6>
                    </div>
                    <div class="btn-group">
                        <button wire:click="switchLocation('views')" class="btn btn-sm {{ $currentLocation === 'views' ? 'btn-primary shadow-sm' : 'btn-outline-secondary' }}">
                            <i class="fas fa-eye me-1"></i> Views (Blade)
                        </button>
                        <button wire:click="switchLocation('public')" class="btn btn-sm {{ $currentLocation === 'public' ? 'btn-primary shadow-sm' : 'btn-outline-secondary' }}">
                            <i class="fas fa-globe me-1"></i> Public (Assets)
                        </button>
                    </div>
                </div>

                @if ($selectedFile)
                <div class="card-body p-0 shadow-sm " x-data="{
                            editor: null,
                            content: @entangle('fileContent'),
                            initEditor() {
                                // Pastikan instance lama dihancurkan jika ada
                                const container = this.$refs.codeeditor;

                                this.editor = CodeMirror.fromTextArea(container, {
                                    lineNumbers: true,
                                    mode: getModeForLanguage('{{ $fileLanguage }}'),
                                    theme: 'monokai',
                                    lineWrapping: true,
                                    indentUnit: 4,
                                    extraKeys: {
                                        'Ctrl-S': () => { this.save(); },
                                        'Cmd-S': () => { this.save(); }
                                    }
                                });

                                // Set value awal
                                this.editor.setValue(this.content || '');

                                // SINKRONISASI: Update Alpine/Livewire saat ada perubahan di Editor
                                this.editor.on('change', (cm) => {
                                    this.content = cm.getValue();
                                });

                                // Refresh agar layout tidak berantakan
                                setTimeout(() => this.editor.refresh(), 50);
                            },
                            save() {
                                // Pastikan content terbaru masuk ke Livewire sebelum call
                                this.content = this.editor.getValue();
                                $wire.saveFile();
                            }
                        }" x-init="initEditor()" x-effect="if(content) { if(editor && editor.getValue() !== content) { editor.setValue(content); } }">

                    <div class="flex-grow-1 h-100" wire:ignore>
                        <textarea x-ref="codeeditor" class="d-none"></textarea>
                    </div>

                    <div class="card-footer bg-light py-1 px-3">
                        <small class="text-muted">{{ strtoupper($fileLanguage) }} Mode | Auto-sync enabled</small>
                    </div>
                </div>
                @else
                <div class="card shadow-sm border-dashed" style="height: 600px;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-muted">
                        <i class="bi bi-file-earmark-code mb-3" style="font-size: 3rem;"></i>
                        <h5>Pilih file untuk diedit</h5>
                    </div>
                </div>
                @endif

            </div>
        </div>

        <div class="col-lg-2 col-md-3">
            <div class="card border-0 shadow-sm sticky-top" style="top: 100px; height: calc(100vh - 30px - 100px);">
                <div class="card-header bg-white fw-bold py-3 border-bottom">
                    <i class="fas fa-folder-tree me-2 text-warning"></i>Daftar File
                </div>
                <div class="card-body overflow-auto p-0">
                    <div class="list-group list-group-flush shadow-none">
                        @forelse($files as $file)
                        <button wire:click="selectFile('{{ $file['path'] }}')" class="list-group-item list-group-item-action border-0 py-2 px-3 {{ $selectedFile === $file['path'] ? 'active bg-primary text-white fw-bold shadow-sm' : '' }}" style="font-size: 0.8rem;">
                            <div class="d-flex align-items-center">
                                <i class="fas {{ in_array($file['extension'], ['php','blade.php']) ? 'fa-code text-info' : 'fa-file-alt text-secondary' }} me-2 {{ $selectedFile === $file['path'] ? 'text-white' : '' }}"></i>
                                <span class="text-truncate">{{ $file['name'] }}</span>
                            </div>
                            <div class="mt-1 small {{ $selectedFile === $file['path'] ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.65rem;">
                                {{ number_format($file['size'] / 1024, 1) }} KB
                            </div>
                        </button>
                        @empty
                        <div class="p-4 text-center">
                            <i class="fas fa-search fa-2x text-light mb-2"></i>
                            <p class="text-muted small">Kosong</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-light border-0">
                    <button type="button" @click="save()" wire:loading.attr="disabled" class="btn btn-primary btn-sm w-100 mb-3 mt-1">
                        <span wire:loading.remove>Save Changes</span>
                        <span wire:loading>Saving...</span>
                    </button>

                    <button class="btn btn-sm btn-outline-dark w-100" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="variableModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="fas fa-book me-2"></i> Cheat Sheet Variable</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light px-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary border-bottom pb-2">Basic Setting</h6>
                            <div class="bg-white rounded border overflow-hidden">
                                <table class="table table-sm table-hover mb-0">
                                    <tbody class="font-monospace small">
                                        <tr>
                                            <td class="p-2">@{{ $setting->sitename }}</td>
                                            <td class="text-end p-2"><button class="btn btn-xs btn-outline-primary" onclick="copyText('@{{ $setting->sitename }}')">Copy</button></td>
                                        </tr>
                                        <tr>
                                            <td class="p-2">@{{ $setting->description }}</td>
                                            <td class="text-end p-2"><button class="btn btn-xs btn-outline-primary" onclick="copyText('@{{ $setting->description }}')">Copy</button></td>
                                        </tr>
                                        <tr>
                                            <td class="p-2">@{{ $setting->url }}</td>
                                            <td class="text-end p-2"><button class="btn btn-xs btn-outline-primary" onclick="copyText('@{{ $setting->url }}')">Copy</button></td>
                                        </tr>
                                        <tr>
                                            <td class="p-2">@{{ $setting->logo }}</td>
                                            <td class="text-end p-2"><button class="btn btn-xs btn-outline-primary" onclick="copyText('@{{ $setting->logo }}')">Copy</button></td>
                                        </tr>
                                        <tr>
                                            <td class="p-2">@{{ $setting->images }}</td>
                                            <td class="text-end p-2"><button class="btn btn-xs btn-outline-primary" onclick="copyText('@{{ $setting->images }}')">Copy</button></td>
                                        </tr>
                                        <tr>
                                            <td class="p-2">@{{ $setting->email }}</td>
                                            <td class="text-end p-2"><button class="btn btn-xs btn-outline-primary" onclick="copyText('@{{ $setting->email }}')">Copy</button></td>
                                        </tr>
                                        <tr>
                                            <td class="p-2">@{{ $setting->address }}</td>
                                            <td class="text-end p-2"><button class="btn btn-xs btn-outline-primary" onclick="copyText('@{{ $setting->address }}')">Copy</button></td>
                                        </tr>
                                        <tr>
                                            <td class="p-2">@{{ $setting->phone }}</td>
                                            <td class="text-end p-2"><button class="btn btn-xs btn-outline-primary" onclick="copyText('@{{ $setting->phone }}')">Copy</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary border-bottom pb-2">Social Media</h6>
                            <div class="bg-white rounded border overflow-hidden">
                                <table class="table table-sm table-hover mb-0">
                                    <tbody class="font-monospace small">

                                        <tr>
                                            <td class="p-2">@{{ $setting->whatsapp }}</td>
                                            <td class="text-end p-2"><button class="btn btn-xs btn-outline-primary" onclick="copyText('@{{ $setting->whatsapp }}')">Copy</button></td>
                                        </tr>

                                        <tr>
                                            <td class="p-2">@{{ $setting->facebook }}</td>
                                            <td class="text-end p-2"><button class="btn btn-xs btn-outline-primary" onclick="copyText('@{{ $setting->facebook }}')">Copy</button></td>
                                        </tr>
                                        <tr>
                                            <td class="p-2">@{{ $setting->instagram }}</td>
                                            <td class="text-end p-2"><button class="btn btn-xs btn-outline-primary" onclick="copyText('@{{ $setting->instagram }}')">Copy</button></td>
                                        </tr>
                                        <tr>
                                            <td class="p-2">@{{ $setting->linkedin }}</td>
                                            <td class="text-end p-2"><button class="btn btn-xs btn-outline-primary" onclick="copyText('@{{ $setting->linkedin }}')">Copy</button></td>
                                        </tr>
                                        <tr>
                                            <td class="p-2">@{{ $setting->youtube }}</td>
                                            <td class="text-end p-2"><button class="btn btn-xs btn-outline-primary" onclick="copyText('@{{ $setting->youtube }}')">Copy</button></td>
                                        </tr>
                                        <tr>
                                            <td class="p-2">@{{ $setting->tiktok }}</td>
                                            <td class="text-end p-2"><button class="btn btn-xs btn-outline-primary" onclick="copyText('@{{ $setting->tiktok }}')">Copy</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-12">
    <h6 class="fw-bold text-primary border-bottom pb-2">Looping Examples (Copy & Paste to Editor)</h6>

    <ul class="nav nav-pills mb-3 small" id="pills-tab" role="tablist">
        <li class="nav-item"><button class="nav-link active py-1" data-bs-toggle="pill" data-bs-target="#loop-posts">Latest Posts</button></li>
        <li class="nav-item"><button class="nav-link py-1" data-bs-toggle="pill" data-bs-target="#loop-gallery">Gallery</button></li>
        <li class="nav-item"><button class="nav-link py-1" data-bs-toggle="pill" data-bs-target="#loop-youtube">Youtube</button></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="loop-posts">
            <div class="bg-dark text-warning p-3 rounded shadow-inner position-relative">
                <button class="btn btn-xs btn-light position-absolute top-0 end-0 m-2" onclick="copyText('latest-posts-code')">Copy</button>
                <pre class="mb-0" style="font-size: 0.75rem;" id="latest-posts-code">
@@forelse ($latestposts as $post)
    @@forelse ($post->gambar() as $gam)
        @@if ($loop->first)
            &lt;img src="@{{ $gam }}" alt="@{{ $post->title }}" class="img-fluid" loading="lazy"&gt;
        @@endif
    @@empty
        &lt;img src="/frontend/img/default.webp" class="img-fluid"&gt;
    @@endforelse
    @{{ formatDate($post->datepublish) }}
    @{{ $post->title }}
    @{{ $post->category->name }}
@@empty
    &lt;p&gt;Belum ada Berita&lt;/p&gt;
@@endforelse</pre>
            </div>
        </div>

        <div class="tab-pane fade" id="loop-gallery">
            <div class="bg-dark text-warning p-3 rounded shadow-inner position-relative">
                <button class="btn btn-xs btn-light position-absolute top-0 end-0 m-2" onclick="copyText('gallery-code')">Copy</button>
                <pre class="mb-0" style="font-size: 0.75rem;" id="gallery-code">
@@foreach ($gallery as $galleries)
    &lt;div class="col-lg-3 col-md-12"&gt;
        &lt;a href="/storage/@{{ $galleries->image_path }}" class="glightbox"&gt;
            &lt;img src="/storage/@{{ $galleries->image_path }}" alt="@{{ $galleries->name }}" class="img-fluid"&gt;
        &lt;/a&gt;
    &lt;/div&gt;
@@endforeach</pre>
            </div>
        </div>

        <div class="tab-pane fade" id="loop-youtube">
            <div class="bg-dark text-warning p-3 rounded shadow-inner position-relative">
                <button class="btn btn-xs btn-light position-absolute top-0 end-0 m-2" onclick="copyText('youtube-code')">Copy</button>
                <pre class="mb-0" style="font-size: 0.75rem;" id="youtube-code">
@@foreach ($youtubes as $video)
    &lt;div class="col"&gt;
        &lt;div class="video-item" data-video-id="@{{ $video->video_id }}"&gt;
            &lt;img src="@{{ $video->thumbnail_url }}" alt="Thumbnail"&gt;
            &lt;i class="fab fa-youtube"&gt;&lt;/i&gt;
        &lt;/div&gt;
    &lt;/div&gt;
@@endforeach</pre>
            </div>
        </div>
    </div>
</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@once
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css">
<style>
    .CodeMirror {
        height: 100% !important;
        font-family: 'Fira Code', monospace;
        font-size: 13px;
    }

</style>
@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/php/php.min.js"></script>
<script>
    function getModeForLanguage(lang) {
        const modes = {
            'js': 'javascript'
            , 'javascript': 'javascript'
            , 'css': 'text/css'
            , 'php': 'application/x-httpd-php'
            , 'html': 'text/html'
            , 'blade': 'application/x-httpd-php'
        };
        return modes[lang] || 'text/plain';
    }

    function copyText(elementIdOrText) {
        let text = '';
        const el = document.getElementById(elementIdOrText);
        if (el) {
            text = el.innerText.replace(/@@/g, '@'); // Bersihkan double @ untuk copy
        } else {
            text = elementIdOrText;
        }

        navigator.clipboard.writeText(text).then(() => {
            alert('Tersalin ke clipboard!');
        });
    }

</script>
@endpush
@endonce
