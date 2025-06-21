@extends('layouts.app')

@push('styles')
<style>
    .table .badge {
        position: static !important;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Deliveries Management</h1>
    </div>

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
                        <td colspan="7" class="text-center text-muted">No deliveries found</td>
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