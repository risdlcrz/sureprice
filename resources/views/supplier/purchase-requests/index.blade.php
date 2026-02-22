@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Purchase Requests</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Request Number</th>
                                    <th>Status</th>
                                    <th>Requested By</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseRequests as $pr)
                                    <tr>
                                        <td>{{ $pr->request_number }}</td>
                                        <td>
                                            <span class="badge badge-{{ $pr->status === 'pending' ? 'warning' : ($pr->status === 'approved' ? 'success' : 'danger') }}">
                                                {{ ucfirst($pr->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $pr->requestedBy?->name ?? 'N/A' }}</td>
                                        <td>{{ $pr->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('supplier.purchase-requests.show', $pr) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No purchase requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 