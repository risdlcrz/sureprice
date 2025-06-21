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
    @if(auth()->check() && auth()->user()->user_type === 'company' && auth()->user()->company && auth()->user()->company->designation === 'client')
        <a href="{{ route('client.dashboard') }}" class="btn">
            <i class="fas fa-home"></i>Dashboard
        </a>
        <a href="{{ route('client.project.procurement') }}" class="btn">
            <i class="fas fa-tasks"></i>Project & Procurement
        </a>
        <a href="{{ route('client.payments') }}" class="btn">
            <i class="fas fa-money-check-alt"></i>Payments
        </a>
        <a href="{{ route('messages.index') }}" class="btn">
            <i class="fas fa-comments"></i>Messages
        </a>
    @elseif(auth()->check() && auth()->user()->user_type === 'company' && auth()->user()->company && auth()->user()->company->designation === 'supplier')
        <a href="{{ route('supplier.dashboard') }}" class="btn">
            <i class="fas fa-home"></i>Dashboard
        </a>
        <a href="{{ route('supplier.materials.index') }}" class="btn">
            <i class="fas fa-boxes"></i>My Materials
        </a>
        <a href="{{ route('supplier.quotations.index') }}" class="btn">
            <i class="fas fa-file-invoice"></i>Quotations
        </a>
        <a href="{{ route('supplier.ranking') }}" class="btn">
            <i class="fas fa-chart-line"></i>Performance
        </a>
        <a href="{{ route('supplier.profile.edit') }}" class="btn">
            <i class="fas fa-user-edit"></i>Edit My Information
        </a>
        <a href="{{ route('messages.index') }}" class="btn">
            <i class="fas fa-comments"></i>Messages
        </a>
    @else
        <a href="{{ route('admin.dbadmin') }}" class="btn">
            <i class="fas fa-home"></i>Dashboard
        </a>
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('information-management.index') }}" class="btn">
            <i class="fas fa-folder-open"></i>Information Management
        </a>
        <a href="{{ route('admin.suppliers.pending-updates') }}" class="btn">
            <i class="fas fa-user-clock"></i>Supplier Profile Updates
        </a>
        @endif
        <a href="{{ route('admin.notification') }}" class="btn">
            <i class="fas fa-bell"></i>Notification Hub
        </a>
        <a href="{{ route('admin.project') }}" class="btn">
            <i class="fas fa-tasks"></i>Project & Procurement
        </a>
        <a href="{{ route('history.dashboard') }}" class="btn">
            <i class="fas fa-history"></i>Project History
        </a>
        <a href="{{ route('admin.analytics') }}" class="btn">
            <i class="fas fa-chart-bar"></i>Analytics
        </a>
        <a href="{{ route('inventory.index') }}" class="btn">
            <i class="fas fa-boxes"></i>Inventory
        </a>
        <a href="{{ route('admin.transactions') }}" class="btn">
            <i class="fas fa-money-check-alt"></i>Transactions
        </a>
        <a href="{{ route('payments.index') }}" class="btn">
            <i class="fas fa-money-check-alt"></i>Payments
        </a>
        <a href="{{ route('messages.index') }}" class="btn">
            <i class="fas fa-comments"></i>Messages
        </a>
    @endif
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