@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h4 class="card-title mb-0">Edit Request for Quotation: {{ $rfq->rfq_number }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('procurement.rfqs.update', $rfq) }}" method="POST" id="rfqForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <div class="mb-4">
                            <h5 class="mb-3">Basic Information</h5>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title', $rfq->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" required>{{ old('description', $rfq->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="due_date" class="form-label">Due Date</label>
                                    <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                           id="due_date" name="due_date" value="{{ old('due_date', $rfq->due_date->format('Y-m-d')) }}" required>
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Materials -->
                        <div class="mb-4">
                            <h5 class="mb-3">Materials</h5>
                            <div id="materials-container">
                                <!-- Materials will be added here by JavaScript -->
                            </div>
                            <button type="button" class="btn btn-secondary" onclick="addMaterial()">Add Material</button>
                        </div>

                        <!-- Suppliers -->
                        <div class="mb-4">
                            <h5 class="mb-3">Select Suppliers</h5>
                            <div class="row">
                                @foreach($suppliers as $supplier)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input @error('suppliers') is-invalid @enderror" 
                                               type="checkbox" name="suppliers[]" 
                                               value="{{ $supplier->id }}" 
                                               id="supplier_{{ $supplier->id }}"
                                               {{ in_array($supplier->id, old('suppliers', $rfq->suppliers->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="supplier_{{ $supplier->id }}">
                                            {{ $supplier->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @error('suppliers')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Update RFQ</button>
                            <a href="{{ route('procurement.rfqs.show', $rfq) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let materialCount = 0;

function addMaterial(material = null, quantity = 1) {
    const container = document.getElementById('materials-container');
    const newMaterialItem = document.createElement('div');
    newMaterialItem.classList.add('material-item', 'mb-3');

    let materialOptions = '';
    @foreach($materials as $_material)
        materialOptions += `<option value="{{ $_material->id }}" ${material && material.id === {{ $_material->id }} ? 'selected' : ''}>{{ $_material->name }} ({{ $_material->code }})</option>`;
    @endforeach

    newMaterialItem.innerHTML = `
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Material</label>
                <select class="form-select" 
                        name="materials[${materialCount}][material_id]" required>
                    <option value="">Select Material</option>
                    ${materialOptions}
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Quantity</label>
                <input type="number" class="form-control" 
                       name="materials[${materialCount}][quantity]" value="${quantity}" 
                       min="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-danger d-block w-100 remove-material" 
                        onclick="removeMaterial(this)">Remove</button>
            </div>
        </div>
    `;
    
    container.appendChild(newMaterialItem);
    materialCount++;
}

function removeMaterial(button) {
    const container = document.getElementById('materials-container');
    if (container.children.length > 1) {
        button.closest('.material-item').remove();
    } else {
        alert('At least one material is required.');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const oldMaterials = @json(old('materials', $rfq->materials->map(function($m) { return ['material_id' => $m->id, 'quantity' => $m->pivot->quantity]; })->toArray()));
    if (oldMaterials.length > 0) {
        oldMaterials.forEach(item => {
            addMaterial({ id: item.material_id }, item.quantity);
        });
    } else {
        addMaterial();
    }
});
</script>
@endpush
@endsection 