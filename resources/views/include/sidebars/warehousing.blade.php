<!-- Logo and Title -->
<div class="logo-title-container">
    <img src="{{ asset('images/sureprice_logo.png') }}" alt="SurePrice Logo" class="header-logo">
</div>

<!-- Profile Section -->
<div class="profile-container">
    <div class="label">Logged in as</div>
    @if(auth()->check())
        <div class="username">{{ auth()->user() ? auth()->user()->getDisplayNameAttribute() : 'N/A' }}</div>
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
    <a href="{{ route('warehouse.material-requests.index') }}" class="btn {{ request()->routeIs('warehouse.material-requests.*') ? 'active' : '' }}">
        <i class="fas fa-clipboard-check"></i>Material Requests
    </a>
    <a href="{{ route('warehouse.deliveries.index') }}" class="btn {{ request()->routeIs('warehouse.deliveries.*') ? 'active' : '' }}">
        <i class="fas fa-truck"></i>Deliveries
    </a>
    <a href="{{ route('warehouse.reports.index') }}" class="btn {{ request()->routeIs('warehouse.reports.*') ? 'active' : '' }}">
        <i class="fas fa-file-alt"></i>Reports
    </a>
    <a href="{{ route('messages.index') }}" class="btn position-relative">
        <i class="fas fa-comments"></i>Messages
        @if(isset($globalUnreadCount) && $globalUnreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.8em;">{{ $globalUnreadCount }}</span>
        @endif
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