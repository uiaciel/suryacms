@extends('frontend::app')

@section('content')
    @if ($page->is_builder)
        <style>
            {!! $page->css !!}
        </style>
        <div class="gjs-rendered-content">
            {!! $page->html !!}
        </div>
    @else
        <div class="container">
            <h1>{{ $page->title }}</h1>
            <span>{{ formatDate($page->datepublish) }}</span>
            <span>{{ $page->view }}</span>
            <div class="content">
                {!! $page->content !!}
            </div>
        </div>
        @if ($page->pdf)
            <div class="container">
                <h2>PDF</h2>
                <a href="{{ asset($page->pdf) }}" target="_blank">Download PDF</a>
            </div>
            <iframe src="/storage/{{ $page->pdf }}"></iframe>
        @endif
    @endif
@endsection
