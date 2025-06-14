@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h3 mb-0">Warranty Requests</h1>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary" id="filterBtn">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                            <a href="{{ route('warranty-requests.export') }}" class="btn btn-outline-success">
                                <i class="bi bi-download"></i> Export
                            </a>
                            <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#addWarrantyModal">
                                <i class="bi bi-plus-circle"></i> Add Request
                            </button>
                        </div>
                    </div>

                    <!-- Filters Section -->
                    <div class="collapse mb-4" id="filtersCollapse">
                        <div class="card card-body">
                            <form id="filtersForm" class="row g-3">
                                <div class="col-md-3">
                                    <label for="statusFilter" class="form-label">Status</label>
                                    <select class="form-select" id="statusFilter" name="status">
                                        <option value="">All</option>
                                        <option value="pending">Pending</option>
                                        <option value="in_review">In Review</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="dateFrom" class="form-label">Date From</label>
                                    <input type="date" class="form-control" id="dateFrom" name="date_from">
                                </div>
                                <div class="col-md-3">
                                    <label for="dateTo" class="form-label">Date To</label>
                                    <input type="date" class="form-control" id="dateTo" name="date_to">
                                </div>
                                <div class="col-md-3">
                                    <label for="searchInput" class="form-label">Search</label>
                                    <input type="text" class="form-control" id="searchInput" name="search" placeholder="Search...">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                                    <button type="reset" class="btn btn-secondary">Reset</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Warranty Requests Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Contract</th>
                                    <th>Product</th>
                                    <th>Serial Number</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($warrantyRequests as $request)
                                    <tr>
                                        <td>#{{ $request->id }}</td>
                                        <td>{{ $request->contract->contract_number }}</td>
                                        <td>{{ $request->product_name }}</td>
                                        <td>{{ $request->serial_number }}</td>
                                        <td>
                                            <span class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : ($request->status === 'in_review' ? 'warning' : 'secondary')) }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('warranty-requests.show', $request) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bi bi-inbox fs-1"></i>
                                                <p class="mt-2">No warranty requests found</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $warrantyRequests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Warranty Request Modal -->
<div class="modal fade" id="addWarrantyModal" tabindex="-1" aria-labelledby="addWarrantyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addWarrantyModalLabel">Add Warranty Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addWarrantyForm" class="needs-validation" enctype="multipart/form-data" novalidate>
                    <div class="mb-3">
                        <label for="contract_id" class="form-label">Contract</label>
                        <select class="form-select" id="contract_id" name="contract_id" required>
                            <option value="">Select a completed contract</option>
                            @foreach(App\Models\Contract::where('status', 'COMPLETED')->get() as $contract)
                                <option value="{{ $contract->id }}">{{ $contract->contract_number }} - {{ $contract->client->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Please select a contract.</div>
                    </div>
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" required>
                        <div class="invalid-feedback">Please provide the product name.</div>
                    </div>
                    <div class="mb-3">
                        <label for="serial_number" class="form-label">Serial Number</label>
                        <input type="text" class="form-control" id="serial_number" name="serial_number" required>
                        <div class="invalid-feedback">Please provide the serial number.</div>
                    </div>
                    <div class="mb-3">
                        <label for="issue_description" class="form-label">Issue Description</label>
                        <textarea class="form-control" id="issue_description" name="issue_description" rows="3" required></textarea>
                        <div class="invalid-feedback">Please describe the issue.</div>
                    </div>
                    <div class="mb-3">
                        <label for="purchase_date" class="form-label">Purchase Date</label>
                        <input type="date" class="form-control" id="purchase_date" name="purchase_date">
                    </div>
                    <div class="mb-3">
                        <label for="receipt_number" class="form-label">Receipt Number</label>
                        <input type="text" class="form-control" id="receipt_number" name="receipt_number">
                    </div>
                    <div class="mb-3">
                        <label for="model_number" class="form-label">Model Number (if applicable)</label>
                        <input type="text" class="form-control" id="model_number" name="model_number">
                    </div>
                    <div class="mb-3">
                        <label for="purchase_proof" class="form-label">Upload Purchase Slip</label>
                        <input type="file" class="form-control" id="purchase_proof" name="purchaseProof" accept=".pdf,.jpg,.jpeg,.png" required>
                        <div class="form-text">Upload a photo or scan of the receipt (PDF, JPG, PNG)</div>
                        <div class="invalid-feedback">Please provide the purchase slip.</div>
                    </div>
                    <div class="mb-3">
                        <label for="issue_photos" class="form-label">Photos of the Issue</label>
                        <input type="file" class="form-control" id="issue_photos" name="issuePhotos[]" accept=".jpg,.jpeg,.png" multiple>
                        <div class="form-text">Upload photos showing the issue (optional)</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitAddWarranty">Submit Request</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter button toggle
    const filterBtn = document.getElementById('filterBtn');
    const filtersCollapse = document.getElementById('filtersCollapse');
    
    filterBtn.addEventListener('click', function() {
        const bsCollapse = new bootstrap.Collapse(filtersCollapse);
        bsCollapse.toggle();
    });

    // Export functionality
    document.getElementById('exportBtn').addEventListener('click', function() {
        // Implement export functionality here
        alert('Export functionality will be implemented here');
    });

    // Add Warranty Request Submit
    document.getElementById('submitAddWarranty').addEventListener('click', function() {
        const form = document.getElementById('addWarrantyForm');
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        const formData = new FormData(form);
        // Show loading state
        const submitBtn = this;
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';
        fetch('/api/warranty-requests', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Warranty request added successfully.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Close modal, reset form, reload page
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addWarrantyModal'));
                    modal.hide();
                    form.reset();
                    form.classList.remove('was-validated');
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Something went wrong');
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: error.message || 'Failed to add warranty request. Please try again.',
                confirmButtonText: 'OK'
            });
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
});
</script>
@endpush 