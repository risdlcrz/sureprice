@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h4 class="card-title mb-0">Add New Material</h4>
                </div>
                <div class="card-body">
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
    });
</script>
@endpush 