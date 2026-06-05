<div class="w-full">
    <x-suryacms::import-export-offcanvas />

    <header class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <h3 class="font-bold text-gray-800 text-2xl mb-0">Page Builder</h3>
            <a href="{{ route('admin.page.create') }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-full shadow-sm text-sm hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Export
            </a>
        </div>

        <nav aria-label="breadcrumb" class="hidden sm:block">
            <ol class="flex gap-2 text-sm text-gray-600">
                <li><a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-700">Admin</a></li>
                <li>/</li>
                <li><a href="/admin/pages" class="text-blue-600 hover:text-blue-700">Page Builder</a></li>
                <li>/</li>
                <li class="text-gray-600">{{ $titlePage }}</li>
            </ol>
        </nav>
    </header>

    <x-suryacms::session-status />

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-700">Title</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-700">Slug</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-700 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($pagesbuilder as $page)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-900">{{ $page->title }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $page->slug ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 text-xs font-medium rounded-full {{ $page->status ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $page->status ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button wire:click="openInputModal({{ $page->id }})"
                                    class="inline-flex items-center px-3 py-1.5 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition text-sm"
                                    title="Input raw HTML & CSS">
                                    <i class="fas fa-code mr-1.5"></i>HTML/CSS
                                </button>
                                <a href="{{ route('admin.homepage.builder', $page->slug) }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm">
                                    <i class="fas fa-edit mr-1.5"></i>Builder
                                </a>
                                <button wire:click="delete({{ $page->id }})"
                                    wire:confirm="Are you sure you want to delete this page?"
                                    class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                No pages found. <a href="{{ route('admin.page.create') }}"
                                    class="text-blue-600 hover:underline">Create your first page</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal: Input HTML & CSS -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(0,0,0,0.5);">
            <div class="flex items-center justify-center min-h-screen px-4 py-6 sm:px-6 lg:px-8">
                <div class="relative bg-white rounded-xl shadow-lg w-full max-w-4xl">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-6 border-b border-gray-200">
                        <h3 class="text-xl font-bold text-gray-900">
                            <i class="fas fa-code mr-2 text-amber-600"></i>Input Raw HTML & CSS
                        </h3>
                        <button wire:click="closeModal()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="p-6 space-y-6 max-h-96 overflow-y-auto">
                        <!-- HTML Input -->
                        <div>
                            <label for="rawHtml" class="block text-sm font-semibold text-gray-700 mb-2">
                                HTML Content
                            </label>
                            <textarea id="rawHtml" wire:model="rawHtml"
                                class="w-full h-40 px-4 py-3 border border-gray-300 rounded-lg font-mono text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                placeholder="Paste your raw HTML here..."></textarea>
                            <p class="mt-2 text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>The HTML will be automatically converted to
                                GrapeJS-compatible format
                            </p>
                        </div>

                        <!-- CSS Input -->
                        <div>
                            <label for="rawCss" class="block text-sm font-semibold text-gray-700 mb-2">
                                CSS Styles (Optional)
                            </label>
                            <textarea id="rawCss" wire:model="rawCss"
                                class="w-full h-40 px-4 py-3 border border-gray-300 rounded-lg font-mono text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                placeholder="Paste your CSS here..."></textarea>
                            <p class="mt-2 text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>CSS will be saved and applied to your page
                            </p>
                        </div>

                        <!-- Info Box -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-lightbulb mr-2"></i><strong>Workflow:</strong>
                                After saving, the HTML will be converted to GrapeJS format. Click the
                                <strong>Builder</strong> button to edit it further in the visual editor.
                            </p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                        <button wire:click="closeModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition font-medium">
                            Cancel
                        </button>
                        <button wire:click="saveHtmlCss()" wire:loading.attr="disabled"
                            class="px-6 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition font-medium flex items-center gap-2"
                            {{ $isLoading ? 'disabled' : '' }}>
                            <i class="fas fa-check" wire:loading.remove></i>
                            <i class="fas fa-spinner fa-spin" wire:loading></i>
                            <span wire:loading.remove>Save & Convert</span>
                            <span wire:loading>Processing...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
