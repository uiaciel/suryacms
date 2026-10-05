@extends('frontend::app')
@section('content')
@if (! is_null($page->html))
    <style>{!! $css ?? $page->css !!}</style>
    {!! $html ?? $page->html !!}
@else
    <article class="page-content">
        <h1>{{ $page->title }}</h1>
        {!! $page->content !!}
    </article>
@endif
@endsection
