<div class="container">
    <h1>Video</h1>

    @forelse ($youtubes as $video)
        <article>
            <h2>
                <a href="{{ $video->url ?? '#' }}" target="_blank" rel="noopener">
                    {{ $video->title }}
                </a>
            </h2>

            @if (!empty($video->video_id))
                <iframe
                    src="https://www.youtube.com/embed/{{ $video->video_id }}"
                    title="{{ $video->title }}"
                    width="560" height="315"
                    frameborder="0"
                    allowfullscreen>
                </iframe>
            @elseif (!empty($video->thumbnail))
                <a href="{{ $video->url ?? '#' }}" target="_blank" rel="noopener">
                    <img src="{{ $video->thumbnail }}" alt="{{ $video->title }}">
                </a>
            @endif

            <time>{{ \Carbon\Carbon::parse($video->created_at)->format('d M Y') }}</time>
        </article>
    @empty
        <p>Video Tidak ditemukan</p>
    @endforelse
</div>
