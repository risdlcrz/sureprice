@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Warehouse Dashboard</h1>

    <!-- Warehouse Logs Card -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card shadow-sm h-100" onclick="window.location.href='{{ route('warehouse.logs') }}'" style="cursor:pointer;">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                        <i class="bi bi-journal-text fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Logs</div>
                        <div class="h5 mb-0">Warehouse Logs</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Materials Card -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                        <i class="bi bi-box-seam fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Materials</div>
                        <div class="h4 mb-0">{{ $totalMaterials }}</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Stock Value Card -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                        <i class="bi bi-cash-stack fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Stock Value</div>
                        <div class="h4 mb-0">₱{{ number_format($stockValue, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pending Deliveries Card -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                        <i class="bi bi-truck fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Pending Deliveries</div>
                        <div class="h4 mb-0">{{ $pendingDeliveries->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Low Stock Materials Card -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                        <i class="bi bi-exclamation-triangle fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Low Stock Materials</div>
                        <div class="h4 mb-0">{{ $lowStockMaterials->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Pending Deliveries -->
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="mb-0">Pending Deliveries</h5>
                </div>
                <div class="card-body">
                    @if($pendingDeliveries->isEmpty())
                        <p class="text-muted text-center py-4">No pending deliveries</p>
                    @else
                        <div class="vstack gap-3">
                            @foreach($pendingDeliveries as $delivery)
                                <div class="border rounded p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-semibold">Delivery #{{ $delivery->delivery_number }}</div>
                                            <div class="text-muted small">Expected: 
                                                @if($delivery->delivery_date)
                                                    {{ $delivery->delivery_date->format('M d, Y') }}
                                                @else
                                                    Not set
                                                @endif
                                            </div>
                                        </div>
                                        <span class="badge bg-warning text-dark">{{ ucfirst($delivery->status) }}</span>
                                    </div>
                                    <div class="mt-2 text-muted small">{{ $delivery->items->count() }} items</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Recent Stock Movements -->
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="mb-0">Recent Stock Movements</h5>
                </div>
                <div class="card-body">
                    @if($recentMovements->isEmpty())
                        <p class="text-muted text-center py-4">No recent stock movements</p>
                    @else
                        <div class="vstack gap-3">
                            @foreach($recentMovements as $movement)
                                <div class="border rounded p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-semibold">{{ $movement->material->name ?? 'Unknown Material' }}</div>
                                            <div class="text-muted small">
                                                @if($movement->created_at)
                                                    {{ $movement->created_at->format('M d, Y H:i') }}
                                                @else
                                                    Date not available
                                                @endif
                                            </div>
                                        </div>
                                        <span class="badge {{ $movement->type === 'in' ? 'bg-success' : 'bg-danger' }}">{{ $movement->type === 'in' ? 'In' : 'Out' }}</span>
                                    </div>
                                    <div class="mt-2 text-muted small">Quantity: {{ $movement->quantity }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Low Stock Materials -->
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="mb-0">Low Stock Materials</h5>
                </div>
                <div class="card-body">
                    @if($lowStockMaterials->isEmpty())
                        <p class="text-muted text-center py-4">No low stock materials</p>
                    @else
                        <div class="vstack gap-3">
                            @foreach($lowStockMaterials as $material)
                                <div class="border rounded p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-semibold">{{ $material->name }}</div>
                                            <div class="text-muted small">{{ $material->category->name ?? 'No Category' }}</div>
                                        </div>
                                        <span class="badge bg-danger">Critical</span>
                                    </div>
                                    <div class="mt-2 text-muted small">Current Stock: {{ $material->current_stock }} / Minimum: {{ $material->minimum_stock }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Monthly Stock Movements Chart -->
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="mb-0">Monthly Stock Movements</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyMovementsChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('monthlyMovementsChart').getContext('2d');
    const monthlyData = JSON.parse('@json($monthlyMovements)');
    const months = monthlyData.map(item => {
        const date = new Date();
        date.setMonth(item.month - 1);
        return date.toLocaleString('default', { month: 'short' });
    });
    const incomingData = monthlyData.map(item => item.incoming);
    const outgoingData = monthlyData.map(item => item.outgoing);
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Incoming',
                    data: incomingData,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Outgoing',
                    data: outgoingData,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            }
        }
    });
});
</script>
@endpush
@endsection 

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body, .container {
    background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%) !important;
    font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
}
h1, .h3 {
    font-family: 'Inter', Arial, sans-serif;
    font-weight: 700;
    letter-spacing: 0.01em;
    color: #198754;
    font-size: 2.2rem;
    margin-bottom: 2rem;
}
.card {
    border: none;
    border-radius: 1.25rem;
    box-shadow: 0 8px 32px 0 rgba(44,62,80,0.10), 0 1.5px 6px rgba(44,62,80,0.04);
    margin-bottom: 1.5rem;
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(8px);
    transition: box-shadow 0.2s, background 0.2s, transform 0.2s;
}
/* Gradient backgrounds for stat cards */
.row.g-4.mb-4 > .col-12.col-md-6.col-lg-3:nth-child(1) .card {
    background: linear-gradient(90deg, #1565c0 0%, #1e88e5 100%) !important;
    color: #fff;
}
.row.g-4.mb-4 > .col-12.col-md-6.col-lg-3:nth-child(2) .card {
    background: linear-gradient(90deg, #11998e 0%, #38ef7d 100%) !important;
    color: #fff;
}
.row.g-4.mb-4 > .col-12.col-md-6.col-lg-3:nth-child(3) .card {
    background: linear-gradient(90deg, #f7971e 0%, #f7971e 80%, #ffd200 100%) !important;
    color: #fff;
}
.row.g-4.mb-4 > .col-12.col-md-6.col-lg-3:nth-child(4) .card {
    background: linear-gradient(90deg, #c33764 0%, #f857a6 100%) !important;
    color: #fff;
}
.card:hover {
    box-shadow: 0 16px 48px 0 rgba(44,62,80,0.16), 0 2px 8px rgba(44,62,80,0.08);
    background: rgba(255,255,255,0.97);
    transform: translateY(-4px) scale(1.02);
}
.card-header {
    background: #fff;
    border-radius: 1.25rem 1.25rem 0 0;
    border-bottom: none;
    padding: 1.5rem 2rem 1rem 2rem;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 1rem;
}
.bg-info, .bg-primary, .bg-success, .bg-warning, .bg-danger {
    border-radius: 50% !important;
    box-shadow: 0 2px 8px 0 rgba(44,62,80,0.10);
    font-size: 1.5em;
    display: flex;
    align-items: center;
    justify-content: center;
}
.badge {
    font-size: 0.95em;
    padding: 0.5em 1em;
    border-radius: 0.7em;
    font-weight: 600;
    letter-spacing: 0.01em;
    box-shadow: 0 1px 4px #38b6ff22;
}
</style>
@endpush