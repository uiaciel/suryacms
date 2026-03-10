@extends('suryacms::layouts.app')
@section('content')
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Create User</h1>
                <nav class="flex text-sm text-slate-500 mt-1" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 ">
                        <li><a href="/admin" class="hover:text-blue-600">Admin</a></li>
                        <li><i class="fas fa-chevron-right text-[10px] mx-2"></i></li>
                        <li><a href="/admin/users" class="hover:text-blue-600">Users</a></li>
                        <li><i class="fas fa-chevron-right text-[10px] mx-2"></i></li>
                        <li class="text-blue-600 font-medium">Create</li>
                    </ol>
                </nav>
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-xl bg-green-50 border border-green-100 flex items-center shadow-sm">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <!-- Step Progress -->
            <div class="p-8 border-b border-slate-50 bg-slate-50/50">
                <div class="flex justify-center items-center max-w-md mx-auto">
                    <div class="flex flex-col items-center relative">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all {{ !$verified ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-green-500 text-white' }}">
                            @if(!$verified) 1 @else <i class="fas fa-check"></i> @endif
                        </div>
                        <span class="text-[11px] uppercase tracking-wider font-bold mt-2 {{ !$verified ? 'text-blue-600' : 'text-green-600' }}">Verifikasi</span>
                    </div>

                    <div class="flex-1 h-0.5 mx-4 transition-colors {{ $verified ? 'bg-green-500' : 'bg-slate-200' }}"></div>

                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all {{ $verified ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-slate-200 text-slate-500' }}">
                            2
                        </div>
                        <span class="text-[11px] uppercase tracking-wider font-bold mt-2 {{ $verified ? 'text-blue-600' : 'text-slate-400' }}">Data User</span>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <div class="max-w-xl mx-auto">
                    @if (!$verified)
                        <section class="space-y-6">
                            <div class="text-center">
                                <h2 class="text-xl font-bold text-slate-800">Konfirmasi Keamanan</h2>
                                <p class="text-sm text-slate-500 mt-1">Silahkan masukkan password admin Anda untuk melanjutkan.</p>
                            </div>

                            <form method="POST" action="{{ route('admin.users.verifyPassword') }}" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password Admin</label>
                                    <input type="password" name="password"
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-sm"
                                        placeholder="••••••••" required autofocus>
                                    @error('password')
                                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-500/25">
                                    Verifikasi & Lanjut
                                </button>
                            </form>
                        </section>
                    @else
                        <section class="space-y-6">
                            <div>
                                <h2 class="text-xl font-bold text-slate-800">Informasi User Baru</h2>
                                <p class="text-sm text-slate-500 mt-1">Lengkapi data di bawah ini untuk membuat akun baru.</p>
                            </div>

                            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
                                @csrf
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ old('name') }}"
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-sm" required>
                                    @error('name') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Email</label>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-sm" required>
                                    @error('email') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                                        <input type="password" name="password"
                                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-sm" required>
                                        @error('password') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1.5">Konfirmasi Password</label>
                                        <input type="password" name="password_confirmation"
                                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-sm" required>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-4">
                                    <a href="/admin/users" class="text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">Batal</a>
                                    <button type="submit" class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-green-500/25">
                                        Simpan User Baru
                                    </button>
                                </div>
                            </form>
                        </section>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
