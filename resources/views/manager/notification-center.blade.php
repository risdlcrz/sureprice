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
        </div>
        <div class="card-body p-4 bg-light rounded-bottom-4">
            @if($notifications->isEmpty())
                <div class="d-flex flex-column align-items-center justify-content-center py-5">
                    <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                    <span class="fw-semibold text-muted" style="font-size:1.2em;">No notifications found.</span>
                </div>
            @else
                <ul class="list-group list-group-flush">
                    @foreach($notifications as $notification)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-3 @if(is_null($notification->read_at)) fw-bold bg-white @else bg-light @endif" style="border-radius: 1rem; margin-bottom: 0.5rem; border: none;">
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
<style>
.card {
    border-radius: 1.25rem;
    box-shadow: 0 4px 24px rgba(44,62,80,0.08), 0 1.5px 6px rgba(44,62,80,0.04);
    border: none;
    margin-bottom: 2rem;
}
.card-header {
    background: #fff;
    border-radius: 1.25rem 1.25rem 0 0;
    border-bottom: none;
    padding: 1.5rem 2rem 1rem 2rem;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 1rem;
}
.card-body {
    background: #f8fafc;
    border-radius: 0 0 1.25rem 1.25rem;
}
.list-group-item {
    border: none;
    border-radius: 1rem !important;
    margin-bottom: 0.5rem;
    background: #fff;
    transition: box-shadow 0.2s;
}
.list-group-item.bg-light {
    background: #f8fafc !important;
}
.list-group-item.bg-white {
    background: #fff !important;
    box-shadow: 0 2px 8px #38b6ff11;
}
.list-group-item:hover {
    box-shadow: 0 4px 16px #38b6ff22;
}
</style>
@endpush
@endsection 