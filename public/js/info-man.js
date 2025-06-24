document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // File upload display logic
    const fileInput = document.getElementById('csvUpload');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const clearFileBtn = document.getElementById('clearFileBtn');

    if (fileInput && fileNameDisplay && clearFileBtn) {
        fileInput.addEventListener('change', updateFileDisplay);
        clearFileBtn.addEventListener('click', clearFileInput);
    }

    function updateFileDisplay() {
        if (fileInput.files && fileInput.files.length > 0) {
            fileNameDisplay.textContent = fileInput.files[0].name;
            fileNameDisplay.style.color = '#333';
            clearFileBtn.style.display = 'flex';
        } else {
            fileNameDisplay.textContent = 'Choose file';
            fileNameDisplay.style.color = '#666';
            clearFileBtn.style.display = 'none';
        }
    }

    function clearFileInput(e) {
        e.preventDefault();
        e.stopPropagation();
        fileInput.value = '';
        updateFileDisplay();
    }

    // Mobile menu toggle functionality
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('expanded');
        });
    }

    // Handle form submission feedback
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            }
        });
    }
});