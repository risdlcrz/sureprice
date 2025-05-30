@extends('layouts.app')

@section('content')
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">SurePrice</a>
    </div>
</nav>

<div class="sidebar">
    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('contracts.index') }}">
                    <i class="bi bi-file-text me-2"></i>
                    Contracts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('clients.index') }}">
                    <i class="bi bi-people me-2"></i>
                    Clients
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('materials.index') }}">
                    <i class="bi bi-box-seam me-2"></i>
                    Materials
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('suppliers.index') }}">
                    <i class="bi bi-truck me-2"></i>
                    Suppliers
                </a>
            </li>
        </ul>
    </div>
</div>

<main>
    <div class="container mt-4 mb-5">
        <h1 class="text-center mb-4">{{ isset($contract) ? 'Edit' : 'Create' }} Contract Agreement</h1>
        
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        
        <form method="POST" action="{{ isset($contract) ? route('contracts.update', $contract->id) : route('contracts.store') }}" 
            enctype="multipart/form-data" id="contractForm">
            @csrf
            @if(isset($contract))
                @method('PUT')
            @endif

            <!-- Contractor Information -->
            <div class="form-section">
                <h2>Contractor Information</h2>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="contractor_name" class="form-label">Contractor Name</label>
                        <input type="text" class="form-control" id="contractor_name" name="contractor_name" 
                            value="{{ old('contractor_name', isset($contract) ? $contract->contractor->name : auth()->user()->name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="contractor_company" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="contractor_company" name="contractor_company" 
                            value="{{ old('contractor_company', isset($contract) ? $contract->contractor->company_name : auth()->user()->company_name) }}" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="contractor_street" class="form-label">Street Address</label>
                        <input type="text" class="form-control" id="contractor_street" name="contractor_street" 
                            value="{{ old('contractor_street', isset($contract) ? $contract->contractor->street : '') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="contractor_city" class="form-label">City</label>
                        <input type="text" class="form-control" id="contractor_city" name="contractor_city" 
                            value="{{ old('contractor_city', isset($contract) ? $contract->contractor->city : '') }}" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="contractor_state" class="form-label">State/Province</label>
                        <input type="text" class="form-control" id="contractor_state" name="contractor_state" 
                            value="{{ old('contractor_state', isset($contract) ? $contract->contractor->state : '') }}" required>
                    </div>
                    <div class="col-md-1 mb-3">
                        <label for="contractor_postal" class="form-label">Postal</label>
                        <input type="text" class="form-control" id="contractor_postal" name="contractor_postal" 
                            value="{{ old('contractor_postal', isset($contract) ? $contract->contractor->postal : '') }}" 
                            pattern="[0-9\-]*" title="Only numbers and hyphens allowed" maxlength="10" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="contractor_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="contractor_email" name="contractor_email" 
                            value="{{ old('contractor_email', isset($contract) ? $contract->contractor->email : auth()->user()->email) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="contractor_phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="contractor_phone" name="contractor_phone" 
                            value="{{ old('contractor_phone', isset($contract) ? $contract->contractor->phone : '') }}" 
                            pattern="[0-9()\- +]*" title="Only numbers, parentheses, hyphens, and plus signs are allowed" required>
                    </div>
                </div>
            </div>

            <!-- Client Information -->
            <div class="form-section">
                <h2>Client Information</h2>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" id="client_search" placeholder="Search client by name or email">
                            <button class="btn btn-outline-secondary" type="button" id="client_search_btn">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                        <div class="search-results" id="client_search_results"></div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="company_name" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="company_name" name="company_name" 
                            value="{{ old('company_name', isset($contract) ? $contract->client->company_name : '') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="contact_person" class="form-label">Contact Person</label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person" 
                            value="{{ old('contact_person', isset($contract) ? $contract->client->name : '') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="client_street" class="form-label">Street Address</label>
                        <input type="text" class="form-control" id="client_street" name="client_street" 
                            value="{{ old('client_street', isset($contract) ? $contract->client->street : '') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="client_city" class="form-label">City</label>
                        <input type="text" class="form-control" id="client_city" name="client_city" 
                            value="{{ old('client_city', isset($contract) ? $contract->client->city : '') }}" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="client_state" class="form-label">State/Province</label>
                        <input type="text" class="form-control" id="client_state" name="client_state" 
                            value="{{ old('client_state', isset($contract) ? $contract->client->state : '') }}" required>
                    </div>
                    <div class="col-md-1 mb-3">
                        <label for="client_postal" class="form-label">Postal</label>
                        <input type="text" class="form-control" id="client_postal" name="client_postal" 
                            value="{{ old('client_postal', isset($contract) ? $contract->client->postal : '') }}" 
                            pattern="[0-9\-]*" title="Only numbers and hyphens allowed" maxlength="10" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="client_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="client_email" name="client_email" 
                            value="{{ old('client_email', isset($contract) ? $contract->client->email : '') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="client_phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="client_phone" name="client_phone" 
                            value="{{ old('client_phone', isset($contract) ? $contract->client->phone : '') }}" 
                            pattern="[0-9()\- +]*" title="Only numbers, parentheses, hyphens, and plus signs are allowed" required>
                    </div>
                </div>
            </div>

            <!-- Property Information -->
            <div class="form-section">
                <h2>Property Information</h2>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="property_street" class="form-label">Street Address</label>
                        <input type="text" class="form-control" id="property_street" name="property_street" 
                            value="{{ old('property_street', isset($contract) ? $contract->property->street : '') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="property_city" class="form-label">City</label>
                        <input type="text" class="form-control" id="property_city" name="property_city" 
                            value="{{ old('property_city', isset($contract) ? $contract->property->city : '') }}" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="property_state" class="form-label">State/Province</label>
                        <input type="text" class="form-control" id="property_state" name="property_state" 
                            value="{{ old('property_state', isset($contract) ? $contract->property->state ?? '' : '') }}" required>
                    </div>
                    <div class="col-md-1 mb-3">
                        <label for="property_postal" class="form-label">Postal</label>
                        <input type="text" class="form-control" id="property_postal" name="property_postal" 
                            value="{{ old('property_postal', isset($contract) ? $contract->property->postal ?? '' : '') }}" 
                            pattern="[0-9\-]*" title="Only numbers and hyphens allowed" maxlength="10" required>
                    </div>
                </div>
            </div>

            <!-- Scope of Work -->
            <div class="form-section">
                <h2>Scope of Work</h2>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Select Scope of Work:</label>
                        @php
                            $scope_works = [];
                            if (isset($contract) && !empty($contract->scope_of_work)) {
                                $scope_works = explode(', ', $contract->scope_of_work);
                            }
                        @endphp
                        <div class="form-check scope-work-option">
                            <input class="form-check-input scope-work" type="checkbox" name="scope_of_work[]" id="scope_design" value="Design Services" 
                                {{ in_array('Design Services', $scope_works) ? 'checked' : '' }}>
                            <label class="form-check-label" for="scope_design">Design Services</label>
                        </div>
                        <div class="form-check scope-work-option">
                            <input class="form-check-input scope-work" type="checkbox" name="scope_of_work[]" id="scope_construction" value="Construction" 
                                {{ in_array('Construction', $scope_works) ? 'checked' : '' }}>
                            <label class="form-check-label" for="scope_construction">Construction</label>
                        </div>
                        <div class="form-check scope-work-option">
                            <input class="form-check-input scope-work" type="checkbox" name="scope_of_work[]" id="scope_renovation" value="Renovation" 
                                {{ in_array('Renovation', $scope_works) ? 'checked' : '' }}>
                            <label class="form-check-label" for="scope_renovation">Renovation</label>
                        </div>
                        <div class="form-check scope-work-option">
                            <input class="form-check-input scope-work" type="checkbox" name="scope_of_work[]" id="scope_maintenance" value="Maintenance" 
                                {{ in_array('Maintenance', $scope_works) ? 'checked' : '' }}>
                            <label class="form-check-label" for="scope_maintenance">Maintenance</label>
                        </div>
                        <div class="form-check scope-work-option">
                            <input class="form-check-input scope-work" type="checkbox" name="scope_of_work[]" id="scope_other" value="Other" 
                                {{ (count(array_diff($scope_works, ['Design Services', 'Construction', 'Renovation', 'Maintenance'])) > 0) ? 'checked' : '' }}>
                            <label class="form-check-label" for="scope_other">Other</label>
                            <input type="text" class="form-control other-work-input mt-2" id="other_work_text" name="other_work_text" 
                                placeholder="Specify other work" value="{{ old('other_work_text', isset($contract) ? implode(', ', array_diff($scope_works, ['Design Services', 'Construction', 'Renovation', 'Maintenance'])) : '') }}"
                                style="display: {{ (count(array_diff($scope_works, ['Design Services', 'Construction', 'Renovation', 'Maintenance'])) > 0) ? 'block' : 'none' }}">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="scope_description" class="form-label">Scope Description</label>
                        <textarea class="form-control" id="scope_description" name="scope_description" rows="4">{{ old('scope_description', isset($contract) ? $contract->scope_description : '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Agreement, Service, and Project Period Sections -->
            <div class="contract-section">
                <h4>AGREEMENT</h4>
                <p id="agreementClausePreview" class="preview-text"></p>
            </div>

            <div class="contract-section">
                <h4>SERVICE</h4>
                <p id="serviceClausePreview" class="preview-text"></p>
            </div>

            <div class="contract-section">
                <h4>PROJECT PERIOD</h4>
                <p id="projectPeriodPreview" class="preview-text"></p>
                <div class="row">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                            value="{{ old('start_date', isset($contract) ? $contract->start_date : '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                            value="{{ old('end_date', isset($contract) ? $contract->end_date : '') }}" required>
                    </div>
                </div>
            </div>

            <!-- Amount -->
            <div class="form-section">
                <h2>Amount</h2>
                <div class="row">
                    <div class="col-md-12">
                        <div id="item_container">
                            <div class="row mb-2">
                                <div class="col-md-5">
                                    <strong>Material</strong>
                                </div>
                                <div class="col-md-2">
                                    <strong>Qty</strong>
                                </div>
                                <div class="col-md-2">
                                    <strong>Amount</strong>
                                </div>
                                <div class="col-md-2">
                                    <strong>Total</strong>
                                </div>
                                <div class="col-md-1"></div>
                            </div>
                            @if(isset($contract) && !empty($contract->items))
                                @foreach($contract->items as $item)
                                    <div class="row item-row mb-2">
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <input type="text" class="form-control material-search" 
                                                    value="{{ $item->material->name ?? '' }}" placeholder="Search material">
                                                <input type="hidden" name="item_material_id[]" value="{{ $item->material_id }}">
                                            </div>
                                            <div class="material-search-results"></div>
                                            <div class="supplier-section mt-2" style="{{ $item->material->has_preferred_suppliers ? '' : 'display: none;' }}">
                                                <div class="form-check">
                                                    <input class="form-check-input has-preferred-suppliers" type="checkbox" 
                                                        {{ $item->material->has_preferred_suppliers ? 'checked' : '' }} disabled>
                                                    <label class="form-check-label">Has preferred suppliers</label>
                                                </div>
                                                <select class="form-select supplier-select mt-2" name="item_supplier_id[]" 
                                                    style="{{ $item->material->has_preferred_suppliers ? '' : 'display: none;' }}">
                                                    <option value="">Select Supplier</option>
                                                    @if($item->material->has_preferred_suppliers)
                                                        @foreach($item->material->suppliers->where('pivot.is_preferred', true) as $supplier)
                                                            <option value="{{ $supplier->id }}" 
                                                                data-price="{{ $supplier->pivot->price }}"
                                                                {{ $item->supplier_id == $supplier->id ? 'selected' : '' }}>
                                                                {{ $supplier->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" class="form-control quantity" name="item_quantity[]" 
                                                value="{{ $item->quantity }}" min="0" step="0.01" required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" class="form-control amount" name="item_amount[]" 
                                                value="{{ $item->amount }}" min="0" step="0.01" required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control total" 
                                                value="{{ number_format($item->total, 2) }}" readonly>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger btn-sm remove-item">×</button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" class="btn btn-secondary mt-2" id="add_item">Add Item</button>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-3 offset-md-9">
                        <div class="input-group mb-3">
                            <span class="input-group-text">Total:</span>
                            <input type="text" class="form-control" id="total_amount" name="total_amount" 
                                value="{{ old('total_amount', isset($contract) ? number_format($contract->total_amount ?? 0, 2) : '0.00') }}" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signature -->
            <div class="form-section">
                <h2>Signatures</h2>
                <div class="row">
                    <div class="col-md-6">
                        <h4>Contractor Signature</h4>
                        <div class="signature-pad-container">
                            <canvas id="contractor_signature" class="signature-pad" width="400" height="200"></canvas>
                            <input type="hidden" name="contractor_signature" id="contractor_signature_data">
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="clearSignature('contractor')">Clear</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4>Client Signature</h4>
                        <div class="signature-pad-container">
                            <canvas id="client_signature" class="signature-pad" width="400" height="200"></canvas>
                            <input type="hidden" name="client_signature" id="client_signature_data">
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="clearSignature('client')">Clear</button>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary btn-lg">{{ isset($contract) ? 'Update' : 'Create' }} Contract</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<style>
    body {
        min-height: 100vh;
        background-color: #f8f9fa;
    }
    
    .sidebar {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        z-index: 100;
        padding: 48px 0 0;
        box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        background-color: #343a40;
        width: 250px;
    }

    .sidebar .nav-link {
        font-weight: 500;
        color: rgba(255, 255, 255, 0.75);
        padding: 0.75rem 1rem;
        transition: all 0.3s;
    }

    .sidebar .nav-link:hover {
        color: #fff;
        background-color: rgba(255, 255, 255, 0.1);
    }

    .sidebar .nav-link.active {
        color: #fff;
        background-color: rgba(255, 255, 255, 0.2);
    }

    .sidebar .nav-link i {
        margin-right: 0.5rem;
    }

    .sidebar-sticky {
        position: relative;
        top: 0;
        height: calc(100vh - 48px);
        padding-top: .5rem;
        overflow-x: hidden;
        overflow-y: auto;
    }

    .navbar {
        box-shadow: 0 2px 4px rgba(0,0,0,.1);
        background-color: #fff !important;
    }

    .navbar-brand {
        padding-left: 1rem;
        font-size: 1.2rem;
        font-weight: 600;
    }

    main {
        margin-left: 250px;
        padding: 20px;
    }

    .container {
        max-width: 1500px;
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .form-section {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    .search-results {
        position: absolute;
        z-index: 1000;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        background: white;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        display: none;
        margin-top: 2px;
    }
    .search-result-item {
        padding: 8px 12px;
        cursor: pointer;
    }
    .search-result-item:hover {
        background-color: #f8f9fa;
    }
    .scope-work-option {
        margin-bottom: 5px;
    }
    .other-work-input {
        margin-top: 5px;
        display: none;
    }
    .contract-section {
        margin-bottom: 30px;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 4px;
    }
    .contract-section h4 {
        color: #2c3e50;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 5px;
        margin-bottom: 15px;
    }
    .preview-text {
        padding: 10px;
        background-color: #fff;
        border-radius: 4px;
        margin-top: 10px;
        border: 1px solid #dee2e6;
    }
    .item-row {
        position: relative;
        padding: 10px;
        border: 1px solid #eee;
        border-radius: 4px;
        margin-bottom: 10px !important;
        background-color: #fff;
    }
    .item-row:hover {
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .material-search-results {
        position: absolute;
        z-index: 1000;
        width: calc(100% - 30px);
        max-height: 200px;
        overflow-y: auto;
        background: white;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        display: none;
        margin-top: 2px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .supplier-section {
        margin-top: 10px;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 4px;
    }
    .signature-pad-container {
        position: relative;
        width: 400px;
        height: 200px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        background-color: #fff;
    }
    .signature-pad {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: #fff;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    // Client search functionality
    const clientSearch = document.getElementById('client_search');
    const clientSearchBtn = document.getElementById('client_search_btn');
    const clientSearchResults = document.getElementById('client_search_results');
    
    function searchClients() {
        const searchTerm = clientSearch.value.trim();
        if (searchTerm.length < 2) {
            clientSearchResults.style.display = 'none';
            return;
        }
        
        fetch(`/clients/search?query=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                clientSearchResults.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(client => {
                        const div = document.createElement('div');
                        div.className = 'search-result-item';
                        div.textContent = client.company_name 
                            ? `${client.company_name} (${client.name})`
                            : client.name;
                        div.addEventListener('click', () => {
                            fillClientForm(client);
                            clientSearchResults.style.display = 'none';
                        });
                        clientSearchResults.appendChild(div);
                    });
                    clientSearchResults.style.display = 'block';
                } else {
                    clientSearchResults.style.display = 'none';
                }
            });
    }
    
    clientSearchBtn.addEventListener('click', searchClients);
    clientSearch.addEventListener('input', searchClients);
    
    // Hide search results when clicking outside
    document.addEventListener('click', (e) => {
        if (!clientSearch.contains(e.target) && !clientSearchResults.contains(e.target)) {
            clientSearchResults.style.display = 'none';
        }
    });
    
    // Fill client form with selected client data
    function fillClientForm(client) {
        document.getElementById('company_name').value = client.company_name || '';
        document.getElementById('contact_person').value = client.name || '';
        document.getElementById('client_street').value = client.street || '';
        document.getElementById('client_city').value = client.city || '';
        document.getElementById('client_state').value = client.state || '';
        document.getElementById('client_postal').value = client.postal || '';
        document.getElementById('client_email').value = client.email || '';
        document.getElementById('client_phone').value = client.phone || '';
    }

    // Property address auto-fill
    document.getElementById('property_street').addEventListener('change', function() {
        if (confirm('Is the client address the same as the property address?')) {
            document.getElementById('client_street').value = this.value;
            document.getElementById('client_city').value = document.getElementById('property_city').value;
            document.getElementById('client_state').value = document.getElementById('property_state').value;
            document.getElementById('client_postal').value = document.getElementById('property_postal').value;
        }
    });

    // Handle "Other" scope of work option
    document.getElementById('scope_other').addEventListener('change', function() {
        const otherInput = document.getElementById('other_work_text');
        otherInput.style.display = this.checked ? 'block' : 'none';
        if (!this.checked) {
            otherInput.value = '';
        }
    });

    // Update contract preview
    function updateContractPreview() {
        const scopeWork = Array.from(document.querySelectorAll('input[name="scope_of_work[]"]:checked'))
            .map(el => {
                if (el.id === 'scope_other' && el.checked) {
                    const otherText = document.getElementById('other_work_text').value;
                    return otherText || 'Other';
                }
                return el.value;
            })
            .join(', ') || '[Scope of Work]';
            
        const contractorName = document.getElementById('contractor_name').value || '[Contractor Name]';
        const contractorCompany = document.getElementById('contractor_company').value || '[Contractor Company]';
        const contractorAddress = [
            document.getElementById('contractor_street').value || '[Street]',
            document.getElementById('contractor_city').value || '[City]',
            document.getElementById('contractor_state').value || '[State]',
            document.getElementById('contractor_postal').value || '[Postal]'
        ].filter(Boolean).join(', ');
        
        const clientCompany = document.getElementById('company_name').value;
        const clientContact = document.getElementById('contact_person').value;
        const clientName = clientCompany || clientContact || '[Client Name]';
        const clientAddress = [
            document.getElementById('client_street').value || '[Street]',
            document.getElementById('client_city').value || '[City]',
            document.getElementById('client_state').value || '[State]',
            document.getElementById('client_postal').value || '[Postal]'
        ].filter(Boolean).join(', ');
        
        const propertyAddress = [
            document.getElementById('property_street').value || '[Street]',
            document.getElementById('property_city').value || '[City]',
            document.getElementById('property_state').value || '[State]',
            document.getElementById('property_postal').value || '[Postal]'
        ].filter(Boolean).join(', ');
        
        // Update preview elements
        document.getElementById('agreementClausePreview').textContent = 
            `This ${scopeWork} is executed by and between ${contractorName} (${contractorCompany}) with address at ${contractorAddress} hereafter known as "Contractor" and ${clientName} with address at ${clientAddress} hereafter known as "Client".`;
            
        document.getElementById('serviceClausePreview').textContent = 
            `The Contractor agrees to provide and perform ${scopeWork} for the Client's property with address located at ${propertyAddress}.`;
            
        document.getElementById('projectPeriodPreview').textContent = 
            "This project shall commence and is scheduled to be completed on the following date periods unless otherwise reasonable delays would arise where such delay or interference is not caused by the Contractor, such as but not limited to cause by third party, inclement weather, fortuitous events, including acts of God:";
    }

    // Add event listeners for preview updates
    const fieldsToWatch = [
        'contractor_name', 'contractor_company', 'contractor_street', 'contractor_city', 'contractor_state', 'contractor_postal',
        'company_name', 'contact_person', 'client_street', 'client_city', 'client_state', 'client_postal',
        'property_street', 'property_city', 'property_state', 'property_postal'
    ];
    
    fieldsToWatch.forEach(id => {
        document.getElementById(id)?.addEventListener('input', updateContractPreview);
    });
    
    document.querySelectorAll('input[name="scope_of_work[]"]').forEach(el => {
        el.addEventListener('change', updateContractPreview);
    });

    document.getElementById('other_work_text')?.addEventListener('input', updateContractPreview);

    // Initial preview update
    document.addEventListener('DOMContentLoaded', updateContractPreview);

    // Initialize Material Search for a row
    function initializeMaterialSearch(row) {
        const searchInput = row.querySelector('.material-search');
        const searchResults = row.querySelector('.material-search-results');
        const materialIdInput = row.querySelector('input[name="item_material_id[]"]');
        const hasPreferredCheckbox = row.querySelector('.has-preferred-suppliers');
        const supplierSection = row.querySelector('.supplier-section');
        const supplierSelect = row.querySelector('.supplier-select');
        const amountInput = row.querySelector('.amount');

        if (!searchInput) return;

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            fetch(`/materials/search?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(material => {
                            const div = document.createElement('div');
                            div.className = 'search-result-item';
                            div.textContent = material.name;
                            div.dataset.material = JSON.stringify(material);
                            searchResults.appendChild(div);
                        });
                        searchResults.style.display = 'block';
                    } else {
                        searchResults.style.display = 'none';
                    }
                });
        });

        searchResults.addEventListener('click', function(e) {
            if (e.target.classList.contains('search-result-item')) {
                const material = JSON.parse(e.target.dataset.material);
                searchInput.value = material.name;
                materialIdInput.value = material.id;
                searchResults.style.display = 'none';

                // Update price if available
                if (material.default_price) {
                    amountInput.value = material.default_price;
                    calculateItemTotal(amountInput);
                }

                // Show/hide preferred suppliers
                hasPreferredCheckbox.checked = material.has_preferred_suppliers;
                supplierSection.style.display = material.has_preferred_suppliers ? 'block' : 'none';
                
                if (material.has_preferred_suppliers) {
                    fetch(`/materials/${material.id}/suppliers?preferred=true`)
                        .then(response => response.json())
                        .then(suppliers => {
                            supplierSelect.innerHTML = '<option value="">Select Supplier</option>';
                            suppliers.forEach(supplier => {
                                const option = document.createElement('option');
                                option.value = supplier.id;
                                option.textContent = supplier.name;
                                option.dataset.price = supplier.pivot.price;
                                supplierSelect.appendChild(option);
                            });
                            supplierSelect.style.display = 'block';
                        });
                }
            }
        });

        // Handle supplier selection
        supplierSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.dataset.price) {
                amountInput.value = selectedOption.dataset.price;
                calculateItemTotal(amountInput);
            }
        });
    }

    // Calculate totals
    function calculateItemTotal(input) {
        const row = input.closest('.item-row');
        if (!row) return;
        
        const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
        const amount = parseFloat(row.querySelector('.amount').value) || 0;
        const total = quantity * amount;
        row.querySelector('.total').value = total.toFixed(2);
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const total = parseFloat(row.querySelector('.total').value) || 0;
            grandTotal += total;
        });
        document.getElementById('total_amount').value = grandTotal.toFixed(2);
    }

    // Add item functionality
    document.getElementById('add_item')?.addEventListener('click', function() {
        const container = document.getElementById('item_container');
        const newRow = document.createElement('div');
        newRow.className = 'row item-row mb-2';
        
        newRow.innerHTML = `
            <div class="col-md-5">
                <div class="input-group">
                    <input type="text" class="form-control material-search" placeholder="Search material">
                    <input type="hidden" name="item_material_id[]">
                </div>
                <div class="material-search-results"></div>
                <div class="supplier-section mt-2" style="display: none;">
                    <div class="form-check">
                        <input class="form-check-input has-preferred-suppliers" type="checkbox" disabled>
                        <label class="form-check-label">Has preferred suppliers</label>
                    </div>
                    <select class="form-select supplier-select mt-2" name="item_supplier_id[]" style="display: none;">
                        <option value="">Select Supplier</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control quantity" name="item_quantity[]" 
                    placeholder="Qty" min="0" step="0.01" required>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control amount" name="item_amount[]" 
                    placeholder="Amount" min="0" step="0.01" required>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control total" placeholder="Total" readonly>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm remove-item">×</button>
            </div>
        `;

        container.appendChild(newRow);

        // Initialize material search for the new row
        initializeMaterialSearch(newRow);

        // Add event listeners for calculations
        const quantityInput = newRow.querySelector('.quantity');
        const amountInput = newRow.querySelector('.amount');
        
        quantityInput.addEventListener('input', function() {
            calculateItemTotal(this);
        });
        
        amountInput.addEventListener('input', function() {
            calculateItemTotal(this);
        });
    });

    // Remove item functionality
    document.getElementById('item_container')?.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            e.target.closest('.item-row').remove();
            calculateGrandTotal();
        }
    });

    // Initialize existing items
    document.querySelectorAll('.item-row').forEach(row => {
        initializeMaterialSearch(row);
        
        const quantityInput = row.querySelector('.quantity');
        const amountInput = row.querySelector('.amount');
        
        quantityInput.addEventListener('input', function() {
            calculateItemTotal(this);
        });
        
        amountInput.addEventListener('input', function() {
            calculateItemTotal(this);
        });
    });

    // Add first item if needed
    document.addEventListener('DOMContentLoaded', function() {
        const itemContainer = document.getElementById('item_container');
        if (itemContainer && itemContainer.querySelectorAll('.item-row').length === 0) {
            document.getElementById('add_item')?.click();
        }
    });

    // Initialize signature pads
    let contractorSignaturePad = null;
    let clientSignaturePad = null;

    function initializeSignaturePads() {
        const contractorCanvas = document.getElementById('contractor_signature');
        const clientCanvas = document.getElementById('client_signature');

        if (contractorCanvas && clientCanvas) {
            contractorSignaturePad = new SignaturePad(contractorCanvas, {
                backgroundColor: 'rgb(255, 255, 255)'
            });
            
            clientSignaturePad = new SignaturePad(clientCanvas, {
                backgroundColor: 'rgb(255, 255, 255)'
            });

            // Load existing signatures if available
            @if(isset($contract))
                @if($contract->contractor_signature)
                    contractorSignaturePad.fromDataURL("{{ $contract->contractor_signature }}");
                @endif
                @if($contract->client_signature)
                    clientSignaturePad.fromDataURL("{{ $contract->client_signature }}");
                @endif
            @endif

            // Update hidden inputs when signing
            contractorSignaturePad.addEventListener('endStroke', () => {
                document.getElementById('contractor_signature_data').value = contractorSignaturePad.toDataURL();
            });

            clientSignaturePad.addEventListener('endStroke', () => {
                document.getElementById('client_signature_data').value = clientSignaturePad.toDataURL();
            });
        }
    }

    function clearSignature(type) {
        if (type === 'contractor' && contractorSignaturePad) {
            contractorSignaturePad.clear();
            document.getElementById('contractor_signature_data').value = '';
        } else if (type === 'client' && clientSignaturePad) {
            clientSignaturePad.clear();
            document.getElementById('client_signature_data').value = '';
        }
    }

    // Initialize signature pads on page load
    document.addEventListener('DOMContentLoaded', function() {
        initializeSignaturePads();
    });

    // Handle form submission
    document.getElementById('contractForm')?.addEventListener('submit', function(e) {
        // Add any final validation here if needed
        if (contractorSignaturePad && contractorSignaturePad.isEmpty()) {
            alert('Please provide contractor signature');
            e.preventDefault();
            return;
        }
        if (clientSignaturePad && clientSignaturePad.isEmpty()) {
            alert('Please provide client signature');
            e.preventDefault();
            return;
        }
    });

    // Resize handler for signature pads
    window.addEventListener('resize', function() {
        if (contractorSignaturePad || clientSignaturePad) {
            initializeSignaturePads();
        }
    });
</script>
@endpush 