@include('include.sidebars._brand')

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
    @include('include.sidebars._inbox')
</div>

@include('include.sidebars._footer')
