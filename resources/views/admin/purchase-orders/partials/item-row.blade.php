{{-- One purchase order item row. Params: $index (int or '__INDEX__' for the JS template), $row (array), $materials --}}
<div class="item-row mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Material</label>
            <select class="form-select material-select" name="items[{{ $index }}][material_id]" required>
                <option value="">Select Material</option>
                @foreach($materials as $material)
                    <option value="{{ $material->id }}" data-unit="{{ $material->unit }}" @selected(($row['material_id'] ?? null) == $material->id)>{{ $material->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Quantity</label>
            <input type="number" class="form-control quantity" name="items[{{ $index }}][quantity]" value="{{ $row['quantity'] ?? '' }}" min="0.01" step="0.01" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Unit Price</label>
            <input type="number" class="form-control unit-price" name="items[{{ $index }}][unit_price]" value="{{ $row['unit_price'] ?? '' }}" min="0.01" step="0.01" required>
        </div>
        <div class="col-md-1">
            <label class="form-label">Unit</label>
            <input type="text" class="form-control unit" name="items[{{ $index }}][unit]" value="{{ $row['unit'] ?? '' }}" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Specifications</label>
            <input type="text" class="form-control" name="items[{{ $index }}][specifications]" value="{{ $row['specifications'] ?? '' }}">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger w-100 remove-item" title="Remove item"><i class="fas fa-trash"></i></button>
        </div>
    </div>
</div>
