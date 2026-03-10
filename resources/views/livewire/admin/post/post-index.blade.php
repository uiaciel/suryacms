<div class="w-full">
    <x-suryacms::import-export-offcanvas />

    <header class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <h3 class="font-bold text-gray-800 text-2xl mb-0">Posts</h3>
            <a href="{{ route('admin.post.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-full shadow-sm text-sm hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Create New Post
            </a>
        </div>

        <nav aria-label="breadcrumb" class="hidden sm:block">
            <ol class="flex gap-2 text-sm text-gray-600">
                <li><a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-700">Admin</a></li>
                <li>/</li>
                <li><a href="/admin/posts" class="text-blue-600 hover:text-blue-700">Posts</a></li>
                <li>/</li>
                <li class="text-gray-600">{{ $titlePage }}</li>
            </ol>
        </nav>
    </header>

    <x-suryacms::session-status />

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="p-6" x-data="{
                search: '',
                category: '',
                status: '',

                phpFormat: '{{ get_date_format() ?? "m/d/Y" }}',

                format_tanggal(dateString) {
                    if (!dateString) return '';

                    const date = new Date(dateString);
                    if (isNaN(date)) return dateString; // Fallback jika tanggal tidak valid

                    const pad = (num) => String(num).padStart(2, '0');

                    const Y = date.getFullYear(); // Tahun (YYYY)
                    const m = pad(date.getMonth() + 1); // Bulan (01-12)
                    const d = pad(date.getDate()); // Hari (01-31)

                    const M = date.toLocaleDateString('id-ID', { month: 'short' });

                    switch (this.phpFormat) {
                        case 'd/m/Y':
                            // Contoh: 16/10/2025
                            return `${d}/${m}/${Y}`;
                        case 'Y-m-d':
                            // Contoh: 2025-10-16
                            return `${Y}-${m}-${d}`;
                        case 'm/d/Y':
                            // Contoh: 10/16/2025
                            return `${m}/${d}/${Y}`;
                        case 'd-M-Y':
                            // Contoh: 16-Okt-2025
                            return `${d}-${M}-${Y}`;
                        default:
                            // Fallback jika format tidak dikenali
                            return date.toLocaleDateString('id-ID');
                    }
                },

                posts: {{ Js::from($posts) }},
                filteredPosts() {
                    return this.posts.filter(post => {
                        let matchSearch = this.search === '' ||
                            post.title.toLowerCase().includes(this.search.toLowerCase()) ||
                            (post.category && post.category.name.toLowerCase().includes(this.search.toLowerCase())) ||
                            post.status.toLowerCase().includes(this.search.toLowerCase());
                        let matchCategory = this.category === '' || (post.category && post.category.name === this.category);
                        let matchStatus = this.status === '' || post.status === this.status;
                        return matchSearch && matchCategory && matchStatus;
                    });
                },
                resetFilters() {
                    this.search = '';
                    this.category = '';
                    this.status = '';
                }
            }">
            <div class="flex justify-between items-center mb-6">
                <h5 class="font-bold text-lg text-blue-600 mb-0">All Posts</h5>
            </div>

            {{-- Search and Filter Section --}}
            <div class="p-4 rounded-lg border border-blue-200 bg-blue-50 mb-6">
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 items-end">
                    <div class="lg:col-span-2">
                        <label for="search-input" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <div class="flex items-center border border-gray-300 rounded-lg bg-white">
                            <span class="px-3 text-gray-400"><i class="fas fa-search"></i></span>
                            <input type="text" id="search-input" class="w-full px-3 py-2 border-0 focus:outline-none focus:ring-0" placeholder="Search by title, category, or status..." x-model.debounce.500ms="search" aria-label="Search posts">
                        </div>
                    </div>
                    <div>
                        <label for="filter-category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select id="filter-category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" x-model="category" aria-label="Filter by category">
                            <option value="">All Categories</option>
                            @foreach ($categories as $cat)
                            <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="filter-status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="filter-status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" x-model="status" aria-label="Filter by status">
                            <option value="">All Statuses</option>
                            <option value="Publish">Published</option>
                            <option value="Draft">Drafted</option>
                        </select>
                    </div>
                    <div>
                        <button type="button" class="w-full px-4 py-2 border border-gray-300 text-gray-700 bg-white rounded-lg hover:bg-gray-50 font-medium" @click="resetFilters()">Reset</button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 text-sm">#</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 text-sm">Title</th>
                            @if ($setting->is_multilingual == 'Yes')
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 text-sm hidden md:table-cell">Lang</th>
                            @endif
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 text-sm hidden md:table-cell">Category</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 text-sm hidden md:table-cell">Date Publish</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 text-sm hidden md:table-cell">Status</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 text-sm">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(post, index) in filteredPosts()" :key="post.id">
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm" x-text="index + 1"></td>
                                <td class="px-4 py-3">
                                    <a :href="`/media/${post.slug}`" target="_blank" class="text-blue-600 hover:text-blue-700 font-semibold" :title="post.title">
                                        <span x-text="post.title.length > 50 ? post.title.substring(0, 50) + '...' : post.title"></span>
                                    </a>
                                </td>
                                @if ($setting->is_multilingual === 'Yes')
                                <td class="px-4 py-3 hidden md:table-cell">
                                    <template x-if="post.language_id == 1">
                                        <img src="/assets/images/id.png" width="20" class="rounded" alt="ID">
                                    </template>
                                    <template x-if="post.language_id == 2">
                                        <img src="/assets/images/us.png" width="20" class="rounded" alt="US">
                                    </template>
                                </td>
                                @endif
                                <td class="px-4 py-3 text-sm hidden md:table-cell" x-text="post.category.name"></td>
                                <td class="px-4 py-3 text-sm hidden md:table-cell" x-text="format_tanggal(post.datepublish)"></td>
                                <td class="px-4 py-3 hidden md:table-cell">
                                    <span :class="post.status === 'Publish' ? 'px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium' : 'px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium'">
                                        <span x-text="post.status === 'Publish' ? 'Published' : 'Drafted'"></span>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <a :href="`/admin/posts/edit/${post.id}`" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium inline-flex items-center" title="Edit Post">
                                        <i class="fas fa-pencil mr-1"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredPosts().length === 0">
                            <tr>
                                <td colspan="{{ $setting->is_multilingual == 'Yes' ? '7' : '6' }}" class="px-4 py-8 text-center text-gray-500 text-sm">
                                    No posts found.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            {{-- Pagination removed --}}
        </div>
    </div>
</div>
