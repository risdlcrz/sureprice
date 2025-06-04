@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1>Edit Supplier</h1>
    <form action="{{ route('supplier-rankings.update', $supplier->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="modal-body">
            <!-- Basic Information -->
            <div class="form-section mb-4">
                <h4 class="section-title">Basic Information</h4>
                <hr>
                <div class="mb-3">
                    <input type="text" name="company" class="form-control" placeholder="Supplier Name / Company Name" value="{{ old('company', $supplier->company) }}" required>
                </div>
                <div class="mb-3">
                    <select name="supplier_type" class="form-select" required>
                        <option value="">Type of Supplier</option>
                        <option value="Individual" {{ old('supplier_type', $supplier->supplier_type) == 'Individual' ? 'selected' : '' }}>Individual</option>
                        <option value="Company" {{ old('supplier_type', $supplier->supplier_type) == 'Company' ? 'selected' : '' }}>Company</option>
                        <option value="Contractor" {{ old('supplier_type', $supplier->supplier_type) == 'Contractor' ? 'selected' : '' }}>Contractor</option>
                        <option value="Material Supplier" {{ old('supplier_type', $supplier->supplier_type) == 'Material Supplier' ? 'selected' : '' }}>Material Supplier</option>
                        <option value="Equipment Rental" {{ old('supplier_type', $supplier->supplier_type) == 'Equipment Rental' ? 'selected' : '' }}>Equipment Rental</option>
                    </select>
                </div>
                <div class="mb-3">
                    <input type="text" name="business_reg_no" class="form-control" placeholder="Business Registration Number (if applicable)" value="{{ old('business_reg_no', $supplier->business_reg_no) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">DTI/SEC Registration (Upload)</label>
                    <input type="file" name="dti_sec_registration" class="form-control">
                    @if($supplier->dti_sec_registration_path)
                        <a href="/storage/{{ $supplier->dti_sec_registration_path }}" target="_blank">View Current</a>
                    @endif
                </div>
            </div>

            <!-- Contact Details -->
            <div class="form-section mb-4">
                <h4 class="section-title">Contact Details</h4>
                <hr>
                <div class="mb-3">
                    <input type="text" name="contact_person" class="form-control" placeholder="Contact Person Name" value="{{ old('contact_person', $supplier->contact_person) }}" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="designation" class="form-control" placeholder="Designation / Role" value="{{ old('designation', $supplier->designation) }}">
                </div>
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email Address" value="{{ old('email', $supplier->email) }}" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="mobile_number" class="form-control" placeholder="Mobile Number" value="{{ old('mobile_number', $supplier->mobile_number) }}" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="telephone_number" class="form-control" placeholder="Telephone Number (optional)" value="{{ old('telephone_number', $supplier->telephone_number) }}">
                </div>
                <div class="mb-3">
                    <input type="text" name="street" class="form-control" placeholder="Street" value="{{ old('street', $supplier->street) }}">
                </div>
                <div class="mb-3">
                    <input type="text" name="city" class="form-control" placeholder="City/Municipality" value="{{ old('city', $supplier->city) }}">
                </div>
                <div class="mb-3">
                    <input type="text" name="province" class="form-control" placeholder="Province" value="{{ old('province', $supplier->province) }}">
                </div>
                <div class="mb-3">
                    <input type="text" name="zip_code" class="form-control" placeholder="ZIP Code" value="{{ old('zip_code', $supplier->zip_code) }}">
                </div>
            </div>

            <!-- Primary Products and Services -->
            <div class="form-section mb-4">
                <h4 class="section-title">Primary Products/Services Offered</h4>
                <hr>
                @php
                    $selectedMaterials = $supplier->materials->pluck('material_name')->toArray();
                @endphp
                <div class="category-box mb-3">
                    <div><strong class="text-success">Building Materials</strong></div>
                    <div class="d-flex flex-wrap gap-3 mb-2">
                        <div><input type="checkbox" name="materials[]" value="Cement" {{ in_array('Cement', $selectedMaterials) ? 'checked' : '' }}> Cement</div>
                        <div><input type="checkbox" name="materials[]" value="Steel Bars" {{ in_array('Steel Bars', $selectedMaterials) ? 'checked' : '' }}> Steel Bars</div>
                        <div><input type="checkbox" name="materials[]" value="Gravel" {{ in_array('Gravel', $selectedMaterials) ? 'checked' : '' }}> Gravel</div>
                        <div><input type="checkbox" name="materials[]" value="Sand" {{ in_array('Sand', $selectedMaterials) ? 'checked' : '' }}> Sand</div>
                        <div><input type="checkbox" name="materials[]" value="Hollow Blocks" {{ in_array('Hollow Blocks', $selectedMaterials) ? 'checked' : '' }}> Hollow Blocks</div>
                        <div><input type="checkbox" name="materials[]" value="Wood/Lumber" {{ in_array('Wood/Lumber', $selectedMaterials) ? 'checked' : '' }}> Wood/Lumber</div>
                        <div><input type="checkbox" name="materials[]" value="Plywood" {{ in_array('Plywood', $selectedMaterials) ? 'checked' : '' }}> Plywood</div>
                        <div><input type="checkbox" name="materials[]" value="Metal Roofing" {{ in_array('Metal Roofing', $selectedMaterials) ? 'checked' : '' }}> Metal Roofing</div>
                    </div>
                </div>
                <div class="category-box mb-3">
                    <div><strong class="text-success">Finishing Materials</strong></div>
                    <div class="d-flex flex-wrap gap-3 mb-2">
                        <div><input type="checkbox" name="materials[]" value="Paint" {{ in_array('Paint', $selectedMaterials) ? 'checked' : '' }}> Paint</div>
                        <div><input type="checkbox" name="materials[]" value="Tiles" {{ in_array('Tiles', $selectedMaterials) ? 'checked' : '' }}> Tiles</div>
                        <div><input type="checkbox" name="materials[]" value="Glass" {{ in_array('Glass', $selectedMaterials) ? 'checked' : '' }}> Glass</div>
                        <div><input type="checkbox" name="materials[]" value="Aluminum" {{ in_array('Aluminum', $selectedMaterials) ? 'checked' : '' }}> Aluminum</div>
                        <div><input type="checkbox" name="materials[]" value="Ceiling Boards" {{ in_array('Ceiling Boards', $selectedMaterials) ? 'checked' : '' }}> Ceiling Boards</div>
                        <div><input type="checkbox" name="materials[]" value="Wallpaper" {{ in_array('Wallpaper', $selectedMaterials) ? 'checked' : '' }}> Wallpaper</div>
                        <div><input type="checkbox" name="materials[]" value="Vinyl Flooring" {{ in_array('Vinyl Flooring', $selectedMaterials) ? 'checked' : '' }}> Vinyl Flooring</div>
                        <div><input type="checkbox" name="materials[]" value="Carpet" {{ in_array('Carpet', $selectedMaterials) ? 'checked' : '' }}> Carpet</div>
                    </div>
                </div>
                <div class="category-box mb-3">
                    <div><strong class="text-success">Plumbing & Electrical</strong></div>
                    <div class="d-flex flex-wrap gap-3 mb-2">
                        <div><input type="checkbox" name="materials[]" value="PVC Pipes" {{ in_array('PVC Pipes', $selectedMaterials) ? 'checked' : '' }}> PVC Pipes</div>
                        <div><input type="checkbox" name="materials[]" value="Electrical Wires" {{ in_array('Electrical Wires', $selectedMaterials) ? 'checked' : '' }}> Electrical Wires</div>
                        <div><input type="checkbox" name="materials[]" value="Lighting Fixtures" {{ in_array('Lighting Fixtures', $selectedMaterials) ? 'checked' : '' }}> Lighting Fixtures</div>
                        <div><input type="checkbox" name="materials[]" value="Plumbing Fixtures" {{ in_array('Plumbing Fixtures', $selectedMaterials) ? 'checked' : '' }}> Plumbing Fixtures</div>
                        <div><input type="checkbox" name="materials[]" value="Sanitary Wares" {{ in_array('Sanitary Wares', $selectedMaterials) ? 'checked' : '' }}> Sanitary Wares</div>
                        <div><input type="checkbox" name="materials[]" value="HVAC Systems" {{ in_array('HVAC Systems', $selectedMaterials) ? 'checked' : '' }}> HVAC Systems</div>
                    </div>
                </div>
                <div class="category-box mb-3">
                    <div><strong class="text-success">Hardware & Tools</strong></div>
                    <div class="d-flex flex-wrap gap-3 mb-2">
                        <div><input type="checkbox" name="materials[]" value="Hand Tools" {{ in_array('Hand Tools', $selectedMaterials) ? 'checked' : '' }}> Hand Tools</div>
                        <div><input type="checkbox" name="materials[]" value="Power Tools" {{ in_array('Power Tools', $selectedMaterials) ? 'checked' : '' }}> Power Tools</div>
                        <div><input type="checkbox" name="materials[]" value="Safety Equipment" {{ in_array('Safety Equipment', $selectedMaterials) ? 'checked' : '' }}> Safety Equipment</div>
                        <div><input type="checkbox" name="materials[]" value="Hardware Supplies" {{ in_array('Hardware Supplies', $selectedMaterials) ? 'checked' : '' }}> Hardware Supplies</div>
                        <div><input type="checkbox" name="materials[]" value="Construction Equipment" {{ in_array('Construction Equipment', $selectedMaterials) ? 'checked' : '' }}> Construction Equipment</div>
                    </div>
                </div>
                <div class="category-box mb-3">
                    <div><strong class="text-success">Services</strong></div>
                    <div class="d-flex flex-wrap gap-3 mb-2">
                        <div><input type="checkbox" name="materials[]" value="Scaffolding Rental" {{ in_array('Scaffolding Rental', $selectedMaterials) ? 'checked' : '' }}> Scaffolding Rental</div>
                        <div><input type="checkbox" name="materials[]" value="Equipment Rental" {{ in_array('Equipment Rental', $selectedMaterials) ? 'checked' : '' }}> Equipment Rental</div>
                        <div><input type="checkbox" name="materials[]" value="Skilled Labor" {{ in_array('Skilled Labor', $selectedMaterials) ? 'checked' : '' }}> Skilled Labor</div>
                        <div><input type="checkbox" name="materials[]" value="Interior Design" {{ in_array('Interior Design', $selectedMaterials) ? 'checked' : '' }}> Interior Design</div>
                        <div><input type="checkbox" name="materials[]" value="Architectural Services" {{ in_array('Architectural Services', $selectedMaterials) ? 'checked' : '' }}> Architectural Services</div>
                        <div><input type="checkbox" name="materials[]" value="Engineering Services" {{ in_array('Engineering Services', $selectedMaterials) ? 'checked' : '' }}> Engineering Services</div>
                        <div><input type="checkbox" name="materials[]" value="Project Management" {{ in_array('Project Management', $selectedMaterials) ? 'checked' : '' }}> Project Management</div>
                    </div>
                </div>
                <div class="category-box mb-3">
                    <div><strong class="text-success">Specialty Items</strong></div>
                    <div class="d-flex flex-wrap gap-3 mb-2">
                        <div><input type="checkbox" name="materials[]" value="Doors & Windows" {{ in_array('Doors & Windows', $selectedMaterials) ? 'checked' : '' }}> Doors & Windows</div>
                        <div><input type="checkbox" name="materials[]" value="Kitchen Cabinets" {{ in_array('Kitchen Cabinets', $selectedMaterials) ? 'checked' : '' }}> Kitchen Cabinets</div>
                        <div><input type="checkbox" name="materials[]" value="Countertops" {{ in_array('Countertops', $selectedMaterials) ? 'checked' : '' }}> Countertops</div>
                        <div><input type="checkbox" name="materials[]" value="Built-in Furniture" {{ in_array('Built-in Furniture', $selectedMaterials) ? 'checked' : '' }}> Built-in Furniture</div>
                        <div><input type="checkbox" name="materials[]" value="Acoustic Panels" {{ in_array('Acoustic Panels', $selectedMaterials) ? 'checked' : '' }}> Acoustic Panels</div>
                        <div><input type="checkbox" name="materials[]" value="Fire Protection" {{ in_array('Fire Protection', $selectedMaterials) ? 'checked' : '' }}> Fire Protection</div>
                        <div><input type="checkbox" name="materials[]" value="Security Systems" {{ in_array('Security Systems', $selectedMaterials) ? 'checked' : '' }}> Security Systems</div>
                    </div>
                </div>
            </div>

            <!-- Terms & Banking -->
            <div class="form-section mb-4">
                <h4 class="section-title">Terms & Banking</h4>
                <hr>
                <div class="mb-3">
                    <select name="payment_terms" class="form-select">
                        <option value="">Preferred Payment Terms</option>
                        <option value="7 days" {{ old('payment_terms', $supplier->payment_terms) == '7 days' ? 'selected' : '' }}>7 days</option>
                        <option value="15 days" {{ old('payment_terms', $supplier->payment_terms) == '15 days' ? 'selected' : '' }}>15 days</option>
                        <option value="30 days" {{ old('payment_terms', $supplier->payment_terms) == '30 days' ? 'selected' : '' }}>30 days</option>
                    </select>
                </div>
                <div class="mb-3">
                    <select name="vat_registered" class="form-select">
                        <option value="Yes" {{ old('vat_registered', $supplier->vat_registered) ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ !old('vat_registered', $supplier->vat_registered) ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div class="mb-3">
                    <select name="use_sureprice" class="form-select">
                        <option value="Yes" {{ old('use_sureprice', $supplier->use_sureprice) ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ !old('use_sureprice', $supplier->use_sureprice) ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>

            <!-- Upload Documents -->
            <div class="form-section mb-4">
                <h4 class="section-title">Upload Documents</h4>
                <hr>
                <div class="mb-3">
                    <label class="form-label">Business Permit / Mayor's Permit</label>
                    <input type="file" name="mayors_permit" class="form-control">
                    @if($supplier->mayors_permit_path)
                        <a href="/storage/{{ $supplier->mayors_permit_path }}" target="_blank">View Current</a>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="form-label">Valid ID (Owner or Rep)</label>
                    <input type="file" name="valid_id" class="form-control">
                    @if($supplier->valid_id_path)
                        <a href="/storage/{{ $supplier->valid_id_path }}" target="_blank">View Current</a>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="form-label">Company Profile / Portfolio (Optional)</label>
                    <input type="file" name="company_profile" class="form-control">
                    @if($supplier->company_profile_path)
                        <a href="/storage/{{ $supplier->company_profile_path }}" target="_blank">View Current</a>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="form-label">Sample Price List (Optional)</label>
                    <input type="file" name="price_list" class="form-control">
                    @if($supplier->price_list_path)
                        <a href="/storage/{{ $supplier->price_list_path }}" target="_blank">View Current</a>
                    @endif
                </div>
            </div>

            <!-- Bank & Payout Details -->
            <div class="form-section mb-4">
                <h4 class="section-title">Bank & Payout Details (Optional)</h4>
                <hr>
                <div class="mb-3">
                    <input type="text" name="bank_name" class="form-control" placeholder="Bank Name" value="{{ old('bank_name', $supplier->bank_name) }}">
                </div>
                <div class="mb-3">
                    <input type="text" name="account_name" class="form-control" placeholder="Account Name" value="{{ old('account_name', $supplier->account_name) }}">
                </div>
                <div class="mb-3">
                    <input type="text" name="account_number" class="form-control" placeholder="Account Number" value="{{ old('account_number', $supplier->account_number) }}">
                </div>
            </div>

            <!-- Agreement & Consent -->
            <div class="form-section mb-4">
                <h4 class="section-title">Agreement & Consent</h4>
                <hr>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="agree_terms" id="agree_terms">
                    <label class="form-check-label" for="agree_terms">
                        I agree to the Terms and Conditions and Privacy Policy
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="consent_contact" id="consent_contact">
                    <label class="form-check-label" for="consent_contact">
                        I consent to be contacted for verification and partnership opportunities
                    </label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="{{ route('supplier-rankings.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Supplier</button>
        </div>
    </form>
</div>
@endsection 