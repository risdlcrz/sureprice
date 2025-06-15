<!-- Logo and Title -->
<div class="logo-title-container">
    <img src="{{ asset('images/gdc_logo.png') }}" alt="Company Logo" class="header-logo">
    <div class="header-title">GEOCON</div>
</div>

<!-- Profile Section -->
<div class="profile-container">
    <div class="label">Logged in as</div>
    @if(auth()->check())
        <div class="username">{{ auth()->user()->getDisplayNameAttribute() }}</div>
        <div class="role">{{ ucfirst(auth()->user()->role) }}</div>
    @else
        <div class="username">Guest</div>
    @endif
    <i class="fas fa-user-circle profile-icon"></i>
</div>

<!-- Navigation Links -->
<div class="nav-buttons">
    <a href="{{ route('procurement.dashboard') }}" class="btn">
        <i class="fas fa-home"></i>Dashboard
    </a>
    <a href="{{ route('procurement.projects') }}" class="btn">
        <i class="fas fa-tasks"></i>Project & Procurement
    </a>
    <a href="{{ route('procurement.inventory.index') }}" class="btn">
        <i class="fas fa-boxes"></i>Inventory
    </a>
    <a href="{{ route('procurement.history') }}" class="btn">
        <i class="fas fa-history"></i>Project History
    </a>
    <a href="{{ route('procurement.analytics') }}" class="btn">
        <i class="fas fa-chart-bar"></i>Analytics
    </a>
    <a href="{{ route('procurement.notification') }}" class="btn">
        <i class="fas fa-bell"></i>Notification Hub
    </a>
</div>

<!-- Logout Button -->
<div class="logout-container">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn">
            <i class="fas fa-sign-out-alt"></i>Logout
        </button>
    </form>
</div> 