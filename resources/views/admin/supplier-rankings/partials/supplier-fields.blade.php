<div class="row">
    <div class="col-md-6">
        <h6 class="font-weight-bold">Basic Information</h6>
        <table class="table table-sm">
            <tr>
                <th>Company Name:</th>
                <td>
                    @if($editable)
                        <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $supplier['company_name'] ?? $supplier['company'] ?? '') }}" required>
                    @else
                        <span id="detail-company">{{ $supplier['company_name'] ?? $supplier['company'] ?? 'N/A' }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Contact Person:</th>
                <td>
                    @if($editable)
                        <input type="text" class="form-control" id="contact_person" name="contact_person" value="{{ old('contact_person', $supplier['contact_person'] ?? '') }}" required>
                    @else
                        <span id="detail-contact-person">{{ $supplier['contact_person'] ?? 'N/A' }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Email:</th>
                <td>
                    @if($editable)
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $supplier['email'] ?? '') }}" required>
                    @else
                        <span id="detail-email">{{ $supplier['email'] ?? 'N/A' }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Mobile Number:</th>
                <td>
                    @if($editable)
                        <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $supplier['mobile_number'] ?? '') }}" required>
                    @else
                        <span id="detail-mobile">{{ $supplier['mobile_number'] ?? 'N/A' }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Telephone Number:</th>
                <td>
                    @if($editable)
                        <input type="text" class="form-control" id="telephone_number" name="telephone_number" value="{{ old('telephone_number', $supplier['telephone_number'] ?? '') }}">
                    @else
                        <span id="detail-telephone">{{ $supplier['telephone_number'] ?? 'N/A' }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h6 class="font-weight-bold">Business Information</h6>
        <table class="table table-sm">
            <tr>
                <th>Business Registration No:</th>
                <td>
                    @if($editable)
                        <input type="text" class="form-control" id="business_reg_no" name="business_reg_no" value="{{ old('business_reg_no', $supplier['business_reg_no'] ?? '') }}">
                    @else
                        <span id="detail-business-reg">{{ $supplier['business_reg_no'] ?? 'N/A' }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Supplier Type:</th>
                <td>
                    @if($editable)
                        <select class="form-control" id="supplier_type" name="supplier_type">
                            <option value="">Select Type</option>
                            <option value="Manufacturer" {{ (old('supplier_type', $supplier['supplier_type'] ?? '') == 'Manufacturer') ? 'selected' : '' }}>Manufacturer</option>
                            <option value="Distributor" {{ (old('supplier_type', $supplier['supplier_type'] ?? '') == 'Distributor') ? 'selected' : '' }}>Distributor</option>
                            <option value="Wholesaler" {{ (old('supplier_type', $supplier['supplier_type'] ?? '') == 'Wholesaler') ? 'selected' : '' }}>Wholesaler</option>
                            <option value="Retailer" {{ (old('supplier_type', $supplier['supplier_type'] ?? '') == 'Retailer') ? 'selected' : '' }}>Retailer</option>
                        </select>
                    @else
                        <span id="detail-supplier-type">{{ $supplier['supplier_type'] ?? 'N/A' }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Business Size:</th>
                <td>
                    @if($editable)
                        <select class="form-control" id="business_size" name="business_size">
                            <option value="">Select Size</option>
                            <option value="Small" {{ (old('business_size', $supplier['business_size'] ?? '') == 'Small') ? 'selected' : '' }}>Small</option>
                            <option value="Medium" {{ (old('business_size', $supplier['business_size'] ?? '') == 'Medium') ? 'selected' : '' }}>Medium</option>
                            <option value="Large" {{ (old('business_size', $supplier['business_size'] ?? '') == 'Large') ? 'selected' : '' }}>Large</option>
                        </select>
                    @else
                        <span id="detail-business-size">{{ $supplier['business_size'] ?? 'N/A' }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Years in Operation:</th>
                <td>
                    @if($editable)
                        <input type="number" class="form-control" id="years_operation" name="years_operation" min="0" value="{{ old('years_operation', $supplier['years_operation'] ?? '') }}">
                    @else
                        <span id="detail-years-operation">{{ $supplier['years_operation'] ?? 'N/A' }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Payment Terms:</th>
                <td>
                    @if($editable)
                        <input type="text" class="form-control" id="payment_terms" name="payment_terms" value="{{ old('payment_terms', $supplier['payment_terms'] ?? '') }}">
                    @else
                        <span id="detail-payment-terms">{{ $supplier['payment_terms'] ?? 'N/A' }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <h6 class="font-weight-bold">Address Information</h6>
        <table class="table table-sm">
            <tr>
                <th>Address:</th>
                <td>
                    @if($editable)
                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $supplier['address'] ?? '') }}">
                    @else
                        <span id="detail-address">{{ $supplier['address'] ?? 'N/A' }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h6 class="font-weight-bold">Bank Information</h6>
        <table class="table table-sm">
            <tr>
                <th>Bank Name:</th>
                <td>
                    @if($editable)
                        <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name', $supplier['bank_name'] ?? '') }}">
                    @else
                        <span id="detail-bank-name">{{ $supplier['bank_name'] ?? 'N/A' }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Account Name:</th>
                <td>
                    @if($editable)
                        <input type="text" class="form-control" id="account_name" name="account_name" value="{{ old('account_name', $supplier['account_name'] ?? '') }}">
                    @else
                        <span id="detail-account-name">{{ $supplier['account_name'] ?? 'N/A' }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Account Number:</th>
                <td>
                    @if($editable)
                        <input type="text" class="form-control" id="account_number" name="account_number" value="{{ old('account_number', $supplier['account_number'] ?? '') }}">
                    @else
                        <span id="detail-account-number">{{ $supplier['account_number'] ?? 'N/A' }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <h6 class="font-weight-bold">Additional Information</h6>
        <table class="table table-sm">
            <tr>
                <th>VAT Registered:</th>
                <td>
                    @if($editable)
                        <div class="custom-control custom-radio d-inline-block mr-2">
                            <input type="radio" id="vat_yes" name="vat_registered" value="Yes" class="custom-control-input" {{ old('vat_registered', $supplier['vat_registered'] ?? '') == 'Yes' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="vat_yes">Yes</label>
                        </div>
                        <div class="custom-control custom-radio d-inline-block">
                            <input type="radio" id="vat_no" name="vat_registered" value="No" class="custom-control-input" {{ old('vat_registered', $supplier['vat_registered'] ?? '') == 'No' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="vat_no">No</label>
                        </div>
                    @else
                        <span id="detail-vat-registered">{{ (isset($supplier['vat_registered']) ? ($supplier['vat_registered'] ? 'Yes' : 'No') : 'N/A') }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Use SurePrice:</th>
                <td>
                    @if($editable)
                        <div class="custom-control custom-radio d-inline-block mr-2">
                            <input type="radio" id="sureprice_yes" name="use_sureprice" value="Yes" class="custom-control-input" {{ old('use_sureprice', $supplier['use_sureprice'] ?? '') == 'Yes' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="sureprice_yes">Yes</label>
                        </div>
                        <div class="custom-control custom-radio d-inline-block">
                            <input type="radio" id="sureprice_no" name="use_sureprice" value="No" class="custom-control-input" {{ old('use_sureprice', $supplier['use_sureprice'] ?? '') == 'No' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="sureprice_no">No</label>
                        </div>
                    @else
                        <span id="detail-use-sureprice">{{ (isset($supplier['use_sureprice']) ? ($supplier['use_sureprice'] ? 'Yes' : 'No') : 'N/A') }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Products/Materials:</th>
                <td>
                    @if($editable)
                        <select class="form-control select2" id="products" name="products[]" multiple>
                            <option value="Raw Materials" {{ (isset($supplier['products']) && in_array('Raw Materials', (array)$supplier['products'])) ? 'selected' : '' }}>Raw Materials</option>
                            <option value="Packaging Materials" {{ (isset($supplier['products']) && in_array('Packaging Materials', (array)$supplier['products'])) ? 'selected' : '' }}>Packaging Materials</option>
                            <option value="Office Supplies" {{ (isset($supplier['products']) && in_array('Office Supplies', (array)$supplier['products'])) ? 'selected' : '' }}>Office Supplies</option>
                            <option value="Equipment" {{ (isset($supplier['products']) && in_array('Equipment', (array)$supplier['products'])) ? 'selected' : '' }}>Equipment</option>
                            <option value="Services" {{ (isset($supplier['products']) && in_array('Services', (array)$supplier['products'])) ? 'selected' : '' }}>Services</option>
                        </select>
                    @else
                        <span id="detail-materials">
                            @if(isset($supplier['products']) && is_array($supplier['products']))
                                {{ implode(', ', $supplier['products']) }}
                            @elseif(isset($supplier['materials']) && is_array($supplier['materials']))
                                {{ implode(', ', $supplier['materials']) }}
                            @else
                                {{ $supplier['materials'] ?? 'N/A' }}
                            @endif
                        </span>
                    @endif
                </td>
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
                <td>
                    @if($editable)
                        <input type="number" step="0.01" class="form-control" id="final_score" name="final_score" value="{{ old('final_score', $supplier['final_score'] ?? 0) }}">
                    @else
                        <span id="detail-final-score">{{ number_format($supplier['final_score'] ?? 0, 2) }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Cost Variance Score:</th>
                <td>
                    @if($editable)
                        <input type="number" step="0.01" class="form-control" id="cost_variance_score" name="cost_variance_score" value="{{ old('cost_variance_score', $supplier['cost_variance_score'] ?? 0) }}">
                    @else
                        <span id="detail-cost-variance">{{ number_format($supplier['cost_variance_score'] ?? 0, 2) }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Sustainability Score:</th>
                <td>
                    @if($editable)
                        <input type="number" step="0.01" class="form-control" id="sustainability_score" name="sustainability_score" value="{{ old('sustainability_score', $supplier['sustainability_score'] ?? 0) }}">
                    @else
                        <span id="detail-sustainability">{{ number_format($supplier['sustainability_score'] ?? 0, 2) }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Total Deliveries:</th>
                <td>
                    @if($editable)
                        <input type="number" class="form-control" id="total_deliveries" name="total_deliveries" value="{{ old('total_deliveries', $supplier['total_deliveries'] ?? 0) }}">
                    @else
                        <span id="detail-total-deliveries">{{ $supplier['total_deliveries'] ?? 0 }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>On-time Deliveries:</th>
                <td>
                    @if($editable)
                        <input type="number" class="form-control" id="ontime_deliveries" name="ontime_deliveries" value="{{ old('ontime_deliveries', $supplier['ontime_deliveries'] ?? 0) }}">
                    @else
                        <span id="detail-ontime-deliveries">{{ $supplier['ontime_deliveries'] ?? 0 }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div> 