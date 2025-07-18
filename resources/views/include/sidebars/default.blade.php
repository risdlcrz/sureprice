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
                {{ auth()->user()->getDisplayNameAttribute() }}
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
    @if(auth()->check() && auth()->user()->user_type === 'company' && auth()->user()->company && auth()->user()->company->designation === 'client')
        <a href="{{ url('/') }}" class="btn" style="display: flex; align-items: center; gap: 8px;">
            <img src="{{ asset('images/sureprice_logo.png') }}" alt="Home" style="height: 24px; width: 24px;"> <span>Home</span>
        </a>
        <a href="{{ route('client.dashboard') }}" class="btn">
            <i class="fas fa-home"></i>Dashboard
        </a>
        <a href="{{ route('client.project.procurement') }}" class="btn">
            <i class="fas fa-tasks"></i>Project & Procurement
        </a>
        <a href="{{ route('client.payments') }}" class="btn">
            <i class="fas fa-money-check-alt"></i>Payments
        </a>
        <a href="{{ route('client.quotation.index') }}" class="btn">
            <i class="fas fa-file-alt"></i>View Quotation
        </a>
        <a href="{{ route('messages.index') }}" class="btn position-relative">
            <i class="fas fa-comments position-relative">
                @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                    <span class="position-absolute badge rounded-pill bg-danger" style="font-size:0.8em; top: -6px; right: -6px;">{{ $unreadMessagesCount }}</span>
                @endif
            </i>
            Messages
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
        <a href="{{ route('supplier.notification') }}" class="btn position-relative d-flex align-items-center">
            <span class="icon-badge-wrapper position-relative" style="display: inline-block; width: 28px;">
                <i class="fas fa-bell"></i>
                @if(isset($globalUnreadCount) && $globalUnreadCount > 0)
                    <span class="notification-badge position-absolute badge rounded-pill bg-danger" style="font-size:0.8em; top: -6px; right: -6px;">{{ $globalUnreadCount }}</span>
                @endif
            </span>
            <span class="ms-2">Notification Center</span>
        </a>
        <a href="{{ route('messages.index') }}" class="btn position-relative d-flex align-items-center">
            <span class="icon-badge-wrapper position-relative" style="display: inline-block; width: 28px;">
                <i class="fas fa-comments"></i>
                @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                    <span class="messages-badge position-absolute badge rounded-pill bg-danger" style="font-size:0.8em; top: -6px; right: -6px;">{{ $unreadMessagesCount }}</span>
                @endif
            </span>
            <span class="ms-2">Messages</span>
        </a>
    @else
        @if(auth()->check() && auth()->user()->role === 'manager')
            <a href="{{ route('manager.dashboard') }}" class="btn">
                <i class="fas fa-home"></i>Dashboard
            </a>
            <a href="{{ route('manager.notification') }}" class="btn position-relative d-flex align-items-center">
                <span class="icon-badge-wrapper position-relative" style="display: inline-block; width: 28px;">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge position-absolute badge rounded-pill bg-danger" style="font-size:0.8em; top: -6px; right: -6px; display: none;">0</span>
                </span>
                <span class="ms-2">Notification Center</span>
            </a>
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
            <a href="{{ route('messages.index') }}" class="btn position-relative d-flex align-items-center">
                <span class="icon-badge-wrapper position-relative" style="display: inline-block; width: 28px;">
                    <i class="fas fa-comments"></i>
                    <span class="messages-badge position-absolute badge rounded-pill bg-danger" style="font-size:0.8em; top: -6px; right: -6px; display: none;">0</span>
                </span>
                <span class="ms-2">Messages</span>
                @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.8em;">{{ $unreadMessagesCount }}</span>
                @endif
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
        <a href="{{ route('admin.notification') }}" class="btn position-relative d-flex align-items-center">
                <span class="icon-badge-wrapper position-relative" style="display: inline-block; width: 28px;">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge position-absolute badge rounded-pill bg-danger" style="font-size:0.8em; top: -6px; right: -6px; display: none;">0</span>
                </span>
                <span class="ms-2">Notification Center</span>
            @if(isset($globalUnreadCount) && $globalUnreadCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.8em;">{{ $globalUnreadCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.analytics') }}" class="btn">
            <i class="fas fa-chart-bar"></i>Analytics
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

<!-- Logout Button -->
<div class="logout-container">
    <!-- REMOVE THIS BLOCK COMPLETELY -->
</div> 

@push('scripts')
<script>
function updateNotificationBadge() {
    fetch('/api/unread-notifications-count')
        .then(response => response.json())
        .then(data => {
            document.querySelectorAll('.notification-badge').forEach(badge => {
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = '';
                } else {
                    badge.textContent = '0';
                    badge.style.display = 'none';
                }
            });
        });
}
function updateMessagesBadge() {
    fetch('/api/unread-messages-count')
        .then(response => response.json())
        .then(data => {
            document.querySelectorAll('.messages-badge').forEach(badge => {
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = '';
                } else {
                    badge.textContent = '0';
                    badge.style.display = 'none';
                }
            });
        });
}
setInterval(updateNotificationBadge, 10000); // Poll every 10 seconds
setInterval(updateMessagesBadge, 10000); // Poll every 10 seconds
// Initial load
updateNotificationBadge();
updateMessagesBadge();
</script>
@endpush 