<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Homepage Builder</title>
    @stack('styles')
    @livewireStyles
</head>
<body class="builder-editor">
    {{ $slot }}
    @livewireScripts
    @stack('scripts')
</body>
</html>
