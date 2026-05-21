<!DOCTYPE html>
<html lang="{{ $setting->language ?? 'id' }}">

<head>
    <meta charset="utf-8" />
    @hasSection('seo')
    @yield('seo')
    @endif
    @if($setting->language == "en")
    <title>{{ $seo->title ?? $setting->sitename_translation }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="{{ $seo->keyword ?? $setting->keywords_translation }}" name="keywords" />
    <meta content="{{ $seo->description ?? $setting->description_translation }}" name="description" />
    <meta name="author" content="Kreasi Tek Media">
    <meta name="robots" content="index, follow">

    <meta property="og:title" content="{{ $seo->title ?? $setting->sitename_translation }}">
    <meta property="og:description" content="{{ $seo->description ?? $setting->description_translation }}">
    <meta property="og:image" content="{{ $seo->image ?? $setting->images }}">
    <meta property="og:url" content="{{ $seo->url ?? $setting->url }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $seo->title ?? $setting->sitename }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo->title ?? $setting->sitename_translation }}">
    <meta name="twitter:description" content="{{ $seo->description ?? $setting->description_translation }}">
    <meta name="twitter:image" content="{{ $seo->image ?? $setting->images }}">
    <meta name="twitter:url" content="{{ $seo->url ?? $setting->url }}">
    @else
    <title>{{ $seo->title ?? $setting->sitename }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="{{ $seo->keyword ?? $setting->keywords }}" name="keywords" />
    <meta content="{{ $seo->description ?? $setting->description }}" name="description" />
    <meta name="author" content="Kreasi Tek Media">
    <meta name="robots" content="index, follow">

    <meta property="og:title" content="{{ $seo->title ?? $setting->sitename }}">
    <meta property="og:description" content="{{ $seo->description ?? $setting->description }}">
    <meta property="og:image" content="{{ $seo->image ?? $setting->images }}">
    <meta property="og:url" content="{{ $seo->url ?? $setting->url }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $seo->title ?? $setting->sitename }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo->title ?? $setting->sitename }}">
    <meta name="twitter:description" content="{{ $seo->description ?? $setting->description }}">
    <meta name="twitter:image" content="{{ $seo->image ?? $setting->images}}">
    <meta name="twitter:url" content="{{ $seo->url ?? $setting->url }}">
    @endif

    <link href="{{ $setting->favicon }}" rel="icon">
    <link href="{{ $setting->favicon }}" rel="apple-touch-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="/frontend/default/css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/service-worker.js')
                        .then(reg => console.log('Service Worker registered', reg))
                        .catch(err => console.error('Service Worker error', err));
                });
            }
    </script>

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0d6efd">

    <meta name="google-site-verification" content="{{ $setting->googlesiteverification }}">
    <meta name="google_analytics" content="{{ $setting->google_analytics }}">
    <meta name="google_adsense" content="{{ $setting->google_adsense }}">

</head>

<body>
    @include('frontend::navigation')

    <main>

        @hasSection('schema')
            @yield('schema')
        @endif

        @hasSection('content')
            @yield('content')
        @else
            {{ $slot ?? '' }}
        @endif
    </main>
    <footer class="bg-dark py-4 mt-auto">
        <div class="container px-5">
            <div class="row align-items-center justify-content-between flex-column flex-sm-row">
                <div class="col-auto">
                    <div class="small m-0 text-white">
                        Copyright © Your Website 2025
                    </div>
                </div>
                <div class="col-auto">
                    <a class="link-light small" href="#!">
                        Privacy
                    </a>
                    <span class="text-white mx-1">
                        ·
                    </span>
                    <a class="link-light small" href="#!">
                        Terms
                    </a>
                    <span class="text-white mx-1">
                        ·
                    </span>
                    <a class="link-light small" href="#!">
                        Contact
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/frontend/default/js/scripts.js"></script>
</body>

</html>
