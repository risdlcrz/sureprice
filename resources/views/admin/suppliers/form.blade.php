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
                                            <input type="text" class="form-control" id="materialSearch" placeholder="Search by name or code...">
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
    .material-result {
        padding: 0.5rem;
        cursor: pointer;
        border-bottom: 1px solid #dee2e6;
    }
    .material-result:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form validation
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

    // Material search functionality
    const materialSearch = document.getElementById('materialSearch');
    const searchMaterialBtn = document.getElementById('searchMaterialBtn');
    const materialSearchResults = document.getElementById('materialSearchResults');
    const selectedMaterialsTable = document.getElementById('selectedMaterialsTable').getElementsByTagName('tbody')[0];
    let searchTimeout;

    if (materialSearch && searchMaterialBtn && materialSearchResults) {
        materialSearch.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(searchMaterials, 300);
        });

        searchMaterialBtn.addEventListener('click', searchMaterials);
    }

    function searchMaterials() {
        const query = materialSearch.value.trim();
        if (query.length < 2) {
            materialSearchResults.style.display = 'none';
            return;
        }

        fetch(`/api/materials/search?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    materialSearchResults.innerHTML = data.map(material => `
                        <div class="material-result" data-material='${JSON.stringify(material)}'>
                            <strong>${material.name}</strong> (${material.code})<br>
                            <small>${material.category ? material.category.name : 'No Category'} - ${material.unit}</small>
                        </div>
                    `).join('');
                    materialSearchResults.style.display = 'block';
                } else {
                    materialSearchResults.innerHTML = '<div class="p-2">No materials found</div>';
                    materialSearchResults.style.display = 'block';
                }
            })
            .catch(() => {
                materialSearchResults.innerHTML = '<div class="p-2 text-danger">Error searching materials</div>';
                materialSearchResults.style.display = 'block';
            });
    }

    // Handle material selection
    materialSearchResults.addEventListener('click', function(e) {
        const materialResult = e.target.closest('.material-result');
        if (!materialResult) return;

        const material = JSON.parse(materialResult.dataset.material);
        if (document.querySelector(`tr[data-material-id="${material.id}"]`)) {
            alert('This material is already added');
            return;
        }

        const row = document.createElement('tr');
        row.dataset.materialId = material.id;
        row.innerHTML = `
            <td>${material.code}</td>
            <td>${material.name}</td>
            <td>${material.category.name}</td>
            <td>${material.unit}</td>
            <td>
                <input type="number" class="form-control form-control-sm material-price" 
                       name="materials[${material.id}][price]" 
                       value="${material.base_price}" step="0.01" required>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm material-lead-time" 
                       name="materials[${material.id}][lead_time]" 
                       value="0" required>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-material">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;
        selectedMaterialsTable.appendChild(row);
        materialSearchResults.style.display = 'none';
        materialSearch.value = '';
    });

    // Handle material removal
    selectedMaterialsTable.addEventListener('click', function(e) {
        if (e.target.closest('.remove-material')) {
            e.target.closest('tr').remove();
        }
    });
});
</script>
@endpush
@endsection 