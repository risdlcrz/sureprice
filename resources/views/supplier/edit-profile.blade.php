@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Edit My Information</h1>
    @if(!$supplier)
        <div class="alert alert-danger">Supplier information not found. Please contact support.</div>
    @else
        <form method="POST" action="{{ route('supplier.profile.update') }}" enctype="multipart/form-data" class="p-4 rounded shadow bg-white" style="max-width: 800px; margin: 0 auto;">
            @csrf
            @method('PUT')
            <h2 class="mb-4">Edit My Information</h2>
            <hr>
            <h4 class="mt-4 mb-3">Account Information</h4>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $supplier->username) }}" required>
            </div>
            <h4 class="mt-4 mb-3">Basic Information</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="company_name" class="form-label">Company Name</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $supplier->company_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="supplier_type" class="form-label">Type of Company</label>
                    <select class="form-select" id="supplier_type" name="supplier_type" required>
                        <option value="">Select company type</option>
                        <option value="Individual" {{ old('supplier_type', $supplier->supplier_type) == 'Individual' ? 'selected' : '' }}>Individual</option>
                        <option value="Contractor" {{ old('supplier_type', $supplier->supplier_type) == 'Contractor' ? 'selected' : '' }}>Contractor</option>
                        <option value="Material Supplier" {{ old('supplier_type', $supplier->supplier_type) == 'Material Supplier' ? 'selected' : '' }}>Material Supplier</option>
                        <option value="Equipment Rental" {{ old('supplier_type', $supplier->supplier_type) == 'Equipment Rental' ? 'selected' : '' }}>Equipment Rental</option>
                        <option value="Other" {{ old('supplier_type', $supplier->supplier_type) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-6" id="other_supplier_type_group" style="{{ old('supplier_type', $supplier->supplier_type) == 'Other' ? '' : 'display:none;' }}">
                    <label for="other_supplier_type" class="form-label">Specify Type</label>
                    <input type="text" class="form-control" id="other_supplier_type" name="other_supplier_type" value="{{ old('other_supplier_type', $supplier->other_supplier_type) }}">
                </div>
                <div class="col-md-6">
                    <label for="designation" class="form-label">Company Role</label>
                    <select class="form-select" id="designation" name="designation" required>
                        <option value="">Select company role</option>
                        <option value="client" {{ old('designation', $supplier->designation) == 'client' ? 'selected' : '' }}>Client</option>
                        <option value="supplier" {{ old('designation', $supplier->designation) == 'supplier' ? 'selected' : '' }}>Supplier</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="business_reg_no" class="form-label">Business Registration Number</label>
                    <input type="text" class="form-control" id="business_reg_no" name="business_reg_no" value="{{ old('business_reg_no', $supplier->business_reg_no) }}">
                </div>
            </div>
            <h4 class="mt-4 mb-3">Contact Details</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="contact_person" class="form-label">Contact Person</label>
                    <input type="text" class="form-control" id="contact_person" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $supplier->email) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="mobile_number" class="form-label">Mobile Number</label>
                    <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $supplier->mobile_number) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="telephone_number" class="form-label">Telephone Number</label>
                    <input type="text" class="form-control" id="telephone_number" name="telephone_number" value="{{ old('telephone_number', $supplier->telephone_number) }}">
                </div>
                <div class="col-md-6">
                    <label for="street" class="form-label">Street Address</label>
                    <input type="text" class="form-control" id="street" name="street" value="{{ old('street', $supplier->street) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="barangay" class="form-label">Barangay</label>
                    <input type="text" class="form-control" id="barangay" name="barangay" value="{{ old('barangay', $supplier->barangay) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="city" class="form-label">City/Municipality</label>
                    <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $supplier->city) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="state" class="form-label">Province</label>
                    <input type="text" class="form-control" id="state" name="state" value="{{ old('state', $supplier->state) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="postal" class="form-label">ZIP Code</label>
                    <input type="text" class="form-control" id="postal" name="postal" value="{{ old('postal', $supplier->postal) }}" required>
                </div>
            </div>
            <h4 class="mt-4 mb-3">Business Details</h4>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="years_operation" class="form-label">Years in Operation</label>
                    <input type="number" class="form-control" id="years_operation" name="years_operation" value="{{ old('years_operation', $supplier->years_operation) }}" min="0">
                </div>
                <div class="col-md-4">
                    <label for="business_size" class="form-label">Business Size</label>
                    <select class="form-select" id="business_size" name="business_size">
                        <option value="">Select business size</option>
                        <option value="Solo" {{ old('business_size', $supplier->business_size) == 'Solo' ? 'selected' : '' }}>Solo</option>
                        <option value="Small Enterprise" {{ old('business_size', $supplier->business_size) == 'Small Enterprise' ? 'selected' : '' }}>Small Enterprise</option>
                        <option value="Medium" {{ old('business_size', $supplier->business_size) == 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="Large" {{ old('business_size', $supplier->business_size) == 'Large' ? 'selected' : '' }}>Large</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="payment_terms" class="form-label">Payment Terms</label>
                    <select class="form-select" id="payment_terms" name="payment_terms">
                        <option value="">Select preferred terms</option>
                        <option value="7 days" {{ old('payment_terms', $supplier->payment_terms) == '7 days' ? 'selected' : '' }}>7 days</option>
                        <option value="15 days" {{ old('payment_terms', $supplier->payment_terms) == '15 days' ? 'selected' : '' }}>15 days</option>
                        <option value="30 days" {{ old('payment_terms', $supplier->payment_terms) == '30 days' ? 'selected' : '' }}>30 days</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="vat_registered" class="form-label">VAT Registered?</label>
                    <select class="form-select" id="vat_registered" name="vat_registered" required>
                        <option value="">-- Select --</option>
                        <option value="1" {{ old('vat_registered', $supplier->vat_registered) == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('vat_registered', $supplier->vat_registered) == '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="use_sureprice" class="form-label">Use SurePrice?</label>
                    <select class="form-select" id="use_sureprice" name="use_sureprice" required>
                        <option value="">-- Select --</option>
                        <option value="1" {{ old('use_sureprice', $supplier->use_sureprice) == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('use_sureprice', $supplier->use_sureprice) == '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div class="col-12">
                    <label for="primary_products_services" class="form-label">Primary Products/Services</label>
                    <textarea class="form-control" id="primary_products_services" name="primary_products_services" rows="2">{{ old('primary_products_services', $supplier->primary_products_services) }}</textarea>
                </div>
                <div class="col-12">
                    <label for="service_areas" class="form-label">Areas of Operation</label>
                    <textarea class="form-control" id="service_areas" name="service_areas" rows="2">{{ old('service_areas', $supplier->service_areas) }}</textarea>
                </div>
            </div>
            <h4 class="mt-4 mb-3">Bank Details (Optional)</h4>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="bank_name" class="form-label">Bank Name</label>
                    <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name', $bankDetails->bank_name ?? $supplier->bank_name ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label for="bank_account_name" class="form-label">Account Name</label>
                    <input type="text" class="form-control" id="bank_account_name" name="bank_account_name" value="{{ old('bank_account_name', $bankDetails->account_name ?? $supplier->bank_account_name ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label for="bank_account_number" class="form-label">Account Number</label>
                    <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', $bankDetails->account_number ?? $supplier->bank_account_number ?? '') }}">
                </div>
            </div>
            <h4 class="mt-4 mb-3">Documents</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="dti_sec_registration" class="form-label">DTI/SEC Registration</label>
                    <input type="file" class="form-control" id="dti_sec_registration" name="dti_sec_registration">
                    @if(isset($documents['DTI_SEC_REGISTRATION']))
                        <small class="text-success">Current: <a href="{{ asset('storage/' . $documents['DTI_SEC_REGISTRATION']->path) }}" target="_blank">{{ $documents['DTI_SEC_REGISTRATION']->original_name }}</a></small>
                    @endif
                </div>
                <div class="col-md-6">
                    <label for="business_permit_mayor_permit" class="form-label">Business Permit/Mayor's Permit</label>
                    <input type="file" class="form-control" id="business_permit_mayor_permit" name="business_permit_mayor_permit">
                    @if(isset($documents['BUSINESS_PERMIT_MAYOR_PERMIT']))
                        <small class="text-success">Current: <a href="{{ asset('storage/' . $documents['BUSINESS_PERMIT_MAYOR_PERMIT']->path) }}" target="_blank">{{ $documents['BUSINESS_PERMIT_MAYOR_PERMIT']->original_name }}</a></small>
                    @endif
                </div>
                <div class="col-md-6">
                    <label for="valid_id_owner_rep" class="form-label">Valid ID (Owner/Rep)</label>
                    <input type="file" class="form-control" id="valid_id_owner_rep" name="valid_id_owner_rep">
                    @if(isset($documents['VALID_ID_OWNER_REP']))
                        <small class="text-success">Current: <a href="{{ asset('storage/' . $documents['VALID_ID_OWNER_REP']->path) }}" target="_blank">{{ $documents['VALID_ID_OWNER_REP']->original_name }}</a></small>
                    @endif
                </div>
                <div class="col-md-6">
                    <label for="accreditations_certifications" class="form-label">Accreditations/Certifications</label>
                    <input type="file" class="form-control" id="accreditations_certifications" name="accreditations_certifications">
                    @if(isset($documents['ACCREDITATIONS_CERTIFICATIONS']))
                        <small class="text-success">Current: <a href="{{ asset('storage/' . $documents['ACCREDITATIONS_CERTIFICATIONS']->path) }}" target="_blank">{{ $documents['ACCREDITATIONS_CERTIFICATIONS']->original_name }}</a></small>
                    @endif
                </div>
                <div class="col-md-6">
                    <label for="company_profile_portfolio" class="form-label">Company Profile/Portfolio</label>
                    <input type="file" class="form-control" id="company_profile_portfolio" name="company_profile_portfolio">
                    @if(isset($documents['COMPANY_PROFILE_PORTFOLIO']))
                        <small class="text-success">Current: <a href="{{ asset('storage/' . $documents['COMPANY_PROFILE_PORTFOLIO']->path) }}" target="_blank">{{ $documents['COMPANY_PROFILE_PORTFOLIO']->original_name }}</a></small>
                    @endif
                </div>
                <div class="col-md-6">
                    <label for="sample_price_list" class="form-label">Sample Price List</label>
                    <input type="file" class="form-control" id="sample_price_list" name="sample_price_list">
                    @if(isset($documents['SAMPLE_PRICE_LIST']))
                        <small class="text-success">Current: <a href="{{ asset('storage/' . $documents['SAMPLE_PRICE_LIST']->path) }}" target="_blank">{{ $documents['SAMPLE_PRICE_LIST']->original_name }}</a></small>
                    @endif
                </div>
            </div>
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary px-4">Submit for Approval</button>
                <a href="{{ route('supplier.dashboard') }}" class="btn btn-secondary ms-2">Cancel</a>
            </div>
        </form>
    @endif
</div>
@endsection 