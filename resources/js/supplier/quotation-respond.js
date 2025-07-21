// quotation-respond.js

document.addEventListener('DOMContentLoaded', function () {
    function parseNumber(val) {
        const n = parseFloat(val);
        return isNaN(n) ? 0 : n;
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
            const discountAmountInput = document.getElementById('discount-amount');
            if (discountPercentageInput && !discountPercentageInput.closest('.discount-option').style.display.includes('none')) {
                const percent = parseNumber(discountPercentageInput.value);
                discount = subtotal * (percent / 100);
            } else if (discountAmountInput && !discountAmountInput.closest('.discount-option').style.display.includes('none')) {
                discount = parseNumber(discountAmountInput.value);
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
        discountType.addEventListener('change', updateSummary);
    }
    const discountPercentage = document.getElementById('discount-percentage');
    if (discountPercentage) {
        discountPercentage.addEventListener('input', updateSummary);
    }
    const discountAmount = document.getElementById('discount-amount');
    if (discountAmount) {
        discountAmount.addEventListener('input', updateSummary);
    }

    // Initial calculation
    updateSummary();
}); 