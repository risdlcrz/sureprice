// Extracted from resources/views/supplier/materials/create.blade.php
const __page = window.__page || {};
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_id');
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

    // For linking existing materials
    const materialSearchInput = document.getElementById('material_search');
    const materialIdInput = document.getElementById('material_id');
    const materialDetailsDiv = document.getElementById('material_details');
    const searchResultsDiv = document.getElementById('material_search_results');

    materialSearchInput.addEventListener('keyup', function() {
        const searchTerm = this.value;
        
        if (searchTerm.length < 2) {
            searchResultsDiv.innerHTML = '';
            materialDetailsDiv.style.display = 'none';
            materialIdInput.value = '';
            return; 
        }

        fetch(`${__page.create0_0}?term=${searchTerm}`)
            .then(response => response.json())
            .then(data => {
                searchResultsDiv.innerHTML = ''; // Clear previous results
                if (data.length > 0) {
                    data.forEach(material => {
                        const resultItem = document.createElement('a');
                        resultItem.href = '#';
                        resultItem.classList.add('list-group-item', 'list-group-item-action');
                        resultItem.innerHTML = `<strong>${material.name}</strong> <small class="text-muted">(${material.code})</small>`;
                        
                        resultItem.addEventListener('click', function(e) {
                            e.preventDefault();
                            // Populate the form
                            materialSearchInput.value = material.name;
                            materialIdInput.value = material.id;
                            document.getElementById('selected_material_name').innerText = material.name;
                            document.getElementById('selected_material_code').innerText = material.code;
                            document.getElementById('selected_material_unit').innerText = material.unit;
                            const srp = material.srp_price || material.base_price || '0.00';
                            document.getElementById('selected_material_srp').innerText = parseFloat(srp).toFixed(2);
                            
                            // Show details and hide results
                            materialDetailsDiv.style.display = 'block';
                            searchResultsDiv.innerHTML = '';
                        });
                        
                        searchResultsDiv.appendChild(resultItem);
                    });
                } else {
                    searchResultsDiv.innerHTML = '<div class="list-group-item">No materials found.</div>';
                }
            });
    });
});
