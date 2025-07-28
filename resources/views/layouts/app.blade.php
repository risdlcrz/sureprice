<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SurePrice')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css" rel="stylesheet">
    
    <!-- Consolidated Vite assets - load app.js only once -->
    @vite([
        'resources/css/app.css',
        'resources/css/header.css',
        'resources/css/login.css',
        'resources/css/messages.css',
        'resources/js/app.js',
    ])
    <!-- Additional Styles -->
    @stack('styles')

    <!-- Add Inter Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app-layout.css'])
    <link rel="icon" type="image/png" href="{{ asset('images/sureprice.png') }}" />
</head>
<body>
    @if(View::hasSection('is_landing'))
        <!-- Landing Page Navbar -->
        <nav class="navbar navbar-expand-lg landing-navbar shadow-sm py-3">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="/">
                    <img src="{{ asset('images/sureprice_logo.png') }}" alt="SurePrice Logo" style="height: 64px;">
                </a>
                <div class="ms-auto d-flex align-items-center gap-3">
                    @if(auth()->check())
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> {{ auth()->user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('contracts.index') }}">My Contracts</a></li>
                                <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
                            </ul>
                        </div>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    @else
                        <a href="{{ route('login.form') }}" class="btn btn-outline-light me-2">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-light text-success fw-bold">Sign Up</a>
                    @endif
                </div>
            </div>
        </nav>
        <main>
            @yield('content')
        </main>
    @else
        <div class="app-container" id="appContainer">
            <!-- Sidebar Toggle Button -->
            <button class="sidebar-toggle-btn d-none d-md-flex" id="sidebarToggleBtn" type="button" title="Toggle Sidebar">
                <i class="fas fa-angle-double-left" id="sidebarToggleIcon"></i>
            </button>

            <!-- Mobile Top Header -->
            <div class="mobile-topbar d-md-none d-flex">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('images/sureprice_logo.png') }}" alt="SurePrice Logo" style="height: 40px;">
                </div>
                <button class="btn btn-success" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <!-- Mobile Fullscreen Menu -->
            <div class="mobile-menu" id="mobileMenu">
                <div class="logo-close-container d-flex justify-content-between align-items-start w-100 mb-4">
                    <button class="close-btn-mobile" onclick="toggleMobileMenu()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="w-100 d-flex flex-column align-items-center mt-2 mobile-nav-buttons">
                    @if(request()->is('admin/project*'))
                        @include('include.sidebars.project')
                    @elseif(request()->is('admin/analytics*'))
                        @include('include.sidebars.analytics')
                    @elseif(auth()->check() && auth()->user()->role === 'warehousing')
                        @include('include.sidebars.warehousing')
                    @elseif(auth()->check() && auth()->user()->user_type === 'employee' && auth()->user()->role === 'procurement')
                        @include('include.sidebars.procurement')
                    @else
                        @include('include.sidebars.default')
                    @endif
                </div>
            </div>

            <!-- Desktop Sidebar -->
            <div class="left-header d-none d-md-flex">
                @if(request()->is('admin/project*'))
                    @include('include.sidebars.project')
                @elseif(request()->is('admin/analytics*'))
                    @include('include.sidebars.analytics')
                @elseif(auth()->check() && auth()->user()->role === 'warehousing')
                    @include('include.sidebars.warehousing')
                @elseif(auth()->check() && auth()->user()->user_type === 'employee' && auth()->user()->role === 'procurement')
                    @include('include.sidebars.procurement')
                @else
                    @include('include.sidebars.default')
                @endif
            </div>

            <!-- Main Content -->
            <div class="content">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    @endif

    <!-- jQuery (if needed for legacy code) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @stack('scripts')
    {{-- @vite(['resources/js/contracts-show.js']) --}}
    @vite(['resources/js/app-layout.js'])

    @auth
        @if(Auth::user()->user_type === 'admin')
            {{-- Removed Transactions link from navbar --}}
        @endif
    @endauth
</body>
</html>
