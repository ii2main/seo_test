<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SEO Rank')</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        body {
            background: #f6f8fb;
        }

        .app-navbar {
            background: linear-gradient(135deg, #0f172a 0%, #1f2937 60%, #0b1220 100%);
        }

        .app-navbar .navbar-brand {
            font-weight: 700;
            letter-spacing: .2px;
        }

        .app-navbar .nav-link {
            color: rgba(255,255,255,.85) !important;
            border-radius: .5rem;
            padding: .5rem .75rem;
            margin: 0 .15rem;
            transition: background-color .15s ease, color .15s ease;
        }

        .app-navbar .nav-link:hover {
            background: rgba(255,255,255,.10);
            color: #fff !important;
        }

        .app-navbar .nav-link.active {
            background: rgba(255,255,255,.16);
            color: #fff !important;
        }

        .app-shell {
            padding-top: 1.25rem;
            padding-bottom: 2rem;
        }

        .app-card {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .08);
        }

        .app-card .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(15, 23, 42, .06);
            font-weight: 600;
        }

        .btn-soft-light {
            background: rgba(255,255,255,.12);
            color: #fff;
            border: 1px solid rgba(255,255,255,.18);
        }

        .btn-soft-light:hover {
            background: rgba(255,255,255,.18);
            color: #fff;
        }

        .navbar-toggler {
            border-color: rgba(255,255,255,.25) !important;
        }

        .navbar-light .navbar-toggler-icon {
            filter: invert(1);
            opacity: .9;
        }
    </style>

    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark app-navbar shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">SEO Rank</a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            @auth
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('ranks.index') ? 'active' : '' }}"
                           href="{{ route('ranks.index') }}">
                            Ranks
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('domains.index') ? 'active' : '' }}"
                           href="{{ route('domains.index') }}">
                            Domains
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('locations.index') ? 'active' : '' }}"
                           href="{{ route('locations.index') }}">
                            Locations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('languages.index') ? 'active' : '' }}"
                           href="{{ route('languages.index') }}">
                            Languages
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav ml-auto align-items-lg-center">
                    <li class="nav-item mr-lg-2">
                        <span class="nav-link disabled" style="opacity:.85;">
                            {{ Auth::user()->name ?? 'Account' }}
                        </span>
                    </li>

                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-soft-light btn-sm">Logout</button>
                        </form>
                    </li>
                </ul>
            @endauth

            @guest
                <ul class="navbar-nav ml-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('login.form') ? 'active' : '' }}"
                           href="{{ route('login.form') }}">
                            Login
                        </a>
                    </li>
                    <li class="nav-item ml-lg-2">
                        <a class="btn btn-primary btn-sm"
                           href="{{ route('register.form') }}">
                            Register
                        </a>
                    </li>
                </ul>
            @endguest
        </div>
    </div>
</nav>

<div class="app-shell">
    <div class="container">
        @yield('content')
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
@stack('scripts')
</body>
</html>