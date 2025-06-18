@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Procurement Dashboard</h1>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title h5">Active RFQs</h3>
                            <p class="display-6 text-white mb-0">{{ $recentQuotations->where('status', 'active')->count() }}</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title h5">Active Orders</h3>
                            <p class="display-6 text-white mb-0">{{ $recentPurchaseOrders->whereIn('status', ['approved', 'processing'])->count() }}</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title h5">Pending Requests</h3>
                            <p class="display-6 text-white mb-0">{{ $recentPurchaseRequests->where('status', 'pending')->count() }}</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title h5">Total Suppliers</h3>
                            <p class="display-6 text-white mb-0">{{ \App\Models\Supplier::count() }}</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
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
                        <a href="{{ route('quotations.create') }}" class="btn btn-primary">Create RFQ</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title h5">Manage Orders</h3>
                        <p class="card-text text-muted">View and manage purchase orders</p>
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-primary">View Orders</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title h5">Supplier Management</h3>
                        <p class="card-text text-muted">Manage supplier information and invitations</p>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-primary">Manage Suppliers</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <!-- Recent Purchase Requests -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Purchase Requests</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($recentPurchaseRequests as $request)
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
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Purchase Orders</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($recentPurchaseOrders as $order)
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
    </div>

    <!-- Recent Quotations -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Quotations (RFQs)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>RFQ Number</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Suppliers</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentQuotations as $quotation)
                                <tr>
                                    <td>{{ $quotation->rfq_number }}</td>
                                    <td>{{ $quotation->due_date->format('M d, Y') }}</td>
                                    <td><span class="badge bg-{{ $quotation->status_color }}">{{ ucfirst($quotation->status) }}</span></td>
                                    <td>{{ $quotation->suppliers->count() }} suppliers</td>
                                    <td>
                                        <a href="{{ route('quotations.show', $quotation->id) }}" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No recent quotations found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 