@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Add New Supplier</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('supplier-rankings.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Supplier Name</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <input id="contact_person" type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                                       name="contact_person" value="{{ old('contact_person') }}">
                                @error('contact_person')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input id="website" type="url" class="form-control @error('website') is-invalid @enderror" 
                                       name="website" value="{{ old('website') }}">
                                @error('website')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea id="address" class="form-control @error('address') is-invalid @enderror" 
                                      name="address" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Materials</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="material_input" placeholder="Add material">
                                <button class="btn btn-outline-secondary" type="button" id="add_material_btn">Add</button>
                            </div>
                            <div id="materials_list" class="d-flex flex-wrap gap-2 mb-3">
                                @if(old('materials'))
                                    @foreach(json_decode(old('materials')) as $material)
                                        <span class="badge bg-primary me-1 mb-1 d-inline-flex align-items-center">
                                            {{ $material }}
                                            <button type="button" class="btn-close btn-close-white ms-2" 
                                                    style="font-size: 0.5rem;" data-material="{{ $material }}"></button>
                                        </span>
                                    @endforeach
                                @endif
                            </div>
                            <input type="hidden" name="materials" id="materials_input" 
                                   value="{{ old('materials', '[]') }}">
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('supplier-rankings.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Supplier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const materialInput = document.getElementById('material_input');
    const addMaterialBtn = document.getElementById('add_material_btn');
    const materialsList = document.getElementById('materials_list');
    const materialsInput = document.getElementById('materials_input');
    
    // Load existing materials from hidden input
    let materials = [];
    if (materialsInput.value) {
        try {
            materials = JSON.parse(materialsInput.value);
        } catch (e) {
            console.error('Error parsing materials:', e);
        }
    }
    
    // Function to update the materials list display
    function updateMaterialsList() {
        if (!materialsList) return;
        
        materialsList.innerHTML = '';
        materials.forEach((material, index) => {
            const badge = document.createElement('span');
            badge.className = 'badge bg-primary me-1 mb-1 d-inline-flex align-items-center';
            badge.innerHTML = `
                ${material}
                <button type="button" class="btn-close btn-close-white ms-2" 
                        style="font-size: 0.5rem;" 
                        data-index="${index}"></button>
            `;
            materialsList.appendChild(badge);
        });
        
        // Add event listeners to remove buttons
        materialsList.querySelectorAll('.btn-close').forEach(btn => {
            btn.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                materials.splice(index, 1);
                updateMaterialsList();
                updateMaterialsInput();
            });
        });
    }
    
    // Function to update the hidden input with materials array
    function updateMaterialsInput() {
        if (materialsInput) {
            materialsInput.value = JSON.stringify(materials);
        }
    }
    
    // Add material when clicking the add button
    if (addMaterialBtn && materialInput) {
        addMaterialBtn.addEventListener('click', function() {
            const material = materialInput.value.trim();
            if (material && !materials.includes(material)) {
                materials.push(material);
                materialInput.value = '';
                updateMaterialsList();
                updateMaterialsInput();
            }
        });
        
        // Also add material when pressing Enter
        materialInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addMaterialBtn.click();
            }
        });
    }
    
    // Initialize the materials list
    updateMaterialsList();
});
</script>
@endpush
@endsection
