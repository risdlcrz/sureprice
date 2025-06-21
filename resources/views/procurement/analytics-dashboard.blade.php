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
            <a href="{{ route('procurement.analytics.transactions') }}" class="text-decoration-none">
                <div class="card h-100">
                    <img src="{{ asset('images/aimage1.svg') }}" class="card-img-top" alt="Purchase Orders">
                    <div class="card-body">
                        <h5 class="card-title">Transactions</h5>
                        <p class="card-text">History and reports of past transactions.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Budget Allocation Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <a href="{{ route('procurement.analytics.budget-allocation') }}" class="text-decoration-none">
                <div class="card h-100">
                    <img src="{{ asset('images/aimage2.svg') }}" class="card-img-top" alt="Budget">
                    <div class="card-body">
                        <h5 class="card-title">Budget Allocation and Expenditures</h5>
                        <p class="card-text">Monitor budget allocations and track expenditures.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Supplier Rankings Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <a href="{{ route('procurement.suppliers.rankings') }}" class="text-decoration-none">
                <div class="card h-100">
                    <img src="{{ asset('images/aimage3.svg') }}" class="card-img-top" alt="Suppliers">
                    <div class="card-body">
                        <h5 class="card-title">Supplier Ranking and Performance</h5>
                        <p class="card-text">Evaluate and compare supplier performance metrics.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Price Trend Analysis Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <a href="{{ route('procurement.analytics.price-analysis') }}" class="text-decoration-none">
                <div class="card h-100">
                    <img src="{{ asset('images/aimage4.svg') }}" class="card-img-top" alt="Price Trend">
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