<div>
    @if($showModal)
    <div class="fixed inset-0 z-40 bg-black/50 transition-opacity"></div>

    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="relative w-full max-w-5xl bg-white rounded-xl shadow-2xl flex flex-col max-h-[90vh]">

            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="text-lg font-bold text-gray-800" id="YoutubeModalLabel">
                    Select Video from Youtube Gallery
                </h3>
                <button type="button"
                    class="text-gray-400 hover:text-gray-600 transition-colors text-2xl"
                    wire:click="closeModal">
                    &times;
                </button>
            </div>

            <div class="p-4 md:p-6 overflow-hidden">
                @if (count($videosFromDb) > 0)
                <div class="space-y-1 overflow-y-auto border rounded-lg divide-y divide-gray-100 shadow-sm"
                     style="max-height: 400px;">

                    @foreach ($videosFromDb as $video)
                    <div class="flex items-center p-3 hover:bg-gray-50 transition-colors group">

                        @if (!empty($video['thumbnail_url']))
                        <div class="flex-shrink-0">
                            <img src="{{ $video['thumbnail_url'] }}" alt="{{ $video['title'] }}"
                                 class="w-20 h-12 rounded object-cover border border-gray-200">
                        </div>
                        @endif

                        <div class="flex-grow mx-4 min-w-0">
                            <span class="block text-sm font-medium text-gray-700 truncate">
                                {{ $video['title'] }}
                            </span>
                        </div>

                        <div class="flex-shrink-0">
                            <button type="button"
                                wire:click="selectVideo({{ $video['id'] }})"
                                class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-md transition-all shadow-sm">
                                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Insert Video
                            </button>
                        </div>
                    </div>
                    @endforeach

                </div>
                @else
                <div class="text-center py-10 border-2 border-dashed border-gray-200 rounded-lg">
                    <p class="text-gray-400 italic">No videos found in the database.</p>
                </div>
                @endif
            </div>

            <div class="p-4 border-t bg-gray-50 flex justify-end rounded-b-xl">
                <button wire:click="closeModal"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-100 transition-colors shadow-sm">
                    Close
                </button>
            </div>

        </div>
    </div>
    @endif
</div>
