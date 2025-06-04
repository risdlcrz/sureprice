<!-- Add Supplier Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="fas fa-building me-2"></i>Supplier Registration
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.suppliers.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-4">
                        <!-- Basic Information -->
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Basic Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                            <input type="text" id="company_name" name="company_name" class="form-control" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="supplier_type" class="form-label">Type of Supplier <span class="text-danger">*</span></label>
                                            <select id="supplier_type" name="supplier_type" class="form-select" required>
                                                <option value="">Select Type</option>
                                                <option value="Individual">Individual</option>
                                                <option value="Company">Company</option>
                                                <option value="Contractor">Contractor</option>
                                                <option value="Material Supplier">Material Supplier</option>
                                                <option value="Equipment Rental">Equipment Rental</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="business_reg_no" class="form-label">Business Registration Number</label>
                                            <input type="text" id="business_reg_no" name="business_reg_no" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Details -->
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-address-card me-2"></i>Contact Details
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="contact_person" class="form-label">Contact Person <span class="text-danger">*</span></label>
                                            <input type="text" id="contact_person" name="contact_person" class="form-control" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="designation" class="form-label">Designation</label>
                                            <input type="text" id="designation" name="designation" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" id="email" name="email" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="mobile_number" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                            <input type="text" id="mobile_number" name="mobile_number" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="telephone_number" class="form-label">Telephone Number</label>
                                            <input type="text" id="telephone_number" name="telephone_number" class="form-control">
                                        </div>
                                    </div>
                                    <hr class="my-4">
                                    <h6 class="mb-3">Address Information</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="street" class="form-label">Street Address</label>
                                            <input type="text" id="street" name="street" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="city" class="form-label">City/Municipality</label>
                                            <input type="text" id="city" name="city" class="form-control">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="province" class="form-label">Province</label>
                                            <input type="text" id="province" name="province" class="form-control">
                                        </div>
                                        <div class="col-md-1">
                                            <label for="zip_code" class="form-label">ZIP</label>
                                            <input type="text" id="zip_code" name="zip_code" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Business Details -->
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-briefcase me-2"></i>Business Details
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="years_operation" class="form-label">Years in Operation</label>
                                            <input type="number" id="years_operation" name="years_operation" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="business_size" class="form-label">Business Size</label>
                                            <select id="business_size" name="business_size" class="form-select">
                                                <option value="">Select Size</option>
                                                <option value="Solo">Solo</option>
                                                <option value="Small Enterprise">Small Enterprise</option>
                                                <option value="Medium">Medium</option>
                                                <option value="Large">Large</option>
                                            </select>
                                        </div>
                                    </div>
                                    <hr class="my-4">
                                    <div class="row">
                                        <div class="col-12">
                                            <label class="form-label">Products/Services Offered</label>
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        @include('admin.suppliers.partials.product-categories')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms & Banking -->
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-warning">
                                    <h6 class="mb-0">
                                        <i class="fas fa-file-invoice-dollar me-2"></i>Terms & Banking
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="payment_terms" class="form-label">Payment Terms</label>
                                            <select id="payment_terms" name="payment_terms" class="form-select">
                                                <option value="">Select Terms</option>
                                                <option value="7 days">7 days</option>
                                                <option value="15 days">15 days</option>
                                                <option value="30 days">30 days</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="vat_registered" class="form-label">VAT Registered</label>
                                            <select id="vat_registered" name="vat_registered" class="form-select">
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="use_sureprice" class="form-label">Use SurePrice</label>
                                            <select id="use_sureprice" name="use_sureprice" class="form-select">
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <hr class="my-4">
                                    <h6 class="mb-3">Banking Information</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="bank_name" class="form-label">Bank Name</label>
                                            <input type="text" id="bank_name" name="bank_name" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="account_name" class="form-label">Account Name</label>
                                            <input type="text" id="account_name" name="account_name" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="account_number" class="form-label">Account Number</label>
                                            <input type="text" id="account_number" name="account_number" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Documents -->
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-file-alt me-2"></i>Documents
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="dti_sec_registration" class="form-label">DTI/SEC Registration</label>
                                            <input type="file" id="dti_sec_registration" name="dti_sec_registration" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="mayors_permit" class="form-label">Mayor's Permit</label>
                                            <input type="file" id="mayors_permit" name="mayors_permit" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="valid_id" class="form-label">Valid ID</label>
                                            <input type="file" id="valid_id" name="valid_id" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="company_profile" class="form-label">Company Profile</label>
                                            <input type="file" id="company_profile" name="company_profile" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="price_list" class="form-label">Price List</label>
                                            <input type="file" id="price_list" name="price_list" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Agreement -->
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-dark text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-handshake me-2"></i>Agreement & Consent
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="agree_terms" name="agree_terms" required>
                                        <label class="form-check-label" for="agree_terms">
                                            I agree to the Terms and Conditions and Privacy Policy
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="agree_contact" name="agree_contact" required>
                                        <label class="form-check-label" for="agree_contact">
                                            I consent to be contacted for verification and partnership opportunities
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Register Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    .card-header {
        padding: 0.75rem 1.25rem;
    }
    .card-header h6 {
        font-size: 1rem;
    }
    .form-check-label {
        font-size: 0.9rem;
    }
    .required-field::after {
        content: " *";
        color: red;
    }
</style>
@endpush 