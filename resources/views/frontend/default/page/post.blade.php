@extends('frontend::app')

@section('seo')
@php
    $seo = (object)[
        'title' => $post->title ?? '',
        'keyword' => $post->keyword ?? '',
        'description' => \Illuminate\Support\Str::limit(strip_tags($post->content), 155, '...'),
        'image' => $post->thumbnail ?? ($post->gambar()[0] ?? null),
        'url' => url()->current(),
    ];
@endphp
    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ $seo->description }}">
    <meta name="keywords" content="{{ $seo->keyword }}">
    <meta name="article:published_time" content="{{ $post->created_at->toIso8601String() }}">
    <meta name="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}">
    <meta name="article:author" content="{{ $post->user->name ?? 'Admin' }}">
    <meta name="article:section" content="{{ $post->category->name ?? '' }}">

    <!-- Open Graph Tags -->
    <meta property="og:title" content="{{ $seo->title }}">
    <meta property="og:description" content="{{ $seo->description }}">
    <meta property="og:image" content="{{ $seo->image }}">
    <meta property="og:url" content="{{ $seo->url }}">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="{{ config('app.name') }}">

    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo->title }}">
    <meta name="twitter:description" content="{{ $seo->description }}">
    <meta name="twitter:image" content="{{ $seo->image }}">

    <!-- Google News Tags -->
    <meta name="news_keywords" content="{{ $seo->keyword }}">
    <meta name="published_time" content="{{ $post->created_at->toIso8601String() }}">
    <meta name="publication_name" content="{{ config('app.name') }}">
@endsection

@section('schema')
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'NewsArticle',
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => url()->current()
        ],
        'headline' => $post->title ?? '',
        'image' => [$post->thumbnail ?? ($post->gambar()[0] ?? '')],
        'datePublished' => $post->created_at->toIso8601String() ?? '',
        'dateModified' => $post->updated_at->toIso8601String() ?? '',
        'author' => [
            '@type' => 'Person',
            'name' => $post->user->name ?? 'Admin'
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => config('app.name'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => asset('images/logo.png')
            ]
        ],
        'description' => \Illuminate\Support\Str::limit(strip_tags($post->content), 155, '...'),
        'articleSection' => $post->category->name ?? '',
        'keywords' => $post->keyword ?? '',
        'articleBody' => \Illuminate\Support\Str::limit(strip_tags($post->content), 500)
    ]) !!}
    </script>
@endsection

@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <article>
                    <!-- Post header-->
                    <header class="mb-4 border-start border-5 border-primary ps-4 mb-3">
                        <!-- Post title-->
                        <h1 class="fw-bolder mb-1">{{ $post->title }}</h1>
                        <!-- Post meta content-->
                        <div class="text-muted fst-italic mb-2"><a
                                class="badge bg-secondary text-decoration-none link-light"
                                href="/category/{{ $post->category->slug }}">{{ $post->category->name }}</a> {{ text('diposting pada ', 'Posted on')}} {{ formatDate($post->datepublish) }}</div>
                        <!-- Post categories-->

                    </header>

                    <section class="mb-5 text-dark">

                        {!! $post->content !!}

                    </section>
                </article>

            </div>
            <div class="col-md-4">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3">{{ text('Berita Terbaru', 'News Update') }}</h5>

                        @forelse ($latestposts as $terbaru)
                        <div class="row mb-3">
                            <div class="col-md-4">
                                {!! getFirstImage($terbaru, 'img-thumbnail', $terbaru->title) !!}
                            </div>
                            <div class="col-md-8">
                                <a href="/media/{{ $terbaru->slug }}" class="text-decoration-none">
                                    <h6 class="text-secondary">{{ $terbaru->title }}</h6>
                                </a>
                                <div class="d-flex justify-content-between align-items-center ">
                                    <small class="text-muted ">{{ formatDate($terbaru->datepublish) }}</small>
                                </div>
                            </div>
                        </div>

                        @empty
                        <p>Belum ada Berita</p>
                        @endforelse

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
