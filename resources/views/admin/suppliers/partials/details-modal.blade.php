<!-- Supplier Details Modal -->
<div class="modal fade" id="supplierDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Supplier Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Basic Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Company Name:</th>
                                <td id="detail-company">N/A</td>
                            </tr>
                            <tr>
                                <th>Contact Person:</th>
                                <td id="detail-contact-person">N/A</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td id="detail-email">N/A</td>
                            </tr>
                            <tr>
                                <th>Mobile Number:</th>
                                <td id="detail-mobile">N/A</td>
                            </tr>
                            <tr>
                                <th>Telephone Number:</th>
                                <td id="detail-telephone">N/A</td>
                            </tr>
                            <tr>
                                <th>Address:</th>
                                <td id="detail-address">N/A</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Business Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Business Registration:</th>
                                <td id="detail-business-reg">N/A</td>
                            </tr>
                            <tr>
                                <th>Supplier Type:</th>
                                <td id="detail-supplier-type">N/A</td>
                            </tr>
                            <tr>
                                <th>Business Size:</th>
                                <td id="detail-business-size">N/A</td>
                            </tr>
                            <tr>
                                <th>Years in Operation:</th>
                                <td id="detail-years-operation">N/A</td>
                            </tr>
                            <tr>
                                <th>Payment Terms:</th>
                                <td id="detail-payment-terms">N/A</td>
                            </tr>
                            <tr>
                                <th>VAT Registered:</th>
                                <td id="detail-vat-registered">N/A</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="font-weight-bold">Performance Metrics</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Final Score:</th>
                                <td id="detail-final-score">N/A</td>
                            </tr>
                            <tr>
                                <th>Engagement Score:</th>
                                <td id="detail-engagement">N/A</td>
                            </tr>
                            <tr>
                                <th>Delivery Speed Score:</th>
                                <td id="detail-delivery-speed">N/A</td>
                            </tr>
                            <tr>
                                <th>Performance Score:</th>
                                <td id="detail-performance">N/A</td>
                            </tr>
                            <tr>
                                <th>Quality Score:</th>
                                <td id="detail-quality">N/A</td>
                            </tr>
                            <tr>
                                <th>Cost Variance Score:</th>
                                <td id="detail-cost-variance">N/A</td>
                            </tr>
                            <tr>
                                <th>Sustainability Score:</th>
                                <td id="detail-sustainability">N/A</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="font-weight-bold">Documents</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>DTI/SEC Registration:</th>
                                <td id="detail-dti-sec">N/A</td>
                            </tr>
                            <tr>
                                <th>Mayor's Permit:</th>
                                <td id="detail-mayors-permit">N/A</td>
                            </tr>
                            <tr>
                                <th>Valid ID:</th>
                                <td id="detail-valid-id">N/A</td>
                            </tr>
                            <tr>
                                <th>Company Profile:</th>
                                <td id="detail-company-profile">N/A</td>
                            </tr>
                            <tr>
                                <th>Price List:</th>
                                <td id="detail-price-list">N/A</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editSupplierBtn">Edit Supplier</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle supplier link clicks
    document.querySelectorAll('.supplier-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const supplier = JSON.parse(this.dataset.supplier);
            
            // Update modal content
            document.getElementById('detail-company').textContent = supplier.company || 'N/A';
            document.getElementById('detail-contact-person').textContent = supplier.contact_person || 'N/A';
            document.getElementById('detail-email').textContent = supplier.email || 'N/A';
            document.getElementById('detail-mobile').textContent = supplier.mobile_number || 'N/A';
            document.getElementById('detail-telephone').textContent = supplier.telephone_number || 'N/A';
            document.getElementById('detail-address').textContent = supplier.address || 'N/A';
            document.getElementById('detail-business-reg').textContent = supplier.business_reg_no || 'N/A';
            document.getElementById('detail-supplier-type').textContent = supplier.supplier_type || 'N/A';
            document.getElementById('detail-business-size').textContent = supplier.business_size || 'N/A';
            document.getElementById('detail-years-operation').textContent = supplier.years_operation || 'N/A';
            document.getElementById('detail-payment-terms').textContent = supplier.payment_terms || 'N/A';
            document.getElementById('detail-vat-registered').textContent = supplier.vat_registered ? 'Yes' : 'No';
            
            // Performance metrics
            document.getElementById('detail-final-score').textContent = supplier.final_score ? supplier.final_score.toFixed(2) : 'N/A';
            document.getElementById('detail-engagement').textContent = supplier.engagement_score ? supplier.engagement_score.toFixed(2) : 'N/A';
            document.getElementById('detail-delivery-speed').textContent = supplier.delivery_speed_score ? supplier.delivery_speed_score.toFixed(2) : 'N/A';
            document.getElementById('detail-performance').textContent = supplier.performance_score ? supplier.performance_score.toFixed(2) : 'N/A';
            document.getElementById('detail-quality').textContent = supplier.quality_score ? supplier.quality_score.toFixed(2) : 'N/A';
            document.getElementById('detail-cost-variance').textContent = supplier.cost_variance_score ? supplier.cost_variance_score.toFixed(2) : 'N/A';
            document.getElementById('detail-sustainability').textContent = supplier.sustainability_score ? supplier.sustainability_score.toFixed(2) : 'N/A';
            
            // Documents
            document.getElementById('detail-dti-sec').textContent = supplier.dti_sec_registration_path ? 'Available' : 'N/A';
            document.getElementById('detail-mayors-permit').textContent = supplier.mayors_permit_path ? 'Available' : 'N/A';
            document.getElementById('detail-valid-id').textContent = supplier.valid_id_path ? 'Available' : 'N/A';
            document.getElementById('detail-company-profile').textContent = supplier.company_profile_path ? 'Available' : 'N/A';
            document.getElementById('detail-price-list').textContent = supplier.price_list_path ? 'Available' : 'N/A';
        });
    });
});
</script>
@endpush 