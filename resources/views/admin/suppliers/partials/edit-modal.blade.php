<div class="modal fade" id="editSupplierModal" tabindex="-1" aria-labelledby="editSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSupplierModalLabel">Edit Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editSupplierForm" method="POST" action="{{ route('admin.suppliers.update', ['supplier' => ':id']) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic Information -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Basic Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_company_name" class="form-label">Company Name *</label>
                                    <input type="text" class="form-control" id="edit_company_name" name="company_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_business_type" class="form-label">Business Type *</label>
                                    <select class="form-select" id="edit_business_type" name="business_type" required>
                                        <option value="">Select Business Type</option>
                                        <option value="Sole Proprietorship">Sole Proprietorship</option>
                                        <option value="Partnership">Partnership</option>
                                        <option value="Corporation">Corporation</option>
                                        <option value="Cooperative">Cooperative</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_tax_id" class="form-label">Tax ID Number *</label>
                                    <input type="text" class="form-control" id="edit_tax_id" name="tax_id" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_years_in_business" class="form-label">Years in Business *</label>
                                    <input type="number" class="form-control" id="edit_years_in_business" name="years_in_business" min="0" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Details -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Contact Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_contact_person" class="form-label">Contact Person *</label>
                                    <input type="text" class="form-control" id="edit_contact_person" name="contact_person" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_contact_position" class="form-label">Position *</label>
                                    <input type="text" class="form-control" id="edit_contact_position" name="contact_position" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_phone" class="form-label">Phone Number *</label>
                                    <input type="tel" class="form-control" id="edit_phone" name="phone" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="edit_email" name="email" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="edit_address" class="form-label">Complete Address *</label>
                                <textarea class="form-control" id="edit_address" name="address" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Business Details -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Business Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Products/Services Offered *</label>
                                <div class="product-categories">
                                    @include('admin.suppliers.partials.product-categories')
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="edit_business_description" class="form-label">Business Description</label>
                                <textarea class="form-control" id="edit_business_description" name="business_description" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Terms & Banking -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Terms & Banking</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_payment_terms" class="form-label">Payment Terms *</label>
                                    <select class="form-select" id="edit_payment_terms" name="payment_terms" required>
                                        <option value="">Select Payment Terms</option>
                                        <option value="Cash on Delivery">Cash on Delivery</option>
                                        <option value="Net 15">Net 15</option>
                                        <option value="Net 30">Net 30</option>
                                        <option value="Net 60">Net 60</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_bank_name" class="form-label">Bank Name</label>
                                    <input type="text" class="form-control" id="edit_bank_name" name="bank_name">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_bank_account" class="form-label">Bank Account Number</label>
                                    <input type="text" class="form-control" id="edit_bank_account" name="bank_account">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_bank_branch" class="form-label">Bank Branch</label>
                                    <input type="text" class="form-control" id="edit_bank_branch" name="bank_branch">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Documents</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_business_permit" class="form-label">Business Permit</label>
                                    <input type="file" class="form-control" id="edit_business_permit" name="business_permit">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_tax_clearance" class="form-label">Tax Clearance</label>
                                    <input type="file" class="form-control" id="edit_tax_clearance" name="tax_clearance">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_insurance_certificate" class="form-label">Insurance Certificate</label>
                                    <input type="file" class="form-control" id="edit_insurance_certificate" name="insurance_certificate">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_other_documents" class="form-label">Other Documents</label>
                                    <input type="file" class="form-control" id="edit_other_documents" name="other_documents[]" multiple>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Agreement & Consent -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Agreement & Consent</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_terms_agreement" name="terms_agreement" required>
                                    <label class="form-check-label" for="edit_terms_agreement">
                                        I agree to the terms and conditions of supplier registration
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_data_consent" name="data_consent" required>
                                    <label class="form-check-label" for="edit_data_consent">
                                        I consent to the processing of my personal data
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editSupplierForm" class="btn btn-primary">Update Supplier</button>
            </div>
        </div>
    </div>
</div> 