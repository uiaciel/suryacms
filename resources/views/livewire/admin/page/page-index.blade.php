<div class="w-full">
    <x-suryacms::import-export-offcanvas />

    <header class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <h3 class="font-bold text-gray-800 text-2xl mb-0">Pages</h3>
            <a href="{{ route('admin.page.create') }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-full shadow-sm text-sm hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Create New Page
            </a>
        </div>

        <nav aria-label="breadcrumb" class="hidden sm:block">
            <ol class="flex gap-2 text-sm text-gray-600">
                <li><a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-700">Admin</a></li>
                <li>/</li>
                <li><a href="/admin/pages" class="text-blue-600 hover:text-blue-700">Pages</a></li>
                <li>/</li>
                <li class="text-gray-600">{{ $titlePage }}</li>
            </ol>
        </nav>
    </header>

    <x-suryacms::session-status />

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="p-6" x-data="{
            search: '',
            phpFormat: '{{ get_date_format() ?? 'm/d/Y' }}',
            format_tanggal(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                if (isNaN(date)) return dateString;
                const pad = (num) => String(num).padStart(2, '0');
                const Y = date.getFullYear();
                const m = pad(date.getMonth() + 1);
                const d = pad(date.getDate());
                const M = date.toLocaleDateString('id-ID', { month: 'short' });
                switch (this.phpFormat) {
                    case 'd/m/Y':
                        return `${d}/${m}/${Y}`;
                    case 'Y-m-d':
                        return `${Y}-${m}-${d}`;
                    case 'm/d/Y':
                        return `${m}/${d}/${Y}`;
                    case 'd-M-Y':
                        return `${d}-${M}-${Y}`;
                    default:
                        return date.toLocaleDateString('id-ID');
                }
            },
            pages: {{ Js::from($pages) }},
            filteredPages() {
                return this.pages.filter(page => {
                    return this.search === '' ||
                        page.title.toLowerCase().includes(this.search.toLowerCase()) ||
                        page.status.toLowerCase().includes(this.search.toLowerCase());
                });
            }
        }">

            <div class="flex justify-between items-center mb-6">
                <h5 class="font-bold text-lg text-blue-600 mb-0">All Pages</h5>
            </div>

            {{-- Search and Filter Section --}}
            <div class="p-4 rounded-lg border border-blue-200 bg-blue-50 mb-6">
                <div class="max-w-md">
                    <label for="search-input" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <div class="flex items-center border border-gray-300 rounded-lg bg-white">
                        <span class="px-3 text-gray-400"><i class="fas fa-search"></i></span>
                        <input type="text" id="search-input"
                            class="w-full px-3 py-2 border-0 focus:outline-none focus:ring-0"
                            placeholder="Search by title or status..." x-model.debounce.300ms="search">
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 text-sm">#</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 text-sm">Title</th>
                            @if ($setting->is_multilingual == 'Yes')
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 text-sm">Lang</th>
                            @endif
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 text-sm">PDF</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 text-sm">Date Publish</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 text-sm">Status</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700 text-sm">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(page, index) in filteredPages()" :key="page.id">
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm" x-text="index + 1"></td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-800" x-text="page.title"></div>
                                    <small class="text-gray-500" x-text="'/' + page.slug"></small><a
                                        :href="'/' + page.slug"
                                        class="ml-2 text-blue-600 hover:text-blue-800 transition" title="View"
                                        target="_blank">
                                        <i class="fa-solid fa-up-right-from-square"></i>
                                    </a>
                                    <template x-if="page.is_builder">
                                        <a href="/admin/page-builder/"
                                            class="ml-2 inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 rounded text-[10px] font-bold uppercase hover:bg-emerald-200 transition">
                                            <i class="fa-solid fa-cubes"></i> Edit with Builder
                                        </a>
                                    </template>
                                </td>
                                @if ($setting->is_multilingual == 'Yes')
                                    <td class="px-4 py-3 text-center">
                                        <template x-if="page.language_id == 1">
                                            <img src="/assets/images/id.png" width="20" class="inline rounded"
                                                alt="ID">
                                        </template>
                                        <template x-if="page.language_id == 2">
                                            <img src="/assets/images/us.png" width="20" class="inline rounded"
                                                alt="US">
                                        </template>
                                    </td>
                                @endif
                                <td class="px-4 py-3 text-center">
                                    <template x-if="page.pdf">
                                        <a :href="'/storage/' + page.pdf" target="_blank" class="text-red-600 text-xl">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </template>
                                    <template x-if="!page.pdf">
                                        <i class="fas fa-minus text-gray-300"></i>
                                    </template>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600" x-text="format_tanggal(page.datepublish)">
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        :class="page.status === 'Publish' ?
                                            'px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium' :
                                            'px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium'"
                                        x-text="page.status"></span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex gap-2">
                                        <a :href="'/admin/pages/edit/' + page.id"
                                            class="p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                                            title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <button @click="if(confirm('Are you sure?')) $wire.deletePage(page.id)"
                                            class="p-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                                            title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredPages().length === 0">
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500 text-sm">No pages found.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
