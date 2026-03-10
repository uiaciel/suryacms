<div class="space-y-3">
    @if(!empty($scrapeResult) && isset($scrapeResult['title']))
        <div class="space-y-4">
            {{-- Scraped Title --}}
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-semibold text-gray-700">Scraped Title</label>
                    <button type="button"
                        class="text-xs px-3 py-1 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded transition-colors"
                        wire:click="copyToEditor('title')"
                        title="Copy to title field">
                        <i class="fas fa-copy"></i> Copy Title
                    </button>
                </div>
                <p class="text-gray-700 text-sm">{{ $scrapeResult['title'] ?? 'No title' }}</p>
            </div>

            {{-- Scraped Content Preview --}}
            <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-semibold text-gray-700">Scraped Content</label>
                    <div class="flex gap-2">
                        <button type="button"
                            class="text-xs px-3 py-1 bg-purple-100 text-purple-700 hover:bg-purple-200 rounded transition-colors"
                            wire:click="copyToEditor('content')"
                            title="Copy content to editor">
                            <i class="fas fa-copy"></i> Content Only
                        </button>
                        <button type="button"
                            class="text-xs px-3 py-1 bg-indigo-100 text-indigo-700 hover:bg-indigo-200 rounded transition-colors"
                            wire:click="copyFullToEditor"
                            title="Copy image + content to editor">
                            <i class="fas fa-image"></i> + Image
                        </button>
                    </div>
                </div>
                <div class="text-gray-700 text-sm max-h-64 overflow-y-auto bg-white p-3 rounded border border-purple-100">
                    {!! nl2br(e(substr($scrapeResult['content'] ?? '', 0, 500))) !!}
                    @if(strlen($scrapeResult['content'] ?? '') > 500)
                        <p class="text-gray-500 text-xs mt-2">... (truncated for preview)</p>
                    @endif
                </div>
            </div>

            {{-- Scraped Image --}}
            @if(!empty($scrapeResult['image']))
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-semibold text-gray-700">Scraped Image</label>
                        <button type="button"
                            class="text-xs px-3 py-1 bg-green-100 text-green-700 hover:bg-green-200 rounded transition-colors"
                            wire:click="copyToEditor('image')"
                            title="Copy image to editor">
                            <i class="fas fa-copy"></i> Copy Image
                        </button>
                    </div>
                    <img src="{{ $scrapeResult['image'] }}" alt="Scraped Image" class="max-w-xs h-auto rounded border border-green-200">
                </div>
            @endif
        </div>
    @else
        <div class="text-center text-gray-500 py-8">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p>No scraped content yet</p>
            <p class="text-xs mt-1">Enter a URL and click "Scrape" to fetch content</p>
        </div>
    @endif
</div>
