@extends('frontend::app')

@section('content')
    <div class="container">
        <h1>{{ $post->title }}</h1>
        <span>{{ formatDate($post->datepublish) }}</span>
        <span>{{ $post->view }}</span>
        <img src="{{ getFirstImageUrl($post) }}" alt="{{ $post->title }}">
        <div class="content">
            {!! $post->content !!}
        </div>
    </div>

    <ul>
        @foreach($tagcloud as $tag => $count)
            <li>{{ $tag }}</li>
        @endforeach
    </ul>

    <ul>
        @foreach ($latestposts as $latestpost)
            <li>
                <h1>{{ $latestpost->title }}</h1>
                <span>{{ formatDate($latestpost->datepublish) }}</span>
                <span>{{ $latestpost->view }}</span>
                <img src="{{ getFirstImageUrl($latestpost) }}" alt="{{ $latestpost->title }}">
                <div class="content">
                   {{ get_content_excerpt($latestpost->content, 100) }}
                </div>
            </li>
        @endforeach
    </ul>
@endsection
