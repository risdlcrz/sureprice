@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4 text-gray-800">Procurement Notification Hub</h1>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Your Notifications</h6>
        </div>
        <div class="card-body">
            @if($notifications->isEmpty())
                <p>No new notifications.</p>
            @else
                <div class="list-group">
                    @foreach($notifications as $notification)
                        <div class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ $notification->title }}</h5>
                                <small>{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">{{ $notification->message }}</p>
                            @if($notification->link)
                                <a href="{{ $notification->link }}" class="badge bg-primary text-white">View Details</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 