<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'GEOCON') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    
    <!-- Vite Assets -->
    @vite(['resources/css/header.css'])
    @vite(['resources/js/header.js'])
    
    <!-- Additional Styles -->
    @stack('styles')
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
                @elseif(request()->is('admin/history*'))
                    @include('include.sidebars.history')
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
            @elseif(request()->is('admin/history*'))
                @include('include.sidebars.history')
            @else
                @include('include.sidebars.default')
            @endif
        </div>

        <!-- Main Content -->
        <div class="content">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Additional Scripts -->
    @stack('scripts')

    <script>
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('active');
        }
    </script>

    @auth
        @if(Auth::user()->user_type === 'admin')
            {{-- Removed Transactions link from navbar --}}
        @endif
    @endauth
</body>
</html>
