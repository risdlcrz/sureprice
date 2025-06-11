<!-- Logo and Title -->
<div class="logo-title-container">
    <img src="{{ asset('images/gdc_logo.png') }}" alt="Company Logo" class="header-logo">
    <div class="header-title">GEOCON</div>
</div>

<!-- Profile Section -->
<div class="profile-container">
    <div class="label">Logged in as</div>
    <div class="username">Main Admin</div>
    <i class="fas fa-user-circle profile-icon"></i>
</div>

<!-- Back Button -->
<div class="nav-buttons mb-3">
    <a href="{{ route('admin.dbadmin') }}" class="btn btn-back">
        <i class="fas fa-arrow-left"></i>Back to Main Dashboard
    </a>
</div>

<!-- Navigation Links -->
<div class="nav-buttons">
    <a href="{{ route('admin.history') }}" class="btn {{ request()->routeIs('admin.history') ? 'active' : '' }}">
        <i class="fas fa-home"></i>History Dashboard
    </a>
    <a href="{{ route('admin.past-transactions') }}" class="btn {{ request()->routeIs('admin.past-transactions') ? 'active' : '' }}">
        <i class="fas fa-folder-open"></i>Past Transactions
    </a>
    <a href="{{ route('admin.supplier-performance') }}" class="btn {{ request()->routeIs('admin.supplier-performance') ? 'active' : '' }}">
        <i class="fas fa-chart-line"></i>Supplier Performance
    </a>
    <a href="{{ route('admin.procurement-logs') }}" class="btn {{ request()->routeIs('admin.procurement-logs') ? 'active' : '' }}">
        <i class="fas fa-boxes-stacked"></i>Procurement Logs
    </a>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('payments.index') }}">
            <i class="fas fa-money-check-alt"></i> Payments
        </a>
    </li>
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