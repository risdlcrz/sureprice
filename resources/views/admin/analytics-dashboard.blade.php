@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Analytics Dashboard</h1>
        </div>
    </div>

    <div class="row">
        <!-- Active Purchase Orders Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <a href="{{ route('admin.purchase-order') }}" class="text-decoration-none">
                <div class="card h-100">
                    <img src="{{ asset('images/purchase-order.jpg') }}" class="card-img-top" alt="Purchase Orders">
                    <div class="card-body">
                        <h5 class="card-title">Active Purchase Order</h5>
                        <p class="card-text">Track and analyze active purchase orders and their status.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Budget Allocation Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <a href="{{ route('admin.budget-allocation') }}" class="text-decoration-none">
                <div class="card h-100">
                    <img src="{{ asset('images/budget.jpg') }}" class="card-img-top" alt="Budget">
                    <div class="card-body">
                        <h5 class="card-title">Budget Allocation and Expenditures</h5>
                        <p class="card-text">Monitor budget allocations and track expenditures.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Supplier Rankings Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <a href="{{ route('admin.supplier-rankings') }}" class="text-decoration-none">
                <div class="card h-100">
                    <img src="{{ asset('images/suppliers.jpg') }}" class="card-img-top" alt="Suppliers">
                    <div class="card-body">
                        <h5 class="card-title">Supplier Ranking and Performance</h5>
                        <p class="card-text">Evaluate and compare supplier performance metrics.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Price Trend Analysis Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <a href="{{ route('admin.price-analysis') }}" class="text-decoration-none">
                <div class="card h-100">
                    <img src="{{ asset('images/price-trend.jpg') }}" class="card-img-top" alt="Price Trend">
                    <div class="card-body">
                        <h5 class="card-title">Price Trend Analysis</h5>
                        <p class="card-text">Analyze price trends and market fluctuations.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Additional Analytics Content -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Analytics Overview</h5>
                </div>
                <div class="card-body">
                    <!-- Add your analytics overview content here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    transition: transform 0.2s;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.card:hover {
    transform: translateY(-5px);
}

.card-img-top {
    height: 200px;
    object-fit: cover;
}
</style>
@endpush
