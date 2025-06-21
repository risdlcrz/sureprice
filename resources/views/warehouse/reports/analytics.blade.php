@extends('layouts.app')

@section('content')
<style>
    body.analytics-bg {
        background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%) !important;
    }
    .glass-card {
        background: rgba(255,255,255,0.85);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.10);
        border-radius: 1.25rem;
        border: 1px solid rgba(255,255,255,0.18);
        backdrop-filter: blur(6px);
        transition: box-shadow 0.2s;
    }
    .glass-card:hover {
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.18);
    }
    .summary-stat {
        text-align: center;
        padding: 1.5rem 0;
    }
    .summary-stat .stat-label {
        font-size: 1rem;
        color: #64748b;
    }
    .summary-stat .stat-value {
        font-size: 2.2rem;
        font-weight: 700;
        color: #1e293b;
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.body.classList.add('analytics-bg');
});
</script>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold mb-0" style="letter-spacing: -1px;">Warehouse Analytics & Trends</h1>
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
<script>
// Modern color palette
const palette = [
    '#3b82f6', '#06b6d4', '#f59e42', '#f43f5e', '#a78bfa', '#10b981', '#fbbf24', '#6366f1', '#f87171', '#34d399', '#f472b6', '#60a5fa', '#facc15', '#4ade80', '#818cf8'
];

// Inventory Levels Chart
const inventoryLevelsCtx = document.getElementById('inventoryLevelsChart').getContext('2d');
const inventoryLabels = JSON.parse('@json($stocks->pluck("material.name"))');
const inventoryData = JSON.parse('@json($stocks->pluck("current_stock"))');
new Chart(inventoryLevelsCtx, {
    type: 'bar',
    data: {
        labels: inventoryLabels,
        datasets: [{
            label: 'Current Stock',
            data: inventoryData,
            backgroundColor: palette[0],
            borderRadius: 8,
            maxBarThickness: 32
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#fff',
                titleColor: '#1e293b',
                bodyColor: '#334155',
                borderColor: palette[0],
                borderWidth: 1
            }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: '#e5e7eb' } },
            x: { grid: { display: false } }
        }
    }
});

// Most Used Materials per Project Chart
const mostUsedCtx = document.getElementById('mostUsedMaterialsChart').getContext('2d');
const mostUsedRaw = JSON.parse('@json($mostUsedByProject)');
const projectGroups = {};
mostUsedRaw.forEach(item => {
    if (!projectGroups[item.reference_number]) projectGroups[item.reference_number] = [];
    projectGroups[item.reference_number].push({
        label: item.material ? item.material.name : 'Unknown',
        value: item.total_used
    });
});
const projectLabels = Object.keys(projectGroups);
const materialLabels = [...new Set(mostUsedRaw.map(item => item.material ? item.material.name : 'Unknown'))];
const datasets = projectLabels.map((project, i) => ({
    label: project,
    data: materialLabels.map(mat => {
        const found = projectGroups[project].find(m => m.label === mat);
        return found ? found.value : 0;
    }),
    backgroundColor: palette[i % palette.length],
    borderRadius: 8,
    maxBarThickness: 28
}));
new Chart(mostUsedCtx, {
    type: 'bar',
    data: {
        labels: materialLabels,
        datasets: datasets
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true },
            tooltip: {
                backgroundColor: '#fff',
                titleColor: '#1e293b',
                bodyColor: '#334155',
                borderColor: '#06b6d4',
                borderWidth: 1
            }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: '#e5e7eb' } },
            x: { grid: { display: false } }
        }
    }
});

// Monthly Usage Trends Chart
const monthlyTrendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
const monthlyRaw = JSON.parse('@json($monthlyTrends)');
const months = [...new Set(monthlyRaw.map(item => `${item.year}-${String(item.month).padStart(2, '0')}`))];
const materialNames = [...new Set(monthlyRaw.map(item => item.material ? item.material.name : 'Unknown'))];
const materialMap = {};
materialNames.forEach(name => { materialMap[name] = Array(months.length).fill(0); });
monthlyRaw.forEach(item => {
    const idx = months.indexOf(`${item.year}-${String(item.month).padStart(2, '0')}`);
    const name = item.material ? item.material.name : 'Unknown';
    if (idx !== -1) materialMap[name][idx] = item.total_used;
});
const trendDatasets = materialNames.map((name, i) => ({
    label: name,
    data: materialMap[name],
    borderColor: palette[i % palette.length],
    backgroundColor: palette[i % palette.length] + '33',
    fill: true,
    tension: 0.4,
    pointRadius: 4,
    pointHoverRadius: 7
}));
new Chart(monthlyTrendsCtx, {
    type: 'line',
    data: {
        labels: months,
        datasets: trendDatasets
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true },
            tooltip: {
                backgroundColor: '#fff',
                titleColor: '#1e293b',
                bodyColor: '#334155',
                borderColor: '#f59e42',
                borderWidth: 1
            }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: '#e5e7eb' } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush
@endsection 