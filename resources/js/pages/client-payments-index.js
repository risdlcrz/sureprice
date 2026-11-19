// Extracted from resources/views/client/payments/index.blade.php
// Comprehensive modal stability fix
document.addEventListener('DOMContentLoaded', function() {
    // Disable all hover effects when modal is open
    function disableHoverEffects() {
        const style = document.createElement('style');
        style.id = 'modal-stability-fix';
        style.textContent = `
            * {
                transition: none !important;
                animation: none !important;
                transform: none !important;
            }
            .modal * {
                transition: none !important;
                animation: none !important;
                transform: none !important;
            }
        `;
        document.head.appendChild(style);
    }

    // Enable hover effects when modal is closed
    function enableHoverEffects() {
        const style = document.getElementById('modal-stability-fix');
        if (style) {
            style.remove();
        }
    }

    // Listen for modal events
    document.addEventListener('show.bs.modal', function() {
        disableHoverEffects();
    });

    document.addEventListener('hidden.bs.modal', function() {
        enableHoverEffects();
    });

    // Ensure modals work properly
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        // Remove any existing event listeners
        const newModal = modal.cloneNode(true);
        modal.parentNode.replaceChild(newModal, modal);
    });

    // Simple form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const invalidFields = form.querySelectorAll('.is-invalid');
            if (invalidFields.length > 0) {
                e.preventDefault();
                alert('Please correct the errors before submitting.');
            }
        });
    });
});

// Prevent any mouse events from interfering with modal
document.addEventListener('mouseover', function(e) {
    if (e.target.closest('.modal')) {
        e.stopPropagation();
    }
});

document.addEventListener('mouseenter', function(e) {
    if (e.target.closest('.modal')) {
        e.stopPropagation();
    }
});

document.addEventListener('mouseleave', function(e) {
    if (e.target.closest('.modal')) {
        e.stopPropagation();
    }
});

// Ensure modal backdrop is stable
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-backdrop')) {
        e.stopPropagation();
    }
});
