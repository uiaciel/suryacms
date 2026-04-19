@extends('suryacms::layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header Section -->
    <div class="mb-8">
        <a href="{{ route('admin.guide.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-6 transition">
            <i class="fas fa-arrow-left"></i>
            Back to Guides
        </a>

        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white text-2xl">
                <i class="{{ $guide['icon'] }}"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-gray-900">{{ $guide['display_name'] }} Guide</h1>
                @if($guide['description'])
                <p class="text-gray-600 text-sm mt-2">{{ $guide['description'] }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-24">
                <h3 class="font-bold text-gray-900 mb-4">All Packages</h3>
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @forelse($allPackages as $pkgName => $pkg)
                    <a href="{{ $pkg['route'] }}"
                       class="block px-3 py-2 rounded-lg text-sm transition @if($pkgName === $package) bg-blue-100 text-blue-700 font-medium @else text-gray-700 hover:bg-gray-100 @endif">
                        <i class="{{ $pkg['icon'] }} mr-2"></i>
                        {{ $pkg['display_name'] }}
                    </a>
                    @empty
                    <p class="text-gray-500 text-sm">No packages available</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Content Area -->
                <div class="p-6 lg:p-8 prose prose-sm max-w-none">
                    {{-- Load the package guide view --}}
                    @includeIf($guide['view'])
                </div>

                <!-- Footer -->
                <div class="px-6 lg:px-8 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <div>
                            Guide for <span class="font-semibold">{{ $guide['display_name'] }}</span>
                        </div>
                        <a href="{{ route('admin.guide.index') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                            View All Guides
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.prose {
    --tw-prose-body: #374151;
    --tw-prose-headings: #111827;
    --tw-prose-lead: #6B7280;
    --tw-prose-links: #2563EB;
    --tw-prose-bold: #111827;
    --tw-prose-counters: #6B7280;
    --tw-prose-bullets: #D1D5DB;
    --tw-prose-hr: #E5E7EB;
    --tw-prose-quotes: #6B7280;
    --tw-prose-quote-borders: #E5E7EB;
    --tw-prose-captions: #6B7280;
    --tw-prose-kbd: #111827;
    --tw-prose-kbd-shadows: 0 0 0 1px rgba(17, 24, 39, 0.1);
    --tw-prose-code: #DC2626;
    --tw-prose-pre-code: #E5E7EB;
    --tw-prose-pre-bg: #1F2937;
    --tw-prose-th-borders: #D1D5DB;
    --tw-prose-td-borders: #E5E7EB;
    --tw-prose-invert-body: #D1D5DB;
    --tw-prose-invert-headings: #F3F4F6;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
    scroll-margin-top: 100px;
}

.prose h2 {
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.prose h3 {
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
}

.prose code {
    background-color: #F3F4F6;
    padding: 0.2em 0.4em;
    border-radius: 0.25rem;
    font-size: 0.875em;
}

.prose pre {
    background-color: #1F2937;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
}

.prose pre code {
    background-color: transparent;
    color: #E5E7EB;
    padding: 0;
}

.prose blockquote {
    border-left-color: #3B82F6;
    padding-left: 1rem;
    font-style: italic;
}

.prose a {
    color: #2563EB;
    text-decoration: underline;
}

.prose a:hover {
    color: #1D4ED8;
}

.prose table {
    border-collapse: collapse;
    width: 100%;
}

.prose thead {
    background-color: #F9FAFB;
    border-bottom: 1px solid #E5E7EB;
}

.prose th {
    padding: 0.75rem;
    text-align: left;
    font-weight: 600;
}

.prose td {
    padding: 0.75rem;
    border-bottom: 1px solid #E5E7EB;
}

.prose tbody tr:hover {
    background-color: #F9FAFB;
}
</style>
@endsection
