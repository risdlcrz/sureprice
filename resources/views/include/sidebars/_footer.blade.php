{{-- Sidebar footer: logout. A form (not a link + #id) because the sidebar is rendered twice (desktop + mobile). --}}
@auth
<div class="sidebar-bottom">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn">
            <i class="fas fa-sign-out-alt"></i>Logout
        </button>
    </form>
</div>
@endauth
