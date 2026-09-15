<div
    class="space-y-6"
    x-data="postGenerator({
        websiteName: @js($setting->url ?? config('app.name')),
        tagline: @js($setting->tagline ?? ''),
        description: @js($setting->description ?? ''),
        categories: @js($categories->map(fn ($category) => ['id' => (string) $category->id, 'name' => $category->name])->values()),
    })"
>

    <header class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <h3 class="font-bold text-gray-800 text-2xl mb-0">Generate Posts</h3>

        </div>

        <nav aria-label="breadcrumb" class="hidden sm:block">
            <ol class="flex gap-2 text-sm text-gray-600">
                <li><a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-700">Admin</a></li>
                <li>/</li>
                <li><a href="/{{ config('suryacms.admin_prefix') }}/posts"
                        class="text-blue-600 hover:text-blue-700">Posts</a></li>
                <li>/</li>
                <li class="text-gray-600">Generate</li>
            </ol>
        </nav>
    </header>

{{-- Header --}}
<x-suryacms::session-status />
<x-suryacms::toast-alert />
{{-- Success Message --}}
@if ($message)
    <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400">
        <div class="flex items-center gap-2">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 13l4 4L19 7" />
            </svg>

            <span>{{ $message }}</span>
        </div>
    </div>
@endif

{{-- Error Message --}}
@if ($error)
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400">
        <div class="flex items-center gap-2">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>

            <span>{{ $error }}</span>
        </div>
    </div>
@endif

{{-- Generate Prompt --}}
<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">

    <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
        <h3 class="font-semibold text-gray-900 dark:text-white">
            Generate Prompt
        </h3>

        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Tentukan jumlah artikel dan kategori yang ingin dibuat.
        </p>
    </div>

    <div class="p-5">

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

            {{-- Quantity --}}
            <div>

                <label
                    for="quantity"
                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Jumlah Artikel
                </label>

                <input
                    id="quantity"
                    type="number"
                    min="1"
                    max="50"
                    x-model.number="quantity"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                    placeholder="Contoh: 5"
                >

                @error('quantity')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                        {{ $message }}
                    </p>
                @enderror

                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Maksimal 50 artikel dalam satu proses.
                </p>

            </div>

            {{-- Category --}}
            <div>

                <label
                    for="selectedCategory"
                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Kategori Prioritas
                </label>

                <select
                    id="selectedCategory"
                    x-model="selectedCategory"
                    @change="$wire.set('selectedCategory', selectedCategory)"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                >

                    <option value="">
                        Semua / AI menentukan
                    </option>

                    @foreach ($categories as $category)

                        <option value="{{ $category->id }}">
                            {{ $category->name }}
                        </option>

                    @endforeach

                </select>

                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Kategori ini akan menjadi topik utama artikel.
                </p>

            </div>

        </div>

        {{-- Website Context --}}
        <div class="mt-5 rounded-lg bg-gray-50 p-4 dark:bg-gray-700/50">

            <h4 class="mb-3 text-sm font-semibold text-gray-800 dark:text-gray-200">
                Informasi yang digunakan
            </h4>

            <div class="grid grid-cols-1 gap-3 text-sm md:grid-cols-3">

                <div>
                    <span class="block text-xs text-gray-500 dark:text-gray-400">
                        Website
                    </span>

                    <span class="font-medium text-gray-800 dark:text-gray-200">
                        {{ $setting->url ?? config('app.name') }}
                    </span>
                </div>

                <div>
                    <span class="block text-xs text-gray-500 dark:text-gray-400">
                        Tagline
                    </span>

                    <span class="font-medium text-gray-800 dark:text-gray-200">
                        {{ $setting->tagline ?: '-' }}
                    </span>
                </div>

                <div>
                    <span class="block text-xs text-gray-500 dark:text-gray-400">
                        Kategori
                    </span>

                    <span class="font-medium text-gray-800 dark:text-gray-200">
                        {{ $categories->count() }} kategori tersedia
                    </span>
                </div>

            </div>

        </div>

        {{-- Generate Button --}}
        <div class="mt-5">

            <button
                type="button"
                @click="generatePrompt()"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 disabled:cursor-not-allowed disabled:opacity-60 dark:focus:ring-blue-800"
            >

                {{-- Normal --}}
                <span
                    class="inline-flex items-center gap-2"
                >

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>

                    Generate Post

                </span>

            </button>

        </div>

    </div>

</div>

{{-- Generated Prompt --}}
    <div
        x-cloak
        x-show="generatedPrompt"
        class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
    >

        <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-gray-700">

            <div>

                <h3 class="font-semibold text-gray-900 dark:text-white">
                    Prompt AI
                </h3>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Copy prompt ini lalu gunakan pada AI pilihan Anda.
                </p>

            </div>

            <button
                type="button"
                onclick="copyGeneratedPrompt()"
                class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 dark:focus:ring-gray-700"
            >

                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2M16 20h2a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2h4z" />
                </svg>

                Copy Prompt

            </button>

        </div>

        <div class="p-5">

            <textarea
                id="generatedPrompt"
                readonly
                x-model="generatedPrompt"
                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-4 font-mono text-xs leading-6 text-gray-800 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300"
                rows="24"
            ></textarea>

        </div>

    </div>

{{-- Import Artikel --}}
<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">

    <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">

        <div class="flex items-start gap-3">

            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400">

                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M12 12v9m0 0l-3-3m3 3l3-3" />
                </svg>

            </div>

            <div>

                <h3 class="font-semibold text-gray-900 dark:text-white">
                    Import Artikel dari AI
                </h3>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Tempel hasil JSON dari AI ke dalam form berikut.
                </p>

            </div>

        </div>

    </div>

    <div class="p-5">

        {{-- Info --}}
        <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">

            <div class="flex gap-3">

                <svg
                    class="mt-0.5 h-5 w-5 shrink-0 text-blue-600 dark:text-blue-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 110-18 9 9 0 010 18z"
                    />
                </svg>

                <div class="text-sm text-blue-700 dark:text-blue-300">

                    <p class="font-medium">
                        Artikel akan masuk sebagai Draft
                    </p>

                    <p class="mt-1">
                        Setelah diimport, artikel tidak langsung dipublikasikan.
                        Admin dapat mengecek dan mengedit artikel terlebih dahulu.
                    </p>

                </div>

            </div>

        </div>

        {{-- JSON Input --}}
        <div>

            <label
                for="importJson"
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
            >
                JSON Artikel
            </label>

            <textarea
                id="importJson"
                wire:model="importJson"
                rows="20"
                spellcheck="false"
                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-4 font-mono text-xs leading-6 text-gray-900 outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300"
                placeholder='{
"articles": [
    {
        "title": "Judul artikel",
        "slug": "judul-artikel",
        "content": "Isi artikel...",
        "category": "Kategori",
        "tags": [
            "tag 1",
            "tag 2"
        ]
    }
]

}'></textarea>
        </div>

        {{-- Import Button --}}
        <div class="mt-5 flex items-center justify-between gap-3">

            <p class="text-xs text-gray-500 dark:text-gray-400">
                Pastikan JSON yang ditempel valid.
            </p>

            <button
                type="button"
                wire:click="importArticles"
                wire:loading.attr="disabled"
                class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-green-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300 disabled:cursor-not-allowed disabled:opacity-60 dark:focus:ring-green-800"
            >

                {{-- Normal --}}
                <span
                    wire:loading.remove
                    wire:target="importArticles"
                    class="inline-flex items-center gap-2"
                >

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6L16 6a5 5 0 011 9.9M12 12v9m0 0l-3-3m3 3l3-3"
                        />
                    </svg>

                    Import Artikel

                </span>

                {{-- Loading --}}
                <span
                    wire:loading
                    wire:target="importArticles"
                    class="inline-flex items-center gap-2"
                >

                    <svg
                        class="h-5 w-5 animate-spin"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        />

                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                        />
                    </svg>

                    Mengimport...

                </span>

            </button>

        </div>

    </div>

</div>

</div>

@push('scripts')

<script>

function postGenerator(data)
{
    return {
        ...data,
        quantity: 1,
        selectedCategory: '',
        generatedPrompt: '',

        generatePrompt()
        {
            const quantity = Number(this.quantity);

            if (!Number.isInteger(quantity) || quantity < 1 || quantity > 50) {
                alert('Jumlah artikel harus berupa angka antara 1 dan 50.');
                return;
            }

            const category = this.categories.find(
                (item) => item.id === String(this.selectedCategory)
            );
            const categoryName = category?.name || 'sesuai dengan topik website';
            const categoryList = this.categories.map((item) => item.name).join(', ');

            this.generatedPrompt = `Anda adalah seorang penulis artikel profesional untuk website ${this.websiteName}.

INFORMASI WEBSITE:

Nama Website:
${this.websiteName}

Tagline:
${this.tagline}

Deskripsi Website:
${this.description}

Kategori artikel yang tersedia:
${categoryList}

KATEGORI YANG DIPRIORITASKAN:
${categoryName}

TUGAS:

Buat ${quantity} artikel original dan informatif yang relevan dengan website tersebut.

Artikel harus:
- Menggunakan Bahasa Indonesia yang natural dan mudah dipahami.
- Memiliki judul yang menarik.
- Tidak menggunakan clickbait berlebihan.
- Relevan dengan kategori yang tersedia.
- Memberikan informasi yang bermanfaat bagi pembaca.
- Memiliki struktur artikel yang jelas.
- Menggunakan HTML semantik agar konten rapi saat dibuka di editor.
- Membungkus setiap paragraf dengan tag <p>.
- Menggunakan <h2> atau <h3> untuk subjudul jika diperlukan.
- Menggunakan <ul> atau <ol> dan <li> untuk daftar.
- Menggunakan <strong> atau <em> untuk penekanan seperlunya.
- Menggunakan <a href="URL"> hanya jika terdapat tautan yang relevan.
- Tidak menyebutkan bahwa artikel dibuat oleh AI.
- Tidak menggunakan markdown.
- Jangan membuat informasi atau fakta yang tidak masuk akal.
- Setiap artikel harus memiliki slug yang sesuai dengan judul.
- Setiap artikel harus memiliki tags yang relevan.

CONTENT HTML:
- Field content wajib berisi HTML valid, bukan Markdown atau teks polos.
- Jangan membungkus seluruh content dengan tag <html>, <head>, atau <body>.
- Jangan menggunakan style inline, script, iframe, atau tag berbahaya lainnya.
- Gunakan tag HTML sederhana yang didukung editor.

FORMAT OUTPUT:

WAJIB mengembalikan hasil hanya dalam format JSON yang valid.

Jangan gunakan:
- Markdown
- \`\`\`json
- \`\`\`
- Penjelasan sebelum JSON
- Penjelasan setelah JSON

Gunakan struktur JSON berikut:

{
    "articles": [
        {
            "title": "Judul artikel",
            "slug": "judul-artikel",
            "content": "<p>Paragraf pembuka artikel.</p><h2>Subjudul</h2><p>Isi artikel yang informatif.</p>",
            "category": "${categoryName}",
            "tags": [
                "tag 1",
                "tag 2",
                "tag 3"
            ]
        }
    ]
}

Jumlah artikel wajib tepat ${quantity} artikel.

Pastikan JSON valid dan dapat langsung diproses oleh aplikasi.`;
        },
    };
}

function copyGeneratedPrompt()
{
    const textarea = document.getElementById('generatedPrompt');

    if (!textarea) {
        return;
    }

    navigator.clipboard.writeText(textarea.value)
        .then(() => {
            showCopyMessage();
        })
        .catch(() => {

            textarea.select();
            textarea.setSelectionRange(0, 999999);

            document.execCommand('copy');

            showCopyMessage();

        });
}

function showCopyMessage()
{
    // Bisa diganti dengan toast notification
    // jika SuryaCMS sudah memiliki komponen toast.

    alert('Prompt berhasil disalin.');
}

</script>

@endpush
