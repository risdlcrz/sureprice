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
<h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Material Usage Report (Web Preview)</h1>
    <div class="d-flex justify-content-between align-items-center mb-4">
        
        <a href="{{ route('warehouse.reports.usage.pdf', request()->all()) }}" class="btn btn-primary">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </a>
    </div>
    <div class="card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Material Usage</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Total Out</th>
                        <th>Total In</th>
                        <th>Net Change</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usageStats as $stat)
                    <tr>
                        <td>{{ $stat['material']->name ?? '-' }}</td>
                        <td>{{ $stat['total_out'] }}</td>
                        <td>{{ $stat['total_in'] }}</td>
                        <td>{{ $stat['net_change'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted animated-empty">No material usage data found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 