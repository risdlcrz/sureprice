<!-- Add Supplier Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Supplier Registration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('supplier-rankings.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h3>Basic Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="company">Company Name</label>
                                <input type="text" id="company" name="company" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="supplier_type">Type of Supplier</label>
                                <select id="supplier_type" name="supplier_type" class="form-control" required>
                                    <option value="">Select Type</option>
                                    <option value="Individual">Individual</option>
                                    <option value="Company">Company</option>
                                    <option value="Contractor">Contractor</option>
                                    <option value="Material Supplier">Material Supplier</option>
                                    <option value="Equipment Rental">Equipment Rental</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="business_reg_no">Business Registration Number</label>
                                <input type="text" id="business_reg_no" name="business_reg_no" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Contact Details -->
                    <div class="form-section">
                        <h3>Contact Details</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="contact_person">Contact Person</label>
                                <input type="text" id="contact_person" name="contact_person" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="designation">Designation</label>
                                <input type="text" id="designation" name="designation" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="mobile_number">Mobile Number</label>
                                <input type="text" id="mobile_number" name="mobile_number" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="telephone_number">Telephone Number</label>
                                <input type="text" id="telephone_number" name="telephone_number" class="form-control">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea id="address" name="address" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Business Details -->
                    <div class="form-section">
                        <h3>Business Details</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="years_operation">Years in Operation</label>
                                <input type="number" id="years_operation" name="years_operation" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="business_size">Business Size</label>
                                <select id="business_size" name="business_size" class="form-control">
                                    <option value="">Select Size</option>
                                    <option value="Solo">Solo</option>
                                    <option value="Small Enterprise">Small Enterprise</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Large">Large</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Materials/Services Offered</label>
                            <div class="checkbox-group">
                                @include('admin.supplier-rankings.partials.materials-checkboxes')
                            </div>
                        </div>
                    </div>

                    <!-- Terms & Banking -->
                    <div class="form-section">
                        <h3>Terms & Banking</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="payment_terms">Payment Terms</label>
                                <select id="payment_terms" name="payment_terms" class="form-control">
                                    <option value="">Select Terms</option>
                                    <option value="7 days">7 days</option>
                                    <option value="15 days">15 days</option>
                                    <option value="30 days">30 days</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="vat_registered">VAT Registered</label>
                                <select id="vat_registered" name="vat_registered" class="form-control">
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="use_sureprice">Use SurePrice</label>
                                <select id="use_sureprice" name="use_sureprice" class="form-control">
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="bank_name">Bank Name</label>
                                <input type="text" id="bank_name" name="bank_name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="account_name">Account Name</label>
                                <input type="text" id="account_name" name="account_name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="account_number">Account Number</label>
                                <input type="text" id="account_number" name="account_number" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="form-section">
                        <h3>Documents</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="dti_sec_registration">DTI/SEC Registration</label>
                                <input type="file" id="dti_sec_registration" name="dti_sec_registration" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="accreditation_docs">Accreditation Documents</label>
                                <input type="file" id="accreditation_docs" name="accreditation_docs" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="mayors_permit">Mayor's Permit</label>
                                <input type="file" id="mayors_permit" name="mayors_permit" class="form-control">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="valid_id">Valid ID</label>
                                <input type="file" id="valid_id" name="valid_id" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="company_profile">Company Profile</label>
                                <input type="file" id="company_profile" name="company_profile" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="price_list">Price List</label>
                                <input type="file" id="price_list" name="price_list" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Register Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div> 