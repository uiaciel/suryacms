<div class="container">
    <h1>Gallery</h1>
    @foreach($gallery as $item)
        <a href="{{ asset('storage/'.$item->image_path) }}">
            <img src="{{ asset('storage/'.$item->cover_path) }}" alt="{{ $item->alt_text }}">
        </a>
    @empty
        <p>Gallery tidak ditemukan</p>
    @endforelse
</div>
