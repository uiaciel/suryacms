<div>

    <div class="col-12">
        <div class="card shadow-lg rounded-4 border-0">
            <div class="card-header d-flex justify-content-between align-items-center bg-white p-3 border-bottom">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="bi bi-youtube me-2 text-danger"></i> Video Library (YouTube)
                </h5>
                <button type="button" class="btn btn-sm btn-outline-danger fw-semibold" data-bs-toggle="modal" data-bs-target="#youtubeModal">
                    <i class="bi bi-plus-circle-fill me-1"></i> Add YT Video
                </button>
            </div>
            <div class="card-body p-4">
                <div class="list-group list-group-flush">
                   @forelse($videos as $video)
                   <div class="list-group-item d-flex justify-content-between align-items-center py-3 px-0" wire:key="video-{{ $video->id }}">
                       <div class="d-flex align-items-center">

                           <a href="#" data-bs-toggle="modal" data-bs-target="#youtubePreviewModal{{ $video->id }}" class="me-3 flex-shrink-0">
                               <img src="{{ $video->thumbnail_url }}" alt="thumb" class="rounded-3 shadow-sm" style="width: 120px; height: 80px; object-fit: cover;">
                           </a>

                           <div class="ms-2">
                               <div class="fw-bold text-dark mb-1">{{ $video->title }}</div>
                               <span class="badge bg-secondary me-2">{{ $video->category }}</span>

                               <button class="badge bg-primary border-0" data-bs-toggle="modal" data-bs-target="#youtubePreviewModal{{ $video->id }}">
                                   <i class="bi bi-play-circle me-1"></i> View
                               </button>

                               <button class="badge bg-warning text-dark border-0" wire:click="openEdit({{ $video->id }})">
                                   <i class="bi bi-pencil-square me-1"></i> Edit
                               </button>

                               <button class="badge bg-danger border-0" wire:click="delete({{ $video->id }})" onclick="return confirm('Are You Sure To Delete This Video?')">
                                   <i class="bi bi-trash-fill me-1"></i> Delete
                               </button>
                           </div>
                       </div>

                       <div class="ms-3 flex-shrink-0">
                           <select class="form-select form-select-sm" wire:model="statusPerVideo.{{ $video->id }}" wire:change="editStatus({{ $video->id }})">
                               <option value="published">Published</option>
                               <option value="draft">Draft</option>
                           </select>
                       </div>
                   </div>

                   <div class="modal fade" id="youtubePreviewModal{{ $video->id }}" tabindex="-1" aria-hidden="true">
                       <div class="modal-dialog modal-dialog-centered modal-lg">
                           <div class="modal-content rounded-4 shadow">
                               <div class="modal-header bg-dark text-white">
                                   <h5 class="modal-title">
                                       <i class="bi bi-youtube me-2"></i> Preview Video
                                   </h5>
                                   <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                               </div>

                               <div class="modal-body">

                                   {{-- Video Player --}}
                                   <div class="ratio ratio-16x9 mb-3">
                                       <iframe src="https://www.youtube.com/embed/{{ $video->video_id }}" allowfullscreen></iframe>
                                   </div>

                                   {{-- Info --}}
                                   <h5 class="fw-bold">{{ $video->title }}</h5>
                                   <p class="text-muted mb-2">{{ $video->category }}</p>
                                   <p>{{ $video->description }}</p>

                               </div>
                           </div>
                       </div>
                   </div>

                   {{-- Modal Edit Video --}}
                   <div wire:ignore.self class="modal fade" id="modalEditYT" tabindex="-1">
                       <div class="modal-dialog modal-dialog-centered modal-md">
                           <div class="modal-content rounded-4 shadow">
                               <div class="modal-header bg-warning">
                                   <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Edit Video</h5>
                                   <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                               </div>

                               <div class="modal-body">

                                    <div class="mb-3">
                                        <img src="{{ $video->thumbnail_url }}" alt="thumb" class="rounded-3 shadow-sm" style="width: 120px; height: 80px; object-fit: cover;">
                                    </div>
                                   <div class="mb-3">
                                       <label class="form-label fw-semibold">Title</label>
                                       <input type="text" class="form-control" wire:model="editTitle">
                                   </div>

                                   <div class="mb-3">
                                       <label class="form-label fw-semibold">Category</label>
                                       <input type="text" class="form-control" wire:model="editCategory">
                                   </div>

                                   <div class="mb-3">
                                       <label class="form-label fw-semibold">Description</label>
                                       <textarea class="form-control" rows="4" wire:model="editDescription"></textarea>
                                   </div>

                                   <button class="btn btn-warning w-100 fw-bold" wire:click="saveEdit">
                                       <i class="bi bi-save me-1"></i> Save Changes
                                   </button>

                               </div>
                           </div>
                       </div>
                   </div>

                   @empty
                   <div class="text-center p-3 text-muted">
                       <i class="bi bi-film me-2"></i> No YouTube videos found.
                   </div>
                   @endforelse

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="youtubeModal" tabindex="-1" aria-labelledby="youtubeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="youtubeModalLabel">
                        <i class="bi bi-youtube me-2"></i> Add YouTube Video
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <livewire:suryacms::admin.youtube.youtube-create />
                </div>
            </div>
        </div>
    </div>

{{-- Modal Preview Video --}}
<div wire:ignore.self class="modal fade" id="modalPreviewYT" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="bi bi-youtube me-2"></i> Preview Video</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <div class="ratio ratio-16x9">
                    <iframe id="previewYoutubeFrame" src="" allowfullscreen></iframe>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {

        Livewire.on('open-youtube-modal', data => {
            let modal = new bootstrap.Modal(document.getElementById('modalPreviewYT'));
            document.getElementById('previewYoutubeFrame').src = data.videoUrl;
            modal.show();
        });

        Livewire.on('show-edit-modal', () => {
            new bootstrap.Modal(document.getElementById('modalEditYT')).show();
        });

        Livewire.on('hide-edit-modal', () => {
            let editModal = bootstrap.Modal.getInstance(document.getElementById('modalEditYT'));
            editModal.hide();
        });

    });

</script>

@endpush

</div>
