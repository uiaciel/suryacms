<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ $titlePage }}</h1>
            <nav class="flex text-sm text-slate-500 mt-1" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li><a href="/admin" class="hover:text-blue-600">Admin</a></li>
                    <li><i class="fas fa-chevron-right text-[10px] mx-2"></i></li>
                    <li><span class="text-slate-400">User</span></li>
                    <li><i class="fas fa-chevron-right text-[10px] mx-2"></i></li>
                    <li class="text-blue-600 font-medium">{{ $titlePage }}</li>
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

    @if (session('error'))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-xl bg-red-50 border border-red-100 flex items-center shadow-sm">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Profile Information -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50">
            <h2 class="text-lg font-bold text-slate-800">Profile Information</h2>
            <p class="text-sm text-slate-500">Update your account's profile information and email address.</p>
        </div>
        <div class="p-6">
            <form wire:submit.prevent="updateProfile" class="space-y-5">
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('Name') }}</label>
                    <input type="text" id="name" wire:model="name"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-sm"
                        required autofocus autocomplete="name">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('Email') }}</label>
                    <input type="email" id="email" wire:model="email"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-sm"
                        required autocomplete="username">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-blue-500/25">
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update Password -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50">
            <h2 class="text-lg font-bold text-slate-800">Update Password</h2>
            <p class="text-sm text-slate-500">Ensure your account is using a long, random password to stay secure.</p>
        </div>
        <div class="p-6">
            <form wire:submit.prevent="updatePassword" class="space-y-5">
                @foreach(['current_password' => 'Current Password', 'password' => 'New Password', 'password_confirmation' => 'Confirm Password'] as $id => $label)
                <div>
                    <label for="{{ $id }}" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __($label) }}</label>
                    <div class="relative group" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" id="{{ $id }}" wire:model="{{ $id }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-sm"
                            required autocomplete="{{ $id === 'current_password' ? 'current-password' : 'new-password' }}">
                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    @error($id)
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>
                @endforeach

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-slate-800/20">
                        {{ __('Update Password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Alpine.js handles the password toggle now, but we keep this for any other logic
    document.addEventListener('livewire:navigated', () => {
        // Re-initialize any scripts if needed
    });
</script>
@endpush
