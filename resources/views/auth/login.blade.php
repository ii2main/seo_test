@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Login</div>

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

                        <form method="POST" action="{{ route('login') }}" novalidate>
                            @csrf

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
                                        autofocus
                                >
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="password">Password <span class="text-danger">*</span></label>
                                <input
                                        id="password"
                                        type="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        name="password"
                                        required
                                        autocomplete="current-password"
                                >
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="d-flex gap-2 align-items-center">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>

                                <a href="{{ route('register.form') }}" class="btn btn-link">
                                    Create account
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection