@extends('frontend::app')
@section('content')
<h2>{{ $category->name }}</h2>

    <ul>
        @forelse ($posts as $post)
            <div class="container">
                <h1>{{ $post->title }}</h1>
                <span>{{ formatDate($post->datepublish) }}</span>
                <span>{{ $post->view }}</span>
                <img src="{{ getFirstImageUrl($post) }}" alt="{{ $post->title }}">
                <div class="content">
                    {!! $post->content !!}
                </div>
            </div>
        @empty
            <h1>Artikel tidak ditemukan</h1>
        @endforelse
    </ul>

    <aside>
        <h4>Daftar Kategori</h4>
        <ul>
            @foreach ($categories as $cat)
                <li>
                    <a href="{{ route('frontend.category.show', $cat->slug) }}">{{ $cat->name }}</a>
                </li>
            @endforeach
        </ul>
    </aside>
@endsection
