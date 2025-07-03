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