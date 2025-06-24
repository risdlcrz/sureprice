// Global chart variable
let inventoryChart = null;
let updateInterval = null;
let updateRetryCount = 0;
const MAX_RETRIES = 5;

// Product Details Chart
let productDetailsChart = null;

function loadChartData() {
    try {
        const savedData = localStorage.getItem('inventoryChartData');
        if (savedData) {
            return JSON.parse(savedData);
        }
    } catch (error) {
        console.error('Error loading chart data:', error);
    }
    return {
        labels: [],
        data: [],
        colors: []
    };
}

function saveChartData() {
    if (productQualityGradeEl) productQualityGradeEl.textContent = data.qualityGrade;
    
    const productOriginEl = document.getElementById('productOrigin');
    if (productOriginEl) productOriginEl.textContent = data.origin;
    
    // Technical Information
    const productModelNumberEl = document.getElementById('productModelNumber');
    const productCertificationEl = document.getElementById('productCertification');
    const productBatchNumberEl = document.getElementById('productBatchNumber');
    const productExpiryDateEl = document.getElementById('productExpiryDate');
    
    console.log('Technical elements found:', {
        productModelNumber: productModelNumberEl ? 'YES' : 'NO',
        productCertification: productCertificationEl ? 'YES' : 'NO',
        productBatchNumber: productBatchNumberEl ? 'YES' : 'NO',
        productExpiryDate: productExpiryDateEl ? 'YES' : 'NO'
    });
    
    if (productModelNumberEl) productModelNumberEl.textContent = data.model;
    if (productCertificationEl) productCertificationEl.textContent = data.certification;
    if (productBatchNumberEl) productBatchNumberEl.textContent = data.batch;
    if (productExpiryDateEl) productExpiryDateEl.textContent = data.expiry;
    
    // Additional Information
    const productSpecificationsEl = document.getElementById('productSpecifications');
    const productWarrantyEl = document.getElementById('productWarranty');
    
    if (productSpecificationsEl) productSpecificationsEl.textContent = data.specifications;
    if (productWarrantyEl) productWarrantyEl.textContent = data.warranty;
}

function saveChartDataToStorage() {
    if (!inventoryChart) return;
    
    try {
        const chartData = {
            labels: inventoryChart.data.labels,
            data: inventoryChart.data.datasets[0].data,
            colors: inventoryChart.data.datasets[0].pointBackgroundColor
        };
        localStorage.setItem('inventoryChartData', JSON.stringify(chartData));
    } catch (error) {
        console.error('Error saving chart data:', error);
    }
}

function initChart() {
    const ctx = document.getElementById('inventoryStatusChart');
    if (!ctx) {
        console.error('Chart canvas not found');
        return;
    }
    
    if (inventoryChart) {
        inventoryChart.destroy();
    }

    console.log('Initializing chart...');
    
    // Initialize with empty data
    inventoryChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['No Data'],
            datasets: [{
                label: 'Stock Level',
                data: [0],
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                borderWidth: 2,
                tension: 0.1,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: ['#dc3545'],
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return `Stock Level: ${context.parsed.y.toFixed(1)}%`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });

    // Update the chart immediately
    updateGraphAndStats();
    
    // Set up interval for updates
    setInterval(updateGraphAndStats, 900000); // 15 minutes
}

function updateGraphAndStats() {
    if (!inventoryChart) {
        console.error('Chart not initialized');
        return;
    }

    const table = document.getElementById('inventoryTable');
    if (!table) {
        console.error('Inventory table not found');
        return;
    }

    const rows = table.querySelectorAll('tbody tr');
    if (!rows.length) {
        console.warn('No table rows found');
        
        // Update chart with "No Data" state
        inventoryChart.data.labels = ['No Data'];
        inventoryChart.data.datasets[0].data = [0];
        inventoryChart.data.datasets[0].pointBackgroundColor = ['#dc3545'];
        inventoryChart.update();
        
        // Update stat counters if they exist
        const outOfStockEl = document.getElementById('outOfStockCount');
        const lowStockEl = document.getElementById('lowStockCount');
        if (outOfStockEl) outOfStockEl.textContent = '0';
        if (lowStockEl) lowStockEl.textContent = '0';
        
        // Clear lists if they exist
        const criticalList = document.getElementById('criticalItemList');
        const lowStockList = document.getElementById('lowStockItemList');
        if (criticalList) criticalList.innerHTML = '';
        if (lowStockList) lowStockList.innerHTML = '';
        
        return;
    }

    // Process data from table rows
    let totalStockPercentage = 0;
    let outOfStock = 0;
    let lowStock = 0;
    const criticalItems = [];
    const lowStockItems = [];
    
    rows.forEach(row => {
        const stock = parseInt(row.cells[2]?.textContent);
        const threshold = parseInt(row.cells[5]?.textContent);
        const name = row.cells[0]?.textContent?.trim();
        
        if (!isNaN(stock) && !isNaN(threshold) && name) {
            const maxStock = threshold * 2;
            const percentage = Math.min((stock / maxStock) * 100, 100);
            totalStockPercentage += percentage;
            
            if (stock === 0) {
                outOfStock++;
                criticalItems.push(name);
            } else if (stock <= threshold) {
                lowStock++;
                lowStockItems.push(name);
            }
        }
    });

    // Calculate average stock level
    const averageStock = rows.length > 0 ? totalStockPercentage / rows.length : 0;

    // Update chart
    const now = new Date();
    const timeLabel = now.toLocaleTimeString();
    
    if (inventoryChart.data.labels[0] === 'No Data') {
        inventoryChart.data.labels = [timeLabel];
        inventoryChart.data.datasets[0].data = [averageStock];
    } else {
        inventoryChart.data.labels.push(timeLabel);
        inventoryChart.data.datasets[0].data.push(averageStock);
        
        // Keep only last 10 points
        if (inventoryChart.data.labels.length > 10) {
            inventoryChart.data.labels.shift();
            inventoryChart.data.datasets[0].data.shift();
        }
    }
    
    inventoryChart.update();

    // Update counters and lists
    const outOfStockEl = document.getElementById('outOfStockCount');
    const lowStockEl = document.getElementById('lowStockCount');
    const criticalList = document.getElementById('criticalItemList');
    const lowStockList = document.getElementById('lowStockItemList');

    if (outOfStockEl) outOfStockEl.textContent = outOfStock;
    if (lowStockEl) lowStockEl.textContent = lowStock;
    
    if (criticalList) {
        criticalList.innerHTML = criticalItems
            .map(item => `<li class="list-group-item">${item}</li>`)
            .join('');
    }
    
    if (lowStockList) {
        lowStockList.innerHTML = lowStockItems
            .map(item => `<li class="list-group-item">${item}</li>`)
            .join('');
    }
}

function saveSwitchStates() {
    const states = {};
    document.querySelectorAll('.custom-switch input').forEach(input => {
        if (input.dataset.id) {
            states[input.dataset.id] = input.checked;
        }
    });
    localStorage.setItem('switchStates', JSON.stringify(states));
}

function loadSwitchStates() {
    const states = JSON.parse(localStorage.getItem('switchStates') || '{}');
    document.querySelectorAll('.custom-switch input').forEach(input => {
        if (input.dataset.id && states.hasOwnProperty(input.dataset.id)) {
            input.checked = states[input.dataset.id];
        }
    });
}

function initProductDetailsChart(productName, stockData) {
    const ctx = document.getElementById('productStockChart');
    if (!ctx) return;
    
    if (productDetailsChart) {
        productDetailsChart.destroy();
    }

    const now = new Date();
    const labels = [];
    const data = [];
    const colors = [];

    // Generate 12 time points (last 12 hours)
    for (let i = 11; i >= 0; i--) {
        const time = new Date(now - i * 3600000); // Subtract hours
        labels.push(time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' }));
        
        // Use the current stock value for all points (since we don't have historical data)
        const stockPercentage = (stockData.stock / (stockData.threshold * 2)) * 100;
        data.push(stockPercentage);
        
        // Determine color based on stock level
        const color = stockPercentage < 20 ? '#dc3545' : 
                     stockPercentage < 50 ? '#ffc107' : 
                     stockPercentage < 80 ? '#0d6efd' : '#28a745';
        colors.push(color);
    }

    productDetailsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Stock Level',
                data: data,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                borderWidth: 3,
                tension: 0.1,
                pointRadius: 6,
                pointHoverRadius: 8,
                pointBackgroundColor: colors,
                fill: true,
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return `Stock Level: ${context.parsed.y.toFixed(1)}%`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        stepSize: 20,
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize chart first
    initChart();
    
    // Load switch states
    loadSwitchStates();
    
    // Set up event listeners safely
    const toggleAllBtn = document.getElementById('toggleAll');
    if (toggleAllBtn) {
        toggleAllBtn.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.custom-switch input');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
            saveSwitchStates();
            updateGraphAndStats();
        });
    }

    document.querySelectorAll('.custom-switch input').forEach(input => {
        input.addEventListener('change', () => {
            saveSwitchStates();
            updateGraphAndStats();
        });
    });

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const input = this.value.toLowerCase();
            const rows = document.querySelectorAll('#inventoryTable tbody tr');
            
            rows.forEach(row => {
                const name = row.cells[0].textContent.toLowerCase();
                const category = row.cells[1].textContent.toLowerCase();
                const unit = row.cells[3].textContent.toLowerCase();
                
                const matches = name.includes(input) || 
                              category.includes(input) || 
                              unit.includes(input);
                
                row.style.display = matches ? '' : 'none';
            });

            updateGraphAndStats();
        });
    }

    // Modal event listeners
    const editModal = document.getElementById('editMaterialModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;

            const editId = document.getElementById('edit_id');
            const editName = document.getElementById('edit_name');
            const editTotalStock = document.getElementById('edit_total_stock');
            const editThreshold = document.getElementById('edit_threshold');
            const editUnit = document.getElementById('edit_unit');
            const editCategory = document.getElementById('edit_category');
            const editPrice = document.getElementById('edit_price');

            if (editId) editId.value = button.getAttribute('data-id') || '';
            if (editName) editName.value = button.getAttribute('data-name') || '';
            if (editTotalStock) editTotalStock.value = button.getAttribute('data-total') || '';
            if (editThreshold) editThreshold.value = button.getAttribute('data-threshold') || '';
            if (editUnit) editUnit.value = button.getAttribute('data-unit') || '';
            if (editCategory) editCategory.value = button.getAttribute('data-category') || '';
            if (editPrice) editPrice.value = button.getAttribute('data-price') || '';
        });
    }

    const deleteModal = document.getElementById('deleteMaterialModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;

            const deleteId = document.getElementById('delete_id');
            const deleteName = document.getElementById('delete_name');

            if (deleteId) deleteId.value = button.getAttribute('data-id') || '';
            if (deleteName) deleteName.textContent = button.getAttribute('data-name') || '';
        });
    }

    // Replace the existing click event handler for product links with this updated version:
    const table = document.getElementById('inventoryTable');
    if (table) {
        table.addEventListener('click', function(e) {
            // Check if clicked element is a product link
            if (!e.target.classList.contains('product-link')) {
                return;
            }

            e.preventDefault(); // Prevent default link behavior
            
            const link = e.target;
            
            // Get all data attributes
            const productData = {
                name: link.getAttribute('data-name') || '-',
                category: link.getAttribute('data-category') || '-',
                stock: link.getAttribute('data-stock') || '0',
                unit: link.getAttribute('data-unit') || '-',
                price: link.getAttribute('data-price') || '0',
                threshold: link.getAttribute('data-threshold') || '0',
                brand: link.getAttribute('data-brand') || '-',
                manufacturer: link.getAttribute('data-manufacturer') || '-',
                specifications: link.getAttribute('data-specifications') || '-',
                dimensions: link.getAttribute('data-dimensions') || '-',
                qualityGrade: link.getAttribute('data-quality-grade') || '-',
                origin: link.getAttribute('data-material-origin') || '-',
                model: link.getAttribute('data-model') || '-',
                certification: link.getAttribute('data-certification') || '-',
                batch: link.getAttribute('data-batch') || '-',
                expiry: link.getAttribute('data-expiry') || '-',
                warranty: link.getAttribute('data-warranty') || '-'
            };

            // Update modal elements
            document.getElementById('productName').textContent = productData.name;
            document.getElementById('productCategory').textContent = productData.category;
            document.getElementById('productStock').textContent = `${productData.stock} ${productData.unit}`;
            document.getElementById('productUnit').textContent = productData.unit;
            document.getElementById('productPrice').textContent = `₱${parseFloat(productData.price).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
            document.getElementById('productThreshold').textContent = productData.threshold;
            
            // Product Details
            document.getElementById('productBrand').textContent = productData.brand;
            document.getElementById('productManufacturer').textContent = productData.manufacturer;
            document.getElementById('productDimensions').textContent = productData.dimensions;
            document.getElementById('productQualityGrade').textContent = productData.qualityGrade;
            document.getElementById('productOrigin').textContent = productData.origin;
            
            // Technical Information
            document.getElementById('productModelNumber').textContent = productData.model;
            document.getElementById('productCertification').textContent = productData.certification;
            document.getElementById('productBatchNumber').textContent = productData.batch;
            document.getElementById('productExpiryDate').textContent = productData.expiry;
            
            // Additional Information
            document.getElementById('productSpecifications').textContent = productData.specifications;
            document.getElementById('productWarranty').textContent = productData.warranty;

            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('productDetailsModal'));
            modal.show();
        });
    }});

// Add this function at the beginning
function getProductDataFromLink(link) {
    return {
        name: link.getAttribute('data-name') || '-',
        category: link.getAttribute('data-category') || '-',
        stock: link.getAttribute('data-stock') || '0',
        unit: link.getAttribute('data-unit') || '-',
        price: link.getAttribute('data-price') || '0',
        threshold: link.getAttribute('data-threshold') || '0',
        brand: link.getAttribute('data-brand') || '-',
        manufacturer: link.getAttribute('data-manufacturer') || '-',
        specifications: link.getAttribute('data-specifications') || '-',
        dimensions: link.getAttribute('data-dimensions') || '-',
        qualityGrade: link.getAttribute('data-quality-grade') || '-',
        origin: link.getAttribute('data-material-origin') || '-',
        model: link.getAttribute('data-model') || '-',
        certification: link.getAttribute('data-certification') || '-',
        batch: link.getAttribute('data-batch') || '-',
        expiry: link.getAttribute('data-expiry') || '-',
        warranty: link.getAttribute('data-warranty') || '-'
    };
}

// Add this function to update the modal content
function updateProductDetailsModal(data) {
    console.log('Updating modal with data:', data);
    
    // Basic Information
    const productNameEl = document.getElementById('productName');
    const productCategoryEl = document.getElementById('productCategory');
    const productStockEl = document.getElementById('productStock');
    const productUnitEl = document.getElementById('productUnit');
    const productPriceEl = document.getElementById('productPrice');
    const productThresholdEl = document.getElementById('productThreshold');
    
    console.log('Basic elements found:', {
        productName: productNameEl ? 'YES' : 'NO',
        productCategory: productCategoryEl ? 'YES' : 'NO',
        productStock: productStockEl ? 'YES' : 'NO',
        productUnit: productUnitEl ? 'YES' : 'NO',
        productPrice: productPriceEl ? 'YES' : 'NO',
        productThreshold: productThresholdEl ? 'YES' : 'NO'
    });
    
    if (productNameEl) productNameEl.textContent = data.name;
    if (productCategoryEl) productCategoryEl.textContent = data.category;
    if (productStockEl) productStockEl.textContent = `${data.stock} ${data.unit}`;
    if (productUnitEl) productUnitEl.textContent = data.unit;
    if (productPriceEl) productPriceEl.textContent = `₱${parseFloat(data.price).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
    if (productThresholdEl) productThresholdEl.textContent = data.threshold;
    
    // Product Details
    const productBrandEl = document.getElementById('productBrand');
    const productManufacturerEl = document.getElementById('productManufacturer');
    const productDimensionsEl = document.getElementById('productDimensions');
    const productQualityGradeEl = document.getElementById('productQualityGrade');
    const productOriginEl = document.getElementById('productOrigin');
    
    console.log('Detail elements found:', {
        productBrand: productBrandEl ? 'YES' : 'NO',
        productManufacturer: productManufacturerEl ? 'YES' : 'NO',
        productDimensions: productDimensionsEl ? 'YES' : 'NO',
        productQualityGrade: productQualityGradeEl ? 'YES' : 'NO',
        productOrigin: productOriginEl ? 'YES' : 'NO'
    });
    
    if (productBrandEl) productBrandEl.textContent = data.brand;
    if (productManufacturerEl) productManufacturerEl.textContent = data.manufacturer;
    if (productDimensionsEl) productDimensionsEl.textContent = data.dimensions;
    if (productQualityGradeEl) productQualityGradeEl.textContent = data.qualityGrade;
    if (productOriginEl) productOriginEl.textContent = data.origin;
    
    // Technical Information
    const productModelNumberEl = document.getElementById('productModelNumber');
    const productCertificationEl = document.getElementById('productCertification');
    const productBatchNumberEl = document.getElementById('productBatchNumber');
    const productExpiryDateEl = document.getElementById('productExpiryDate');
    
    console.log('Technical elements found:', {
        productModelNumber: productModelNumberEl ? 'YES' : 'NO',
        productCertification: productCertificationEl ? 'YES' : 'NO',
        productBatchNumber: productBatchNumberEl ? 'YES' : 'NO',
        productExpiryDate: productExpiryDateEl ? 'YES' : 'NO'
    });
    
    if (productModelNumberEl) productModelNumberEl.textContent = data.model;
    if (productCertificationEl) productCertificationEl.textContent = data.certification;
    if (productBatchNumberEl) productBatchNumberEl.textContent = data.batch;
    if (productExpiryDateEl) productExpiryDateEl.textContent = data.expiry;
    
    // Additional Information
    const productSpecificationsEl = document.getElementById('productSpecifications');
    const productWarrantyEl = document.getElementById('productWarranty');
    
    console.log('Additional elements found:', {
        productSpecifications: productSpecificationsEl ? 'YES' : 'NO',
        productWarranty: productWarrantyEl ? 'YES' : 'NO'
    });
    
    if (productSpecificationsEl) productSpecificationsEl.textContent = data.specifications;
    if (productWarrantyEl) productWarrantyEl.textContent = data.warranty;

    // Set status color based on stock level
    const statusElement = document.getElementById('productStatus');
    if (statusElement) {
        statusElement.className = 'card-text';
        if (parseInt(data.stock) === 0) {
            statusElement.textContent = 'Out of Stock';
            statusElement.classList.add('text-danger', 'fw-bold');
        } else if (parseInt(data.stock) <= parseInt(data.threshold)) {
            statusElement.textContent = 'Low Stock';
            statusElement.classList.add('text-warning', 'fw-bold');
        } else {
            statusElement.textContent = 'In Stock';
            statusElement.classList.add('text-success', 'fw-bold');
        }
    }
}

    // Set status color based on stock level
    const statusElement = document.getElementById('productStatus');
    if (statusElement) {
        statusElement.className = 'card-text';
        if (parseInt(data.stock) === 0) {
            statusElement.textContent = 'Out of Stock';
            statusElement.classList.add('text-danger', 'fw-bold');
        } else if (parseInt(data.stock) <= parseInt(data.threshold)) {
            statusElement.textContent = 'Low Stock';
            statusElement.classList.add('text-warning', 'fw-bold');
        } else {
            statusElement.textContent = 'In Stock';
            statusElement.classList.add('text-success', 'fw-bold');
        }
    }

// Add event listener for product links
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Setting up event listeners');
    
    const table = document.getElementById('inventoryTable');
    console.log('Table found:', table ? 'YES' : 'NO');
    
    if (table) {
        table.addEventListener('click', function(e) {
            console.log('Table clicked, target:', e.target);
            console.log('Target classes:', e.target.classList);
            
            const target = e.target;
            if (target.classList.contains('product-link')) {
                e.preventDefault();
                console.log('Product link clicked - preventDefault called');
                
                // Log all data attributes
                console.log('All target attributes:');
                for (let attr of target.attributes) {
                    console.log(`${attr.name}: ${attr.value}`);
                }
                
                const productData = getProductDataFromLink(target);
                console.log('Product data extracted:', productData);
                
                updateProductDetailsModal(productData);
                
                const modal = new bootstrap.Modal(document.getElementById('productDetailsModal'));
                modal.show();
            } else {
                console.log('Clicked element is not a product-link');
            }
        });
    } else {
        console.error('inventoryTable not found!');
    }
});