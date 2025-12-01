<section class="video-gallery bg-primary py-5">

    <div class="d-flex justify-content-center mb-3">
        <h2 class=" border border-5 border-white px-3 py-1 text-white rounded-5 ">Gamma TV
        </h2>

    </div>

    <div class="container">

        <div class="row row-cols-1 row-cols-md-3 g-4 wow flash">
            @foreach ($youtubes as $video)
                <div class="col">
                    <div class="video-item" data-bs-toggle="modal" data-bs-target="#videoModal"
                        data-video-id="{{ $video->video_id }}"
                        data-video-url="https://www.youtube.com/embed/{{ $video->video_id }}">
                        <img src="{{ $video->thumbnail_url }}" alt="Video Thumbnail" class="video-thumbnail">
                        <i class="fab fa-youtube play-button"></i>
                    </div>
                </div>
            @endforeach

            <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content modal-content-video">
                        <div class="modal-header bg-dark">
                            <h5 class="modal-title" id="videoModalLabel" style="color: white;">Gamma TV</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body modal-body-video">
                            <div class="embed-responsive embed-responsive-16by9">
                                <iframe id="youtube-player" class="embed-responsive-item" src=""
                                    allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const videoModal = document.getElementById('videoModal');
                    const youtubePlayer = document.getElementById('youtube-player');

                    videoModal.addEventListener('show.bs.modal', function(event) {
                        const button = event.relatedTarget;
                        const videoUrl = button.getAttribute('data-video-url');
                        youtubePlayer.src = videoUrl + '?autoplay=1';
                    });

                    videoModal.addEventListener('hide.bs.modal', function() {
                        youtubePlayer.src = '';
                    });
                });
            </script>
        </div>
    </div>
</section>
