@extends('layouts.app')

@push('styles')
<style>
    .content-wrapper {
        margin-left: 0;
        padding: 20px;
        min-height: 100vh;
        background-color: #f8f9fa;
    }

    @media (max-width: 768px) {
        .content-wrapper {
            margin-left: 0;
            padding-top: 56px;
        }
    }

    .section-container {
        margin-bottom: 2rem;
        padding: 1.25rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        background-color: #fff;
    }

    .section-title {
        margin-bottom: 1.25rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #0d6efd;
        color: #344767;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #344767;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #edf2f7;
    }

    .scope-category-group {
        background-color: #f8f9fa;
            padding: 1rem;
        border-radius: 0.5rem;
        height: 100%;
        margin-bottom: 1rem;
    }

    .scope-category-group h6 {
        color: #344767;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .form-check {
        margin-bottom: 0.75rem;
    }

    .form-check:last-child {
        margin-bottom: 0;
    }

    .signature-pad {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
    }

    .signature-pad canvas {
        width: 100%;
        height: 200px;
    }

    .table-responsive {
        margin: 0;
        padding: 0;
    }

    #scopeMaterialsTable th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #344767;
    }

    .room-row {
        background-color: #fff;
        padding: 1rem;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
        border: 1px solid #dee2e6;
    }

    @media (max-width: 768px) {
        .section-container {
            padding: 1rem;
        }

        .card-body {
            padding: 1rem;
        }

        .row {
            margin-left: -0.5rem;
            margin-right: -0.5rem;
        }

        .col-md-6, .col-md-4, .col-md-3, .col-md-2, .col-md-1 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

    .form-group {
            margin-bottom: 0.75rem;
        }
    }
    
    /* Search results styling */
    .search-results {
        position: absolute;
        z-index: 1050;
        width: 95%;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        max-height: 300px;
        overflow-y: auto;
        margin-top: 0.25rem;
    }

    .search-result-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #dee2e6;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .search-result-item:hover {
        background-color: #f8f9fa;
    }

    .search-result-item:last-child {
        border-bottom: none;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
        <div class="col-12">
                <div class="card mb-4 mt-4">
                <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ isset($contract) ? 'Edit Contract' : 'Create New Contract' }}</h5>
                        </div>
                </div>
                <div class="card-body">
                    <form id="contractForm" method="POST" action="{{ isset($contract) ? route('contracts.update', $contract->id) : route('contracts.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if(isset($contract))
                            @method('PUT')
                        @endif

                        <!-- Contractor Information Section -->
                            <div class="section-container mb-4">
                            <h5 class="section-title">Contractor Information</h5>
                                <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                            <label for="contractor_name" class="form-label">Name</label>
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
                                            <label for="contractor_company" class="form-label">Company Name (Optional)</label>
                                        <input type="text" class="form-control @error('contractor_company') is-invalid @enderror" 
                                            id="contractor_company" name="contractor_company" 
                                            value="{{ old('contractor_company', $contractor ? $contractor->company_name : '') }}">
                                        @error('contractor_company')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                                <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <div class="form-group">
                                            <label for="contractor_email" class="form-label">Email</label>
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
                                            <label for="contractor_phone" class="form-label">Phone Number</label>
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
                                        <div id="clientSearchResults" class="mt-2 d-none">
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

                                <!-- Client Address Fields -->
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
                        </div>

                        <!-- Property Information Section -->
                        <div class="section-container" id="propertySection">
                            <h5 class="section-title">Property Information</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="property_type">Property Type</label>
                                        <select class="form-control @error('property_type') is-invalid @enderror" 
                                            id="property_type" name="property_type" required>
                                            <option value="">Select Type</option>
                                            <option value="residential" {{ old('property_type', $property ? $property->property_type : '') == 'residential' ? 'selected' : '' }}>Residential</option>
                                            <option value="commercial" {{ old('property_type', $property ? $property->property_type : '') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                                            <option value="industrial" {{ old('property_type', $property ? $property->property_type : '') == 'industrial' ? 'selected' : '' }}>Industrial</option>
                                        </select>
                                        @error('property_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
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

                        <!-- Room/Area Details -->
                        <div class="section-container" id="roomSection">
                            <h5 class="section-title">Room/Area Details</h5>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary" id="addRoomBtn">
                                        <i class="fas fa-plus"></i> Add Room/Area
                                    </button>
                                    <button type="button" class="btn btn-secondary ms-2" id="applyToAllBtn">
                                        Apply Selected Scope to All Rooms
                                    </button>
                                </div>
                            </div>
                            <div id="roomDetails">
                                <!-- Rooms will be added here dynamically -->
                            </div>
                            
                            <!-- Grand Total Summary -->
                            <div class="card mt-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">Grand Total Summary</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <p class="mb-1">Total Floor Area:</p>
                                            <h5 id="grandTotalArea">0 sq m</h5>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="mb-1">Total Materials Cost:</p>
                                            <h5 id="grandTotalMaterials">₱0.00</h5>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="mb-1">Total Labor Cost:</p>
                                            <h5 id="grandTotalLabor">₱0.00</h5>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="mb-1">Grand Total:</p>
                                            <h5 id="grandTotal">₱0.00</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Estimated Timeline -->
                        <div class="row mt-4">
                            <div class="col-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Estimated Timeline</h6>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="text-muted">Total Estimated Days:</span>
                                                    <span class="h4 mb-0 ms-2" id="totalEstimatedDays">0</span>
                                </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Scope Materials Summary -->
                    <div class="row mt-4">
                        <div class="col-12">
                                <div class="card">
                                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Required Materials by Scope</h6>
                                                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="scopeMaterialsTable">
                                            <thead>
                                                <tr>
                                                    <th>Scope</th>
                                                    <th>Material</th>
                                                    <th>Quantity</th>
                                                    <th>Base Price</th>
                                                    <th>Supplier</th>
                                                    <th>Total</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Materials will be populated dynamically -->
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="5" class="text-end"><strong>Total Materials Cost:</strong></td>
                                                    <td colspan="2"><strong id="totalMaterialsCost">₱0.00</strong></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                                            </div>
                                                        </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mt-3">
                                <div class="col-md-12">
                                                            <div class="form-group">
                                        <label for="scope_description">Additional Notes & Custom Requirements</label>
                                        <textarea class="form-control @error('scope_description') is-invalid @enderror" 
                                            id="scope_description" name="scope_description" rows="4">{{ old('scope_description', $contract ? $contract->scope_description : '') }}</textarea>
                                        @error('scope_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                                            </div>
                                                        </div>
                            </div>
                        </div>

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
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="total_amount">Total Contract Amount</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">₱</span>
                                            </div>
                                            <input type="number" step="0.01" min="0" class="form-control @error('total_amount') is-invalid @enderror" 
                                                id="total_amount" name="total_amount" 
                                                value="{{ old('total_amount', $contract ? $contract->total_amount : '') }}" readonly>
                                            @error('total_amount')
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
                            </div>
                        </div>

                        <!-- Signature Section -->
                        <div class="section-container" id="signaturesSection">
                            <h5 class="section-title">Signatures</h5>
                            
                            <!-- Contractor Signature -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                        <label>Contractor Signature</label>
                                    <div class="signature-pad">
                                        <canvas id="contractorSignaturePad"></canvas>
                                    </div>
                                    <div class="signature-buttons">
                                        <button type="button" class="btn btn-secondary btn-sm" id="clearContractorSignature">Clear</button>
                                    </div>
                                        @if(isset($existing_contractor_signature))
                                        <div class="mt-2">
                                            <img src="{{ $existing_contractor_signature }}" alt="Existing Contractor Signature" class="img-fluid" style="max-height: 100px;">
                                                <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="keep_contractor_signature" id="keepContractorSignature" value="1">
                                                <label class="form-check-label" for="keepContractorSignature">
                                                        Keep existing signature
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                        </div>
                                
                                <!-- Client Signature -->
                                <div class="col-md-6">
                                        <label>Client Signature</label>
                                    <div class="signature-pad">
                                        <canvas id="clientSignaturePad"></canvas>
                                    </div>
                                    <div class="signature-buttons">
                                        <button type="button" class="btn btn-secondary btn-sm" id="clearClientSignature">Clear</button>
                                    </div>
                                        @if(isset($existing_client_signature))
                                        <div class="mt-2">
                                            <img src="{{ $existing_client_signature }}" alt="Existing Client Signature" class="img-fluid" style="max-height: 100px;">
                                                <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="keep_client_signature" id="keepClientSignature" value="1">
                                                <label class="form-check-label" for="keepClientSignature">
                                                        Keep existing signature
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                        </div>
                                        </div>
                                    </div>
                        </div>

                        <!-- Timeline and Labor Section -->
                        <div class="section-container" id="timelineSection">
                            <h5 class="section-title">Project Timeline & Labor</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                            id="start_date" name="start_date" 
                                            value="{{ old('start_date', $contract ? $contract->start_date?->format('Y-m-d') : '') }}" 
                                            required
                                            onchange="updateEndDate()">
                                        @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                        </div>
                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_date">End Date</label>
                                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                            id="end_date" name="end_date" 
                                            value="{{ old('end_date', $contract ? $contract->end_date?->format('Y-m-d') : '') }}" 
                                            required>
                                        @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
        </div>
    </div>
</div>

                            <!-- Labor Costs -->
                            <div class="row mt-4">
                                <div class="col-12">
                <div class="card">
                                        <div class="card-header bg-white">
                                            <h6 class="mb-0">Labor Costs</h6>
                                        </div>
                    <div class="card-body">
                        <div class="row">
                                                <div class="col-md-6">
                                <div class="form-group">
                                                        <label for="workers_count">Number of Workers</label>
                                                        <input type="number" class="form-control @error('workers_count') is-invalid @enderror"
                                                            id="workers_count" name="workers_count"
                                                            value="{{ old('workers_count', $contract ? $contract->workers_count : '') }}"
                                                            min="1" step="1" required
                                                            onchange="calculateLaborCost()">
                                                        @error('workers_count')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                        </div>
                                    </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="daily_rate">Daily Rate per Worker</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">₱</span>
                                                            <input type="number" class="form-control @error('daily_rate') is-invalid @enderror"
                                                                id="daily_rate" name="daily_rate"
                                                                value="{{ old('daily_rate', $contract ? $contract->daily_rate : '500') }}"
                                                                min="0" step="0.01" required
                                                                onchange="calculateLaborCost()">
                                </div>
                                                        @error('daily_rate')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                            </div>
                        </div>
                                            </div>
                                            <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                                        <label>Working Days</label>
                                                        <input type="number" class="form-control" id="working_days" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                                        <label>Total Labor Cost</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">₱</span>
                                                            <input type="number" class="form-control" id="total_labor_cost" name="total_labor_cost" readonly>
                                </div>
                            </div>
                        </div>
                                </div>
                            </div>
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize core functionality
    initializePaymentMethod();
    initializeSignaturePads();
    
    // Create initial room if none exists
    const roomDetails = document.getElementById('roomDetails');
    if (roomDetails && roomDetails.children.length === 0) {
        createRoomRow();
    }
    
    // Update initial calculations
    updateGrandTotal();
    updateTimeline();
    
    // Initialize date fields
    const startDateInput = document.getElementById('start_date');
    if (startDateInput) {
        startDateInput.addEventListener('change', updateEndDate);
    }

    // Add room button click handler
    const addRoomBtn = document.getElementById('addRoomBtn');
    if (addRoomBtn) {
        addRoomBtn.addEventListener('click', createRoomRow);
    }

    // Apply to all rooms button handler
    const applyToAllBtn = document.getElementById('applyToAllBtn');
    if (applyToAllBtn) {
        applyToAllBtn.addEventListener('click', function() {
            const rooms = document.querySelectorAll('.room-row');
            if (rooms.length > 0) {
                // Get the scope selections from the first room
                const firstRoom = rooms[0];
                const selectedScopes = Array.from(firstRoom.querySelectorAll('.scope-checkbox:checked'))
                    .map(checkbox => checkbox.value);
                
                // Apply to all other rooms
                rooms.forEach((room, index) => {
                    if (index > 0) { // Skip first room
                        room.querySelectorAll('.scope-checkbox').forEach(checkbox => {
                            checkbox.checked = selectedScopes.includes(checkbox.value);
                            updateRoomCalculations(checkbox);
                        });
                    }
                });
                
                updateGrandTotal();
            }
        });
    }
});

function initializePaymentMethod() {
    const paymentMethod = document.getElementById('payment_method');
    const bankDetails = document.getElementById('bankDetails');
    const checkDetails = document.getElementById('checkDetails');

    if (paymentMethod) {
        paymentMethod.addEventListener('change', function() {
            bankDetails.style.display = this.value === 'bank_transfer' ? 'flex' : 'none';
            checkDetails.style.display = this.value === 'check' ? 'flex' : 'none';
        });
        
        // Initialize on page load
        paymentMethod.dispatchEvent(new Event('change'));
    }
}

function initializeSignaturePads() {
    const contractorCanvas = document.getElementById('contractorSignaturePad');
    const clientCanvas = document.getElementById('clientSignaturePad');
    
    if (contractorCanvas && clientCanvas) {
        const contractorPad = new SignaturePad(contractorCanvas);
        const clientPad = new SignaturePad(clientCanvas);

        // Clear signature buttons
        document.getElementById('clearContractorSignature')?.addEventListener('click', () => contractorPad.clear());
        document.getElementById('clearClientSignature')?.addEventListener('click', () => clientPad.clear());

        // Form submission handling
        document.getElementById('contractForm')?.addEventListener('submit', function(e) {
            if (!contractorPad.isEmpty() || document.getElementById('keepContractorSignature')?.checked) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'contractor_signature';
                input.value = contractorPad.isEmpty() ? '' : contractorPad.toDataURL();
                this.appendChild(input);
            }

            if (!clientPad.isEmpty() || document.getElementById('keepClientSignature')?.checked) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'client_signature';
                input.value = clientPad.isEmpty() ? '' : clientPad.toDataURL();
                this.appendChild(input);
            }
        });

        // Handle canvas resize
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            [contractorCanvas, clientCanvas].forEach(canvas => {
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
            });
            contractorPad.clear();
            clientPad.clear();
        }

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas(); // Initial setup
    }
}

// Room management with scope of work
document.addEventListener('DOMContentLoaded', function() {
    const addRoomBtn = document.getElementById('addRoomBtn');
    const applyToAllBtn = document.getElementById('applyToAllBtn');
    const roomDetails = document.getElementById('roomDetails');
    let roomCount = 0;

    function createRoomRow() {
        const roomDiv = document.createElement('div');
        roomDiv.className = 'room-row card mb-4';
        roomDiv.innerHTML = `
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Room/Area ${roomCount + 1}</h6>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRoom(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Room/Area Name</label>
                            <input type="text" class="form-control" name="rooms[${roomCount}][name]" 
                                placeholder="e.g., Living Room" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Length (meters)</label>
                            <input type="number" class="form-control room-dimension" 
                                name="rooms[${roomCount}][length]" 
                                min="0.1" step="0.01" required
                                onchange="calculateRoomArea(this)">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Width (meters)</label>
                            <input type="number" class="form-control room-dimension" 
                                name="rooms[${roomCount}][width]" 
                                min="0.1" step="0.01" required
                                onchange="calculateRoomArea(this)">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Area (sq m)</label>
                            <input type="text" class="form-control room-area" 
                                name="rooms[${roomCount}][area]" 
                                value="0.00" readonly>
                        </div>
                    </div>
                </div>

                <!-- Scope of Work for this room -->
                <div class="scope-of-work-section">
                    <h6 class="mb-3">Scope of Work for this Room/Area</h6>
                    <div class="row">
                        <!-- Building Construction Services -->
                        <div class="col-md-4">
                            <div class="scope-category-group">
                                <h6 class="mb-2">Building Construction Services</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input scope-checkbox" type="checkbox" 
                                        name="rooms[${roomCount}][scope][]" 
                                        value="building_finishing"
                                        data-timeframe="14"
                                        onchange="updateRoomCalculations(this)">
                                    <label class="form-check-label">
                                        Building Finishing
                                        <small class="d-block text-muted">Est. 14 days</small>
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input scope-checkbox" type="checkbox" 
                                        name="rooms[${roomCount}][scope][]" 
                                        value="nonresidential_construction"
                                        data-timeframe="30"
                                        onchange="updateRoomCalculations(this)">
                                    <label class="form-check-label">
                                        Nonresidential Construction
                                        <small class="d-block text-muted">Est. 30 days</small>
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input scope-checkbox" type="checkbox" 
                                        name="rooms[${roomCount}][scope][]" 
                                        value="commercial_office"
                                        data-timeframe="45"
                                        onchange="updateRoomCalculations(this)">
                                    <label class="form-check-label">
                                        Commercial & Office Building
                                        <small class="d-block text-muted">Est. 45 days</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Painting Services -->
                        <div class="col-md-4">
                            <div class="scope-category-group">
                                <h6 class="mb-2">Painting Services</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input scope-checkbox" type="checkbox" 
                                        name="rooms[${roomCount}][scope][]" 
                                        value="commercial_painting"
                                        data-timeframe="10"
                                        onchange="updateRoomCalculations(this)">
                                    <label class="form-check-label">
                                        Commercial Painting
                                        <small class="d-block text-muted">Est. 10 days</small>
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input scope-checkbox" type="checkbox" 
                                        name="rooms[${roomCount}][scope][]" 
                                        value="interior_painting"
                                        data-timeframe="7"
                                        onchange="updateRoomCalculations(this)">
                                    <label class="form-check-label">
                                        Interior Painting
                                        <small class="d-block text-muted">Est. 7 days</small>
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input scope-checkbox" type="checkbox" 
                                        name="rooms[${roomCount}][scope][]" 
                                        value="exterior_painting"
                                        data-timeframe="8"
                                        onchange="updateRoomCalculations(this)">
                                    <label class="form-check-label">
                                        Exterior Painting
                                        <small class="d-block text-muted">Est. 8 days</small>
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input scope-checkbox" type="checkbox" 
                                        name="rooms[${roomCount}][scope][]" 
                                        value="pavement_marking"
                                        data-timeframe="3"
                                        onchange="updateRoomCalculations(this)">
                                    <label class="form-check-label">
                                        Pavement Marking
                                        <small class="d-block text-muted">Est. 3 days</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Repair Services -->
                        <div class="col-md-4">
                            <div class="scope-category-group">
                                <h6 class="mb-2">Repair & Maintenance</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input scope-checkbox" type="checkbox" 
                                        name="rooms[${roomCount}][scope][]" 
                                        value="automotive_repair"
                                        data-timeframe="5"
                                        onchange="updateRoomCalculations(this)">
                                    <label class="form-check-label">
                                        Automotive Repair
                                        <small class="d-block text-muted">Est. 5 days</small>
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input scope-checkbox" type="checkbox" 
                                        name="rooms[${roomCount}][scope][]" 
                                        value="electronic_repair"
                                        data-timeframe="4"
                                        onchange="updateRoomCalculations(this)">
                                    <label class="form-check-label">
                                        Electronic Equipment Repair
                                        <small class="d-block text-muted">Est. 4 days</small>
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input scope-checkbox" type="checkbox" 
                                        name="rooms[${roomCount}][scope][]" 
                                        value="durable_goods"
                                        data-timeframe="2"
                                        onchange="updateRoomCalculations(this)">
                                    <label class="form-check-label">
                                        Durable Goods Services
                                        <small class="d-block text-muted">Est. 2 days</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Room Summary -->
                    <div class="room-summary mt-3 p-3 bg-light rounded">
                        <div class="row">
                            <div class="col-md-4">
                                <p class="mb-1">Room Materials Cost:</p>
                                <h6 class="room-materials-cost">₱0.00</h6>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1">Room Labor Cost:</p>
                                <h6 class="room-labor-cost">₱0.00</h6>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1">Room Total:</p>
                                <h6 class="room-total-cost">₱0.00</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        roomDetails.appendChild(roomDiv);
        roomCount++;
        updateGrandTotal();
    }

    // Add room button click handler
    if (addRoomBtn) {
        addRoomBtn.addEventListener('click', createRoomRow);
    }

    // Apply to all rooms button handler
    if (applyToAllBtn) {
        applyToAllBtn.addEventListener('click', function() {
            const rooms = document.querySelectorAll('.room-row');
            if (rooms.length > 0) {
                // Get the scope selections from the first room
                const firstRoom = rooms[0];
                const selectedScopes = Array.from(firstRoom.querySelectorAll('.scope-checkbox:checked'))
                    .map(checkbox => checkbox.value);
                
                // Apply to all other rooms
                rooms.forEach((room, index) => {
                    if (index > 0) { // Skip first room
                        room.querySelectorAll('.scope-checkbox').forEach(checkbox => {
                            checkbox.checked = selectedScopes.includes(checkbox.value);
                            updateRoomCalculations(checkbox);
                        });
                    }
                });
                
                updateGrandTotal();
            }
        });
    }
});

function calculateRoomArea(input) {
    const roomRow = input.closest('.room-row');
    const length = parseFloat(roomRow.querySelector('input[name$="[length]"]').value) || 0;
    const width = parseFloat(roomRow.querySelector('input[name$="[width]"]').value) || 0;
    const area = length * width;
    roomRow.querySelector('.room-area').value = area.toFixed(2);
    roomRow.querySelector('.room-area').textContent = area.toFixed(2);
    updateRoomCalculations(roomRow);
}

function updateRoomCalculations(element) {
    const roomRow = element.closest('.room-row');
    const areaInput = roomRow.querySelector('.room-area');
    const area = parseFloat(areaInput.value || areaInput.textContent) || 0;
    
    const selectedScopes = Array.from(roomRow.querySelectorAll('.scope-checkbox:checked'))
        .map(checkbox => checkbox.value);
    
    let materialsList = [];
    let totalMaterialsCost = 0;
    let totalLaborCost = 0;
    let totalTimeframe = 0;
    
    // Calculate materials and costs for each selected scope
    selectedScopes.forEach(scope => {
        if (scopeMaterials[scope]) {
            const scopeData = scopeMaterials[scope];
            
            // Add timeframe instead of taking maximum
            totalTimeframe += scopeData.timeframe;
            
            // Calculate materials
            scopeData.materials.forEach(material => {
                const qty = typeof material.quantity === 'function' ? material.quantity(area) : material.quantity;
                const totalPrice = material.basePrice * qty;
                
                materialsList.push({
                    scope: scopeData.name,
                    name: material.name,
                    quantity: qty,
                    unit: material.unit,
                    basePrice: material.basePrice,
                    total: totalPrice,
                    timeframe: scopeData.timeframe
                });
                
                totalMaterialsCost += totalPrice;
            });
        }
    });
    
    // Calculate labor cost based on total timeframe
    const workersCount = parseInt(document.getElementById('workers_count')?.value) || 1;
    const dailyRate = parseFloat(document.getElementById('daily_rate')?.value) || 500;
    totalLaborCost = workersCount * dailyRate * totalTimeframe;
    
    // Update room summary
    roomRow.querySelector('.room-materials-cost').textContent = `₱${totalMaterialsCost.toFixed(2)}`;
    roomRow.querySelector('.room-labor-cost').textContent = `₱${totalLaborCost.toFixed(2)}`;
    roomRow.querySelector('.room-total-cost').textContent = `₱${(totalMaterialsCost + totalLaborCost).toFixed(2)}`;
    
    // Update materials table
    const materialsTableBody = document.getElementById('scopeMaterialsTable').querySelector('tbody');
    materialsTableBody.innerHTML = materialsList.map(material => `
        <tr>
            <td>${material.scope}</td>
            <td>${material.name}</td>
            <td>${material.quantity.toFixed(2)} ${material.unit}</td>
            <td>₱${material.basePrice.toFixed(2)}</td>
            <td>-</td>
            <td>₱${material.total.toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-edit"></i>
                </button>
            </td>
        </tr>
    `).join('');

    // Update total materials cost in the table footer
    const totalMaterialsCostElement = document.getElementById('totalMaterialsCost');
    if (totalMaterialsCostElement) {
        totalMaterialsCostElement.textContent = `₱${totalMaterialsCost.toFixed(2)}`;
    }
    
    updateGrandTotal();
    updateTimeline();
}

function updateGrandTotal() {
    let totalArea = 0;
    let totalMaterials = 0;
    let totalLabor = 0;
    
    document.querySelectorAll('.room-row').forEach(room => {
        totalArea += parseFloat(room.querySelector('.room-area').textContent) || 0;
        totalMaterials += parseFloat(room.querySelector('.room-materials-cost').textContent.replace('₱', '')) || 0;
        totalLabor += parseFloat(room.querySelector('.room-labor-cost').textContent.replace('₱', '')) || 0;
    });
    
    document.getElementById('grandTotalArea').textContent = `${totalArea.toFixed(2)} sq m`;
    document.getElementById('grandTotalMaterials').textContent = `₱${totalMaterials.toFixed(2)}`;
    document.getElementById('grandTotalLabor').textContent = `₱${totalLabor.toFixed(2)}`;
    document.getElementById('grandTotal').textContent = `₱${(totalMaterials + totalLabor).toFixed(2)}`;
    
    // Update the total contract amount
    const totalAmount = document.getElementById('total_amount');
    if (totalAmount) {
        totalAmount.value = (totalMaterials + totalLabor).toFixed(2);
    }
}

function removeRoom(button) {
    const roomRow = button.closest('.room-row');
    roomRow.remove();
    updateGrandTotal();
}

// Materials data structure with standard items and prices
const scopeMaterials = {
    // Building Construction Services
    building_finishing: {
        name: 'Building Finishing Contractors',
        timeframe: 14,
        materials: [
            { name: 'Drywall Sheets', unit: 'pieces', basePrice: 850.00, quantity: (area) => Math.ceil(area * 0.5) },
            { name: 'Ceiling Tiles', unit: 'boxes', basePrice: 2500.00, quantity: (area) => Math.ceil(area * 0.15) },
            { name: 'Finishing Tools Set', unit: 'sets', basePrice: 12000.00, quantity: () => 1 },
            { name: 'Paint and Primers', unit: 'gallons', basePrice: 2800.00, quantity: (area) => Math.ceil(area * 0.08) },
            { name: 'Safety Equipment', unit: 'sets', basePrice: 5000.00, quantity: () => 1 }
        ]
    },
    nonresidential_construction: {
        name: 'Nonresidential Building Construction',
        timeframe: 30,
        materials: [
            { name: 'Structural Steel', unit: 'tons', basePrice: 65000.00, quantity: (area) => Math.ceil(area * 0.15) },
            { name: 'Concrete Mix', unit: 'cubic meters', basePrice: 3500.00, quantity: (area) => Math.ceil(area * 0.3) },
            { name: 'Construction Safety Equipment', unit: 'sets', basePrice: 15000.00, quantity: () => 1 },
            { name: 'Heavy Equipment Rental', unit: 'days', basePrice: 25000.00, quantity: () => 5 }
        ]
    },
    commercial_office: {
        name: 'Commercial and Office Building Contractors',
        timeframe: 45,
        materials: [
            { name: 'Office Grade Materials Pack', unit: 'sets', basePrice: 75000.00, quantity: (area) => Math.ceil(area * 0.05) },
            { name: 'Commercial Grade Fixtures', unit: 'sets', basePrice: 45000.00, quantity: (area) => Math.ceil(area * 0.04) },
            { name: 'Safety Equipment', unit: 'sets', basePrice: 25000.00, quantity: () => 1 },
            { name: 'Building Materials', unit: 'lots', basePrice: 150000.00, quantity: (area) => Math.ceil(area * 0.025) }
        ]
    },

    // Painting Services
    commercial_painting: {
        name: 'Commercial Painting',
        timeframe: 10,
        materials: [
            { name: 'Commercial Grade Paint', unit: 'gallons', basePrice: 2500.00, quantity: (area) => Math.ceil(area * 0.2) },
            { name: 'Industrial Paint Sprayer', unit: 'units', basePrice: 45000.00, quantity: () => 1 },
            { name: 'Paint Supplies', unit: 'sets', basePrice: 8500.00, quantity: () => 1 },
            { name: 'Safety Equipment', unit: 'sets', basePrice: 8500.00, quantity: () => 1 }
        ]
    },
    interior_painting: {
        name: 'Interior Commercial Painting',
        timeframe: 7,
        materials: [
            { name: 'Interior Paint', unit: 'gallons', basePrice: 1800.00, quantity: (area) => Math.ceil(area * 0.15) },
            { name: 'Primer', unit: 'gallons', basePrice: 1200.00, quantity: (area) => Math.ceil(area * 0.1) },
            { name: 'Painting Tools Set', unit: 'sets', basePrice: 15000.00, quantity: () => 1 },
            { name: 'Protection Materials', unit: 'sets', basePrice: 5000.00, quantity: () => 1 }
        ]
    },
    exterior_painting: {
        name: 'Exterior Commercial Painting',
        timeframe: 8,
        materials: [
            { name: 'Exterior Paint', unit: 'gallons', basePrice: 2200.00, quantity: (area) => Math.ceil(area * 0.18) },
            { name: 'Weather Sealant', unit: 'gallons', basePrice: 1500.00, quantity: (area) => Math.ceil(area * 0.1) },
            { name: 'Scaffolding Rental', unit: 'days', basePrice: 3500.00, quantity: () => 5 },
            { name: 'Safety Equipment', unit: 'sets', basePrice: 12000.00, quantity: () => 1 }
        ]
    },
    pavement_marking: {
        name: 'Pavement Marking',
        timeframe: 3,
        materials: [
            { name: 'Road Paint', unit: 'gallons', basePrice: 3500.00, quantity: (area) => Math.ceil(area * 0.05) },
            { name: 'Line Marking Machine', unit: 'units', basePrice: 35000.00, quantity: () => 1 },
            { name: 'Safety Cones and Signs', unit: 'sets', basePrice: 12000.00, quantity: () => 1 },
            { name: 'Reflective Materials', unit: 'sets', basePrice: 8500.00, quantity: () => 1 }
        ]
    },

    // Repair Services
    automotive_repair: {
        name: 'Automotive Repair and Maintenance',
        timeframe: 5,
        materials: [
            { name: 'Diagnostic Equipment', unit: 'sets', basePrice: 85000.00, quantity: () => 1 },
            { name: 'Tool Set', unit: 'sets', basePrice: 45000.00, quantity: () => 1 },
            { name: 'Safety Equipment', unit: 'sets', basePrice: 15000.00, quantity: () => 1 },
            { name: 'Maintenance Supplies', unit: 'sets', basePrice: 25000.00, quantity: () => 1 }
        ]
    },
    electronic_repair: {
        name: 'Electronic and Precision Equipment Repair',
        timeframe: 4,
        materials: [
            { name: 'Electronic Testing Equipment', unit: 'sets', basePrice: 125000.00, quantity: () => 1 },
            { name: 'Soldering Station', unit: 'units', basePrice: 35000.00, quantity: () => 1 },
            { name: 'Component Kit', unit: 'sets', basePrice: 25000.00, quantity: () => 1 },
            { name: 'Precision Tools', unit: 'sets', basePrice: 75000.00, quantity: () => 1 }
        ]
    },
    durable_goods: {
        name: 'Miscellaneous Durable Goods',
        timeframe: 2,
        materials: [
            { name: 'Storage Equipment', unit: 'sets', basePrice: 45000.00, quantity: () => 1 },
            { name: 'Handling Equipment', unit: 'units', basePrice: 65000.00, quantity: () => 1 },
            { name: 'Safety Supplies', unit: 'sets', basePrice: 15000.00, quantity: () => 1 }
        ]
    }
};

function updateTimeline() {
    let totalTimeframe = 0;
    
    // Sum up timeframes from all rooms
    document.querySelectorAll('.room-row').forEach(room => {
        const selectedScopes = Array.from(room.querySelectorAll('.scope-checkbox:checked'))
            .map(checkbox => checkbox.value);
        
        // Add up timeframes for all selected scopes
        selectedScopes.forEach(scope => {
            if (scopeMaterials[scope]) {
                totalTimeframe += scopeMaterials[scope].timeframe;
            }
        });
    });
    
    // Update the total estimated days display
    document.getElementById('totalEstimatedDays').textContent = totalTimeframe;
    
    // Update the end date if start date is set
    updateEndDate();
    
    // Update working days input
    const workingDaysInput = document.getElementById('working_days');
    if (workingDaysInput) {
        workingDaysInput.value = totalTimeframe;
    }
}

function updateEndDate() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const totalDays = parseInt(document.getElementById('totalEstimatedDays').textContent) || 0;
    
    if (startDateInput && endDateInput && startDateInput.value) {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(startDate);
        endDate.setDate(startDate.getDate() + totalDays);
        
        // Format the date as YYYY-MM-DD
        const endDateStr = endDate.toISOString().split('T')[0];
        endDateInput.value = endDateStr;
    }
}

// Update working days display when timeline changes
document.addEventListener('DOMContentLoaded', function() {
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'characterData' && mutation.target.id === 'totalEstimatedDays') {
                document.getElementById('working_days').value = mutation.target.textContent;
                calculateLaborCost();
            }
        });
    });

    const totalEstimatedDays = document.getElementById('totalEstimatedDays');
    if (totalEstimatedDays) {
        observer.observe(totalEstimatedDays, { characterData: true, subtree: true });
    }
});
</script>
@endpush