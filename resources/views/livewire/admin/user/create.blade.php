@extends('suryacms::layouts.app')
@section('content')
    <div class="row p-3">
        <header class="page-header d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold mb-2">Create User</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="/admin/users">Users</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </header>

        <div class="card bg-white border-0 shadow-sm">
            <div class="card-body p-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Step Progress -->
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="text-center">
                                <div class="rounded-circle {{ !$verified ? 'bg-primary text-white' : 'bg-success text-white' }} d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    @if(!$verified) 1 @else <i class="bi bi-check-lg"></i> @endif
                                </div>
                                <div class="small mt-1 fw-bold">Verifikasi</div>
                            </div>
                            <div style="width: 100px; height: 2px;" class="{{ $verified ? 'bg-success' : 'bg-light' }} mx-2"></div>
                            <div class="text-center">
                                <div class="rounded-circle {{ $verified ? 'bg-primary text-white' : 'bg-light text-muted' }} d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    2
                                </div>
                                <div class="small mt-1 {{ $verified ? 'fw-bold' : '' }}">Data User</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-8">
                        @if (!$verified)
                            <section>
                                <header class="mb-4 text-center">
                                    <h2 class="fs-5 fw-semibold text-body">Konfirmasi Keamanan</h2>
                                    <p class="mt-1 text-body-secondary">Silahkan masukkan password admin Anda untuk melanjutkan.</p>
                                </header>

                                <form method="POST" action="{{ route('admin.users.verifyPassword') }}" class="p-3 border rounded bg-light">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="password" class="form-label fw-bold">Password Admin</label>
                                        <input type="password" name="password" class="form-control" placeholder="••••••••" required autofocus>
                                        @error('password')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Verifikasi & Lanjut</button>
                                </form>
                            </section>
                        @else
                            <section>
                                <header class="mb-4">
                                    <h2 class="fs-5 fw-semibold text-body">Informasi User Baru</h2>
                                    <p class="mt-1 text-body-secondary">Lengkapi data di bawah ini untuk membuat akun baru.</p>
                                </header>

                                <form method="POST" action="{{ route('admin.users.store') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="name" class="form-label fw-bold">Nama Lengkap</label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                        @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label fw-bold">Alamat Email</label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                        @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="password" class="form-label fw-bold">Password</label>
                                            <input type="password" name="password" class="form-control" required>
                                            @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Password</label>
                                            <input type="password" name="password_confirmation" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between mt-4">
                                        <a href="/admin/users" class="btn btn-light">Batal</a>
                                        <button type="submit" class="btn btn-success px-4">Simpan User Baru</button>
                                    </div>
                                </form>
                            </section>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
