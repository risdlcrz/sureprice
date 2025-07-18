@extends('layouts.app')

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
}
.card .card-body, .card-footer {
    border-radius: 24px;
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
.badge.bg-success {
    background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
    color: #fff;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(67,233,123,0.08);
}
.badge.bg-primary {
    background: linear-gradient(90deg, #56ccf2 0%, #2f80ed 100%);
    color: #fff;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(44,62,80,0.08);
}
.badge.bg-secondary {
    background: linear-gradient(90deg, #bdc3c7 0%, #2c3e50 100%);
    color: #fff;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(44,62,80,0.08);
}
.badge.bg-info {
    background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
    color: #fff;
    font-weight: 600;
}
.badge.bg-danger {
    background: linear-gradient(90deg, #ff5858 0%, #f09819 100%);
    color: #fff;
    font-weight: 600;
}
.btn-primary.w-100, .btn-primary.btn-sm {
    background: linear-gradient(90deg, #56ccf2 0%, #2f80ed 100%);
    color: #fff;
    border: none;
    font-weight: 600;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(44,62,80,0.04);
}
.btn-primary.w-100:hover, .btn-primary.btn-sm:hover {
    filter: brightness(0.95);
}
.btn-secondary.btn-sm {
    background: #e0e0e0;
    color: #333;
    border: none;
    font-weight: 600;
    border-radius: 8px;
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
.form-select, .form-control {
    border-radius: 12px;
    font-size: 1rem;
    box-shadow: 0 1px 4px rgba(44,62,80,0.04);
}
label.form-label {
    font-weight: 600;
    color: #198754;
}
</style>
@endpush

@section('content')
<div class="container py-4">
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Deliveries Management</h1>
    

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('warehouse.deliveries.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="type" class="form-label">Delivery Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">All Types</option>
                        <option value="incoming" {{ request('type') == 'incoming' ? 'selected' : '' }}>Incoming</option>
                        <option value="outgoing" {{ request('type') == 'outgoing' ? 'selected' : '' }}>Outgoing</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="date_range" class="form-label">Date Range</label>
                    <input type="text" class="form-control" id="date_range" name="date_range" 
                           value="{{ request('date_range') }}" placeholder="Select date range">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Deliveries Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Delivery #</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Items</th>
                        <th>Warehouse</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deliveries as $delivery)
                    <tr>
                        <td>{{ $delivery->delivery_number }}</td>
                        <td>
                            <span class="badge bg-{{ $delivery->type === 'incoming' ? 'success' : 'primary' }}">
                                {{ ucfirst($delivery->type) }}
                            </span>
                        </td>
                        <td>{{ $delivery->delivery_date ? $delivery->delivery_date->format('M d, Y') : 'N/A' }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'pending' => 'secondary',
                                    'processing' => 'primary',
                                    'shipped' => 'info',
                                    'completed' => 'success',
                                    'cancelled' => 'danger',
                                    'received' => 'success',
                                ];
                                $color = $statusColors[$delivery->status] ?? 'dark';
                            @endphp
                            <span class="badge bg-{{ $color }}">{{ ucfirst($delivery->status) }}</span>
                        </td>
                        <td>{{ $delivery->items_count }} {{ Str::plural('item', $delivery->items_count) }}</td>
                        <td>{{ $delivery->warehouse->name ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('warehouse.deliveries.show', $delivery) }}" 
                               class="btn btn-sm btn-{{ in_array($delivery->status, ['pending', 'processing']) ? 'primary' : 'secondary' }}">
                                {{ in_array($delivery->status, ['pending', 'processing']) ? 'Process' : 'View' }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted animated-empty">No deliveries found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $deliveries->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize date range picker
    $('#date_range').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    $('#date_range').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });

    $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
</script>
@endpush
@endsection 