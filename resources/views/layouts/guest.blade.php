<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <link href=" https://cdn.jsdelivr.net/npm/sweetalert2@11.26.3/dist/sweetalert2.min.css " rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
        integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>

    <link rel="manifest" href="{{ route('admin.manifest') }}">
    <meta name="theme-color" content="#0f172a">

    <link href="{{ $setting->favicon }}" rel="icon" />

    <link rel="apple-touch-icon" sizes="180x180" href="{{ $setting->logo }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $setting->logo }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $setting->logo }}">

    <link rel="mask-icon" href="{{ $setting->logo }}" color="#5bbad5">

    @vite(['resources/js/app.js'])
    @livewireStyles

    @stack('styles')

</head>

<body class="bg-[#f0f3fb] text-slate-700 flex overflow-hidden h-screen">
    <div class="flex h-screen w-full relative">

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden">

            <!-- PAGE CONTENT -->
            <main class="flex-1 overflow-y-auto main-scroll">

                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif

            </main>

        </div>
    </div>

</body>

</html>
