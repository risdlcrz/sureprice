@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Inventory Report</h1>
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
                        <th>Current Stock</th>
                        <th>Minimum Stock</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($stocks))
                        @forelse($stocks as $stock)
                        <tr>
                            <td>{{ $stock->material->name ?? 'N/A' }}</td>
                            <td>{{ $stock->material->category->name ?? '-' }}</td>
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
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No inventory data found for the selected warehouse.</td>
                        </tr>
                        @endforelse
                    @else
                    <tr>
                        <td colspan="5" class="text-center text-muted">No inventory data available.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 