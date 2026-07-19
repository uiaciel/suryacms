<div>
    <x-import-export-offcanvas />

    <header class="page-header d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-2">{{ $titlePage }}</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="/admin/menu">Menu</a></li>
                <li class="breadcrumb-item active">{{ $titlePage }}</li>
            </ol>
        </nav>
    </header>

    <x-session-status />

    <div class="card border-0 shadow rounded">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Create Menu</h4>
                <div class="btn-group" role="group" aria-label="Post Actions">
                    <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasImport" aria-controls="offcanvasImport">
                        <i class="fas fa-file-import me-2"></i> Import
                    </button>
                    <button type="button" class="btn btn-secondary" wire:click="dataExport">
                        <i class="fas fa-file-export me-2"></i> Export
                    </button>
                </div>
            </div>

            <form wire:submit.prevent="storeMenu" x-data="menuForm()">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Type Menu</label>
                            <select class="form-select" wire:model.live="type" x-model="selectedType">
                                <option value="">Select</option>
                                <option value="post">Post</option>
                                <option value="page">Page</option>
                                <option value="categoryPost">Category Post</option>
                                <option value="custom">Custom / Dropdown</option>
                            </select>
                        </div>

                        <div class="mb-3" id="post-wrapper" x-show="selectedType === 'post'">
                            <label class="form-label fw-bold">Select Post</label>
                            <select class="form-select" x-model="selectedLink" @change="$wire.dispatch('updateLink', $event.target.value)">
                                <option value="">Select Post</option>
                                <template x-for="post in posts" :key="post.id">
                                    <option :value="post.link" x-text="post.title"></option>
                                </template>
                            </select>
                        </div>

                        <div class="mb-3" id="page-wrapper" x-show="selectedType === 'page'">
                            <label class="form-label fw-bold">Select Page</label>
                            <select class="form-select" x-model="selectedLink" @change="$wire.dispatch('updateLink', $event.target.value)">
                                <option value="">Select Page</option>
                                <template x-for="page in pages" :key="page.id">
                                    <option :value="page.link" x-text="page.title"></option>
                                </template>
                            </select>
                        </div>

                        <div class="mb-3" id="category-wrapper" x-show="selectedType === 'categoryPost'">
                            <label class="form-label fw-bold">Select Category</label>
                            <select class="form-select" x-model="selectedLink" @change="$wire.dispatch('updateLink', $event.target.value)">
                                <option value="">Select Categories</option>
                                <template x-for="category in categoryPost" :key="category.id">
                                    <option :value="category.link" x-text="category.name"></option>
                                </template>
                            </select>
                        </div>

                        <div class="mb-3" id="link-wrapper" x-show="selectedType === 'custom'">
                            <label class="form-label fw-bold">Link</label>
                            <input type="text" class="form-control" wire:model.live="link" x-model="selectedLink">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Name Menu</label>
                            <input type="text" class="form-control" wire:model.defer="name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Category</label>
                            <select class="form-select" wire:model.defer="category">
                                <option value="">Select Category</option>
                                @foreach ($categoriesmenu as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Parent Menu</label>
                            <select class="form-select" wire:model.defer="parent_id">
                                <option value="">-- No Parent --</option>
                                @foreach ($menux as $menu)
                                    <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Save Menu</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function menuForm() {
                return {
                    pages: [],
                    posts: [],
                    categoryPost: [],
                    selectedType: @entangle('type').live,
                    selectedLink: @entangle('link').live,

                    init() {
                        this.$watch('selectedType', (value) => {
                            this.selectedLink = ''; // Reset link when type changes
                            if (value === 'post') {
                                this.fetchOptions('/admin/getmenus/posts', 'post');
                            } else if (value === 'page') {
                                this.fetchOptions('/admin/getmenus/pages', 'page');
                            } else if (value === 'categoryPost') {
                                this.fetchOptions('/admin/getmenus/categories', 'categoryPost');
                            }
                        });
                    },

                    fetchOptions(url, type) {
                        fetch(url)
                            .then(response => response.json())
                            .then(data => {
                                if (type === 'post') {
                                    this.posts = data;
                                } else if (type === 'page') {
                                    this.pages = data;
                                } else if (type === 'categoryPost') {
                                    this.categoryPost = data;
                                }
                            })
                            .catch(() => alert('Failed to load data.'));
                    }
                };
            }
        </script>
    @endpush
</div>
