@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-center page-title">Admin Dashboard</h1>

<div class="row row-cols-1 row-cols-md-3 g-4">
    <!-- Information Management -->
    <div class="col">
        <div class="card" onclick="window.location.href='{{ route('information-management.index') }}';" style="cursor:pointer;">
            <img src="{{ asset('images/imagecard1.jpg') }}" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}';" alt="Information Management" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">Information Management</h5>
            </div>
        </div>
    </div>
    <!-- Notification Hub -->
    <div class="col">
        <div class="card" onclick="window.location.href='{{ route('notifications.index') }}';" style="cursor:pointer;">
            <img src="{{ asset('images/imagecard2.jpg') }}" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}';" alt="Notification Hub" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">Centralized Notification Hub</h5>
            </div>
        </div>
    </div>
    <!-- Analytics -->
    <div class="col">
        <div class="card" onclick="window.location.href='{{ route('admin.analytics') }}';" style="cursor:pointer;">
            <img src="{{ asset('images/imagecard5.jpg') }}" alt="Analytics" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">Analytics and Recommendations</h5>
            </div>
        </div>
    </div>
</div>

@endsection

