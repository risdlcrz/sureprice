@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add New Inventory Item</h3>
                    <div class="card-tools">
                        <a href="{{ route('inventory.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to Inventory
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('inventory.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="material_id">Material</label>
                                    <select name="material_id" id="material_id" class="form-control @error('material_id') is-invalid @enderror" required>
                                        <option value="">Select Material</option>
                                        @foreach($materials as $material)
                                            <option value="{{ $material->id }}" {{ old('material_id') == $material->id ? 'selected' : '' }}>
                                                {{ $material->name }} ({{ $material->category->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('material_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quantity">Quantity</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" 
                                           value="{{ old('quantity') }}" step="0.01" required>
                                    @error('quantity')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="unit">Unit</label>
                                    <input type="text" name="unit" id="unit" class="form-control @error('unit') is-invalid @enderror" 
                                           value="{{ old('unit') }}" required>
                                    @error('unit')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location">Location</label>
                                    <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror" 
                                           value="{{ old('location') }}">
                                    @error('location')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="batch_number">Batch Number</label>
                                    <input type="text" name="batch_number" id="batch_number" class="form-control @error('batch_number') is-invalid @enderror" 
                                           value="{{ old('batch_number') }}">
                                    @error('batch_number')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expiry_date">Expiry Date</label>
                                    <input type="date" name="expiry_date" id="expiry_date" class="form-control @error('expiry_date') is-invalid @enderror" 
                                           value="{{ old('expiry_date') }}">
                                    @error('expiry_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="minimum_threshold">Minimum Threshold</label>
                                    <input type="number" name="minimum_threshold" id="minimum_threshold" class="form-control @error('minimum_threshold') is-invalid @enderror" 
                                           value="{{ old('minimum_threshold') }}" step="0.01" required>
                                    @error('minimum_threshold')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="discontinued" {{ old('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Inventory Item
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
    // Auto-fill unit when material is selected
    const materialSelect = document.getElementById('material_id');
    const unitInput = document.getElementById('unit');

    materialSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const materialName = selectedOption.text;
        const unit = materialName.match(/\((.*?)\)/);
        if (unit) {
            unitInput.value = unit[1];
        }
    });
});
</script>
@endpush
@endsection 