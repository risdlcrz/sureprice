@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h4 class="card-title mb-0">Add Material to Your Listings</h4>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-create-tab" data-bs-toggle="pill" data-bs-target="#pills-create" type="button" role="tab" aria-controls="pills-create" aria-selected="true">Create New Material</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-link-tab" data-bs-toggle="pill" data-bs-target="#pills-link" type="button" role="tab" aria-controls="pills-link" aria-selected="false">Link Existing Material</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-create" role="tabpanel" aria-labelledby="pills-create-tab">
                            <form action="{{ route('supplier.materials.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label">Material Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="code" class="form-label">Material Code</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" readonly required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description (optional)</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="unit" class="form-label">Unit of Measure (e.g., pcs, kg, meter)</label>
                                    <input type="text" class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit" value="{{ old('unit') }}" required>
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="price" class="form-label">Your Unit Price (₱)</label>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" min="0" step="0.01" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Add Material</button>
                                    <a href="{{ route('supplier.materials.index') }}" class="btn btn-secondary mt-2">Cancel</a>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="pills-link" role="tabpanel" aria-labelledby="pills-link-tab">
                            <form action="{{ route('supplier.materials.link') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="material_search" class="form-label">Search for Material</label>
                                    <input type="text" class="form-control" id="material_search" placeholder="Start typing to search for materials..." autocomplete="off">
                                    <input type="hidden" id="material_id" name="material_id">
                                    <div id="material_search_results" class="list-group mt-2"></div>
                                </div>
                                <div id="material_details" class="mb-3" style="display: none;">
                                    <h5>Selected Material</h5>
                                    <p><strong>Name:</strong> <span id="selected_material_name"></span></p>
                                    <p><strong>Code:</strong> <span id="selected_material_code"></span></p>
                                    <p><strong>Unit:</strong> <span id="selected_material_unit"></span></p>
                                    <p><strong>Suggested Retail Price (SRP):</strong> ₱<span id="selected_material_srp"></span></p>
                                </div>
                                <div class="mb-3">
                                    <label for="link_price" class="form-label">Your Unit Price (₱)</label>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" id="link_price" name="price" min="0" step="0.01" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Link Material</button>
                                    <a href="{{ route('supplier.materials.index') }}" class="btn btn-secondary mt-2">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category_id');
        const codeInput = document.getElementById('code');
        function updateCode() {
            const cat = categorySelect.options[categorySelect.selectedIndex]?.text || '';
            if (cat) {
                codeInput.value = (cat.substring(0,3).toUpperCase() + '-' + Math.floor(1000 + Math.random() * 9000));
            } else {
                codeInput.value = '';
            }
        }
        categorySelect.addEventListener('change', updateCode);

        // For linking existing materials
        const materialSearchInput = document.getElementById('material_search');
        const materialIdInput = document.getElementById('material_id');
        const materialDetailsDiv = document.getElementById('material_details');
        const searchResultsDiv = document.getElementById('material_search_results');

        materialSearchInput.addEventListener('keyup', function() {
            const searchTerm = this.value;
            
            if (searchTerm.length < 2) {
                searchResultsDiv.innerHTML = '';
                materialDetailsDiv.style.display = 'none';
                materialIdInput.value = '';
                return; 
            }

            fetch(`{{ route('supplier.materials.search') }}?term=${searchTerm}`)
                .then(response => response.json())
                .then(data => {
                    searchResultsDiv.innerHTML = ''; // Clear previous results
                    if (data.length > 0) {
                        data.forEach(material => {
                            const resultItem = document.createElement('a');
                            resultItem.href = '#';
                            resultItem.classList.add('list-group-item', 'list-group-item-action');
                            resultItem.innerHTML = `<strong>${material.name}</strong> <small class="text-muted">(${material.code})</small>`;
                            
                            resultItem.addEventListener('click', function(e) {
                                e.preventDefault();
                                // Populate the form
                                materialSearchInput.value = material.name;
                                materialIdInput.value = material.id;
                                document.getElementById('selected_material_name').innerText = material.name;
                                document.getElementById('selected_material_code').innerText = material.code;
                                document.getElementById('selected_material_unit').innerText = material.unit;
                                const srp = material.srp_price || material.base_price || '0.00';
                                document.getElementById('selected_material_srp').innerText = parseFloat(srp).toFixed(2);
                                
                                // Show details and hide results
                                materialDetailsDiv.style.display = 'block';
                                searchResultsDiv.innerHTML = '';
                            });
                            
                            searchResultsDiv.appendChild(resultItem);
                        });
                    } else {
                        searchResultsDiv.innerHTML = '<div class="list-group-item">No materials found.</div>';
                    }
                });
        });
    });
</script>
@endpush 