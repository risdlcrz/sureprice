@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h1 class="h3 mb-4 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Inventory Dashboard</h1>
    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-0 hover-shadow">
                <div class="card-body text-center">
                    <h3 class="card-title h5 fw-semibold">Total Items</h3>
                    <p class="display-6 text-primary mb-0">{{ $totalItems }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-0 hover-shadow">
                <div class="card-body text-center">
                    <h3 class="card-title h5 fw-semibold">Low Stock</h3>
                    <p class="display-6 text-warning mb-0">{{ $lowStockItems }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-0 hover-shadow">
                <div class="card-body text-center">
                    <h3 class="card-title h5 fw-semibold">Expiring Items</h3>
                    <p class="display-6 text-danger mb-0">{{ $expiringItems }}</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Quick Actions -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="{{ route('procurement.inventory.create') }}" class="btn btn-primary btn-lg w-100 rounded-pill"><i class="fas fa-plus"></i> Add New Item</a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('procurement.inventory.low-stock') }}" class="btn btn-outline-warning btn-lg w-100 rounded-pill"><i class="fas fa-exclamation-triangle"></i> Low Stock</a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('procurement.inventory.expiring') }}" class="btn btn-outline-danger btn-lg w-100 rounded-pill"><i class="fas fa-clock"></i> Expiring Soon</a>
        </div>
    </div>
    <!-- Search and Inventory Table -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Inventory List</h5>
            <div class="input-group w-auto">
                <input type="text" id="searchInput" class="form-control" placeholder="Search inventory...">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="inventoryTable">
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Last Restock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventories as $inventory)
                        <tr>
                            <td>{{ $inventory->material->name }}</td>
                            <td>{{ $inventory->material->category->name }}</td>
                            <td>{{ $inventory->quantity }}</td>
                            <td>{{ $inventory->unit }}</td>
                            <td>{{ $inventory->location ?? 'N/A' }}</td>
                            <td>
                                @if($inventory->status)
                                    @php
                                        $statusClass = '';
                                        switch ($inventory->status) {
                                            case 'active':
                                                $statusClass = 'bg-success';
                                                break;
                                            case 'inactive':
                                                $statusClass = 'bg-warning';
                                                break;
                                            case 'obsolete':
                                            case 'danger':
                                                $statusClass = 'bg-danger';
                                                break;
                                        }
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
                                        {{ ucfirst($inventory->status) }}
                                    </span>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $inventory->last_restock_date ? $inventory->last_restock_date->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#adjustStockModal{{ $inventory->id }}">
                                        <i class="fas fa-balance-scale"></i>
                                    </button>
                                    <a href="{{ route('procurement.inventory.edit', $inventory) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('procurement.inventory.destroy', $inventory) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this item?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                <!-- Adjust Stock Modal -->
                                <div class="modal fade" id="adjustStockModal{{ $inventory->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('procurement.inventory.adjust-stock', $inventory) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Adjust Stock - {{ $inventory->material->name }}</h5>
                                                    <button type="button" class="close" data-bs-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Operation</label>
                                                        <select name="operation" class="form-control" required>
                                                            <option value="add">Add Stock</option>
                                                            <option value="subtract">Subtract Stock</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Quantity</label>
                                                        <input type="number" name="quantity" class="form-control" step="0.01" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Notes</label>
                                                        <textarea name="notes" class="form-control" rows="3"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $inventories->links() }}
            </div>
        </div>
    </div>
</div>
@push('styles')
    @vite(['resources/css/procurement/inventory-dashboard.css'])
@endpush
@push('scripts')
    @vite(['resources/js/procurement/inventory-dashboard.js'])
@endpush
@endsection 