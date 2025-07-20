// Custom JS extracted from admin/inquiries/form.blade.php

window.materialSearchUrl = "/materials/search";

document.addEventListener('DOMContentLoaded', function() {
    // Initialize form validation
    const form = document.getElementById('inquiryForm');
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    }

    // Material search functionality
    const materialSearch = document.getElementById('materialSearch');
    const searchMaterialBtn = document.getElementById('searchMaterialBtn');
    const materialSearchResults = document.getElementById('materialSearchResults');
    const selectedMaterials = document.getElementById('selectedMaterials');
    let searchTimeout;

    if (materialSearch && searchMaterialBtn && materialSearchResults) {
        // Show materials when input is focused
        materialSearch.addEventListener('focus', () => {
            searchMaterials();
        });

        materialSearch.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(searchMaterials, 300);
        });

        searchMaterialBtn.addEventListener('click', searchMaterials);
    }

    function searchMaterials() {
        const query = materialSearch.value.trim();
        
        materialSearchResults.innerHTML = '<div class="p-2">Loading materials...</div>';
        materialSearchResults.style.display = 'block';

        fetch(`${window.materialSearchUrl}?query=${encodeURIComponent(query)}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.length > 0) {
                materialSearchResults.innerHTML = data.map(material => `
                    <div class="material-result" data-material='${JSON.stringify(material)}'>
                        <strong>${material.name}</strong><br>
                        <small>${material.description || ''} - ${material.unit}</small>
                    </div>
                `).join('');

                // Add click handlers
                materialSearchResults.querySelectorAll('.material-result').forEach(result => {
                    result.addEventListener('click', () => addMaterial(JSON.parse(result.dataset.material)));
                });
            } else {
                materialSearchResults.innerHTML = '<div class="p-2">No materials found</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            materialSearchResults.innerHTML = '<div class="p-2 text-danger">Error searching materials</div>';
        });
    }

    function addMaterial(material) {
        // Check if material already exists
        if (document.querySelector(`input[name="materials[${material.id}][id]"]`)) {
            alert('This material is already added');
            return;
        }

        const materialHtml = `
            <div class="material-item card mb-2">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <strong>${material.name}</strong>
                            <input type="hidden" name="materials[${material.id}][id]" value="${material.id}">
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control form-control-sm" 
                                name="materials[${material.id}][quantity]" 
                                placeholder="Quantity" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" 
                                name="materials[${material.id}][notes]" 
                                placeholder="Specifications/Notes">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm remove-material">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        selectedMaterials.insertAdjacentHTML('beforeend', materialHtml);
        materialSearch.value = '';
        materialSearchResults.style.display = 'none';

        // Add remove functionality to the new material
        const newMaterial = selectedMaterials.lastElementChild;
        newMaterial.querySelector('.remove-material').addEventListener('click', function() {
            newMaterial.remove();
        });
    }

    // Add remove functionality to existing materials
    document.querySelectorAll('.remove-material').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.material-item').remove();
        });
    });

    // Close search results when clicking outside
    document.addEventListener('click', (e) => {
        if (!materialSearchResults.contains(e.target) && e.target !== materialSearch && e.target !== searchMaterialBtn) {
            materialSearchResults.style.display = 'none';
        }
    });

    // Attachment removal function
    window.removeAttachment = function(attachmentId) {
        if (confirm('Are you sure you want to remove this attachment?')) {
            fetch(`/api/inquiries/remove-attachment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    inquiry_id: window.inquiryId || "",
                    attachment_id: attachmentId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const attachmentElement = document.querySelector(`[onclick="removeAttachment('${attachmentId}')"]`).closest('.attachment-item');
                    attachmentElement.remove();
                } else {
                    alert('Failed to remove attachment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error removing attachment');
            });
        }
    };
}); 