<div class="logo-title-container">
    <img src="{{ asset('images/sureprice_logo.png') }}" alt="SurePrice Logo" class="header-logo">
</div>

<!-- Profile Section -->
<div class="profile-container">
    <div class="label">Logged in as</div>
    @if(auth()->check())
        <div class="username">
            @if(auth()->user()->role === 'manager')
                Manager
            @elseif(auth()->user()->role === 'admin')
                Admin
            @else
                {{ auth()->user() ? auth()->user()->getDisplayNameAttribute() : 'N/A' }}
            @endif
        </div>
        <div class="role">{{ ucfirst(auth()->user()->role) }}</div>
    @else
        <div class="username">Guest</div>
    @endif
    <i class="fas fa-user-circle profile-icon"></i>
</div>

<!-- Navigation Links -->
<div class="nav-buttons">
    @if(!auth()->check())
        <a href="{{ url('/') }}" class="btn" style="display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-home"></i> <span>Home</span>
        </a>
        <a href="{{ route('client.quotation.create') }}" class="btn" style="display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-file-alt"></i> <span>Request Quotation</span>
        </a>
        <a href="{{ route('login.form') }}" class="btn" style="display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-sign-in-alt"></i> <span>Login</span>
        </a>
        <a href="{{ route('register') }}" class="btn" style="display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-user-plus"></i> <span>Sign up</span>
        </a>
    @elseif(auth()->check() && auth()->user()->user_type === 'company' && (optional(auth()->user()->company)->designation === 'client' || auth()->user()->role === 'client'))
        <a href="{{ url('/') }}" class="btn" style="display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-building"></i> <span>Home</span>
        </a>
        <a href="{{ route('client.quotation.index') }}" class="btn">
            <i class="fas fa-tachometer-alt"></i>Dashboard
        </a>
        <a href="{{ route('client.quotation.create') }}" class="btn">
            <i class="fas fa-tasks"></i>Project & Procurement
        </a>
        <a href="{{ route('contracts.index') }}" class="btn">
            <i class="fas fa-file-contract"></i>My Contract
        </a>
        <a href="{{ route('client.quotation.index') }}" class="btn">
            <i class="fas fa-file-alt"></i>My Quotations
        </a>
        <a href="{{ route('client.payments') }}" class="btn">
            <i class="fas fa-money-check-alt"></i>My Payments
        </a>
        <a href="{{ route('client.feedback.index') }}" class="btn">
            <i class="fas fa-star"></i>My Feedback
        </a>
        @include('include.sidebars._inbox')
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
        <a href="{{ route('supplier.purchase-requests.index') }}" class="btn">
            <i class="fas fa-file-alt"></i>Purchase Requests
        </a>
        <a href="{{ route('supplier.purchase-orders.index') }}" class="btn">
            <i class="fas fa-file-invoice-dollar"></i>Purchase Orders
        </a>
        <a href="{{ route('supplier.ranking') }}" class="btn">
            <i class="fas fa-chart-line"></i>Performance
        </a>
        <a href="{{ route('supplier.profile.edit') }}" class="btn">
            <i class="fas fa-user-edit"></i>Edit My Information
        </a>
        @include('include.sidebars._inbox')
    @else
        @if(auth()->check() && auth()->user()->role === 'manager')
            <a href="{{ route('manager.dashboard') }}" class="btn">
                <i class="fas fa-home"></i>Dashboard
            </a>
            @include('include.sidebars._inbox')
            <a href="{{ route('admin.project') }}" class="btn">
                <i class="fas fa-tasks"></i>Project & Procurement
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
            <a href="{{ route('history.dashboard') }}" class="btn">
                <i class="fas fa-history"></i>Project History
            </a>
            <a href="{{ route('admin.feedback.analytics') }}" class="btn">
                <i class="fas fa-chart-line"></i>Feedback Analytics
            </a>
        @elseif(auth()->check() && auth()->user()->role === 'finance')
            <a href="{{ route('finance.dashboard') }}" class="btn">
                <i class="fas fa-home"></i>Dashboard
            </a>
            <a href="{{ route('finance.payments') }}" class="btn">
                <i class="fas fa-money-check-alt"></i>Payments
            </a>
            <a href="{{ route('finance.transactions') }}" class="btn">
                <i class="fas fa-money-check-alt"></i>Transactions
            </a>
        @else
            <a href="{{ route('admin.dbadmin') }}" class="btn">
                <i class="fas fa-home"></i>Dashboard
            </a>
        @endif
        @if(auth()->check() && auth()->user()->role === 'admin')
        <a href="{{ route('information-management.index') }}" class="btn">
            <i class="fas fa-folder-open"></i>Information Management
        </a>
        @include('include.sidebars._inbox', ['messages' => false])
        <a href="{{ route('admin.analytics') }}" class="btn">
            <i class="fas fa-chart-bar"></i>Analytics
        </a>
        <a href="{{ route('admin.feedback.index') }}" class="btn">
            <i class="fas fa-star"></i>Client Feedback
        </a>
        <a href="{{ route('history.dashboard') }}" class="btn">
            <i class="fas fa-history"></i>Project History
        </a>
        @endif
    @endif
</div>

<div class="sidebar-bottom">
    <hr class="separator">
    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn">
        <i class="fas fa-sign-out-alt"></i>Logout
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</div>

