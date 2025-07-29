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
            <a href="{{ route('admin.transactions') }}" class="text-decoration-none">
                <div class="card h-100">
                    <img src="{{ asset('images/aimage1.jpg') }}" class="card-img-top" alt="Purchase Orders" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body">
                        <h5 class="card-title">Transactions</h5>
                        <p class="card-text">History and reports of past transactions.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Budget Allocation Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <a href="{{ route('admin.budget-allocation') }}" class="text-decoration-none">
                <div class="card h-100">
                    <img src="{{ asset('images/aimage2.jpg') }}" class="card-img-top" alt="Budget" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
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
                    <img src="{{ asset('images/aimage3.jpg') }}" class="card-img-top" alt="Suppliers" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
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
                    <img src="{{ asset('images/aimage4.jpg') }}" class="card-img-top" alt="Price Trend" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body">
                        <h5 class="card-title">Price Trend Analysis</h5>
                        <p class="card-text">Analyze price trends and market fluctuations.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Supplier Recommendation Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <a href="{{ route('analytics.supplier-recommendation') }}" class="text-decoration-none">
                <div class="card h-100">
                    <img src="{{ asset('images/aimage5.jpg') }}" class="card-img-top" alt="Supplier Recommendation" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body">
                        <h5 class="card-title">Supplier Recommendation</h5>
                        <p class="card-text">Get data-driven supplier recommendations by category and metrics.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Client Feedback Analytics Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <a href="{{ route('admin.feedback.analytics') }}" class="text-decoration-none">
                <div class="card h-100">
                    <img src="{{ asset('images/aimage6.jpg') }}" class="card-img-top" alt="Client Feedback" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                    <div class="card-body">
                        <h5 class="card-title">Client Feedback Analytics</h5>
                        <p class="card-text">Analyze client satisfaction and identify improvement areas.</p>
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
    border: none;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    overflow: hidden;
    height: 100%;
    cursor: pointer;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}
.card-img-top {
    height: 200px;
    object-fit: cover;
}

.card-body {
    padding: 1.5rem;
}

.card-footer {
    background: none;
    border-top: none;
    padding: 1rem;
}

.card-title {
    color: #333;
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 0;
    text-align: center;
}
</style>
@endpush
