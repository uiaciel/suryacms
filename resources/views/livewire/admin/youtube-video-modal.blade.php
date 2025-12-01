<div>
    @if($showModal)
    <div class="modal-backdrop fade show"></div>

    <div class="modal fade show d-block" tabindex="-1" aria-labelledby="YoutubeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="YoutubeModalLabel">Select Video from Youtube Gallery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    @if (count($videosFromDb) > 0)
                    <div class="list-group list-group-flush border rounded-3 p-2 mb-3"
                        style="max-height: 250px; overflow-y: auto;">
                        @foreach ($videosFromDb as $video)
                        <li class="list-group-item d-flex align-items-center justify-content-between">

                            @if (!empty($video['thumbnail_url']))
                            <img src="{{ $video['thumbnail_url'] }}" alt="{{ $video['title'] }}"
                                class="img-thumbnail me-3" style="width: 80px; height: 50px; object-fit: cover;">
                            @endif
                            <span class="me-auto text-truncate">{{ $video['title'] }}</span>
                            <button type="button" onclick="copyImageUrlToClipboard('{{ $video['video_url'] }}')"
                                class="btn btn-success btn-sm ms-2">
                                Copy URL
                            </button>
                        </li>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted">No videos found in the database.</p>
                    @endif
                </div>

            </div>
        </div>
    </div>
    @endif
</div>
