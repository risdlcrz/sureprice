@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body, .container {
    background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%) !important;
    font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
}
h1, .h3 {
    font-family: 'Inter', Arial, sans-serif;
    font-weight: 700;
    letter-spacing: 0.01em;
    color: #198754;
    font-size: 2.2rem;
    margin-bottom: 2rem;
}
.card {
    border: none;
    border-radius: 1.25rem;
    box-shadow: 0 8px 32px 0 rgba(44,62,80,0.10), 0 1.5px 6px rgba(44,62,80,0.04);
    margin-bottom: 1.5rem;
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(8px);
    transition: box-shadow 0.2s, background 0.2s, transform 0.2s;
}
.card:hover {
    box-shadow: 0 16px 48px 0 rgba(44,62,80,0.16), 0 2px 8px rgba(44,62,80,0.08);
    background: rgba(255,255,255,0.97);
    transform: translateY(-4px) scale(1.02);
}
.table th {
    font-weight: 600;
    color: #495057;
    background: #f8fafc;
    border-top: none;
    text-align: center;
}
.table-hover tbody tr:hover {
    background: #f4faff;
    transition: background 0.2s;
}
.table td, .table th {
    vertical-align: middle;
    text-align: center;
}
.badge {
    font-size: 0.95em;
    padding: 0.5em 1em;
    border-radius: 0.7em;
    font-weight: 600;
    letter-spacing: 0.01em;
    box-shadow: 0 1px 4px #38b6ff22;
}
.btn-success, .btn-primary, .btn-outline-primary, .btn-outline-info {
    font-weight: 600;
    border-radius: 2rem;
    padding: 0.5em 1.5em;
    font-size: 1.08em;
    letter-spacing: 0.01em;
    box-shadow: 0 2px 8px #2196f322;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.btn-success {
    background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%) !important;
    color: #fff !important;
    border: none;
}
.btn-success:hover {
    background: linear-gradient(90deg, #38f9d7 0%, #43e97b 100%) !important;
    color: #fff;
    box-shadow: 0 4px 16px #43e97b44;
}
.btn-primary {
    background: linear-gradient(90deg, #2196f3 0%, #21cbf3 100%) !important;
    color: #fff !important;
    border: none;
}
.btn-primary:hover {
    background: linear-gradient(90deg, #21cbf3 0%, #2196f3 100%) !important;
    color: #fff;
    box-shadow: 0 4px 16px #2196f344;
}
.btn-outline-primary {
    border: 2px solid #2196f3;
    color: #2196f3;
    background: #fff;
}
.btn-outline-primary:hover {
    background: linear-gradient(90deg, #2196f3 0%, #21cbf3 100%) !important;
    color: #fff !important;
    border: none;
    box-shadow: 0 4px 16px #2196f344;
}
.btn-outline-info {
    border: 2px solid #21cbf3;
    color: #21cbf3;
    background: #fff;
}
.btn-outline-info:hover {
    background: linear-gradient(90deg, #2196f3 0%, #21cbf3 100%) !important;
    color: #fff !important;
    border: none;
    box-shadow: 0 4px 16px #2196f344;
}
.form-select, .form-control {
    padding: 0.7rem 1.2rem;
    border-radius: 1.1rem;
    border: 1.5px solid #ced4da;
    font-size: 1.08em;
    background: #f8fafc;
    transition: border 0.2s, box-shadow 0.2s;
}
.form-select:focus, .form-control:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem #19875422;
}
</style>
@endpush

@section('content')
<div class="container py-5">
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Inventory Management</h1>
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#addStockModal">
                <i class="fas fa-plus me-1"></i> Add Stock
            </button>
            <button type="button" class="btn btn-outline-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#adjustStockModal">
                <i class="fas fa-edit me-1"></i> Adjust Stock
            </button>
        </div>
    </div>

    <!-- Warehouse Selector -->
    <form method="GET" action="{{ route('warehouse.inventory.index') }}" class="mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label for="warehouse_id" class="form-label">Warehouse</label>
                <select name="warehouse_id" id="warehouse_id" class="form-select" onchange="this.form.submit()">
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ $warehouseId == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('warehouse.inventory.index') }}" method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="warehouse_id" value="{{ $warehouseId }}">
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <label for="stock_status" class="form-label">Stock Status</label>
                    <select name="stock_status" id="stock_status" class="form-select">
                        <option value="">All Status</option>
                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                        <option value="normal" {{ request('stock_status') == 'normal' ? 'selected' : '' }}>Normal</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Search by name or code">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i> Apply Filters</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Material</th>
                        <th>Category</th>
                        <th>Code</th>
                        <th>Supplier</th>
                        <th>Current Stock</th>
                        <th>Threshold</th>
                        <th>Status</th>
                        <th>Warehouse</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paginatedStocks as $row)
                        @php
                            $material = $row->material;
                            $currentStock = $row->current_stock ?? 0;
                            $threshold = $row->threshold ?? 0;
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $material->name ?? 'N/A' }}</td>
                            <td>{{ $material->category->name ?? '-' }}</td>
                            <td>{{ $material->code ?? '-' }}</td>
                            <td>-</td>
                            <td>{{ $currentStock }}</td>
                            <td>{{ $threshold }}</td>
                            <td>
                                @if($currentStock <= 0)
                                    <span class="badge rounded-pill bg-danger">Out of Stock</span>
                                @elseif($currentStock < $threshold)
                                    <span class="badge rounded-pill bg-warning text-dark">Low Stock</span>
                                @else
                                    <span class="badge rounded-pill bg-success">Normal</span>
                                @endif
                            </td>
                            <td>-</td>
                            <td>
                                <a href="{{ route('warehouse.inventory.history', ['material' => $material->id, 'warehouse_id' => $row->warehouse_id]) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-history"></i> View History
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">No materials found in this warehouse.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $paginatedStocks->appends(request()->except('page'))->links() }}
        </div>
    </div>
</div>

<!-- Add Stock Modal -->
<div class="modal fade" id="addStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('warehouse.inventory.add-stock') }}" method="POST">
                @csrf
                <input type="hidden" name="warehouse_id" value="{{ $warehouseId }}">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-1"></i> Add Stock</h5>
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
                    <button type="submit" class="btn btn-success">Add Stock</button>
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
                <input type="hidden" name="warehouse_id" value="{{ $warehouseId }}">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-1"></i> Adjust Stock</h5>
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
                        <label for="adjust_threshold" class="form-label">Threshold</label>
                        <input type="number" name="threshold" id="adjust_threshold" class="form-control" min="0" 
                            value="{{ old('threshold', isset($stock) && $stock->threshold ? $stock->threshold : (isset($stock) ? floor($stock->current_stock * 0.2) : 0)) }}">
                        <small class="text-muted">Default is 20% of current stock. You can edit this value.</small>
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
                <h5 class="modal-title"><i class="fas fa-history me-1"></i> Stock Movement History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped">
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
            new bootstrap.Modal(document.getElementById('stockHistoryModal')).show();
        });
}
</script>
@endpush
@endsection 