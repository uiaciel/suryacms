<div class="min-h-screen flex flex-col justify-center items-center px-4 transition-colors duration-700 relative overflow-hidden selection:bg-indigo-500 selection:text-white"
     :class="darkMode ? 'bg-gradient-to-b from-slate-950 via-indigo-950 to-slate-900' : 'bg-gradient-to-b from-amber-50 via-sky-50 to-indigo-50'">

    <!-- CSS Custom Keyframes Animation (Super Light) -->
    <style>
        /* Animasi Pesawat & Awan */
        @keyframes planeFly {
            0% {
                transform: translateX(-15vw) translateY(0);
                opacity: 0;
            }
            10% { opacity: 0.8; }
            90% { opacity: 0.8; }
            100% {
                transform: translateX(110vw) translateY(-12vh);
                opacity: 0;
            }
        }

        .animate-plane {
            animation: planeFly 22s linear infinite;
        }

        @keyframes cloudDriftSlow {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100vw); }
        }
        @keyframes cloudDriftFast {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100vw); }
        }

        /* Animasi Bintang Jatuh (Shooting Stars) */
        @keyframes shootingStar {
            0% { transform: translateX(0) translateY(0) rotate(-45deg) scale(0); opacity: 1; }
            70% { opacity: 1; }
            100% { transform: translateX(-300px) translateY(300px) rotate(-45deg) scale(1); opacity: 0; }
        }

        .animate-plane { animation: planeFly 25s linear infinite; }
        .animate-cloud-slow { animation: cloudDriftSlow 45s linear infinite; }
        .animate-cloud-fast { animation: cloudDriftFast 30s linear infinite; }

        .shooting-star {
            position: absolute;
            height: 2px;
            background: linear-gradient(-45deg, #ffffff, rgba(255, 255, 255, 0));
            filter: drop-shadow(0 0 6px #6366f1);
            animation: shootingStar 3s ease-in-out infinite;
        }
    </style>

    <!-- ================= BACKGROUND GRAPHICS ================= -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none select-none">

        <!-- --- DAY MODE ELEMENTS --- -->
        <div x-show="!darkMode" x-transition:enter="transition opacity duration-700 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <!-- Pendaran Cahaya Matahari -->
            <div class="absolute -top-20 -left-20 w-96 h-96 bg-amber-300/30 rounded-full blur-3xl"></div>

            <!-- SVG Matahari -->
            <div class="absolute top-10 left-10 md:top-16 md:left-20 text-amber-400/80 animate-pulse">
                <svg class="w-24 h-24 md:w-32 md:h-32 drop-shadow-md" fill="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="5"/>
                    <path stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M12 2v2m0 16v2m10-10h-2M4 12H2m15.071-7.071l-1.414 1.414M6.343 17.657l-1.414 1.414m12.728 0l-1.414-1.414M6.343 6.343L4.929 4.929"/>
                </svg>
            </div>

            <!-- Pesawat Terbang + Asap Jejak (Contrail) -->
            <div class="absolute top-24 left-0 animate-plane flex items-center gap-1 z-0">

                <!-- Asap Jejak Pesawat (Garis Gradien Pudar ke Kiri) -->
                <div class="w-32 md:w-48 h-[2px] bg-gradient-to-r from-transparent via-white/40 to-white/90 rounded-full blur-[1px]"></div>

                <!-- Ikon Pesawat di-rotate mengarah ke kanan atas -->
                <div class="rotate-[90deg] text-slate-500/80 dark:text-slate-200 -ml-2 drop-shadow-sm">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/>
                    </svg>
                </div>

            </div>

            <!-- Awan-Awan Kecil Melintas (Drift) -->
            <div class="absolute top-12 left-0 animate-cloud-slow text-white/70">
                <svg class="w-24 h-12" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z"/>
                </svg>
            </div>
            <div class="absolute top-36 left-0 animate-cloud-fast text-white/50" style="animation-delay: -12s;">
                <svg class="w-16 h-8" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z"/>
                </svg>
            </div>
            <div class="absolute top-28 left-0 animate-cloud-slow text-white/60" style="animation-delay: -25s;">
                <svg class="w-20 h-10" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z"/>
                </svg>
            </div>
        </div>

        <!-- --- NIGHT MODE ELEMENTS --- -->
        <div x-show="darkMode" x-cloak x-transition:enter="transition opacity duration-700 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <!-- Pendaran Cahaya Bulan -->
            <div class="absolute top-10 left-10 w-72 h-72 bg-indigo-500/20 rounded-full blur-3xl"></div>

            <!-- SVG Bulan Sabit -->
            <div class="absolute top-10 left-10 md:top-16 md:left-20 text-indigo-100/90 drop-shadow-[0_0_15px_rgba(199,210,254,0.4)]">
                <svg class="w-20 h-20 md:w-28 md:h-28" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.3 2a10 10 0 0 0 9.7 12.3 10 10 0 1 1-9.7-12.3z"/>
                </svg>
            </div>

            <!-- Bintang Jatuh (Shooting Stars) -->
            <div class="shooting-star top-10 right-20 w-32" style="animation-delay: 0s;"></div>
            <div class="shooting-star top-1/3 right-10 w-24" style="animation-delay: 2.2s;"></div>
            <div class="shooting-star top-1/4 right-1/2 w-40" style="animation-delay: 4.5s;"></div>

            <!-- Bintang Kelap-Kelip -->
            <div class="absolute top-12 right-1/4 text-indigo-200/70 animate-pulse">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 7.2h7.6l-6 4.8 2.4 7.2-6-4.8-6 4.8 2.4-7.2-6-4.8h7.6z"/></svg>
            </div>
            <div class="absolute top-1/3 right-12 text-blue-200/90 animate-pulse" style="animation-delay: 1s;">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 7.2h7.6l-6 4.8 2.4 7.2-6-4.8-6 4.8 2.4-7.2-6-4.8h7.6z"/></svg>
            </div>
            <div class="absolute bottom-1/3 left-1/4 text-indigo-300/50 animate-pulse" style="animation-delay: 0.5s;">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 7.2h7.6l-6 4.8 2.4 7.2-6-4.8-6 4.8 2.4-7.2-6-4.8h7.6z"/></svg>
            </div>
            <div class="absolute top-20 right-1/3 text-purple-200/60 animate-pulse" style="animation-delay: 1.5s;">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 7.2h7.6l-6 4.8 2.4 7.2-6-4.8-6 4.8 2.4-7.2-6-4.8h7.6z"/></svg>
            </div>

            <!-- Awan Malam Tipis -->
            <div class="absolute top-36 left-4 text-indigo-900/30">
                <svg class="w-64 h-24" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z"/>
                </svg>
            </div>
        </div>

    </div>
    <!-- ================= END BACKGROUND GRAPHICS ================= -->

    <!-- Tombol Switch Dark / Light Mode -->
    <div class="absolute top-5 right-5 z-20">
        <button type="button"
                @click="toggleDarkMode()"
                class="p-2.5 rounded-2xl bg-white/80 dark:bg-slate-800/80 backdrop-blur-md border border-slate-200/60 dark:border-slate-700/60 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 shadow-lg transition-all focus:outline-none hover:scale-105 active:scale-95">
            <svg x-show="darkMode" class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg x-show="!darkMode" x-cloak class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>
    </div>

    <!-- Card Container -->
    <div class="w-full max-w-md z-10 my-8">

        <!-- Logo & Title Section -->
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center justify-center mb-4 transition-transform hover:scale-105">
                @if($setting?->logo)
                    <img src="{{ asset($setting->logo) }}"
                         alt="{{ $setting->sitename ?? config('app.name', 'SuryaCMS') }}"
                         class="h-16 w-auto object-contain drop-shadow-md">
                @else
                    <div class="h-16 w-16 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-bold text-2xl shadow-lg shadow-indigo-500/30">
                        S
                    </div>
                @endif
            </a>

            <h1 class="text-2xl font-extrabold tracking-tight text-slate-800 dark:text-slate-100">
                {{ $setting?->sitename ?? 'SuryaCMS' }}
            </h1>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">
                Admin Dashboard
            </p>
        </div>

        <!-- Main Form Card (Glassmorphism Effect) -->
        <div class="bg-white/85 dark:bg-slate-800/85 backdrop-blur-xl rounded-3xl shadow-2xl shadow-indigo-950/10 dark:shadow-none border border-white/60 dark:border-slate-700/60 p-8 transition-all">

            <!-- ALERT: Error / Validation Messages -->
            @if ($errors->any())
                <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-2xl bg-rose-50/90 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-rose-700 dark:text-rose-300 text-sm flex items-start justify-between gap-3">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-5 h-5 shrink-0 text-rose-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <p class="font-semibold">Please Check your login</p>
                            <ul class="mt-1 list-disc list-inside text-xs space-y-1 opacity-90">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button @click="show = false" type="button" class="text-rose-400 hover:text-rose-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            <!-- ALERT: Session Status -->
            @if (session('status'))
                <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-2xl bg-emerald-50/90 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-700 dark:text-emerald-300 text-sm flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-xs font-medium">{{ session('status') }}</span>
                    </div>
                    <button @click="show = false" type="button" class="text-emerald-400 hover:text-emerald-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email / Username Address -->
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Email / Username
                    </label>
                    <div class="relative">
                        <input id="email"
                               type="text"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               autocomplete="username"
                               placeholder="nama@email.com"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-200 text-sm focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all outline-none">
                    </div>
                </div>

                <!-- Password Field -->
                <div x-data="{ showPassword: false }">
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Password
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 hover:underline">
                                Forget Password?
                            </a>
                        @endif
                    </div>

                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'"
                               id="password"
                               name="password"
                               required
                               autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full pl-4 pr-11 py-3 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-200 text-sm focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all outline-none">

                        <!-- Tombol View Password -->
                        <button type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg transition-colors focus:outline-none"
                                tabindex="-1">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a9.954 9.954 0 013.122-.38c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-0.469 0.469A9.993 9.993 0 0112 19c-1.12 0-2.193-.18-3.191-.512L13.875 18.825z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me"
                               type="checkbox"
                               name="remember"
                               class="rounded-md border-slate-300 dark:border-slate-700 text-indigo-600 shadow-sm focus:ring-indigo-500/20 dark:bg-slate-900 dark:checked:bg-indigo-600">
                        <span class="ms-2 text-xs text-slate-600 dark:text-slate-400 font-medium">
                            Remember Me
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit"
                            class="w-full inline-flex justify-center items-center px-5 py-3.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold text-sm rounded-2xl shadow-lg shadow-indigo-600/30 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 hover:scale-[1.01]">
                        Login
                    </button>
                </div>
            </form>

        </div>

        <!-- Footer / Watermark SuryaCMS -->
        <div class="text-center mt-8">
            <p class="text-xs text-slate-500/80 dark:text-slate-400/80">
                Powered by <span class="font-bold text-slate-700 dark:text-slate-300">SuryaCMS {{ suryacms_version() }}</span>
            </p>
        </div>

    </div>
</div>
