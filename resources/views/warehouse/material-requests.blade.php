@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="display-5 mb-4">Material Requests - Approval</h1>
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
                            <td>{{ $request->contract->contract_number ?? '-' }}</td>
                            <td>{{ $request->user->name ?? '-' }}</td>
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
                            <td colspan="6" class="text-center text-muted">No material requests found.</td>
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