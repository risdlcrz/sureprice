@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Stock Movement History</h1>
            <p class="text-muted mb-0">{{ $material->name }}</p>
        </div>
        <a href="{{ route('warehouse.inventory.index') }}" class="btn btn-secondary">Back to Inventory</a>
    </div>

    <!-- Material Info Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Current Stock</h6>
                    <h3 class="mb-0">{{ $material->stock }}</h3>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Minimum Stock</h6>
                    <h3 class="mb-0">{{ $material->minimum_stock }}</h3>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Category</h6>
                    <h3 class="mb-0">{{ $material->category->name }}</h3>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Status</h6>
                    <h3 class="mb-0">
                        @if($material->stock <= 0)
                            <span class="badge bg-danger">Out of Stock</span>
                        @elseif($material->stock < $material->minimum_stock)
                            <span class="badge bg-warning">Low Stock</span>
                        @else
                            <span class="badge bg-success">Normal</span>
                        @endif
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Movement History Table -->
    <div class="card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Movement History</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
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
                        <td>{{ $movement->reference_number }}</td>
                        <td>
                            <span class="badge bg-{{ $movement->type === 'in' ? 'success' : 'danger' }}">
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
        <div class="card-footer">
            {{ $movements->links() }}
        </div>
    </div>
</div>
@endsection 