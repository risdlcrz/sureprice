@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

<div class="card h-100 materials-request-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Materials Requests</h5>
        @can('create', App\Models\MaterialRequest::class)
            <a href="{{ route('material-requests.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> New Request
            </a>
        @endcan
    </div>
    <div class="card-body">
        <div class="list-group list-group-flush">
            @forelse($materialRequests as $request)
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">
                                <a href="{{ route('material-requests.show', $request) }}" class="text-decoration-none">
                                    {{ $request->request_number }}
                                </a>
                            </h6>
                            <small class="text-muted">
                                Requested by: {{ $request->requestedBy->name }} | 
                                {{ $request->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <div>
                            <span class="badge bg-{{ $request->status_color }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </div>
                    </div>
                    @if($request->contract)
                        <small class="text-muted d-block mt-1">
                            Contract: {{ $request->contract->contract_number }}
                        </small>
                    @endif
                    <div class="progress">
                        <div class="progress-bar bg-{{ $request->status_color }} w-{{ $request->progress }}" 
                             role="progressbar"
                             aria-valuenow="{{ $request->progress }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-3">
                    <p>No material requests found</p>
                </div>
            @endforelse
        </div>
    </div>
    <div class="card-footer text-center">
        <a href="{{ route('material-requests.index') }}" class="text-decoration-none">
            View All Requests
        </a>
    </div>
</div> 