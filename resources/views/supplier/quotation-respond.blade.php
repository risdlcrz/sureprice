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
                            <p>Discount Type: <span class="badge {{ $existingResponse->discount_badge_class }}">{{ $existingResponse->discount_type_display }}</span></p>
                            <p>Discount: <strong>{{ $existingResponse->discount_display }}</strong></p>
                            <p>Final Amount: <strong>₱{{ number_format($existingResponse->final_amount, 2) }}</strong></p>
                        @endif
                        <p>Notes: {{ $existingResponse->notes ?? 'N/A' }}</p>
                        <p>You can re-submit your response below if needed.</p>
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

                    <form action="{{ route('supplier.quotations.respond', $quotation) }}" method="POST" id="quotationForm">
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
                                        <div id="discount-info" class="alert alert-info" style="display: none;">
                                            <small id="discount-description"></small>
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
                            <button type="submit" class="btn btn-primary btn-lg">Submit Quotation Response</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@php
    $discountTypeInit = ($existingResponse && $existingResponse->hasDiscount()) ? $existingResponse->discount_type : null;
    $discountPercentageInit = ($existingResponse && $existingResponse->hasDiscount()) ? $existingResponse->discount_percentage : null;
    $discountAmountInit = ($existingResponse && $existingResponse->hasDiscount()) ? $existingResponse->discount_amount : null;
@endphp
<script>
let discountTypeInit = {!! json_encode($discountTypeInit) !!};
let discountPercentageInit = {!! json_encode($discountPercentageInit) !!};
let discountAmountInit = {!! json_encode($discountAmountInit) !!};

document.addEventListener('DOMContentLoaded', function() {
    const discountType = document.getElementById('discount-type');
    const percentageDiscount = document.getElementById('percentage-discount');
    const amountDiscount = document.getElementById('amount-discount');
    const discountPercentage = document.getElementById('discount-percentage');
    const discountAmount = document.getElementById('discount-amount');
    const materialPrices = document.querySelectorAll('.material-price');
    const discountInfo = document.getElementById('discount-info');
    const discountDescription = document.getElementById('discount-description');
    const discountEligibility = document.getElementById('discount-eligibility');
    const eligibilityMessage = document.getElementById('eligibility-message');
    const maxPercentage = document.getElementById('max-percentage');
    const togglePerMaterialDiscount = document.getElementById('toggle-per-material-discount');
    const perMaterialSummary = document.getElementById('per-material-summary');
    const globalSummary = document.getElementById('global-summary');
    const perMaterialDiscountInputs = document.querySelectorAll('.per-material-discount-input');
    const perMaterialDiscountHeaders = document.querySelectorAll('.per-material-discount-header');
    const perMaterialDiscountCells = document.querySelectorAll('.per-material-discount-cell');
    
    // Apply initial values if present
    if (typeof discountTypeInit !== 'undefined' && discountTypeInit !== null) {
        if (discountPercentageInit > 0) {
            discountPercentage.value = discountPercentageInit;
            percentageDiscount.style.display = 'block';
        } else if (discountAmountInit > 0) {
            discountAmount.value = discountAmountInit;
            amountDiscount.style.display = 'block';
        }
    }

    // Show/hide discount options based on type
    if (discountType) {
    discountType.addEventListener('change', function() {
        percentageDiscount.style.display = 'none';
        amountDiscount.style.display = 'none';
        discountInfo.style.display = 'none';
        discountEligibility.style.display = 'none';
        if (this.value !== 'none') {
            // Show discount input based on type
            if (this.value === 'percentage' || this.value === 'bulk' || this.value === 'seasonal' || 
                this.value === 'loyalty' || this.value === 'new_customer' || this.value === 'payment_terms' || 
                this.value === 'delivery_terms' || this.value === 'custom') {
                percentageDiscount.style.display = 'block';
            } else if (this.value === 'amount') {
                amountDiscount.style.display = 'block';
            }
            // Get discount info
            getDiscountInfo(this.value);
        }
            calculateTotals();
        });
    }

    // Toggle per-material discount
    togglePerMaterialDiscount.addEventListener('change', function() {
        var show = this.checked;
        perMaterialSummary.style.display = show ? 'block' : 'none';
        globalSummary.style.display = show ? 'none' : 'block';
        perMaterialDiscountHeaders.forEach(function(h) { h.style.display = show ? '' : 'none'; });
        perMaterialDiscountCells.forEach(function(c) { c.style.display = show ? '' : 'none'; });

        var pricingSummaryCol = document.getElementById('pricing-summary-col');
        var discountOptionsCol = document.getElementById('discount-options-col');
        var globalDiscountOptions = document.getElementById('global-discount-options');
        if (show) {
            if (globalDiscountOptions) { globalDiscountOptions.style.display = 'none'; }
            if (pricingSummaryCol) {
                pricingSummaryCol.classList.remove('col-md-6');
                pricingSummaryCol.classList.add('col-md-12');
            }
            if (discountOptionsCol) { discountOptionsCol.style.display = 'none'; }
        } else {
            if (globalDiscountOptions) { globalDiscountOptions.style.display = 'block'; }
            if (pricingSummaryCol) {
                pricingSummaryCol.classList.remove('col-md-12');
                pricingSummaryCol.classList.add('col-md-6');
            }
            if (discountOptionsCol) { discountOptionsCol.style.display = 'block'; }
        }
        calculateTotals();
    });

    // Get discount information from server
    function getDiscountInfo(discountType) {
        var subtotal = calculateSubtotal();
        fetch('{{ route("supplier.quotations.discount-info") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                discount_type: discountType,
                order_amount: subtotal
            })
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.error) {
                discountInfo.style.display = 'none';
                return;
            }
            discountDescription.textContent = data.description;
            maxPercentage.textContent = data.max_percentage;
            discountInfo.style.display = 'block';
            // Show eligibility
            if (data.is_eligible) {
                eligibilityMessage.textContent = data.message;
                discountEligibility.className = 'alert alert-success';
            } else {
                eligibilityMessage.textContent = data.message;
                discountEligibility.className = 'alert alert-warning';
            }
            discountEligibility.style.display = 'block';
            // Update max percentage for input
            if (discountPercentage) {
                discountPercentage.max = data.max_percentage;
            }
        })
        .catch(function(error) {
            console.error('Error fetching discount info:', error);
        });
    }

    // Calculate subtotal
    function calculateSubtotal() {
        var subtotal = 0;
        materialPrices.forEach(function(input) {
            var price = parseFloat(input.value) || 0;
            var quantity = parseFloat(input.dataset.quantity) || 0;
            subtotal += price * quantity;
        });
        return subtotal;
    }

    // Calculate totals when prices or discounts change
    function calculateTotals() {
        var subtotal = 0;
        var perMaterialDiscountTotal = 0;
        var perMaterialFinalTotal = 0;
        var finalAmount = 0;
        var discount = 0;
        var discountDisplay = '';
        var usePerMaterial = togglePerMaterialDiscount.checked;

        if (usePerMaterial) {
            materialPrices.forEach(function(input) {
                var row = input.closest('tr');
                var materialIdMatch = input.name.match(/materials\[(\d+)\]/);
                var materialId = materialIdMatch ? materialIdMatch[1] : null;
                var unitPrice = parseFloat(input.value) || 0;
                var quantity = parseFloat(input.dataset.quantity) || 0;
                var discountTypeSelect = row.querySelector('.per-material-discount-type');
                var discountType = discountTypeSelect ? discountTypeSelect.options[discountTypeSelect.selectedIndex].text : '';
                var discountInput = row.querySelector('.per-material-discount-input');
                var discountPercentage = parseFloat(discountInput.value) || 0;
                var discountAmount = (unitPrice * discountPercentage) / 100;
                var finalPrice = unitPrice - discountAmount;

                // Update summary cells
                var unitPriceCell = document.querySelector('.summary-unit-price[data-material-id="' + materialId + '"]');
                var discountTypeCell = document.querySelector('.summary-discount-type[data-material-id="' + materialId + '"]');
                var discountPercentageCell = document.querySelector('.summary-discount-percentage[data-material-id="' + materialId + '"]');
                var discountAmountCell = document.querySelector('.summary-discount-amount[data-material-id="' + materialId + '"]');
                var finalPriceCell = document.querySelector('.summary-final-price[data-material-id="' + materialId + '"]');
                if (unitPriceCell) unitPriceCell.textContent = '₱' + unitPrice.toFixed(2);
                if (discountTypeCell) discountTypeCell.textContent = discountType;
                if (discountPercentageCell) discountPercentageCell.textContent = discountPercentage;
                if (discountAmountCell) discountAmountCell.textContent = '₱' + discountAmount.toFixed(2);
                if (finalPriceCell) finalPriceCell.textContent = '₱' + finalPrice.toFixed(2);

                subtotal += unitPrice * quantity;
                perMaterialDiscountTotal += discountAmount * quantity;
                perMaterialFinalTotal += finalPrice * quantity;
            });
            finalAmount = perMaterialFinalTotal;
            document.getElementById('subtotal').textContent = '₱' + subtotal.toFixed(2);
            document.getElementById('discount-display').textContent = 'Per-material: -₱' + perMaterialDiscountTotal.toFixed(2);
            document.getElementById('final-amount').textContent = '₱' + finalAmount.toFixed(2);
        } else {
            subtotal = calculateSubtotal();
            if (discountType && discountType.value !== 'none') {
            if (discountType.value === 'percentage' || discountType.value === 'bulk' || 
                discountType.value === 'seasonal' || discountType.value === 'loyalty' || 
                discountType.value === 'new_customer' || discountType.value === 'payment_terms' || 
                discountType.value === 'delivery_terms' || discountType.value === 'custom') {
                    var percentage = parseFloat(discountPercentage.value) || 0;
                discount = (subtotal * percentage) / 100;
                discountDisplay = percentage + '% (-₱' + discount.toFixed(2) + ')';
            } else if (discountType.value === 'amount') {
                discount = parseFloat(discountAmount.value) || 0;
                if (discount > subtotal) {
                    discount = subtotal;
                    discountAmount.value = subtotal;
                }
                discountDisplay = '₱' + discount.toFixed(2);
            }
        }
            finalAmount = subtotal - discount;
        document.getElementById('subtotal').textContent = '₱' + subtotal.toFixed(2);
            document.getElementById('discount-display').textContent = discountDisplay || '₱0.00';
        document.getElementById('final-amount').textContent = '₱' + finalAmount.toFixed(2);
        }
        // Update discount info if discount type is selected
        if (discountType && discountType.value !== 'none') {
            getDiscountInfo(discountType.value);
        }
    }

    // Add event listeners for real-time calculation
    materialPrices.forEach(function(input) {
        input.addEventListener('input', calculateTotals);
    });
    if (discountPercentage) {
        discountPercentage.addEventListener('input', calculateTotals);
    }
    if (discountAmount) {
        discountAmount.addEventListener('input', calculateTotals);
    }
    perMaterialDiscountInputs.forEach(function(input) {
        input.addEventListener('input', calculateTotals);
    });
    // Add event listeners for per-material discount type dropdowns
    var perMaterialDiscountTypeSelects = document.querySelectorAll('.per-material-discount-type');
    perMaterialDiscountTypeSelects.forEach(function(select) {
        select.addEventListener('change', calculateTotals);
    });
    // Initial calculation
    calculateTotals();
});
</script>
@endpush
@endsection 