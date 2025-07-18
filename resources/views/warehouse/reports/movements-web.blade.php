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
.card-title, h1.h3 {
    font-weight: 700;
    color: #198754;
    letter-spacing: 0.01em;
}
.btn-primary {
    background: linear-gradient(90deg, #56ccf2 0%, #2f80ed 100%);
    color: #fff;
    border: none;
    font-weight: 600;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(44,62,80,0.04);
    padding-left: 1.5rem;
    padding-right: 1.5rem;
}
.btn-primary:hover {
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
.badge.bg-success {
    background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
    color: #fff;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(67,233,123,0.08);
}
.badge.bg-danger {
    background: linear-gradient(90deg, #ff5858 0%, #f09819 100%);
    color: #fff;
    font-weight: 600;
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
</style>
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
                    <tr>
                        <td colspan="9" class="text-center text-muted animated-empty">No stock movements found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 