<div class="container-fluid">
    <x-suryacms::import-export-offcanvas />

    <header class="page-header d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-2">{{ $titlePage }}
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="/admin/menu">Menu</a></li>
                <li class="breadcrumb-item active">{{ $titlePage }}</li>
            </ol>
        </nav>
    </header>

    <div class="row">
        <div class="col-md-4">
            <div class="card" x-data="menuForm()">

                <div class="card-body">
                    <x-suryacms::session-status />

                    <form wire:submit.prevent="storeMenu">
                        @csrf
                        <div class="mb-3">
                            <label for="category" class="form-label fw-bold">Grup Menu</label>
                            <div id="category-container">
                                <template x-if="!isAddingNewCategory">
                                    <select class="form-select" id="category-select" wire:model.defer="category"
                                        x-model="selectedCategory" @change="handleCategoryChange">
                                        <option value="">Select a menu</option>
                                        @foreach ($categoriesmenu as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                        @endforeach
                                        @if ($setting->is_multilingual == 'Yes')

                                            @foreach ($language as $language )
                                                <option value="{{ $language->name }}">{{ $language->name }}</option>

                                            @endforeach
                                        @endif
                                        <option value="new">Add New Menu</option>
                                    </select>
                                </template>
                                <template x-if="isAddingNewCategory">
                                    <div>
                                        <input type="text" class="form-control mt-2" id="category-input"
                                            wire:model.defer="category" x-model="newCategory"
                                            placeholder="Enter new category">
                                        <button type="button" class="btn btn-secondary mt-2"
                                            @click="cancelNewCategory">Cancel</button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label fw-bold">Type</label>
                            <select class="form-select" id="type" wire:model.defer="type" x-model="selectedType"
                                @change="toggleLinkInput" required>
                                <option value="" selected>Select</option>
                                <option value="post">Post</option>
                                <option value="page">Page</option>
                                <option value="categoryPost">Category Post</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>

                        <div class="mb-3" id="post-wrapper" x-show="selectedType === 'post'">
                            <label for="post" class="form-label fw-bold">Select Post</label>
                            <select class="form-select" id="post" wire:model="link" x-model="selectedPost"
                                @change="updateLink">
                                <option value="">Select Post</option>
                                <template x-for="post in posts" :key="post.id">
                                    <option :value="post.link" x-text="post.title"></option>
                                </template>
                            </select>
                        </div>

                        <div class="mb-3" id="page-wrapper" x-show="selectedType === 'page'">
                            <label for="page" class="form-label fw-bold">Select Page</label>
                            <select class="form-select" id="page" wire:model="link" x-model="selectedPage"
                                @change="updateLink">
                                <option value="">Select Page</option>
                                <template x-for="page in pages" :key="page.id">
                                    <option :value="page.link" x-text="page.title"></option>
                                </template>
                            </select>
                        </div>

                        <div class="mb-3" id="category-wrapper" x-show="selectedType === 'categoryPost'">
                            <label for="categoryPost" class="form-label fw-bold">Select Category</label>
                            <select class="form-select" id="categoryPost" wire:model="link"
                                x-model="selectedCategoryPost" @change="updateLink">
                                <option value="">Select Categories</option>
                                <template x-for="category in categoryPost" :key="category.id">
                                    <option :value="category.link" x-text="category.name"></option>
                                </template>
                            </select>
                        </div>

                        <div class="mb-3" id="link-wrapper">
                            <label for="link" class="form-label fw-bold">Link</label>
                            <input type="text" class="form-control" id="link" wire:model="link" x-model="customLink" @input="$wire.link = customLink">

                        </div>

                         <div class="mb-3">
                             <label for="name" class="form-label fw-bold">Name</label>
                             <input type="text" class="form-control" id="name" wire:model.defer="name" x-model="getName" required>
                         </div>

                        <div class="mb-4">
                            <label for="parent_id" class="form-label fw-bold">Parent Menu</label>
                            <select class="form-select" id="parent_id" wire:model.defer="parent_id"
                                x-model="selectedParent">
                                <option value="">No Parent</option>
                                <template x-for="menu in filteredMenus" :key="menu.id">
                                    <option :value="menu.id" x-text="menu.name"></option>
                                </template>
                            </select>
                        </div>
                        <div class="mb-3">
                            <button type="submit" @click.prevent="syncToLivewireThenSubmit" class="btn btn-primary">Add to Menu</button>

                        </div>

                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">

            @if (session()->has('message'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <livewire:suryacms::admin.menu.menu-list />
                </div>
            </div>

        </div>
    </div>

    <script>
        function menuForm() {
            return {
                pages: [],
                posts: [],
                categoryPost: [],
                selectedCategory: '',
                newCategory: '',
                isAddingNewCategory: false,
                selectedType: '',
                selectedPost: '',
                selectedPage: '',
                selectedCategoryPost: '',
                customLink: '',
                getName: '',
                selectedParent: null,
                menus: @json($menux),
                filteredMenus: @json($menux),

                handleCategoryChange() {
                    if (this.selectedCategory === 'new') {
                        this.isAddingNewCategory = true;
                        this.newCategory = '';
                    } else {
                        this.isAddingNewCategory = false;
                        this.filterParentOptions();
                    }
                },

                cancelNewCategory() {
                    this.isAddingNewCategory = false;
                    this.selectedCategory = '';
                    this.filterParentOptions();
                },

                toggleLinkInput() {
                    if (this.selectedType === 'custom') {
                        this.customLink = '';
                    } else if (this.selectedType === 'post') {
                        this.fetchOptions('/admin/getmenus/posts', 'post');
                    } else if (this.selectedType === 'page') {
                        this.fetchOptions('/admin/getmenus/pages', 'page');
                    } else if (this.selectedType === 'categoryPost') {
                        this.fetchOptions('/admin/getmenus/categories', 'categoryPost');
                    }
                },

                fetchOptions(url, type) {
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            if (type === 'post') {
                                this.posts = data;
                                this.$watch('selectedPost', (value) => {
                                    const selected = this.posts.find(post => post.link === value);
                                    this.getName = selected ? selected.title : '';
                                    this.$wire.link = value;
                                });
                            } else if (type === 'page') {
                                this.pages = data;
                                this.$watch('selectedPage', (value) => {
                                    const selected = this.pages.find(page => page.link === value);
                                    this.getName = selected ? selected.title : '';
                                    this.$wire.link = value;
                                });

                            } else if (type === 'categoryPost') {
                                this.categoryPost = data;
                                this.$watch('selectedCategoryPost', (value) => {
                                    const selected = this.categoryPost.find(category => category.link === value);
                                    this.getName = selected ? selected.name : '';
                                    this.$wire.link = value;
                                });

                            }
                        })
                        .catch(() => alert('Failed to load data.'));
                },

                updateLink(event) {
                    this.customLink = event.target.value;
                },

                filterParentOptions() {
                    this.filteredMenus = this.menus.filter(menu => {
                        return this.selectedCategory === '' || menu.category === this.selectedCategory;
                    });
                },

                handleSubmit() {
                    if (this.isAddingNewCategory) {
                        this.selectedCategory = this.newCategory;
                    }
                    this.$el.submit();
                },

                syncToLivewireThenSubmit() {
                // Sinkronisasi manual
                this.$wire.name = this.getName;
                this.$wire.type = this.selectedType;
                this.$wire.link = this.selectedType === 'custom' ? this.customLink
                : this.selectedType === 'post' ? this.selectedPost
                : this.selectedType === 'page' ? this.selectedPage
                : this.selectedType === 'categoryPost' ? this.selectedCategoryPost
                : null;
                this.$wire.category = this.isAddingNewCategory ? this.newCategory : this.selectedCategory;
                this.$wire.parent_id = this.selectedParent || null;

                // Panggil method Livewire
                this.$wire.storeMenu();
                }

            };
        }
    </script>
</div>
