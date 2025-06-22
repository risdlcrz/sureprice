@extends('layouts.app')

@push('styles')
<style>
    #inventoryTable .badge {
        position: static;
        display: inline-block;
        transform: none;
    }

    .card-body .badge {
        position: absolute;
        top: 20px;
        right: 20px;
    }

    .inventory-status-badge {
        position: static !important;
        display: inline-block !important;
        transform: none !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Inventory Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('inventory.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Item
                        </a>
                    </div>
                </div>
                <div class="card-body">
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
                                            <span class="badge inventory-status-badge {{ $statusClass }}">
                                                {{ ucfirst($inventory->status) }}
                                            </span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $inventory->last_restock_date ? $inventory->last_restock_date->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('purchase-requests.create', ['material_id' => $inventory->material->id]) }}" class="btn btn-sm btn-success" title="Request Restock">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                            <a href="{{ route('inventory.edit', $inventory) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('inventory.destroy', $inventory) }}" method="POST" class="d-inline">
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