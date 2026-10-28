<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard</title>

    <!-- Tailwind CDN Config untuk Dark Mode "class" -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>

    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.3/dist/sweetalert2.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>

    <link rel="manifest" href="{{ route('admin.manifest') }}">
    <meta name="theme-color" content="#0f172a">

    <link href="{{ asset($setting?->favicon ?? $setting?->logo) }}" rel="icon" />

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset($setting?->logo) }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset($setting?->logo) }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset($setting?->logo) }}">

    <link rel="mask-icon" href="{{ asset($setting?->logo) }}" color="#5bbad5">

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @vite(['resources/js/app.js'])
    @livewireStyles

    @stack('styles')
</head>

<body class="text-slate-700 antialiased selection:bg-indigo-500 selection:text-white"
      x-data="{
          darkMode: false,
          initTheme() {
              // 1. Cek apakah pengguna pernah mengubah secara manual
              const savedTheme = localStorage.getItem('theme');

              if (savedTheme) {
                  this.darkMode = savedTheme === 'dark';
              } else {
                  // 2. Jika belum ada di localStorage, cek waktu jam akses (06:00 - 18:00)
                  const currentHour = new Date().getHours();
                  // Malam: Jam 18.00 ke atas ATAU sebelum jam 06.00 pagi
                  this.darkMode = (currentHour >= 18 || currentHour < 6);
              }

              this.applyTheme();
          },
          toggleDarkMode() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
              this.applyTheme();
          },
          applyTheme() {
              if (this.darkMode) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          }
      }"
      x-init="initTheme()">

    <div class="min-h-screen w-full relative flex flex-col bg-slate-50 dark:bg-slate-900 transition-colors duration-200">

        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-y-auto">
            @hasSection('content')
                @yield('content')
            @else
                {{ $slot ?? '' }}
            @endif
        </main>

    </div>

    @livewireScripts
    @stack('scripts')
</body>

</html>
