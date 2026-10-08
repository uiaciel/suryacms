<div class="w-full">
    <x-suryacms::import-export-offcanvas />
    <x-suryacms::session-status />

    {{-- Header --}}
    <header class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>

            <div class="flex items-center gap-3">

                <div>
                    <h1 class="text-2xl font-bold text-gray-900 leading-tight mb-2">Page Builder</h1>
                    <nav aria-label="breadcrumb" class="mb-2">
                <ol class="flex items-center gap-2 text-xs text-gray-500">
                    <li><a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition">Admin</a></li>
                    <li><i class="fas fa-chevron-right text-[10px] text-gray-300"></i></li>
                    <li><a href="/{{ config('suryacms.admin_prefix') }}/pages" class="hover:text-blue-600 transition">Pages</a></li>
                    <li><i class="fas fa-chevron-right text-[10px] text-gray-300"></i></li>
                    <li class="text-gray-700 font-medium">{{ $titlePage }}</li>
                </ol>
            </nav>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">

            <a href="{{ route('admin.page.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl text-sm font-semibold hover:shadow-lg hover:shadow-blue-500/30 transition">
                <i class="fas fa-plus"></i>
                <span>Add Page</span>
            </a>
        </div>
    </header>

    {{-- Stats Cards --}}
    @php
        $total = $pagesbuilder->count();
        $published = $pagesbuilder->where('status', true)->count();
        $draft = $total - $published;
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="relative overflow-hidden bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Pages</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $total }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-layer-group text-blue-600 text-lg"></i>
                </div>
            </div>
            <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-blue-500/5 rounded-full"></div>
        </div>

        <div class="relative overflow-hidden bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Published</p>
                    <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $published }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                </div>
            </div>
            <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-emerald-500/5 rounded-full"></div>
        </div>

        <div class="relative overflow-hidden bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Draft</p>
                    <p class="text-3xl font-bold text-amber-600 mt-1">{{ $draft }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                    <i class="fas fa-edit text-amber-600 text-lg"></i>
                </div>
            </div>
            <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-amber-500/5 rounded-full"></div>
        </div>
    </div>

    {{-- Toolbar: Search & Filter --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm">
        <div class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Search by title or slug..."
                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
            </div>
            <select wire:model.live="statusFilter"
                class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                <option value="all">All Status</option>
                <option value="published">Published</option>
                <option value="draft">Draft</option>
            </select>
        </div>
    </div>

    {{-- Pages Table --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-list text-blue-600"></i>
                All Pages
                <span class="text-xs font-normal text-gray-500">({{ $total }})</span>
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/70">
                    <tr>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wider">Page</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wider">Slug</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($pagesbuilder as $page)
                        <tr class="hover:bg-blue-50/30 transition group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-file-code text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 group-hover:text-blue-600 transition">{{ $page->title }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">ID: #{{ $page->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <code class="text-xs px-2.5 py-1 bg-gray-100 text-gray-700 rounded-md font-mono">
                                    {{ $page->slug ?? '-' }}
                                </code>
                            </td>
                            <td class="px-6 py-4">
                                @if ($page->status)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openInputModal({{ $page->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-gradient-to-br from-amber-50 to-orange-50 text-amber-700 border border-amber-100 rounded-lg hover:from-amber-100 hover:to-orange-100 hover:border-amber-200 transition text-xs font-semibold"
                                        title="Quick edit raw HTML & CSS">
                                        <i class="fas fa-code"></i>
                                        <span class="hidden sm:inline">HTML/CSS</span>
                                    </button>
                                    <a href="{{ route('admin.homepage.builder', $page->slug) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-gradient-to-br from-blue-50 to-indigo-50 text-blue-700 border border-blue-100 rounded-lg hover:from-blue-100 hover:to-indigo-100 hover:border-blue-200 transition text-xs font-semibold"
                                        title="Open visual builder">
                                        <i class="fas fa-paint-brush"></i>
                                        <span class="hidden sm:inline">Builder</span>
                                    </a>
                                    <a href="{{ url($page->slug) }}" target="_blank"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-gray-50 text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition text-xs"
                                        title="Preview">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <button wire:click="delete({{ $page->id }})"
                                        wire:confirm="Are you sure you want to delete this page?"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-red-50 text-red-600 border border-red-100 rounded-lg hover:bg-red-100 hover:border-red-200 transition text-xs"
                                        title="Delete page">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center mb-4">
                                        <i class="fas fa-folder-open text-3xl text-blue-400"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">No pages yet</h3>
                                    <p class="text-sm text-gray-500 mb-4">Get started by creating your first builder page.</p>
                                    <a href="{{ route('admin.page.create') }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl text-sm font-semibold hover:shadow-lg hover:shadow-blue-500/30 transition">
                                        <i class="fas fa-plus"></i>
                                        Create First Page
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal: Quick Code Editor with Tabs --}}
@if ($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-6xl border border-gray-200">
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-amber-50 to-orange-50 rounded-t-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-md shadow-amber-500/30">
                            <i class="fas fa-code text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Quick Code Editor</h3>
                            <p class="text-xs text-gray-600">Paste raw HTML & CSS or get AI prompts</p>
                        </div>
                    </div>
                    <button wire:click="closeModal()"
                        class="w-9 h-9 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-gray-900 hover:border-gray-300 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Tab Navigation --}}
                <div class="border-b border-gray-200 bg-gray-50/50">
                    <nav class="flex gap-1 px-6">
                        <button wire:click="$set('activeTab', 'tips')"
                            class="relative px-4 py-3 text-sm font-semibold transition {{ $activeTab === 'tips' ? 'text-amber-600' : 'text-gray-600 hover:text-gray-900' }}">
                            <i class="fas fa-lightbulb mr-2"></i>
                            Tips & Prompts
                            @if ($activeTab === 'tips')
                                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-amber-500 to-orange-500 rounded-t-full"></span>
                            @endif
                        </button>
                        <button wire:click="$set('activeTab', 'html')"
                            class="relative px-4 py-3 text-sm font-semibold transition {{ $activeTab === 'html' ? 'text-orange-600' : 'text-gray-600 hover:text-gray-900' }}">
                            <i class="fab fa-html5 mr-2"></i>
                            HTML Input
                            @if ($activeTab === 'html')
                                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-orange-500 to-red-500 rounded-t-full"></span>
                            @endif
                        </button>
                        <button wire:click="$set('activeTab', 'css')"
                            class="relative px-4 py-3 text-sm font-semibold transition {{ $activeTab === 'css' ? 'text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                            <i class="fab fa-css3-alt mr-2"></i>
                            CSS Input
                            @if ($activeTab === 'css')
                                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-t-full"></span>
                            @endif
                        </button>
                    </nav>
                </div>

                {{-- Tab Content --}}
                <div class="p-6">
                    {{-- Tab 1: Tips & Prompts --}}
                    @if ($activeTab === 'tips')
                        <div class="grid grid-cols-12 gap-6">
                            {{-- Left Sidebar: Template List --}}
                            <div class="col-span-12 lg:col-span-4">
                                <div class="bg-gradient-to-br from-gray-50 to-slate-50 border border-gray-200 rounded-xl p-4">
                                    <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                                        <i class="fas fa-list-ul text-amber-600"></i>
                                        Template Prompts
                                    </h4>
                                    <div class="space-y-2 max-h-[500px] overflow-y-auto pr-2">
                                        @foreach ($this->getTemplatePrompts() as $index => $template)
                                            <button wire:click="$set('selectedTemplate', {{ $index }})"
                                                class="w-full text-left p-3 rounded-lg border transition group {{ $selectedTemplate === $index ? 'bg-gradient-to-r from-amber-50 to-orange-50 border-amber-300 shadow-sm' : 'bg-white border-gray-200 hover:border-amber-200 hover:bg-amber-50/50' }}">
                                                <div class="flex items-start gap-2">
                                                    <div class="w-8 h-8 rounded-lg {{ $selectedTemplate === $index ? 'bg-gradient-to-br from-amber-500 to-orange-600' : 'bg-gray-100 group-hover:bg-amber-100' }} flex items-center justify-center flex-shrink-0 transition">
                                                        <i class="{{ $template['icon'] }} {{ $selectedTemplate === $index ? 'text-white' : 'text-gray-600 group-hover:text-amber-600' }} text-xs"></i>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-semibold {{ $selectedTemplate === $index ? 'text-amber-900' : 'text-gray-900' }} truncate">
                                                            {{ $template['title'] }}
                                                        </p>
                                                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">
                                                            {{ $template['description'] }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Right Content: Template Detail --}}
                            <div class="col-span-12 lg:col-span-8">
                                @php
                                    $templates = $this->getTemplatePrompts();
                                    $selected = $templates[$selectedTemplate] ?? $templates[0];
                                @endphp

                                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                                    {{-- Template Header --}}
                                    <div class="p-5 bg-gradient-to-r from-amber-50 to-orange-50 border-b border-gray-200">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex items-start gap-3">
                                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-md shadow-amber-500/30">
                                                    <i class="{{ $selected['icon'] }} text-white text-lg"></i>
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-bold text-gray-900">{{ $selected['title'] }}</h3>
                                                    <p class="text-sm text-gray-600 mt-1">{{ $selected['description'] }}</p>
                                                </div>
                                            </div>
                                            <button onclick="copyPromptToClipboard()"
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-lg text-sm font-semibold hover:shadow-lg hover:shadow-amber-500/30 transition flex-shrink-0">
                                                <i class="fas fa-copy"></i>
                                                <span>Copy Prompt</span>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Template Content --}}
                                    <div class="p-5 space-y-4">
                                        {{-- Structure Overview --}}
                                        <div>
                                            <h4 class="text-sm font-bold text-gray-900 mb-2 flex items-center gap-2">
                                                <i class="fas fa-sitemap text-blue-600"></i>
                                                Struktur Halaman
                                            </h4>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($selected['structure'] as $section)
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg text-xs font-medium">
                                                        <i class="fas fa-check-circle text-blue-500"></i>
                                                        {{ $section }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Best Practices --}}
                                        <div>
                                            <h4 class="text-sm font-bold text-gray-900 mb-2 flex items-center gap-2">
                                                <i class="fas fa-star text-amber-600"></i>
                                                Best Practices
                                            </h4>
                                            <ul class="space-y-1.5">
                                                @foreach ($selected['tips'] as $tip)
                                                    <li class="flex items-start gap-2 text-sm text-gray-700">
                                                        <i class="fas fa-chevron-right text-amber-500 text-xs mt-1"></i>
                                                        <span>{{ $tip }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        {{-- Prompt Preview --}}
                                        <div>
                                            <h4 class="text-sm font-bold text-gray-900 mb-2 flex items-center gap-2">
                                                <i class="fas fa-terminal text-green-600"></i>
                                                AI Prompt
                                            </h4>
                                            <div class="relative">
                                                <pre id="promptContent" class="bg-slate-900 text-slate-100 p-4 rounded-xl text-xs font-mono leading-relaxed overflow-x-auto max-h-64 overflow-y-auto">{{ $selected['prompt'] }}</pre>
                                            </div>
                                            <p class="mt-2 text-xs text-gray-500 flex items-center gap-1.5">
                                                <i class="fas fa-info-circle text-blue-500"></i>
                                                Copy prompt ini dan paste ke AI (ChatGPT, Claude, dll) untuk generate HTML & CSS
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Tab 2: HTML Input --}}
                    @if ($activeTab === 'html')
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-800">
                                    <i class="fab fa-html5 text-orange-500"></i>
                                    HTML Content
                                    <span class="text-xs font-normal text-gray-500">(required)</span>
                                </label>
                                <span class="text-xs text-gray-400 font-mono">.html</span>
                            </div>
                            <div class="relative">
                                <textarea wire:model="rawHtml"
                                    class="w-full h-96 px-4 py-3 bg-slate-900 text-slate-100 border border-slate-700 rounded-xl font-mono text-sm leading-relaxed focus:ring-2 focus:ring-amber-500/40 focus:border-amber-500 focus:bg-slate-800 transition resize-none"
                                    placeholder="<!-- Paste your HTML here -->
                                    <div class='container'>
                                    <h1>Hello World</h1>
                                    </div>"
                                    spellcheck="false"></textarea>
                                <div class="absolute top-3 right-3 flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500 flex items-center gap-1.5">
                                <i class="fas fa-magic text-amber-500"></i>
                                Will be auto-converted to GrapeJS-compatible format on save
                            </p>
                        </div>
                    @endif

                    {{-- Tab 3: CSS Input --}}
                    @if ($activeTab === 'css')
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-800">
                                    <i class="fab fa-css3-alt text-blue-500"></i>
                                    CSS Styles
                                    <span class="text-xs font-normal text-gray-500">(optional)</span>
                                </label>
                                <span class="text-xs text-gray-400 font-mono">.css</span>
                            </div>
                            <div class="relative">
                                <textarea wire:model="rawCss"
                                    class="w-full h-96 px-4 py-3 bg-slate-900 text-slate-100 border border-slate-700 rounded-xl font-mono text-sm leading-relaxed focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 focus:bg-slate-800 transition resize-none"
                                    placeholder="/* Paste your CSS here */
                                        .container {
                                        max-width: 1200px;
                                        margin: 0 auto;
                                        }"
                                    spellcheck="false"></textarea>
                            </div>
                            <p class="mt-2 text-xs text-gray-500 flex items-center gap-1.5">
                                <i class="fas fa-info-circle text-blue-500"></i>
                                CSS will be saved and applied to your page
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 bg-gray-50/70 rounded-b-2xl">
                    <p class="text-xs text-gray-500 hidden sm:flex items-center gap-1.5">
                        <i class="fas fa-keyboard"></i>
                        @if ($activeTab === 'tips')
                            Copy prompt dan gunakan di AI favorit Anda
                        @else
                            Changes are saved when you click the button
                        @endif
                    </p>
                    <div class="flex items-center gap-2 ml-auto">
                        <button wire:click="closeModal()"
                            class="px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-100 transition">
                            Cancel
                        </button>
                        @if ($activeTab !== 'tips')
                            <button wire:click="saveHtmlCss()" wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl text-sm font-semibold hover:shadow-lg hover:shadow-amber-500/30 transition disabled:opacity-60 disabled:cursor-not-allowed"
                                {{ $isLoading ? 'disabled' : '' }}>
                                <i class="fas fa-check" wire:loading.remove></i>
                                <i class="fas fa-spinner fa-spin" wire:loading></i>
                                <span wire:loading.remove>Save & Convert</span>
                                <span wire:loading>Processing...</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function copyPromptToClipboard() {
                const promptContent = document.getElementById('promptContent');
                const text = promptContent.textContent;

                navigator.clipboard.writeText(text).then(() => {
                    // Show success notification
                    const button = event.target.closest('button');
                    const originalHTML = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-check"></i><span>Copied!</span>';
                    button.classList.add('bg-green-600');
                    button.classList.remove('from-amber-500', 'to-orange-600');

                    setTimeout(() => {
                        button.innerHTML = originalHTML;
                        button.classList.remove('bg-green-600');
                        button.classList.add('from-amber-500', 'to-orange-600');
                    }, 2000);
                }).catch(err => {
                    console.error('Failed to copy:', err);
                    alert('Failed to copy prompt. Please select and copy manually.');
                });
            }
        </script>
    @endpush

@endif
</div>
