@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
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
                                <x-status-badge :status="$request->status" />
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
                        <x-empty-row cols="6">No material requests found.</x-empty-row>
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