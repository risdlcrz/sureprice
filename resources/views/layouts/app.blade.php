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

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>

    @auth
        @if(Auth::user()->user_type === 'admin')
            {{-- Removed Transactions link from navbar --}}
        @endif
    @endauth

    <!-- Messenger Floating Button and Popup Chat -->
    <style>
        #messenger-fab {
            position: fixed;
            bottom: 32px;
            right: 32px;
            z-index: 1050;
            background: #2563eb;
            color: #fff;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            cursor: pointer;
            transition: background 0.2s;
        }
        #messenger-fab:hover {
            background: #1d4ed8;
        }
        #messenger-popup {
            position: fixed;
            bottom: 100px;
            right: 32px;
            width: 380px;
            height: 540px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.25);
            z-index: 1051;
            display: none;
            flex-direction: column;
            overflow: hidden;
        }
        #messenger-popup-header {
            background: #2563eb;
            color: #fff;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        #messenger-popup iframe {
            border: none;
            width: 100%;
            height: 100%;
        }
        @media (max-width: 500px) {
            #messenger-popup {
                width: 98vw;
                right: 1vw;
                height: 80vh;
                bottom: 80px;
            }
        }
    </style>
    <div id="messenger-fab" title="Open Messenger">
        <i class="fab fa-facebook-messenger fa-2x"></i>
    </div>
    <div id="messenger-popup">
        <div id="messenger-popup-header">
            <span>Messenger</span>
            <button id="messenger-popup-close" class="btn btn-sm btn-light" style="color:#2563eb;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <iframe src="{{ route('chat.index', ['popup' => 1]) }}" allow="camera; microphone"></iframe>
    </div>
    <script>
        const fab = document.getElementById('messenger-fab');
        const popup = document.getElementById('messenger-popup');
        const closeBtn = document.getElementById('messenger-popup-close');
        const iframe = popup.querySelector('iframe');

        fab.addEventListener('click', () => {
            popup.style.display = 'flex';
            fab.style.display = 'none';
        });

        closeBtn.addEventListener('click', () => {
            popup.style.display = 'none';
            fab.style.display = 'flex';
        });

        // Listen for messages from the iframe
        window.addEventListener('message', function(event) {
            if (event.data === 'enlarge-chat') {
                // Open chat in new tab
                window.open('{{ route('chat.index') }}', '_blank');
                // Close the popup
                popup.style.display = 'none';
                fab.style.display = 'flex';
            }
        });
    </script>
</body>
</html>
