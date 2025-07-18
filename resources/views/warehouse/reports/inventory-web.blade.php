@extends('layouts.app')

@section('content')
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #e8f0ef 0%, #f8fafc 100%);
    font-family: 'Inter', sans-serif;
}
.card {
    background: rgba(255,255,255,0.85);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.10);
    border-radius: 24px;
    border: 1px solid rgba(255,255,255,0.18);
    backdrop-filter: blur(6px);
    transition: box-shadow 0.2s, transform 0.2s;
}
.card:hover {
    box-shadow: 0 12px 32px 0 rgba(31, 38, 135, 0.16);
    transform: translateY(-2px) scale(1.01);
}
.card-title, h1.h3 {
    font-weight: 700;
    color: #198754;
    letter-spacing: 0.01em;
}
.btn-primary, .btn-success {
    background: linear-gradient(90deg, #56ccf2 0%, #2f80ed 100%);
    color: #fff;
    border: none;
    font-weight: 600;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(44,62,80,0.04);
    padding-left: 1.5rem;
    padding-right: 1.5rem;
}
.btn-primary:hover, .btn-success:hover {
    filter: brightness(0.95);
}
.form-select, .form-control {
    border-radius: 12px;
    font-size: 1rem;
    box-shadow: 0 1px 4px rgba(44,62,80,0.04);
}
label.form-label {
    font-weight: 600;
    color: #198754;
}
.table {
    border-radius: 16px;
    overflow: hidden;
    background: transparent;
}
.table thead th {
    background: rgba(25,135,84,0.08);
    color: #198754;
    font-weight: 600;
    border: none;
}
.table-hover tbody tr:hover {
    background: #e6f4ea;
    transition: background 0.2s;
}
.table-primary {
    background: #e3f2fd !important;
    font-weight: 600;
    color: #1976d2;
}
.badge.bg-success {
    background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
    color: #fff;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(67,233,123,0.08);
}
.badge.bg-warning {
    background: linear-gradient(90deg, #f7971e 0%, #ffd200 100%);
    color: #fff;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(247,151,30,0.08);
}
.badge.bg-danger {
    background: linear-gradient(90deg, #ff5858 0%, #f09819 100%);
    color: #fff;
    font-weight: 600;
}
.animated-empty {
    animation: fadeIn 1s ease-in;
    font-size: 1.1rem;
    letter-spacing: 0.01em;
    opacity: 0.7;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 0.7; transform: translateY(0); }
}
</style>
@endpush
<div class="container py-4">
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Inventory Report</h1>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('warehouse.reports.inventory.pdf', request()->all()) }}" class="btn btn-primary">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('warehouse.reports.inventory') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="warehouse_id" class="form-label">Warehouse</label>
                        <select name="warehouse_id" id="warehouse_id" class="form-select" onchange="this.form.submit()">
                            @if(isset($warehouses))
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ (isset($selectedWarehouseId) && $selectedWarehouseId == $warehouse->id) ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <noscript><button type="submit" class="btn btn-success">Filter</button></noscript>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Inventory Levels</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Category</th>
                        <th>Supplier</th>
                        <th>Current Stock</th>
                        <th>Minimum Stock</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($stocks))
                        @forelse($stocks->groupBy('material_id') as $materialId => $materialStocks)
                        @php
                            $material = $materialStocks->first()->material;
                            $totalStock = $materialStocks->sum('current_stock');
                        @endphp
                        <tr class="table-primary">
                            <td>{{ $material->name ?? 'N/A' }}</td>
                            <td>{{ $material->category->name ?? '-' }}</td>
                            <td colspan="3"><strong>Total Stock: {{ $totalStock }}</strong></td>
                            <td></td>
                        </tr>
                        @foreach($materialStocks as $stock)
                        <tr>
                            <td></td>
                            <td></td>
                            <td>{{ $stock->supplier->company_name ?? '-' }}</td>
                            <td>{{ $stock->current_stock }}</td>
                            <td>
                                @php $minStock = $stock->threshold > 0 ? $stock->threshold : floor($stock->current_stock * 0.2); @endphp
                                {{ $minStock }}
                                @if($stock->threshold <= 0)
                                    <span class="text-muted">(20%)</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $status = 'Normal';
                                    $color = 'success';
                                    if ($stock->current_stock == 0) {
                                        $status = 'Out of Stock';
                                        $color = 'danger';
                                    } elseif ($stock->current_stock < $minStock) {
                                        $status = 'Low Stock';
                                        $color = 'warning';
                                    }
                                @endphp
                                <span class="badge bg-{{ $color }}">{{ $status }}</span>
                            </td>
                        </tr>
                        @endforeach
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted animated-empty">No inventory data found for the selected warehouse.</td>
                        </tr>
                        @endforelse
                    @else
                    <tr>
                        <td colspan="6" class="text-center text-muted animated-empty">No inventory data available.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 