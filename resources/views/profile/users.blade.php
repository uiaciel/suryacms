@extends('suryacms::layouts.app')
@section('content')
    <div class="row p-3">
        <header class="page-header d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
                <h3 class="fw-bold mb-0">List Users</h3>
                <a href="/admin/users/create" class="btn btn-primary ms-3">
                    <i class="bi bi-plus-lg"></i> New User
                </a>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Users</li>
                </ol>
            </nav>
        </header>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Name</th>
                                <th>Email</th>
                                <th>Joined</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-semibold text-dark">{{ $user->name }}</span>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
