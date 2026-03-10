<div>
    <div class="col-span-full">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="border-b border-gray-200 p-4 flex items-center justify-between">
                <h5 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="bi bi-youtube text-red-600"></i> Video Library (YouTube)
                </h5>
                <button type="button" class="inline-flex items-center px-4 py-2 border border-red-600 text-red-600 font-semibold rounded-lg hover:bg-red-50 transition-colors" onclick="document.getElementById('youtubeModal').classList.remove('hidden')">
                    <i class="bi bi-plus-circle-fill mr-1"></i> Add YT Video
                </button>
            </div>

            <!-- Video List -->
            <div class="p-4">
                <div class="space-y-0">
                    @forelse($videos as $video)
                    <div class="border-b border-gray-200 py-3 px-0 flex items-center justify-between last:border-b-0" wire:key="video-{{ $video->id }}">
                        <!-- Video Info -->
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <!-- Thumbnail -->
                            <button
                                onclick="document.getElementById('youtubePreviewModal{{ $video->id }}').classList.remove('hidden')"
                                class="flex-shrink-0 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow"
                            >
                                <img src="{{ $video->thumbnail_url }}" alt="thumb" class="w-32 h-20 object-cover">
                            </button>

                            <!-- Details -->
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-gray-900 mb-2">{{ $video->title }}</div>

                                <div class="flex flex-wrap gap-2">
                                    <!-- Category Badge -->
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-200 text-gray-800">
                                        {{ $video->category }}
                                    </span>

                                    <!-- View Button -->
                                    <button
                                        onclick="document.getElementById('youtubePreviewModal{{ $video->id }}').classList.remove('hidden')"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-200 text-blue-800 hover:bg-blue-300 transition-colors"
                                    >
                                        <i class="bi bi-play-circle mr-1"></i> View
                                    </button>

                                    <!-- Edit Button -->
                                    <button
                                        wire:click="openEdit({{ $video->id }})"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-200 text-amber-800 hover:bg-amber-300 transition-colors"
                                    >
                                        <i class="bi bi-pencil-square mr-1"></i> Edit
                                    </button>

                                    <!-- Delete Button -->
                                    <button
                                        wire:click="delete({{ $video->id }})"
                                        onclick="return confirm('Are you sure you want to delete this video?')"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-200 text-red-800 hover:bg-red-300 transition-colors"
                                    >
                                        <i class="bi bi-trash-fill mr-1"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Status Select -->
                        <div class="flex-shrink-0 ml-3">
                            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                wire:model="statusPerVideo.{{ $video->id }}"
                                wire:change="editStatus({{ $video->id }})">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>

                    <!-- Preview Modal -->
                    <div class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center" id="youtubePreviewModal{{ $video->id }}" onclick="event.target.id === 'youtubePreviewModal{{ $video->id }}' && this.classList.add('hidden')">
                        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto" @click.stop>
                            <!-- Modal Header -->
                            <div class="bg-gray-900 text-white p-6 flex items-center justify-between rounded-t-2xl">
                                <h5 class="text-lg font-bold flex items-center gap-2">
                                    <i class="bi bi-youtube"></i> Preview Video
                                </h5>
                                <button type="button" class="text-white hover:bg-gray-800 p-1 rounded transition-colors" onclick="document.getElementById('youtubePreviewModal{{ $video->id }}').classList.add('hidden')">
                                    <i class="bi bi-x text-2xl"></i>
                                </button>
                            </div>

                            <!-- Modal Body -->
                            <div class="p-6">
                                <!-- Video Player -->
                                <div class="aspect-video bg-gray-900 rounded-lg overflow-hidden mb-4">
                                    <iframe src="https://www.youtube.com/embed/{{ $video->video_id }}" class="w-full h-full" allowfullscreen></iframe>
                                </div>

                                <!-- Info -->
                                <h5 class="font-bold text-lg text-gray-900 mb-2">{{ $video->title }}</h5>
                                <p class="text-gray-600 text-sm mb-3">{{ $video->category }}</p>
                                <p class="text-gray-700">{{ $video->description }}</p>
                            </div>
                        </div>
                    </div>

                    @empty
                    <!-- Empty State -->
                    <div class="text-center py-12 text-gray-500">
                        <i class="bi bi-film text-4xl mb-3 block text-gray-300"></i>
                        <p>No YouTube videos found.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Add YouTube Modal -->
    <div class="fixed inset-0 bg-black/50 z-40 hidden flex items-center justify-center" id="youtubeModal" onclick="event.target.id === 'youtubeModal' && this.classList.add('hidden')">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto" @click.stop>
            <!-- Modal Header -->
            <div class="bg-red-600 text-white p-6 flex items-center justify-between rounded-t-2xl">
                <h5 class="text-lg font-bold flex items-center gap-2" id="youtubeModalLabel">
                    <i class="bi bi-youtube mr-2"></i> Add YouTube Video
                </h5>
                <button type="button" class="text-white hover:bg-red-700 p-1 rounded transition-colors" onclick="document.getElementById('youtubeModal').classList.add('hidden')">
                    <i class="bi bi-x text-2xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <livewire:suryacms::admin.youtube.youtube-create />
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center" id="modalEditYT" onclick="event.target.id === 'modalEditYT' && this.classList.add('hidden')">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4" @click.stop>
            <!-- Modal Header -->
            <div class="bg-amber-500 text-white p-6 flex items-center justify-between rounded-t-2xl">
                <h5 class="text-lg font-bold flex items-center gap-2">
                    <i class="bi bi-pencil-square mr-2"></i> Edit Video
                </h5>
                <button type="button" class="text-white hover:bg-amber-600 p-1 rounded transition-colors" onclick="document.getElementById('modalEditYT').classList.add('hidden')">
                    <i class="bi bi-x text-2xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <div class="mb-4">
                    <img src="{{ $video->thumbnail_url ?? '' }}" alt="thumb" class="rounded-lg shadow-sm w-32 h-20 object-cover">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-900 mb-2">Title</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all" wire:model="editTitle">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-900 mb-2">Category</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all" wire:model="editCategory">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-900 mb-2">Description</label>
                    <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all resize-none" rows="4" wire:model="editDescription"></textarea>
                </div>

                <button class="w-full px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg transition-colors flex items-center justify-center gap-2" wire:click="saveEdit">
                    <i class="bi bi-save"></i> Save Changes
                </button>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {

        Livewire.on('open-youtube-modal', data => {
            let modal = document.getElementById('modalPreviewYT');
            document.getElementById('previewYoutubeFrame').src = data.videoUrl;
            modal.classList.remove('hidden');
        });

        Livewire.on('show-edit-modal', () => {
            document.getElementById('modalEditYT').classList.remove('hidden');
        });

        Livewire.on('hide-edit-modal', () => {
            document.getElementById('modalEditYT').classList.add('hidden');
        });

    });

</script>

@endpush

</div>

