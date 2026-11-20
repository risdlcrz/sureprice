@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <x-page-header title="Warranty Requests">
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary" id="filterBtn">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                            <button type="button" class="btn btn-outline-success" id="exportBtn">
                                <i class="bi bi-download"></i> Export
                            </button>
                            <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="bi bi-upload"></i> Import
                            </button>
                            <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#addWarrantyModal">
                                <i class="bi bi-plus-circle"></i> Add Request
                            </button>
                        </div>
                    </x-page-header>

                    <!-- Additional Work Request Button -->
                    <div class="mb-4">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#additionalWorkModal">
                            <i class="bi bi-tools"></i> Request Additional Work
                        </button>
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
                                @forelse($warrantyRequests as $warrantyRequest)
                                    <tr>
                                        <td>{{ $warrantyRequest->id }}</td>
                                        <td>{{ $warrantyRequest->contract->contract_number ?? '-' }}</td>
                                        <td>{{ $warrantyRequest->product_name }}</td>
                                        <td>{{ $warrantyRequest->serial_number }}</td>
                                        <td>
                                            <x-status-badge :status="$warrantyRequest->status" />
                                        </td>
                                        <td>{{ $warrantyRequest->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('warranty-requests.show', $warrantyRequest) }}" class="btn btn-sm btn-info me-1">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form action="{{ route('warranty-requests.update-status', $warrantyRequest) }}" method="POST" class="d-inline me-1">
                                                    @csrf
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="btn btn-success btn-sm mb-1" {{ $warrantyRequest->status === 'approved' ? 'disabled' : '' }}>Approve</button>
                                                </form>
                                                <form action="{{ route('warranty-requests.update-status', $warrantyRequest) }}" method="POST" class="d-inline me-1">
                                                    @csrf
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-danger btn-sm mb-1" {{ $warrantyRequest->status === 'rejected' ? 'disabled' : '' }}>Reject</button>
                                                </form>
                                                <form action="{{ route('warranty-requests.update-status', $warrantyRequest) }}" method="POST" class="d-inline me-1">
                                                    @csrf
                                                    <input type="hidden" name="status" value="pending">
                                                    <button type="submit" class="btn btn-warning btn-sm mb-1" {{ $warrantyRequest->status === 'pending' ? 'disabled' : '' }}>Set to Pending</button>
                                                </form>
                                                <form action="{{ route('warranty-requests.update-status', $warrantyRequest) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="in_review">
                                                    <button type="submit" class="btn btn-info btn-sm mb-1" {{ $warrantyRequest->status === 'in_review' ? 'disabled' : '' }}>Set to In Review</button>
                                                </form>
                                            </div>
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
<x-modal-dialog id="addWarrantyModal" dialog-class="modal-lg">
    <x-slot:title>Add Warranty Request</x-slot:title>
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
</x-modal-dialog>

<!-- Import Modal -->
<x-modal-dialog id="importModal" dialog-class="modal-lg">
    <x-slot:title>Import Warranty Requests</x-slot:title>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6 class="alert-heading"><i class="bi bi-info-circle"></i> Instructions</h6>
                    <ol class="mb-0">
                        <li>Download the template file using the button below</li>
                        <li>Fill in the required information in the template</li>
                        <li>Save the file as CSV format</li>
                        <li>Upload the filled template using the form below</li>
                    </ol>
                </div>

                <div class="mb-4">
                    <a href="{{ route('warranty-requests.template') }}" class="btn btn-outline-primary">
                        <i class="bi bi-download"></i> Download Template
                    </a>
                </div>

                <form id="importForm" action="{{ route('warranty-requests.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="importFile" class="form-label">Select CSV File</label>
                        <input type="file" class="form-control" id="importFile" name="file" accept=".csv" required>
                        <div class="form-text">Only CSV files are accepted. Maximum file size: 5MB</div>
                    </div>
                </form>

                <div class="alert alert-warning">
                    <h6 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Important Notes</h6>
                    <ul class="mb-0">
                        <li>All fields marked with * are required</li>
                        <li>Contract Number must exist in the system</li>
                        <li>Status must be one of: pending, in_review, approved, rejected</li>
                        <li>Dates should be in YYYY-MM-DD format</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="importForm" class="btn btn-primary">Import</button>
            </div>
</x-modal-dialog>

<!-- Additional Work Request Modal -->
<x-modal-dialog id="additionalWorkModal" dialog-class="modal-xl">
    <x-slot:title>Request Additional Work</x-slot:title>
            <div class="modal-body">
                <form id="additionalWorkForm" class="needs-validation" novalidate>
                    <div class="row">
                        <!-- Contract Selection -->
                        <div class="col-md-6 mb-3">
                            <label for="work_contract_id" class="form-label">Select Contract*</label>
                            <select class="form-select" id="work_contract_id" name="contract_id" required>
                                <option value="">Choose a contract...</option>
                                @foreach(App\Models\Contract::where('status', 'COMPLETED')->get() as $contract)
                                    <option value="{{ $contract->id }}" 
                                            data-client="{{ $contract->client->name }}"
                                            data-number="{{ $contract->contract_number }}">
                                        {{ $contract->contract_number }} - {{ $contract->client->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Please select a contract.</div>
                        </div>

                        <!-- Work Type -->
                        <div class="col-md-6 mb-3">
                            <label for="work_type" class="form-label">Type of Work*</label>
                            <select class="form-select" id="work_type" name="work_type" required>
                                <option value="">Select work type...</option>
                                <option value="installation">Installation</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="repair">Repair</option>
                                <option value="upgrade">Upgrade</option>
                                <option value="other">Other</option>
                            </select>
                            <div class="invalid-feedback">Please select a work type.</div>
                        </div>
                    </div>

                    <!-- Work Description -->
                    <div class="mb-3">
                        <label for="work_description" class="form-label">Work Description*</label>
                        <textarea class="form-control" id="work_description" name="description" rows="3" required></textarea>
                        <div class="invalid-feedback">Please provide a description of the work needed.</div>
                    </div>

                    <!-- Materials Section -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Required Materials</h6>
                        </div>
                        <div class="card-body">
                            <div id="materialsContainer">
                                <div class="material-item row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Material*</label>
                                        <input type="text" class="form-control material-search-input" placeholder="Search material..." required>
                                        <input type="hidden" class="material-id-input" name="materials[0][material_id]" required>
                                        <div class="material-search-results list-group position-absolute w-100" style="z-index: 1000;"></div>
                                        <div class="invalid-feedback">Please select a material.</div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Quantity*</label>
                                        <input type="number" class="form-control" name="materials[0][quantity]" min="0.01" step="0.01" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Unit</label>
                                        <input type="text" class="form-control unit-display" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Notes</label>
                                        <input type="text" class="form-control" name="materials[0][notes]">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger remove-material" style="display: none;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary" id="addMaterial">
                                <i class="bi bi-plus-circle"></i> Add Material
                            </button>
                        </div>
                    </div>

                    <!-- Labor Section -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Labor Requirements</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="estimated_hours" class="form-label">Estimated Hours*</label>
                                    <input type="number" class="form-control" id="estimated_hours" name="estimated_hours" min="0.5" step="0.5" required>
                                    <div class="invalid-feedback">Please provide estimated hours.</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="required_skills" class="form-label">Required Skills</label>
                                    <input type="text" class="form-control" id="required_skills" name="required_skills" placeholder="e.g., Electrical, Plumbing">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="labor_notes" class="form-label">Additional Labor Notes</label>
                                <textarea class="form-control" id="labor_notes" name="labor_notes" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Section -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Timeline</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="preferred_start_date" class="form-label">Preferred Start Date*</label>
                                    <input type="date" class="form-control" id="preferred_start_date" name="preferred_start_date" required>
                                    <div class="invalid-feedback">Please select a preferred start date.</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="preferred_end_date" class="form-label">Preferred Completion Date*</label>
                                    <input type="date" class="form-control" id="preferred_end_date" name="preferred_end_date" required>
                                    <div class="invalid-feedback">Please select a preferred completion date.</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="timeline_notes" class="form-label">Timeline Notes</label>
                                <textarea class="form-control" id="timeline_notes" name="timeline_notes" rows="2" placeholder="Any specific timing requirements or constraints"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div class="mb-3">
                        <label for="additional_notes" class="form-label">Additional Notes</label>
                        <textarea class="form-control" id="additional_notes" name="additional_notes" rows="3" placeholder="Any other relevant information"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitAdditionalWork">Submit Request</button>
            </div>
</x-modal-dialog>

@endsection

@push('scripts')
<script>window.__page = Object.assign(window.__page || {}, { index0_0: @json((string) (route("warranty-requests.export"))), index0_1: @json((string) (route("warranty-requests.index"))), index0_2: @json((string) (url('api/warranty-requests'))), index0_3: @json((string) (url('api/materials/search'))), index0_4: @json((string) (route("warranty-requests.additional-work"))), index0_5: @json((string) (csrf_token())) });</script>
@vite('resources/js/pages/warranty-requests-index.js')
@endpush 