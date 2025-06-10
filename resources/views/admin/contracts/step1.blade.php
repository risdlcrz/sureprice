@extends('layouts.app')

@push('styles')
<style>
    .content-wrapper {
        margin-left: 0;
        padding: 20px;
        min-height: 100vh;
        background-color: #f8f9fa;
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

    .progress {
        height: 0.5rem;
        margin-bottom: 2rem;
    }

    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
    }

    .step {
        text-align: center;
        flex: 1;
        position: relative;
    }

    .step:not(:last-child):after {
        content: '';
        position: absolute;
        top: 50%;
        right: 0;
        width: 100%;
        height: 2px;
        background: #dee2e6;
        z-index: 1;
    }

    .step.active:not(:last-child):after {
        background: #0d6efd;
    }

    .step-number {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #dee2e6;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
        position: relative;
        z-index: 2;
    }

    .step.active .step-number {
        background: #0d6efd;
    }

    .step-label {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .step.active .step-label {
        color: #0d6efd;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Create New Contract - Step 1</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Progress Steps -->
                        <div class="step-indicator">
                            <div class="step active">
                                <div class="step-number">1</div>
                                <div class="step-label">Basic Information</div>
                            </div>
                            <div class="step">
                                <div class="step-number">2</div>
                                <div class="step-label">Scope & Materials</div>
                            </div>
                            <div class="step">
                                <div class="step-number">3</div>
                                <div class="step-label">Terms & Conditions</div>
                            </div>
                            <div class="step">
                                <div class="step-number">4</div>
                                <div class="step-label">Payment & Review</div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('contracts.store.step1') }}" id="step1Form" novalidate>
                            @csrf

                            <!-- Contractor Information Section -->
                            <div class="section-container mb-4">
                                <h5 class="section-title">Contractor Information</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contractor_name">Name</label>
                                            <input type="text" class="form-control" id="contractor_name" name="contractor_name" value="{{ old('contractor_name', session('contract_step1.contractor_name')) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contractor_company">Company Name (Optional)</label>
                                            <input type="text" class="form-control" id="contractor_company" name="contractor_company" value="{{ old('contractor_company', session('contract_step1.contractor_company')) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contractor_email">Email</label>
                                            <input type="email" class="form-control" id="contractor_email" name="contractor_email" value="{{ old('contractor_email', session('contract_step1.contractor_email')) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contractor_phone">Phone Number</label>
                                            <input type="tel" class="form-control" id="contractor_phone" name="contractor_phone" value="{{ old('contractor_phone', session('contract_step1.contractor_phone')) }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="contractor_street">Street Address</label>
                                            <input type="text" class="form-control" id="contractor_street" name="contractor_street" value="{{ old('contractor_street', session('contract_step1.contractor_street')) }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="contractor_barangay">Barangay</label>
                                            <input type="text" class="form-control" id="contractor_barangay" name="contractor_barangay" value="{{ old('contractor_barangay', session('contract_step1.contractor_barangay')) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="contractor_city">City/Municipality</label>
                                            <input type="text" class="form-control" id="contractor_city" name="contractor_city" value="{{ old('contractor_city', session('contract_step1.contractor_city')) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="contractor_postal">Postal Code</label>
                                            <input type="text" class="form-control" id="contractor_postal" name="contractor_postal" value="{{ old('contractor_postal', session('contract_step1.contractor_postal')) }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="contractor_state">Province/State</label>
                                            <input type="text" class="form-control" id="contractor_state" name="contractor_state" value="{{ old('contractor_state', session('contract_step1.contractor_state')) }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Client Information Section -->
                            <div class="section-container mb-4">
                                <h5 class="section-title">Client Information</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="client_name">Name</label>
                                            <input type="text" class="form-control" id="client_name" name="client_name" value="{{ old('client_name', session('contract_step1.client_name')) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="client_company">Company Name (Optional)</label>
                                            <input type="text" class="form-control" id="client_company" name="client_company" value="{{ old('client_company', session('contract_step1.client_company')) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="client_email">Email</label>
                                            <input type="email" class="form-control" id="client_email" name="client_email" value="{{ old('client_email', session('contract_step1.client_email')) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="client_phone">Phone Number</label>
                                            <input type="tel" class="form-control" id="client_phone" name="client_phone" value="{{ old('client_phone', session('contract_step1.client_phone')) }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="client_street">Street Address</label>
                                            <input type="text" class="form-control" id="client_street" name="client_street" value="{{ old('client_street', session('contract_step1.client_street')) }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="client_barangay">Barangay</label>
                                            <input type="text" class="form-control" id="client_barangay" name="client_barangay" value="{{ old('client_barangay', session('contract_step1.client_barangay')) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="client_city">City/Municipality</label>
                                            <input type="text" class="form-control" id="client_city" name="client_city" value="{{ old('client_city', session('contract_step1.client_city')) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="client_postal">Postal Code</label>
                                            <input type="text" class="form-control" id="client_postal" name="client_postal" value="{{ old('client_postal', session('contract_step1.client_postal')) }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="client_state">Province/State</label>
                                            <input type="text" class="form-control" id="client_state" name="client_state" value="{{ old('client_state', session('contract_step1.client_state')) }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Property Information Section -->
                            <div class="section-container mb-4">
                                <h5 class="section-title">Property Information</h5>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="property_type">Property Type</label>
                                            <select class="form-control" id="property_type" name="property_type" required>
                                                <option value="">Select Type</option>
                                                <option value="residential" {{ old('property_type', session('contract_step1.property_type')) == 'residential' ? 'selected' : '' }}>Residential</option>
                                                <option value="commercial" {{ old('property_type', session('contract_step1.property_type')) == 'commercial' ? 'selected' : '' }}>Commercial</option>
                                                <option value="industrial" {{ old('property_type', session('contract_step1.property_type')) == 'industrial' ? 'selected' : '' }}>Industrial</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="property_street">Street Address</label>
                                            <input type="text" class="form-control" id="property_street" name="property_street" value="{{ old('property_street', session('contract_step1.property_street')) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="property_unit">Unit/Floor/Building (Optional)</label>
                                            <input type="text" class="form-control" id="property_unit" name="property_unit" value="{{ old('property_unit', session('contract_step1.property_unit')) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="property_barangay">Barangay</label>
                                            <input type="text" class="form-control" id="property_barangay" name="property_barangay" value="{{ old('property_barangay', session('contract_step1.property_barangay')) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="property_city">City/Municipality</label>
                                            <input type="text" class="form-control" id="property_city" name="property_city" value="{{ old('property_city', session('contract_step1.property_city')) }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="property_state">Province/State</label>
                                            <input type="text" class="form-control" id="property_state" name="property_state" value="{{ old('property_state', session('contract_step1.property_state')) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="property_postal">Postal/ZIP Code</label>
                                            <input type="text" class="form-control" id="property_postal" name="property_postal" value="{{ old('property_postal', session('contract_step1.property_postal')) }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Next Step</button>
                                <a href="{{ route('contracts.clear-session') }}" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel? All entered data will be lost.')">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('step1Form');
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>
@endpush 