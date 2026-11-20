@extends('layouts.app')

@section('content')
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
@vite('resources/css/pages/warehouse-reports-analytics.css')
@endpush
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.body.classList.add('analytics-bg');
});
</script>
<div class="container py-4 fadein-analytics">
<h1 class="h3 mb-4 text-center page-title">Warehouse Analytics & Trends</h1>
    <div class="d-flex justify-content-between align-items-center mb-4">
        
        <a href="{{ route('warehouse.reports.analytics.pdf', request()->query()) }}" class="btn btn-outline-primary d-none d-lg-inline-block">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </a>
    </div>

    <!-- Warehouse Selector -->
    <div class="glass-card p-3 mb-4">
        <form method="GET" action="{{ route('warehouse.reports.analytics') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="warehouse_id" class="form-label fw-semibold">Select Warehouse</label>
                <select name="warehouse_id" id="warehouse_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Warehouses</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ $selectedWarehouseId == $warehouse->id ? 'selected' : '' }}>
                            {{ $warehouse->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="row mb-4 g-3">
        <div class="col-12 col-md-4">
            <div class="glass-card summary-stat">
                <div class="stat-label">Total Materials</div>
                <div class="stat-value">{{ $stocks->pluck('material')->unique()->count() }}</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="glass-card summary-stat">
                <div class="stat-label">Total Stock</div>
                <div class="stat-value">{{ number_format($stocks->sum('current_stock'), 0) }}</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="glass-card summary-stat">
                <div class="stat-label">Most Used Material</div>
                <div class="stat-value">
                    {{ $mostUsedByProject->count() ? ($mostUsedByProject->first()->material->name ?? 'Unknown') : '-' }}
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <div class="glass-card h-100 p-3">
                <div class="mb-3">
                    <h5 class="fw-semibold mb-0">Current Inventory Levels</h5>
                </div>
                <canvas id="inventoryLevelsChart" height="250"></canvas>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="glass-card h-100 p-3">
                <div class="mb-3">
                    <h5 class="fw-semibold mb-0">Most Used Materials per Project</h5>
                </div>
                <canvas id="mostUsedMaterialsChart" height="250"></canvas>
            </div>
        </div>
        <div class="col-12">
            <div class="glass-card h-100 p-3">
                <div class="mb-3">
                    <h5 class="fw-semibold mb-0">Monthly Usage Trends</h5>
                </div>
                <canvas id="monthlyTrendsChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Floating PDF Download Button -->
    <a href="{{ route('warehouse.reports.analytics.pdf', request()->query()) }}" class="fab-download d-lg-none" title="Download PDF">
        <i class="fas fa-file-pdf"></i>
    </a>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>window.__page = Object.assign(window.__page || {}, { analytics1_0: {{ Js::from($stocks->pluck("material.name")) }}, analytics1_1: {{ Js::from($stocks->pluck("current_stock")) }}, analytics1_2: {{ Js::from($mostUsedByProject) }}, analytics1_3: {{ Js::from($monthlyTrends) }} });</script>
@vite('resources/js/pages/warehouse-reports-analytics.js')
@endpush
@endsection 