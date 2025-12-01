@extends('suryacms::layouts.app')
@section('content')

<div class="container">

    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">

            <div class="d-flex justify-content-between align-items-center">

                <header class="mb-4">
                    <h2 class="fs-5 fw-semibold text-body">
                        {{ __('List Users') }}
                    </h2>

                    <p class="mt-1 text-body-secondary">
                        {{ __("User can be access to admin.") }}
                    </p>
                </header>

<div>
                <a href="/admin/users/create" class="ms-3 btn btn-dark">New User</a>
</div>

            </div>

                    <ul class="list-group">

                        @foreach ($users as $user )

                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4>{{ $user->name }}</h4>
                                <h4>{{ $user->email }}</h4>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

