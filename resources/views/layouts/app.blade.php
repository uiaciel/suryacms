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
    <style>
        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }

        .main-scroll::-webkit-scrollbar {
            width: 5px;
        }

        .main-scroll::-webkit-scrollbar-thumb {
            background: #c7d2e0;
            border-radius: 10px;
        }

        code,
        .mono {
            font-family: 'DM Mono', monospace;
        }

        /* Stat card hover */
        .stat-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        /* Active nav */
        .nav-active {
            background: linear-gradient(135deg, #4a6cf7 0%, #384ae6 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(74, 108, 247, 0.35);
        }

        /* Toast */
        .toast-enter {
            animation: slideIn 0.3s ease forwards;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Table hover */
        .table-row-hover:hover td {
            background: #f8faff;
        }

        /* Badge pulse */
        .badge-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }
        }
    </style>

    @vite(['resources/js/app.js'])
    @livewireStyles

    @stack('styles')

</head>

<body class="bg-[#f0f3fb] text-slate-700 flex overflow-hidden h-screen" x-data="{
    sidebarOpen: @js($sidebarOpen ?? false),
    sidebarMinimized: @js($sidebarMinimized ?? false)
}">
    <div class="flex h-screen w-full relative">
        <!-- Overlay for mobile -->
        <div x-show="sidebarOpen" x-transition:enter="transition opacity-ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition opacity-ease-in duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-cloak>
        </div>

        <!-- SIDEBAR -->
        <aside
            :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0', sidebarMinimized ? 'lg:w-20' :
                'lg:w-64'
            ]"
            class="w-64 bg-[#1e2356] text-white fixed lg:static inset-y-0 left-0 z-50 flex-shrink-0 flex flex-col shadow-2xl overflow-hidden transition-all duration-300 ease-in-out">

            <!-- Sidebar Header -->
            <div class="flex items-center justify-between px-6 py-5 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center text-white font-black text-sm">
                        S</div>
                    <span x-show="!sidebarMinimized" class="text-lg font-black tracking-tight">Admin<span
                            class="text-blue-400">Panel</span></span>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-white/60 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Sidebar Menu -->
            <nav class="flex-1 px-3 py-4 overflow-y-auto sidebar-scroll space-y-2 gap-3">
                <div>

                    <p x-show="!sidebarMinimized"
                        class="text-[10px] font-bold text-white/30 uppercase tracking-widest px-3 mb-2 mt-2">Main Menu
                    </p>
                    <a href="/admin" wire:navigate
                        class="sidebar-menu-item w-full flex items-center px-3 py-2.5 rounded-xl transition-all text-sm font-semibold {{ Request::is('admin') && !Request::is('admin/*') ? 'nav-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}"
                        :class="sidebarMinimized ? 'justify-center px-0' : ''">
                        <i class="fas fa-th-large w-4 text-center" :class="sidebarMinimized ? '' : 'mr-3'"></i> <span
                            x-show="!sidebarMinimized">Dashboard</span>
                    </a>
                    <a href="/admin/posts" wire:navigate
                        class="sidebar-menu-item w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition-all text-sm font-semibold {{ Request::is('admin/posts*') ? 'nav-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}"
                        :class="sidebarMinimized ? 'justify-center px-0' : ''">
                        <span class="flex items-center"><i class="fas fa-file-alt w-4 text-center"
                                :class="sidebarMinimized ? '' : 'mr-3'"></i> <span
                                x-show="!sidebarMinimized">Posts</span></span>
                        <span x-show="!sidebarMinimized"
                            class="bg-blue-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $posts->count() }}</span>
                    </a>
                    <a href="/admin/pages" wire:navigate
                        class="sidebar-menu-item w-full flex items-center px-3 py-2.5 rounded-xl transition-all text-sm font-semibold {{ Request::is('admin/pages*') ? 'nav-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}"
                        :class="sidebarMinimized ? 'justify-center px-0' : ''">
                        <i class="fas fa-copy w-4 text-center" :class="sidebarMinimized ? '' : 'mr-3'"></i> <span
                            x-show="!sidebarMinimized">Pages</span>
                    </a>
                    <a href="/admin/galleries" wire:navigate
                        class="sidebar-menu-item w-full flex items-center px-3 py-2.5 rounded-xl transition-all text-sm font-semibold {{ Request::is('admin/galleries*') ? 'nav-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}"
                        :class="sidebarMinimized ? 'justify-center px-0' : ''">
                        <i class="fas fa-photo-video w-4 text-center" :class="sidebarMinimized ? '' : 'mr-3'"></i> <span
                            x-show="!sidebarMinimized">Media</span>
                    </a>
                    <a href="/admin/contacts" wire:navigate
                        class="sidebar-menu-item w-full flex items-center px-3 py-2.5 rounded-xl transition-all text-sm font-semibold {{ Request::is('admin/contacts*') ? 'nav-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}"
                        :class="sidebarMinimized ? 'justify-center px-0' : ''">
                        <i class="fas fa-envelope w-4 text-center" :class="sidebarMinimized ? '' : 'mr-3'"></i> <span
                            x-show="!sidebarMinimized">Inbox</span>
                    </a>
                </div>

                @php
                    $packageMenus = app()->bound('suryacms.admin.menus') ? app('suryacms.admin.menus') : [];
                @endphp

                @if (!empty($packageMenus))
                    @foreach ($packageMenus as $packageName => $menus)
                        <div>
                            <p x-show="!sidebarMinimized"
                                class="text-[10px] font-bold text-white/30 uppercase tracking-widest px-3 mb-2 mt-5">
                                {{ $packageName }}</p>

                            @foreach ($menus as $menu)
                                <a href="{{ $menu['route'] }}" wire:navigate
                                    class="sidebar-menu-item w-full flex items-center px-3 py-2.5 rounded-xl transition-all text-sm font-semibold {{ Request::is(ltrim($menu['route'], '/')) ? 'nav-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}"
                                    :class="sidebarMinimized ? 'justify-center px-0' : ''">
                                    <i class="{{ $menu['icon'] }} w-4 text-center"
                                        :class="sidebarMinimized ? '' : 'mr-3'"></i>
                                    <span x-show="!sidebarMinimized">{{ $menu['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endforeach
                @endif

                <div>
                    <p x-show="!sidebarMinimized"
                        class="text-[10px] font-bold text-white/30 uppercase tracking-widest px-3 mb-2 mt-5">Themes</p>
                    <a href="/admin/themes" wire:navigate
                        class="sidebar-menu-item w-full flex items-center px-3 py-2.5 rounded-xl transition-all text-sm font-semibold {{ Request::is('admin/themes') ? 'nav-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}"
                        :class="sidebarMinimized ? 'justify-center px-0' : ''">
                        <i class="fas fa-layer-group w-4 text-center" :class="sidebarMinimized ? '' : 'mr-3'"></i>
                        <span x-show="!sidebarMinimized">All Themes</span>
                    </a>
                    <a href="/admin/themes/editor"
                        class="sidebar-menu-item w-full flex items-center px-3 py-2.5 rounded-xl transition-all text-sm font-semibold {{ Request::is('admin/themes/editor*') ? 'nav-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}"
                        :class="sidebarMinimized ? 'justify-center px-0' : ''">
                        <i class="fas fa-code w-4 text-center" :class="sidebarMinimized ? '' : 'mr-3'"></i>
                        <span x-show="!sidebarMinimized">Editor</span>
                    </a>
                    <a href="/admin/themes/builder" wire:navigate
                        class="sidebar-menu-item w-full flex items-center px-3 py-2.5 rounded-xl transition-all text-sm font-semibold {{ Request::is('admin/themes/generate*') ? 'nav-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}"
                        :class="sidebarMinimized ? 'justify-center px-0' : ''">
                        <i class="fas fa-magic w-4 text-center" :class="sidebarMinimized ? '' : 'mr-3'"></i>
                        <span x-show="!sidebarMinimized">Builder</span>
                    </a>
                </div>

                @if (isset($setting) && $setting->homepage_type == 'homepage')
                    <div>
                        <p x-show="!sidebarMinimized"
                            class="text-[10px] font-bold text-white/30 uppercase tracking-widest px-3 mb-2 mt-5">
                            Homepage Builder</p>
                        <div x-data="{ open: {{ Request::is('admin/homepage-builder*') ? 'true' : 'false' }} }">
                            <button @click="open = !open"
                                class="sidebar-menu-item w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition-all text-sm font-semibold {{ Request::is('admin/homepage-builder*') ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/10 hover:text-white' }}"
                                :class="sidebarMinimized ? 'justify-center px-0' : ''">
                                <span class="flex items-center"><i class="fas fa-palette w-4 text-center"
                                        :class="sidebarMinimized ? '' : 'mr-3'"></i> <span
                                        x-show="!sidebarMinimized">Builder</span></span>
                                <i x-show="!sidebarMinimized"
                                    class="fas fa-chevron-down text-[10px] transition-transform duration-200"
                                    :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open && !sidebarMinimized" x-collapse x-cloak class="mt-1 space-y-1">
                                @foreach (collect($pages)->filter(fn($page) => Str::startsWith($page->title, 'Homepage'))->take(4) as $page)
                                    <a href="/admin/homepage-builder/{{ $page->slug }}"
                                        class="flex items-center pl-10 pr-3 py-2 rounded-xl text-xs font-medium transition-all {{ Request::is('admin/homepage-builder/' . $page->slug) ? 'text-blue-400 bg-white/5' : 'text-white/50 hover:text-white hover:bg-white/5' }}">
                                        <i class="fas fa-circle text-[6px] mr-3"></i> {{ $page->title }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <div>
                    <p x-show="!sidebarMinimized"
                        class="text-[10px] font-bold text-white/30 uppercase tracking-widest px-3 mb-2 mt-5">Tools &
                        Settings</p>
                    <a href="/admin/menu" wire:navigate
                        class="sidebar-menu-item w-full flex items-center px-3 py-2.5 rounded-xl transition-all text-sm font-semibold {{ Request::is('admin/menu*') ? 'nav-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}"
                        :class="sidebarMinimized ? 'justify-center px-0' : ''">
                        <i class="fas fa-bars w-4 text-center" :class="sidebarMinimized ? '' : 'mr-3'"></i> <span
                            x-show="!sidebarMinimized">Menu</span>
                    </a>
                    <a href="/admin/setting"
                        class="sidebar-menu-item w-full flex items-center px-3 py-2.5 rounded-xl transition-all text-sm font-semibold {{ Request::is('admin/setting*') ? 'nav-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}"
                        :class="sidebarMinimized ? 'justify-center px-0' : ''">
                        <i class="fas fa-cog w-4 text-center" :class="sidebarMinimized ? '' : 'mr-3'"></i> <span
                            x-show="!sidebarMinimized">Settings</span>
                    </a>
                </div>

                <div>

                </div>

            </nav>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-white/10">
                <div class="flex items-center gap-3 px-2">
                    <div
                        class="w-8 h-8 shrink-0 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                        {{ substr(Auth::user()->name ?? 'AS', 0, 2) }}</div>
                    <div x-show="!sidebarMinimized" class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name ?? 'Admin' }}</p>
                        <p class="text-[10px] text-white/40 truncate">Administrator</p>
                    </div>
                    <form x-show="!sidebarMinimized" method="POST" action="{{ route('logout') }}" class="inline"
                        onclick="this.submit(); return false;">
                        @csrf
                        <button type="button" class="text-white/40 hover:text-red-400 transition"><i
                                class="fas fa-sign-out-alt text-sm"></i></button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden">

            <!-- TOPBAR -->
            <header
                class="flex items-center justify-between px-6 py-3.5 bg-white border-b border-gray-100 sticky top-0 z-40 shadow-sm">
                <!-- Mobile Menu Toggle -->
                <button @click="sidebarOpen = true"
                    class="lg:hidden mr-4 text-gray-500 hover:text-blue-600 transition-colors">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <!-- Desktop Sidebar Toggle -->
                <button @click="sidebarMinimized = !sidebarMinimized"
                    class="hidden lg:flex mr-4 text-gray-400 hover:text-blue-600 transition-colors">
                    <i class="fas fa-outdent text-lg transition-transform"
                        :class="sidebarMinimized ? 'rotate-180' : ''"></i>
                </button>

                <div class="flex items-center gap-4">
                    <div class="hidden lg:flex items-center text-sm text-gray-400 gap-2">
                        <span>SuryaCMS</span>
                        <i class="fas fa-chevron-right text-[9px]"></i>
                        <span class="text-gray-700 font-semibold">Admin Dashboard</span>
                    </div>
                </div>

                <div class="flex items-center gap-3 ml-auto">
                    <!-- Theme Toggle -->
                    <button id="darkModeToggle"
                        class="w-9 h-9 flex items-center justify-center bg-gray-50 hover:bg-gray-100 rounded-xl transition">
                        <i class="fas fa-moon text-gray-500 text-sm" id="darkModeIcon"></i>
                    </button>

                    <!-- Profile -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center gap-2 focus:outline-none group">
                            <img class="h-9 w-9 rounded-xl border-2 border-blue-100 group-hover:border-blue-300 transition"
                                src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}&background=1e2356&color=fff&bold=true"
                                alt="Avatar">
                            <span
                                class="hidden sm:inline text-sm font-semibold text-gray-700 group-hover:text-blue-600 transition">{{ Auth::user()->name ?? 'User' }}</span>
                            <i class="fas fa-chevron-down text-[10px] text-gray-400 transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''"></i>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50"
                            x-cloak>
                            <a href="{{ route('admin.profile.edit') }}" wire:navigate
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                <i class="fas fa-user-cog mr-3 w-4 text-center text-gray-400"></i> Edit Profile
                            </a>
                            <a href="{{ route('admin.users.index') }}" wire:navigate
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                <i class="fas fa-users mr-3 w-4 text-center text-gray-400"></i> Manage Users
                            </a>
                            <a href="{{ route('admin.about') }}" wire:navigate
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                <i class="fas fa-info-circle mr-3 w-4 text-center text-gray-400"></i> About SuryaCMS
                            </a>
                            <a href="/admin/guide" wire:navigate
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition border-b border-gray-50">
                                <i class="fas fa-book mr-3 w-4 text-center text-gray-400"></i> Documentation
                            </a>
                            <a href="/admin/setting" wire:navigate
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition border-b border-gray-50">
                                <i class="fas fa-cog mr-3 w-4 text-center text-gray-400"></i> Settings
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                    <i class="fas fa-sign-out-alt mr-3 w-4 text-center"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- PAGE CONTENT -->
            <main class="flex-1 overflow-y-auto main-scroll p-4 lg:p-6 bg-[#f0f3fb]">

                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-100 px-6 py-3 text-sm text-gray-500 text-center">
                2025 © Surya CMS | <a href="{{ $setting->url ?? '#' }}"
                    class="text-blue-600 hover:underline">{{ $setting->url ?? 'https://suryacms.local' }}</a>
            </footer>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
    @livewireScriptConfig

    <script src=" https://cdn.jsdelivr.net/npm/sweetalert2@11.26.3/dist/sweetalert2.all.min.js "></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var darkModeToggle = document.getElementById('darkModeToggle');
            var htmlElement = document.documentElement;
            var darkModeIcon = document.getElementById('darkModeIcon');

            const currentTheme = localStorage.getItem('theme') ? localStorage.getItem('theme') : 'light';
            if (currentTheme === 'dark') {
                document.documentElement.classList.add('dark');
                darkModeIcon.className = 'fas fa-sun text-gray-500 text-sm';
            }

            darkModeToggle.addEventListener('click', function() {
                let isDark = document.documentElement.classList.contains('dark');
                if (!isDark) {
                    document.documentElement.classList.add('dark');
                    darkModeIcon.className = 'fas fa-sun text-gray-500 text-sm';
                    localStorage.setItem('theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    darkModeIcon.className = 'fas fa-moon text-gray-500 text-sm';
                    localStorage.setItem('theme', 'light');
                }
            });
        });
    </script>

</body>

</html>
