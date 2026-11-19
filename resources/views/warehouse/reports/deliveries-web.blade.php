@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
@vite('resources/css/pages/warehouse-reports-deliveries-web.css')
@endpush

@section('content')
<div class="container py-4">
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Deliveries Report (Web Preview)</h1>
    <div class="d-flex justify-content-between align-items-center mb-4">
        
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
                    @forelse($deliveries as $delivery)
                    <tr>
                        <td>{{ $delivery->delivery_number }}</td>
                        <td>{{ $delivery->delivery_date ? $delivery->delivery_date->format('M d, Y') : '-' }}</td>
                        <td><x-status-badge :status="$delivery->status" /></td>
                        <td>{{ $delivery->items->count() }}</td>
                        <td>{{ $delivery->notes }}</td>
                    </tr>
                    @empty
                    <x-empty-row cols="5">No deliveries found.</x-empty-row>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 