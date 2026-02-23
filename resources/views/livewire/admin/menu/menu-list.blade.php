<div>

    @forelse ($menus->groupBy('category') as $category => $menusByCategory)
    <div class="accordion mb-3" id="accordionMenu{{ $category }}">

        <div class="accordion-item">

            <h2 class="accordion-header" id="heading{{ $category }}">
                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $category }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ $category }}">
                    <strong>Grup Menu: {{ $category }}</strong>

                </button>
            </h2>

            <div class="collapse {{ $loop->first ? 'show' : '' }}  mb-3" id="collapse{{ $category }}">

                <div class="accordion-body bg-white">

                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <h4>{{$category}}</h4>
                        <button type="button" class="btn btn-sm btn-outline-primary ms-2" wire:click="showCopyModal('{{ $category }}')">
                            <i class="bi bi-copy"></i>

                        </button>

                    </div>
                    <ul class="list-group">
                        @foreach ($menusByCategory as $menu)
                        <li class="list-group-item list-group-item-action">

                            <div class=" d-flex justify-content-between align-items-center">
                                <div>
                                    {{ $menu->name }} <a href="#" wire:click.prevent="showEditModal({{ $menu->id }})">Edit</a>
                                </div>
                                <div>
                                    <button wire:click="moveUp({{ $menu->id }})" class="btn btn-sm btn-outline-secondary">↑</button>
                                    <button wire:click="moveDown({{ $menu->id }})" class="btn btn-sm btn-outline-secondary">↓</button>
                                    <button onclick="confirm('Are you sure you want to delete this menu and its submenus?') || event.stopImmediatePropagation()" wire:click="deleteMenu({{ $menu->id }})" class="btn btn-sm btn-outline-danger">Delete</button>
                                </div>
                            </div>

                            @if ($menu->children->count())
                            <ul class="list-group mt-2 ms-4">
                                @foreach ($menu->children as $child)
                                <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">

                                    <div>
                                        {{ $child->name }} <a href="#" wire:click.prevent="showEditModal({{ $child->id }})">Edit</a>
                                    </div>
                                    <div>
                                        <button wire:click="moveUp({{ $child->id }}, {{ $menu->id }})" class="btn btn-sm btn-outline-secondary">↑</button>
                                        <button wire:click="moveDown({{ $child->id }}, {{ $menu->id }})" class="btn btn-sm btn-outline-secondary">↓</button>
                                        <button onclick="confirm('Are you sure you want to delete this submenu?') || event.stopImmediatePropagation()" wire:click="deleteMenu({{ $child->id }})" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </li>
                        @endforeach
                    </ul>

                </div>

            </div>

        </div>

    </div>

    @empty
    <h5>No Data</h5>

    @endforelse

    <!-- Copy Menu Group Modal -->
    <div wire:ignore.self class="modal fade" id="copyMenuModal" tabindex="-1" aria-labelledby="copyMenuModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="copyMenuGroup">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="copyMenuModalLabel">Copy Menu Group</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Source Category</label>
                            <input type="text" class="form-control" wire:model="sourceCategory" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">New Category Name</label>
                            <input type="text" class="form-control" wire:model.defer="newCategory" placeholder="Enter new category name">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Copy</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="editMenuModal" tabindex="-1" aria-labelledby="editMenuModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="updateMenu" x-data="menuEditForm()">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editMenuModalLabel">Edit Menu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="type" class="form-label fw-bold">Type</label>
                            <select class="form-select" id="type" wire:model="editType" x-model="selectedType" @change="toggleLinkInput" required>
                                <option value="" selected>Select</option>
                                <option value="post">Post</option>
                                <option value="page">Page</option>
                                <option value="categoryPost">Category Post</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>

                        <div class="mb-3" id="post-wrapper" x-show="selectedType === 'post'">
                            <label for="post" class="form-label fw-bold">Select Post</label>
                            <select class="form-select" id="post" wire:model="editLink" x-model="selectedPost" @change="updateLink">

                                <option value="">Select Post</option>
                                <template x-for="post in posts" :key="post.id">
                                    <option :value="post.link" x-text="post.title"></option>
                                </template>
                            </select>
                        </div>

                        <div class="mb-3" id="page-wrapper" x-show="selectedType === 'page'">
                            <label for="page" class="form-label fw-bold">Select Page</label>
                            <select class="form-select" id="page" wire:model="editLink" x-model="selectedPage" @change="updateLink">

                                <option value="">Select Page</option>
                                <template x-for="page in pages" :key="page.id">
                                    <option :value="page.link" x-text="page.title"></option>
                                </template>
                            </select>
                        </div>

                        <div class="mb-3" id="category-wrapper" x-show="selectedType === 'categoryPost'">
                            <label for="categoryPost" class="form-label fw-bold">Select Category</label>
                            <select class="form-select" id="categoryPost" wire:model="editLink" x-model="selectedCategoryPost" @change="updateLink">

                                <option value="">Select Categories</option>
                                <template x-for="category in categoryPost" :key="category.id">
                                    <option :value="category.link" x-text="category.name"></option>
                                </template>
                            </select>
                        </div>

                         <div class="mb-3">
                             <label class="form-label fw-bold">Name Menu</label>
                             <input type="text" class="form-control" wire:model.defer="editName">
                         </div>

                        <div class="mb-3" id="link-wrapper">
                            <label class="form-label fw-bold">Link</label>
                            <input type="text" class="form-control" wire:model="editLink" x-model="customLink">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Parent Menu</label>
                            <select class="form-select" wire:model.defer="editParentId" x-model="selectedParent">
                                <option value="">-- No Parent --</option>
                                <template x-for="menu in filteredMenus" :key="menu.id">
                                    <option :value="menu.id" x-text="menu.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        window.addEventListener('showEditMenuModal', () => {
            var modal = new bootstrap.Modal(document.getElementById('editMenuModal'));
            modal.show();
        });
        window.addEventListener('hideEditMenuModal', () => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('editMenuModal'));
            if(modal) modal.hide();
        });
        window.addEventListener('showCopyMenuModal', () => {
            var modal = new bootstrap.Modal(document.getElementById('copyMenuModal'));
            modal.show();
        });
        window.addEventListener('hideCopyMenuModal', () => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('copyMenuModal'));
            if(modal) modal.hide();
        });

        function menuEditForm() {
            return {
                pages: [],
                posts: [],
                categoryPost: [],
                selectedType: '',
                selectedPost: '',
                selectedPage: '',
                selectedCategoryPost: '',
                customLink: '',
                selectedParent: null,
                menus: @json($menus),
                filteredMenus: @json($menus),

                init() {
                    this.selectedType = @json($editType ?? '');
                    if (this.selectedType) {
                        this.toggleLinkInput();
                    }
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
                            } else if (type === 'page') {
                                this.pages = data;
                            } else if (type === 'categoryPost') {
                                this.categoryPost = data;
                            }
                        })
                        .catch(() => alert('Failed to load data.'));
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
                    this.filteredMenus = this.menus.filter(menu => menu.id !== this.editMenuId);
                }
            };
        }

        window.addEventListener('initializemenuEditForm', (event) => {
            const type = event.detail.type;
            const link = event.detail.link;

            if (type) {
                const form = document.querySelector('#editMenuModal form').__x.$data;
                form.selectedType = type;
                form.toggleLinkInput();

                if (link) {
                    setTimeout(() => {
                        if (type === 'post') form.selectedPost = link;
                        else if (type === 'page') form.selectedPage = link;
                        else if (type === 'categoryPost') form.selectedCategoryPost = link;
                        else form.customLink = link;
                    }, 500);
                }
            }
        });
    </script>
    @endpush

</div>
