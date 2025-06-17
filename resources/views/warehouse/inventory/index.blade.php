@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Inventory Management</h1>
        <div class="flex space-x-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStockModal">
                Add Stock
            </button>
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#adjustStockModal">
                Adjust Stock
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <form action="{{ route('warehouse.inventory.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="category" class="form-label">Category</label>
                    <select name="category" id="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="stock_status" class="form-label">Stock Status</label>
                    <select name="stock_status" id="stock_status" class="form-select">
                        <option value="">All Status</option>
                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                        <option value="normal" {{ request('stock_status') == 'normal' ? 'selected' : '' }}>Normal</option>
                    </select>
                </div>
                <div>
                    <label for="search" class="form-label">Search</label>
                    <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Search by name or code">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="btn btn-primary w-full">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Minimum Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($materials as $material)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $material->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $material->category->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $material->code }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $material->stock }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $material->minimum_stock }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($material->stock == 0)
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Out of Stock</span>
                                @elseif($material->stock < $material->minimum_stock)
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Low Stock</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Normal</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <button type="button" class="btn btn-sm btn-primary" onclick="showStockHistory({{ $material->id }})">
                                    View History
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">No materials found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">
            {{ $materials->links() }}
        </div>
    </div>
</div>

<!-- Add Stock Modal -->
<div class="modal fade" id="addStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('warehouse.inventory.add-stock') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="material_id" class="form-label">Material</label>
                        <select name="material_id" id="material_id" class="form-select" required>
                            <option value="">Select Material</option>
                            @foreach($materials as $material)
                                <option value="{{ $material->id }}">{{ $material->name }} ({{ $material->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Adjust Stock Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('warehouse.inventory.update-stock') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Adjust Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="adjust_material_id" class="form-label">Material</label>
                        <select name="material_id" id="adjust_material_id" class="form-select" required>
                            <option value="">Select Material</option>
                            @foreach($materials as $material)
                                <option value="{{ $material->id }}">{{ $material->name }} ({{ $material->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="adjustment_type" class="form-label">Adjustment Type</label>
                        <select name="adjustment_type" id="adjustment_type" class="form-select" required>
                            <option value="add">Add Stock</option>
                            <option value="remove">Remove Stock</option>
                            <option value="set">Set Stock</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="adjust_quantity" class="form-label">Quantity</label>
                        <input type="number" name="quantity" id="adjust_quantity" class="form-control" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="adjust_notes" class="form-label">Notes</label>
                        <textarea name="notes" id="adjust_notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Adjust Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Stock History Modal -->
<div class="modal fade" id="stockHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Stock Movement History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Previous Stock</th>
                                <th>New Stock</th>
                                <th>Reference</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody id="stockHistoryBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showStockHistory(materialId) {
    // Fetch stock history for the material
    fetch(`/api/materials/${materialId}/stock-history`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('stockHistoryBody');
            tbody.innerHTML = '';
            
            data.forEach(movement => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${new Date(movement.created_at).toLocaleString()}</td>
                    <td>
                        <span class="badge bg-${movement.type === 'in' ? 'success' : 'danger'}">
                            ${movement.type === 'in' ? 'In' : 'Out'}
                        </span>
                    </td>
                    <td>${movement.quantity}</td>
                    <td>${movement.previous_stock}</td>
                    <td>${movement.new_stock}</td>
                    <td>${movement.reference_number}</td>
                    <td>${movement.notes || '-'}</td>
                `;
                tbody.appendChild(row);
            });
            
            // Show the modal
            new bootstrap.Modal(document.getElementById('stockHistoryModal')).show();
        });
}
</script>
@endpush
@endsection 