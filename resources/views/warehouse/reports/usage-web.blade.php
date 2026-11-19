@extends('layouts.app')

@section('content')
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
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
                    <x-empty-row cols="4">No material usage data found.</x-empty-row>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 