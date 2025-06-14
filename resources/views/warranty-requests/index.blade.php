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
                    </div>

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

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Warranty Requests</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
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
        </div>
    </div>
</div>

<!-- Additional Work Request Modal -->
<div class="modal fade" id="additionalWorkModal" tabindex="-1" aria-labelledby="additionalWorkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="additionalWorkModalLabel">Request Additional Work</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
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
        </div>
    </div>
</div>

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
        const status = document.getElementById('statusFilter').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        const search = document.getElementById('searchInput').value;

        let exportUrl = '{{ route("warranty-requests.export") }}';
        const params = new URLSearchParams();

        if (status) params.append('status', status);
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);
        if (search) params.append('search', search);

        if (params.toString()) {
            exportUrl += '?' + params.toString();
        }

        window.location.href = exportUrl;
    });

    // Filter form submission
    document.getElementById('filtersForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const params = new URLSearchParams(formData);
        window.location.href = '{{ route("warranty-requests.index") }}?' + params.toString();
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

    let materialCount = 1;

    // Add Material Row
    document.getElementById('addMaterial').addEventListener('click', function() {
        const materialsContainer = document.getElementById('materialsContainer');
        const template = document.querySelector('.material-item').cloneNode(true);
        template.querySelectorAll('input').forEach(input => input.value = ''); // Clear values for new row
        template.querySelector('.material-id-input').value = '';
        template.querySelector('.unit-display').value = '';
        template.querySelector('.material-search-results').innerHTML = '';
        template.querySelector('.remove-material').style.display = 'block';
        // Update names for new row
        template.querySelectorAll('[name*="materials[0]"]').forEach(element => {
            element.name = element.name.replace('[0]', `[${materialCount}]`);
        });
        materialsContainer.appendChild(template);
        setupMaterialSearch(template); // Initialize search for the new row
        materialCount++;
    });

    // Remove Material Row
    document.getElementById('materialsContainer').addEventListener('click', function(e) {
        if (e.target.closest('.remove-material')) {
            e.target.closest('.material-item').remove();
        }
    });

    // Handle material selection and unit display
    document.getElementById('materialsContainer').addEventListener('input', function(e) {
        if (e.target.classList.contains('material-search-input')) {
            const searchInput = e.target;
            const materialItem = searchInput.closest('.material-item');
            const materialIdInput = materialItem.querySelector('.material-id-input');
            const unitDisplay = materialItem.querySelector('.unit-display');
            
            // Clear previously selected material if user starts typing again
            materialIdInput.value = '';
            unitDisplay.value = '';
            materialItem.querySelector('.material-search-results').innerHTML = '';
        }
    });

    document.getElementById('materialsContainer').addEventListener('click', function(e) {
        if (e.target.classList.contains('list-group-item-action')) {
            e.preventDefault(); // Prevent default link behavior
            const selectedResult = e.target;
            const materialId = selectedResult.dataset.materialId;
            const materialName = selectedResult.dataset.materialName;
            const unit = selectedResult.dataset.unit;

            const materialItem = selectedResult.closest('.material-item');
            materialItem.querySelector('.material-search-input').value = materialName;
            materialItem.querySelector('.material-id-input').value = materialId;
            materialItem.querySelector('.unit-display').value = unit;
            materialItem.querySelector('.material-search-results').innerHTML = ''; // Clear results
        }
    });

    // Debounce function
    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }

    // Setup Material Search functionality
    function setupMaterialSearch(container) {
        const searchInput = container.querySelector('.material-search-input');
        const materialIdInput = container.querySelector('.material-id-input');
        const searchResultsDiv = container.querySelector('.material-search-results');
        const unitDisplay = container.querySelector('.unit-display');

        const performSearch = debounce(function() {
            const query = searchInput.value.trim();
            if (query.length < 2) {
                searchResultsDiv.innerHTML = '';
                return;
            }

            fetch(`/api/materials/search?query=${query}`)
                .then(response => response.json())
                .then(materials => {
                    searchResultsDiv.innerHTML = '';
                    if (materials.length > 0) {
                        materials.forEach(material => {
                            const item = document.createElement('a');
                            item.href = '#';
                            item.classList.add('list-group-item', 'list-group-item-action');
                            item.dataset.materialId = material.id;
                            item.dataset.materialName = `${material.name} (${material.code})`;
                            item.dataset.unit = material.unit;
                            item.textContent = `${material.name} (${material.code}) - ${material.unit}`;
                            searchResultsDiv.appendChild(item);
                        });
                    } else {
                        searchResultsDiv.innerHTML = '<div class="list-group-item">No materials found</div>';
                    }
                })
                .catch(error => {
                    console.error('Error searching materials:', error);
                    searchResultsDiv.innerHTML = '<div class="list-group-item text-danger">Error searching</div>';
                });
        }, 300);

        searchInput.addEventListener('input', performSearch);

    }

    // Initial setup for the first row
    setupMaterialSearch(document.querySelector('.material-item'));

    // Additional Work Request Form Handling
    const additionalWorkForm = document.getElementById('additionalWorkForm');
    const addMaterialBtn = document.getElementById('addMaterial');
    const materialsContainer = document.getElementById('materialsContainer');
    let materialCount = 1;

    // Add Material Row
    addMaterialBtn.addEventListener('click', function() {
        const template = document.querySelector('.material-item').cloneNode(true);
        template.querySelectorAll('input').forEach(input => input.value = ''); // Clear values for new row
        template.querySelector('.material-id-input').value = '';
        template.querySelector('.unit-display').value = '';
        template.querySelector('.material-search-results').innerHTML = '';
        template.querySelector('.remove-material').style.display = 'block';
        // Update names for new row
        template.querySelectorAll('[name*="materials[0]"]').forEach(element => {
            element.name = element.name.replace('[0]', `[${materialCount}]`);
        });
        materialsContainer.appendChild(template);
        setupMaterialSearch(template); // Initialize search for the new row
        materialCount++;
    });

    // Remove Material Row
    materialsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-material')) {
            e.target.closest('.material-item').remove();
        }
    });

    // Handle material selection and unit display
    materialsContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('material-search-input')) {
            const searchInput = e.target;
            const materialItem = searchInput.closest('.material-item');
            const materialIdInput = materialItem.querySelector('.material-id-input');
            const unitDisplay = materialItem.querySelector('.unit-display');
            
            // Clear previously selected material if user starts typing again
            materialIdInput.value = '';
            unitDisplay.value = '';
            materialItem.querySelector('.material-search-results').innerHTML = '';
        }
    });

    materialsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('list-group-item-action')) {
            const selectedResult = e.target;
            const materialId = selectedResult.dataset.materialId;
            const materialName = selectedResult.dataset.materialName;
            const unit = selectedResult.dataset.unit;

            const materialItem = selectedResult.closest('.material-item');
            materialItem.querySelector('.material-search-input').value = materialName;
            materialItem.querySelector('.material-id-input').value = materialId;
            materialItem.querySelector('.unit-display').value = unit;
            materialItem.querySelector('.material-search-results').innerHTML = ''; // Clear results
        }
    });

    // Form Validation and Submission
    document.getElementById('submitAdditionalWork').addEventListener('click', function() {
        if (!additionalWorkForm.checkValidity()) {
            additionalWorkForm.classList.add('was-validated');
            return;
        }

        const formData = new FormData(additionalWorkForm);
        const data = Object.fromEntries(formData.entries());
        
        // Convert materials array
        const materials = [];
        document.querySelectorAll('.material-item').forEach((item, index) => {
            materials.push({
                material_id: item.querySelector('.material-select').value,
                quantity: item.querySelector('input[name$="[quantity]"]').value,
                notes: item.querySelector('input[name$="[notes]"]').value
            });
        });
        data.materials = materials;

        // Submit the form
        fetch('{{ route("warranty-requests.additional-work") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Additional work request submitted successfully');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while submitting the request');
        });
    });
});
</script>
@endpush 