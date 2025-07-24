@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h4 class="card-title mb-0">Respond to Quotation</h4>
                </div>
                <div class="card-body">
                    @if(isset($quotationRequest) && $quotationRequest)
                        <div class="alert alert-secondary mb-4">
                            <h5 class="mb-1">Client Quotation Request #{{ $quotationRequest->request_number }}</h5>
                            <span class="badge bg-{{ $quotationRequest->status_color ?? 'secondary' }}">{{ $quotationRequest->status_label ?? ucfirst($quotationRequest->status) }}</span>
                            <ul class="mb-1 mt-2">
                                <li><strong>Submitted At:</strong> {{ $quotationRequest->created_at->format('M d, Y H:i') }}</li>
                                <li><strong>Requested By (User ID):</strong> {{ $quotationRequest->user_id }}</li>
                                <li><strong>Rooms/Scopes/Materials:</strong>
                                    <ul>
                                        @foreach($quotationRequest->rooms as $room)
                                            <li>
                                                <strong>{{ $room->name ?? 'Room' }}</strong>
                                                <ul>
                                                    @foreach($room->scopes as $scope)
                                                        <li>
                                                            {{ $scope->scopeType->name ?? 'Scope' }}:
                                                            @if($scope->scopeType && $scope->scopeType->materials)
                                                                {{ $scope->scopeType->materials->pluck('name')->join(', ') }}
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    @endif

                    @if($existingResponse)
                    <div class="alert alert-info">
                        <h5>You have already submitted a response for this quotation.</h5>
                        <p>Your current status: <strong>{{ ucfirst($existingResponse->status) }}</strong></p>
                        <p>Total Quoted Amount: <strong>₱{{ number_format($existingResponse->total_amount, 2) }}</strong></p>
                        @if($existingResponse->hasDiscount())
                            <p>Discount Type: <span class="badge bg-info text-dark">{{ $existingResponse->discount_type_display }}</span></p>
                            <p>Discount: <strong>{{ $existingResponse->discount_display }}</strong></p>
                            <p>Final Amount: <strong>₱{{ number_format($existingResponse->final_amount, 2) }}</strong></p>
                        @endif
                        <p>Notes: {{ $existingResponse->notes ?? 'N/A' }}</p>
                        <p class="text-danger fw-bold">You cannot submit more than one response to the same quotation.</p>
                    </div>
                    @endif

                    @if($errors->has('discount'))
                        <div class="alert alert-danger">
                            <h6>Discount Validation Errors:</h6>
                            <ul class="mb-0">
                                @foreach($errors->get('discount') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('supplier.quotations.respond', $quotation) }}" method="POST" id="quotationForm" @if($existingResponse) style="pointer-events:none;opacity:0.6;" @endif>
                        @csrf

                        <h5>Quoted Materials</h5>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="toggle-per-material-discount">
                            <label class="form-check-label" for="toggle-per-material-discount">
                                Enable Per-Material Discount (Percentage)
                            </label>
                        </div>
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>SRP</th>
                                        <th>Base Price</th>
                                        <th>Requested Quantity</th>
                                        <th>SRP / Base Price</th>
                                        <th>Current Material Price</th>
                                        <th>Your Quoted Price</th>
                                        <th class="per-material-discount-header" style="display:none;">Discount Type</th>
                                        <th class="per-material-discount-header" style="display:none;">Discount (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($materialsInQuotation as $material)
                                    <tr>
                                        <td>{{ $material->name }} ({{ $material->code }})</td>
                                        <td>₱{{ number_format(isset($material->srp_price) ? $material->srp_price : (isset($material->material) && isset($material->material->srp_price) ? $material->material->srp_price : 0), 2) }}</td>
                                        <td>₱{{ number_format(isset($material->base_price) ? $material->base_price : (isset($material->material) && isset($material->material->base_price) ? $material->material->base_price : 0), 2) }}</td>
                                        <td>{{ $material->requested_quantity }} {{ $material->unit }}</td>
                                        <td>
                                            @if(isset($material->srp_price) && $material->srp_price > 0)
                                                <span>₱{{ number_format($material->srp_price, 2) }} <small class="text-muted">(SRP)</small></span><br>
                                            @endif
                                            <span>₱{{ number_format($material->base_price, 2) }} <small class="text-muted">(Base)</small></span>
                                        </td>
                                        <td>₱{{ number_format($material->price, 2) }}</td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control form-control-sm material-price" 
                                                   name="materials[{{ $material->id }}][unit_price]" 
                                                   value="{{ old('materials.'.$material->id.'.unit_price', $existingResponse ? ($existingResponse->items->where('material_id', $material->id)->first()->unit_price ?? $material->price) : $material->price) }}" 
                                                   min="0" step="0.01" required
                                                   data-quantity="{{ $material->requested_quantity }}">
                                            <input type="hidden" name="materials[{{ $material->id }}][quantity]" value="{{ $material->requested_quantity }}">
                                        </td>
                                        <td class="per-material-discount-cell" style="display:none;">
                                            <select class="form-select form-select-sm per-material-discount-type" name="materials[{{ $material->id }}][discount_type]">
                                                @foreach($discountTypes as $type => $label)
                                                    <option value="{{ $type }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="per-material-discount-cell" style="display:none;">
                                            <input type="number" class="form-control form-control-sm per-material-discount-input" name="materials[{{ $material->id }}][discount_percentage]" min="0" max="100" step="0.01" value="0">
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No materials requested for this quotation.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pricing Summary -->
                        <div class="row mb-4">
                            <div class="col-md-6" id="pricing-summary-col">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">Pricing Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="per-material-summary" style="display:none;">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Material</th>
                                                        <th>Unit Price</th>
                                                        <th>Discount Type</th>
                                                        <th>Discount (%)</th>
                                                        <th>Discount Amount</th>
                                                        <th>Final Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($materialsInQuotation as $material)
                                                    <tr>
                                                        <td>{{ $material->name }} ({{ $material->code }})</td>
                                                        <td><span class="summary-unit-price" data-material-id="{{ $material->id }}">₱0.00</span></td>
                                                        <td><span class="summary-discount-type" data-material-id="{{ $material->id }}"></span></td>
                                                        <td><span class="summary-discount-percentage" data-material-id="{{ $material->id }}">0</span>%</td>
                                                        <td><span class="summary-discount-amount" data-material-id="{{ $material->id }}">₱0.00</span></td>
                                                        <td><span class="summary-final-price" data-material-id="{{ $material->id }}">₱0.00</span></td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="global-summary">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Subtotal:</span>
                                            <span id="subtotal">₱0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Discount:</span>
                                            <span id="discount-display">₱0.00</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Final Amount:</span>
                                            <span id="final-amount" class="text-success">₱0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <div class="col-md-6" id="discount-options-col">
                                <div class="card border-info" id="global-discount-options">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">Discount Options</h6>
                                    </div>
                                    <div class="card-body" id="global-discount-options">
                                        <div class="mb-3">
                                            <label class="form-label">Discount Type</label>
                                            <select class="form-select" id="discount-type" name="discount_type">
                                                @foreach($discountTypes as $type => $label)
                                                    <option value="{{ $type }}" {{ old('discount_type', $existingResponse->discount_type ?? '') == $type ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div id="discount-description" class="alert alert-info">
                                            <h6>Discount Description</h6>
                                            <p>Please select a discount type to see its description.</p>
                                        </div>
                                        <div id="discount-eligibility" class="alert" style="display: none;">
                                            <small id="eligibility-message"></small>
                                        </div>
                                        <div id="percentage-discount" class="discount-option" style="display: none;">
                                            <label class="form-label">Discount Percentage (%)</label>
                                            <input type="number" class="form-control" id="discount-percentage" name="discount_percentage" min="0" max="100" step="0.01" value="{{ old('discount_percentage', $existingResponse->discount_percentage ?? '') }}">
                                            <small class="text-muted">Maximum: <span id="max-percentage">0</span>%</small>
                                        </div>
                                        <div id="amount-discount" class="discount-option" style="display: none;">
                                            <label class="form-label">Discount Amount (₱)</label>
                                            <input type="number" class="form-control" id="discount-amount" name="discount_amount" min="0" step="0.01" value="{{ old('discount_amount', $existingResponse->discount_amount ?? '') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Discount Reason (Optional)</label>
                                            <textarea class="form-control" id="discount-reason" name="discount_reason" rows="2" placeholder="e.g., Bulk order discount, seasonal promotion, etc.">{{ old('discount_reason', $existingResponse->discount_reason ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Terms -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="payment_terms" class="form-label">Payment Terms</label>
                                <input type="text" class="form-control" id="payment_terms" name="payment_terms" value="{{ old('payment_terms', $existingResponse->payment_terms ?? 'Net 30') }}" placeholder="e.g., Net 30, 50% advance">
                            </div>
                            <div class="col-md-4">
                                <label for="delivery_terms" class="form-label">Delivery Terms</label>
                                <input type="text" class="form-control" id="delivery_terms" name="delivery_terms" value="{{ old('delivery_terms', $existingResponse->delivery_terms ?? 'FOB Destination') }}" placeholder="e.g., FOB Destination, 7-10 days">
                            </div>
                            <div class="col-md-4">
                                <label for="validity_period" class="form-label">Validity Period</label>
                                <input type="text" class="form-control" id="validity_period" name="validity_period" value="{{ old('validity_period', $existingResponse->validity_period ?? '30 days') }}" placeholder="e.g., 30 days, 2 weeks">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any additional information about your quotation...">{{ old('notes', $existingResponse->notes ?? '') }}</textarea>
                        </div>

                        <div class="d-grid">
                            @if(!$existingResponse)
                                <button type="submit" class="btn btn-primary btn-lg">Submit Quotation Response</button>
                            @else
                                <button type="button" class="btn btn-secondary btn-lg" disabled>Already Submitted</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    @vite(['resources/css/supplier/quotation-respond.css'])
@endpush

@push('scripts')
    @vite(['resources/js/supplier/quotation-respond.js'])
<script>
// Discount descriptions mapping with requirements
const discountDescriptions = {
    'none': 'No discount will be applied to this quotation.',
    'bulk_order': 'Bulk Order Discount: Applies when the client orders in large quantities. <br><strong>Requirement:</strong> Minimum order quantity must be met (e.g., 100+ units).',
    'seasonal': 'Seasonal Promotion: Special discount for a limited time or season. <br><strong>Requirement:</strong> Only available during the promotional period (e.g., summer sale, holiday promo).',
    'loyalty': 'Loyalty Discount: For repeat or long-term clients. <br><strong>Requirement:</strong> Client must have completed at least 3 previous orders or be a registered partner.',
    'new_customer': 'New Customer Discount: For first-time clients only. <br><strong>Requirement:</strong> Client must not have any previous orders.',
    'early_payment': 'Early Payment Discount: Applies if the client pays before the due date. <br><strong>Requirement:</strong> Payment must be made within 7 days of invoice.',
    'flexible_delivery': 'Flexible Delivery Discount: Discount for clients who accept flexible delivery schedules. <br><strong>Requirement:</strong> Client agrees to a delivery window instead of a fixed date.',
    'custom': 'Custom Discount: Any other discount not listed above. <br><strong>Requirement:</strong> Please specify the reason and eligibility in the notes.'
};

function updateDiscountDescription() {
    const select = document.getElementById('discount-type');
    const descDiv = document.getElementById('discount-description');
    const value = select.value;
    descDiv.innerHTML = discountDescriptions[value] || '';
    // Always keep the description visible
    descDiv.style.display = 'block';
}
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('discount-type');
    if (select) {
        select.addEventListener('change', updateDiscountDescription);
        updateDiscountDescription();
    }
});
</script>
@endpush
@endsection 