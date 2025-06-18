@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Procurement Management Dashboard</h1>
        </div>
    </div>

    <!-- Main Cards -->
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
        <!-- Purchase Requests Card -->
        <div class="col">
            <div class="card h-100">
                <img src="{{ asset('images/purchase-request.svg') }}" class="card-img-top" alt="Purchase Requests">
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
                <img src="{{ asset('images/purchase-order.svg') }}" class="card-img-top" alt="Purchase Orders">
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

        <!-- Quotations (RFQ) Card -->
        <div class="col">
            <div class="card h-100">
                <img src="{{ asset('images/new-quotation.svg') }}" class="card-img-top" alt="Quotations">
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
                <img src="{{ asset('images/supplier-invitation.svg') }}" class="card-img-top" alt="Supplier Invitations">
                <div class="card-body">
                    <h5 class="card-title">Supplier Invitations</h5>
                    <p class="card-text">Invite suppliers to participate in procurement processes.</p>
                </div>
                <div class="card-footer d-flex gap-2">
                    <a href="{{ route('supplier-invitations.create') }}" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-plus"></i> New Invitation
                    </a>
                    <a href="{{ route('supplier-invitations.index') }}" class="btn btn-secondary flex-grow-1">
                        <i class="fas fa-list"></i> View All
                    </a>
                </div>
            </div>
        </div>

        <!-- Inquiries Card -->
        <div class="col">
            <div class="card h-100">
                <img src="{{ asset('images/inquiry.svg') }}" class="card-img-top" alt="Inquiries">
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

        <!-- Materials Management Card -->
        <div class="col">
            <div class="card h-100">
                <img src="{{ asset('images/materials.svg') }}" class="card-img-top" alt="Materials">
                <div class="card-body">
                    <h5 class="card-title">Materials Management</h5>
                    <p class="card-text">Manage materials, categories, and supplier relationships.</p>
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
    </div>

    <!-- Recent Activity Section -->
    <div class="row">
        <!-- Recent Purchase Requests -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Purchase Requests</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($recentPurchaseRequests ?? [] as $request)
                        <a href="{{ route('purchase-requests.show', $request->id) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $request->request_number }}</h6>
                                <small>{{ $request->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">
                                Status: <span class="badge bg-{{ $request->status_color }}">{{ ucfirst($request->status) }}</span><br>
                                Total: ₱{{ number_format($request->total_amount, 2) }}
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

        <!-- Recent Purchase Orders -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Purchase Orders</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($recentPurchaseOrders ?? [] as $order)
                        <a href="{{ route('purchase-orders.show', $order->id) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $order->po_number }}</h6>
                                <small>{{ $order->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">
                                Supplier: {{ $order->supplier->name }}<br>
                                Status: <span class="badge bg-{{ $order->status_color }}">{{ ucfirst($order->status) }}</span>
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

        <!-- Recent Quotations -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Quotations</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($recentQuotations ?? [] as $quotation)
                        <a href="{{ route('quotations.show', $quotation->id) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $quotation->rfq_number }}</h6>
                                <small>{{ $quotation->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">
                                Status: <span class="badge bg-{{ $quotation->status_color }}">{{ ucfirst($quotation->status) }}</span><br>
                                Due: {{ $quotation->due_date->format('M d, Y') }}
                            </p>
                        </a>
                        @empty
                        <div class="list-group-item">
                            <p class="mb-0">No recent quotations</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 