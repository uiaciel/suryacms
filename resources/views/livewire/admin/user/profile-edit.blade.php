<div>
    <div class="d-flex mb-3">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">Admin</a></li>
                <li class="breadcrumb-item"><a href="#">User</a></li>
                <li class="breadcrumb-item active"><a href="#">{{ $titlePage }}</a></li>
                {{-- <li class="breadcrumb-item active" aria-current="page">Data</li> --}}
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-12">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form wire:submit.prevent="updateProfile" class="mt-6 space-y-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <input type="text" class="form-control" id="name" name="name" wire:model="name"
                                required autofocus autocomplete="name">
                            @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input type="email" class="form-control" id="email" name="email" wire:model="email"
                                required autocomplete="username">
                            @error('email')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror

                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form wire:submit.prevent="updatePassword" class="mt-6 space-y-6">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">{{ __('Current Password') }}</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="current_password"
                                    name="current_password" wire:model="current_password" required
                                    autocomplete="current-password">
                                <button type="button" class="btn btn-outline-secondary toggle-password"
                                    data-target="current_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('New Password') }}</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password"
                                    wire:model="password" required autocomplete="new-password">
                                <button type="button" class="btn btn-outline-secondary toggle-password"
                                    data-target="password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" wire:model="password_confirmation" required
                                    autocomplete="new-password">
                                <button type="button" class="btn btn-outline-secondary toggle-password"
                                    data-target="password_confirmation">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('Update Password') }}</button>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            document.querySelectorAll('.toggle-password').forEach(function(button) {
                                button.addEventListener('click', function() {
                                    const target = document.getElementById(this.getAttribute('data-target'));
                                    if (target.type === 'password') {
                                        target.type = 'text';
                                        this.innerHTML = '<i class="fas fa-eye-slash"></i>';
                                    } else {
                                        target.type = 'password';
                                        this.innerHTML = '<i class="fas fa-eye"></i>';
                                    }
                                });
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
