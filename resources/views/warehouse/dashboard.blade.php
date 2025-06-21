@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="display-5 mb-4">Warehouse Dashboard</h1>

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
    const monthlyData = @json($monthlyMovements);
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