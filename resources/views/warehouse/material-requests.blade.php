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
.badge.bg-warning {
    background: linear-gradient(90deg, #f7971e 0%, #ffd200 100%);
    color: #fff;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(247,151,30,0.08);
}
.badge.bg-secondary {
    background: linear-gradient(90deg, #bdc3c7 0%, #2c3e50 100%);
    color: #fff;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(44,62,80,0.08);
}
.btn-info.btn-sm {
    background: linear-gradient(90deg, #56ccf2 0%, #2f80ed 100%);
    color: #fff;
    border: none;
    font-weight: 600;
    border-radius: 8px;
    margin-right: 4px;
}
.btn-success.btn-sm {
    background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
    color: #fff;
    border: none;
    font-weight: 600;
    border-radius: 8px;
}
.btn-info.btn-sm:hover, .btn-success.btn-sm:hover {
    filter: brightness(0.95);
}
.text-success {
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

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Material Requests - Approval</h1>
    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Contract</th>
                        <th>Requested By</th>
                        <th>Status</th>
                        <th>Requested At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materialRequests as $request)
                        <tr>
                            <td>{{ $request->id }}</td>
                            <td>{{ $request->quotationRequest->user->name ?? '-' }}</td>
                            <td>{{ $request->requestedBy->name ?? $request->user->name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'pending' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <a href="{{ route('warehouse.material-requests.show', $request) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                @if($request->status === 'pending')
                                <form action="{{ route('warehouse.material-requests.approve', $request) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Approve this material request?')">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>
                                @else
                                    <span class="text-success">Approved</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted animated-empty">No material requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $materialRequests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 