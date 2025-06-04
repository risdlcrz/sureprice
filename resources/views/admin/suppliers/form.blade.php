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
                        </div>

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
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const materialSearch = document.getElementById('materialSearch');
    const materialSearchResults = document.getElementById('materialSearchResults');
    const selectedMaterialsTable = document.getElementById('selectedMaterialsTable').getElementsByTagName('tbody')[0];
    let searchTimeout;

    // Material search
    materialSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        // Always show results, even with empty query
        materialSearchResults.innerHTML = '<div class="p-2">Searching...</div>';
        materialSearchResults.style.display = 'block';

        searchTimeout = setTimeout(() => {
            // Use the materials.search route
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
                console.log('Search results:', materials); // Debug log
                
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
            })
            .catch(error => {
                console.error('Search Error:', error);
                materialSearchResults.innerHTML = `<div class="p-2 text-danger">Error searching materials: ${error.message}</div>`;
            });
        }, 300);
    });

    // Add material when clicked
    materialSearchResults.addEventListener('click', function(e) {
        const materialResult = e.target.closest('.material-result');
        if (!materialResult) return;

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
        materialSearch.value = '';
        materialSearchResults.style.display = 'none';
    });

    // Remove material when clicked
    selectedMaterialsTable.addEventListener('click', function(e) {
        if (e.target.closest('.remove-material')) {
            e.target.closest('tr').remove();
        }
    });

    // Hide search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!materialSearch.contains(e.target) && !materialSearchResults.contains(e.target)) {
            materialSearchResults.style.display = 'none';
        }
    });

    // Show search results on focus
    materialSearch.addEventListener('focus', function() {
        if (this.value.trim() !== '') {
            materialSearchResults.style.display = 'block';
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
});
</script>
@endpush
@endsection 