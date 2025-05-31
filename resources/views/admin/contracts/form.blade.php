@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h4 class="card-title mb-0">{{ isset($contract) ? 'Edit Contract' : 'Create New Contract' }}</h4>
                </div>
                <div class="card-body">
                    <form id="contractForm" method="POST" action="{{ isset($contract) ? route('contracts.update', $contract->id) : route('contracts.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if(isset($contract))
                            @method('PUT')
                        @endif

                        <!-- Contractor Information Section -->
                        <div class="section-container" id="contractorSection">
                            <h5 class="section-title">Contractor Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contractor_name">Name</label>
                                        <input type="text" class="form-control @error('contractor_name') is-invalid @enderror" 
                                            id="contractor_name" name="contractor_name" 
                                            value="{{ old('contractor_name', $contractor ? $contractor->name : '') }}" required>
                                        @error('contractor_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contractor_company">Company Name (Optional)</label>
                                        <input type="text" class="form-control @error('contractor_company') is-invalid @enderror" 
                                            id="contractor_company" name="contractor_company" 
                                            value="{{ old('contractor_company', $contractor ? $contractor->company_name : '') }}">
                                        @error('contractor_company')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contractor_street">Street Address</label>
                                        <input type="text" class="form-control @error('contractor_street') is-invalid @enderror" 
                                            id="contractor_street" name="contractor_street" 
                                            value="{{ old('contractor_street', $contractor ? $contractor->street : '') }}" required>
                                        @error('contractor_street')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contractor_unit">Unit/Floor/Building (Optional)</label>
                                        <input type="text" class="form-control @error('contractor_unit') is-invalid @enderror" 
                                            id="contractor_unit" name="contractor_unit" 
                                            value="{{ old('contractor_unit', $contractor ? $contractor->unit : '') }}">
                                        @error('contractor_unit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contractor_barangay">Barangay</label>
                                        <input type="text" class="form-control @error('contractor_barangay') is-invalid @enderror" 
                                            id="contractor_barangay" name="contractor_barangay" 
                                            value="{{ old('contractor_barangay', $contractor ? $contractor->barangay : '') }}" required>
                                        @error('contractor_barangay')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contractor_city">City/Municipality</label>
                                        <input type="text" class="form-control @error('contractor_city') is-invalid @enderror" 
                                            id="contractor_city" name="contractor_city" 
                                            value="{{ old('contractor_city', $contractor ? $contractor->city : '') }}" required>
                                        @error('contractor_city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contractor_state">Province/State</label>
                                        <input type="text" class="form-control @error('contractor_state') is-invalid @enderror" 
                                            id="contractor_state" name="contractor_state" 
                                            value="{{ old('contractor_state', $contractor ? $contractor->state : '') }}" required>
                                        @error('contractor_state')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contractor_postal">Postal/ZIP Code</label>
                                        <input type="text" class="form-control @error('contractor_postal') is-invalid @enderror" 
                                            id="contractor_postal" name="contractor_postal" 
                                            value="{{ old('contractor_postal', $contractor ? $contractor->postal : '') }}" required>
                                        @error('contractor_postal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contractor_email">Email</label>
                                        <input type="email" class="form-control @error('contractor_email') is-invalid @enderror" 
                                            id="contractor_email" name="contractor_email" 
                                            value="{{ old('contractor_email', $contractor ? $contractor->email : '') }}" required>
                                        @error('contractor_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contractor_phone">Phone Number</label>
                                        <input type="tel" class="form-control @error('contractor_phone') is-invalid @enderror" 
                                            id="contractor_phone" name="contractor_phone" 
                                            value="{{ old('contractor_phone', $contractor ? $contractor->phone : '') }}" required>
                                        @error('contractor_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Client Information Section -->
                        <div class="section-container" id="clientSection">
                            <h5 class="section-title">Client Information</h5>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="client_search">Search Client</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="client_search" placeholder="Search by name, email, or phone...">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="searchClientBtn">
                                                    <i class="fas fa-search"></i> Search
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="clientSearchResults" class="mt-2" style="display: none;">
                                        <!-- Search results will be populated here -->
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_name">Name</label>
                                        <input type="text" class="form-control @error('client_name') is-invalid @enderror" 
                                            id="client_name" name="client_name" 
                                            value="{{ old('client_name', $client ? $client->name : '') }}" required>
                                        @error('client_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_company">Company Name (Optional)</label>
                                        <input type="text" class="form-control @error('client_company') is-invalid @enderror" 
                                            id="client_company" name="client_company" 
                                            value="{{ old('client_company', $client ? $client->company_name : '') }}">
                                        @error('client_company')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_street">Street Address</label>
                                        <input type="text" class="form-control @error('client_street') is-invalid @enderror" 
                                            id="client_street" name="client_street" 
                                            value="{{ old('client_street', $client ? $client->street : '') }}" required>
                                        @error('client_street')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_unit">Unit/Floor/Building (Optional)</label>
                                        <input type="text" class="form-control @error('client_unit') is-invalid @enderror" 
                                            id="client_unit" name="client_unit" 
                                            value="{{ old('client_unit', $client ? $client->unit : '') }}">
                                        @error('client_unit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_barangay">Barangay</label>
                                        <input type="text" class="form-control @error('client_barangay') is-invalid @enderror" 
                                            id="client_barangay" name="client_barangay" 
                                            value="{{ old('client_barangay', $client ? $client->barangay : '') }}" required>
                                        @error('client_barangay')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_city">City/Municipality</label>
                                        <input type="text" class="form-control @error('client_city') is-invalid @enderror" 
                                            id="client_city" name="client_city" 
                                            value="{{ old('client_city', $client ? $client->city : '') }}" required>
                                        @error('client_city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_state">Province/State</label>
                                        <input type="text" class="form-control @error('client_state') is-invalid @enderror" 
                                            id="client_state" name="client_state" 
                                            value="{{ old('client_state', $client ? $client->state : '') }}" required>
                                        @error('client_state')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_postal">Postal/ZIP Code</label>
                                        <input type="text" class="form-control @error('client_postal') is-invalid @enderror" 
                                            id="client_postal" name="client_postal" 
                                            value="{{ old('client_postal', $client ? $client->postal : '') }}" required>
                                        @error('client_postal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_email">Email</label>
                                        <input type="email" class="form-control @error('client_email') is-invalid @enderror" 
                                            id="client_email" name="client_email" 
                                            value="{{ old('client_email', $client ? $client->email : '') }}" required>
                                        @error('client_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_phone">Phone Number</label>
                                        <input type="tel" class="form-control @error('client_phone') is-invalid @enderror" 
                                            id="client_phone" name="client_phone" 
                                            value="{{ old('client_phone', $client ? $client->phone : '') }}" required>
                                        @error('client_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Property Information Section -->
                        <div class="section-container" id="propertySection">
                            <h5 class="section-title">Property Information</h5>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="same_as_client" name="same_as_client">
                                        <label class="form-check-label" for="same_as_client">
                                            Same as Client Address
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="property_street">Street Address</label>
                                        <input type="text" class="form-control @error('property_street') is-invalid @enderror" 
                                            id="property_street" name="property_street" 
                                            value="{{ old('property_street', $property ? $property->street : '') }}" required>
                                        @error('property_street')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="property_unit">Unit/Floor/Building (Optional)</label>
                                        <input type="text" class="form-control @error('property_unit') is-invalid @enderror" 
                                            id="property_unit" name="property_unit" 
                                            value="{{ old('property_unit', $property ? $property->unit : '') }}">
                                        @error('property_unit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="property_barangay">Barangay</label>
                                        <input type="text" class="form-control @error('property_barangay') is-invalid @enderror" 
                                            id="property_barangay" name="property_barangay" 
                                            value="{{ old('property_barangay', $property ? $property->barangay : '') }}" required>
                                        @error('property_barangay')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="property_city">City/Municipality</label>
                                        <input type="text" class="form-control @error('property_city') is-invalid @enderror" 
                                            id="property_city" name="property_city" 
                                            value="{{ old('property_city', $property ? $property->city : '') }}" required>
                                        @error('property_city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="property_state">Province/State</label>
                                        <input type="text" class="form-control @error('property_state') is-invalid @enderror" 
                                            id="property_state" name="property_state" 
                                            value="{{ old('property_state', $property ? $property->state : '') }}" required>
                                        @error('property_state')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="property_postal">Postal/ZIP Code</label>
                                        <input type="text" class="form-control @error('property_postal') is-invalid @enderror" 
                                            id="property_postal" name="property_postal" 
                                            value="{{ old('property_postal', $property ? $property->postal : '') }}" required>
                                        @error('property_postal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Scope of Work Section -->
                        <div class="section-container" id="scopeSection">
                            <h5 class="section-title">Scope of Work</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Work Categories</label>
                                        <div class="scope-categories">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="scope_of_work[]" 
                                                            value="renovation" id="scope_renovation"
                                                            {{ in_array('renovation', old('scope_of_work', $contract ? explode(', ', $contract->scope_of_work) : [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="scope_renovation">
                                                            Renovation
                                                        </label>
                                                    </div>
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="scope_of_work[]" 
                                                            value="repair" id="scope_repair"
                                                            {{ in_array('repair', old('scope_of_work', $contract ? explode(', ', $contract->scope_of_work) : [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="scope_repair">
                                                            Repair
                                                        </label>
                                                    </div>
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="scope_of_work[]" 
                                                            value="construction" id="scope_construction"
                                                            {{ in_array('construction', old('scope_of_work', $contract ? explode(', ', $contract->scope_of_work) : [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="scope_construction">
                                                            Construction
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="scope_of_work[]" 
                                                            value="plumbing" id="scope_plumbing"
                                                            {{ in_array('plumbing', old('scope_of_work', $contract ? explode(', ', $contract->scope_of_work) : [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="scope_plumbing">
                                                            Plumbing
                                                        </label>
                                                    </div>
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="scope_of_work[]" 
                                                            value="electrical" id="scope_electrical"
                                                            {{ in_array('electrical', old('scope_of_work', $contract ? explode(', ', $contract->scope_of_work) : [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="scope_electrical">
                                                            Electrical
                                                        </label>
                                                    </div>
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="scope_of_work[]" 
                                                            value="painting" id="scope_painting"
                                                            {{ in_array('painting', old('scope_of_work', $contract ? explode(', ', $contract->scope_of_work) : [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="scope_painting">
                                                            Painting
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="scope_of_work[]" 
                                                            value="flooring" id="scope_flooring"
                                                            {{ in_array('flooring', old('scope_of_work', $contract ? explode(', ', $contract->scope_of_work) : [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="scope_flooring">
                                                            Flooring
                                                        </label>
                                                    </div>
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="scope_of_work[]" 
                                                            value="roofing" id="scope_roofing"
                                                            {{ in_array('roofing', old('scope_of_work', $contract ? explode(', ', $contract->scope_of_work) : [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="scope_roofing">
                                                            Roofing
                                                        </label>
                                                    </div>
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="scope_of_work[]" 
                                                            value="other" id="scope_other"
                                                            {{ in_array('other', old('scope_of_work', $contract ? explode(', ', $contract->scope_of_work) : [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="scope_other">
                                                            Other
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="scope_description">Detailed Work Description</label>
                                        <textarea class="form-control @error('scope_description') is-invalid @enderror" 
                                            id="scope_description" name="scope_description" rows="6" required>{{ old('scope_description', $contract ? $contract->scope_description : '') }}</textarea>
                                        @error('scope_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Items and Materials Section -->
                        <div class="section-container" id="itemsSection">
                            <h5 class="section-title">Items and Materials</h5>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" id="addItemBtn">
                                        <i class="fas fa-plus"></i> Add Item
                                    </button>
                                </div>
                            </div>
                            <div id="itemsList">
                                @if(isset($items) && count($items) > 0)
                                    @foreach($items as $index => $item)
                                        <div class="item-container mb-4 border p-3 rounded">
                                            <div class="row">
                                                <div class="col-md-11">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Material</label>
                                                                <input type="text" class="form-control material-name" 
                                                                    name="items[{{ $index }}][material_name]" 
                                                                    value="{{ $item->material->name }}" required readonly>
                                                                <input type="hidden" class="material-id" 
                                                                    name="items[{{ $index }}][material_id]" 
                                                                    value="{{ $item->material_id }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Unit</label>
                                                                <input type="text" class="form-control item-unit" 
                                                                    name="items[{{ $index }}][unit]" 
                                                                    value="{{ $item->material->unit }}" required readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Quantity</label>
                                                                <input type="number" class="form-control item-quantity" 
                                                                    name="items[{{ $index }}][quantity]" 
                                                                    value="{{ $item->quantity }}" min="1" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Unit Price</label>
                                                                <input type="number" class="form-control item-price" 
                                                                    name="items[{{ $index }}][unit_price]" 
                                                                    value="{{ $item->amount }}" min="0" step="0.01" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Total</label>
                                                                <input type="text" class="form-control item-total" 
                                                                    value="{{ number_format($item->total, 2) }}" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Preferred Suppliers</label>
                                                                <div class="suppliers-container">
                                                                    @foreach($item->material->suppliers as $supplier)
                                                                        <div class="form-check">
                                                                            <input type="checkbox" class="form-check-input supplier-checkbox" 
                                                                                name="items[{{ $index }}][suppliers][]" 
                                                                                value="{{ $supplier->id }}" 
                                                                                id="supplier_{{ $index }}_{{ $supplier->id }}"
                                                                                {{ $item->supplier_id == $supplier->id ? 'checked' : '' }}>
                                                                            <label class="form-check-label" for="supplier_{{ $index }}_{{ $supplier->id }}">
                                                                                {{ $supplier->name }} - {{ $supplier->price_range ?? 'Price not available' }}
                                                                            </label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger remove-item-btn">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- Item Template -->
                        <template id="itemTemplate">
                            <div class="item-container mb-4 border p-3 rounded">
                                <div class="row">
                                    <div class="col-md-11">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <div class="form-group">
                                                    <label>Material Search</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control material-search" placeholder="Search materials...">
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-secondary search-material-btn" type="button">
                                                                <i class="fas fa-search"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="material-search-results mt-2" style="display: none;"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Material Name</label>
                                                    <input type="text" class="form-control material-name" name="items[INDEX][material_name]" required readonly>
                                                    <input type="hidden" class="material-id" name="items[INDEX][material_id]">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Unit</label>
                                                    <input type="text" class="form-control item-unit" name="items[INDEX][unit]" required readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Quantity</label>
                                                    <input type="number" class="form-control item-quantity" name="items[INDEX][quantity]" min="1" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Unit Price</label>
                                                    <input type="number" class="form-control item-price" name="items[INDEX][unit_price]" min="0" step="0.01" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Total</label>
                                                    <input type="text" class="form-control item-total" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Preferred Suppliers</label>
                                                    <div class="suppliers-container">
                                                        <!-- Suppliers will be dynamically loaded here -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger remove-item-btn">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Contract Clauses Section -->
                        <div class="section-container" id="clausesSection">
                            <h5 class="section-title">Contract Clauses</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="payment_terms">Payment Terms</label>
                                        <textarea class="form-control" id="payment_terms" name="payment_terms" rows="4" required>{{ old('payment_terms', $contract->payment_terms ?? "1. Initial deposit of 30% upon contract signing\n2. 40% upon completion of 50% of work\n3. Remaining 30% upon final inspection and completion") }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="warranty_terms">Warranty Terms</label>
                                        <textarea class="form-control" id="warranty_terms" name="warranty_terms" rows="4" required>{{ old('warranty_terms', $contract->warranty_terms ?? "1. Workmanship warranty for 1 year from completion date\n2. Materials warranty as per manufacturer specifications\n3. Warranty excludes damage from misuse or natural disasters") }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="cancellation_terms">Cancellation Terms</label>
                                        <textarea class="form-control" id="cancellation_terms" name="cancellation_terms" rows="4" required>{{ old('cancellation_terms', $contract->cancellation_terms ?? "1. Client may cancel within 3 business days for full refund\n2. Cancellation after materials ordered subject to 25% fee\n3. Contractor may terminate if client breaches payment terms") }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="additional_terms">Additional Terms and Conditions</label>
                                        <textarea class="form-control" id="additional_terms" name="additional_terms" rows="6">{{ old('additional_terms', $contract->additional_terms ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details -->
                        <div class="section-container" id="paymentSection">
                            <h5 class="section-title">Payment Details</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="total_amount">Total Contract Amount</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">₱</span>
                                            </div>
                                            <input type="number" step="0.01" min="0" class="form-control @error('total_amount') is-invalid @enderror" 
                                                id="total_amount" name="total_amount" 
                                                value="{{ old('total_amount', $contract ? $contract->total_amount : '') }}" required>
                                            @error('total_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="budget_allocation">Budget Allocation</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">₱</span>
                                            </div>
                                            <input type="number" step="0.01" min="0" class="form-control @error('budget_allocation') is-invalid @enderror" 
                                                id="budget_allocation" name="budget_allocation" 
                                                value="{{ old('budget_allocation', $contract ? $contract->budget_allocation : '') }}" required>
                                            @error('budget_allocation')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_method">Payment Method</label>
                                        <select class="form-control @error('payment_method') is-invalid @enderror" 
                                            id="payment_method" name="payment_method" required>
                                            <option value="">Select Payment Method</option>
                                            <option value="cash" {{ old('payment_method', $contract ? $contract->payment_method : '') == 'cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="check" {{ old('payment_method', $contract ? $contract->payment_method : '') == 'check' ? 'selected' : '' }}>Check</option>
                                            <option value="bank_transfer" {{ old('payment_method', $contract ? $contract->payment_method : '') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        </select>
                                        @error('payment_method')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_terms">Payment Terms</label>
                                        <textarea class="form-control @error('payment_terms') is-invalid @enderror" 
                                            id="payment_terms" name="payment_terms" rows="3" required>{{ old('payment_terms', $contract ? $contract->payment_terms : '') }}</textarea>
                                        @error('payment_terms')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Bank Transfer Details -->
                            <div id="bankDetails" class="row mt-3" style="display: none;">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bank_name">Bank Name</label>
                                        <input type="text" class="form-control @error('bank_name') is-invalid @enderror" 
                                            id="bank_name" name="bank_name" 
                                            value="{{ old('bank_name', $contract ? $contract->bank_name : '') }}">
                                        @error('bank_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bank_account_name">Account Name</label>
                                        <input type="text" class="form-control @error('bank_account_name') is-invalid @enderror" 
                                            id="bank_account_name" name="bank_account_name" 
                                            value="{{ old('bank_account_name', $contract ? $contract->bank_account_name : '') }}">
                                        @error('bank_account_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bank_account_number">Account Number</label>
                                        <input type="text" class="form-control @error('bank_account_number') is-invalid @enderror" 
                                            id="bank_account_number" name="bank_account_number" 
                                            value="{{ old('bank_account_number', $contract ? $contract->bank_account_number : '') }}">
                                        @error('bank_account_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Check Details -->
                            <div id="checkDetails" class="row mt-3" style="display: none;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="check_number">Check Number</label>
                                        <input type="text" class="form-control @error('check_number') is-invalid @enderror" 
                                            id="check_number" name="check_number" 
                                            value="{{ old('check_number', $contract ? $contract->check_number : '') }}">
                                        @error('check_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="check_date">Check Date</label>
                                        <input type="date" class="form-control @error('check_date') is-invalid @enderror" 
                                            id="check_date" name="check_date" 
                                            value="{{ old('check_date', $contract ? $contract->check_date : '') }}">
                                        @error('check_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <div class="form-group">
                                        <label for="check_image">Upload Check Image</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input @error('check_image') is-invalid @enderror" 
                                                id="check_image" name="check_image" accept="image/*">
                                            <label class="custom-file-label" for="check_image">Choose file</label>
                                        </div>
                                        @error('check_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if(isset($contract) && $contract->check_image)
                                            <div class="mt-2">
                                                <img src="{{ Storage::url($contract->check_image) }}" alt="Check Image" class="img-thumbnail" style="max-height: 200px;">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Signature Section -->
                        <div class="section-container" id="signatureSection">
                            <h5 class="section-title">Signatures</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contractor Signature</label>
                                        @if(isset($existing_contractor_signature))
                                            <div class="existing-signature mb-3">
                                                <img src="{{ $existing_contractor_signature }}" alt="Existing Contractor Signature" class="img-fluid mb-2" style="max-height: 150px;">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="keep_contractor_signature" name="keep_contractor_signature" value="1" checked>
                                                    <label class="form-check-label" for="keep_contractor_signature">
                                                        Keep existing signature
                                                    </label>
                                                </div>
                                            </div>
                                            <input type="hidden" name="existing_contractor_signature" value="{{ $existing_contractor_signature }}">
                                        @endif
                                        <div class="signature-pad-container">
                                            <canvas id="contractorSignature" class="signature-pad"></canvas>
                                            <input type="hidden" name="contractor_signature" id="contractorSignatureData">
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-secondary clear-signature" data-pad="contractor">Clear</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Client Signature</label>
                                        @if(isset($existing_client_signature))
                                            <div class="existing-signature mb-3">
                                                <img src="{{ $existing_client_signature }}" alt="Existing Client Signature" class="img-fluid mb-2" style="max-height: 150px;">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="keep_client_signature" name="keep_client_signature" value="1" checked>
                                                    <label class="form-check-label" for="keep_client_signature">
                                                        Keep existing signature
                                                    </label>
                                                </div>
                                            </div>
                                            <input type="hidden" name="existing_client_signature" value="{{ $existing_client_signature }}">
                                        @endif
                                        <div class="signature-pad-container">
                                            <canvas id="clientSignature" class="signature-pad"></canvas>
                                            <input type="hidden" name="client_signature" id="clientSignatureData">
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-secondary clear-signature" data-pad="client">Clear</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contractor_name_signed">Contractor Name (Print)</label>
                                        <input type="text" class="form-control" id="contractor_name_signed" name="contractor_name_signed" 
                                            value="{{ old('contractor_name_signed', $contract->contractor_name ?? $contractor->name ?? '') }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="contractor_date_signed">Date Signed</label>
                                        <input type="date" class="form-control" id="contractor_date_signed" name="contractor_date_signed" 
                                            value="{{ old('contractor_date_signed', $contract->contractor_date_signed ?? date('Y-m-d')) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_name_signed">Client Name (Print)</label>
                                        <input type="text" class="form-control" id="client_name_signed" name="client_name_signed" 
                                            value="{{ old('client_name_signed', $contract->client_name ?? $client->name ?? '') }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="client_date_signed">Date Signed</label>
                                        <input type="date" class="form-control" id="client_date_signed" name="client_date_signed" 
                                            value="{{ old('client_date_signed', $contract->client_date_signed ?? date('Y-m-d')) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Save Contract</button>
                            <a href="{{ route('contracts.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
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
    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #2c3e50;
    }
    .card {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .card-header {
        border-bottom: 1px solid #edf2f7;
    }
    @media (max-width: 768px) {
        .section-container {
            padding: 1rem;
        }
        .card-body {
            padding: 1rem;
        }
    }
    /* Rich text editor styles */
    .tox-tinymce {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .material-search-results {
        position: absolute;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        z-index: 1000;
    }
    .material-result {
        transition: background-color 0.2s;
    }
    .material-result:hover {
        background-color: #f8f9fa;
    }
    .item-container {
        position: relative;
    }
    .suppliers-container {
        max-height: 150px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 0.5rem;
    }
    .form-check {
        margin-bottom: 0.5rem;
    }
    .form-check:last-child {
        margin-bottom: 0;
    }
    .remove-item-btn {
        margin-top: 2rem;
    }
    .signature-pad-container {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        background-color: #fff;
    }
    .signature-pad {
        width: 100%;
        height: 200px;
        border-radius: 4px;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form validation
    const form = document.getElementById('contractForm');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    // Client search functionality
    const clientSearchInput = document.getElementById('client_search');
    const clientSearchResults = document.getElementById('clientSearchResults');
    const searchClientBtn = document.getElementById('searchClientBtn');
    let searchTimeout;

    function performClientSearch() {
        const searchTerm = clientSearchInput.value.trim();
        if (searchTerm.length < 2) {
            clientSearchResults.style.display = 'none';
            return;
        }

        fetch(`{{ route('clients.search') }}?query=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    const resultsHtml = data.map(client => `
                        <div class="client-result p-2 border-bottom" style="cursor: pointer;" 
                             onclick="selectClient(${JSON.stringify(client).replace(/"/g, '&quot;')})">
                            <strong>${client.name}</strong><br>
                            <small>${client.email} | ${client.phone}</small>
                        </div>
                    `).join('');
                    
                    clientSearchResults.innerHTML = `
                        <div class="card">
                            <div class="card-body p-0">
                                ${resultsHtml}
                            </div>
                        </div>
                    `;
                    clientSearchResults.style.display = 'block';
                } else {
                    clientSearchResults.innerHTML = '<div class="alert alert-info">No clients found</div>';
                    clientSearchResults.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error searching clients:', error);
                clientSearchResults.innerHTML = '<div class="alert alert-danger">Error searching clients</div>';
                clientSearchResults.style.display = 'block';
            });
    }

    function selectClient(client) {
        document.getElementById('client_id').value = client.id;
        document.getElementById('client_name').value = client.name;
        document.getElementById('client_email').value = client.email;
        document.getElementById('client_phone').value = client.phone;
        document.getElementById('client_address').value = client.address;
        document.getElementById('client_type').value = client.type || 'individual';
        
        clientSearchResults.style.display = 'none';
        clientSearchInput.value = '';
    }

    clientSearchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performClientSearch, 300);
    });

    searchClientBtn.addEventListener('click', performClientSearch);

    // Close search results when clicking outside
    document.addEventListener('click', (e) => {
        if (!clientSearchResults.contains(e.target) && e.target !== clientSearchInput && e.target !== searchClientBtn) {
            clientSearchResults.style.display = 'none';
        }
    });

    // Initialize TinyMCE
    tinymce.init({
        selector: '#work_description',
        height: 300,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'charmap', 'preview',
            'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'table', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist | ' +
            'removeformat',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }'
    });

    // Date validation
    const startDateInput = document.getElementById('start_date');
    const completionDateInput = document.getElementById('completion_date');

    startDateInput.addEventListener('change', validateDates);
    completionDateInput.addEventListener('change', validateDates);

    function validateDates() {
        const startDate = new Date(startDateInput.value);
        const completionDate = new Date(completionDateInput.value);
        
        if (completionDate < startDate) {
            completionDateInput.setCustomValidity('Completion date must be after start date');
        } else {
            completionDateInput.setCustomValidity('');
        }
    }

    // Items and Materials Management
    const itemsList = document.getElementById('itemsList');
    const itemTemplate = document.getElementById('itemTemplate');
    const addItemBtn = document.getElementById('addItemBtn');
    let itemCount = {{ isset($items) ? count($items) : 0 }};

    function addItem() {
        const newItem = document.importNode(itemTemplate.content, true);
        
        // Replace INDEX placeholder with actual index
        newItem.querySelectorAll('[name*="INDEX"]').forEach(element => {
            element.name = element.name.replace('INDEX', itemCount);
        });

        // Add event listeners for the new item
        setupItemEventListeners(newItem.querySelector('.item-container'));
        
        itemsList.appendChild(newItem);
        itemCount++;
    }

    function setupItemEventListeners(container) {
        const searchInput = container.querySelector('.material-search');
        const searchBtn = container.querySelector('.search-material-btn');
        const searchResults = container.querySelector('.material-search-results');
        const quantityInput = container.querySelector('.item-quantity');
        const priceInput = container.querySelector('.item-price');
        const totalInput = container.querySelector('.item-total');
        const removeBtn = container.querySelector('.remove-item-btn');
        let searchTimeout;

        // Material search
        function performMaterialSearch() {
            const searchTerm = searchInput.value.trim();
            if (searchTerm.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            // Show loading state
            searchResults.innerHTML = '<div class="p-2">Searching...</div>';
            searchResults.style.display = 'block';

            fetch('/api/materials/search?query=' + encodeURIComponent(searchTerm))
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        const resultsHtml = data.map(material => `
                            <div class="material-result p-2 border-bottom" style="cursor: pointer;" 
                                data-material='${JSON.stringify(material)}'>
                                <strong>${material.name}</strong><br>
                                <small>${material.description || ''} - ${material.unit}</small>
                            </div>
                        `).join('');
                        
                        searchResults.innerHTML = resultsHtml;
                        
                        // Add click handlers for results
                        searchResults.querySelectorAll('.material-result').forEach(result => {
                            result.addEventListener('click', () => selectMaterial(result));
                        });
                    } else {
                        searchResults.innerHTML = '<div class="p-2">No materials found</div>';
                    }
                })
                .catch(error => {
                    console.error('Error searching materials:', error);
                    searchResults.innerHTML = '<div class="p-2 text-danger">Error searching materials</div>';
                });
        }

        function selectMaterial(resultElement) {
            const material = JSON.parse(resultElement.dataset.material);
            
            container.querySelector('.material-id').value = material.id;
            container.querySelector('.material-name').value = material.name;
            container.querySelector('.item-unit').value = material.unit;
            if (material.price) {
                container.querySelector('.item-price').value = material.price;
            }
            
            searchResults.style.display = 'none';
            searchInput.value = '';

            // Load suppliers for this material
            loadSuppliers(material.id);
            
            // Calculate total
            calculateTotal();
        }

        function loadSuppliers(materialId) {
            const suppliersContainer = container.querySelector('.suppliers-container');
            
            fetch(`/api/materials/${materialId}/suppliers`)
                .then(response => response.json())
                .then(suppliers => {
                    if (suppliers.length > 0) {
                        const suppliersHtml = suppliers.map(supplier => `
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input supplier-checkbox" 
                                    name="items[${itemCount}][suppliers][]" 
                                    value="${supplier.id}" 
                                    id="supplier_${itemCount}_${supplier.id}">
                                <label class="form-check-label" for="supplier_${itemCount}_${supplier.id}">
                                    ${supplier.name} - ${supplier.price_range || 'Price not available'}
                                </label>
                            </div>
                        `).join('');
                        suppliersContainer.innerHTML = suppliersHtml;
                    } else {
                        suppliersContainer.innerHTML = '<p class="text-muted">No suppliers available for this material</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading suppliers:', error);
                    suppliersContainer.innerHTML = '<p class="text-danger">Error loading suppliers</p>';
                });
        }

        function calculateTotal() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            totalInput.value = (quantity * price).toFixed(2);
            updateContractTotal();
        }

        // Event listeners
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performMaterialSearch, 300);
        });

        searchBtn.addEventListener('click', performMaterialSearch);

        quantityInput.addEventListener('input', calculateTotal);
        priceInput.addEventListener('input', calculateTotal);

        removeBtn.addEventListener('click', () => {
            container.remove();
            updateContractTotal();
        });

        // Close search results when clicking outside
        document.addEventListener('click', (e) => {
            if (!searchResults.contains(e.target) && e.target !== searchInput && e.target !== searchBtn) {
                searchResults.style.display = 'none';
            }
        });
    }

    function updateContractTotal() {
        let total = 0;
        document.querySelectorAll('.item-total').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('total_amount').value = total.toFixed(2);
    }

    // Add event listener to the Add Item button
    addItemBtn.addEventListener('click', addItem);

    // Add initial item if none exists
    if (itemCount === 0) {
        addItem();
    }

    // Setup event listeners for existing items
    document.querySelectorAll('.item-container').forEach(container => {
        setupItemEventListeners(container);
    });

    // Initialize TinyMCE for contract clauses
    ['payment_terms', 'warranty_terms', 'cancellation_terms', 'additional_terms'].forEach(id => {
        tinymce.init({
            selector: `#${id}`,
            height: 200,
            menubar: false,
            plugins: [
                'lists', 'link', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic | ' +
                    'bullist numlist | removeformat',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }'
        });
    });

    // Initialize signature pads
    const contractorPad = new SignaturePad(document.getElementById('contractorSignature'), {
        backgroundColor: 'rgb(255, 255, 255)',
        penColor: 'rgb(0, 0, 0)'
    });

    const clientPad = new SignaturePad(document.getElementById('clientSignature'), {
        backgroundColor: 'rgb(255, 255, 255)',
        penColor: 'rgb(0, 0, 0)'
    });

    // Handle existing signatures
    document.getElementById('keep_contractor_signature')?.addEventListener('change', function() {
        const container = document.querySelector('#contractorSignature').closest('.signature-pad-container');
        container.style.display = this.checked ? 'none' : 'block';
    });

    document.getElementById('keep_client_signature')?.addEventListener('change', function() {
        const container = document.querySelector('#clientSignature').closest('.signature-pad-container');
        container.style.display = this.checked ? 'none' : 'block';
    });

    // Initially hide signature pads if keeping existing signatures
    if (document.getElementById('keep_contractor_signature')?.checked) {
        document.querySelector('#contractorSignature').closest('.signature-pad-container').style.display = 'none';
    }
    if (document.getElementById('keep_client_signature')?.checked) {
        document.querySelector('#clientSignature').closest('.signature-pad-container').style.display = 'none';
    }

    // Clear signature buttons
    document.querySelectorAll('.clear-signature').forEach(button => {
        button.addEventListener('click', () => {
            const padType = button.dataset.pad;
            if (padType === 'contractor') {
                contractorPad.clear();
                document.getElementById('contractorSignatureData').value = '';
            } else {
                clientPad.clear();
                document.getElementById('clientSignatureData').value = '';
            }
        });
    });

    // Save signatures on form submit
    document.getElementById('contractForm').addEventListener('submit', function(e) {
        if (!document.getElementById('keep_contractor_signature')?.checked && !contractorPad.isEmpty()) {
            document.getElementById('contractorSignatureData').value = contractorPad.toDataURL();
        }
        if (!document.getElementById('keep_client_signature')?.checked && !clientPad.isEmpty()) {
            document.getElementById('clientSignatureData').value = clientPad.toDataURL();
        }
    });

    // Resize signature pads on window resize
    function resizeSignaturePads() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        ['contractorSignature', 'clientSignature'].forEach(id => {
            const canvas = document.getElementById(id);
            const container = canvas.parentElement;
            
            canvas.width = container.offsetWidth * ratio;
            canvas.height = container.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            canvas.style.width = "100%";
            canvas.style.height = "200px";
        });
    }

    window.addEventListener('resize', resizeSignaturePads);
    resizeSignaturePads();

    // Add custom validation for phone numbers
    function validatePhone(input) {
        const phoneNumber = input.value.replace(/[^0-9]/g, '');
        return phoneNumber.length >= 10;
    }

    // Add custom validation for email addresses
    function validateEmail(input) {
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return emailRegex.test(input.value);
    }

    // Update ZIP/postal code validation
    function validateZip(input) {
        const zipRegex = /^[0-9]{4,10}(-[0-9]{4})?$/;
        return zipRegex.test(input.value);
    }

    document.getElementById('property_zip').addEventListener('input', function() {
        if (!validateZip(this)) {
            this.setCustomValidity('Please enter a valid postal code (minimum 4 digits)');
        } else {
            this.setCustomValidity('');
        }
    });

    // Add event listeners for real-time validation
    document.querySelectorAll('input[type="tel"]').forEach(input => {
        input.addEventListener('input', function() {
            if (!validatePhone(this)) {
                this.setCustomValidity('Please enter a valid phone number (minimum 10 digits)');
            } else {
                this.setCustomValidity('');
            }
        });
    });

    document.querySelectorAll('input[type="email"]').forEach(input => {
        input.addEventListener('input', function() {
            if (!validateEmail(this)) {
                this.setCustomValidity('Please enter a valid email address');
            } else {
                this.setCustomValidity('');
            }
        });
    });

    // Format phone numbers as they are typed
    document.querySelectorAll('input[type="tel"]').forEach(input => {
        input.addEventListener('input', function() {
            let cleaned = this.value.replace(/\D/g, '');
            if (cleaned.length >= 10) {
                cleaned = cleaned.match(/^(\d{3})(\d{3})(\d{4})$/);
                if (cleaned) {
                    this.value = '(' + cleaned[1] + ') ' + cleaned[2] + '-' + cleaned[3];
                }
            }
        });
    });

    // Handle payment method changes
    const paymentMethod = document.getElementById('payment_method');
    const bankDetails = document.getElementById('bankDetails');
    const checkDetails = document.getElementById('checkDetails');

    function togglePaymentDetails() {
        if (paymentMethod.value === 'bank_transfer') {
            bankDetails.style.display = 'flex';
            checkDetails.style.display = 'none';
            document.querySelectorAll('#bankDetails input').forEach(input => {
                input.required = true;
            });
            document.querySelectorAll('#checkDetails input').forEach(input => {
                input.required = false;
            });
        } else if (paymentMethod.value === 'check') {
            bankDetails.style.display = 'none';
            checkDetails.style.display = 'flex';
            document.querySelectorAll('#bankDetails input').forEach(input => {
                input.required = false;
            });
            document.querySelectorAll('#checkDetails input:not([type="file"])').forEach(input => {
                input.required = true;
            });
        } else {
            bankDetails.style.display = 'none';
            checkDetails.style.display = 'none';
            document.querySelectorAll('#bankDetails input, #checkDetails input').forEach(input => {
                input.required = false;
            });
        }
    }
    
    paymentMethod.addEventListener('change', togglePaymentDetails);
    togglePaymentDetails(); // Run on page load

    // Handle file input change
    document.getElementById('check_image').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Choose file';
        e.target.nextElementSibling.textContent = fileName;
    });

    // Handle "Same as Client Address" checkbox
    document.getElementById('same_as_client').addEventListener('change', function() {
        if (this.checked) {
            // Copy client address to property address
            document.getElementById('property_street').value = document.getElementById('client_street').value;
            document.getElementById('property_unit').value = document.getElementById('client_unit').value;
            document.getElementById('property_barangay').value = document.getElementById('client_barangay').value;
            document.getElementById('property_city').value = document.getElementById('client_city').value;
            document.getElementById('property_state').value = document.getElementById('client_state').value;
            document.getElementById('property_postal').value = document.getElementById('client_postal').value;

            // Disable property address fields
            ['street', 'unit', 'barangay', 'city', 'state', 'postal'].forEach(field => {
                document.getElementById(`property_${field}`).readOnly = true;
            });
        } else {
            // Enable property address fields
            ['street', 'unit', 'barangay', 'city', 'state', 'postal'].forEach(field => {
                document.getElementById(`property_${field}`).readOnly = false;
            });
        }
    });

    // Update property address when client address changes if checkbox is checked
    ['street', 'unit', 'barangay', 'city', 'state', 'postal'].forEach(field => {
        document.getElementById(`client_${field}`).addEventListener('input', function() {
            if (document.getElementById('same_as_client').checked) {
                document.getElementById(`property_${field}`).value = this.value;
            }
        });
    });
});
</script>
@endpush
@endsection 