@include('include.sidebars._brand')

<!-- Navigation Links -->
<div class="nav-buttons">
    <a href="{{ route('procurement.dashboard') }}" class="btn">
        <i class="fas fa-home"></i>Dashboard
    </a>
    <a href="{{ route('procurement.purchase-requests.index') }}" class="btn">
        <i class="fas fa-shopping-cart"></i>Purchase Requests
    </a>
    <a href="{{ route('procurement.projects.index') }}" class="btn">
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
    @include('include.sidebars._inbox')
</div>

@include('include.sidebars._footer')
