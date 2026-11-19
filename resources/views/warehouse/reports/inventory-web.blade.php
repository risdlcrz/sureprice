@extends('layouts.app')

@section('content')
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
label.form-label {
    font-weight: 600;
    color: #198754;
}
.table-primary {
    background: #e3f2fd !important;
    font-weight: 600;
    color: #1976d2;
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
                        <x-empty-row cols="6">No inventory data found for the selected warehouse.</x-empty-row>
                        @endforelse
                    @else
                    <x-empty-row cols="6">No inventory data available.</x-empty-row>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 