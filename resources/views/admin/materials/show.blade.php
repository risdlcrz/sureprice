@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h4 class="card-title mb-0">Material Details: {{ $material->name }}</h4>
                </div>
                <div class="card-body">
                    <p><strong>Code:</strong> {{ $material->code }}</p>
                    <p><strong>Category:</strong> {{ $material->category->name ?? 'N/A' }}</p>
                    <p><strong>Unit:</strong> {{ $material->unit }}</p>
                    <p><strong>Description:</strong> {{ $material->description }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Link Suppliers</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.materials.suppliers.update', $material) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="suppliers" class="form-label">Select Suppliers</label>
                            <select name="suppliers[]" id="suppliers" class="form-control" multiple>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ in_array($supplier->id, $linkedSupplierIds) ? 'selected' : '' }}>
                                        {{ $supplier->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Suppliers</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush 