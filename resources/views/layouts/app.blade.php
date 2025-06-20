<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'GEOCON') }}</title>

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
    @vite(['resources/css/header.css', 'resources/css/login.css', 'resources/js/app.js'])
    
    <!-- Additional Styles -->
    @stack('styles')

    @vite('resources/css/app.css')

    @vite(['resources/css/messages.css', 'resources/js/app.js'])

    <!-- Add Inter Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            background: #f7f9fb;
            color: #222;
        }
        .app-container {
            min-height: 100vh;
            display: flex;
            flex-direction: row;
        }
        .left-header {
            background: #1b5e20;
            min-width: 220px;
            max-width: 260px;
            padding: 2rem 1rem 2rem 1rem;
            border-radius: 0 2rem 2rem 0;
            box-shadow: 2px 0 16px 0 rgba(0,0,0,0.04);
        }
        .left-header .nav-link, .left-header button, .left-header a {
            color: #fff;
            font-size: 1.1rem;
            border-radius: 0.75rem;
            margin-bottom: 0.5rem;
            padding: 0.75rem 1rem;
            transition: background 0.2s;
        }
        .left-header .nav-link.active, .left-header .nav-link:hover, .left-header button:hover, .left-header a:hover {
            background: #388e3c;
            color: #fff;
        }
        .content {
            flex: 1;
            padding: 2.5rem 2rem 2rem 2rem;
            background: #f7f9fb;
            border-radius: 2rem 0 0 2rem;
            min-height: 100vh;
        }
        .navbar, .mobile-topbar {
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            box-shadow: 0 2px 8px 0 rgba(0,0,0,0.03);
            border-radius: 0 0 1rem 1rem;
            padding: 1rem 2rem;
            margin-bottom: 2rem;
        }
        .navbar .logo, .mobile-topbar .logo {
            font-weight: 600;
            font-size: 1.5rem;
            color: #1b5e20;
        }
        .btn, .btn-primary, .btn-secondary {
            border-radius: 2rem !important;
            font-weight: 500;
            font-size: 1rem;
            padding: 0.5rem 1.5rem;
        }
        .card, .modal-content {
            border-radius: 1.5rem;
            box-shadow: 0 2px 16px 0 rgba(0,0,0,0.06);
            border: none;
        }
        .modal-header, .modal-footer {
            border: none;
            background: #f7f9fb;
            border-radius: 1.5rem 1.5rem 0 0;
        }
        .form-label {
            font-weight: 500;
            color: #1b5e20;
        }
        .form-control, .form-select {
            border-radius: 0.75rem;
            border: 1px solid #cfd8dc;
            font-size: 1rem;
            padding: 0.75rem 1rem;
        }
        .alert {
            border-radius: 1rem;
            font-size: 1rem;
        }
        @media (max-width: 900px) {
            .app-container { flex-direction: column; }
            .left-header { max-width: 100vw; min-width: 0; border-radius: 0; }
            .content { border-radius: 0; padding: 1rem; }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Mobile Top Header -->
        <div class="mobile-topbar d-md-none d-flex">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ Vite::asset('resources/images/gdc_logo.png') }}" alt="Logo" style="height: 40px;">
                <strong>GEOCON</strong>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Additional Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @stack('scripts')

    <script>
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('active');
        }

        // Auto-hide alerts after 5 seconds, except those with .alert-static
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert:not(.alert-static)');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });

        window.PUSHER_APP_KEY = "{{ env('PUSHER_APP_KEY') }}";
        window.PUSHER_APP_CLUSTER = "{{ env('PUSHER_APP_CLUSTER') }}";
    </script>

    @auth
        @if(Auth::user()->user_type === 'admin')
            {{-- Removed Transactions link from navbar --}}
        @endif
    @endauth
</body>
</html>
