@extends('layouts.app')

@section('content')
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
@vite('resources/css/pages/warehouse-reports-movements-web.css')
@endpush
<div class="container py-4">
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Stock Movements Report (Web Preview)</h1>
    <div class="d-flex justify-content-between align-items-center mb-4">
        
        <a href="{{ route('warehouse.reports.movements.pdf', request()->all()) }}" class="btn btn-primary">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </a>
    </div>
    <div class="card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Stock Movements</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Material</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Previous Stock</th>
                        <th>New Stock</th>
                        <th>Reference</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                    <tr>
                        <td>{{ $movement->created_at->format('M d, Y H:i') }}</td>
                        <td>{{ $movement->material->name ?? '-' }}</td>
                        <td>{{ $movement->material->category->name ?? '-' }}</td>
                        <td><span class="badge bg-{{ $movement->type === 'in' ? 'success' : 'danger' }}">{{ ucfirst($movement->type) }}</span></td>
                        <td>{{ $movement->quantity }}</td>
                        <td>{{ $movement->previous_stock }}</td>
                        <td>{{ $movement->new_stock }}</td>
                        <td>{{ $movement->reference_number }}</td>
                        <td>{{ $movement->notes }}</td>
                    </tr>
                    @empty
                    <x-empty-row cols="9">No stock movements found.</x-empty-row>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 