@extends('layouts.app')
@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Notifications @if(isset($unreadCount) && $unreadCount > 0)<span class="badge bg-danger">{{ $unreadCount }}</span>@endif</h4>
        </div>
        <div class="card-body">
            @if($notifications->isEmpty())
                <p>No notifications found.</p>
            @else
                <ul class="list-group list-group-flush">
                    @foreach($notifications as $notification)
                        <li class="list-group-item d-flex justify-content-between align-items-center @if(is_null($notification->read_at)) fw-bold @endif">
                            <div>
                                <div>{{ $notification->data['title'] ?? $notification->type }}</div>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                <div>{{ $notification->data['message'] ?? '' }}</div>
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
@endsection 