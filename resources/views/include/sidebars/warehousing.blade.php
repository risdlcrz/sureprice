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
        <div class="role">Warehousing</div>
    @else
        <div class="username">Guest</div>
    @endif
    <i class="fas fa-user-circle profile-icon"></i>
</div>

<!-- Navigation Links -->
<div class="nav-buttons">
    <a href="{{ route('warehouse.dashboard') }}" class="btn {{ request()->routeIs('warehouse.dashboard') ? 'active' : '' }}">
        <i class="fas fa-warehouse"></i>Dashboard
    </a>
    <a href="{{ route('warehouse.inventory.index') }}" class="btn {{ request()->routeIs('warehouse.inventory.*') ? 'active' : '' }}">
        <i class="fas fa-boxes"></i>Inventory
    </a>
    <a href="{{ route('warehouse.deliveries.index') }}" class="btn {{ request()->routeIs('warehouse.deliveries.*') ? 'active' : '' }}">
        <i class="fas fa-truck"></i>Deliveries
    </a>
    <a href="{{ route('warehouse.reports.index') }}" class="btn {{ request()->routeIs('warehouse.reports.*') ? 'active' : '' }}">
        <i class="fas fa-file-alt"></i>Reports
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