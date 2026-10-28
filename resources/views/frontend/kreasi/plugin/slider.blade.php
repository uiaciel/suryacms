<div class="container">
    @forelse ($sliders as $slide)
        <figure>
            <img src="{{ asset('storage/' . $slide->image_path) }}"
                 alt="{{ $slide->alt_text ?? $slide->name }}">
            <figcaption>
                <h2>{{ $slide->name }}</h2>
                <p>{{ $slide->description }}</p>
            </figcaption>
        </figure>
    @empty
        <p>Slider tidak ditemukan</p>
    @endforelse
</div>
