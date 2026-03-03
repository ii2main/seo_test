@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Register</div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}" novalidate>
                            @csrf

                            <div class="form-group mb-3">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input
                                        id="name"
                                        type="text"
                                        class="form-control @error('name') is-invalid @enderror"
                                        name="name"
                                        value="{{ old('name') }}"
                                        required
                                        autocomplete="name"
                                        autofocus
                                >
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input
                                        id="email"
                                        type="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        name="email"
                                        value="{{ old('email') }}"
                                        required
                                        autocomplete="email"
                                >
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="password">Password <span class="text-danger">*</span></label>
                                <input
                                        id="password"
                                        type="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        name="password"
                                        required
                                        autocomplete="new-password"
                                >
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                                <input
                                        id="password_confirmation"
                                        type="password"
                                        class="form-control"
                                        name="password_confirmation"
                                        required
                                        autocomplete="new-password"
                                >
                            </div>

                            <div class="d-flex gap-2 align-items-center">
                                <button type="submit" class="btn btn-primary">
                                    Register
                                </button>

                                <a href="{{ route('login.form') }}" class="btn btn-link">
                                    Already have an account? Login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection