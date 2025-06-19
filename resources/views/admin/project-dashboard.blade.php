@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h1 class="text-center mb-5 fw-bold" style="letter-spacing:1px;">Project & Procurement Dashboard</h1>

    <!-- Project Management Section -->
    <section class="mb-5">
        <h2 class="mb-4 fw-semibold text-success">Project Management</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <!-- Create Contract Card -->
            <div class="col">
                <div class="card h-100 shadow-sm border-0 hover-shadow">
                    <img src="{{ Vite::asset('resources/Images/ppimage1.jpg') }}" class="card-img-top rounded-top-4" alt="Create Contract" style="object-fit:cover; height:180px;">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">Create Contract</h5>
                        <p class="card-text text-muted">Start a new contract and set up initial terms and conditions.</p>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <a href="{{ route('contracts.create') }}" class="btn btn-primary w-100">Create New Contract</a>
                    </div>
                </div>
            </div>
            <!-- View Contracts Card -->
            <div class="col">
                <div class="card h-100 shadow-sm border-0 hover-shadow">
                    <img src="{{ Vite::asset('resources/Images/ppimage2.jpg') }}" class="card-img-top rounded-top-4" alt="View Contracts" style="object-fit:cover; height:180px;">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">View Contracts</h5>
                        <p class="card-text text-muted">Access and manage existing contracts, track status and approvals.</p>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <a href="{{ route('contracts.index') }}" class="btn btn-secondary w-100">View All Contracts</a>
                    </div>
                </div>
            </div>
            <!-- Project Timeline Card -->
            <div class="col">
                <div class="card h-100 shadow-sm border-0 hover-shadow">
                    <img src="{{ Vite::asset('resources/Images/ppimage3.jpg') }}" class="card-img-top rounded-top-4" alt="Project Timeline" style="object-fit:cover; height:180px;">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">Project Timeline</h5>
                        <p class="card-text text-muted">Visualize project schedules, milestones, and deadlines.</p>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <a href="{{ route('project-timeline.index') }}" class="btn btn-outline-success w-100">View Timeline</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Procurement Management Section -->
    <section class="mb-5">
        <h2 class="mb-4 fw-semibold text-success">Procurement Management</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <!-- Purchase Requests Card -->
            <div class="col">
                <div class="card h-100 shadow-sm border-0 hover-shadow">
                    <img src="{{ Vite::asset('resources/Images/ppimage4.jpg') }}" class="card-img-top rounded-top-4" alt="Purchase Requests" style="object-fit:cover; height:180px;">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">Purchase Requests</h5>
                        <p class="card-text text-muted">Create and manage purchase requests for materials and supplies.</p>
                    </div>
                    <div class="card-footer bg-white border-0 d-flex gap-2">
                        <a href="{{ route('purchase-requests.create') }}" class="btn btn-primary flex-grow-1">+ New Request</a>
                        <a href="{{ route('purchase-requests.index') }}" class="btn btn-outline-secondary flex-grow-1">View All</a>
                    </div>
                </div>
            </div>
            <!-- Purchase Orders Card -->
            <div class="col">
                <div class="card h-100 shadow-sm border-0 hover-shadow">
                    <img src="{{ Vite::asset('resources/Images/ppimage5.jpg') }}" class="card-img-top rounded-top-4" alt="Purchase Orders" style="object-fit:cover; height:180px;">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">Purchase Orders</h5>
                        <p class="card-text text-muted">Create and manage purchase orders from approved purchase requests.</p>
                    </div>
                    <div class="card-footer bg-white border-0 d-flex gap-2">
                        <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary flex-grow-1">+ New Order</a>
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary flex-grow-1">View All</a>
                    </div>
                </div>
            </div>
            <!-- Inquiries Card -->
            <div class="col">
                <div class="card h-100 shadow-sm border-0 hover-shadow">
                    <img src="{{ Vite::asset('resources/Images/ppimage7.jpg') }}" class="card-img-top rounded-top-4" alt="Inquiries" style="object-fit:cover; height:180px;">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">Inquiries</h5>
                        <p class="card-text text-muted">Submit and track material inquiries and procurement requests.</p>
                    </div>
                    <div class="card-footer bg-white border-0 d-flex gap-2">
                        <a href="{{ route('inquiries.create') }}" class="btn btn-primary flex-grow-1">+ New Inquiry</a>
                        <a href="{{ route('inquiries.index') }}" class="btn btn-outline-secondary flex-grow-1">View All</a>
                    </div>
                </div>
            </div>
            <!-- Quotation Management Card -->
            <div class="col">
                <div class="card h-100 shadow-sm border-0 hover-shadow mt-4">
                    <img src="{{ Vite::asset('resources/Images/ppimage8.jpg') }}" class="card-img-top rounded-top-4" alt="Quotation Management" style="object-fit:cover; height:180px;">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">Quotation Management</h5>
                        <p class="card-text text-muted">Create and manage RFQs, compare supplier quotations, and track responses.</p>
                    </div>
                    <div class="card-footer bg-white border-0 d-flex gap-2">
                        <a href="{{ route('quotations.create') }}" class="btn btn-primary flex-grow-1">+ New RFQ</a>
                        <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary flex-grow-1">View All</a>
                    </div>
                </div>
            </div>
            <!-- Materials Management Card -->
            <div class="col">
                <div class="card h-100 shadow-sm border-0 hover-shadow mt-4">
                    <img src="{{ Vite::asset('resources/Images/ppimage9.jpg') }}" class="card-img-top rounded-top-4" alt="Materials Management" style="object-fit:cover; height:180px;">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">Materials Management</h5>
                        <p class="card-text text-muted">Manage materials inventory, specifications, and pricing information.</p>
                    </div>
                    <div class="card-footer bg-white border-0 d-flex gap-2">
                        <a href="{{ route('materials.create') }}" class="btn btn-primary flex-grow-1">+ New Material</a>
                        <a href="{{ route('materials.index') }}" class="btn btn-outline-secondary flex-grow-1">View All</a>
                    </div>
                </div>
            </div>
            <!-- Supplier Management Card -->
            <div class="col">
                <div class="card h-100 shadow-sm border-0 hover-shadow">
                    <img src="{{ Vite::asset('resources/Images/ppimage10.jpg') }}" class="card-img-top rounded-top-4" alt="Supplier Management" style="object-fit:cover; height:180px;">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">Supplier Management</h5>
                        <p class="card-text text-muted">Manage supplier information, relationships, performance tracking, and send invitations to new suppliers.</p>
                    </div>
                    <div class="card-footer bg-white border-0 d-flex gap-2">
                        <a href="{{ route('supplier-invitations.create') }}" class="btn btn-primary flex-grow-1">Invite Supplier</a>
                        <a href="{{ route('supplier-invitations.index') }}" class="btn btn-outline-secondary flex-grow-1">View Invitations</a>
                        <a href="{{ route('information-management.index', ['type' => 'company']) }}" class="btn btn-outline-primary flex-grow-1">View Supplier</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Stats Section -->
    <section class="mb-5">
        <h2 class="mb-4 fw-semibold text-success">Quick Stats</h2>
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="card text-center shadow-sm border-0 p-3">
                    <div class="fw-bold fs-4 text-primary">₱{{ number_format($totalBudget, 2) }}</div>
                    <div class="text-muted">Total Budget</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card text-center shadow-sm border-0 p-3">
                    <div class="fw-bold fs-4 text-success">₱{{ number_format($totalSpent, 2) }}</div>
                    <div class="text-muted">Total Spent</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card text-center shadow-sm border-0 p-3">
                    <div class="fw-bold fs-4 text-info">{{ $recentContracts->count() }}</div>
                    <div class="text-muted">Recent Contracts</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card text-center shadow-sm border-0 p-3">
                    <div class="fw-bold fs-4 text-warning">{{ $recentPurchaseOrders->count() }}</div>
                    <div class="text-muted">Recent Orders</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Activities Section -->
    <section class="mb-5">
        <h2 class="mb-4 fw-semibold text-success">Recent Activities</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-semibold">Recent Contracts</div>
                    <div class="card-body p-2">
                        @if(isset($recentContracts) && $recentContracts->count())
                            <ul class="list-group list-group-flush">
                                @foreach($recentContracts as $contract)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Total Amount: ₱{{ number_format($contract->total_amount, 2) }}</span>
                                        <span class="text-muted small">{{ $contract->created_at->diffForHumans() }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-muted">No recent contracts</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-semibold">Recent Purchase Orders</div>
                    <div class="card-body p-2">
                        @if(isset($recentPurchaseOrders) && $recentPurchaseOrders->count())
                            <ul class="list-group list-group-flush">
                                @foreach($recentPurchaseOrders as $order)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>PO #: {{ $order->po_number }}</span>
                                        <span class="text-muted small">{{ $order->created_at->diffForHumans() }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-muted">No recent purchase orders</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-semibold">Recent Purchase Requests</div>
                    <div class="card-body p-2">
                        @if(isset($recentPurchaseRequests) && $recentPurchaseRequests->count())
                            <ul class="list-group list-group-flush">
                                @foreach($recentPurchaseRequests as $pr)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>PR #: {{ $pr->pr_number }}</span>
                                        <span class="text-muted small">{{ $pr->created_at->diffForHumans() }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-muted">No recent purchase requests</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
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
