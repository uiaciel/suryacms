<div class="container">
    <h1>Blog</h1>

    <ul>
        @forelse ($latestlatests as $latest)
            <li>
                <h2>
                    <a href="{{ route('frontend.latest.show', $latest->slug) }}">{{ $latest->title }}</a>
                </h2>
                <img src="{{ getFirstImageUrl($latest) }}" alt="{{ $latest->title }}">

                <time>{{ \Carbon\Carbon::parse($latest->datepublish)->format('d M Y') }}</time>
                <span>{{ $latest->category->name ?? '-' }}</span>
                <p>{{ Str::limit(strip_tags(preg_replace('/<img[^>]+>/i', '', $latest->content)), 200, '...') }}</p>
                @if ($latest->tags)
                    <p>
                        @foreach (array_filter(explode(',', $latest->tags)) as $tag)
                            <a href="{{ url('/tag/' . Str::slug(trim($tag))) }}">{{ trim($tag) }}</a>
                        @endforeach
                    </p>
                @endif
            </li>
        @empty
            <li>{{ text('Belum ada latestingan.', 'No latests yet.') }}</li>
        @endforelse
    </ul>

</div>
