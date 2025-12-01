@extends('suryacms::layouts.app')
@section('content')
        <div class="row">

           <div class="card">
                <div class="card-body">
                        @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                         <header class="mb-4">
                             <h2 class="fs-5 fw-semibold text-body">
                                 Create User
                             </h2>

                             <p class="mt-1 text-body-secondary">
                                 Hanya Admin yang bisa menambahkan user baru
                             </p>
                         </header>

                        @if (!$verified)
                        <form method="POST" action="{{ route('admin.users.verifyPassword') }}" class="card p-4 shadow-sm mb-4">
                            @csrf
                            <div class="mb-3">
                                <label for="password" class="form-label">Masukkan Password Anda (User ID 1)</label>
                                <input type="password" name="password" class="form-control" required>
                                @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Verifikasi</button>
                        </form>
                        @else
                        <form method="POST" action="{{ route('admin.users.store') }}" class="card p-4 shadow-sm">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" name="name" class="form-control" required>
                                @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                                @error('email')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                                @error('password')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-success">Buat User</button>
                        </form>
                        @endif

                </div>
           </div>

        </div>

@endsection
