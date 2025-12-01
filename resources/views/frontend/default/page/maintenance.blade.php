<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport" />

    <title>{{ $setting->sitename ?? 'Website' }} - {{ $setting->tagline ?? 'Tageline' }}</title>
    <meta name="description" content="{{ $setting->description ?? 'Deskripsi Website' }}">
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
    <meta name="twitter:image" content="{{ $setting->images ?? '' }}">
    <meta name="twitter:url" content="{{ $setting->url }}">

    <link href="{{ $setting->favicon }}" rel="icon">
    <link href="{{ $setting->favicon }}" rel="apple-touch-icon">

    <script>
        if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/service-worker.js')
                        .then(reg => console.log('Service Worker registered', reg))
                        .catch(err => console.error('Service Worker error', err));
                });
            }
    </script>

   <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
   <link href="/frontend/default/css/styles.css" rel="stylesheet" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


    <style>
        :root,[data-bs-theme="light"] {

             /* Primary */
            --bs-primary: {{ $setting->color_primary ?? '#2E4030' }};
            --bs-primary-rgb: {{ implode(',', sscanf($setting->color_primary ?? '#2E4030', "#%02x%02x%02x")) }};

            /* Secondary */
            --bs-secondary: {{ $setting->color_secondary ?? '#A0A38B' }};
            --bs-secondary-rgb: {{ implode(',', sscanf($setting->color_secondary ?? '#A0A38B', "#%02x%02x%02x")) }};

            /* Success */
            --bs-success: {{ $setting->color_success ?? '#DCCA94' }};
            --bs-success-rgb: {{ implode(',', sscanf($setting->color_success ?? '#DCCA94', "#%02x%02x%02x")) }};

            /* Danger */
            --bs-danger: {{ $setting->color_danger ?? '#992222' }};
            --bs-danger-rgb: {{ implode(',', sscanf($setting->color_danger ?? '#992222', "#%02x%02x%02x")) }};

            /* Warning */
            --bs-warning: {{ $setting->color_warning ?? '#D6AE60' }};
            --bs-warning-rgb: {{ implode(',', sscanf($setting->color_warning ?? '#D6AE60', "#%02x%02x%02x")) }};

            /* Info */
            --bs-info: {{ $setting->color_info ?? '#C2B38B' }};
            --bs-info-rgb: {{ implode(',', sscanf($setting->color_info ?? '#C2B38B', "#%02x%02x%02x")) }};

            /* Light */
            --bs-light: {{ $setting->color_light ?? '#F5F5F2' }};
            --bs-light-rgb: {{ implode(',', sscanf($setting->color_light ?? '#F5F5F2', "#%02x%02x%02x")) }};

            /* Dark */
            --bs-dark: {{ $setting->color_dark ?? '#101b11' }};
            --bs-dark-rgb: {{ implode(',', sscanf($setting->color_dark ?? '#101b11', "#%02x%02x%02x")) }};
        }
    </style>

    @vite(['resources/css/app.css','resources/js/app.js'])
    @livewireStyles

</head>
<body>

<section class="bg-dark d-flex align-items-top py-4 px-4 min-vh-100">
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-6 text-center text-bg-primary p-4 p-md-5 rounded-3">
 <img class="img-fluid mb-4" loading="lazy" src="/{{ $setting->logo }}" width="245" height="80" alt="{{ $setting->sitename }}">
 <hr class="border-primary-subtle mb-4">
 <h2 class="h1 mb-4">We'll be back soon!</h2>
 <p class="lead mb-5">Sorry for the inconvenience but we're performing some maintenance at the moment. We'll be back online shortly!</p>
 <div class="text-center">

 <i class="fas fa-hourglass-half fa-spin fs-1"></i>
        </div>
      </div>
    </div>
  </div>
</section>

</body>

</html>
