{{-- Notifications (+ optional Messages) links with live unread counters. --}}
<a href="{{ route('notifications.index') }}" class="btn d-flex align-items-center">
    <i class="fas fa-bell"></i>Notifications<x-nav-count kind="notifications" />
</a>
@if($messages ?? true)
    <a href="{{ route('messages.index') }}" class="btn d-flex align-items-center">
        <i class="fas fa-comments"></i>Messages<x-nav-count kind="messages" />
    </a>
@endif
