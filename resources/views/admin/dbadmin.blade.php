@extends('layouts.app')

@section('content')
<h1 class="text-center my-4">Admin Dashboard</h1>

<div class="top-controls mb-4">
    <a href="{{ route('admin.companies.pending') }}" class="btn btn-primary">
        <i class="fas fa-building me-2"></i>View Pending Companies
    </a>
</div>

<div class="row row-cols-1 row-cols-md-3 g-4">
    <!-- Card 1 -->
    <div class="col">
        <div class="card" onclick="window.location.href='{{ route('information-management.index') }}';" style="cursor:pointer;">
            <img src="{{ asset('images/imagecard1.svg') }}" alt="Image 1" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">Information Management</h5>
            </div>
        </div>
    </div>
    <!-- Card 2 -->
    <div class="col">
        <div class="card" onclick="window.location.href='{{ route('admin.notification') }}';" style="cursor:pointer;">
            <img src="{{ asset('images/imagecard2.svg') }}" alt="Image 2" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">Centralized Notification Hub</h5>
            </div>
        </div>
    </div>
    <!-- Card 3 -->
    <div class="col">
        <div class="card" onclick="window.location.href='{{ route('admin.project') }}';" style="cursor:pointer;">
            <img src="{{ asset('images/imagecard3.svg') }}" alt="Image 3" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">Project and Procurement Request</h5>
            </div>
        </div>
    </div>
    <!-- Card 4 -->
    <div class="col">
        <div class="card" onclick="window.location.href='{{ route('history.dashboard') }}';" style="cursor:pointer;">
            <img src="{{ asset('images/imagecard4.svg') }}" alt="Image 4" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">Project History and Reports</h5>
            </div>
        </div>
    </div>
    <!-- Card 5 -->
    <div class="col">
        <div class="card" onclick="window.location.href='{{ route('admin.analytics') }}';" style="cursor:pointer;">
            <img src="{{ asset('images/imagecard5.svg') }}" alt="Image 5" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">Analytics and Recommendations</h5>
            </div>
        </div>
    </div>
    <!-- Card 6 -->
    <div class="col">
        <div class="card" onclick="window.location.href='{{ route('admin.inventory') }}';" style="cursor:pointer;">
            <img src="{{ asset('images/imagecard6.svg') }}" alt="Image 6" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">Inventory Management</h5>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    @vite(['resources/css/dbadmin.css'])
@endpush
