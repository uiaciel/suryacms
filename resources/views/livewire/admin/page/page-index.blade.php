<div class="w-full p-4">
    <x-suryacms::import-export-offcanvas />

    <header class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <h3 class="font-bold text-gray-800 text-2xl mb-0">Pages</h3>
            <a href="{{ route('admin.page.create') }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-full shadow-sm text-sm hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>New Page
            </a>
        </div>

        <nav aria-label="breadcrumb" class="hidden sm:block">
            <ol class="flex gap-2 text-sm text-gray-600">
                <li><a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-700">Admin</a></li>
                <li>/</li>
                <li><a href="/{{ config('suryacms.admin_prefix') }}/pages"
                        class="text-blue-600 hover:text-blue-700">Pages</a></li>
                <li>/</li>
                <li class="text-gray-600">{{ $titlePage }}</li>
            </ol>
        </nav>
    </header>

    <x-suryacms::session-status />
    <x-suryacms::toast-alert />

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="p-4 sm:p-6" x-data="{
            search: '',
            status: '',
            pdfFilter: '',
            language: '',
            category: '',
            showFilter: false,

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
                    case 'd/m/Y': return `${d}/${m}/${Y}`;
                    case 'Y-m-d': return `${Y}-${m}-${d}`;
                    case 'm/d/Y': return `${m}/${d}/${Y}`;
                    case 'd-M-Y': return `${d}-${M}-${Y}`;
                    default: return date.toLocaleDateString('id-ID');
                }
            },

            pages: {{ Js::from($pages) }},

            get categories() {
                const set = new Set();
                this.pages.forEach(p => {
                    if (p.category && p.category.name) set.add(p.category.name);
                });
                return Array.from(set).sort();
            },

            filteredPages() {
                return this.pages.filter(page => {
                    const q = this.search.toLowerCase();
                    const matchSearch = this.search === '' ||
                        page.title.toLowerCase().includes(q) ||
                        (page.slug && page.slug.toLowerCase().includes(q)) ||
                        (page.status && page.status.toLowerCase().includes(q)) ||
                        (page.category && page.category.name && page.category.name.toLowerCase().includes(q));

                    const matchStatus = this.status === '' || page.status === this.status;

                    const matchPdf = this.pdfFilter === '' ||
                        (this.pdfFilter === 'yes' && page.pdf) ||
                        (this.pdfFilter === 'no' && !page.pdf);

                    const matchLang = this.language === '' || String(page.language_id) === String(this.language);

                    const matchCategory = this.category === '' ||
                        (page.category && page.category.name === this.category);

                    return matchSearch && matchStatus && matchPdf && matchLang && matchCategory;
                });
            },

            resetFilters() {
                this.search = '';
                this.status = '';
                this.pdfFilter = '';
                this.language = '';
                this.category = '';
            },

            activeFilterCount() {
                let n = 0;
                if (this.search) n++;
                if (this.status) n++;
                if (this.pdfFilter) n++;
                if (this.language) n++;
                if (this.category) n++;
                return n;
            }
        }">

            {{-- Header row: title + toggle filter --}}
            <div class="flex justify-between items-center gap-3 mb-4 sm:mb-6">
                <h5 class="font-bold text-base sm:text-lg text-blue-600 mb-0">All Pages</h5>

                <div class="flex items-center gap-2">
                    <button type="button"
                        @click="showFilter = !showFilter"
                        class="px-3 py-2 border border-blue-300 text-blue-600 rounded-full text-sm font-medium hover:bg-blue-50 transition inline-flex items-center gap-1.5"
                        :aria-expanded="showFilter">
                        <i class="fas fa-filter"></i>
                        <span x-text="showFilter ? 'Hide' : 'Filter'"></span>
                        <template x-if="activeFilterCount() > 0">
                            <span class="ml-1 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 bg-blue-600 text-white text-[10px] font-bold rounded-full"
                                x-text="activeFilterCount()"></span>
                        </template>
                    </button>

                    <a href="/{{ config('suryacms.admin_prefix') }}/page-builder"
                        class="px-3 sm:px-4 py-2 bg-blue-600 text-white rounded-full shadow-sm text-sm hover:bg-blue-700 transition whitespace-nowrap">
                        <i class="fa-solid fa-code mr-1 sm:mr-2"></i>Builder
                    </a>
                </div>
            </div>

            {{-- Search and Filter Section --}}
            <div
                class="rounded-lg border border-blue-200 bg-blue-50 mb-4 sm:mb-6 overflow-hidden"
                x-show="showFilter"
                x-cloak
                x-transition.opacity.duration.200ms
                :class="{ 'hidden lg:block': !showFilter }">
                <div class="p-3 sm:p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 sm:gap-4 items-end">

                    {{-- Search --}}
                    <div class="sm:col-span-2 lg:col-span-2">
                        <label for="search-input" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">Search</label>
                        <div class="flex items-center border border-gray-300 rounded-lg bg-white">
                            <span class="px-3 text-gray-400 text-sm"><i class="fas fa-search"></i></span>
                            <input type="text" id="search-input"
                                class="w-full px-3 py-2 text-sm border-0 focus:outline-none focus:ring-0"
                                placeholder="Search by title, slug, category, status..."
                                x-model.debounce.300ms="search"
                                aria-label="Search pages">
                        </div>
                    </div>

                    {{-- Category (auto dari data) --}}
                    <template x-if="categories.length > 0">
                        <div>
                            <label for="filter-category" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">Category</label>
                            <select id="filter-category"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                x-model="category">
                                <option value="">All Categories</option>
                                <template x-for="cat in categories" :key="cat">
                                    <option :value="cat" x-text="cat"></option>
                                </template>
                            </select>
                        </div>
                    </template>

                    {{-- Status --}}
                    <div>
                        <label for="filter-status" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">Status</label>
                        <select id="filter-status"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            x-model="status">
                            <option value="">All Statuses</option>
                            <option value="Publish">Publish</option>
                            <option value="Draft">Draft</option>
                        </select>
                    </div>

                    {{-- PDF --}}
                    <div>
                        <label for="filter-pdf" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">PDF</label>
                        <select id="filter-pdf"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            x-model="pdfFilter">
                            <option value="">All</option>
                            <option value="yes">Has PDF</option>
                            <option value="no">No PDF</option>
                        </select>
                    </div>

                    {{-- Language (hanya jika multilingual) --}}
                    @if ($setting->is_multilingual == 'Yes')
                        <div>
                            <label for="filter-language" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">Language</label>
                            <select id="filter-language"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                x-model="language">
                                <option value="">All Languages</option>
                                <option value="1">Indonesia</option>
                                <option value="2">English</option>
                            </select>
                        </div>
                    @endif

                    {{-- Reset --}}
                    <div class="{{ $setting->is_multilingual == 'Yes' ? '' : 'sm:col-span-2 lg:col-span-1' }}">
                        <button type="button"
                            class="w-full px-4 py-2 text-sm border border-gray-300 text-gray-700 bg-white rounded-lg hover:bg-gray-50 font-medium inline-flex items-center justify-center gap-2"
                            @click="resetFilters()">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </div>

                {{-- Active filter chips (mobile & desktop) --}}
                <template x-if="activeFilterCount() > 0">
                    <div class="px-3 sm:px-4 pb-3 flex flex-wrap gap-1.5 -mt-1">
                        <template x-if="search">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-white border border-blue-200 text-blue-700 text-xs rounded-full">
                                Search: <strong x-text="search"></strong>
                                <button @click="search = ''" class="text-blue-400 hover:text-blue-700"><i class="fas fa-times"></i></button>
                            </span>
                        </template>
                        <template x-if="category">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-white border border-blue-200 text-blue-700 text-xs rounded-full">
                                Kategori: <strong x-text="category"></strong>
                                <button @click="category = ''" class="text-blue-400 hover:text-blue-700"><i class="fas fa-times"></i></button>
                            </span>
                        </template>
                        <template x-if="status">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-white border border-blue-200 text-blue-700 text-xs rounded-full">
                                Status: <strong x-text="status"></strong>
                                <button @click="status = ''" class="text-blue-400 hover:text-blue-700"><i class="fas fa-times"></i></button>
                            </span>
                        </template>
                        <template x-if="pdfFilter">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-white border border-blue-200 text-blue-700 text-xs rounded-full">
                                PDF: <strong x-text="pdfFilter === 'yes' ? 'Has PDF' : 'No PDF'"></strong>
                                <button @click="pdfFilter = ''" class="text-blue-400 hover:text-blue-700"><i class="fas fa-times"></i></button>
                            </span>
                        </template>
                        <template x-if="language">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-white border border-blue-200 text-blue-700 text-xs rounded-full">
                                Lang: <strong x-text="language == 1 ? 'Indonesia' : 'English'"></strong>
                                <button @click="language = ''" class="text-blue-400 hover:text-blue-700"><i class="fas fa-times"></i></button>
                            </span>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Result info --}}
            <div class="mb-2 text-xs text-gray-500">
                Showing <span class="font-semibold text-gray-700" x-text="filteredPages().length"></span>
                of <span class="font-semibold text-gray-700" x-text="pages.length"></span> pages
            </div>

            <div class="overflow-x-auto -mx-4 sm:mx-0">
                <table class="w-full min-w-[640px]">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-3 sm:px-4 py-3 text-left font-semibold text-gray-700 text-xs sm:text-sm">#</th>
                            <th class="px-3 sm:px-4 py-3 text-left font-semibold text-gray-700 text-xs sm:text-sm">Title</th>
                            @if ($setting->is_multilingual == 'Yes')
                                <th class="px-3 sm:px-4 py-3 text-center font-semibold text-gray-700 text-xs sm:text-sm">Lang</th>
                            @endif
                            <th class="px-3 sm:px-4 py-3 text-center font-semibold text-gray-700 text-xs sm:text-sm">PDF</th>
                            <th class="px-3 sm:px-4 py-3 text-left font-semibold text-gray-700 text-xs sm:text-sm">Date Publish</th>
                            <th class="px-3 sm:px-4 py-3 text-left font-semibold text-gray-700 text-xs sm:text-sm">Status</th>
                            <th class="px-3 sm:px-4 py-3 text-right font-semibold text-gray-700 text-xs sm:text-sm">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(page, index) in filteredPages()" :key="page.id">
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                <td class="px-3 sm:px-4 py-3 text-sm" x-text="index + 1"></td>
                                <td class="px-3 sm:px-4 py-3">
                                    <div class="font-bold text-gray-800 text-sm" x-text="page.title"></div>
                                    <div class="flex flex-wrap items-center gap-2 mt-0.5">
                                        <small class="text-gray-500 text-xs" x-text="'/' + page.slug"></small>
                                        <a :href="'/' + page.slug"
                                            class="text-blue-600 hover:text-blue-800 transition text-xs"
                                            title="View" target="_blank">
                                            <i class="fa-solid fa-up-right-from-square"></i>
                                        </a>
                                        <template x-if="page.is_builder">
                                            <a href="/{{ config('suryacms.admin_prefix') }}/page-builder/"
                                                class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 rounded px-1.5 py-0.5 text-[10px] font-bold uppercase hover:bg-emerald-200 transition">
                                                <i class="fa-solid fa-cubes"></i> Builder
                                            </a>
                                        </template>
                                    </div>
                                </td>
                                @if ($setting->is_multilingual == 'Yes')
                                    <td class="px-3 sm:px-4 py-3 text-center">
                                        <template x-if="page.language_id == 1">
                                            <img src="/assets/images/id.png" width="20" class="inline rounded" alt="ID">
                                        </template>
                                        <template x-if="page.language_id == 2">
                                            <img src="/assets/images/us.png" width="20" class="inline rounded" alt="US">
                                        </template>
                                    </td>
                                @endif
                                <td class="px-3 sm:px-4 py-3 text-center">
                                    <template x-if="page.pdf">
                                        <a :href="page.pdf" target="_blank" class="text-red-600 text-lg">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </template>
                                    <template x-if="!page.pdf">
                                        <i class="fas fa-minus text-gray-300"></i>
                                    </template>
                                </td>
                                <td class="px-3 sm:px-4 py-3 text-xs sm:text-sm text-gray-600" x-text="format_tanggal(page.datepublish)"></td>
                                <td class="px-3 sm:px-4 py-3">
                                    <span
                                        :class="page.status === 'Publish' ?
                                            'px-2 sm:px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium' :
                                            'px-2 sm:px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium'"
                                        x-text="page.status"></span>
                                </td>
                                <td class="px-3 sm:px-4 py-3 text-right">
                                    <div class="inline-flex gap-1.5 sm:gap-2">
                                        <a :href="'/{{ config('suryacms.admin_prefix') }}/pages/edit/' + page.id"
                                            class="p-1.5 sm:p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-xs sm:text-sm"
                                            title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <button @click="if(confirm('Are you sure?')) $wire.deletePage(page.id)"
                                            class="p-1.5 sm:p-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-xs sm:text-sm"
                                            title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredPages().length === 0">
                            <tr>
                                <td colspan="{{ $setting->is_multilingual == 'Yes' ? '7' : '6' }}"
                                    class="px-4 py-8 text-center text-gray-500 text-sm">
                                    <i class="fas fa-inbox text-3xl text-gray-300 mb-2 block"></i>
                                    No pages found.
                                    <template x-if="activeFilterCount() > 0">
                                        <div class="mt-2">
                                            <button @click="resetFilters()"
                                                class="text-blue-600 hover:text-blue-700 text-xs font-medium underline">
                                                Clear filters
                                            </button>
                                        </div>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
