import Chart from 'chart.js/auto';

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize chart first
    initializeChart();
    
    // Initialize increment/decrement buttons
    initializeScoreControls();
    
    // Initialize modal functionality
    initializeEvaluationModal();
    
    // Initialize form submission
    initializeFormSubmission();

    // Initialize search validation (if search form present)
    initializeSearch();
});

// --- Top Performing Suppliers chart ---------------------------------------------------------
// Podium colours by overall rank (validated: contrast >= 3:1 on white, distinct for colour-blind readers).
// Rank is also shown by the badges and names, so colour is never the only cue.
const PODIUM = { 1: '#9c7206', 2: '#4a78b0', 3: '#b85c22' };
const INK = '#0f172a';      // value labels: text ink, never the bar colour
const MUTED = '#64748b';    // axis ticks
const GRID = '#e2e8f0';

// Draws each bar's score just above it (Chart.js has no built-in data labels).
const valueLabels = {
    id: 'rkValueLabels',
    afterDatasetsDraw(chart) {
        const { ctx } = chart;
        const meta = chart.getDatasetMeta(0);
        ctx.save();
        ctx.font = '600 13px system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif';
        ctx.fillStyle = INK;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'bottom';
        meta.data.forEach((bar, i) => {
            const value = chart.data.datasets[0].data[i];
            ctx.fillText(Number(value).toFixed(2), bar.x, bar.y - 6);
        });
        ctx.restore();
    },
};

function initializeChart() {
    const top = (window.__page && window.__page.topSuppliers) || [];
    const canvas = document.getElementById('topSuppliersChart');
    if (!canvas || top.length === 0) return;

    const colors = top.map((s) => PODIUM[s.rank] || MUTED);
    const chart = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: top.map((s) => s.name),
            datasets: [{
                label: 'Overall score',
                data: top.map((s) => s.score),
                backgroundColor: colors,
                hoverBackgroundColor: colors,
                borderRadius: { topLeft: 8, topRight: 8 },   // rounded data end, square at the baseline
                borderSkipped: 'start',
                barPercentage: 0.55,
                categoryPercentage: 0.7,
                maxBarThickness: 80,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: { top: 24 } },               // room for the value labels
            animation: { duration: 400 },
            scales: {
                // Bars start at zero on the score's real 0-5 range: a cut axis would exaggerate small gaps.
                y: {
                    min: 0, max: 5,
                    ticks: { stepSize: 1, color: MUTED, font: { size: 11 } },
                    grid: { color: GRID, lineWidth: 1, drawTicks: false },
                    border: { display: false, dash: [4, 4] },   // v4: border.dash styles the gridlines (soft, dashed)
                },
                x: {
                    grid: { display: false },
                    border: { color: GRID },
                    ticks: { color: '#334155', font: { size: 12, weight: '600' } },
                },
            },
            plugins: {
                legend: { display: false },                    // one series: the card title names it
                tooltip: {
                    backgroundColor: '#0f172a',
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        title: (items) => `#${top[items[0].dataIndex].rank} · ${items[0].label}`,
                        label: (item) => `Overall score: ${Number(item.raw).toFixed(2)} / 5`,
                    },
                },
            },
        },
        plugins: [valueLabels],
    });
    // Hovering / focusing a Performance Metrics row highlights its bar
    document.querySelectorAll('.rk-item[data-chart-index]').forEach((item) => {
        const index = Number(item.dataset.chartIndex);
        const show = () => {
            chart.setActiveElements([{ datasetIndex: 0, index }]);
            chart.tooltip.setActiveElements([{ datasetIndex: 0, index }], { x: 0, y: 0 });
            chart.update();
        };
        const hide = () => {
            chart.setActiveElements([]);
            chart.tooltip.setActiveElements([], { x: 0, y: 0 });
            chart.update();
        };
        item.addEventListener('mouseenter', show);
        item.addEventListener('focus', show);
        item.addEventListener('mouseleave', hide);
        item.addEventListener('blur', hide);
    });
}

// Score controls (increment/decrement buttons)
function initializeScoreControls() {
    const incrementButtons = document.querySelectorAll('.btn-increment');
    
    incrementButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const action = this.getAttribute('data-action');
            const scoreInput = this.closest('.score-input').querySelector('input[type="number"]');
            
            if (!scoreInput) return;
            
            let currentValue = parseFloat(scoreInput.value) || 0;
            const step = parseFloat(scoreInput.getAttribute('step')) || 0.5;
            const min = parseFloat(scoreInput.getAttribute('min')) || 0;
            const max = parseFloat(scoreInput.getAttribute('max')) || 5;
            
            let newValue = currentValue;
            
            if (action === 'increment') {
                newValue = Math.min(currentValue + step, max);
            } else if (action === 'decrement') {
                newValue = Math.max(currentValue - step, min);
            }
            
            // Round to avoid floating point precision issues
            newValue = Math.round(newValue * 2) / 2;
            
            scoreInput.value = newValue.toFixed(1);
            
            // Update button states
            updateButtonStates(scoreInput);
            
            // Trigger change event for any listeners
            scoreInput.dispatchEvent(new Event('change'));
        });
    });
    
    // Update button states on input change
    document.querySelectorAll('.score-input input[type="number"]').forEach(input => {
        input.addEventListener('input', function() {
            updateButtonStates(this);
        });
        
        // Initialize button states
        updateButtonStates(input);
    });
}

// Update increment/decrement button states
function updateButtonStates(input) {
    const scoreInput = input.closest('.score-input');
    const incrementBtn = scoreInput.querySelector('[data-action="increment"]');
    const decrementBtn = scoreInput.querySelector('[data-action="decrement"]');
    
    const currentValue = parseFloat(input.value) || 0;
    const min = parseFloat(input.getAttribute('min')) || 0;
    const max = parseFloat(input.getAttribute('max')) || 5;
    
    // Update increment button
    if (incrementBtn) {
        incrementBtn.disabled = currentValue >= max;
    }
    
    // Update decrement button
    if (decrementBtn) {
        decrementBtn.disabled = currentValue <= min;
    }
}

// Evaluation modal functionality
function initializeEvaluationModal() {
    const evaluationModal = document.getElementById('evaluationModal');
    const form = document.getElementById('evaluationForm');
    
    if (!evaluationModal || !form) return;
    
    // Handle modal show event
    evaluationModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const supplierId = button.getAttribute('data-supplier-id');
        
        if (supplierId) {
            // Set supplier ID in hidden field
            document.getElementById('supplier_id').value = supplierId;
            
            // Reset form
            resetEvaluationForm();
            
            // Load existing evaluation if available
            loadSupplierData(supplierId);
        }
    });
    
    // Handle modal hide event
    evaluationModal.addEventListener('hide.bs.modal', function(event) {
        // Clear any success messages
        const successMessage = document.getElementById('successMessage');
        if (successMessage) {
            successMessage.classList.add('d-none');
        }
        
        // Reset form
        resetEvaluationForm();
    });
}

// Reset evaluation form
function resetEvaluationForm() {
    const form = document.getElementById('evaluationForm');
    if (!form) return;
    
    // Reset all score inputs to 0.0
    const scoreInputs = form.querySelectorAll('input[type="number"]');
    scoreInputs.forEach(input => {
        if (input.name.includes('_score')) {
            input.value = '0.0';
            updateButtonStates(input);
        }
    });
    
    // Reset metric displays
    const metricDisplays = {
        'ontime_deliveries_display': '0',
        'total_deliveries_display': '0',
        'defective_units_display': '0',
        'total_units_display': '0',
        'actual_cost_display': '₱0.00',
        'estimated_cost_display': '₱0.00'
    };
    
    Object.entries(metricDisplays).forEach(([id, value]) => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
        }
    });
    
    // Reset hidden metric fields
    const hiddenFields = ['ontime_deliveries', 'total_deliveries', 'defective_units', 
                         'total_units', 'actual_cost', 'estimated_cost'];
    hiddenFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (field) {
            field.value = '';
        }
    });
}

// Load supplier data (this would typically fetch from server)
function loadSupplierData(supplierId) {
    // In a real implementation, you would fetch supplier evaluation data
    // For now, we'll just set some example metrics
    
    // Example: Load metrics from server or existing data
    const metrics = {
        ontime_deliveries: 15,
        total_deliveries: 20,
        defective_units: 2,
        total_units: 100,
        actual_cost: 50000,
        estimated_cost: 48000
    };
    
    // Update metric displays
    document.getElementById('ontime_deliveries_display').textContent = metrics.ontime_deliveries;
    document.getElementById('total_deliveries_display').textContent = metrics.total_deliveries;
    document.getElementById('defective_units_display').textContent = metrics.defective_units;
    document.getElementById('total_units_display').textContent = metrics.total_units;
    document.getElementById('actual_cost_display').textContent = `₱${metrics.actual_cost.toLocaleString()}.00`;
    document.getElementById('estimated_cost_display').textContent = `₱${metrics.estimated_cost.toLocaleString()}.00`;
    
    // Update hidden fields
    document.getElementById('ontime_deliveries').value = metrics.ontime_deliveries;
    document.getElementById('total_deliveries').value = metrics.total_deliveries;
    document.getElementById('defective_units').value = metrics.defective_units;
    document.getElementById('total_units').value = metrics.total_units;
    document.getElementById('actual_cost').value = metrics.actual_cost;
    document.getElementById('estimated_cost').value = metrics.estimated_cost;
}

// Search form validation
function initializeSearch() {
    const form = document.getElementById('searchForm');
    const input = document.getElementById('searchInput');
    const errorDiv = document.getElementById('searchError');

    if (!form || !input) return;

    form.addEventListener('submit', function(e) {
        if (input.value.trim() === '') {
            e.preventDefault();
            if (errorDiv) {
                errorDiv.style.display = 'block';
                setTimeout(() => { errorDiv.style.display = 'none'; }, 3000);
            }
        }
    });
}

// Form submission functionality
function initializeFormSubmission() {
    const form = document.getElementById('evaluationForm');
    const saveButton = document.getElementById('saveButton');
    const successMessage = document.getElementById('successMessage');
    
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        const spinner = saveButton.querySelector('.spinner-border');
        if (spinner) {
            spinner.classList.remove('d-none');
        }
        saveButton.disabled = true;
        
        // Get form data
        const formData = new FormData(form);
        const supplierId = formData.get('supplier_id');
        
        // Calculate ratios and final score
        const ontimeDeliveries = parseInt(formData.get('ontime_deliveries')) || 0;
        const totalDeliveries = parseInt(formData.get('total_deliveries')) || 1;
        const defectiveUnits = parseInt(formData.get('defective_units')) || 0;
        const totalUnits = parseInt(formData.get('total_units')) || 1;
        const actualCost = parseFloat(formData.get('actual_cost')) || 0;
        const estimatedCost = parseFloat(formData.get('estimated_cost')) || 1;
        
        const deliveryOntimeRatio = totalDeliveries > 0 ? (ontimeDeliveries / totalDeliveries) : 0;
        const defectRatio = totalUnits > 0 ? (defectiveUnits / totalUnits) : 0;
        const costVarianceRatio = estimatedCost > 0 ? Math.abs((actualCost - estimatedCost) / estimatedCost) : 0;
        
        // Calculate final score (average of all scores)
        const scores = [
            parseFloat(formData.get('delivery_speed_score')) || 0,
            parseFloat(formData.get('quality_score')) || 0,
            parseFloat(formData.get('cost_variance_score')) || 0,
            parseFloat(formData.get('performance_score')) || 0,
            parseFloat(formData.get('engagement_score')) || 0,
            parseFloat(formData.get('sustainability_score')) || 0
        ];
        const finalScore = scores.reduce((sum, score) => sum + score, 0) / scores.length;
        
        // Prepare data for submission
        const evaluationData = {
            delivery_speed_score: formData.get('delivery_speed_score'),
            delivery_ontime_ratio: deliveryOntimeRatio.toFixed(2),
            quality_score: formData.get('quality_score'),
            defect_ratio: defectRatio.toFixed(2),
            cost_variance_score: formData.get('cost_variance_score'),
            cost_variance_ratio: costVarianceRatio.toFixed(2),
            performance_score: formData.get('performance_score'),
            engagement_score: formData.get('engagement_score'),
            sustainability_score: formData.get('sustainability_score'),
            final_score: finalScore.toFixed(2),
            _token: formData.get('_token')
        };
        
        // Submit to server (use Laravel url() to respect subdirectory base)
        const postUrl = `${window.appBaseUrl || ''}/admin/suppliers/${supplierId}/evaluations`;
        fetch(postUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': evaluationData._token,
                'Accept': 'application/json'
            },
            body: JSON.stringify(evaluationData)
        })
        .then(async response => {
            if (response.ok) {
                return response.json();
            }
            // try to parse JSON error message(s)
            let errorText = '';
            try {
                const errorData = await response.json();
                if (errorData.errors) {
                    errorText = Object.values(errorData.errors).flat().join(' ');
                } else if (errorData.message) {
                    errorText = errorData.message;
                }
            } catch (e) {
                // not JSON
                errorText = await response.text();
            }
            throw new Error(errorText || 'Network response was not ok');
        })
        .then(data => {
            // Show success message
            if (successMessage) {
                successMessage.classList.remove('d-none');
                setTimeout(() => {
                    successMessage.classList.add('d-none');
                }, 3000);
            }
            
            // Optionally reload page to update rankings
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        })
        .catch(error => {
            console.error('Error:', error);
            const errorMsgElem = document.getElementById('errorMessage');
            if (errorMsgElem) {
                errorMsgElem.textContent = error.message || 'An unexpected error occurred.';
                errorMsgElem.classList.remove('d-none');
                setTimeout(() => {
                    errorMsgElem.classList.add('d-none');
                }, 5000);
            } else {
                alert('An error occurred while saving the evaluation. Please try again.');
            }
        })
        .finally(() => {
            // Hide loading state
            if (spinner) {
                spinner.classList.add('d-none');
            }
            saveButton.disabled = false;
        });
    });
}

