@extends('suryacms::layouts.app')

@section('content')
    <h2 class="text-2xl font-bold text-gray-800">Code Playground</h2>
    <p class="text-sm text-gray-500 mb-4">Copy Paste HTML, CSS, JS, atau Blade Code untuk diedit dan diformat.</p>

    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
        <div class="p-4 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                <div class="w-full md:w-1/3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Mode Bahasa:</label>
                    <select id="mode-select"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="htmlmixed">HTML</option>
                        <option value="css">CSS</option>
                        <option value="javascript">JavaScript</option>
                        <option value="php">Blade (PHP)</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        class="flex-1 md:flex-none px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition shadow-sm"
                        onclick="copyToClipboard()">Copy Code</button>
                    <button
                        class="flex-1 md:flex-none px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300 transition shadow-sm"
                        onclick="clearEditor()">Clear</button>
                </div>
            </div>

            <div class="border rounded-md overflow-hidden">
                <textarea id="code-editor" name="code"></textarea>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/monokai.min.css">
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/css/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/htmlmixed/htmlmixed.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/php/php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/fold/foldcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/fold/foldgutter.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/fold/xml-fold.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/fold/brace-fold.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/search/searchcursor.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/search/search.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/dialog/dialog.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/fold/foldgutter.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/dialog/dialog.min.css">

    <script>
        var editor = CodeMirror.fromTextArea(document.getElementById("code-editor"), {
            lineNumbers: true,
            mode: "htmlmixed",
            theme: "monokai",
            indentUnit: 4,
            autoCloseTags: true,
            matchBrackets: true,
            // Fitur Code Folding
            foldGutter: true,
            gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"]
        });

        // Ganti Mode Bahasa
        document.getElementById('mode-select').addEventListener('change', function() {
            editor.setOption("mode", this.value);
        });

        function copyToClipboard() {
            const content = editor.getValue();
            navigator.clipboard.writeText(content).then(() => {
                alert("Kode berhasil disalin!");
            });
        }

        function clearEditor() {
            editor.setValue("");
        }
    </script>
@endpush
