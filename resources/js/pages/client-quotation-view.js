// Extracted from resources/views/client/quotation/view.blade.php
// Blocks kept in their original order: they used to run as consecutive inline scripts.
const __page = window.__page || {};

// ---- block 0 ----
// Move hasAnyResponses to global scope
let hasAnyResponses = false;

document.addEventListener('DOMContentLoaded', function() {
    // Only initialize Select2 once for all supplier-select elements
    if (window.__supplierSelect2Initialized !== true) {
        window.__supplierSelect2Initialized = true;
        $('.supplier-select').select2({
            width: '100%',
            templateResult: function (data) {
                if (!data.id) return data.text;
                var badges = [];
                try { badges = JSON.parse($(data.element).attr('data-badges')); } catch {}
                var html = '<span>' + $(data.element).attr('data-supplier') + ' (₱' + $(data.element).attr('data-price') + ')';
                if (badges && badges.length) {
                    badges.forEach(function(badge) {
                        if (badge === 'Cheapest') html += ' <span class="badge badge-cheapest">Cheapest</span>';
                        if (badge === 'Best Delivery') html += ' <span class="badge badge-delivery">Best Delivery</span>';
                        if (badge === 'Least Defects') html += ' <span class="badge badge-defects">Least Defects</span>';
                        if (badge === 'Overall Best') html += ' <span class="badge badge-overall">Overall Best</span>';
                    });
                }
                html += '</span>';
                return $(html);
            },
            templateSelection: function (data) {
                if (!data.id) return data.text;
                var badges = [];
                try { badges = JSON.parse($(data.element).attr('data-badges')); } catch {}
                var html = '<span>' + $(data.element).attr('data-supplier') + ' (₱' + $(data.element).attr('data-price') + ')';
                if (badges && badges.length) {
                    badges.forEach(function(badge) {
                        if (badge === 'Cheapest') html += ' <span class="badge badge-cheapest">Cheapest</span>';
                        if (badge === 'Best Delivery') html += ' <span class="badge badge-delivery">Best Delivery</span>';
                        if (badge === 'Least Defects') html += ' <span class="badge badge-defects">Least Defects</span>';
                        if (badge === 'Overall Best') html += ' <span class="badge badge-overall">Overall Best</span>';
                    });
                }
                html += '</span>';
                return $(html);
            },
            escapeMarkup: function (m) { return m; }
        });
    }
    const recommendBtn = document.getElementById('clientRecommendAllBtn');
    const recommendModal = new bootstrap.Modal(document.getElementById('clientRecommendModal'));
    const applyRecommendBtn = document.getElementById('clientApplyRecommendBtn');
    
    // Check if there are any supplier responses
    hasAnyResponses = false;
    $('.supplier-select').each(function() {
        if ($(this).find('option').length > 1) { // More than just "Select Supplier"
            hasAnyResponses = true;
        }
    });
    
    // Disable recommend button if no responses
    if (!hasAnyResponses) {
        recommendBtn.disabled = true;
        recommendBtn.title = 'No supplier responses available yet';
        recommendBtn.classList.add('btn-secondary');
        recommendBtn.classList.remove('btn-primary');
    }
    
    recommendBtn.addEventListener('click', function() {
        if (!hasAnyResponses) {
            alert('No supplier responses available yet. Please wait for suppliers to submit their offers.');
            return;
        }
        recommendModal.show();
    });
    
    applyRecommendBtn.addEventListener('click', function() {
        if (!hasAnyResponses) {
            alert('No supplier responses available yet. Please wait for suppliers to submit their offers.');
            return;
        }
        
        const category = document.getElementById('clientRecommendCategory').value;
        fetch(`${__page.view0_0}?id=${__page.view0_1}&category=${category}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.recommendations) {
                    for (const materialId in data.recommendations) {
                        const supplierId = data.recommendations[materialId];
                        const select = document.querySelector(`select[name='selected_suppliers[${materialId}]']`);
                        if (select) {
                            $(select).val(supplierId).trigger('change');
                        }
                    }
                }
                recommendModal.hide();
                // --- NEW: Check if all suppliers are selected and show the button ---
                setTimeout(function() {
                    let allSelected = true;
                    $('.supplier-select').each(function() {
                        if (!$(this).val()) {
                            allSelected = false;
                        }
                    });
                    if (allSelected && hasAnyResponses) {
                        // The proceed button is now directly in the HTML, no need to inject here
                    }
                }, 500);
                // --- END NEW ---
            });
    });
    // Disable the Proceed button after click to prevent double submission
    $(document).on('submit', '#proceedQuotationForm', function(e) {
        // Remove any existing hidden inputs for selected_suppliers
        $(this).find('input[name^="selected_suppliers"]').remove();
        // For each supplier-select, add a hidden input with the current value
        $('.supplier-select').each(function() {
            var materialId = $(this).data('material-id');
            var supplierId = $(this).val();
            if (supplierId) {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'selected_suppliers[' + materialId + ']',
                    value: supplierId
                }).appendTo('#proceedQuotationForm');
            }
        });
        var btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true);
        btn.text('Processing...');
        // Hide the tip alert only when the client clicks 'Proceed with Quotation'
        $('#discount-tip-alert').hide();
        $('#live-supplier-summary').hide();
    });
});
function updateLiveSupplierSummary() {
    // Gather all selected supplier IDs
    var selected = [];
    var allSelected = true;
    var supplierId = null;
    $('.supplier-select').each(function() {
        var val = $(this).val();
        if (!val) allSelected = false;
        selected.push(val);
    });
    // Check if all are the same and not empty
    var unique = [...new Set(selected.filter(Boolean))];
    var summaryDiv = $('#live-supplier-summary');
    if (allSelected && unique.length === 1) {
        supplierId = unique[0];
        // Calculate total and check for discount
        var total = 0;
        var discountType = null;
        var discountValue = null;
        var finalAmount = null;
        var discountFound = false;
        // Find all offers for this supplier
        var offers = [];
        $('.supplier-select').each(function() {
            var matId = $(this).data('material-id');
            var price = 0;
            var qty = 1;
            var offer = null;
            // Find the offer in the materialSupplierResponses (rendered as JS object)
            if (window.materialSupplierResponses && window.materialSupplierResponses[matId]) {
                window.materialSupplierResponses[matId].forEach(function(o) {
                    if (o.supplier_id == supplierId) offer = o;
                });
            }
            if (offer && offer.unit_price) price = parseFloat(offer.unit_price);
            if (offer && offer.quantity) qty = parseFloat(offer.quantity);
            total += price * qty;
        });
        // Find discount info from window.supplierDiscounts (rendered as JS object)
        if (window.supplierDiscounts && window.supplierDiscounts[supplierId]) {
            var d = window.supplierDiscounts[supplierId];
            discountType = d.discount_type;
            discountValue = d.discount_percentage ? d.discount_percentage+'%' : (d.discount_amount ? '₱'+parseFloat(d.discount_amount).toLocaleString(undefined, {minimumFractionDigits:2}) : null);
            finalAmount = d.final_amount;
            discountFound = discountType && discountType !== 'none';
        }
        // Set window.finalAmount to the correct value
        if (discountFound && finalAmount) {
            window.finalAmount = parseFloat(finalAmount);
        } else {
            window.finalAmount = total;
        }
        var html = '<strong>Summary for Supplier:</strong> ' + ($('.supplier-select option[value="'+supplierId+'"]:first').text() || supplierId) + '<br>';
        html += '<strong>Total Quoted Price:</strong> ₱' + total.toLocaleString(undefined, {minimumFractionDigits:2}) + '<br>';
        if (discountFound) {
            html += '<strong>Discount:</strong> ' + discountType.charAt(0).toUpperCase() + discountType.slice(1) + ' (' + discountValue + ')<br>';
            html += '<strong>Final Amount After Discount:</strong> ₱' + parseFloat(finalAmount).toLocaleString(undefined, {minimumFractionDigits:2});
        } else {
            html += '<strong>Final Amount:</strong> ₱' + total.toLocaleString(undefined, {minimumFractionDigits:2}) + '<br>';
            html += '<span class="text-danger">This supplier does not include a discount for your selected materials.</span>';
        }
        summaryDiv.html(html).show();
    } else {
        summaryDiv.hide();
        window.finalAmount = 0;
    }
}
// Prepare materialSupplierResponses and supplierDiscounts as JS objects
window.materialSupplierResponses = __page.view0_2;
window.supplierDiscounts = __page.view0_3;
// Listen for changes
$(document).on('change', '.supplier-select', function() {
    updateLiveSupplierSummary();
    // Recompute hasAnyResponses in case options change
    hasAnyResponses = false;
    $('.supplier-select').each(function() {
        if ($(this).find('option').length > 1) {
            hasAnyResponses = true;
        }
    });
    // Show/hide Proceed button
    let allSelected = true;
    $('.supplier-select').each(function() {
        if (!$(this).val()) {
            allSelected = false;
        }
    });
    if (allSelected && hasAnyResponses) {
        // The proceed button is now directly in the HTML, no need to inject here
    } else {
        // The proceed button is now directly in the HTML, no need to remove here
    }
});
$(document).ready(updateLiveSupplierSummary);
$(function () {
    $('[data-bs-toggle="tooltip"]').tooltip();
});

function updateProceedButtonState() {
    let allSelected = true;
    $('.supplier-select').each(function() {
        if (!$(this).val()) {
            allSelected = false;
        }
    });
    const btn = $('#proceedQuotationBtn');
    if (allSelected && hasAnyResponses) {
        btn.prop('disabled', false);
        btn.attr('title', '');
    } else {
        btn.prop('disabled', true);
        btn.attr('title', 'Please select suppliers for all materials before proceeding.');
    }
}
// Call on page load and whenever supplier selection changes
$(document).ready(function() {
    updateProceedButtonState();
});
$(document).on('change', '.supplier-select', function() {
    updateProceedButtonState();
});

// ---- block 1 ----
var totalEstimatedDays = __page.view1_0;
function updateEndDate() {
    var start = $('#project_start_date').val();
    if (!start) {
        $('#project_end_date').val('');
        return;
    }
    var [yyyy, mm, dd] = start.split('-');
    var startDate = new Date(Number(yyyy), Number(mm) - 1, Number(dd));
    if (isNaN(startDate)) {
        $('#project_end_date').val('');
        return;
    }
    var days = totalEstimatedDays;
    var endDate = new Date(startDate);
    endDate.setDate(startDate.getDate() + days - 1);
    var yyyyEnd = endDate.getFullYear();
    var mmEnd = String(endDate.getMonth() + 1).padStart(2, '0');
    var ddEnd = String(endDate.getDate()).padStart(2, '0');
    var endDateStr = `${yyyyEnd}-${mmEnd}-${ddEnd}`;
    $('#project_end_date').val(endDateStr);
    $('#project_end_date').attr('min', start);
}
// Always recalculate on start date change or modal show
$('#project_start_date').on('change', function() {
    updateEndDate();
    updatePaymentPlanOptions();
});
$('#proceedQuotationModal').on('show.bs.modal', function() {
    updateEndDate();
    updatePaymentPlanOptions();
});

// Function to update payment plan options based on project duration
function updatePaymentPlanOptions() {
    const startDate = $('#project_start_date').val();
    const endDate = $('#project_end_date').val();
    
    if (!startDate || !endDate) {
        return;
    }
    
    const start = new Date(startDate);
    const end = new Date(endDate);
    const durationInDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
    const durationInMonths = Math.ceil(durationInDays / 30);
    
    
    // Update monthly payment plan options
    const monthlyOptions = $('#payment_plan option[data-months]');
    monthlyOptions.each(function() {
        const option = $(this);
        const months = parseInt(option.attr('data-months'));
        const originalText = option.text().replace(' (Not Applicable)', '');
        
        if (months > durationInMonths) {
            option.text(originalText + ' (Not Applicable)');
            option.prop('disabled', true);
            option.addClass('text-muted');
        } else {
            option.text(originalText);
            option.prop('disabled', false);
            option.removeClass('text-muted');
        }
    });
    
    // Clear current selection if it's now disabled
    const currentSelection = $('#payment_plan').val();
    if (currentSelection && $('#payment_plan option:selected').prop('disabled')) {
        $('#payment_plan').val('');
        renderPaymentBreakdown();
    }
}

// On page load, initialize
$(document).ready(function() {
    $('#project_end_date').attr('readonly', true);
    updateEndDate();
    updatePaymentPlanOptions();
});

// ---- block 2 ----
// Payment Plan Breakdown Logic
function calculateProjectCosts() {
    let materialCosts = [];
    let totalMaterialCost = 0;
    let laborFee = 0;
    
    // Calculate material costs from selected suppliers
    $('.supplier-select').each(function() {
        const materialId = $(this).data('material-id');
        const supplierId = $(this).val();
        
        if (supplierId) {
            // Get material details from the table
            const materialRow = $(this).closest('tr');
            const materialName = materialRow.find('td:first strong').text();
            
            // Get supplier price from the option text
            const selectedOption = $(this).find('option:selected');
            const optionText = selectedOption.text();
            const priceMatch = optionText.match(/₱([\d,]+\.?\d*)/);
            
            if (priceMatch) {
                const unitPrice = parseFloat(priceMatch[1].replace(/,/g, ''));
                
                // Get quantity from the material data
                let quantity = 1; // default
                if (window.materialSupplierResponses && window.materialSupplierResponses[materialId]) {
                    const materialData = window.materialSupplierResponses[materialId][0];
                    if (materialData && materialData.quantity) {
                        quantity = parseFloat(materialData.quantity);
                    }
                }
                
                const totalPrice = unitPrice * quantity;
                totalMaterialCost += totalPrice;
                
                materialCosts.push({
                    material: materialName,
                    unitPrice: unitPrice,
                    quantity: quantity,
                    totalPrice: totalPrice
                });
            }
        }
    });
    
    // Calculate labor fee (₱100 per day)
    const startDate = $('#project_start_date').val();
    const endDate = $('#project_end_date').val();
    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const durationInDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
        laborFee = durationInDays * 100; // ₱100 per day
    }
    
    const grandTotal = totalMaterialCost + laborFee;
    
    return {
        materialCosts: materialCosts,
        totalMaterialCost: totalMaterialCost,
        laborFee: laborFee,
        grandTotal: grandTotal
    };
}

function getFinalAmount() {
    const costs = calculateProjectCosts();
    return costs.grandTotal;
}

function renderMaterialsCostBreakdown() {
    const costs = calculateProjectCosts();
    const materialsBreakdownDiv = $('#materials-cost-breakdown');
    const materialsTableDiv = $('#materials-cost-table');
    
    if (costs.materialCosts.length > 0) {
        let html = '<table class="table table-bordered"><thead><tr><th>Material</th><th>Unit Price</th><th>Quantity</th><th>Total</th></tr></thead><tbody>';
        costs.materialCosts.forEach(item => {
            html += `<tr><td>${item.material}</td><td>₱${item.unitPrice.toLocaleString(undefined, {minimumFractionDigits:2})}</td><td>${item.quantity}</td><td>₱${item.totalPrice.toLocaleString(undefined, {minimumFractionDigits:2})}</td></tr>`;
        });
        html += `<tr class="fw-bold table-primary"><td colspan="3">Total Material Cost</td><td>₱${costs.totalMaterialCost.toLocaleString(undefined, {minimumFractionDigits:2})}</td></tr>`;
        html += '</tbody></table>';
        
        materialsTableDiv.html(html);
        materialsBreakdownDiv.show();
    } else {
        materialsBreakdownDiv.hide();
    }
}

function renderPaymentBreakdown() {
    const plan = $('#payment_plan').val();
    const breakdownDiv = $('#payment-breakdown');
    const costs = calculateProjectCosts();
    const finalAmount = costs.grandTotal;
    
    breakdownDiv.empty();
    
    // Show simplified cost breakdown in modal
    let html = '<div class="card mb-3"><div class="card-header"><h6 class="mb-0">Project Cost Summary</h6></div><div class="card-body">';
    
    // Material costs summary
    html += `<div class="row mb-3"><div class="col-md-6"><strong>Total Material Cost:</strong></div><div class="col-md-6">₱${costs.totalMaterialCost.toLocaleString(undefined, {minimumFractionDigits:2})}</div></div>`;
    
    // Labor fee
    const startDate = $('#project_start_date').val();
    const endDate = $('#project_end_date').val();
    let durationInDays = 0;
    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        durationInDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
    }
    
    html += `<div class="row mb-3"><div class="col-md-6"><strong>Labor Fee:</strong></div><div class="col-md-6">₱100 × ${durationInDays} days = ₱${costs.laborFee.toLocaleString(undefined, {minimumFractionDigits:2})}</div></div>`;
    html += `<div class="row mb-3"><div class="col-md-6"><strong>Grand Total:</strong></div><div class="col-md-6"><strong class="text-primary fs-5">₱${finalAmount.toLocaleString(undefined, {minimumFractionDigits:2})}</strong></div></div>`;
    html += '</div></div>';
    
    // Payment plan breakdown
    if (!plan) {
        html += '<div class="alert alert-info">Please select a payment plan to see the breakdown.</div>';
        breakdownDiv.html(html);
        return;
    }
    
    let rows = [];
    if (plan === '30% down, 40% halfway, 30% on completion') {
        rows = [
            { label: 'Downpayment', percent: 30 },
            { label: 'Halfway Payment', percent: 40 },
            { label: 'Completion Payment', percent: 30 },
        ];
    } else if (plan === '50/50') {
        rows = [
            { label: 'Downpayment', percent: 50 },
            { label: 'Completion Payment', percent: 50 },
        ];
    } else if (plan === 'Full upon completion') {
        rows = [
            { label: 'Completion Payment', percent: 100 },
        ];
    } else if (plan === 'milestone') {
        rows = [
            { label: 'Downpayment', percent: 20 },
            { label: 'After Foundation', percent: 20 },
            { label: 'After Structure', percent: 30 },
            { label: 'Completion Payment', percent: 30 },
        ];
    } else if (plan === 'monthly3') {
        let months = 3;
        let percent = 100 / months;
        for (let i = 1; i <= months; i++) {
            rows.push({ label: `Month ${i} Payment`, percent: percent });
        }
    } else if (plan === 'monthly6') {
        let months = 6;
        let percent = 100 / months;
        for (let i = 1; i <= months; i++) {
            rows.push({ label: `Month ${i} Payment`, percent: percent });
        }
    } else if (plan === 'monthly12') {
        let months = 12;
        let percent = 100 / months;
        for (let i = 1; i <= months; i++) {
            rows.push({ label: `Month ${i} Payment`, percent: percent });
        }
    }
    
    // Render payment breakdown table
    html += '<div class="card"><div class="card-header"><h6 class="mb-0">Payment Schedule</h6></div><div class="card-body">';
    html += '<table class="table table-bordered"><thead><tr><th>Stage</th><th>Percent</th><th>Amount (₱)</th></tr></thead><tbody>';
    let total = 0;
    rows.forEach(row => {
        let amt = finalAmount ? Math.round(finalAmount * row.percent) / 100 : 0;
        total += amt;
        html += `<tr><td>${row.label}</td><td>${row.percent}%</td><td>₱${amt.toLocaleString(undefined, {minimumFractionDigits:2})}</td></tr>`;
    });
    html += `<tr class="fw-bold"><td>Total</td><td>100%</td><td>₱${finalAmount ? finalAmount.toLocaleString(undefined, {minimumFractionDigits:2}) : '0.00'}</td></tr>`;
    html += '</tbody></table></div></div>';
    
    if (finalAmount === 0) {
        html += '<div class="alert alert-warning mt-2">Select suppliers to see the actual payment breakdown based on the final amount.</div>';
    }
    
    breakdownDiv.html(html);
}

$(document).on('change', '#payment_plan', function() {
    renderPaymentBreakdown();
});

// Update payment breakdown when suppliers are selected
$(document).on('change', '.supplier-select', function() {
    renderMaterialsCostBreakdown();
    renderPaymentBreakdown();
});

// Update payment breakdown when dates change
$(document).on('change', '#project_start_date, #project_end_date', function() {
    renderPaymentBreakdown();
});

$(document).ready(function() {
    renderMaterialsCostBreakdown();
    renderPaymentBreakdown();
});

// used by inline handlers / other scripts in the view
Object.assign(window, { renderPaymentBreakdown });
