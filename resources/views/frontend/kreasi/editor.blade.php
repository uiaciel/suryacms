<!DOCTYPE html>
<html lang="{{ $setting->language ?? 'en' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {!! seo_meta($seo ?? null) !!}
    {!! theme_vite_assets() !!}

    @stack('styles')
    @livewireStyles
</head>

<body>
    <main>
        {{ $slot ?? '' }}
    </main>

    @stack('scripts')
    @livewireScripts
</body>

</html>
