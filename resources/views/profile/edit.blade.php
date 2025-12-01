@extends('suryacms::layouts.app')
@section('content')

    <div class="row">
        <div class="card mb-4">
            <div class="card-body">
                <section>
                    <header class="mb-4">
                        <h2 class="fs-5 fw-semibold text-body">
                            {{ __('Profile Information') }}
                        </h2>

                        <p class="mt-1 text-body-secondary">
                            {{ __("Update your account's profile information and email address.") }}
                        </p>
                    </header>

                    <form method="post" action="{{ route('admin.profile.update') }}" class="mt-4">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

                            @if (session('status') === 'profile-updated')
                            <p class="text-success small">{{ __('Saved.') }}</p>
                            @endif
                        </div>
                    </form>
{{--
                    <livewire:profile.update-password-form />

                    <livewire:profile.delete-user-form /> --}}

                </section>

            </div>
        </div>

    </div>
@endsection
