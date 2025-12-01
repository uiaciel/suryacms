<div>

    <div class="row">

        <div class="col-md-12">

        </div>

        @if (session()->has('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>

                        <h2 class="">{{ $key ? 'Edit' : 'Create' }} Section <small><button wire:click="save"
                                    class="btn btn-primary btn-sm">{{ $key ? 'Update' : 'Save' }}</button></small></h2>
                    </div>
                    <!-- Save Button -->
                    <div class="">
                        @if (!$layout)
                            <button type="button" wire:click="addColumn" class="btn btn-success">Add
                                Column</button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">

                        <div class="mb-3">
                            <label for="key" class="form-label">Section Key</label>
                            <input type="text" wire:model="key" id="key" class="form-control"
                                placeholder="Unique key for this section">
                            @error('key')
                                <div class="text-red-500 text-sm">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="col-md-3">
                        <!-- Layout -->
                        <div class="mb-3">
                            <label class="form-label">Layout</label>
                            <select class="form-control" wire:model="layout" wire:change="updateColumnsFromLayout">
                                <option value="1-column">1 Column</option>
                                <option value="2-column">2 Column</option>
                                <option value="3-column">3 Column</option>
                                <option value="4-column">4 Column</option>
                            </select>

                        </div>

                    </div>
                    <div class="col-md-3">
                        <!-- Settings (Animation, Spacing) -->
                        <div class="mb-3">
                            <label for="animation" class="form-label">Animation</label>
                            <input type="text" wire:model="settings.animation" id="animation" class="form-control"
                                placeholder="Enter animation name (e.g., fade-in)">
                        </div>

                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="spacing" class="form-label">Spacing</label>
                            <input type="text" wire:model="settings.spacing" id="spacing" class="form-control"
                                placeholder="Enter spacing class (e.g., mb-8)">
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <div class="row">

        <h5 class="mb-3">Preview Section</h5>

        @if ($columns)
            <div class="row">
                @foreach ($columns as $column)
                    <div class="col-{{ 12 / count($columns) }} mb-3">
                        <div class="p-3 border rounded {{ $column['class'] ?? '' }}"
                            style="{{ $column['styleString'] ?? '' }}">
                            {!! $column['html'] ?? '' !!}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted fst-italic">Belum ada konten untuk ditampilkan.</p>
        @endif

        <hr class="my-4">
        <h3 class="text-lg font-semibold mb-2">Edit Form Section</h3>

        @foreach ($columns as $index => $column)
            <div class="col mb-3">
                <div class="card">

                    <div class="card-body">
                        <div class="card-title">
                            <h5 class="">Column {{ $index + 1 }}</h5>
                        </div>
                        <div class="mb-2" wire:ignore>
                            <label for="html_{{ $index }}" class="form-label">HTML Content</label>
                            <textarea wire:model="columns.{{ $index }}.html" data-tinymce id="html_{{ $index }}" class="form-control"
                                placeholder="Enter HTML content"></textarea>
                        </div>

                        <div class="mb-2">
                            <label for="class_{{ $index }}" class="form-label">Class</label>
                            <input type="text" wire:model="columns.{{ $index }}.class"
                                id="class_{{ $index }}" class="form-control"
                                placeholder="Enter custom class for this column">
                        </div>

                        <div class="mb-2">
                            <label for="style_{{ $index }}" class="form-label">Style (CSS)</label>
                            <textarea wire:model="columns.{{ $index }}.styleString" id="style_{{ $index }}" class="form-control"
                                placeholder="Enter style rules e.g. 'background: #f5f5f5;'"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @push('scripts')
        <script
            src="https://cdn.tiny.cloud/1/plcsua64qzb9xyaxabkhv7wtjcpr0vzounlnexchn12g0x7a/tinymce/5.10.9-138/tinymce.min.js"
            referrerpolicy="origin"></script>

        {{-- <script>
            tinymce.init({
                selector: 'textarea',
                skin: 'bootstrap',
                content_css: "/frontend/assets/css/bootstrap.min.css",
                image_class_list: [{
                        title: 'image-left',
                        value: 'rounded float-start'
                    },
                    {
                        title: 'image-right',
                        value: 'rounded float-end'
                    },
                    {
                        title: 'image-center',
                        value: 'rounded mx-auto d-block'
                    },
                    {
                        title: 'image-responsive',
                        value: 'rounded img-fluid'
                    },
                    {
                        title: 'image-hidden',
                        value: 'style="display:none;"'
                    }
                ],
                height: 500,

                init_instance_callback: function(editor) {
                    var content = editor.getContent();

                    content = content.replace(/\.\.\/\.\.\/\.\.\//g, '/')
                        .replace(/\.\.\/\.\.\//g, '/')
                        .replace(/\.\.\//g, '/');

                    editor.setContent(content);
                },

                setup: function(editor) {
                    editor.on('init change', function() {
                        editor.save();
                    });
                    editor.on('change', function(e) {
                        @this.set('konten', editor.getContent());
                    });
                },

                plugins: [
                    "code advlist autolink lists link image charmap print preview anchor",
                    "searchreplace visualblocks code fullscreen",
                    "insertdatetime media table contextmenu paste imagetools"
                ],
                toolbar: [
                    "code paste preview | image link undo redo ",
                    "bold italic underline forecolor backcolor",
                    " alignleft aligncenter alignright alignjustify"
                ],
                // toolbar: "code paste | undo redo preview spellcheckdialog formatpainter |  ",
                // toolbar_mode: "wrap",
                menubar: false,
                image_title: true,
                automatic_uploads: true,

                images_upload_handler: function(blobInfo, success, failure) {
                    var xhr, formData;
                    xhr = new XMLHttpRequest();
                    xhr.withCredentials = false;
                    xhr.open('POST', '{{ route('upload') }}');
                    var token = '{{ csrf_token() }}';
                    xhr.setRequestHeader("X-CSRF-Token", token);
                    xhr.onload = function() {
                        var json;
                        if (xhr.status != 200) {
                            failure('HTTP Error: ' + xhr.status);
                            return;
                        }
                        json = JSON.parse(xhr.responseText);
                        if (!json || typeof json.location != 'string') {
                            failure('Invalid JSON: ' + xhr.responseText);
                            return;
                        }
                        success(json.location);
                    };
                    formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    xhr.send(formData);
                },
                file_picker_types: 'image',
                file_picker_callback: function(cb, value, meta) {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    input.onchange = function() {
                        var file = this.files[0];
                        var reader = new FileReader();
                        reader.readAsDataURL(file);
                        reader.onload = function() {
                            var id = 'blobid' + (new Date()).getTime();
                            var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                            var base64 = reader.result.split(',')[1];
                            var blobInfo = blobCache.create(id, file, base64);
                            blobCache.add(blobInfo);
                            cb(blobInfo.blobUri(), {
                                title: file.name
                            });
                        };
                    };
                    input.click();
                }

            });
        </script> --}}
    @endpush

</div>
