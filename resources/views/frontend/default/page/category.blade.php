@extends('frontend::app')
@section('title', $category->name)
@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8 col-md-12 p-4">
                <h2 class="mb-5 text-primary">{{ text('Kategori : ', 'Category : ')}} {{ $category->name }}</h2>
                <ul class="list-unstyled">
                    @forelse ($posts as $post)
                        <li class="article-list-item">
                            <div class="article-image-wrapper">
                                @foreach ($post->gambar() as $gam)
                                    @if ($loop->first)
                                        <img src="{{ $gam }} " class="article-image" alt="{{ $post->title }}">
                                    @endif
                                @endforeach

                            </div>
                            <div class="article-details">
                                <div class="article-meta-info d-flex justify-content-between">
                                    <span class="text-muted">Admin</span> <span class="text-muted">3 Min Read</span>
                                    {{-- <span
                                        class="badge bg-info">{{ $post->category->name }}</span> --}}
                                </div>
                                <a href="{{ route('frontend.post.show', $post->slug) }}"
                                    class="article-title-list">{{ $post->title }}</a>
                                <p class="article-excerpt-list">
                                    {{ Str::limit(strip_tags(preg_replace('/<img[^>]+>/i', '', $post->content)), 200, '...') }}
                                </p>
                            </div>
                        </li>
                    @empty
                    <li>{{ text('Belum ada postingan di kategori ini.', 'There are no posts in this category yet.') }}</li>
                    @endforelse

                </ul>
            </div>
            <div class="col-lg-4 col-md-12 p-4">
                <aside class="mb-3">
                    <div class="border-start border-4 border-primary mb-3">

                        <h4 class="fs-5 ms-2" style="color: #2d2d2d;">{{text('Kategori', 'Categories')}}</h4>
                    </div>
                    <div class="list-group">

                        @foreach ($categories as $category)
                            <a href="{{ route('frontend.category.show', $category->slug) }}"
                                class="list-group-item list-group-item-action">{{ $category->name }}</a>
                        @endforeach

                    </div>

                </aside>
                <aside class="mb-3">
                    <div class="border-start border-4 border-primary mb-3">

                        <h4 class="fs-5 ms-2" style="color: #2d2d2d;">{{ text('Berita Terbaru','Recent Post')}}</h4>
                    </div>

                    <div class="card mb-3">

                        <div class="card-body">

                        </div>
                    </div>

                </aside>

                <aside class="single_sidebar_widget tag_cloud_widget">
                    <h4 class="widget_title" style="color: #2d2d2d;">Tag Clouds</h4>
                    <ul class="list">
                        @foreach ($tagcloud as $tag => $count)
                            <li><a href="#">{{ $tag }} ({{ $count }})</a> </li>
                        @endforeach

                    </ul>
                </aside>
            </div>
        </div>

    </div>

@endsection
