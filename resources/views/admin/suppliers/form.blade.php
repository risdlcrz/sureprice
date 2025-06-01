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
                                        <label for="name">Company Name</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                            id="name" name="name" 
                                            value="{{ old('name', $supplier->name ?? '') }}" required>
                                        @error('name')
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

                        <!-- Address Information -->
                        <div class="section-container mt-4">
                            <h5 class="section-title">Address Information</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">Street Address</label>
                                        <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                            id="address" name="address" 
                                            value="{{ old('address', $supplier->address ?? '') }}" required>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="city">City</label>
                                        <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                            id="city" name="city" 
                                            value="{{ old('city', $supplier->city ?? '') }}" required>
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="state">State/Province</label>
                                        <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                            id="state" name="state" 
                                            value="{{ old('state', $supplier->state ?? '') }}" required>
                                        @error('state')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="postal_code">Postal Code</label>
                                        <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                            id="postal_code" name="postal_code" 
                                            value="{{ old('postal_code', $supplier->postal_code ?? '') }}" required>
                                        @error('postal_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Materials Section -->
                        <div class="section-container mt-4">
                            <h5 class="section-title">Materials</h5>
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
                                @if(isset($supplier) && $supplier->materials)
                                    @foreach($supplier->materials as $material)
                                    <div class="material-item card mb-2">
                                        <div class="card-body py-2">
                                            <div class="row align-items-center">
                                                <div class="col-md-4">
                                                    <strong>{{ $material->name }}</strong>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" class="form-control form-control-sm" 
                                                        name="materials[{{ $material->id }}][price]" 
                                                        value="{{ $material->pivot->price }}" 
                                                        placeholder="Price" step="0.01" min="0">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control form-control-sm" 
                                                        name="materials[{{ $material->id }}][notes]" 
                                                        value="{{ $material->pivot->notes }}" 
                                                        placeholder="Notes">
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
    const selectedMaterials = document.getElementById('selectedMaterials');
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
        if (document.querySelector(`input[name="materials[${material.id}][price]"]`)) {
            alert('This material is already added');
            return;
        }

        const materialHtml = `
            <div class="material-item card mb-2">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <strong>${material.name}</strong>
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control form-control-sm" 
                                name="materials[${material.id}][price]" 
                                value="${material.base_price || ''}" 
                                placeholder="Price" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" 
                                name="materials[${material.id}][notes]" 
                                placeholder="Notes">
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

    // Add remove functionality to existing materials
    document.querySelectorAll('.remove-material').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.material-item').remove();
        });
    });

    // Close search results when clicking outside
    document.addEventListener('click', (e) => {
        if (!materialSearchResults.contains(e.target) && e.target !== materialSearch && e.target !== searchMaterialBtn) {
            materialSearchResults.style.display = 'none';
        }
    });
});
</script>
@endpush
@endsection 