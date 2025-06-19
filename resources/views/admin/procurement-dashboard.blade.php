@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <h1 class="text-center mb-5 fw-bold" style="letter-spacing:1px;">Procurement Dashboard</h1>
        <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
            <!-- Purchase Requests Card -->
            <div class="col">
                <div class="card h-100 shadow-sm border-0 hover-shadow">
                    <img src="{{ asset('images/purchase-request.svg') }}" class="card-img-top rounded-top-4" alt="Purchase Requests" style="object-fit:cover; height:180px;">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">Purchase Requests</h5>
                        <p class="card-text text-muted">Create and manage purchase requests for materials and supplies.</p>
                    </div>
                    <div class="card-footer bg-white border-0 d-flex gap-2">
                        <a href="{{ route('purchase-requests.create') }}" class="btn btn-primary flex-grow-1"> <i class="fas fa-plus"></i> New Request</a>
                        <a href="{{ route('purchase-requests.index') }}" class="btn btn-outline-secondary flex-grow-1"> <i class="fas fa-list"></i> View All</a>
                    </div>
                </div>
            </div>
            <!-- Purchase Orders Card -->
            <div class="col">
                <div class="card h-100 shadow-sm border-0 hover-shadow">
                    <img src="{{ asset('images/purchase-order.svg') }}" class="card-img-top rounded-top-4" alt="Purchase Orders" style="object-fit:cover; height:180px;">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">Purchase Orders</h5>
                        <p class="card-text text-muted">Track and manage all purchase orders and supplier deliveries.</p>
                    </div>
                    <div class="card-footer bg-white border-0 d-flex gap-2">
                        <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary flex-grow-1"> <i class="fas fa-plus"></i> New Order</a>
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary flex-grow-1"> <i class="fas fa-list"></i> View All</a>
                    </div>
                </div>
            </div>
            <!-- Supplier Management Card -->
            <div class="col">
                <div class="card h-100 shadow-sm border-0 hover-shadow">
                    <img src="{{ asset('images/supplier-management.svg') }}" class="card-img-top rounded-top-4" alt="Supplier Management" style="object-fit:cover; height:180px;">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">Supplier Management</h5>
                        <p class="card-text text-muted">Manage supplier information, invitations, and performance.</p>
                    </div>
                    <div class="card-footer bg-white border-0 d-flex gap-2">
                        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-success flex-grow-1"> <i class="fas fa-users"></i> Manage Suppliers</a>
                        <a href="{{ route('information-management.index', ['type' => 'company']) }}" class="btn btn-outline-primary flex-grow-1"> <i class="fas fa-eye"></i> View Suppliers</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
    .hover-shadow:hover {
        box-shadow: 0 8px 32px 0 rgba(56, 142, 60, 0.15) !important;
        transform: translateY(-4px) scale(1.02);
        transition: box-shadow 0.2s, transform 0.2s;
        }
        .card-img-top {
        border-radius: 1.5rem 1.5rem 0 0;
        }
    </style>
@endsection 