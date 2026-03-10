@extends('suryacms::layouts.app')
@section('content')
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Edit Profile</h1>
                <nav class="flex text-sm text-slate-500 mt-1" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 ">
                        <li><a href="/admin" class="hover:text-blue-600 transition-colors">Admin</a></li>
                        <li><i class="fas fa-chevron-right text-[10px] mx-2 text-slate-300"></i></li>
                        <li><span class="text-slate-400">User</span></li>
                        <li><i class="fas fa-chevron-right text-[10px] mx-2 text-slate-300"></i></li>
                        <li class="text-blue-600 font-medium">Edit Profile</li>
                    </ol>
                </nav>
            </div>
        </div>

        @if (session('status') === 'profile-updated')
            <div class="p-4 mb-4 text-sm text-green-800 rounded-xl bg-green-50 border border-green-100 flex items-center shadow-sm">
                <i class="fas fa-check-circle mr-2"></i>
                Profile updated successfully.
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 rounded-xl bg-red-50 border border-red-100 shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle mr-2 mt-0.5 flex-shrink-0"></i>
                    <div>
                        <p class="font-semibold mb-2">Please fix the following errors:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Profile Information -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50">
                <h2 class="text-lg font-bold text-slate-800">Profile Information</h2>
                <p class="text-sm text-slate-500 mt-1">Update your account's profile information and email address.</p>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('name') ? 'border-red-500' : 'border-slate-200' }} focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-sm"
                            required autofocus autocomplete="name">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('email') ? 'border-red-500' : 'border-slate-200' }} focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-sm"
                            required autocomplete="username">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-blue-500/25">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Update Password -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50">
                <h2 class="text-lg font-bold text-slate-800">Update Password</h2>
                <p class="text-sm text-slate-500 mt-1">Ensure your account is using a long, random password to stay secure.</p>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-5">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="update_password" value="1">

                    <div>
                        <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-1.5">Current Password</label>
                        <div class="relative group" x-data="{ show: false }">
                            <input :type="show ? 'text' : 'password'" id="current_password" name="current_password"
                                class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('current_password') ? 'border-red-500' : 'border-slate-200' }} focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-sm"
                                required autocomplete="current-password">
                            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">New Password</label>
                        <div class="relative group" x-data="{ show: false }">
                            <input :type="show ? 'text' : 'password'" id="password" name="password"
                                class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('password') ? 'border-red-500' : 'border-slate-200' }} focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-sm"
                                required autocomplete="new-password">
                            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1.5">Confirm Password</label>
                        <div class="relative group" x-data="{ show: false }">
                            <input :type="show ? 'text' : 'password'" id="password_confirmation" name="password_confirmation"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-sm"
                                required autocomplete="new-password">
                            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-slate-800/20">
                            <i class="fas fa-key mr-2"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Account -->
        <div class="bg-white rounded-2xl shadow-sm border border-red-100 overflow-hidden">
            <div class="p-6 border-b border-red-50">
                <h2 class="text-lg font-bold text-red-700">Delete Account</h2>
                <p class="text-sm text-red-600 mt-1">Once you delete your account, there is no going back. Please be certain.</p>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('admin.profile.destroy') }}" class="space-y-5" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')

                    <div>
                        <label for="password_delete" class="block text-sm font-semibold text-slate-700 mb-1.5">Password (to confirm deletion)</label>
                        <div class="relative group" x-data="{ show: false }">
                            <input :type="show ? 'text' : 'password'" id="password_delete" name="password"
                                class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('password') && request()->has('_method') && request('_method') === 'DELETE' ? 'border-red-500' : 'border-slate-200' }} focus:ring-4 focus:ring-red-500/10 focus:border-red-500 transition-all outline-none text-sm"
                                required autocomplete="current-password">
                            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @if ($errors->has('password'))
                            <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $errors->first('password') }}</p>
                        @endif
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-red-500/25">
                            <i class="fas fa-trash-alt mr-2"></i>Delete Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
@endsection
