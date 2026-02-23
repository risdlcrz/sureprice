// quotation-respond.js

document.addEventListener('DOMContentLoaded', function () {
    function parseNumber(val) {
        const n = parseFloat(val);
        return isNaN(n) ? 0 : n;
    }

    // Discount rules from backend (should match QuotationResponse::DISCOUNT_RULES)
    const discountRules = {
        'bulk': { max_percentage: 25, description: 'Bulk Order Discount: Applies when the client orders in large quantities. <br><strong>Requirement:</strong> Minimum order quantity must be met (e.g., 100+ units).' },
        'seasonal': { max_percentage: 15, description: 'Seasonal Promotion: Special discount for a limited time or season. <br><strong>Requirement:</strong> Only available during the promotional period (e.g., summer sale, holiday promo).' },
        'loyalty': { max_percentage: 10, description: 'Loyalty Discount: For repeat or long-term clients. <br><strong>Requirement:</strong> Client must have completed at least 3 previous orders or be a registered partner.' },
        'new_customer': { max_percentage: 20, description: 'New Customer Discount: For first-time clients only. <br><strong>Requirement:</strong> Client must not have any previous orders.' },
        'payment_terms': { max_percentage: 5, description: 'Early Payment Discount: Applies if the client pays before the due date. <br><strong>Requirement:</strong> Payment must be made within 7 days of invoice.' },
        'delivery_terms': { max_percentage: 8, description: 'Flexible Delivery Discount: Discount for clients who accept flexible delivery schedules. <br><strong>Requirement:</strong> Client agrees to a delivery window instead of a fixed date.' },
        'custom': { max_percentage: 30, description: 'Custom Discount: Any other discount not listed above. <br><strong>Requirement:</strong> Please specify the reason and eligibility in the notes.' },
        'none': { max_percentage: 0, description: 'No discount will be applied to this quotation.' }
    };

    // Global variables to track the current state
    let currentDiscountType = 'none';
    let descriptionMonitorInterval = null;

    // Function to force set the discount description
    function forceSetDiscountDescription() {
        const descDiv = document.getElementById('discount-description');
        const descText = document.getElementById('discount-description-text');
        
        if (!descDiv || !descText) {

            return false;
        }

        // Always ensure the div is visible
        descDiv.style.display = 'block';
        descDiv.style.visibility = 'visible';
        descDiv.style.opacity = '1';
        descDiv.style.height = 'auto';
        descDiv.style.overflow = 'visible';

        // Get current discount type
        const discountType = document.getElementById('discount-type')?.value || 'none';
        
        // Set the description based on discount type
        if (discountRules[discountType]) {
            descText.innerHTML = discountRules[discountType].description;
            currentDiscountType = discountType;

            return true;
        } else if (discountType === 'none') {
            descText.innerHTML = 'No discount will be applied to this quotation.';
            currentDiscountType = 'none';

            return true;
        } else {
            descText.innerHTML = 'Discount information for this type is not available. Please contact support for details.';
            currentDiscountType = discountType;

            return true;
        }
    }

    // Function to monitor and maintain the description
    function startDescriptionMonitor() {
        if (descriptionMonitorInterval) {
            clearInterval(descriptionMonitorInterval);
        }

        descriptionMonitorInterval = setInterval(function() {
            const descDiv = document.getElementById('discount-description');
            const descText = document.getElementById('discount-description-text');
            const discountType = document.getElementById('discount-type')?.value || 'none';
            
            // Check if description div is hidden
            if (descDiv && (descDiv.style.display === 'none' || descDiv.style.visibility === 'hidden' || descDiv.style.opacity === '0')) {

                forceSetDiscountDescription();
            }
            
            // Check if description text is empty or wrong
            if (descText && (descText.innerHTML === '' || descText.innerHTML === 'Please select a discount type to see its description.')) {

                forceSetDiscountDescription();
            }
            
            // Check if discount type changed
            if (discountType !== currentDiscountType) {

                forceSetDiscountDescription();
            }
        }, 500); // Check every 500ms
    }

    function updateDiscountFields() {
        const discountType = document.getElementById('discount-type')?.value || 'none';
        const percentDiv = document.getElementById('percentage-discount');
        const percentInput = document.getElementById('discount-percentage');
        const maxPercentSpan = document.getElementById('max-percentage');

        // Add null checks to prevent errors if elements are missing
        if (!percentDiv || !percentInput || !maxPercentSpan) return;

        // Force set the description first
        forceSetDiscountDescription();

        if (discountType !== 'none') {
            percentDiv.style.display = '';
            percentInput.disabled = false;
            if (discountRules[discountType]) {
                maxPercentSpan.textContent = discountRules[discountType].max_percentage;
                percentInput.max = discountRules[discountType].max_percentage;
            } else {
                maxPercentSpan.textContent = '0';
                percentInput.max = 100;
            }
        } else {
            percentDiv.style.display = 'none';
            percentInput.disabled = true;
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
    
    // Start the description monitor
    startDescriptionMonitor();
    
    // Additional safeguards
    setTimeout(forceSetDiscountDescription, 100);
    setTimeout(forceSetDiscountDescription, 500);
    setTimeout(forceSetDiscountDescription, 1000);
    
    // Set up a mutation observer as an additional safeguard
    const descDiv = document.getElementById('discount-description');
    if (descDiv) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                    const style = descDiv.style;
                    if (style.display === 'none' || style.visibility === 'hidden' || style.opacity === '0') {

                        forceSetDiscountDescription();
                    }
                }
                if (mutation.type === 'childList') {

                    setTimeout(forceSetDiscountDescription, 50);
                }
            });
        });
        
        observer.observe(descDiv, {
            attributes: true,
            childList: true,
            subtree: true,
            attributeFilter: ['style', 'class']
        });
    }
    
    // Override any attempts to hide the description
    const originalSetAttribute = Element.prototype.setAttribute;
    Element.prototype.setAttribute = function(name, value) {
        if (this.id === 'discount-description' && name === 'style' && value.includes('display: none')) {

            return;
        }
        return originalSetAttribute.call(this, name, value);
    };
}); 