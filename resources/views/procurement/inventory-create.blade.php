@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Add New Inventory Item</h1>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="{{ route('procurement.inventory.store') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="material_name" class="form-label">Material Name</label>
                                <input type="text" class="form-control" id="material_name" name="material_name" placeholder="e.g., Plywood/MDF" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="category_name" class="form-label">Category</label>
                                <input type="text" class="form-control" id="category_name" name="category_name" placeholder="e.g., Cabinetry" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 mb-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" placeholder="0" step="0.01" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="unit" class="form-label">Unit</label>
                                <input type="text" class="form-control" id="unit" name="unit" placeholder="e.g., sqm, pcs, kg" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" placeholder="e.g., Warehouse A">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" name="status" class="form-select">
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="obsolete">Obsolete</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_restock_date" class="form-label">Last Restock Date</label>
                                <input type="date" class="form-control" id="last_restock_date" name="last_restock_date">
                            </div>
                        </div>
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill">Add Item</button>
                            <a href="{{ route('procurement.inventory.index') }}" class="btn btn-outline-secondary rounded-pill">Back to Inventory</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
    @vite(['resources/css/procurement/inventory-create.css'])
@endpush
@endsection 