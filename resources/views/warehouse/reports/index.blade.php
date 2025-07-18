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
.card-title {
    font-weight: 700;
    color: #198754;
    letter-spacing: 0.01em;
}
.btn-primary, .btn-info {
    background: linear-gradient(90deg, #56ccf2 0%, #2f80ed 100%);
    color: #fff;
    border: none;
    font-weight: 600;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(44,62,80,0.04);
    padding-left: 1.5rem;
    padding-right: 1.5rem;
}
.btn-info {
    background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
}
.btn-primary:hover, .btn-info:hover {
    filter: brightness(0.95);
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
.card-header.bg-white {
    background: rgba(255,255,255,0.85) !important;
    border-top-left-radius: 24px;
    border-top-right-radius: 24px;
    border-bottom: none;
}
</style>
@endpush
<div class="container py-4">
        <h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Warehouse Reports</h1>


    <div class="row">
        <!-- Inventory Report Card -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Inventory Report</h5>
                    <p class="card-text text-muted">Generate detailed inventory reports including stock levels, low stock items, and stock movements.</p>
                    <div class="mt-4">
                        <a href="{{ route('warehouse.reports.inventory') }}" class="btn btn-primary">View</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Movement Report Card -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Stock Movement Report</h5>
                    <p class="card-text text-muted">Track all stock movements including incoming and outgoing deliveries, adjustments, and returns.</p>
                    <div class="mt-4">
                        <a href="{{ route('warehouse.reports.movements') }}" class="btn btn-primary">View</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Report Card -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Delivery Report</h5>
                    <p class="card-text text-muted">View delivery statistics, on-time delivery rates, and delivery performance metrics.</p>
                    <div class="mt-4">
                        <a href="{{ route('warehouse.reports.deliveries') }}" class="btn btn-primary">View</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Material Usage Report Card -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Material Usage Report</h5>
                    <p class="card-text text-muted">Analyze material usage patterns, popular items, and consumption trends.</p>
                    <div class="mt-4">
                        <a href="{{ route('warehouse.reports.usage') }}" class="btn btn-primary">View</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics & Trends Report Card -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Analytics & Trends</h5>
                    <p class="card-text text-muted">Interactive charts for inventory levels, most used materials per project, and monthly usage trends.</p>
                    <div class="mt-4">
                        <a href="{{ route('warehouse.reports.analytics') }}" class="btn btn-info">View Analytics</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Recent Reports</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Report Type</th>
                        <th>Generated By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentReports as $report)
                    @php
                        $downloadRoute = null;
                        switch ($report->type) {
                            case 'warehouse_inventory':
                                $downloadRoute = route('warehouse.reports.inventory.pdf', $report->parameters ?? []);
                                break;
                            case 'warehouse_movements':
                                $downloadRoute = route('warehouse.reports.movements.pdf', $report->parameters ?? []);
                                break;
                            case 'warehouse_deliveries':
                                $downloadRoute = route('warehouse.reports.deliveries.pdf', $report->parameters ?? []);
                                break;
                            case 'warehouse_usage':
                                $downloadRoute = route('warehouse.reports.usage.pdf', $report->parameters ?? []);
                                break;
                            default:
                                $downloadRoute = '#';
                        }
                    @endphp
                    <tr>
                        <td>{{ $report->type }}</td>
                        <td>{{ $report->generated_by->name }}</td>
                        <td>{{ $report->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <a href="{{ $downloadRoute }}" class="btn btn-sm btn-primary">Download</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted animated-empty">No recent reports found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 