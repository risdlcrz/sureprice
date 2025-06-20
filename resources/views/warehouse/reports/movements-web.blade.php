@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Stock Movements Report (Web Preview)</h1>
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
                    @foreach($movements as $movement)
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 