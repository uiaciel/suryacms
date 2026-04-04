<!DOCTYPE html>
<html lang="{{ $setting->language }} ?? 'en'">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport" />
    <title>{{ $setting->sitename }} - {{ $setting->tagline }}</title>
    <meta name="description" content="{{ $setting->description }}">
    <meta name="keywords" content="{{ $setting->keywords }}">
    <meta name="author" content="Kreasi Tek Media">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="{{ $setting->sitename }}">
    <meta property="og:description" content="{{ $setting->description }}">
    <meta property="og:image" content="{{ $setting->images }}">
    <meta property="og:url" content="{{ $setting->url }}">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $setting->sitename }}">
    <meta name="twitter:description" content="{{ $setting->description }}">
    <meta name="twitter:image" content="{{ $setting->images }} ?? ''">
    <meta name="twitter:url" content="{{ $setting->url }}">
    <!-- Favicons -->
    <link href="{{ $setting->favicon }}" rel="icon">
    <link href="{{ $setting->favicon }}" rel="apple-touch-icon">
    <!-- Bootstrap icons-->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="/frontend/default/css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @livewireStyles
</head>

<body>

    <main>
        {{ $slot }}
    </main>
    @stack('scripts')
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/frontend/default/js/scripts.js"></script>
</body>

</html>
