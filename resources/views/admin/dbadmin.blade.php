@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;" >Admin Dashboard</h1>

<div class="row row-cols-1 row-cols-md-3 g-4">
    <!-- Card 1 -->
    <div class="col">
        <div class="card" onclick="window.location.href='{{ route('information-management.index') }}';" style="cursor:pointer;">
            <img src="{{ asset('images/imagecard1.jpg') }}" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';" alt="Image 1" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">Information Management</h5>
            </div>
        </div>
    </div>
    <!-- Card 2 -->
    <div class="col">
        <div class="card" onclick="window.location.href='{{ route('admin.notification') }}';" style="cursor:pointer;">
            <img src="{{ asset('images/imagecard2.jpg') }}" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';" alt="Image 2" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">Centralized Notification Hub</h5>
            </div>
        </div>
    </div>
    <!-- Card 3 -->
    <div class="col">
        <div class="card" onclick="window.location.href='{{ route('admin.project') }}';" style="cursor:pointer;">
            <img src="{{ asset('images/imagecard3.jpg') }}" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';" alt="Image 3" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">Project and Procurement Request</h5>
            </div>
        </div>
    </div>
    <!-- Card 4 -->
    <div class="col">
        <div class="card" onclick="window.location.href='{{ route('history.dashboard') }}';" style="cursor:pointer;">
            <img src="{{ asset('images/imagecard4.jpg') }}" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';" alt="Image 4" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">Project History and Reports</h5>
            </div>
        </div>
    </div>
    <!-- Card 5 -->
    <div class="col">
        <div class="card" onclick="window.location.href='{{ route('admin.analytics') }}';" style="cursor:pointer;">
            <img src="{{ asset('images/resize.jpg') }}" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';" alt="Image 5" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">Analytics and Recommendations</h5>
            </div>
        </div>
    </div>
    <!-- Card 6 -->
    <div class="col">
        <div class="card" onclick="window.location.href='{{ route('inventory.index') }}';" style="cursor:pointer;">
            <img src="{{ asset('images/imagecard6.jpg') }}" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';" alt="Image 6" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">Inventory Management</h5>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('resources/css/dbadmin.css') }}">
@endpush
