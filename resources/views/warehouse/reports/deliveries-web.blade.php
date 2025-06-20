@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Deliveries Report (Web Preview)</h1>
        <a href="{{ route('warehouse.reports.deliveries.pdf', request()->all()) }}" class="btn btn-primary">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </a>
    </div>
    <div class="card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Deliveries</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Delivery #</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total Items</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deliveries as $delivery)
                    <tr>
                        <td>{{ $delivery->delivery_number }}</td>
                        <td>{{ $delivery->delivery_date ? $delivery->delivery_date->format('M d, Y') : '-' }}</td>
                        <td><span class="badge bg-{{ $delivery->status === 'completed' ? 'success' : ($delivery->status === 'pending' ? 'warning' : 'secondary') }}">{{ ucfirst($delivery->status) }}</span></td>
                        <td>{{ $delivery->items->count() }}</td>
                        <td>{{ $delivery->notes }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 