@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Quotation Requests</h2>
        <a href="{{ route('client.quotation.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Quotation Request
        </a>
    </div>

    @if($quotationRequests->count() > 0)
        <div class="row">
            @foreach($quotationRequests as $request)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $request->request_number }}</h6>
                            <span class="badge bg-{{ $request->status_color }}">{{ $request->status_label }}</span>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-2">
                                <i class="fas fa-calendar"></i> 
                                {{ $request->created_at->format('M d, Y') }}
                            </p>
                            <p class="mb-2">
                                <strong>Rooms:</strong> {{ $request->rooms->count() }}
                            </p>
                            <p class="mb-3">
                                <strong>Total Area:</strong> 
                                {{ $request->rooms->sum('area') }} sqm
                            </p>
                            
                            @if($request->notes)
                                <p class="mb-3">
                                    <strong>Notes:</strong><br>
                                    <small class="text-muted">{{ Str::limit($request->notes, 100) }}</small>
                                </p>
                            @endif

                            @if($request->admin_notes)
                                <div class="alert alert-info p-2 mb-3">
                                    <small>
                                        <strong>Admin Response:</strong><br>
                                        {{ Str::limit($request->admin_notes, 150) }}
                                    </small>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('client.quotation.view') }}?id={{ $request->id }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                @if($request->status === 'pending')
                                    <span class="text-muted small">Under Review</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No Quotation Requests Yet</h4>
            <p class="text-muted">You haven't submitted any quotation requests yet.</p>
            <a href="{{ route('client.quotation.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Your First Request
            </a>
        </div>
    @endif
</div>
@endsection 