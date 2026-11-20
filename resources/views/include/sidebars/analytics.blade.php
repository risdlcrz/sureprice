@include('include.sidebars._brand', ['sidebarRole' => 'Analytics'])

<!-- Back Button -->
<div class="nav-buttons nav-buttons--back">
    <a href="{{ route('admin.dbadmin') }}" class="btn btn-back">
        <i class="fas fa-arrow-left"></i>Back to Main Dashboard
    </a>
</div>

<!-- Navigation Links -->
<div class="nav-buttons">
    <a href="{{ route('admin.analytics') }}" class="btn {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
        <i class="fas fa-home"></i>Analytics Dashboard
    </a>
    <a href="{{ route('purchase-orders.index') }}" class="btn {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
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
    <a href="{{ route('payments.index') }}" class="btn {{ request()->routeIs('payments.*') ? 'active' : '' }}">
        <i class="fas fa-money-check-alt"></i>Payments
    </a>
    @include('include.sidebars._inbox')
</div>

@include('include.sidebars._footer')
