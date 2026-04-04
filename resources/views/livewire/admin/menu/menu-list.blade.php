<div class="min-h-screen bg-slate-50 p-6">

    @if (session()->has('message'))
        <div
            class="mb-5 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-3.5 text-sm font-medium text-emerald-700 shadow-sm">
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('message') }}
        </div>
    @endif

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-800">Menu Manager</h1>
            <p class="mt-1 text-sm text-slate-500">Manage and organize your navigation menus by category.</p>
        </div>

    </div>

    @php
        $grouped = $menus->groupBy('category');
    @endphp

    @forelse ($grouped as $category => $categoryMenus)
        <div x-data="{ open: false }"
            class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            {{-- Category Header --}}
            <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/60 px-5 py-4">
                <button @click="open = !open" class="flex items-center gap-3 text-left">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-200">

                        <svg class="h-4 w-4 text-slate-600 transition-transform duration-200"
                            :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>

                    </div>
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-widest text-slate-400">Group</span>
                        <h2 class="text-base font-bold text-slate-700">{{ $category }}</h2>
                    </div>
                    <span class="ml-2 rounded-full bg-slate-200 px-2.5 py-0.5 text-xs font-semibold text-slate-500">
                        {{ $categoryMenus->count() }} items
                    </span>
                </button>

                <div>
                    <button wire:click="openModal('{{ $category }}')"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 active:scale-95"
                        title="Duplicate this menu group">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Duplicate
                    </button>
                    <button wire:click="deleteMenuGroup('{{ $category }}')"
                        wire:confirm="Are you sure you want to delete the entire '{{ $category }}' menu group and all its items?"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-100 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-500 shadow-sm transition hover:border-red-200 hover:bg-red-100 active:scale-95"
                        title="Delete this menu group">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>

                    </button>
                </div>
            </div>

            {{-- Menu Items --}}
            <div class="divide-y divide-slate-100" x-show="open" x-collapse.duration.300ms>
                @foreach ($categoryMenus as $menu)
                    {{-- Parent Menu --}}
                    <div class="group px-5 py-4 transition hover:bg-slate-50/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                                    @if ($menu->type === 'post')
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    @elseif ($menu->type === 'page')
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    @elseif ($menu->type === 'category')
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                    @else
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-700">{{ $menu->name }}</p>
                                    <p class="mt-0.5 hidden items-center gap-1.5 text-xs text-slate-400 lg:flex">
                                        <span
                                            class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-500">{{ ucfirst($menu->type) }}</span>
                                        @if ($menu->link)
                                            <span class="font-mono">{{ Str::limit($menu->link, 40) }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-1.5">
                                <button wire:click="moveUp({{ $menu->id }}, null)" wire:loading.attr="disabled"
                                    class="flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:border-slate-300 hover:text-slate-700 active:scale-90"
                                    title="Move Up">
                                    <span wire:loading.remove wire:target="moveUp({{ $menu->id }}, null)">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        </svg>
                                    </span>
                                    <span wire:loading wire:target="moveUp({{ $menu->id }}, null)">
                                        <svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                        </svg>
                                    </span>
                                </button>
                                <button wire:click="moveDown({{ $menu->id }}, null)" wire:loading.attr="disabled"
                                    class="flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:border-slate-300 hover:text-slate-700 active:scale-90"
                                    title="Move Down">
                                    <span wire:loading.remove wire:target="moveDown({{ $menu->id }}, null)">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </span>
                                    <span wire:loading wire:target="moveDown({{ $menu->id }}, null)">
                                        <svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                        </svg>
                                    </span>
                                </button>

                                <button wire:click="showEditModal({{ $menu->id }})"
                                    class="flex h-7 items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-2.5 text-xs font-semibold text-slate-600 shadow-sm transition hover:border-slate-300 hover:text-slate-800 active:scale-95">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                    Edit
                                </button>
                                <button wire:click="deleteMenu({{ $menu->id }})"
                                    wire:confirm="Are you sure you want to delete this menu and all its submenus?"
                                    class="flex h-7 items-center gap-1.5 rounded-lg border border-red-100 bg-red-50 px-2.5 text-xs font-semibold text-red-500 shadow-sm transition hover:border-red-200 hover:bg-red-100 active:scale-95">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete
                                </button>
                            </div>
                        </div>

                        {{-- Children / Submenus --}}
                        @if ($menu->children && $menu->children->count() > 0)
                            <div class="ml-11 mt-3 space-y-2">
                                @foreach ($menu->children as $child)
                                    <div
                                        class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 transition hover:border-slate-200">
                                        <div class="flex items-center gap-2.5">
                                            <div class="h-4 w-0.5 rounded-full bg-slate-300"></div>
                                            <div
                                                class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-white shadow-sm">
                                                @if ($child->type === 'post')
                                                    <svg class="h-3 w-3 text-slate-400" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                @elseif ($child->type === 'page')
                                                    <svg class="h-3 w-3 text-slate-400" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                    </svg>
                                                @elseif ($child->type === 'category')
                                                    <svg class="h-3 w-3 text-slate-400" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                    </svg>
                                                @else
                                                    <svg class="h-3 w-3 text-slate-400" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-600">{{ $child->name }}
                                                </p>
                                                <p class="hidden items-center gap-1.5 text-xs text-slate-400 lg:flex">
                                                    <span class="font-medium">{{ ucfirst($child->type) }}</span>
                                                    @if ($child->link)
                                                        · <span
                                                            class="font-mono">{{ Str::limit($child->link, 35) }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <button wire:click="moveUp({{ $child->id }}, {{ $menu->id }})" wire:loading.attr="disabled" wire:target="moveUp({{ $child->id }}, {{ $menu->id }})"
                                                class="flex h-6 w-6 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-400 shadow-sm transition hover:text-slate-600 active:scale-90">
                                                <span wire:loading.remove wire:target="moveUp({{ $child->id }}, {{ $menu->id }})">
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 15l7-7 7 7" />
                                                    </svg>
                                                </span>
                                                <span wire:loading wire:target="moveUp({{ $child->id }}, {{ $menu->id }})">
                                                    <svg class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                                    </svg>
                                                </span>
                                            </button>
                                            <button wire:click="moveDown({{ $child->id }}, {{ $menu->id }})" wire:loading.attr="disabled" wire:target="moveDown({{ $child->id }}, {{ $menu->id }})"
                                                class="flex h-6 w-6 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-400 shadow-sm transition hover:text-slate-600 active:scale-90">
                                                <span wire:loading.remove wire:target="moveDown({{ $child->id }}, {{ $menu->id }})">
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </span>
                                                <span wire:loading wire:target="moveDown({{ $child->id }}, {{ $menu->id }})">
                                                    <svg class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                                    </svg>
                                                </span>
                                            </button>
                                            <button wire:click="showEditModal({{ $child->id }})"
                                                class="flex h-6 items-center gap-1 rounded-md border border-slate-200 bg-white px-2 text-xs font-semibold text-slate-500 shadow-sm transition hover:text-slate-700 active:scale-95">
                                                Edit
                                            </button>
                                            <button wire:click="deleteMenu({{ $child->id }})"
                                                wire:confirm="Delete this submenu?"
                                                class="flex h-6 items-center rounded-md border border-red-100 bg-red-50 px-2 text-xs font-semibold text-red-400 shadow-sm transition hover:bg-red-100 active:scale-95">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div
            class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-white py-16 text-center">
            <svg class="mb-4 h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h7" />
            </svg>
            <p class="text-sm font-semibold text-slate-400">No menus found</p>
            <p class="mt-1 text-xs text-slate-400">Start by adding a menu item.</p>
        </div>
    @endforelse

    @if ($showModalEdit)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

            <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-slate-200">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Edit Menu Item</h3>
                        <p class="mt-0.5 text-sm text-slate-400">Update the details for this menu entry.</p>
                    </div>
                    <button wire:click="closeModal"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    {{-- Menu Name --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Menu Name</label>
                        <input wire:model="editName" type="text" placeholder="e.g. Home, About Us..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition focus:border-slate-400 focus:bg-white focus:ring-2 focus:ring-slate-200" />
                    </div>

                    {{-- Menu Type Selector --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Menu Type</label>
                        <select wire:model.live="editType"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition focus:border-slate-400 focus:bg-white focus:ring-2 focus:ring-slate-200">
                            <option value="">-- Select Type --</option>
                            <option value="post">Post</option>
                            <option value="page">Page</option>
                            <option value="categoryPost">Category Post</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>

                    {{-- Custom URL --}}
                    @if ($editType === 'custom')
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">URL</label>
                            <input wire:model="editLink" type="url" placeholder="https://example.com"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition focus:border-slate-400 focus:bg-white focus:ring-2 focus:ring-slate-200" />
                        </div>
                    @endif

                    {{-- Select Post --}}
                    @if ($editType === 'post')
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Select Post</label>
                            <select wire:model="editLink"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition focus:border-slate-400 focus:bg-white focus:ring-2 focus:ring-slate-200">
                                <option value="">-- Select a Post --</option>
                                @foreach ($posts as $post)
                                    <option value="/media/{{ $post->slug }}" @selected($editLink === '/media/' . $post->slug)>
                                        {{ $post->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Select Page --}}
                    @if ($editType === 'page')
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Select Page
                                {{ $editLink }}</label>
                            <select wire:model="editLink"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition focus:border-slate-400 focus:bg-white focus:ring-2 focus:ring-slate-200">
                                <option value="">-- Select a Page --</option>

                                @foreach ($pages as $page)
                                    <option value="/{{ $page->slug }}" @selected($editLink === '/' . $page->slug)>
                                        {{ $page->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Select Category --}}
                    @if ($editType === 'categoryPost')
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Select Category</label>
                            <select wire:model="editLink"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition focus:border-slate-400 focus:bg-white focus:ring-2 focus:ring-slate-200">
                                <option value="">-- Select a Category --</option>
                                @foreach ($categories as $cat)
                                    <option value="/category/{{ $cat->slug }}" @selected($editLink === '/category/' . $cat->slug)>
                                        {{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Parent Menu --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">
                            Parent Menu <span class="font-normal text-slate-400">(optional)</span>
                        </label>
                        <select wire:model.number="editParentId"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition focus:border-slate-400 focus:bg-white focus:ring-2 focus:ring-slate-200">
                            <option value="">-- No Parent (top level) --</option>
                            @foreach ($menus as $parentMenu)
                                @if ($parentMenu->id !== $editMenuId)
                                    <option value="{{ $parentMenu->id }}" @selected($editParentId == $parentMenu->id)>
                                        {{ $parentMenu->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2.5">
                    <button wire:click="closeModal"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 active:scale-95">
                        Cancel
                    </button>
                    <button wire:click="updateMenu" wire:loading.attr="disabled" wire:target="updateMenu"
                        class="inline-flex items-center gap-2 rounded-xl bg-slate-800 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700 active:scale-95 disabled:opacity-60">
                        <span wire:loading.remove wire:target="updateMenu">Save Changes</span>
                        <span wire:loading wire:target="updateMenu" class="flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showCopyModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

            <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-slate-200">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <div class="mb-2 flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100">
                            <svg class="h-5 w-5 text-slate-600" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Duplicate Menu Group</h3>
                        <p class="mt-0.5 text-sm text-slate-400">
                            Duplicate all menus from <strong
                                class="text-slate-600">{{ $sourceCategory ?? '…' }}</strong> with a new category name.
                        </p>
                    </div>
                    <button wire:click="closeModal"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Source Category</label>
                        <div
                            class="flex h-10 items-center rounded-xl border border-slate-200 bg-slate-50 px-3.5 text-sm font-semibold text-slate-600">
                            {{ $sourceCategory ?? '—' }}
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">New Category Name</label>
                        <input wire:model="newCategory" type="text" placeholder="e.g. French, Mobile, Footer..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition focus:border-slate-400 focus:bg-white focus:ring-2 focus:ring-slate-200" />
                        @error('newCategory')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2.5">
                    <button wire:click="closeModal"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 active:scale-95">
                        Cancel
                    </button>
                    <button wire:click="copyMenuGroup" wire:loading.attr="disabled" wire:target="copyMenuGroup"
                        class="inline-flex items-center gap-2 rounded-xl bg-slate-800 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700 active:scale-95 disabled:opacity-60">
                        <span wire:loading.remove wire:target="copyMenuGroup">Duplicate Group</span>
                        <span wire:loading wire:target="copyMenuGroup" class="flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            Duplicating...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
