@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="display-5 mb-4">Material Request #{{ $materialRequest->id }}</h1>
    <div class="mb-3">
        <a href="{{ route('warehouse.material-requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Request Details</h5>
            <p><strong>Contract:</strong> {{ $materialRequest->contract->contract_number ?? '-' }}</p>
            <p><strong>Requested By:</strong> {{ $materialRequest->user->name ?? '-' }}</p>
            <p><strong>Status:</strong> <span class="badge bg-{{ $materialRequest->status === 'approved' ? 'success' : ($materialRequest->status === 'pending' ? 'warning' : 'secondary') }}">{{ ucfirst($materialRequest->status) }}</span></p>
            <p><strong>Requested At:</strong> {{ $materialRequest->created_at->format('M d, Y H:i') }}</p>
            <p><strong>Notes:</strong> {{ $materialRequest->notes ?? '-' }}</p>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Requested Items</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Fulfilled</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materialRequest->items as $item)
                        <tr>
                            <td>{{ $item->material->name ?? '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->unit }}</td>
                            <td>{{ $item->fulfilled_quantity ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($materialRequest->status === 'pending')
    <form action="{{ route('warehouse.material-requests.approve', $materialRequest) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-success" onclick="return confirm('Approve this material request?')">
            <i class="fas fa-check"></i> Approve Request
        </button>
    </form>
    @elseif($materialRequest->status === 'approved')
        <div class="alert alert-success">This request has been approved.</div>
    @endif
</div>
@endsection 