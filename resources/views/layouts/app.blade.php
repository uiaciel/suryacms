<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href=" https://cdn.jsdelivr.net/npm/sweetalert2@11.26.3/dist/sweetalert2.min.css " rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Jost:wght@400;600;700&family=Sarina&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --indigo-600: #4f46e5;
            --indigo-700: #4338ca;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-500: #64748b;
            --slate-800: #1e293b;
            --slate-900: #0f172a;

            --success-custom: #10b981;
            --info-custom: #0ea5e9;
            --warning-custom: #f59e0b;
            --danger-custom: #ef4444;
        }

        :root,
        [data-bs-theme="light"] {}

        :root,
        [data-bs-theme="light"] {
            --bs-body-bg: #f5f7f9;

            --bs-body-bg: var(--slate-50);
            --bs-body-color: var(--slate-900);

            --bs-primary: var(--indigo-600);
            --bs-primary-rgb: 79, 70, 229;

            --bs-secondary: var(--slate-500);
            --bs-secondary-bg: var(--slate-100);

            --bs-border-color: var(--slate-200);
            --bs-border-color-translucent: rgba(226, 232, 240, 0.5);

            --bs-card-bg: #ffffff;
            --bs-tertiary-bg: #ffffff;

            --bs-success: var(--success-custom);
            --bs-info: var(--info-custom);
            --bs-warning: var(--warning-custom);
            --bs-danger: var(--danger-custom);

            --bs-link-color: var(--indigo-600);
            --bs-link-hover-color: var(--indigo-700);

            --bs-button-bg: var(--indigo-600);
            --bs-button-hover-bg: var(--indigo-700);

            --bs-sidebar-bg: #2c3e50;

            --bs-sidebar-text: #ecf0f1;
            --bs-sidebar-header-bg: #34495e;
            --bs-sidebar-link-hover: #3b5472;
            --bs-link-color: #212529;
            --bs-navbar-bg: #ffffff;

            --bs-card-bg: #ffffff;
            --bs-border-color: #e0e6ed;
            --bs-text-primary: var(--slate-900);

        }

        [data-bs-theme="dark"] {
            --bs-body-bg: var(--slate-950, #020617);
            --bs-body-color: var(--slate-100);

            --bs-primary: #818cf8;
            --bs-primary-rgb: 129, 140, 248;

            --bs-card-bg: var(--slate-900);
            --bs-tertiary-bg: var(--slate-900);
            --bs-border-color: var(--slate-800);

            --bs-secondary-bg: var(--slate-800);
            --bs-sidebar-bg: #131a23;
            --bs-sidebar-text: #bdc3c7;
            --bs-sidebar-header-bg: #1c2834;
            --bs-sidebar-link-hover: #1f2a38;
            --bs-navbar-bg: #2c3e50;

            --bs-link-color: #95a5a6;
            --bs-text-primary: var(--slate-50);
        }

        .logo-text {
            font-family: "Sarina", cursive;
            font-size: 1.8rem;
            letter-spacing: 1px;
        }

        /* --- Navbar Styles --- */
        .navbar {
            border-bottom: 1px solid var(--bs-border-color);
            padding: 0.75rem 1rem;
            background-color: var(--bs-navbar-bg);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
            z-index: 1020;
        }

        .navbar-brand {
            font-weight: 600;
        }

        .navbar .nav-link {
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            transition: background-color 0.2s;
        }

        .navbar .nav-link:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .text-primary {
            color: var(--bs-text-primary) !important;
        }

        /* Tombol Toggle Sidebar */
        #sidebarToggle {
            background-color: transparent !important;
            border: none !important;
            color: var(--bs-primary) !important;
            font-size: 1.5rem;
            padding: 0.25rem 0.5rem;
        }

        /* Dropdown Navbar */
        .navbar .dropdown-menu {
            border: 1px solid var(--bs-border-color);
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 0.5rem;
            min-width: 15rem;
        }

        .navbar .dropdown-item {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            transition: background-color 0.2s;
        }

        .navbar .dropdown-item:hover {
            background-color: var(--bs-primary);
            color: white !important;
        }

        /* --- Sidebar Styles --- */
        #sidebar-wrapper {
            background-color: var(--bs-sidebar-bg) !important;
            color: var(--bs-sidebar-text) !important;
            width: 16rem;
            /* Lebar sedikit diperluas */
            transition: all 0.3s ease-in-out;
            position: fixed;
            z-index: 1030;
            height: 100vh;
            overflow-y: auto;
            border-right: none;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            margin-left: -16rem;
            /* Sembunyikan default */
        }

        #wrapper.toggled #sidebar-wrapper {
            margin-left: 0;
        }

        /* Desktop View (Sidebar terlihat) */
        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0;
            }

            #page-content-wrapper {
                margin-left: 16rem;
                /* Geser konten utama */
                width: calc(100% - 16rem);
            }

            #wrapper.toggled #sidebar-wrapper {
                margin-left: -16rem;
            }

            #wrapper.toggled #page-content-wrapper {
                margin-left: 0;
                width: 100%;
            }
        }

        /* Header Sidebar */
        .sidebar-heading {
            padding: 1.5rem 1.5rem 1rem 1.5rem;
            font-size: 1.2rem;
            background-color: var(--bs-sidebar-header-bg);
            color: var(--bs-sidebar-text);
            font-weight: 600;
        }

        /* Menu Item Sidebar */
        .sidebar-menu-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 1.5rem;
            color: var(--bs-sidebar-text) !important;
            text-decoration: none;
            transition: background-color 0.2s, color 0.2s;
            font-weight: 400;
        }

        .sidebar-menu-item:hover {
            background-color: var(--bs-sidebar-link-hover) !important;
            color: white !important;
        }

        .sidebar-menu-item.active {
            background-color: var(--bs-primary) !important;
            color: white !important;
            border-radius: 0;
            box-shadow: inset 3px 0 0 0 #fff;
            /* Aksen aktif di kiri */
            font-weight: 600;
        }

        .sidebar-menu-item i {
            font-size: 1.25rem;
            width: 2rem;
            /* Menjaga lebar ikon yang konsisten */
        }

        .text-section {
            padding: 0.75rem 1.5rem 0.25rem 1.5rem;
            font-size: 0.75rem;
            color: #95a5a6;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        /* Style Konten Utama */
        .main-content {
            padding-top: 2rem;
            min-height: calc(100vh - 70px);
            /* Menyesuaikan tinggi navbar */
        }

        .card {
            border-radius: 1rem;
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            /* transform: translateY(-2px); */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
        }

        .fixed-right-btn {
            position: fixed;
            right: 0px;
            top: 90%;
            transform: translateY(-50%);
            z-index: 1051;
            border-radius: 50px 0 0 50px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            transition: all .2s ease-in-out;
        }

        .fixed-right-btn:hover {
            right: 25px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        }
    </style>

    @vite(['resources/js/app.js'])
    @livewireStyles

    @stack('styles')

</head>

<body>
    <div class="d-flex" id="wrapper">
        <div class="d-flex flex-column" id="sidebar-wrapper">
            <div class="sidebar-heading bg-primary logo-text text-white">
                Surya Cms

            </div>
            <div id="sidebar-menu" class="flex-grow-1">
                <a href="/admin" wire:navigate class="sidebar-menu-item {{ Request::is('admin') ? 'active' : '' }}"
                    data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                    <i class="bi bi-grid-fill me-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/admin/posts" wire:navigate
                    class="sidebar-menu-item {{ Request::is('admin/posts*') ? 'active' : '' }}" data-bs-toggle="tooltip"
                    data-bs-placement="right" title="Posts">
                    <i class="bi bi-file-earmark-text-fill me-3"></i>
                    <span>Posts</span>
                </a>
                <a href="/admin/pages" wire:navigate
                    class="sidebar-menu-item {{ Request::is('admin/pages*') ? 'active' : '' }}" data-bs-toggle="tooltip"
                    data-bs-placement="right" title="Pages">
                    <i class="bi bi-file-earmark-fill me-3"></i>
                    <span>Pages</span>
                </a>
                <a href="/admin/galleries" wire:navigate
                    class="sidebar-menu-item {{ Request::is('admin/galleries*') ? 'active' : '' }}"
                    data-bs-toggle="tooltip" data-bs-placement="right" title="Media Library">
                    <i class="bi bi-image-fill me-3"></i>
                    <span>Media Library</span>
                </a>
                <a href="/admin/contacts" wire:navigate
                    class="sidebar-menu-item {{ Request::is('admin/contacts*') ? 'active' : '' }}"
                    data-bs-toggle="tooltip" data-bs-placement="right" title="Inbox Messages">
                    <i class="bi bi-chat-dots-fill me-3"></i>
                    <span>Inbox Messages</span>
                </a>

                @php
                    $packageMenus = app()->bound('suryacms.admin.menus') ? app('suryacms.admin.menus') : [];
                @endphp

                @if (!empty($packageMenus))
                    @foreach ($packageMenus as $packageName => $menus)
                        <div class="text-section mt-4">
                            {{ $packageName }}
                        </div>

                        @foreach ($menus as $menu)
                            <a href="{{ $menu['route'] }}"
                                class="sidebar-menu-item {{ Request::is(ltrim($menu['route'], '/')) ? 'active' : '' }}"
                                data-bs-toggle="tooltip" data-bs-placement="right" title="{{ $menu['label'] }}">

                                <i class="{{ $menu['icon'] }} me-3"></i>
                                <span>{{ $menu['label'] }}</span>
                            </a>
                        @endforeach
                    @endforeach
                @endif

                <div class="text-section mt-4">
                    Tools & Settings
                </div>

                @if (isset($setting) && $setting->homepage_type == 'homepage')
                    <a data-bs-toggle="collapse" href="#homepageBuilderMenu" class="sidebar-menu-item"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="Homepage Builder"
                        aria-expanded="false">
                        <i class="bi bi-palette-fill me-2"></i> {{-- Tambahkan ikon di sini --}}
                        <span>Homepage Builder</span>
                        <span class="caret ms-auto"></span>
                    </a>
                    <div class="collapse" id="homepageBuilderMenu">
                        <ul class="nav nav-collapse">
                            @foreach (collect($pages)->filter(fn($page) => Str::startsWith($page->title, 'Homepage'))->take(4) as $page)
                                <li>
                                    <a href="/admin/homepage-builder/{{ $page->slug }}"
                                        class="sidebar-menu-item {{ Request::is('admin/homepage-builder/' . $page->slug) ? 'active' : '' }} ps-5"
                                        data-bs-toggle="tooltip" data-bs-placement="right" title="Homepage Editor">
                                        <i class="bi bi-list-task me-3"></i>
                                        <span>{{ $page->title }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <a href="/admin/menu" class="sidebar-menu-item {{ Request::is('admin/menu*') ? 'active' : '' }}"
                    wire:navigate data-bs-toggle="tooltip" data-bs-placement="right" title="Menu">
                    <i class="bi bi-list-task me-3"></i>
                    <span>Menu</span>
                </a>
                <a href="/admin/setting" wire:navigate
                    class="sidebar-menu-item {{ Request::is('admin/setting*') ? 'active' : '' }}"
                    data-bs-toggle="tooltip" data-bs-placement="right" title="Settings">
                    <i class="bi bi-gear-fill me-3"></i>
                    <span>Setting</span>
                </a>
            </div>
        </div>

        <div id="page-content-wrapper" class="flex-grow-1">
            <nav class="navbar navbar-expand-lg sticky-top" aria-label="Main navigation">
                <div class="container-fluid">
                    <button class="btn" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>

                    <a class="navbar-brand text-muted d-none d-md-block" href="#">
                        <span id="typing-effect"></span>
                        <span class="cursor"></span>
                    </a>

                    <div class="navbar-collapse collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mb-lg-0 d-flex align-items-center mb-2 ms-auto flex-row">
                            <li class="nav-item me-2">
                                <a href="{{ $setting->url ?? config('app.url') }}" target="_blank" class="nav-link">
                                    Homepage
                                </a>
                            </li>

                            <li class="nav-item me-2">
                                <a id="darkModeToggle" type="button" class="nav-link">
                                    <i class="bi bi-moon fs-5" id="darkModeIcon"></i>
                                </a>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center rounded-pill border p-2"
                                    href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}&background=4a69bd&color=fff&size=30&rounded=true"
                                        alt="Admin" class="rounded-circle me-2" style="height:30px; width:30px;">
                                    <span class="d-none d-lg-inline me-1">{{ Auth::user()->name ?? 'User' }}</span>

                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <h6 class="dropdown-header">
                                            <i class="bi bi-person me-2"></i>Admin Panel
                                        </h6>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="h{{ route('admin.profile.edit') }}"
                                            wire:navigate>

                                            <i class="bi bi-gear me-2"></i>Edit Profile
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.users.index') }}"
                                            wire:navigate>

                                            <i class="bi bi-people me-2"></i>Manage Users
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}"
                                            style="display: inline;">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                                            </button>
                                        </form>

                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="main-content container-fluid p-4">
                <div class="main">

                    @hasSection('content')
                        @yield('content')
                    @else
                        {{ $slot ?? '' }}
                    @endif

                </div>
                <div class="footer mt-3">
                    <div class="container">
                        2025 © Kreasi Tek Media | <a href="#">{{ $setting->url ?? 'https://#' }}</a>

                    </div>
                </div>

            </div>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
    @livewireScriptConfig

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src=" https://cdn.jsdelivr.net/npm/sweetalert2@11.26.3/dist/sweetalert2.all.min.js "></script>
    <script src="https://cdn.tiny.cloud/1/{{ env('TINY_EDITOR_KEY', 'plcsua64qzb9xyaxabkhv7wtjcpr0vzounlnexchn12g0x7a') }}/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var sidebarToggle = document.getElementById('sidebarToggle');
            var wrapper = document.getElementById('wrapper');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    wrapper.classList.toggle('toggled');
                });
            }

            var darkModeToggle = document.getElementById('darkModeToggle');
            var htmlElement = document.documentElement;
            var darkModeIcon = document.getElementById('darkModeIcon');

            const currentTheme = localStorage.getItem('theme') ? localStorage.getItem('theme') : 'light';
            htmlElement.setAttribute('data-bs-theme', currentTheme);
            darkModeIcon.className = currentTheme === 'dark' ? 'bi bi-sun fs-5' : 'bi bi-moon fs-5';

            darkModeToggle.addEventListener('click', function() {
                let theme = htmlElement.getAttribute('data-bs-theme');
                if (theme === 'light') {
                    theme = 'dark';
                    darkModeIcon.className = 'bi bi-sun fs-5';
                } else {
                    theme = 'light';
                    darkModeIcon.className = 'bi bi-moon fs-5';
                }
                htmlElement.setAttribute('data-bs-theme', theme);
                localStorage.setItem('theme', theme);
            });
        });
    </script>

</body>

</html>
