<div class="container-fluid">
    <x-suryacms::import-export-offcanvas />

    <header class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <h3 class="fw-bold mb-0 text-dark">Posts</h3>
            <a href="{{ route('admin.post.create') }}" class="btn btn-primary rounded-pill px-3 shadow-sm btn-sm btn-md-base">
                        <i class="bi bi-plus-lg me-2"></i>Create New Post
                    </a>
        </div>

        <nav aria-label="breadcrumb" class="d-none d-sm-block">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Admin</a></li>
                <li class="breadcrumb-item"><a href="/admin/posts" class="text-decoration-none">Posts</a></li>
                <li class="breadcrumb-item active">{{ $titlePage }}</li>
            </ol>
        </nav>
    </header>

    <x-suryacms::session-status />

    <div class="card border-0 shadow rounded">
        <div class="card-body" x-data="{
                search: '',
                category: '',
                status: '',

                // 1. Ambil format mentah PHP dan ekspos ke Alpine
                phpFormat: '{{ get_date_format() ?? "m/d/Y" }}',

                // 2. Fungsi JavaScript untuk memformat tanggal
                format_tanggal(dateString) {
                    if (!dateString) return '';

                    // Membuat objek Date dari string tanggal database (Y-m-d H:i:s)
                    const date = new Date(dateString);
                    if (isNaN(date)) return dateString; // Fallback jika tanggal tidak valid

                    // Helper untuk memastikan digit (01, 02, dst.)
                    const pad = (num) => String(num).padStart(2, '0');

                    // Ekstraksi komponen tanggal
                    const Y = date.getFullYear(); // Tahun (YYYY)
                    const m = pad(date.getMonth() + 1); // Bulan (01-12)
                    const d = pad(date.getDate()); // Hari (01-31)

                    // Nama bulan singkat ('Jan', 'Feb', 'Okt', dst.) menggunakan locale Indonesia
                    const M = date.toLocaleDateString('id-ID', { month: 'short' });

                    // Pengecekan format berdasarkan setting PHP
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title fw-bold mb-0 text-primary">All Posts</h5>
                <div>

                </div>
            </div>

            {{-- Search and Filter Section --}}
            <div class="p-4 rounded border border-primary mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-lg-5">
                        <label for="search-input" class="form-label text-muted mb-1 visually-hidden">Search</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" id="search-input" class="form-control border-start-0 ps-1" placeholder="Search by title, category, or status..." x-model.debounce.500ms="search" aria-label="Search posts">
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <label for="filter-category" class="form-label text-muted mb-1 visually-hidden">Category</label>
                        <select id="filter-category" class="form-select py-2" x-model="category" aria-label="Filter by category">
                            <option value="">All Categories</option>
                            @foreach ($categories as $cat)
                            <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-lg-2">
                        <label for="filter-status" class="form-label text-muted mb-1 visually-hidden">Status</label>
                        <select id="filter-status" class="form-select py-2" x-model="status" aria-label="Filter by status">
                            <option value="">All Statuses</option>
                            <option value="Publish">Published</option>
                            <option value="Draft">Drafted</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2 d-grid">
                        <button type="button" class="btn btn-outline-secondary" @click="resetFilters()">Reset</button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 rounded-4 overflow-hidden">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Title</th>
                            @if ($setting->is_multilingual == 'Yes')
                            <th scope="col" class="d-none d-md-table-cell">Lang</th>

                            @endif
                            <th scope="col" class="d-none d-md-table-cell">Category</th>

                            <th scope="col" class="d-none d-md-table-cell">Date Publish</th>

                            <th scope="col" class="d-none d-md-table-cell">Status</th>

                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(post, index) in filteredPosts()" :key="post.id">
                            <tr>
                                <td x-text="index + 1"></td>
                                <td>
                                    <a :href="`/media/${post.slug}`" target="_blank" class="text-decoration-none text-primary fw-bold" data-bs-toggle="tooltip" data-bs-placement="top" :title="post.title">
                                        <span x-text="post.title.length > 50 ? post.title.substring(0, 50) + '...' : post.title"></span>
                                    </a>
                                </td>
                                @if ($setting->is_multilingual === 'Yes')
                                <td class="d-none d-md-table-cell">

                                    <template x-if="post.language_id == 1">
                                        <img src="/assets/images/id.png" width="20" class="img-fluid" alt="ID">
                                    </template>
                                    <template x-if="post.language_id == 2">
                                        <img src="/assets/images/us.png" width="20" class="img-fluid" alt="US">
                                    </template>
                                </td>
                                @endif
                                <td x-text="post.category.name" class="d-none d-md-table-cell"></td>

                                {{-- PANGGIL FUNGSI JS YANG BARU --}}
                                <td x-text="format_tanggal(post.datepublish)" class="d-none d-md-table-cell"></td>

                                <td class="d-none d-md-table-cell">

                                    <span :class="post.status === 'Publish' ? 'badge bg-primary' : 'badge bg-secondary'">
                                        <span x-text="post.status === 'Publish' ? 'Published' : 'Drafted'"></span>
                                    </span>
                                </td>
                                <td>
                                    <a :href="`/admin/posts/edit/${post.id}`" class="btn btn-sm btn-outline-primary rounded-xl" title="Edit Post">

                                        <i class="bi bi-pencil me-1"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredPosts().length === 0">
                            <tr>
                                <td colspan="{{ $setting->is_multilingual == 'Yes' ? '7' : '6' }}" class="text-center py-4">
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
