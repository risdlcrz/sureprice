{{-- Sidebar header: logo + signed-in user card. Optional $sidebarName / $sidebarRole override the defaults
     (prefixed so page variables such as $role or $name don't leak in). --}}
@php
    $sbUser = auth()->user();
    $sbName = $sidebarName ?? ($sbUser ? match ($sbUser->role) {
        'admin' => 'Admin',
        'manager' => 'Manager',
        default => trim((string) $sbUser->display_name) ?: $sbUser->name,
    } : 'Guest');
    $sbRole = $sidebarRole ?? ($sbUser ? ($sbUser->user_type === 'company'
        ? ucfirst(optional($sbUser->company)->designation ?? 'company')
        : ucfirst((string) $sbUser->role)) : null);
    if ($sbRole && strcasecmp($sbRole, $sbName) === 0) {
        $sbRole = $sbUser?->role === 'manager' ? 'Management' : 'Administrator';
    }
@endphp
<a href="{{ $sbUser ? route($sbUser->homeRoute()) : url('/') }}" class="sidebar-brand">
    <img src="{{ asset('images/sureprice_logo.png') }}" alt="SurePrice" class="header-logo">
</a>

<{{ $sbUser ? 'a' : 'div' }} @if($sbUser) href="{{ route('account.edit') }}" title="Account settings" @endif class="profile-container">
    <x-avatar :user="$sbUser" :name="$sbName" class="profile-avatar" />
    <div class="profile-text">
        <div class="username" title="{{ $sbName }}">{{ $sbName }}</div>
        @if($sbRole)<div class="role">{{ $sbRole }}</div>@endif
    </div>
</{{ $sbUser ? 'a' : 'div' }}>
