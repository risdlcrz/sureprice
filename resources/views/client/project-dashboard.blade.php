@extends('layouts.app')

@section('content')
    <h1 class="text-center my-4">Project & Procurement Dashboard</h1>

    <div class="container-fluid ">
        <!-- Project Management Section -->
        <h2 class="mb-4">Project Management</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <!-- Create Contract Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ Vite::asset('resources/images/ppimage1.svg') }}" class="card-img-top" alt="Create Contract">
                    <div class="card-body">
                        <h5 class="card-title">Create Contract</h5>
                        <p class="card-text">Start a new contract and set up initial terms and conditions.</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('contracts.create') }}" class="btn btn-primary w-100">Create New Contract</a>
                    </div>
                </div>
            </div>

            <!-- View Contracts Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ Vite::asset('resources/images/ppimage2.svg') }}" class="card-img-top" alt="View Contracts">
                    <div class="card-body">
                        <h5 class="card-title">View Contracts</h5>
                        <p class="card-text">Access and manage your contracts, track status.</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('contracts.index') }}" class="btn btn-secondary w-100">View All Contracts</a>
                    </div>
                </div>
            </div>

            <!-- Project Timeline Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ Vite::asset('resources/images/ppimage3.svg') }}" class="card-img-top" alt="Project Timeline">
                    <div class="card-body">
                        <h5 class="card-title">Project Timeline</h5>
                        <p class="card-text">View and manage project schedules and timelines.</p>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="contractSearch" placeholder="Search contracts..." autocomplete="off">
                            <div id="contractSearchResults" class="list-group mt-2" style="max-height: 200px; overflow-y: auto; display: none;"></div>
                        </div>
                        <div id="selectedContract" class="mb-3" style="display: none;">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2" id="contractTitle"></h6>
                                    <p class="card-text" id="contractDetails"></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge" id="contractStatus"></span>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">Clear</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('project-timeline.index') }}" class="btn btn-info w-100">View Timeline</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Procurement Section -->
        <h2 class="mt-5 mb-4">Procurement Management</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <!-- Purchase Requests Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ Vite::asset('resources/images/ppimage5.svg') }}" class="card-img-top" alt="Purchase Requests">
                    <div class="card-body">
                        <h5 class="card-title">Purchase Requests</h5>
                        <p class="card-text">Create and manage purchase requests for materials and supplies.</p>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <a href="{{ route('purchase-requests.create') }}" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-plus"></i> New Request
                        </a>
                        <a href="{{ route('purchase-requests.index') }}" class="btn btn-secondary flex-grow-1">
                            <i class="fas fa-list"></i> View All
                        </a>
                    </div>
                </div>
            </div>

            <!-- Purchase Orders Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ Vite::asset('resources/images/ppimage4.svg') }}" class="card-img-top" alt="Purchase Orders">
                    <div class="card-body">
                        <h5 class="card-title">Purchase Orders</h5>
                        <p class="card-text">Create and manage purchase orders from approved purchase requests.</p>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-plus"></i> New Order
                        </a>
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary flex-grow-1">
                            <i class="fas fa-list"></i> View All
                        </a>
                    </div>
                </div>
            </div>

            <!-- Inquiries Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ Vite::asset('resources/images/ppimage6.svg') }}" class="card-img-top" alt="Inquiries">
                    <div class="card-body">
                        <h5 class="card-title">Inquiries</h5>
                        <p class="card-text">Submit and track material inquiries and procurement requests.</p>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <a href="{{ route('inquiries.create') }}" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-plus"></i> New Inquiry
                        </a>
                        <a href="{{ route('inquiries.index') }}" class="btn btn-secondary flex-grow-1">
                            <i class="fas fa-list"></i> View All
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quotation Management Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ Vite::asset('resources/images/ppimage8.svg') }}" class="card-img-top" alt="Quotation Management">
                    <div class="card-body">
                        <h5 class="card-title">Quotation Management</h5>
                        <p class="card-text">Create and manage RFQs, compare supplier quotations, and track responses.</p>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <a href="{{ route('quotations.create') }}" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-plus"></i> New RFQ
                        </a>
                        <a href="{{ route('quotations.index') }}" class="btn btn-secondary flex-grow-1">
                            <i class="fas fa-list"></i> View All
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 