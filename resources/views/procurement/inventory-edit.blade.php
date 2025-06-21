@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Inventory Item</h1>
        <a href="{{ route('procurement.inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Inventory
        </a>
    </div>

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('procurement.inventory.update', $inventory) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="material_name">Material Name</label>
                                <input type="text" class="form-control" id="material_name" name="material_name" value="{{ $inventory->material->name }}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="category_name">Category</label>
                                <input type="text" class="form-control" id="category_name" name="category_name" value="{{ $inventory->material->category->name }}" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="quantity">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" value="{{ $inventory->quantity }}" step="0.01" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="unit">Unit</label>
                                <input type="text" class="form-control" id="unit" name="unit" value="{{ $inventory->unit }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="location">Location</label>
                                <input type="text" class="form-control" id="location" name="location" value="{{ $inventory->location }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="active" @if($inventory->status == 'active') selected @endif>Active</option>
                                    <option value="inactive" @if($inventory->status == 'inactive') selected @endif>Inactive</option>
                                    <option value="obsolete" @if($inventory->status == 'obsolete') selected @endif>Obsolete</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="last_restock_date">Last Restock Date</label>
                                <input type="date" class="form-control" id="last_restock_date" name="last_restock_date" value="{{ optional($inventory->last_restock_date)->format('Y-m-d') }}">
                            </div>
                        </div>

                        <hr>

                        <button type="submit" class="btn btn-primary">Update Item</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 