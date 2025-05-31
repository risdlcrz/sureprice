@extends('layouts.app')

@section('content')
    <div class="sidebar">
        @include('include.header_project')
    </div>

    <div class="content">
        <h1 class="text-center my-4">Procurement Dashboard</h1>

        <div class="container">
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <!-- Purchase Requests Card -->
                <div class="col">
                    <div class="card h-100">
                        <img src="{{ asset('images/purchase-request.jpg') }}" class="card-img-top" alt="Purchase Requests">
                        <div class="card-body">
                            <h5 class="card-title">Purchase Requests</h5>
                            <p class="card-text">Create and manage purchase requests for materials and supplies.</p>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('purchase-request.create') }}" class="btn btn-primary w-100">Create Purchase Request</a>
                        </div>
                    </div>
                </div>

                <!-- Materials Management Card -->
                <div class="col">
                    <div class="card h-100">
                        <img src="{{ asset('images/materials.jpg') }}" class="card-img-top" alt="Materials Management">
                        <div class="card-body">
                            <h5 class="card-title">Materials Management</h5>
                            <p class="card-text">View and manage materials inventory and specifications.</p>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('materials.index') }}" class="btn btn-secondary w-100">Manage Materials</a>
                        </div>
                    </div>
                </div>

                <!-- Suppliers Card -->
                <div class="col">
                    <div class="card h-100">
                        <img src="{{ asset('images/suppliers.jpg') }}" class="card-img-top" alt="Suppliers">
                        <div class="card-body">
                            <h5 class="card-title">Suppliers</h5>
                            <p class="card-text">Manage supplier information and relationships.</p>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary w-100">Manage Suppliers</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 