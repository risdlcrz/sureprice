{{-- Sidebar footer: account settings + logout. A form (not a link + #id) because the sidebar is rendered twice (desktop + mobile). --}}
@auth
<div class="sidebar-bottom">
    <a href="{{ route('account.edit') }}" class="btn">
        <i class="fas fa-user-cog"></i>Account settings
    </a>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn">
            <i class="fas fa-sign-out-alt"></i>Logout
        </button>
    </form>
</div>
@endauth
