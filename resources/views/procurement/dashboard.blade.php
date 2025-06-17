@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1 class="display-5 fw-bold">Welcome, {{ auth()->user()->getDisplayNameAttribute() }}</h1>
        <p class="text-muted">Procurement Dashboard</p>
    </div>

    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title h5">Active RFQs</h3>
                    <p class="display-6 text-primary mb-0">{{ $activeRfqs }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title h5">Pending Approvals</h3>
                    <p class="display-6 text-warning mb-0">{{ $pendingApprovals }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title h5">Active Orders</h3>
                    <p class="display-6 text-success mb-0">{{ $activeOrders }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title h5">Total Suppliers</h3>
                    <p class="display-6 text-info mb-0">{{ $totalSuppliers }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-4">
        <h2 class="h4 mb-3">Quick Actions</h2>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title h5">Create RFQ</h3>
                        <p class="card-text text-muted">Create a new Request for Quotation</p>
                        <a href="{{ route('procurement.rfqs.create') }}" class="btn btn-primary">Create RFQ</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title h5">Manage Orders</h3>
                        <p class="card-text text-muted">View and manage purchase orders</p>
                        <a href="{{ route('procurement.orders.index') }}" class="btn btn-primary">View Orders</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title h5">Supplier Management</h3>
                        <p class="card-text text-muted">Manage supplier information and invitations</p>
                        <a href="{{ route('procurement.suppliers.index') }}" class="btn btn-primary">Manage Suppliers</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent RFQs -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Recent RFQs</h2>
            <a href="{{ route('procurement.rfqs.index') }}" class="btn btn-sm btn-primary">View All</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>RFQ #</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Responses</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentRfqs as $rfq)
                    <tr>
                        <td>{{ $rfq->rfq_number }}</td>
                        <td>{{ $rfq->due_date->format('M d, Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $rfq->status_color }}">
                                {{ ucfirst($rfq->status) }}
                            </span>
                        </td>
                        <td>{{ $rfq->responses_count }} responses</td>
                        <td>
                            <a href="{{ route('procurement.rfqs.show', $rfq) }}" class="btn btn-sm btn-primary">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No recent RFQs found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Recent Orders</h2>
            <a href="{{ route('procurement.orders.index') }}" class="btn btn-sm btn-primary">View All</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->supplier->name }}</td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $order->status_color }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('procurement.orders.show', $order) }}" class="btn btn-sm btn-primary">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No recent orders found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 