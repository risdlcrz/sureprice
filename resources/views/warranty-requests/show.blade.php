@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h3 mb-0">Warranty Request #{{ $warrantyRequest->id }}</h1>
                        <a href="{{ route('warranty-requests.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>

                    <div class="row">
                        <!-- Left Column: Request Details -->
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Request Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Product Name:</strong></p>
                                            <p>{{ $warrantyRequest->product_name }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Serial Number:</strong></p>
                                            <p>{{ $warrantyRequest->serial_number }}</p>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <p class="mb-1"><strong>Issue Description:</strong></p>
                                        <p>{{ $warrantyRequest->issue_description }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="mb-1"><strong>Proof of Purchase:</strong></p>
                                        <a href="{{ Storage::url($warrantyRequest->proof_of_purchase_path) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="bi bi-file-earmark"></i> View Document
                                        </a>
                                    </div>
                                    @if($warrantyRequest->issue_photos_paths)
                                        <div class="mb-3">
                                            <p class="mb-1"><strong>Issue Photos:</strong></p>
                                            <div class="row g-2">
                                                @foreach($warrantyRequest->issue_photos_paths as $photo)
                                                    <div class="col-md-4">
                                                        <a href="{{ Storage::url($photo) }}" target="_blank">
                                                            <img src="{{ Storage::url($photo) }}" class="img-thumbnail" alt="Issue Photo">
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Contract Details -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Contract Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Contract Number:</strong></p>
                                            <p>{{ $warrantyRequest->contract->contract_number }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Client:</strong></p>
                                            <p>{{ $warrantyRequest->contract->client->name }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Status and Actions -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Status & Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <p class="mb-1"><strong>Current Status:</strong></p>
                                        <span class="badge bg-{{ $warrantyRequest->status === 'approved' ? 'success' : ($warrantyRequest->status === 'rejected' ? 'danger' : ($warrantyRequest->status === 'in_review' ? 'warning' : 'secondary')) }} fs-6">
                                            {{ ucfirst($warrantyRequest->status) }}
                                        </span>
                                    </div>

                                    @if($warrantyRequest->admin_notes)
                                        <div class="mb-4">
                                            <p class="mb-1"><strong>Admin Notes:</strong></p>
                                            <p>{{ $warrantyRequest->admin_notes }}</p>
                                        </div>
                                    @endif

                                    @if($warrantyRequest->reviewed_at)
                                        <div class="mb-4">
                                            <p class="mb-1"><strong>Last Reviewed:</strong></p>
                                            <p>{{ $warrantyRequest->reviewed_at->format('M d, Y H:i') }}</p>
                                        </div>
                                    @endif

                                    @if($warrantyRequest->status === 'pending')
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-warning" onclick="updateStatus('in_review')">
                                                <i class="bi bi-search"></i> Mark as In Review
                                            </button>
                                            <button type="button" class="btn btn-success" onclick="updateStatus('approved')">
                                                <i class="bi bi-check-circle"></i> Approve Request
                                            </button>
                                            <button type="button" class="btn btn-danger" onclick="updateStatus('rejected')">
                                                <i class="bi bi-x-circle"></i> Reject Request
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    <div class="mb-3">
                        <label for="adminNotes" class="form-label">Admin Notes</label>
                        <textarea class="form-control" id="adminNotes" name="admin_notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitStatusUpdate()">Update Status</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentStatus = '';

function updateStatus(status) {
    currentStatus = status;
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}

function submitStatusUpdate() {
    const formData = new FormData();
    formData.append('status', currentStatus);
    formData.append('admin_notes', document.getElementById('adminNotes').value);

    fetch(`{{ route('warranty-requests.update-status', $warrantyRequest) }}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Failed to update status: ' + data.message);
        }
    })
    .catch(error => {
        alert('An error occurred while updating the status');
    });
}
</script>
@endpush 