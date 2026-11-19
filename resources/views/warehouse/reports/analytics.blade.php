@extends('layouts.app')

@section('content')
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body.analytics-bg {
    background: linear-gradient(135deg, #e8f0ef 0%, #f8fafc 100%) !important;
    font-family: 'Inter', sans-serif;
}
.fadein-analytics {
    animation: fadeIn 1.2s cubic-bezier(.39,.575,.56,1.000);
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(24px); }
    to { opacity: 1; transform: translateY(0); }
}
.glass-card {
    background: rgba(255,255,255,0.85);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.10);
    border-radius: 1.5rem;
    border: 1px solid rgba(255,255,255,0.18);
    backdrop-filter: blur(8px);
    transition: box-shadow 0.2s, transform 0.2s;
}
.glass-card:hover {
    box-shadow: 0 16px 40px 0 rgba(31, 38, 135, 0.18);
    transform: translateY(-2px) scale(1.01);
}
.summary-stat {
    text-align: center;
    padding: 2rem 0 1.5rem 0;
    border-radius: 1.5rem;
    box-shadow: 0 2px 12px 0 rgba(31, 38, 135, 0.06);
    background: rgba(255,255,255,0.92);
    transition: box-shadow 0.2s, transform 0.2s;
}
.summary-stat:hover {
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12);
    transform: translateY(-2px) scale(1.01);
}
.summary-stat .stat-label {
    font-size: 1.05rem;
    color: #64748b;
    font-weight: 600;
    letter-spacing: 0.01em;
}
.summary-stat .stat-value {
    font-size: 2.3rem;
    font-weight: 700;
    color: #1e293b;
    letter-spacing: 0.01em;
}
label.form-label {
    font-weight: 600;
    color: #198754;
}
.fab-download {
    position: fixed;
    bottom: 32px;
    right: 32px;
    z-index: 1000;
    border-radius: 50%;
    box-shadow: 0 4px 16px rgba(54, 162, 235, 0.18);
    background: linear-gradient(135deg, #3b82f6 60%, #06b6d4 100%);
    color: #fff;
    width: 64px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    transition: background 0.2s;
}
.fab-download:hover {
    background: linear-gradient(135deg, #2563eb 60%, #0ea5e9 100%);
    color: #fff;
    text-decoration: none;
}
</style>
@endpush
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.body.classList.add('analytics-bg');
});
</script>
<div class="container py-4 fadein-analytics">
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Warehouse Analytics & Trends</h1>
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