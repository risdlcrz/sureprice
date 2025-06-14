@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h4 class="card-title mb-0">{{ isset($supplier) ? 'Edit Supplier' : 'Create New Supplier' }}</h4>
                </div>
                <div class="card-body">
                    <form id="supplierForm" method="POST" action="{{ isset($supplier) ? route('suppliers.update', $supplier->id) : route('suppliers.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if(isset($supplier))
                            @method('PUT')
                        @endif

                        <!-- Basic Information -->
                        <div class="section-container">
                            <h5 class="section-title">Basic Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_name">Company Name</label>
                                        <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                            id="company_name" name="company_name" 
                                            value="{{ old('company_name', $supplier->company_name ?? '') }}" required>
                                        @error('company_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_person">Contact Person</label>
                                        <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                                            id="contact_person" name="contact_person" 
                                            value="{{ old('contact_person', $supplier->contact_person ?? '') }}" required>
                                        @error('contact_person')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email Address</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                            id="email" name="email" 
                                            value="{{ old('email', $supplier->email ?? '') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone Number</label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                            id="phone" name="phone" 
                                            value="{{ old('phone', $supplier->phone ?? '') }}" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address and Additional Information -->
                        <div class="section-container mt-4">
                            <h5 class="section-title">Address and Additional Information</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">Complete Address</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                            id="address" name="address" rows="3" required>{{ old('address', $supplier->address ?? '') }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tax_number">Tax Number</label>
                                        <input type="text" class="form-control @error('tax_number') is-invalid @enderror" 
                                            id="tax_number" name="tax_number" 
                                            value="{{ old('tax_number', $supplier->tax_number ?? '') }}">
                                        @error('tax_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="registration_number">Registration Number</label>
                                        <input type="text" class="form-control @error('registration_number') is-invalid @enderror" 
                                            id="registration_number" name="registration_number" 
                                            value="{{ old('registration_number', $supplier->registration_number ?? '') }}">
                                        @error('registration_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                            <option value="active" {{ old('status', $supplier->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', $supplier->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            <option value="pending" {{ old('status', $supplier->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Materials Section -->
                        <div class="section-container mt-4">
                            <h5 class="section-title">Materials</h5>
                            
                            <!-- Material Search -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="materialSearch">Search Materials</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="materialSearch" placeholder="Type to search materials...">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="searchMaterialBtn">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="materialSearchResults" class="search-results" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Selected Materials Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered" id="selectedMaterialsTable">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Unit</th>
                                            <th>Price</th>
                                            <th>Lead Time (days)</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($supplier))
                                            @foreach($supplier->materials as $material)
                                            <tr data-material-id="{{ $material->id }}">
                                                <td>{{ $material->code }}</td>
                                                <td>{{ $material->name }}</td>
                                                <td>{{ $material->category->name }}</td>
                                                <td>{{ $material->unit }}</td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm material-price" 
                                                           name="materials[{{ $material->id }}][price]" 
                                                           value="{{ $material->pivot->price }}" step="0.01" required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm material-lead-time" 
                                                           name="materials[{{ $material->id }}][lead_time]" 
                                                           value="{{ $material->pivot->lead_time }}" required>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger remove-material">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <!-- Add New Material Button -->
                            <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                                <i class="fas fa-plus"></i> Add New Material
                            </button>
                        </div>

                        <!-- Save Supplier Button -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Save Supplier</button>
                            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMaterialModalLabel">Add New Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addMaterialForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="newMaterialName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="newMaterialName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="newMaterialCode" class="form-label">Code</label>
                        <input type="text" class="form-control" id="newMaterialCode" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label for="newMaterialDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="newMaterialDescription" name="description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="newMaterialCategory" class="form-label">Category</label>
                        <select class="form-control" id="newMaterialCategory" name="category" required>
                            <option value="">Select Category</option>
                            <option value="construction">Construction</option>
                            <option value="electrical">Electrical</option>
                            <option value="plumbing">Plumbing</option>
                            <option value="finishing">Finishing</option>
                            <option value="tools">Tools</option>
                            <option value="general">General</option>
                            <option value="other">Other</option>
                        </select>
                        <input type="text" class="form-control mt-2" id="newCustomCategory" name="custom_category" placeholder="Enter custom category" style="display: none;">
                    </div>
                    <div class="mb-3">
                        <label for="newMaterialUnit" class="form-label">Unit</label>
                        <select class="form-control" id="newMaterialUnit" name="unit" required>
                            <option value="">Select Unit</option>
                            <option value="pieces">Pieces</option>
                            <option value="meters">Meters</option>
                            <option value="kg">Kilograms</option>
                            <option value="liters">Liters</option>
                            <option value="sqm">Square Meters</option>
                            <option value="cubic">Cubic Meters</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="newMaterialBasePrice" class="form-label">Base Price</label>
                        <input type="number" class="form-control" id="newMaterialBasePrice" name="base_price" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="newMaterialSrpPrice" class="form-label">SRP</label>
                        <input type="number" class="form-control" id="newMaterialSrpPrice" name="srp_price" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="newMaterialSpecifications" class="form-label">Specifications</label>
                        <textarea class="form-control" id="newMaterialSpecifications" name="specifications" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="newMaterialImages" class="form-label">Upload Images</label>
                        <input type="file" class="form-control" id="newMaterialImages" name="images[]" multiple accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="newMaterialScopeTypes" class="form-label">Scope Types</label>
                        @php
                            $scopeTypes = $scopeTypes ?? \App\Models\ScopeType::orderBy('name')->get();
                        @endphp
                        <select class="form-control select2-multiple" id="newMaterialScopeTypes" name="scope_types[]" multiple="multiple" data-placeholder="Select scope types...">
                            @foreach($scopeTypes as $scopeType)
                                <option value="{{ $scopeType->id }}">{{ $scopeType->name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">You can select multiple scope types where this material is used.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Material</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
    .search-results {
        max-height: 300px;
        overflow-y: auto;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        position: absolute;
        z-index: 1000;
        width: 97%;
    }
    .material-result {
        transition: background-color 0.2s;
    }
    .material-result:hover {
        background-color: #f8f9fa;
    }
    .hover-bg-light:hover {
        background-color: #f8f9fa !important;
    }
    .select2-container {
        min-width: 100% !important;
        display: block !important;
        z-index: 9999 !important;
    }
    .select2-dropdown {
        z-index: 99999 !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #007bff;
        border: none;
        color: #fff;
        border-radius: 20px;
        padding: 0.25em 0.75em;
        margin-top: 0.25em;
        margin-right: 0.25em;
        font-size: 1em;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff;
        margin-right: 0.5em;
        margin-left: 0.25em;
        font-weight: bold;
        cursor: pointer;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Store new materials temporarily
    let newMaterials = [];

    // Function to restore materials from session
    function restoreMaterialsFromSession() {
        @if(session()->has('materials'))
            const materials = @json(session('materials'));
            const table = document.getElementById('selectedMaterialsTable')?.querySelector('tbody');
            if (table) {
                Object.entries(materials).forEach(([materialId, data]) => {
                    const row = document.createElement('tr');
                    row.dataset.materialId = materialId;
                    row.innerHTML = `
                        <td>${data.code || ''}</td>
                        <td>${data.name || ''}</td>
                        <td>${data.category || ''}</td>
                        <td>${data.unit || ''}</td>
                        <td>
                            <input type="number" class="form-control form-control-sm material-price" 
                                   name="materials[${materialId}][price]" 
                                   value="${data.price || 0}" step="0.01" required>
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm material-lead-time" 
                                   name="materials[${materialId}][lead_time]" 
                                   value="${data.lead_time || 0}" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger remove-material">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    `;
                    table.appendChild(row);
                });
            }
        @endif

        @if(session()->has('new_materials'))
            const newMaterialsData = @json(session('new_materials'));
            const table = document.getElementById('selectedMaterialsTable')?.querySelector('tbody');
            if (table) {
                Object.entries(newMaterialsData).forEach(([tempId, data]) => {
                    // Add to newMaterials array
                    newMaterials.push({
                        tempId: tempId,
                        name: data.name,
                        code: data.code,
                        description: data.description,
                        category: data.category,
                        unit: data.unit,
                        base_price: data.base_price,
                        srp_price: data.srp_price,
                        specifications: data.specifications,
                        scope_types: data.scope_types || [],
                        images: data.images || []
                    });

                    // Add to table
                    const row = document.createElement('tr');
                    row.dataset.materialId = tempId;
                    row.innerHTML = `
                        <td>${data.code}</td>
                        <td>${data.name}</td>
                        <td>${data.category}</td>
                        <td>${data.unit}</td>
                        <td>
                            <input type="number" class="form-control form-control-sm material-price" 
                                   name="new_materials[${tempId}][price]" 
                                   value="${data.price || data.base_price}" step="0.01" required>
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm material-lead-time" 
                                   name="new_materials[${tempId}][lead_time]" 
                                   value="${data.lead_time || 0}" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger remove-material">
                                <i class="fas fa-times"></i>
                            </button>
                            ${data.images.map((image, index) => `<input type="hidden" name="new_materials[${tempId}][images][${index}][path]" value="${image.path}">`).join('')}
                        </td>
                    `;
                    table.appendChild(row);
                });
            }
        @endif
    }

    // Call restore function when page loads
    restoreMaterialsFromSession();

    // Function to reset the modal form
    function resetModalForm() {
        const form = document.getElementById('addMaterialForm');
        if (form) {
            form.reset();
        }
        
        // Reset Select2
        const $select = $('#newMaterialScopeTypes');
        if ($select.length) {
            if ($select.data('select2')) {
                $select.val(null).trigger('change');
            }
        }
        
        // Hide custom category input
        const customCategory = document.getElementById('newCustomCategory');
        if (customCategory) {
            customCategory.style.display = 'none';
            customCategory.required = false;
        }
    }

    // When modal is about to be shown
    $('#addMaterialModal').on('show.bs.modal', function () {
        lastFocusedElement = document.activeElement;
        resetModalForm(); // Reset form when modal opens
    });

    // When modal is shown
    $('#addMaterialModal').on('shown.bs.modal', function () {
        // Focus the first input in the modal
        const firstInput = this.querySelector('input, select, textarea');
        if (firstInput) {
            firstInput.focus();
        }

        // Initialize Select2
        var $select = $('#newMaterialScopeTypes');
        if ($select.length && !$select.data('select2')) {
            $select.select2({
                width: '100%',
                placeholder: 'Select scope types...',
                minimumResultsForSearch: 0,
                dropdownParent: $('#addMaterialModal')
            });
            $select.on('select2:open', function() {
                $('.select2-search__field').prop('placeholder', 'Search or select scope types...');
            });
        }
    });

    // When modal is about to be hidden
    $('#addMaterialModal').on('hide.bs.modal', function () {
        // Remove focus from any focused element inside the modal
        if (document.activeElement && this.contains(document.activeElement)) {
            document.activeElement.blur();
        }
        resetModalForm(); // Reset form when modal closes
    });

    // When modal is hidden
    $('#addMaterialModal').on('hidden.bs.modal', function () {
        // Return focus to the element that opened the modal
        if (lastFocusedElement) {
            lastFocusedElement.focus();
        }
    });

    // Material search functionality
    const materialSearch = document.getElementById('materialSearch');
    const materialSearchResults = document.getElementById('materialSearchResults');
    const selectedMaterialsTable = document.getElementById('selectedMaterialsTable')?.getElementsByTagName('tbody')[0];
    let searchTimeout;

    if (materialSearch) {
        materialSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (materialSearchResults) {
                materialSearchResults.innerHTML = '<div class="p-2">Searching...</div>';
                materialSearchResults.style.display = 'block';
            }

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('materials.search') }}?query=${encodeURIComponent(query)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(materials => {
                    if (materialSearchResults) {
                        if (Array.isArray(materials) && materials.length > 0) {
                            const html = materials.map(material => `
                                <div class="material-result p-2 border-bottom hover-bg-light" style="cursor: pointer;" 
                                     data-id="${material.id}"
                                     data-name="${material.name}"
                                     data-code="${material.code || ''}"
                                     data-unit="${material.unit}"
                                     data-category="${material.category ? material.category.name : ''}"
                                     data-base-price="${material.base_price || 0}">
                                    <div>
                                        <strong>${material.name}</strong> ${material.code ? `(${material.code})` : ''}
                                        <br>
                                        <small class="text-muted">
                                            ${material.category ? material.category.name : 'No Category'} | ${material.unit}
                                            ${material.base_price ? ` | Base Price: ₱${material.base_price}` : ''}
                                        </small>
                                    </div>
                                </div>
                            `).join('');
                            
                            materialSearchResults.innerHTML = html;
                        } else {
                            materialSearchResults.innerHTML = '<div class="p-2">No materials found</div>';
                        }
                    }
                })
                .catch(error => {
                    console.error('Search Error:', error);
                    if (materialSearchResults) {
                        materialSearchResults.innerHTML = `<div class="p-2 text-danger">Error searching materials: ${error.message}</div>`;
                    }
                });
            }, 300);
        });

        // Show search results on focus
        materialSearch.addEventListener('focus', function() {
            if (this.value.trim() !== '' && materialSearchResults) {
                materialSearchResults.style.display = 'block';
            }
        });
    }

    // Add material when clicked
    if (materialSearchResults) {
        materialSearchResults.addEventListener('click', function(e) {
            const materialResult = e.target.closest('.material-result');
            if (!materialResult || !selectedMaterialsTable) return;

            const materialId = materialResult.dataset.id;
            if (document.querySelector(`tr[data-material-id="${materialId}"]`)) {
                alert('This material is already added');
                return;
            }

            const row = document.createElement('tr');
            row.dataset.materialId = materialId;
            row.innerHTML = `
                <td>${materialResult.dataset.code}</td>
                <td>${materialResult.dataset.name}</td>
                <td>${materialResult.dataset.category}</td>
                <td>${materialResult.dataset.unit}</td>
                <td>
                    <input type="number" class="form-control form-control-sm material-price" 
                           name="materials[${materialId}][price]" 
                           value="${materialResult.dataset.basePrice}" step="0.01" required>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm material-lead-time" 
                           name="materials[${materialId}][lead_time]" 
                           value="0" required>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-material">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            `;

            selectedMaterialsTable.appendChild(row);
            if (materialSearch) {
                materialSearch.value = '';
            }
            materialSearchResults.style.display = 'none';
        });
    }

    // Remove material when clicked
    if (selectedMaterialsTable) {
        selectedMaterialsTable.addEventListener('click', function(e) {
            if (e.target.closest('.remove-material')) {
                const row = e.target.closest('tr');
                const materialId = row.dataset.materialId;
                
                // If it's a new material, remove it from the newMaterials array
                if (materialId.startsWith('new_')) {
                    newMaterials = newMaterials.filter(m => m.tempId !== materialId);
                }
                
                row.remove();
            }
        });
    }

    // Hide search results when clicking outside
    document.addEventListener('click', function(e) {
        if (materialSearch && materialSearchResults && 
            !materialSearch.contains(e.target) && 
            !materialSearchResults.contains(e.target)) {
            materialSearchResults.style.display = 'none';
        }
    });

    // Form validation
    const form = document.getElementById('supplierForm');
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    }

    // Show/hide custom category input
    const newMaterialCategory = document.getElementById('newMaterialCategory');
    const newCustomCategory = document.getElementById('newCustomCategory');
    
    if (newMaterialCategory && newCustomCategory) {
        newMaterialCategory.addEventListener('change', function() {
            if (this.value === 'other') {
                newCustomCategory.style.display = '';
                newCustomCategory.required = true;
            } else {
                newCustomCategory.style.display = 'none';
                newCustomCategory.required = false;
            }
        });
    }

    // Update the form submit handler
    const addMaterialForm = document.getElementById('addMaterialForm');
    if (addMaterialForm) {
        addMaterialForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const code = formData.get('code');

            // Check for duplicate code
            fetch(`{{ route('materials.check-code') }}?code=${encodeURIComponent(code)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    alert('Warning: A material with this code already exists. Please use a different code.');
                    return;
                }

                // If code is unique, proceed with adding the material
                const tempId = 'new_' + Date.now();
                
                // Add the material to our temporary array
                const newMaterial = {
                    tempId: tempId,
                    name: formData.get('name'),
                    code: formData.get('code'),
                    description: formData.get('description'),
                    category: formData.get('category'),
                    unit: formData.get('unit'),
                    base_price: formData.get('base_price'),
                    srp_price: formData.get('srp_price'),
                    specifications: formData.get('specifications'),
                    scope_types: $('#newMaterialScopeTypes').val() || []
                };
                
                // Handle image files
                const imageFiles = formData.getAll('images[]');
                if (imageFiles.length > 0) {
                    newMaterial.images = imageFiles;
                }
                
                newMaterials.push(newMaterial);
                
                // Add to the table
                const table = document.getElementById('selectedMaterialsTable')?.querySelector('tbody');
                if (table) {
                    const row = document.createElement('tr');
                    row.dataset.materialId = tempId;
                    row.innerHTML = `
                        <td>${newMaterial.code}</td>
                        <td>${newMaterial.name}</td>
                        <td>${newMaterial.category}</td>
                        <td>${newMaterial.unit}</td>
                        <td><input type="number" class="form-control form-control-sm material-price" name="new_materials[${tempId}][price]" value="${newMaterial.base_price}" step="0.01" required></td>
                        <td><input type="number" class="form-control form-control-sm material-lead-time" name="new_materials[${tempId}][lead_time]" value="0" required></td>
                        <td><button type="button" class="btn btn-sm btn-danger remove-material"><i class="fas fa-times"></i></button></td>
                    `;
                    table.appendChild(row);
                }
                
                // Reset the form and close modal
                resetModalForm();
                const modal = bootstrap.Modal.getInstance(document.getElementById('addMaterialModal'));
                if (modal) {
                    modal.hide();
                }
            })
            .catch(error => {
                console.error('Error checking material code:', error);
                alert('Error checking material code. Please try again.');
            });
        });
    }

    // Add hidden inputs for new materials before form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            // Add hidden inputs for each new material
            newMaterials.forEach(material => {
                Object.entries(material).forEach(([key, value]) => {
                    if (key !== 'tempId') {
                        if (key === 'scope_types') {
                            // Handle scope_types array separately
                            value.forEach((scopeTypeId, index) => {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = `new_materials[${material.tempId}][${key}][]`;
                                input.value = scopeTypeId;
                                form.appendChild(input);
                            });
                        } else if (key === 'images') {
                            // Handle image files
                            value.forEach((file, index) => {
                                const input = document.createElement('input');
                                input.type = 'file';
                                input.name = `new_materials[${material.tempId}][${key}][]`;
                                input.style.display = 'none';
                                
                                // Create a new FileList-like object
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(file);
                                input.files = dataTransfer.files;
                                
                                form.appendChild(input);
                            });
                        } else {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = `new_materials[${material.tempId}][${key}]`;
                            input.value = value;
                            form.appendChild(input);
                        }
                    }
                });
            });
        });
    }
});
</script>
@endpush
@endsection 