<meta charset="utf-8">

<title>{{ $data['title'] }}</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="keywords" content="{{ $data['keywords'] }}">
<meta name="description" content="{{ $data['description'] }}">
<meta name="author" content="Kreasi Tek Media">
<meta name="robots" content="index, follow">

<meta property="og:title" content="{{ $data['title'] }}">
<meta property="og:description" content="{{ $data['description'] }}">
<meta property="og:image" content="{{ $data['image'] }}">
<meta property="og:url" content="{{ $data['url'] }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $data['site_name'] }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $data['title'] }}">
<meta name="twitter:description" content="{{ $data['description'] }}">
<meta name="twitter:image" content="{{ $data['image'] }}">
<meta name="twitter:url" content="{{ $data['url'] }}">

<link rel="canonical" href="{{ $data['url'] }}">

@if (! empty($data['favicon']))
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $data['favicon'][16] }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $data['favicon'][32] }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ $data['favicon'][48] }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ $data['favicon'][96] }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $data['favicon']['apple-touch-icon'] }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ $data['favicon'][192] }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ $data['favicon'][512] }}">
    <link rel="shortcut icon" href="{{ $data['favicon'][32] }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="apple-mobile-web-app-title" content="{{ $data['site_name'] }}">
    <meta name="theme-color" content="#ffffff">
@endif
