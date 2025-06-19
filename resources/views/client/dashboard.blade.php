@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1 class="display-5 fw-bold mb-2">Welcome, {{ $company->contact_person }}</h1>
        <p class="text-muted fs-5">Project & Procurement Dashboard</p>
    </div>
    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-0 hover-shadow">
                <div class="card-body text-center">
                    <h3 class="card-title h5 fw-semibold">Total Contracts</h3>
                    <p class="display-6 text-primary mb-0">{{ $contracts->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-0 hover-shadow">
                <div class="card-body text-center">
                    <h3 class="card-title h5 fw-semibold">Active Projects</h3>
                    <p class="display-6 text-success mb-0">
                        {{ $contracts->whereIn('status', ['approved', 'in_progress'])->count() }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-0 hover-shadow">
                <div class="card-body text-center">
                    <h3 class="card-title h5 fw-semibold">Pending Approvals</h3>
                    <p class="display-6 text-warning mb-0">
                        {{ $contracts->whereIn('status', ['draft', 'pending'])->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- Quick Actions -->
    <div class="row g-3">
        <div class="col-md-6">
            <a href="{{ route('contracts.index') }}" class="btn btn-primary btn-lg w-100 rounded-pill">View Contracts</a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('project-timeline.index') }}" class="btn btn-outline-success btn-lg w-100 rounded-pill">Project Timeline</a>
        </div>
    </div>
</div>
<style>
.hover-shadow:hover {
    box-shadow: 0 8px 32px 0 rgba(56, 142, 60, 0.15) !important;
    transform: translateY(-4px) scale(1.02);
    transition: box-shadow 0.2s, transform 0.2s;
}
</style>
@endsection 