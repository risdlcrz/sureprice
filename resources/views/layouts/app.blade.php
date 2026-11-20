<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SurePrice')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1b5e20">
    <script>window.appUrl = p => {{ Js::from(rtrim(url('/'), '/')) }} + '/' + String(p).replace(/^\/+/, '');</script>
    @auth<meta name="unread-counts-url" content="{{ route('api.unread-counts') }}">@endauth
    {{-- Restore the collapsed sidebar before first paint (no flicker). --}}
    <script>if (localStorage.getItem('sp.sidebar') === 'collapsed') document.documentElement.classList.add('sidebar-collapsed');</script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css" rel="stylesheet">

    {{-- Design system first, so page styles pushed below can refine it.
         (Tailwind / resources/css/app.css is only for the Breeze guest layout: its preflight and
         .collapse/.container utilities break Bootstrap.) --}}
    @vite([
        'resources/css/app-layout.css',
        'resources/css/messages.css',
        'resources/js/app.js',
    ])
    @stack('styles')
    <link rel="icon" type="image/png" href="{{ asset('images/sureprice.png') }}" />
</head>
<body class="@hasSection('is_landing') is-landing @else is-app @endif">
    @if(View::hasSection('is_landing'))
        <nav class="navbar navbar-expand-lg landing-navbar">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="/">
                    <img src="{{ asset('images/sureprice_logo.png') }}" alt="SurePrice" class="landing-logo">
                </a>
                <div class="ms-auto d-flex align-items-center gap-2">
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> {{ auth()->user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route(auth()->user()->homeRoute()) }}"><i class="fas fa-gauge me-2"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('contracts.index') }}"><i class="fas fa-file-contract me-2"></i>My Contracts</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">@csrf
                                        <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login.form') }}" class="btn btn-outline-light">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-light">Sign Up</a>
                    @endauth
                </div>
            </div>
        </nav>
        <main>
            @yield('content')
        </main>
    @else
        <div class="app-container" id="appContainer">
            <!-- Mobile top bar -->
            <header class="mobile-topbar d-md-none">
                <img src="{{ asset('images/sureprice_logo.png') }}" alt="SurePrice" class="mobile-logo">
                <button class="mobile-menu-btn" type="button" data-mobile-menu-toggle aria-label="Open menu" aria-controls="mobileMenu">
                    <i class="fas fa-bars"></i>
                </button>
            </header>

            <!-- Mobile drawer -->
            <div class="mobile-menu-backdrop" data-mobile-menu-toggle></div>
            <aside class="mobile-menu" id="mobileMenu" aria-label="Navigation">
                <button class="close-btn-mobile" type="button" data-mobile-menu-toggle aria-label="Close menu">
                    <i class="fas fa-times"></i>
                </button>
                <div class="sidebar-inner">
                    @include('include.sidebars._resolve')
                </div>
            </aside>

            <!-- Desktop sidebar -->
            <aside class="left-header d-none d-md-flex" aria-label="Navigation">
                <button class="sidebar-toggle-btn" id="sidebarToggleBtn" type="button" title="Collapse sidebar" aria-label="Collapse sidebar">
                    <i class="fas fa-angles-left" id="sidebarToggleIcon"></i>
                </button>
                <div class="sidebar-inner">
                    @include('include.sidebars._resolve')
                </div>
            </aside>

            <!-- Main content -->
            <main class="content">
                <x-flash />

                @yield('content')
            </main>
        </div>
    @endif

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @stack('scripts')
    @vite(['resources/js/app-layout.js'])
</body>
</html>
