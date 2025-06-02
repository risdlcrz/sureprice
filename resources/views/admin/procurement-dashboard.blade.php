@extends('layouts.app')

@section('content')
    <div class="sidebar">
        @include('include.header_project')
    </div>

    <div class="content">
        <h1 class="text-center my-4">Procurement Dashboard</h1>

        <div class="container-fluid">
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <!-- Purchase Requests Card -->
                <div class="col">
                    <div class="card h-100">
                        <img src="{{ asset('images/purchase-request.jpg') }}" class="card-img-top" alt="Purchase Requests">
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
                        <img src="{{ asset('images/purchase-order.jpg') }}" class="card-img-top" alt="Purchase Orders">
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
                        <img src="{{ asset('images/new-inquiry.jpg') }}" class="card-img-top" alt="Inquiries">
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

                <!-- Quotations (RFQ) Card -->
                <div class="col">
                    <div class="card h-100">
                        <img src="{{ asset('images/new-quotation.jpg') }}" class="card-img-top" alt="Quotations">
                        <div class="card-body">
                            <h5 class="card-title">Quotations (RFQ)</h5>
                            <p class="card-text">Create and manage requests for quotation and supplier responses.</p>
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

                <!-- Supplier Invitations Card -->
                <div class="col">
                    <div class="card h-100">
                        <img src="{{ asset('images/new-invitation.jpg') }}" class="card-img-top" alt="Supplier Invitations">
                        <div class="card-body">
                            <h5 class="card-title">Supplier Invitations</h5>
                            <p class="card-text">Create and manage supplier invitations for bidding opportunities.</p>
                        </div>
                        <div class="card-footer d-flex gap-2">
                            <a href="{{ route('supplier-invitations.create') }}" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-plus"></i> New Invite
                            </a>
                            <a href="{{ route('supplier-invitations.index') }}" class="btn btn-secondary flex-grow-1">
                                <i class="fas fa-list"></i> View All
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Materials Management Card -->
                <div class="col">
                    <div class="card h-100">
                        <img src="{{ asset('images/materials.jpg') }}" class="card-img-top" alt="Materials Management">
                        <div class="card-body">
                            <h5 class="card-title">Materials Management</h5>
                            <p class="card-text">Manage materials inventory, specifications, and pricing information.</p>
                        </div>
                        <div class="card-footer d-flex gap-2">
                            <a href="{{ route('materials.create') }}" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-plus"></i> New Material
                            </a>
                            <a href="{{ route('materials.index') }}" class="btn btn-secondary flex-grow-1">
                                <i class="fas fa-list"></i> View All
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Suppliers Management Card -->
                <div class="col">
                    <div class="card h-100">
                        <img src="{{ asset('images/suppliers.jpg') }}" class="card-img-top" alt="Suppliers">
                        <div class="card-body">
                            <h5 class="card-title">Suppliers Management</h5>
                            <p class="card-text">Manage supplier information, relationships, and performance tracking.</p>
                        </div>
                        <div class="card-footer d-flex gap-2">
                            <a href="{{ route('suppliers.create') }}" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-plus"></i> New Supplier
                            </a>
                            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary flex-grow-1">
                                <i class="fas fa-list"></i> View All
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities Section -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recent Inquiries</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                @forelse($recentInquiries ?? [] as $inquiry)
                                <a href="{{ route('inquiries.show', $inquiry->id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $inquiry->subject }}</h6>
                                        <small>{{ $inquiry->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($inquiry->description, 50) }}</p>
                                </a>
                                @empty
                                <div class="list-group-item">
                                    <p class="mb-0">No recent inquiries</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recent Purchase Orders</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                @forelse($recentPurchaseOrders ?? [] as $purchaseOrder)
                                <a href="{{ route('purchase-orders.show', $purchaseOrder->id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $purchaseOrder->po_number }}</h6>
                                        <small>{{ $purchaseOrder->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">
                                        Supplier: {{ $purchaseOrder->supplier->name }}<br>
                                        Status: <span class="badge bg-{{ $purchaseOrder->status_color }}">{{ ucfirst($purchaseOrder->status) }}</span>
                                    </p>
                                </a>
                                @empty
                                <div class="list-group-item">
                                    <p class="mb-0">No recent purchase orders</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recent Purchase Requests</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                @forelse($recentPurchaseRequests ?? [] as $pr)
                                <a href="{{ route('purchase-requests.show', $pr->id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $pr->pr_number }}</h6>
                                        <small>{{ $pr->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">
                                        Department: {{ $pr->department }}<br>
                                        Status: <span class="badge bg-{{ $pr->status_color }}">{{ ucfirst($pr->status) }}</span>
                                    </p>
                                </a>
                                @empty
                                <div class="list-group-item">
                                    <p class="mb-0">No recent purchase requests</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .card {
            transition: transform 0.2s;
            cursor: pointer;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .card-footer {
            background: none;
            border-top: none;
            padding: 1rem;
        }
        .btn {
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .list-group-item {
            transition: background-color 0.2s;
        }
        .list-group-item:hover {
            background-color: rgba(0,0,0,0.02);
        }
    </style>
    @endpush
@endsection 