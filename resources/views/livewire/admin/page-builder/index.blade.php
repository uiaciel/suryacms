<div class="w-full">
    <x-suryacms::import-export-offcanvas />

    <header class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <h3 class="font-bold text-gray-800 text-2xl mb-0">Page Builder</h3>
            <a href="{{ route('admin.page.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-full shadow-sm text-sm hover:bg-blue-700 transition">
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
                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $page->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $page->is_active ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.homepage.builder', $page->id) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm">
                                    <i class="fas fa-edit mr-1.5"></i>Builder
                                </a>
                                <button wire:click="delete({{ $page->id }})" wire:confirm="Are you sure you want to delete this page?" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                No pages found. <a href="{{ route('admin.page.create') }}" class="text-blue-600 hover:underline">Create your first page</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
