@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Warehouse Dashboard</h1>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Materials Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-gray-600 text-sm">Total Materials</h2>
                    <p class="text-2xl font-semibold">{{ $totalMaterials }}</p>
                </div>
            </div>
        </div>

        <!-- Stock Value Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-gray-600 text-sm">Total Stock Value</h2>
                    <p class="text-2xl font-semibold">${{ number_format($stockValue, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Deliveries Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-gray-600 text-sm">Pending Deliveries</h2>
                    <p class="text-2xl font-semibold">{{ $pendingDeliveries->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Low Stock Materials Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-gray-600 text-sm">Low Stock Materials</h2>
                    <p class="text-2xl font-semibold">{{ $lowStockMaterials->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Pending Deliveries -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold">Pending Deliveries</h2>
            </div>
            <div class="p-6">
                @if($pendingDeliveries->isEmpty())
                    <p class="text-gray-500 text-center py-4">No pending deliveries</p>
                @else
                    <div class="space-y-4">
                        @foreach($pendingDeliveries as $delivery)
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-medium">Delivery #{{ $delivery->delivery_number }}</h3>
                                        <p class="text-sm text-gray-600">Expected: {{ $delivery->expected_date->format('M d, Y') }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                        {{ ucfirst($delivery->status) }}
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600">{{ $delivery->items->count() }} items</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Stock Movements -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold">Recent Stock Movements</h2>
            </div>
            <div class="p-6">
                @if($recentMovements->isEmpty())
                    <p class="text-gray-500 text-center py-4">No recent stock movements</p>
                @else
                    <div class="space-y-4">
                        @foreach($recentMovements as $movement)
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-medium">{{ $movement->material->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $movement->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $movement->type === 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $movement->type === 'in' ? 'In' : 'Out' }}
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600">Quantity: {{ $movement->quantity }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Low Stock Materials -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold">Low Stock Materials</h2>
            </div>
            <div class="p-6">
                @if($lowStockMaterials->isEmpty())
                    <p class="text-gray-500 text-center py-4">No low stock materials</p>
                @else
                    <div class="space-y-4">
                        @foreach($lowStockMaterials as $material)
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-medium">{{ $material->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $material->category->name }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                        Critical
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600">
                                        Current Stock: {{ $material->stock }} / Minimum: {{ $material->minimum_stock }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Monthly Stock Movements Chart -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold">Monthly Stock Movements</h2>
            </div>
            <div class="p-6">
                <canvas id="monthlyMovementsChart" height="300"></canvas>
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
                    borderColor: 'rgb(34, 197, 94)',
                    tension: 0.1
                },
                {
                    label: 'Outgoing',
                    data: outgoingData,
                    borderColor: 'rgb(239, 68, 68)',
                    tension: 0.1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush
@endsection 