@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/inventory.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Inventory Management</h1>
            <div class="card">
                <div class="card-header">
                    <div class="card-tools d-none"><!-- hidden, moved button below --></div>
                </div>
                <div class="card-body">
                    <div class="row stat-row mb-4">
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

                    <div class="row mb-3">
                        <div class="col-md-9">
                            <div class="input-group">
                                <input type="text" id="searchInput" class="form-control" placeholder="Search inventory...">
                                <div class="input-group-append d-flex align-items-center">
                                    <button class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <a href="{{ route('inventory.create') }}" class="btn btn-primary ml-2">
                                        <i class="fas fa-plus"></i> Add New Item
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-right">
                            <a href="{{ route('inventory.low-stock') }}" class="btn btn-warning">
                                <i class="fas fa-exclamation-triangle"></i> Low Stock
                            </a>
                            <a href="{{ route('inventory.expiring') }}" class="btn btn-danger">
                                <i class="fas fa-clock"></i> Expiring Soon
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="inventoryTable">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Category</th>
                                    @foreach($warehouses as $warehouse)
                                        <th>{{ $warehouse->name }} Stock</th>
                                    @endforeach
                                    <th>Total Quantity</th>
                                    <th>Unit</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($materials as $material)
                                <tr>
                                    <td>{{ $material->name }}</td>
                                    <td>{{ $material->category->name ?? '-' }}</td>
                                    @foreach($warehouses as $warehouse)
                                        <td>{{ $material->warehouse_stocks[$warehouse->id] ?? 0 }}</td>
                                    @endforeach
                                    <td>{{ $material->total_stock }}</td>
                                    <td>{{ $material->unit }}</td>
                                    <td>
                                        @php
                                            $threshold = $material->minimum_stock ?? 0;
                                        @endphp
                                        @if($material->total_stock <= 0)
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @elseif($material->total_stock < $threshold)
                                            <span class="badge bg-warning text-dark">Low Stock</span>
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('purchase-requests.create', ['material_id' => $material->id]) }}" class="btn btn-sm btn-success" title="Request Restock">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                            <a href="{{ route('inventory.edit', $material) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('inventory.destroy', $material) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this item?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $materials->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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