@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1 class="display-5 fw-bold">Welcome, {{ auth()->user()->getDisplayNameAttribute() }}</h1>
        <p class="text-muted">Supplier Dashboard</p>
    </div>

    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title h5">Total Materials</h3>
                    <p class="display-6 text-primary mb-0">{{ $materials->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title h5">Active Quotations</h3>
                    <p class="display-6 text-success mb-0">{{ $activeQuotations->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title h5">Total Sales</h3>
                    <p class="display-6 text-info mb-0">₱{{ number_format($totalSales, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Overview Section -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Monthly Sales Trend</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlySalesChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Sales Performance</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Current Period (6 months)</span>
                            <span class="fw-bold">₱{{ number_format($salesTrend['current_period'] ?? 0, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Previous Period (6 months)</span>
                            <span class="fw-bold">₱{{ number_format($salesTrend['previous_period'] ?? 0, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Growth Rate</span>
                            <span class="fw-bold {{ ($salesTrend['percentage_change'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ ($salesTrend['percentage_change'] ?? 0) >= 0 ? '+' : '' }}{{ $salesTrend['percentage_change'] ?? 0 }}%
                            </span>
                        </div>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar {{ ($salesTrend['percentage_change'] ?? 0) >= 0 ? 'bg-success' : 'bg-danger' }}" 
                             style="width: {{ min(abs($salesTrend['percentage_change'] ?? 0), 100) }}%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Selling Materials -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Top Selling Materials</h5>
                </div>
                <div class="card-body">
                    @if(count($topSellingMaterials) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>Total Sales</th>
                                        <th>Order Count</th>
                                        <th>Average Order Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topSellingMaterials as $material)
                                    <tr>
                                        <td>
                                            <span class="me-2">
                                                <i class="bi bi-box-seam text-primary"></i>
                                            </span>
                                            <span class="fw-semibold">{{ $material->name }}</span>
                                        </td>
                                        <td class="fw-bold text-success">₱{{ number_format($material->total_sales, 2) }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $material->order_count }}</span>
                                        </td>
                                        <td>₱{{ number_format($material->total_sales / $material->order_count, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-graph-down display-4"></i>
                            <p class="mt-2">No sales data available yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-4">
        <h2 class="h4 mb-3">Quick Actions</h2>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title h5">Add New Material</h3>
                        <p class="card-text text-muted">Add a new material to your catalog</p>
                        <a href="{{ route('supplier.materials.create') }}" class="btn btn-primary">Add Material</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title h5">Respond to Quotations</h3>
                        <p class="card-text text-muted">View and respond to quotation requests</p>
                        <a href="{{ route('supplier.quotations.index') }}" class="btn btn-primary">View Quotations</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Materials -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Your Recent Materials</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials->take(5) as $material)
                    <tr>
                        <td>
                            <span class="me-2">
                                <i class="bi bi-box-seam text-primary"></i>
                            </span>
                            <span class="fw-semibold">{{ $material->name }}</span>
                        </td>
                        <td>{{ $material->category->name ?? '-' }}</td>
                        <td>
                            @if($material->stock <= 10)
                                <span class="badge bg-danger">{{ $material->stock }}</span>
                            @else
                                <span class="badge bg-success">{{ $material->stock }}</span>
                            @endif
                        </td>
                        <td>₱{{ number_format($material->price, 2) }}</td>
                        <td>
                            <a href="{{ route('supplier.materials.edit', $material) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No materials found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($materials->count() > 5)
        <div class="card-footer bg-white border-0">
            <a href="{{ route('supplier.materials.index') }}" class="btn btn-link p-0">View all materials &rarr;</a>
        </div>
        @endif
    </div>

    <!-- Recent Quotations -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Recent Quotations</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Quotation #</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeQuotations->take(5) as $quotation)
                    <tr>
                        <td>{{ $quotation->quotation_number }}</td>
                        <td>
                            <span class="badge {{ $quotation->status === 'pending' ? 'bg-warning' : ($quotation->status === 'accepted' ? 'bg-success' : 'bg-secondary') }}">
                                {{ ucfirst($quotation->status) }}
                            </span>
                        </td>
                        <td>{{ $quotation->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('supplier.quotations.show', $quotation) }}" class="btn btn-sm btn-primary">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No quotations found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($activeQuotations->count() > 5)
        <div class="card-footer">
            <a href="{{ route('supplier.quotations.index') }}" class="btn btn-link">View all quotations →</a>
        </div>
        @endif
    </div>

    <!-- Active Quotations Table -->
    @if($activeQuotations->count() > 0)
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0">Your Active Quotations</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Quotation #</th>
                        <th>Client</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeQuotations as $quotation)
                    <tr>
                        <td>{{ $quotation->request_number ?? $quotation->id }}</td>
                        <td>{{ $quotation->user->getDisplayNameAttribute() ?? 'N/A' }}</td>
                        <td><span class="badge bg-warning text-dark">{{ ucfirst($quotation->status) }}</span></td>
                        <td>
                            <a href="{{ route('supplier.quotations.respond', $quotation) }}" class="btn btn-sm btn-primary">View / Respond</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Performance Card -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Your Performance</h5>
            <a href="{{ route('supplier.ranking') }}" class="btn btn-sm btn-primary">View Detailed Performance</a>
        </div>
        <div class="card-body">
            <p><strong>Overall Score:</strong> {{ $ranking->score ?? 'N/A' }}</p>
            <p><strong>Completed Orders:</strong> {{ $completedOrders ?? 0 }}</p>
            <p><strong>On-Time Delivery Rate:</strong> {{ isset($onTimeRate) ? ($onTimeRate . '%') : 'N/A' }}</p>
            <p><strong>Average Rating:</strong> {{ $averageRating ?? 'N/A' }}</p>
        </div>
    </div>
</div>

@push('styles')
{{-- Extracted to resources/css/supplier-dashboard.css --}}
@vite('resources/css/supplier-dashboard.css')
@endpush
@push('scripts')
{{-- Extracted to resources/js/supplier-dashboard.js --}}
@vite('resources/js/supplier-dashboard.js')
@endpush
@endsection 