@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 rounded-top-4" style="padding: 1.5rem 2rem 1rem 2rem;">
                    <h4 class="mb-0 fw-bold text-success">
                        <i class="fas fa-bell me-2"></i>Notifications
                        @if(isset($unreadCount) && $unreadCount > 0)
                            <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
                        @endif
                    </h4>
                    <div>
                        <form method="POST" action="{{ route('supplier.notifications.markAllAsRead') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary btn-sm me-2">Mark All as Read</button>
                        </form>
                        <form method="POST" action="{{ route('supplier.notifications.clearRead') }}" class="d-inline" id="clearReadForm">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">Clear Read Notifications</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if($notifications->isEmpty())
                        <p>No notifications found.</p>
                    @else
                        <ul class="list-group list-group-flush" id="notificationList">
                            @foreach($notifications as $notification)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-3 notification-item @if(is_null($notification->read_at)) fw-bold bg-white @else bg-light @endif" style="border-radius: 1rem; margin-bottom: 0.5rem; border: none; cursor:pointer;" data-id="{{ $notification->id }}" data-read="{{ $notification->read_at ? '1' : '0' }}">
                                    <div>
                                        <div class="mb-1">
                                            <i class="fas fa-info-circle me-2 text-{{ is_null($notification->read_at) ? 'warning' : 'secondary' }}"></i>
                                            {{ $notification->data['title'] ?? $notification->type }}
                                        </div>
                                        <div class="mb-1 small text-muted">{{ $notification->created_at->diffForHumans() }}</div>
                                        <div class="mb-2">{{ $notification->data['message'] ?? '' }}</div>
                                        @if(isset($notification->data['link']))
                                            <a href="{{ $notification->data['link'] }}" class="badge bg-primary text-white">View Details</a>
                                        @endif
                                    </div>
                                    @if(is_null($notification->read_at))
                                        <span class="badge bg-warning text-dark">Unread</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark notification as read on click
    document.querySelectorAll('.notification-item').forEach(function(item) {
        item.addEventListener('click', function(e) {
            var id = this.getAttribute('data-id');
            var isRead = this.getAttribute('data-read') === '1';
            if (!isRead) {
                fetch('/supplier/notifications/' + id + '/mark-as-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                }).then(res => {
                    if (res.ok) {
                        this.classList.remove('fw-bold', 'bg-white');
                        this.classList.add('bg-light');
                        this.setAttribute('data-read', '1');
                        var badge = this.querySelector('.badge.bg-warning');
                        if (badge) badge.remove();
                    }
                });
            }
        });
    });
    // Clear read notifications
    document.getElementById('clearReadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        }).then(res => res.json()).then(data => {
            if (data.success) {
                document.querySelectorAll('.notification-item[data-read="1"]').forEach(function(item) {
                    item.remove();
                });
            }
        });
    });
});
</script>
@endpush 