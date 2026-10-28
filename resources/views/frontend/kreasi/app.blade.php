<!DOCTYPE html>
<html lang="{{ $setting->language ?? 'en' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {!! seo_meta($seo ?? null) !!}
    {!! theme_vite_assets() !!}

    @stack('styles')
</head>

<body>
    @include('frontend::navigation')
    <main>
        @hasSection('seo')
            @yield('seo')
        @endif

        @hasSection('schema')
            @yield('schema')
        @endif

        @hasSection('content')
            @yield('content')
        @else
            {{ $slot ?? '' }}
        @endif
    </main>

    @stack('scripts')
</body>

</html>
