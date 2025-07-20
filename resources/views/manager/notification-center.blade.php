@extends('layouts.app')
@section('content')
<div class="container mt-4">
    <div class="card shadow-sm rounded-4 border-0">
        <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 rounded-top-4" style="padding: 1.5rem 2rem 1rem 2rem;">
            <h4 class="mb-0 fw-bold text-success">
                <i class="fas fa-bell me-2"></i>Notifications
                @if(isset($unreadCount) && $unreadCount > 0)
                    <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
                @endif
            </h4>
            <div>
                <form method="POST" action="{{ route('manager.notifications.markAllAsRead') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-sm me-2">Mark All as Read</button>
                </form>
                <form method="POST" action="{{ route('manager.notifications.clearRead') }}" class="d-inline" id="clearReadForm">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">Clear Read Notifications</button>
                </form>
            </div>
        </div>
        <div class="card-body p-4 bg-light rounded-bottom-4">
            @if($notifications->isEmpty())
                <div class="d-flex flex-column align-items-center justify-content-center py-5">
                    <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                    <span class="fw-semibold text-muted" style="font-size:1.2em;">No notifications found.</span>
                </div>
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
@push('styles')
<link href="{{ asset('resources/css/manager-notification-center.css') }}" rel="stylesheet" />
@endpush
@push('scripts')
<script>
window.csrfToken = '{{ csrf_token() }}';
</script>
<script src="{{ asset('resources/js/manager-notification-center.js') }}"></script>
@endpush
@endsection 