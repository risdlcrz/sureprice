@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="fw-bold mb-1">Stock Movement History</h1>
            <p class="text-muted mb-0 fs-5">{{ $material->name }}</p>
        </div>
        <a href="{{ route('warehouse.inventory.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Inventory
        </a>
    </div>

    <!-- Material Info Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Current Stock</h6>
                    <h3 class="mb-0 fw-bold">{{ $stock ? $stock->current_stock : 0 }}</h3>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Threshold</h6>
                    <h3 class="mb-0 fw-bold">{{ $stock ? $stock->threshold : 0 }}</h3>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Warehouse</h6>
                    <h3 class="mb-0 fw-bold">{{ $stock && $stock->warehouse ? $stock->warehouse->name : '-' }}</h3>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Status</h6>
                    <h3 class="mb-0">
                        @if(!$stock || $stock->current_stock <= 0)
                            <span class="badge rounded-pill bg-danger">Out of Stock</span>
                        @elseif($stock->current_stock < $stock->threshold)
                            <span class="badge rounded-pill bg-warning text-dark">Low Stock</span>
                        @else
                            <span class="badge rounded-pill bg-success">Normal</span>
                        @endif
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Movement History Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 border-bottom-0">
            <h5 class="mb-0 fw-semibold"><i class="fas fa-history me-1"></i> Movement History</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Previous Stock</th>
                        <th>New Stock</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                    <tr>
                        <td>{{ $movement->created_at->format('M d, Y H:i') }}</td>
                        <td><span class="badge bg-secondary">{{ $movement->reference_number }}</span></td>
                        <td>
                            <span class="badge rounded-pill bg-{{ $movement->type === 'in' ? 'success' : 'danger' }}">
                                {{ ucfirst($movement->type) }}
                            </span>
                        </td>
                        <td>{{ $movement->quantity }}</td>
                        <td>{{ $movement->previous_stock }}</td>
                        <td>{{ $movement->new_stock }}</td>
                        <td>{{ $movement->notes }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No movement history found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $movements->links() }}
        </div>
    </div>
</div>
@endsection 