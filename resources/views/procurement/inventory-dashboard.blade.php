@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Inventory Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('procurement.inventory.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Item
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $totalItems }}</h3>
                                    <p>Total Items</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-boxes"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $lowStockItems }}</h3>
                                    <p>Low Stock Items</p>
                        </div>
                                <div class="icon">
                                    <i class="fas fa-exclamation-triangle"></i>
        </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $expiringItems }}</h3>
                                    <p>Expiring Items</p>
                        </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" id="searchInput" class="form-control" placeholder="Search inventory...">
                                <div class="input-group-append">
                                    <button class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('procurement.inventory.low-stock') }}" class="btn btn-warning">
                                <i class="fas fa-exclamation-triangle"></i> Low Stock
                            </a>
                            <a href="{{ route('procurement.inventory.expiring') }}" class="btn btn-danger">
                                <i class="fas fa-clock"></i> Expiring Soon
                            </a>
        </div>
    </div>

                    <!-- Inventory Table -->
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
                                    <td>
                                        <span class="badge {{ $inventory->isLowStock() ? 'badge-warning' : 'badge-success' }}">
                                            {{ $inventory->quantity }}
                                        </span>
                                    </td>
                                    <td>{{ $inventory->unit }}</td>
                                    <td>{{ $inventory->location ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $inventory->status === 'active' ? 'success' : ($inventory->status === 'inactive' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($inventory->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $inventory->last_restock_date ? $inventory->last_restock_date->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#adjustStockModal{{ $inventory->id }}">
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
                                                            <button type="button" class="close" data-dismiss="modal">
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
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $inventories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const input = this.value.toLowerCase();
            const rows = document.querySelectorAll('#inventoryTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(input) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
@endsection 