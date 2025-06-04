<!-- View Supplier Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Supplier Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Basic Information -->
                <div class="info-section">
                    <h3>Basic Information</h3>
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Company Name:</strong> <span id="view_company"></span></p>
                            <p><strong>Type:</strong> <span id="view_supplier_type"></span></p>
                            <p><strong>Business Registration:</strong> <span id="view_business_reg_no"></span></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Contact Person:</strong> <span id="view_contact_person"></span></p>
                            <p><strong>Designation:</strong> <span id="view_designation"></span></p>
                            <p><strong>Email:</strong> <span id="view_email"></span></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Mobile:</strong> <span id="view_mobile_number"></span></p>
                            <p><strong>Telephone:</strong> <span id="view_telephone_number"></span></p>
                            <p><strong>Address:</strong> <span id="view_address"></span></p>
                        </div>
                    </div>
                </div>

                <!-- Business Details -->
                <div class="info-section">
                    <h3>Business Details</h3>
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Years in Operation:</strong> <span id="view_years_operation"></span></p>
                            <p><strong>Business Size:</strong> <span id="view_business_size"></span></p>
                        </div>
                        <div class="col-md-8">
                            <p><strong>Materials/Services:</strong></p>
                            <div id="view_materials" class="materials-list"></div>
                        </div>
                    </div>
                </div>

                <!-- Terms & Banking -->
                <div class="info-section">
                    <h3>Terms & Banking</h3>
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Payment Terms:</strong> <span id="view_payment_terms"></span></p>
                            <p><strong>VAT Registered:</strong> <span id="view_vat_registered"></span></p>
                            <p><strong>Use SurePrice:</strong> <span id="view_use_sureprice"></span></p>
                        </div>
                        <div class="col-md-8">
                            <p><strong>Bank Details:</strong></p>
                            <p>Bank: <span id="view_bank_name"></span></p>
                            <p>Account Name: <span id="view_account_name"></span></p>
                            <p>Account Number: <span id="view_account_number"></span></p>
                        </div>
                    </div>
                </div>

                <!-- Documents -->
                <div class="info-section">
                    <h3>Documents</h3>
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>DTI/SEC Registration:</strong></p>
                            <a id="view_dti_sec_registration_link" href="#" target="_blank" class="btn btn-sm btn-primary">View Document</a>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Accreditation Documents:</strong></p>
                            <a id="view_accreditation_docs_link" href="#" target="_blank" class="btn btn-sm btn-primary">View Document</a>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Mayor's Permit:</strong></p>
                            <a id="view_mayors_permit_link" href="#" target="_blank" class="btn btn-sm btn-primary">View Document</a>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <p><strong>Valid ID:</strong></p>
                            <a id="view_valid_id_link" href="#" target="_blank" class="btn btn-sm btn-primary">View Document</a>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Company Profile:</strong></p>
                            <a id="view_company_profile_link" href="#" target="_blank" class="btn btn-sm btn-primary">View Document</a>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Price List:</strong></p>
                            <a id="view_price_list_link" href="#" target="_blank" class="btn btn-sm btn-primary">View Document</a>
                        </div>
                    </div>
                </div>

                <!-- Evaluation History -->
                <div class="info-section">
                    <h3>Evaluation History</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Engagement</th>
                                    <th>Delivery Speed</th>
                                    <th>Performance</th>
                                    <th>Quality</th>
                                    <th>Cost Variance</th>
                                    <th>Sustainability</th>
                                    <th>Final Score</th>
                                </tr>
                            </thead>
                            <tbody id="view_evaluation_history">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="edit_supplier_btn">Edit Supplier</button>
            </div>
        </div>
    </div>
</div> 