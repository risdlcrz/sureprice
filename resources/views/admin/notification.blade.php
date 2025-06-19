@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4 text-gray-800">Centralized Notification Hub</h1>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Notifications</h6>
        </div>
        <div class="card-body">
            @if(isset($activities) && $activities->isNotEmpty())
                <div class="list-group">
                    @foreach($activities as $activity)
                        <div class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">
                                    {{ $activity->user ? $activity->user->getDisplayNameAttribute() : 'System' }}
                                    <span class="badge bg-{{ $activity->action_color }} ms-2">{{ ucfirst($activity->action) }}</span>
                                </h5>
                                <small>{{ $activity->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">{{ $activity->description }}</p>
                            @if($activity->model_type && $activity->model_id)
                                <small>Related: {{ class_basename($activity->model_type) }} #{{ $activity->model_id }}</small>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p>No notifications found.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }
    .list-group-item {
        border-radius: 10px;
        margin-bottom: 10px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        background: #fff;
        border: 1px solid #e9ecef;
    }
    .badge {
        font-size: 0.85em;
        padding: 0.5em 0.8em;
    }
    .d-flex.w-100.justify-content-between {
        align-items: center;
    }
    .mb-1 {
        margin-bottom: 0.5rem !important;
    }
</style>
@endpush 