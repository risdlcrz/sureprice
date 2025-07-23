// quotation-respond.js

document.addEventListener('DOMContentLoaded', function () {
    function parseNumber(val) {
        const n = parseFloat(val);
        return isNaN(n) ? 0 : n;
    }

    // Discount rules from backend (should match QuotationResponse::DISCOUNT_RULES)
    const discountRules = {
        'bulk': { max_percentage: 25, description: 'Bulk order discount for large quantities' },
        'seasonal': { max_percentage: 15, description: 'Seasonal promotion discount' },
        'loyalty': { max_percentage: 10, description: 'Loyalty discount for repeat customers' },
        'new_customer': { max_percentage: 20, description: 'New customer welcome discount' },
        'payment_terms': { max_percentage: 5, description: 'Early payment discount' },
        'delivery_terms': { max_percentage: 8, description: 'Flexible delivery terms discount' },
        'custom': { max_percentage: 30, description: 'Custom discount (requires approval)' },
        'none': { max_percentage: 0, description: 'No discount' }
    };

    function updateDiscountFields() {
        const discountType = document.getElementById('discount-type')?.value || 'none';
        const percentDiv = document.getElementById('percentage-discount');
        const percentInput = document.getElementById('discount-percentage');
        const maxPercentSpan = document.getElementById('max-percentage');
        const descDiv = document.getElementById('discount-info');
        const descText = document.getElementById('discount-description');

        if (discountType !== 'none') {
            percentDiv.style.display = '';
            percentInput.disabled = false;
            if (discountRules[discountType]) {
                maxPercentSpan.textContent = discountRules[discountType].max_percentage;
                descDiv.style.display = '';
                descText.textContent = discountRules[discountType].description;
                percentInput.max = discountRules[discountType].max_percentage;
            } else {
                maxPercentSpan.textContent = '0';
                descDiv.style.display = 'none';
                percentInput.max = 100;
            }
        } else {
            percentDiv.style.display = 'none';
            percentInput.disabled = true;
            descDiv.style.display = 'none';
        }
    }

    function updateSummary() {
        let subtotal = 0;
        document.querySelectorAll('.material-price').forEach(function (input) {
            const unitPrice = parseNumber(input.value);
            const quantity = parseNumber(input.getAttribute('data-quantity'));
            subtotal += unitPrice * quantity;
        });

        // Discount logic
        let discountType = document.getElementById('discount-type')?.value || 'none';
        let discount = 0;
        let finalAmount = subtotal;

        if (discountType !== 'none') {
            const discountPercentageInput = document.getElementById('discount-percentage');
            if (discountPercentageInput && !discountPercentageInput.disabled) {
                const percent = parseNumber(discountPercentageInput.value);
                discount = subtotal * (percent / 100);
            }
            if (discount > subtotal) discount = subtotal;
            finalAmount = subtotal - discount;
        }

        document.getElementById('subtotal').textContent = `₱${subtotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        document.getElementById('discount-display').textContent = `₱${discount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        document.getElementById('final-amount').textContent = `₱${finalAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    }

    // Listen for changes in quoted price fields
    document.querySelectorAll('.material-price').forEach(function (input) {
        input.addEventListener('input', updateSummary);
    });

    // Listen for changes in discount fields
    const discountType = document.getElementById('discount-type');
    if (discountType) {
        discountType.addEventListener('change', function() {
            updateDiscountFields();
            updateSummary();
        });
    }
    const discountPercentage = document.getElementById('discount-percentage');
    if (discountPercentage) {
        discountPercentage.addEventListener('input', updateSummary);
    }

    // Initial setup
    updateDiscountFields();
    updateSummary();
}); 