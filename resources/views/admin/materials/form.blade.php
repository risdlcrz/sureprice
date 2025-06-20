@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h4 class="card-title mb-0">{{ isset($material) ? 'Edit Material' : 'Create New Material' }}</h4>
                </div>
                <div class="card-body">
                    <form id="materialForm" method="POST" action="{{ isset($material) ? route('materials.update', $material->id) : route('materials.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if(isset($material))
                            @method('PUT')
                        @endif

                        <!-- Basic Information -->
                        <div class="section-container">
                            <h5 class="section-title">Basic Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Material Name</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                            id="name" name="name" 
                                            value="{{ old('name', $material->name ?? '') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="code">Material Code</label>
                                        <input type="text" class="form-control" id="code" name="code" value="{{ old('code', $material->code ?? '') }}" readonly required>
                                        <small class="form-text text-muted">Material code will be generated automatically.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                            id="description" name="description" rows="3">{{ old('description', $material->description ?? '') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="category">Category</label>
                                        <select class="form-control @error('category_id') is-invalid @enderror" 
                                            id="category" name="category_id" required>
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $material->category_id ?? '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <input type="text" class="form-control mt-2" id="custom_category" name="custom_category" placeholder="Enter custom category" style="display: none;">
                                        <small id="custom_category_preview" class="form-text text-primary" style="display: none;"></small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="unit">Unit of Measurement</label>
                                        <select class="form-control @error('unit') is-invalid @enderror" 
                                            id="unit" name="unit" required>
                                            <option value="">Select Unit</option>
                                            <option value="pieces" {{ old('unit', $material->unit ?? '') == 'pieces' ? 'selected' : '' }}>Pieces</option>
                                            <option value="meters" {{ old('unit', $material->unit ?? '') == 'meters' ? 'selected' : '' }}>Meters</option>
                                            <option value="kg" {{ old('unit', $material->unit ?? '') == 'kg' ? 'selected' : '' }}>Kilograms</option>
                                            <option value="liters" {{ old('unit', $material->unit ?? '') == 'liters' ? 'selected' : '' }}>Liters</option>
                                            <option value="sqm" {{ old('unit', $material->unit ?? '') == 'sqm' ? 'selected' : '' }}>Square Meters</option>
                                            <option value="cubic" {{ old('unit', $material->unit ?? '') == 'cubic' ? 'selected' : '' }}>Cubic Meters</option>
                                        </select>
                                        @error('unit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="base_price">Base Price</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">₱</span>
                                            </div>
                                            <input type="number" class="form-control @error('base_price') is-invalid @enderror" 
                                                id="base_price" name="base_price" step="0.01" min="0"
                                                value="{{ old('base_price', $material->base_price ?? '') }}" required>
                                            @error('base_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="warranty_period">Warranty Period (months)</label>
                                        <input type="number" class="form-control @error('warranty_period') is-invalid @enderror" 
                                            id="warranty_period" name="warranty_period" min="0" step="1"
                                            value="{{ old('warranty_period', $material->warranty_period ?? '') }}">
                                        @error('warranty_period')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Leave blank if no warranty.</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="srp_price">SRP Price</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">₱</span>
                                            </div>
                                            <input type="number" class="form-control @error('srp_price') is-invalid @enderror" 
                                                id="srp_price" name="srp_price" step="0.01" min="0"
                                                value="{{ old('srp_price', $material->srp_price ?? '') }}" required>
                                            @error('srp_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="form-text text-muted">Suggested Retail Price for cost comparison</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" id="is_per_area" name="is_per_area" value="1"
                                               {{ old('is_per_area', $material->is_per_area ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_per_area">Is Per Area Calculation</label>
                                        <small class="form-text text-muted">Check if material quantity is calculated based on area (e.g., paint per sqm).</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-check" id="is_wall_material_group">
                                        <input type="checkbox" class="form-check-input" id="is_wall_material" name="is_wall_material" value="1"
                                               {{ old('is_wall_material', $material->is_wall_material ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_wall_material">Is Wall Material</label>
                                        <small class="form-text text-muted">Check if this material is primarily used for wall areas.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4" id="coverage_rate_group">
                                    <div class="form-group">
                                        <label for="coverage_rate">Coverage Rate (unit per sqm)</label>
                                        <input type="number" class="form-control @error('coverage_rate') is-invalid @enderror"
                                               id="coverage_rate" name="coverage_rate" step="0.01" min="0.01"
                                               value="{{ old('coverage_rate', $material->coverage_rate ?? '1') }}">
                                        @error('coverage_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">e.g., 10 for 10 sqm per liter of paint.</small>
                                    </div>
                                </div>
                                <div class="col-md-4" id="minimum_quantity_group">
                                    <div class="form-group">
                                        <label for="minimum_quantity">Minimum Quantity</label>
                                        <input type="number" class="form-control @error('minimum_quantity') is-invalid @enderror"
                                               id="minimum_quantity" name="minimum_quantity" step="1" min="0"
                                               value="{{ old('minimum_quantity', $material->minimum_quantity ?? '1') }}">
                                        @error('minimum_quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Default quantity if not per area calculation.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="suppliers">Suppliers</label>
                                        <select class="form-control select2 @error('suppliers') is-invalid @enderror" 
                                            id="suppliers" name="suppliers[]" multiple>
                                            @if(isset($material) && $material->suppliers)
                                                @foreach($material->suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}" selected>
                                                        {{ $supplier->company_name ?? $supplier->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('suppliers')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Specifications -->
                        <div class="section-container mt-4">
                            <h5 class="section-title">Specifications</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="specifications">Technical Specifications</label>
                                        <textarea class="form-control @error('specifications') is-invalid @enderror" 
                                            id="specifications" name="specifications" rows="4">{{ old('specifications', $material->specifications ?? '') }}</textarea>
                                        @error('specifications')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Scope Types -->
                        <div class="section-container mt-4">
                            <h5 class="section-title">Associated Scope Types</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="scope_types">Select Scope Types where this material is used</label>
                                        <select class="form-control select2-multiple @error('scope_types') is-invalid @enderror" 
                                                id="scope_types" 
                                                name="scope_types[]" 
                                                multiple="multiple"
                                                data-placeholder="Select scope types...">
                                            @foreach($scopeTypes ?? [] as $scopeType)
                                                <option value="{{ $scopeType->id }}"
                                                    {{ (isset($material) && $material->scopeTypes->contains($scopeType->id)) ? 'selected' : '' }}>
                                                    {{ $scopeType->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('scope_types')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            You can select multiple scope types where this material is used. 
                                            For example, paint would be used in "Painting Work", while cleaning supplies might be used in multiple types of work.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Images -->
                        <div class="section-container mt-4">
                            <h5 class="section-title">Images</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="images">Upload Images</label>
                                        <input type="file" class="form-control-file @error('images') is-invalid @enderror" 
                                            id="images" name="images[]" multiple accept="image/*">
                                        @error('images')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            @if(isset($material) && $material->images)
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="existing-images">
                                        @foreach($material->images as $image)
                                        <div class="image-container d-inline-block position-relative m-2">
                                            <img src="{{ Storage::url($image) }}" alt="Material Image" class="img-thumbnail" style="max-width: 150px;">
                                            <button type="button" class="btn btn-danger btn-sm position-absolute" style="top: 0; right: 0;"
                                                onclick="removeImage('{{ $image }}')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Save Material</button>
                            <a href="{{ route('materials.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
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
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2 for multiple selection
    $('.select2-multiple').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Select scope types...',
        allowClear: true
    });

    $('#suppliers').select2({
        placeholder: 'Search for suppliers...',
        allowClear: true,
        ajax: {
            url: '{{ route("admin.suppliers.search") }}',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data.data.map(function(item) {
                        return {
                            id: item.id,
                            text: item.company_name || item.name
                        };
                    }),
                    pagination: {
                        more: data.current_page < data.last_page
                    }
                };
            },
            cache: true
        },
        minimumInputLength: 2,
        templateResult: formatSupplier,
        templateSelection: formatSupplierSelection
    });

    function formatSupplier(supplier) {
        if (supplier.loading) {
            return supplier.text;
        }
        return $('<span>' + supplier.text + '</span>');
    }

    function formatSupplierSelection(supplier) {
        return supplier.text;
    }

    // Form validation
    const form = document.getElementById('materialForm');
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    }

    // Image removal function
    window.removeImage = function(imagePath) {
        if (confirm('Are you sure you want to remove this image?')) {
            fetch(`/api/materials/remove-image`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    material_id: '{{ $material->id ?? "" }}',
                    image_path: imagePath
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const imageContainer = document.querySelector(`img[src*="${imagePath}"]`).closest('.image-container');
                    imageContainer.remove();
                } else {
                    alert('Failed to remove image');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error removing image');
            });
        }
    };

    $('#category').change(function() {
        if ($(this).val() === 'other') {
            $('#custom_category').show().prop('required', true);
            $('#custom_category_preview').show();
        } else {
            $('#custom_category').hide().prop('required', false);
            $('#custom_category_preview').hide();
        }
    });

    $('#custom_category').on('input', function() {
        var val = $(this).val();
        if (val) {
            $('#custom_category_preview').text('Selected Category: ' + val);
        } else {
            $('#custom_category_preview').text('');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const isPerAreaCheckbox = document.getElementById('is_per_area');
        const coverageRateGroup = document.getElementById('coverage_rate_group');
        const minimumQuantityGroup = document.getElementById('minimum_quantity_group');
        const isWallMaterialGroup = document.getElementById('is_wall_material_group');

        function toggleVisibility() {
            if (isPerAreaCheckbox.checked) {
                coverageRateGroup.style.display = 'block';
                minimumQuantityGroup.style.display = 'none';
                isWallMaterialGroup.style.display = 'block'; // Always show for per-area as it's relevant
            } else {
                coverageRateGroup.style.display = 'none';
                minimumQuantityGroup.style.display = 'block';
                isWallMaterialGroup.style.display = 'none'; // Hide if not per area
            }
        }

        isPerAreaCheckbox.addEventListener('change', toggleVisibility);

        // Initial call to set correct visibility based on current state
        toggleVisibility();
    });

    function slugify(text) {
        return text.toString().toLowerCase().replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-')
            .replace(/^-+/, '')
            .replace(/-+$/, '');
    }
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category');
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
    });
});
</script>
@endpush
@endsection 