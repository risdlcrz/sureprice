@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h4 class="card-title mb-0">{{ isset($quotation) ? 'Edit Request for Quotation' : 'Create Request for Quotation' }}</h4>
                </div>
                <div class="card-body">
                    <form id="quotationForm" method="POST" action="{{ isset($quotation) ? route('quotations.update', $quotation->id) : route('quotations.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if(isset($quotation))
                            @method('PUT')
                        @endif

                        <!-- Project Information -->
                        <div class="section-container">
                            <h5 class="section-title">Project Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="project_id">Project</label>
                                        <select class="form-control @error('project_id') is-invalid @enderror" 
                                            id="project_id" name="project_id" required>
                                            <option value="">Select Project</option>
                                            @foreach($projects as $project)
                                                <option value="{{ $project->id }}" 
                                                    {{ old('project_id', $quotation->project_id ?? '') == $project->id ? 'selected' : '' }}>
                                                    {{ $project->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('project_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="due_date">Due Date</label>
                                        <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                            id="due_date" name="due_date" 
                                            value="{{ old('due_date', $quotation->due_date ?? '') }}" required>
                                        @error('due_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Supplier Selection -->
                        <div class="section-container mt-4">
                            <h5 class="section-title">Supplier Selection</h5>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Search and Add Suppliers</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="supplierSearch" 
                                                placeholder="Search for suppliers...">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="searchSupplierBtn">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="supplierSearchResults" class="mt-2" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>

                            <div id="selectedSuppliers">
                                @if(isset($quotation) && $quotation->suppliers)
                                    @foreach($quotation->suppliers as $supplier)
                                    <div class="supplier-item card mb-2">
                                        <div class="card-body py-2">
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <strong>{{ $supplier->name }}</strong>
                                                    <input type="hidden" name="suppliers[]" value="{{ $supplier->id }}">
                                                    <br>
                                                    <small class="text-muted">{{ $supplier->email }} | {{ $supplier->phone }}</small>
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="text" class="form-control form-control-sm" 
                                                        name="supplier_notes[{{ $supplier->id }}]" 
                                                        value="{{ $supplier->pivot->notes ?? '' }}" 
                                                        placeholder="Notes for this supplier">
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger btn-sm remove-supplier">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- Materials Needed -->
                        <div class="section-container mt-4">
                            <h5 class="section-title">Materials Needed</h5>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Search and Add Materials</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="materialSearch" 
                                                placeholder="Search for materials...">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="searchMaterialBtn">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="materialSearchResults" class="mt-2" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>

                            <div id="selectedMaterials">
                                @if(isset($quotation) && $quotation->materials)
                                    @foreach($quotation->materials as $material)
                                    <div class="material-item card mb-2">
                                        <div class="card-body py-2">
                                            <div class="row align-items-center">
                                                <div class="col-md-4">
                                                    <strong>{{ $material->name }}</strong>
                                                    <input type="hidden" name="materials[{{ $material->id }}][id]" value="{{ $material->id }}">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" class="form-control form-control-sm" 
                                                        name="materials[{{ $material->id }}][quantity]" 
                                                        value="{{ $material->pivot->quantity }}" 
                                                        placeholder="Quantity" min="1" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control form-control-sm" 
                                                        value="{{ $material->unit }}" 
                                                        readonly>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control form-control-sm" 
                                                        name="materials[{{ $material->id }}][specifications]" 
                                                        value="{{ $material->pivot->specifications }}" 
                                                        placeholder="Specifications">
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger btn-sm remove-material">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="section-container mt-4">
                            <h5 class="section-title">Additional Information</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="notes">Notes and Special Instructions</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                            id="notes" name="notes" rows="4">{{ old('notes', $quotation->notes ?? '') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attachments -->
                        <div class="section-container mt-4">
                            <h5 class="section-title">Attachments</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="attachments">Upload Files</label>
                                        <input type="file" class="form-control-file @error('attachments') is-invalid @enderror" 
                                            id="attachments" name="attachments[]" multiple>
                                        @error('attachments')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            @if(isset($quotation) && $quotation->attachments)
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="existing-attachments">
                                        @foreach($quotation->attachments as $attachment)
                                        <div class="attachment-item d-inline-block position-relative m-2">
                                            <div class="card">
                                                <div class="card-body p-2">
                                                    <i class="fas fa-file mr-2"></i>
                                                    <span>{{ $attachment->original_name }}</span>
                                                    <button type="button" class="btn btn-danger btn-sm ml-2"
                                                        onclick="removeAttachment('{{ $attachment->id }}')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Submit RFQ</button>
                            <a href="{{ route('quotations.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .section-container {
        margin-bottom: 2rem;
        padding: 1.5rem;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        background-color: #fff;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .section-title {
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #007bff;
        color: #2c3e50;
        font-weight: 600;
    }
    #supplierSearchResults,
    #materialSearchResults {
        position: absolute;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        z-index: 1000;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .supplier-result,
    .material-result {
        padding: 0.5rem;
        cursor: pointer;
        border-bottom: 1px solid #dee2e6;
    }
    .supplier-result:hover,
    .material-result:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form validation
    const form = document.getElementById('quotationForm');
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');

            // Check if at least one supplier is selected
            const suppliers = document.querySelectorAll('input[name="suppliers[]"]');
            if (suppliers.length === 0) {
                event.preventDefault();
                alert('Please select at least one supplier');
                return;
            }

            // Check if at least one material is selected
            const materials = document.querySelectorAll('input[name^="materials["][name$="][id]"]');
            if (materials.length === 0) {
                event.preventDefault();
                alert('Please select at least one material');
                return;
            }
        });
    }

    // Supplier search functionality
    const supplierSearch = document.getElementById('supplierSearch');
    const searchSupplierBtn = document.getElementById('searchSupplierBtn');
    const supplierSearchResults = document.getElementById('supplierSearchResults');
    const selectedSuppliers = document.getElementById('selectedSuppliers');
    let supplierSearchTimeout;

    if (supplierSearch && searchSupplierBtn && supplierSearchResults) {
        supplierSearch.addEventListener('input', () => {
            clearTimeout(supplierSearchTimeout);
            supplierSearchTimeout = setTimeout(searchSuppliers, 300);
        });

        searchSupplierBtn.addEventListener('click', searchSuppliers);
    }

    function searchSuppliers() {
        const query = supplierSearch.value.trim();
        if (query.length < 2) {
            supplierSearchResults.style.display = 'none';
            return;
        }

        supplierSearchResults.innerHTML = '<div class="p-2">Searching...</div>';
        supplierSearchResults.style.display = 'block';

        fetch(`/api/suppliers/search?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    supplierSearchResults.innerHTML = data.map(supplier => `
                        <div class="supplier-result" data-supplier='${JSON.stringify(supplier)}'>
                            <strong>${supplier.name}</strong><br>
                            <small>${supplier.email} | ${supplier.phone}</small>
                        </div>
                    `).join('');

                    // Add click handlers
                    supplierSearchResults.querySelectorAll('.supplier-result').forEach(result => {
                        result.addEventListener('click', () => addSupplier(JSON.parse(result.dataset.supplier)));
                    });
                } else {
                    supplierSearchResults.innerHTML = '<div class="p-2">No suppliers found</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                supplierSearchResults.innerHTML = '<div class="p-2 text-danger">Error searching suppliers</div>';
            });
    }

    function addSupplier(supplier) {
        // Check if supplier already exists
        if (document.querySelector(`input[name="suppliers[]"][value="${supplier.id}"]`)) {
            alert('This supplier is already added');
            return;
        }

        const supplierHtml = `
            <div class="supplier-item card mb-2">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <strong>${supplier.name}</strong>
                            <input type="hidden" name="suppliers[]" value="${supplier.id}">
                            <br>
                            <small class="text-muted">${supplier.email} | ${supplier.phone}</small>
                        </div>
                        <div class="col-md-5">
                            <input type="text" class="form-control form-control-sm" 
                                name="supplier_notes[${supplier.id}]" 
                                placeholder="Notes for this supplier">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm remove-supplier">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        selectedSuppliers.insertAdjacentHTML('beforeend', supplierHtml);
        supplierSearch.value = '';
        supplierSearchResults.style.display = 'none';

        // Add remove functionality to the new supplier
        const newSupplier = selectedSuppliers.lastElementChild;
        newSupplier.querySelector('.remove-supplier').addEventListener('click', function() {
            newSupplier.remove();
        });
    }

    // Material search functionality
    const materialSearch = document.getElementById('materialSearch');
    const searchMaterialBtn = document.getElementById('searchMaterialBtn');
    const materialSearchResults = document.getElementById('materialSearchResults');
    const selectedMaterials = document.getElementById('selectedMaterials');
    let materialSearchTimeout;

    if (materialSearch && searchMaterialBtn && materialSearchResults) {
        materialSearch.addEventListener('input', () => {
            clearTimeout(materialSearchTimeout);
            materialSearchTimeout = setTimeout(searchMaterials, 300);
        });

        searchMaterialBtn.addEventListener('click', searchMaterials);
    }

    function searchMaterials() {
        const query = materialSearch.value.trim();
        if (query.length < 2) {
            materialSearchResults.style.display = 'none';
            return;
        }

        materialSearchResults.innerHTML = '<div class="p-2">Searching...</div>';
        materialSearchResults.style.display = 'block';

        fetch(`/api/materials/search?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    materialSearchResults.innerHTML = data.map(material => `
                        <div class="material-result" data-material='${JSON.stringify(material)}'>
                            <strong>${material.name}</strong><br>
                            <small>${material.description || ''} - ${material.unit}</small>
                        </div>
                    `).join('');

                    // Add click handlers
                    materialSearchResults.querySelectorAll('.material-result').forEach(result => {
                        result.addEventListener('click', () => addMaterial(JSON.parse(result.dataset.material)));
                    });
                } else {
                    materialSearchResults.innerHTML = '<div class="p-2">No materials found</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                materialSearchResults.innerHTML = '<div class="p-2 text-danger">Error searching materials</div>';
            });
    }

    function addMaterial(material) {
        // Check if material already exists
        if (document.querySelector(`input[name="materials[${material.id}][id]"]`)) {
            alert('This material is already added');
            return;
        }

        const materialHtml = `
            <div class="material-item card mb-2">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <strong>${material.name}</strong>
                            <input type="hidden" name="materials[${material.id}][id]" value="${material.id}">
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control form-control-sm" 
                                name="materials[${material.id}][quantity]" 
                                placeholder="Quantity" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control form-control-sm" 
                                value="${material.unit}" 
                                readonly>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control form-control-sm" 
                                name="materials[${material.id}][specifications]" 
                                placeholder="Specifications">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm remove-material">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        selectedMaterials.insertAdjacentHTML('beforeend', materialHtml);
        materialSearch.value = '';
        materialSearchResults.style.display = 'none';

        // Add remove functionality to the new material
        const newMaterial = selectedMaterials.lastElementChild;
        newMaterial.querySelector('.remove-material').addEventListener('click', function() {
            newMaterial.remove();
        });
    }

    // Add remove functionality to existing suppliers and materials
    document.querySelectorAll('.remove-supplier').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.supplier-item').remove();
        });
    });

    document.querySelectorAll('.remove-material').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.material-item').remove();
        });
    });

    // Close search results when clicking outside
    document.addEventListener('click', (e) => {
        if (!supplierSearchResults.contains(e.target) && e.target !== supplierSearch && e.target !== searchSupplierBtn) {
            supplierSearchResults.style.display = 'none';
        }
        if (!materialSearchResults.contains(e.target) && e.target !== materialSearch && e.target !== searchMaterialBtn) {
            materialSearchResults.style.display = 'none';
        }
    });

    // Attachment removal function
    window.removeAttachment = function(attachmentId) {
        if (confirm('Are you sure you want to remove this attachment?')) {
            fetch(`/api/quotations/remove-attachment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    quotation_id: '{{ $quotation->id ?? "" }}',
                    attachment_id: attachmentId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const attachmentElement = document.querySelector(`[onclick="removeAttachment('${attachmentId}')"]`).closest('.attachment-item');
                    attachmentElement.remove();
                } else {
                    alert('Failed to remove attachment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error removing attachment');
            });
        }
    };
});
</script>
@endpush
@endsection 