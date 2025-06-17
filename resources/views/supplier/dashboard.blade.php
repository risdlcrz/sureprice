@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1 class="display-5 fw-bold">Welcome, {{ auth()->user()->getDisplayNameAttribute() }}</h1>
        <p class="text-muted">Supplier Dashboard</p>
    </div>

    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title h5">Total Materials</h3>
                    <p class="display-6 text-primary mb-0">{{ $materials->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title h5">Active Quotations</h3>
                    <p class="display-6 text-success mb-0">{{ $activeQuotations->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title h5">Pending Invitations</h3>
                    <p class="display-6 text-warning mb-0">{{ $pendingInvitations->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-4">
        <h2 class="h4 mb-3">Quick Actions</h2>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title h5">Add New Material</h3>
                        <p class="card-text text-muted">Add a new material to your catalog</p>
                        <a href="{{ route('supplier.materials.create') }}" class="btn btn-primary">Add Material</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title h5">Respond to Quotations</h3>
                        <p class="card-text text-muted">View and respond to quotation requests</p>
                        <a href="{{ route('supplier.quotations.index') }}" class="btn btn-primary">View Quotations</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Materials -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Your Recent Materials</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials->take(5) as $material)
                    <tr>
                        <td>{{ $material->name }}</td>
                        <td>{{ $material->category->name ?? '-' }}</td>
                        <td>{{ $material->stock }}</td>
                        <td>₱{{ number_format($material->price, 2) }}</td>
                        <td>
                            <a href="{{ route('supplier.materials.edit', $material) }}" class="btn btn-sm btn-secondary">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No materials found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($materials->count() > 5)
        <div class="card-footer">
            <a href="{{ route('supplier.materials.index') }}" class="btn btn-link">View all materials →</a>
        </div>
        @endif
    </div>

    <!-- Recent Quotations -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Recent Quotations</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Quotation #</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeQuotations->take(5) as $quotation)
                    <tr>
                        <td>{{ $quotation->quotation_number }}</td>
                        <td>
                            <span class="badge {{ $quotation->status === 'pending' ? 'bg-warning' : ($quotation->status === 'accepted' ? 'bg-success' : 'bg-secondary') }}">
                                {{ ucfirst($quotation->status) }}
                            </span>
                        </td>
                        <td>{{ $quotation->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('supplier.quotations.show', $quotation) }}" class="btn btn-sm btn-primary">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No quotations found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($activeQuotations->count() > 5)
        <div class="card-footer">
            <a href="{{ route('supplier.quotations.index') }}" class="btn btn-link">View all quotations →</a>
        </div>
        @endif
    </div>

    <!-- Performance Card -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Your Performance</h5>
            <a href="{{ route('supplier.ranking') }}" class="btn btn-sm btn-primary">View Detailed Performance</a>
        </div>
        <div class="card-body">
            <p><strong>Overall Score:</strong> {{ $ranking->score ?? 'N/A' }}</p>
            <p><strong>Completed Orders:</strong> {{ $completedOrders ?? 0 }}</p>
            <p><strong>On-Time Delivery Rate:</strong> {{ isset($onTimeRate) ? ($onTimeRate . '%') : 'N/A' }}</p>
            <p><strong>Average Rating:</strong> {{ $averageRating ?? 'N/A' }}</p>
        </div>
    </div>
</div>
@endsection 