<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Responsive Admin Sidebar</title>
  <!-- Bootstrap & Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
     @vite(['resources/css/header.css'])
        @vite(['resources/js/header.js'])
</head>
<body>

  <!-- Mobile Top Header -->
  <div class="mobile-topbar d-md-none d-flex">
    <div class="d-flex align-items-center gap-2">
      <img src="{{ asset('images/gdc_logo.png') }}" alt="Logo" style="height: 40px;">
      <strong>GEOCON</strong>
    </div>
    <button class="btn btn-success" onclick="toggleMobileMenu()">
      <i class="fas fa-bars"></i>
    </button>
  </div>

  <!-- Mobile Fullscreen Menu -->
  <div class="mobile-menu" id="mobileMenu">
    <div class="logo-close-container d-flex justify-content-between align-items-start w-100 mb-4">
      <div class="text-start d-flex flex-column align-items-center">
        <img src="{{ asset('images/gdc_logo.png') }}" alt="Logo" style="height: 100px;">
        <div style="font-size: 35px; font-weight: bold;">GEOCON</div>
      </div>
      <button class="close-btn-mobile" onclick="toggleMobileMenu()">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <div class="w-100 d-flex flex-column align-items-center mt-2 mobile-nav-buttons">
      <a href="{{ route('admin.dbadmin') }}" class="btn"><i class="fas fa-home me-2"></i>Dashboard</a>
      <a href="{{ route('admin.project') }}" class="btn"><i class="fas fa-file-circle-check"></i>Project Approval</a>
      <a href="{{ route('admin.procurement') }}" class="btn"><i class="fas fa-truck-ramp-box"></i>Procurement Request</a>
    </div>
  </div>

  <!-- Desktop Sidebar -->
  <div class="left-header d-none d-md-flex">
    <div class="logo-title-container">
      <img src="{{ asset('images/gdc_logo.png') }}" alt="Company Logo" class="header-logo">
      <div class="header-title">GEOCON</div>
    </div>

    <div class="separator"></div>

    <div class="profile-section">
      <div class="label">Logged in as</div>
      <div class="username">{{ Auth::user()->name }}</div>
      <i class="fas fa-user-circle profile-icon"></i>
    </div>

    <div class="separator"></div>

    <div class="nav-buttons">
      <a href="{{ route('admin.dbadmin') }}" class="btn"><i class="fas fa-home"></i>Dashboard</a>
      <a href="{{ route('admin.project') }}" class="btn"><i class="fas fa-file-circle-check"></i>Project Approval</a>
      <a href="{{ route('admin.procurement') }}" class="btn"><i class="fas fa-truck-ramp-box"></i>Procurement Request</a>
    </div>

    <div style="margin-top: auto; width: 100%;">
      <div class="separator"></div>
      <div class="nav-buttons">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="btn"><i class="fas fa-sign-out-alt"></i>Logout</button>
        </form>
      </div>
    </div>
  </div>

  @push('styles')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  @endpush

  @push('scripts')
  <script>
  function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobileMenu');
    mobileMenu.classList.toggle('active');
  }
  </script>
  @endpush

</body>
</html>