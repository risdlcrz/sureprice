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

    .table {
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid #e3e8ee;
        background: #fff;
        box-shadow: 0 4px 24px rgba(56,189,248,0.07), 0 1.5px 6px rgba(0,0,0,0.03);
        transition: box-shadow 0.25s;
    }
    .table th, .table td {
        vertical-align: middle;
        padding: 18px 24px;
        font-size: 1.08rem;
        border: none;
        transition: background 0.22s, color 0.18s;
    }
    .table th {
        background: #f7fafc;
        font-weight: 700;
        color: #1a7f4e;
        border-bottom: 2px solid #e3e8ee;
        letter-spacing: 0.01em;
    }
    .table tbody tr:nth-child(even) {
        background: #f4f7fa;
    }
    .table tbody tr:hover {
        background: #e0f2fe;
        box-shadow: 0 2px 8px rgba(56,189,248,0.10);
        z-index: 1;
        position: relative;
    }
    .table-responsive {
        border-radius: 20px;
        box-shadow: 0 2px 8px rgba(56,189,248,0.06);
        background: #f8fafc;
        padding: 8px 0;
    }

    .small-box {
        border-radius: 18px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.08);
        padding: 24px 18px 18px 18px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        min-height: 110px;
        transition: box-shadow 0.2s, transform 0.2s;
        position: relative;
        margin-bottom: 12px;
    }
    .small-box .inner {
        width: 100%;
        text-align: left;
    }
    .small-box h3 {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0 0 4px 0;
        color: #222;
    }
    .small-box p {
        font-size: 1.08rem;
        margin: 0;
        color: #444;
        font-weight: 500;
        letter-spacing: 0.01em;
    }
    .small-box .icon {
        position: absolute;
        bottom: 16px;
        right: 18px;
        font-size: 2.1rem;
        opacity: 0.18;
    }
    .small-box.bg-info {
        background: linear-gradient(90deg, #67e8f9 60%, #38bdf8 100%);
        color: #155e75;
    }
    .small-box.bg-warning {
        background: linear-gradient(90deg, #fde68a 60%, #fbbf24 100%);
        color: #92400e;
    }
    .small-box.bg-danger {
        background: linear-gradient(90deg, #fca5a5 60%, #ef4444 100%);
        color: #991b1b;
    }
    .small-box:hover {
        box-shadow: 0 8px 32px rgba(56,189,248,0.13);
        transform: translateY(-2px) scale(1.03);
    }
    /* Responsive improvements */
    @media (max-width: 991.98px) {
        .small-box {
            min-height: 80px;
            padding: 16px 10px 10px 10px;
        }
        .table th, .table td {
            padding: 10px 8px;
            font-size: 0.98rem;
        }
        .input-group, .input-group-append {
            flex-direction: column !important;
            align-items: stretch !important;
        }
        .input-group .form-control, .input-group .btn, .input-group-append .btn {
            width: 100% !important;
            margin-bottom: 8px;
        }
        .btn-group {
            flex-direction: column;
        }
        .btn-group .btn {
            margin-bottom: 4px;
        }
    }
    @media (max-width: 575.98px) {
        .table th, .table td {
            font-size: 0.90rem;
            padding: 6px 4px;
        }
        .small-box h3 {
            font-size: 1.3rem;
        }
        .small-box p {
            font-size: 0.95rem;
        }
    }
    /* Responsive stacked table for mobile */
    @media (max-width: 767.98px) {
        #inventoryTable thead {
            display: none;
        }
        #inventoryTable, #inventoryTable tbody, #inventoryTable tr, #inventoryTable td {
            display: block;
            width: 100%;
        }
        #inventoryTable tr {
            margin-bottom: 1rem;
            border: 1px solid #e3e8ee;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(56,189,248,0.06);
            background: #fff;
            padding: 10px 0;
        }
        #inventoryTable td {
            text-align: left;
            padding: 10px 16px;
            position: relative;
            border: none;
        }
        #inventoryTable td:before {
            content: attr(data-label) ": ";
            font-weight: 600;
            color: #1a7f4e;
            display: block;
            margin-bottom: 2px;
            font-size: 0.98rem;
        }
        .btn-group {
            width: 100%;
        }
        .btn-group .btn {
            width: 100%;
            margin-bottom: 6px;
        }
    }
</style>
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
                                    <td data-label="Material">{{ $material->name }}</td>
                                    <td data-label="Category">{{ $material->category->name ?? '-' }}</td>
                                    @foreach($warehouses as $warehouse)
                                        <td data-label="{{ $warehouse->name }} Stock">{{ $material->warehouse_stocks[$warehouse->id] ?? 0 }}</td>
                                    @endforeach
                                    <td data-label="Total Quantity">{{ $material->total_stock }}</td>
                                    <td data-label="Unit">{{ $material->unit }}</td>
                                    <td data-label="Status">
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
                                    <td data-label="Actions">
                                        <div class="btn-group">
                                            <a href="{{ route('purchase-requests.create', ['material_id' => $material->id]) }}" class="btn btn-sm btn-success" title="Request Restock">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                            @if($material->primary_inventory)
                                                <a href="{{ route('inventory.edit', $material->primary_inventory) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @else
                                                {{-- fallback in case inventory record is missing --}}
                                                <a href="{{ route('inventory.create') }}" class="btn btn-sm btn-primary" title="Create inventory record">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
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