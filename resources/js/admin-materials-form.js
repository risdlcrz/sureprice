// Custom JS extracted from admin/materials/form.blade.php

$(document).ready(function() {
    // Initialize Select2 for multiple selection
    $('.select2-multiple').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Select scope types...',
        allowClear: true
    });

    $('#suppliers').select2({
        placeholder: 'Select suppliers...',
        allowClear: true,
        templateResult: formatSupplier,
        templateSelection: formatSupplierSelection
    });

    function formatSupplier(supplier) {
        if (supplier.loading) {
            return supplier.text;
        }
        return $('<span>' + supplier.text + '</span>');
    }

    function formatSupplierSelection(supplier) {
        return supplier.text;
    }

    // Form validation
    const form = document.getElementById('materialForm');
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    }

    // Image removal function
    window.removeImage = function(imagePath) {
        if (confirm('Are you sure you want to remove this image?')) {
            fetch(`/api/materials/remove-image`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    material_id: window.materialId || "",
                    image_path: imagePath
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const imageContainer = document.querySelector(`img[src*="${imagePath}"]`).closest('.image-container');
                    imageContainer.remove();
                } else {
                    alert('Failed to remove image');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error removing image');
            });
        }
    };

    $('#category').change(function() {
        if ($(this).val() === 'other') {
            $('#custom_category').show().prop('required', true);
            $('#custom_category_preview').show();
        } else {
            $('#custom_category').hide().prop('required', false);
            $('#custom_category_preview').hide();
        }
    });

    $('#custom_category').on('input', function() {
        var val = $(this).val();
        if (val) {
            $('#custom_category_preview').text('Selected Category: ' + val);
        } else {
            $('#custom_category_preview').text('');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const isPerAreaCheckbox = document.getElementById('is_per_area');
        const coverageRateGroup = document.getElementById('coverage_rate_group');
        const minimumQuantityGroup = document.getElementById('minimum_quantity_group');
        const isWallMaterialGroup = document.getElementById('is_wall_material_group');

        function toggleVisibility() {
            if (isPerAreaCheckbox.checked) {
                coverageRateGroup.style.display = 'block';
                minimumQuantityGroup.style.display = 'none';
                isWallMaterialGroup.style.display = 'block'; // Always show for per-area as it's relevant
            } else {
                coverageRateGroup.style.display = 'none';
                minimumQuantityGroup.style.display = 'block';
                isWallMaterialGroup.style.display = 'none'; // Hide if not per area
            }
        }

        isPerAreaCheckbox.addEventListener('change', toggleVisibility);

        // Initial call to set correct visibility based on current state
        toggleVisibility();
    });

    function slugify(text) {
        return text.toString().toLowerCase().replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-')
            .replace(/^-+/, '')
            .replace(/-+$/, '');
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category');
        const codeInput = document.getElementById('code');
        function updateCode() {
            const cat = categorySelect.options[categorySelect.selectedIndex]?.text || '';
            if (cat) {
                codeInput.value = (cat.substring(0,3).toUpperCase() + '-' + Math.floor(1000 + Math.random() * 9000));
            } else {
                codeInput.value = '';
            }
        }
        categorySelect.addEventListener('change', updateCode);
    });

    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category');
        const customCategoryInput = document.getElementById('custom_category');
        function toggleCustomCategory() {
            if (categorySelect.value === 'other') {
                customCategoryInput.style.display = '';
                customCategoryInput.required = true;
            } else {
                customCategoryInput.style.display = 'none';
                customCategoryInput.required = false;
                customCategoryInput.value = '';
            }
        }
        categorySelect.addEventListener('change', toggleCustomCategory);
        // On page load
        toggleCustomCategory();
    });
}); 