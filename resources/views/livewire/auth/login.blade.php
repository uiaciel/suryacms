
    <div class="min-h-screen flex flex-col justify-center items-center bg-slate-50 dark:bg-slate-900 px-4 transition-colors duration-200">

        <!-- Card Container -->
        <div class="w-full max-w-md">

            <!-- Logo & Title Section -->
            <div class="text-center mb-8">
                <a href="/" class="inline-flex items-center justify-center mb-4 transition-transform hover:scale-105">
                    @if(isset($setting) && $setting->logo)
                        <img src="{{ $setting->logo }}"
                             alt="{{ $setting->sitename ?? config('app.name', 'SuryaCMS') }}"
                             class="h-16 w-auto object-contain drop-shadow-sm">
                    @else
                        <!-- Fallback Logo SuryaCMS jika $setting->logo kosong -->
                        <div class="h-16 w-16 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-bold text-2xl shadow-lg shadow-indigo-500/30">
                            S
                        </div>
                    @endif
                </a>

                <h1 class="text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">
                    {{ $setting->sitename ?? 'SuryaCMS' }}
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Admin Dashboard
                </p>
            </div>

            <!-- Main Form Card -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700/60 p-8">

                <!-- ALERT: Error / Validation Messages -->
                @if ($errors->any())
                    <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/50 text-rose-700 dark:text-rose-400 text-sm flex items-start justify-between gap-3">
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

                <!-- ALERT: Session Status (e.g., reset password link sent) -->
                @if (session('status'))
                    <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 text-emerald-700 dark:text-emerald-400 text-sm flex items-center justify-between gap-3">
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
                        <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
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
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-200 text-sm focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all outline-none">
                        </div>
                    </div>

                    <!-- Password Field dengan Toggle View Password (Alpine.js) -->
                    <div x-data="{ showPassword: false }">
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
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
                                   class="w-full pl-4 pr-11 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-200 text-sm focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all outline-none">

                            <!-- Tombol View Password -->
                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg transition-colors focus:outline-none"
                                    tabindex="-1">
                                <!-- Eye Open Icon -->
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <!-- Eye Slash Icon -->
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
                                   class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 shadow-sm focus:ring-indigo-500/20 dark:bg-slate-900 dark:checked:bg-indigo-600">
                            <span class="ms-2 text-xs text-slate-600 dark:text-slate-400 font-medium">
                                Remember Me
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center px-5 py-3 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold text-sm rounded-xl shadow-lg shadow-indigo-500/25 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                            Login
                        </button>
                    </div>
                </form>

            </div>

            <!-- Footer / Watermark SuryaCMS -->
            <div class="text-center mt-8">
                <p class="text-xs text-slate-400 dark:text-slate-500">
                    Powered by <span class="font-semibold text-slate-600 dark:text-slate-400">SuryaCMS v{{ suryacms_version() }}</span>
                </p>
            </div>

        </div>
    </div>

