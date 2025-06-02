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
    <a href="{{ route('admin.analytics') }}" class="btn {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
        <i class="fas fa-home"></i>Analytics Dashboard
    </a>
    <a href="{{ route('admin.purchase-order') }}" class="btn {{ request()->routeIs('admin.purchase-order') ? 'active' : '' }}">
        <i class="fas fa-file-invoice-dollar"></i>Active Purchase Order
    </a>
    <a href="{{ route('admin.budget-allocation') }}" class="btn {{ request()->routeIs('admin.budget-allocation') ? 'active' : '' }}">
        <i class="fas fa-coins"></i>Budget Allocation
    </a>
    <a href="{{ route('admin.supplier-rankings') }}" class="btn {{ request()->routeIs('admin.supplier-rankings') ? 'active' : '' }}">
        <i class="fas fa-ranking-star"></i>Supplier Rankings
    </a>
    <a href="{{ route('admin.price-analysis') }}" class="btn {{ request()->routeIs('admin.price-analysis') ? 'active' : '' }}">
        <i class="fas fa-chart-line"></i>Price Analysis
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