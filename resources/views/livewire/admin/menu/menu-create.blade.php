<div class="p-4 md:p-6 lg:p-8 bg-gray-50 min-h-screen">
    <x-suryacms::import-export-offcanvas />

    <!-- Header -->
    <header class="mb-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-900 flex items-center ">
                <i class="bi bi-list-nested text-blue-600"></i> {{ $titlePage }}
            </h3>
            <nav aria-label="breadcrumb">
                    <ol class="flex gap-2 text-sm text-gray-500 mt-1">
                        <li><a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Admin</a></li>
                        <li class="before:content-['/'] before:mr-2 text-gray-400"></li>
                        <li><a href="/admin/menu" class="hover:text-blue-600 transition-colors">Menu</a></li>
                        <li class="before:content-['/'] before:mr-2 text-gray-400"></li>
                        <li class="text-gray-700 font-medium">{{ $titlePage }}</li>
                </ol>
            </nav>
        </div>

    </header>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Section -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 border-b  border-gray-100 bg-slate-800">
                    <h5 class="font-bold text-white flex items-center gap-2">
                        <i class="bi bi-plus-square-dotted text-blue-600"></i> Create New Menu
                    </h5>
                </div>

                <div class="p-6">
                <x-suryacms::session-status />

                <form wire:submit.prevent="storeMenu" class="space-y-4" x-data="menuForm()" x-cloak>
                    @csrf

                    <!-- Menu Group -->
                    <div>
                        <label for="category" class="block text-sm font-bold text-gray-900 mb-2">Grup Menu</label>
                        <div id="category-container">
                            <template x-if="!isAddingNewCategory">
                                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" id="category-select" wire:model.defer="category"
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
                                <div class="flex gap-2">
                                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" id="category-input"
                                       wire:model.defer="category" x-model="newCategory"
                                        placeholder="Enter new category">
                                    <button type="button" class="px-3 py-2 bg-gray-100 text-red-600 rounded-lg hover:bg-red-200" @click="cancelNewCategory">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Type Select -->
                    <div>
                        <label for="type" class="block text-sm font-bold text-gray-900 mb-2">Type</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white" id="type" wire:model.defer="type" x-model="selectedType"
                                @change="toggleLinkInput" required>
                            <option value="" selected>Select</option>
                            <option value="post">Post</option>
                            <option value="page">Page</option>
                            <option value="categoryPost">Category Post</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>

                    <!-- Post Select -->
                    <div class="space-y-2" x-show="selectedType === 'post'" x-transition>
                        <label for="post" class="block text-sm font-bold text-gray-900">Select Post</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white" id="post"
                            wire:model="link" x-model="selectedPost"
                                @change="updateLink">
                            <option value="" selected>Select Post</option>
                            <template x-for="post in posts" :key="post.id">
                                    <option :value="post.link" x-text="post.title"></option>
                                </template>
                        </select>
                    </div>

                    <!-- Page Select -->
                    <div class="space-y-2" x-show="selectedType === 'page'" x-transition>
                        <label for="page" class="block text-sm font-bold text-gray-900">Select Page</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white" id="page"
                            wire:model="link" x-model="selectedPage"
                                @change="updateLink">
                            <option value="" selected>Select Page</option>
                            <template x-for="page in pages" :key="page.id">
                                    <option :value="page.link" x-text="page.title"></option>
                                </template>
                        </select>
                    </div>

                    <!-- Category Post Select -->
                    <div class="space-y-2" x-show="selectedType === 'categoryPost'" x-transition>
                        <label for="categoryPost" class="block text-sm font-bold text-gray-900">Select Category</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white" id="categoryPost"
                            wire:model="link"
                                x-model="selectedCategoryPost" @change="updateLink">
                            <option value="" selected>Select Categories</option>
                            <template x-for="category in categoryPost" :key="category.id">
                                    <option :value="category.link" x-text="category.name"></option>
                                </template>
                        </select>
                    </div>

                    <!-- Link Input (Auto-populated or Manual) -->
                    <div class="space-y-2">
                        <label for="link" class="block text-sm font-bold text-gray-900">Link</label>
                        <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" id="link" wire:model="link" x-model="customLink" @input="$wire.link = customLink">

                    </div>

                    <!-- Menu Name -->
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-bold text-gray-900">Name</label>
                        <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" id="name" wire:model="name" x-model="getName" required>
                    </div>

                    <!-- Parent Menu -->
                    <div class="space-y-2">
                        <label for="parent_id" class="block text-sm font-bold text-gray-900">Parent Menu</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white" id="parent_id" wire:model.defer="parent_id"
                                x-model="selectedParent">
                            <option value="">No Parent</option>
                            <template x-for="menu in filteredMenus" :key="menu.id">
                                <option :value="menu.id" x-text="menu.name"></option>
                            </template>
                        </select>
                    </div>

                    <button type="submit" @click.prevent="syncToLivewireThenSubmit" class="w-full mt-4 px-6 py-3 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl shadow-md shadow-blue-200 transition-all flex items-center justify-center gap-2">
                        <i class="bi bi-plus-lg"></i> Add to Menu
                    </button>
                </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

                    <livewire:suryacms::admin.menu.menu-list />
            </div>
        </div>
    </div>

    @push('scripts')
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
    @endpush
</div>

