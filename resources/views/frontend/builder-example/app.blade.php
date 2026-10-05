<!doctype html>
<html lang="{{ $setting->language ?? app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->title ?? ($setting->sitename ?? 'Surya CMS') }}</title>
    <link rel="stylesheet" href="{{ asset('frontend/builder-example/css/styles.css') }}">
    @stack('styles')
</head>
<body>
    @include('frontend::navigation')
    <main>
        @yield('content')
    </main>
    <footer class="site-footer">{{ $setting->sitename ?? 'Surya CMS' }}</footer>
    <script src="{{ asset('frontend/builder-example/js/scripts.js') }}"></script>
    @stack('scripts')
</body>
</html>
