<div class="modal fade" id="viewSupplierModal" tabindex="-1" aria-labelledby="viewSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewSupplierModalLabel">Supplier Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Basic Information -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Basic Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Company Name</label>
                                <p id="view_company_name"></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Business Type</label>
                                <p id="view_business_type"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tax ID Number</label>
                                <p id="view_tax_id"></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Years in Business</label>
                                <p id="view_years_in_business"></p>
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
                                <label class="form-label fw-bold">Contact Person</label>
                                <p id="view_contact_person"></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Position</label>
                                <p id="view_contact_position"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Phone Number</label>
                                <p id="view_phone"></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email Address</label>
                                <p id="view_email"></p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Complete Address</label>
                            <p id="view_address"></p>
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
                            <label class="form-label fw-bold">Products/Services Offered</label>
                            <div id="view_products" class="d-flex flex-wrap gap-2"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Business Description</label>
                            <p id="view_business_description"></p>
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
                                <label class="form-label fw-bold">Payment Terms</label>
                                <p id="view_payment_terms"></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Bank Name</label>
                                <p id="view_bank_name"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Bank Account Number</label>
                                <p id="view_bank_account"></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Bank Branch</label>
                                <p id="view_bank_branch"></p>
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
                                <label class="form-label fw-bold">Business Permit</label>
                                <div id="view_business_permit"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tax Clearance</label>
                                <div id="view_tax_clearance"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Insurance Certificate</label>
                                <div id="view_insurance_certificate"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Other Documents</label>
                                <div id="view_other_documents"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Evaluation Metrics -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Evaluation Metrics</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Quality Score</label>
                                <p id="view_quality_score"></p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Delivery Score</label>
                                <p id="view_delivery_score"></p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Price Score</label>
                                <p id="view_price_score"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Communication Score</label>
                                <p id="view_communication_score"></p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Overall Rating</label>
                                <p id="view_overall_rating"></p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Last Evaluation Date</label>
                                <p id="view_last_evaluation_date"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> 