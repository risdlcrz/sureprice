@extends('layouts.app')

@push('styles')
<style>
    .content-wrapper {
        margin-left: 0;
        padding: 20px;
        min-height: 100vh;
        background-color: #f8f9fa;
    }

    .section-container {
        margin-bottom: 2rem;
        padding: 1.25rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        background-color: #fff;
    }

    .section-title {
        margin-bottom: 1.25rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #0d6efd;
        color: #344767;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #344767;
    }

    .progress {
        height: 0.5rem;
        margin-bottom: 2rem;
    }

    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
    }

    .step {
        text-align: center;
        flex: 1;
        position: relative;
    }

    .step:not(:last-child):after {
        content: '';
        position: absolute;
        top: 50%;
        right: 0;
        width: 100%;
        height: 2px;
        background: #dee2e6;
        z-index: 1;
    }

    .step.active:not(:last-child):after {
        background: #0d6efd;
    }

    .step.completed:not(:last-child):after {
        background: #198754;
    }

    .step-number {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #dee2e6;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
        position: relative;
        z-index: 2;
    }

    .step.active .step-number {
        background: #0d6efd;
    }

    .step.completed .step-number {
        background: #198754;
    }

    .step-label {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .step.active .step-label {
        color: #0d6efd;
        font-weight: 600;
    }

    .step.completed .step-label {
        color: #198754;
        font-weight: 600;
    }

    .room-row {
        background-color: #fff;
        padding: 1rem;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
        border: 1px solid #dee2e6;
    }

    .scope-category-group {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0.5rem;
        height: 100%;
        margin-bottom: 1rem;
    }

    .scope-category-group h6 {
        color: #344767;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .form-check {
        margin-bottom: 0.75rem;
    }

    .form-check:last-child {
        margin-bottom: 0;
    }

    .room-summary {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0.375rem;
        margin-top: 1rem;
    }

    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Create New Contract - Step 2</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Progress Steps -->
                        <div class="step-indicator">
                            <div class="step completed">
                                <div class="step-number">1</div>
                                <div class="step-label">Basic Information</div>
                            </div>
                            <div class="step active">
                                <div class="step-number">2</div>
                                <div class="step-label">Scope & Materials</div>
                            </div>
                            <div class="step">
                                <div class="step-number">3</div>
                                <div class="step-label">Terms & Conditions</div>
                            </div>
                            <div class="step">
                                <div class="step-number">4</div>
                                <div class="step-label">Payment & Review</div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('contracts.store.step2') }}" id="step2Form" novalidate>
                            @csrf
                            <input type="hidden" name="contract_id" value="{{ $contract->id ?? (session('contract_step1.contract_id') ?? '') }}">
                            
                            <!-- Room/Area Details -->
                            <div class="section-container" id="roomSection">
                                <h5 class="section-title">Room/Area Details</h5>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-primary" id="addRoomBtn">
                                            <i class="fas fa-plus"></i> Add Room/Area
                                        </button>
                                        <button type="button" class="btn btn-secondary ms-2" id="applyToAllBtn">
                                            Apply Selected Scope to All Rooms
                                        </button>
                                    </div>
                                </div>
                                <div id="roomDetails">
                                    <!-- Rooms will be added here dynamically -->
                                </div>
                                
                                <!-- Hidden fields for form submission -->
                                <input type="hidden" name="total_materials" id="total_materials" value="{{ session('contract_step2.total_materials', 0) }}">
                                <input type="hidden" name="total_labor" id="total_labor" value="{{ session('contract_step2.total_labor', 0) }}">
                                <input type="hidden" name="grand_total" id="grand_total" value="{{ session('contract_step2.grand_total', 0) }}">
                                <input type="hidden" name="total_area" id="total_area" value="{{ session('contract_step2.total_area', 0) }}">
                                
                                <!-- Grand Total Summary -->
                                <div class="card mt-4">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">Grand Total Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <p class="mb-1">Total Floor Area:</p>
                                                <h5 id="grandTotalArea">{{ number_format(session('contract_step2.total_area', 0), 2) }} sq m</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <p class="mb-1">Total Materials Cost:</p>
                                                <h5 id="grandTotalMaterials">₱{{ number_format(session('contract_step2.total_materials', 0), 2) }}</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <p class="mb-1">Total Labor Cost:</p>
                                                <h5 id="grandTotalLabor">₱{{ number_format(session('contract_step2.total_labor', 0), 2) }}</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <p class="mb-1">Grand Total:</p>
                                                <h5 id="grandTotal">₱{{ number_format(session('contract_step2.grand_total', 0), 2) }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detailed Breakdown Section -->
                            <div class="section-container" id="breakdownSection">
                                <h5 class="section-title">Detailed Cost Breakdown</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Category</th>
                                                <th>Material</th>
                                                <th>Unit</th>
                                                <th>Unit Cost</th>
                                                <th>Quantity</th>
                                                <th>Total Cost</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="breakdownTableBody">
                                            <!-- Will be populated dynamically -->
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <td colspan="5" class="text-end"><strong>Total Materials Cost:</strong></td>
                                                <td colspan="2" id="breakdownTotalMaterials">₱0.00</td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-end"><strong>Total Labor Cost:</strong></td>
                                                <td colspan="2" id="breakdownTotalLabor">₱0.00</td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-end"><strong>Grand Total:</strong></td>
                                                <td colspan="2" id="breakdownGrandTotal">₱0.00</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Timeline Section -->
                            <div class="section-container" id="timelineSection">
                                <h5 class="section-title">Project Timeline</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="start_date">Start Date</label>
                                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                                value="{{ old('start_date', session('contract_step2.start_date')) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="end_date">End Date</label>
                                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                                value="{{ old('end_date', session('contract_step2.end_date')) }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <a href="{{ route('contracts.step1') }}" class="btn btn-secondary">Previous Step</a>
                                <button type="submit" class="btn btn-primary">Next Step</button>
                                <a href="{{ route('contracts.clear-session') }}" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel? All entered data will be lost.')">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addRoomBtn')?.addEventListener('click', createRoomRow);
    document.getElementById('applyToAllBtn')?.addEventListener('click', applyScopesToAll);
    // Call updateProjectTimeline when start date changes
    document.getElementById('start_date')?.addEventListener('change', updateProjectTimeline);
    // Call updateProjectTimeline when end date is manually changed (optional, for validation)
    document.getElementById('end_date')?.addEventListener('change', updateProjectTimeline);
    // Call updateProjectTimeline when any scope checkbox changes (for all current and future rooms)
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('scope-checkbox')) {
            updateProjectTimeline();
        }
    });
    // Optionally, initialize the form if needed
    if (typeof initializeForm === 'function') {
        initializeForm();
    }
    // Initial timeline update
    updateProjectTimeline();
});

// Use $scopeTypes from backend for all scope/material logic
const scopeTypes = @json($scopeTypes->keyBy('id'));
const scopesByCategory = {};
Object.values(scopeTypes).forEach(scope => {
    if (!scopesByCategory[scope.category]) scopesByCategory[scope.category] = [];
    scopesByCategory[scope.category].push(scope);
});

const initialSessionData = @json(session('contract_step2', []));

function initializeForm() {
    if (!initialSessionData.rooms || !Array.isArray(initialSessionData.rooms)) return;
    initialSessionData.rooms.forEach(room => {
        const roomContainer = document.createElement('div');
        roomContainer.className = 'room-row mb-4';
        const roomId = Date.now() + Math.floor(Math.random() * 10000); // unique id
        roomContainer.dataset.roomId = roomId;
        roomContainer.innerHTML = `
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Room/Area Name</label>
                        <input type="text" class="form-control" name="rooms[${roomId}][name]" required value="${room.name || ''}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Length (m)</label>
                        <input type="number" class="form-control room-dimension" name="rooms[${roomId}][length]" step="0.01" min="0.01" required value="${room.length || ''}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Width (m)</label>
                        <input type="number" class="form-control room-dimension" name="rooms[${roomId}][width]" step="0.01" min="0.01" required value="${room.width || ''}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Area (sq m)</label>
                        <input type="number" class="form-control" name="rooms[${roomId}][area]" readonly value="${room.area || ''}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label visually-hidden">Remove Room</label>
                        <button type="button" class="btn btn-danger d-block" onclick="removeRoom(this)">
                            <i class="fas fa-trash"></i> Remove Room
                        </button>
                    </div>
                </div>
            </div>
            <input type="hidden" name="rooms[${roomId}][materials_cost]" class="materials-cost-hidden" value="${room.materials_cost || 0}">
            <input type="hidden" name="rooms[${roomId}][labor_cost]" class="labor-cost-hidden" value="${room.labor_cost || 0}">
            <div class="row mt-3">
                <div class="col-12">
                    <h6 class="mb-3">Scope of Work</h6>
                    <div class="accordion" id="scopeAccordion${roomId}">
                        ${Object.entries(scopesByCategory).map(([category, scopes], categoryIndex) => `
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button ${categoryIndex > 0 ? 'collapsed' : ''}" type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#category${roomId}${categoryIndex}"
                                        aria-expanded="${categoryIndex === 0}">
                                        ${category}
                                    </button>
                                </h2>
                                <div id="category${roomId}${categoryIndex}" 
                                    class="accordion-collapse collapse ${categoryIndex === 0 ? 'show' : ''}"
                                    data-bs-parent="#scopeAccordion${roomId}">
                                    <div class="accordion-body">
                                        <div class="row">
                                            ${scopes.map(scope => `
                                                <div class="col-md-6">
                                                    <div class="scope-item mb-4">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input scope-checkbox" 
                                                                name="rooms[${roomId}][scope][]" 
                                                                value="${scope.id}" 
                                                                id="scope_${scope.id}_${roomId}" 
                                                                ${room.scope && room.scope.includes(scope.id.toString()) ? 'checked' : ''}>
                                                            <label class="form-check-label" for="scope_${scope.id}_${roomId}">
                                                                <strong>${scope.name}</strong>
                                                                <span class="badge bg-info ms-2">${scope.estimated_days ?? 0} days</span>
                                                            </label>
                                                        </div>
                                                        <div class="ms-4 mt-2">
                                                            <small class="text-muted">Materials:</small>
                                                            <ul class="list-unstyled small">
                                                                ${(scope.materials && scope.materials.length > 0) ? scope.materials.map(material => `
                                                                    <li>${material.name} - ₱${material.srp_price ?? material.base_price ?? 0} ${material.unit}</li>
                                                                `).join('') : '<li><em>No materials assigned</em></li>'}
                                                            </ul>
                                                            <small class="text-muted">Tasks:</small>
                                                            <ul class="list-unstyled small">
                                                                ${(scope.items && scope.items.length > 0) ? scope.items.map(item => `
                                                                    <li><i class=\"fas fa-check-circle text-success\"></i> ${item}</li>
                                                                `).join('') : '<li><em>No tasks listed</em></li>'}
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            `).join('')}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
            <div class="room-summary mt-3">
                <div class="row">
                    <div class="col-md-4">
                        <p class="mb-1">Materials Cost:</p>
                        <h6 class="materials-cost">₱0.00</h6>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1">Labor Cost:</p>
                        <h6 class="labor-cost">₱0.00</h6>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1">Total Cost:</p>
                        <h6 class="total-cost">₱0.00</h6>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1">Estimated Time:</p>
                        <h6 class="estimated-time">0 days</h6>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('roomDetails').appendChild(roomContainer);
        // Add event listeners for the new room
        const dimensionInputs = roomContainer.querySelectorAll('.room-dimension');
        dimensionInputs.forEach(input => {
            input.addEventListener('input', () => calculateRoomArea(input));
        });
        const scopeCheckboxes = roomContainer.querySelectorAll('.scope-checkbox');
        scopeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => updateRoomCalculations(roomContainer));
        });
        // Initialize calculations
        updateRoomCalculations(roomContainer);
    });
}

function createRoomRow() {
    const roomContainer = document.createElement('div');
    roomContainer.className = 'room-row mb-4';
    const roomId = Date.now();
    roomContainer.dataset.roomId = roomId;
    roomContainer.innerHTML = `
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Room/Area Name</label>
                    <input type="text" class="form-control" name="rooms[${roomId}][name]" required>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label class="form-label">Length (m)</label>
                    <input type="number" class="form-control room-dimension" name="rooms[${roomId}][length]" step="0.01" min="0.01" required>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label class="form-label">Width (m)</label>
                    <input type="number" class="form-control room-dimension" name="rooms[${roomId}][width]" step="0.01" min="0.01" required>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label class="form-label">Area (sq m)</label>
                    <input type="number" class="form-control" name="rooms[${roomId}][area]" readonly>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label visually-hidden">Remove Room</label>
                    <button type="button" class="btn btn-danger d-block" onclick="removeRoom(this)">
                        <i class="fas fa-trash"></i> Remove Room
                    </button>
                </div>
            </div>
        </div>
        <input type="hidden" name="rooms[${roomId}][materials_cost]" class="materials-cost-hidden" value="0">
        <input type="hidden" name="rooms[${roomId}][labor_cost]" class="labor-cost-hidden" value="0">
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="mb-3">Scope of Work</h6>
                <div class="accordion" id="scopeAccordion${roomId}">
                    ${Object.entries(scopesByCategory).map(([category, scopes], categoryIndex) => `
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button ${categoryIndex > 0 ? 'collapsed' : ''}" type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#category${roomId}${categoryIndex}"
                                    aria-expanded="${categoryIndex === 0}">
                                    ${category}
                                </button>
                            </h2>
                            <div id="category${roomId}${categoryIndex}" 
                                class="accordion-collapse collapse ${categoryIndex === 0 ? 'show' : ''}"
                                data-bs-parent="#scopeAccordion${roomId}">
                                <div class="accordion-body">
                                    <div class="row">
                                        ${scopes.map(scope => `
                                            <div class="col-md-6">
                                                <div class="scope-item mb-4">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input scope-checkbox" 
                                                            name="rooms[${roomId}][scope][]" 
                                                            value="${scope.id}" 
                                                            id="scope_${scope.id}_${roomId}">
                                                        <label class="form-check-label" for="scope_${scope.id}_${roomId}">
                                                            <strong>${scope.name}</strong>
                                                            <span class="badge bg-info ms-2">${scope.estimated_days ?? 0} days</span>
                                                        </label>
                                                    </div>
                                                    <div class="ms-4 mt-2">
                                                        <small class="text-muted">Materials:</small>
                                                        <ul class="list-unstyled small">
                                                            ${(scope.materials && scope.materials.length > 0) ? scope.materials.map(material => `
                                                                <li>${material.name} - ₱${material.srp_price ?? material.base_price ?? 0} ${material.unit}</li>
                                                            `).join('') : '<li><em>No materials assigned</em></li>'}
                                                        </ul>
                                                        <small class="text-muted">Tasks:</small>
                                                        <ul class="list-unstyled small">
                                                            ${(scope.items && scope.items.length > 0) ? scope.items.map(item => `
                                                                <li><i class=\"fas fa-check-circle text-success\"></i> ${item}</li>
                                                            `).join('') : '<li><em>No tasks listed</em></li>'}
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        </div>
        <div class="room-summary mt-3">
            <div class="row">
                <div class="col-md-4">
                    <p class="mb-1">Materials Cost:</p>
                    <h6 class="materials-cost">₱0.00</h6>
                </div>
                <div class="col-md-4">
                    <p class="mb-1">Labor Cost:</p>
                    <h6 class="labor-cost">₱0.00</h6>
                </div>
                <div class="col-md-4">
                    <p class="mb-1">Total Cost:</p>
                    <h6 class="total-cost">₱0.00</h6>
                </div>
                <div class="col-md-4">
                    <p class="mb-1">Estimated Time:</p>
                    <h6 class="estimated-time">0 days</h6>
                </div>
            </div>
        </div>
    `;
    document.getElementById('roomDetails').appendChild(roomContainer);
    // Add event listeners for the new room
    const dimensionInputs = roomContainer.querySelectorAll('.room-dimension');
    dimensionInputs.forEach(input => {
        input.addEventListener('input', () => calculateRoomArea(input));
    });
    const scopeCheckboxes = roomContainer.querySelectorAll('.scope-checkbox');
    scopeCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => updateRoomCalculations(roomContainer));
    });
    // Initialize calculations
    updateRoomCalculations(roomContainer);
}

function removeRoom(button) {
    const roomRow = button.closest('.room-row');
    roomRow.remove();
    updateGrandTotal();
    updateProjectTimeline();
    saveFormData();
}

function calculateRoomArea(input) {
    const roomRow = input.closest('.room-row');
    const lengthInput = roomRow.querySelector('input[name$="[length]"]');
    const widthInput = roomRow.querySelector('input[name$="[width]"]');
    const areaInput = roomRow.querySelector('input[name$="[area]"]');

    if (lengthInput.value && widthInput.value) {
        const area = parseFloat(lengthInput.value) * parseFloat(widthInput.value);
        areaInput.value = area.toFixed(2);
        updateRoomCalculations(roomRow);
        saveFormData();
    }
}

function updateRoomCalculations(roomRow) {
    const area = parseFloat(roomRow.querySelector('input[name$="[area]"]').value) || 0;
    let materialsCost = 0;
    let laborCost = 0;
    let estimatedDays = 0;

    const selectedScopes = Array.from(roomRow.querySelectorAll('.scope-checkbox:checked'))
        .map(checkbox => checkbox.value);
    // Calculate costs based on selected scopes
    selectedScopes.forEach(scopeKey => {
        const scope = scopeTypes[scopeKey];
        if (scope) {
            // Add materials cost
            if (scope.materials && Array.isArray(scope.materials)) {
                scope.materials.forEach(material => {
                    // Always use parseFloat and fallback to 0
                    const price = parseFloat(material.srp_price ?? material.base_price ?? 0) || 0;
                    let quantity = 1;
                    if (material.isPerArea && material.coverage) {
                        quantity = area / parseFloat(material.coverage || 1);
                    } else if (material.isPerArea) {
                        quantity = area;
                    }
                    quantity = Math.ceil(quantity * 100) / 100;
                    materialsCost += price * quantity;
                });
            }
            // Add labor cost based on area
            const laborRate = parseFloat(scope.labor_rate ?? 0) || 0;
            laborCost += laborRate * area;
            // Add estimated days
            estimatedDays += parseInt(scope.estimated_days ?? 0) || 0;
        }
    });
    // Update room summary displays
    roomRow.querySelector('.materials-cost').textContent = `₱${materialsCost.toFixed(2)}`;
    roomRow.querySelector('.labor-cost').textContent = `₱${laborCost.toFixed(2)}`;
    roomRow.querySelector('.total-cost').textContent = `₱${(materialsCost + laborCost).toFixed(2)}`;
    roomRow.querySelector('.estimated-time').textContent = `${estimatedDays} days`;
    // Update hidden fields for this room
    roomRow.querySelector('.materials-cost-hidden').value = materialsCost.toFixed(2);
    roomRow.querySelector('.labor-cost-hidden').value = laborCost.toFixed(2);
    // Update grand totals
    updateGrandTotal();
    updateBreakdownTable();
}

function updateGrandTotal() {
    let totalArea = 0;
    let totalMaterials = 0;
    let totalLabor = 0;

    document.querySelectorAll('.room-row').forEach(room => {
        // Get area
        const area = parseFloat(room.querySelector('input[name$="[area]"]').value) || 0;
        totalArea += area;

        // Get costs from hidden fields
        const materialsCost = parseFloat(room.querySelector('.materials-cost-hidden').value) || 0;
        const laborCost = parseFloat(room.querySelector('.labor-cost-hidden').value) || 0;
        
        totalMaterials += materialsCost;
        totalLabor += laborCost;
    });

    // Update display
    document.getElementById('grandTotalArea').textContent = `${totalArea.toFixed(2)} sq m`;
    document.getElementById('grandTotalMaterials').textContent = `₱${totalMaterials.toFixed(2)}`;
    document.getElementById('grandTotalLabor').textContent = `₱${totalLabor.toFixed(2)}`;
    document.getElementById('grandTotal').textContent = `₱${(totalMaterials + totalLabor).toFixed(2)}`;

    // Update hidden fields
    document.getElementById('total_area').value = totalArea.toFixed(2);
    document.getElementById('total_materials').value = totalMaterials.toFixed(2);
    document.getElementById('total_labor').value = totalLabor.toFixed(2);
    document.getElementById('grand_total').value = (totalMaterials + totalLabor).toFixed(2);

    // Update project timeline
    updateProjectTimeline();
}

function updateProjectTimeline() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    if (!startDateInput || !startDateInput.value) return;

    // Calculate total project days based on selected scopes (max of all rooms)
    let totalDays = 0;
    document.querySelectorAll('.room-row').forEach(room => {
        let roomDays = 0;
        room.querySelectorAll('.scope-checkbox:checked').forEach(checkbox => {
            const scope = scopeTypes[checkbox.value];
            if (scope) roomDays += parseInt(scope.estimated_days ?? 0) || 0;
        });
        totalDays = Math.max(totalDays, roomDays);
    });

    const startDate = new Date(startDateInput.value);
    const endDate = new Date(startDate);
    endDate.setDate(startDate.getDate() + totalDays);
    if (endDateInput) {
        endDateInput.value = endDate.toISOString().split('T')[0];
    }
}

function applyScopesToAll() {
    const rooms = document.querySelectorAll('.room-row');
    if (rooms.length < 2) return;

    // Get the scope selections from the first room
    const firstRoom = rooms[0];
    const selectedScopes = Array.from(firstRoom.querySelectorAll('.scope-checkbox:checked'))
        .map(checkbox => checkbox.value);

    // Apply to all other rooms
    rooms.forEach((room, index) => {
        if (index === 0) return; // Skip first room
        
        room.querySelectorAll('.scope-checkbox').forEach(checkbox => {
            checkbox.checked = selectedScopes.includes(checkbox.value);
        });
        updateRoomCalculations(room);
    });
    
    saveFormData();
}

function updateBreakdownTable() {
    const tableBody = document.getElementById('breakdownTableBody');
    tableBody.innerHTML = '';
    let totalMaterialsCost = 0;
    let totalLaborCost = 0;

    // Collect all materials from selected scopes
    const materialsMap = new Map(); // To aggregate same materials

    document.querySelectorAll('.room-row').forEach(room => {
        const area = parseFloat(room.querySelector('input[name$="[area]"]').value) || 0;
        const roomName = room.querySelector('input[name$="[name]"]').value || `Room ${room.dataset.roomId}`;

        room.querySelectorAll('.scope-checkbox:checked').forEach(checkbox => {
            const scope = scopeTypes[checkbox.value];
            if (scope) {
                // Add materials
                if (scope.materials && Array.isArray(scope.materials)) {
                    scope.materials.forEach(material => {
                        const price = parseFloat(material.srp_price ?? material.base_price ?? 0) || 0;
                        const key = `${material.name}-${material.unit}`;
                        // Default isPerArea to false and coverage to 1 if missing
                        const isPerArea = material.isPerArea !== undefined ? material.isPerArea : false;
                        const coverage = material.coverage !== undefined ? parseFloat(material.coverage) || 1 : 1;
                        let quantity, totalCost;
                        if (isPerArea && coverage) {
                            quantity = area / coverage;
                            quantity = Math.ceil(quantity * 100) / 100;
                            totalCost = price * quantity;
                        } else if (isPerArea) {
                            quantity = area;
                            totalCost = price * area;
                        } else {
                            quantity = 1;
                            totalCost = price;
                        }
                        if (materialsMap.has(key)) {
                            const existing = materialsMap.get(key);
                            existing.quantity += quantity;
                            existing.totalCost += totalCost;
                            existing.rooms.push(roomName);
                        } else {
                            materialsMap.set(key, {
                                category: scope.category,
                                name: material.name,
                                unit: material.unit,
                                unitCost: price,
                                quantity: quantity,
                                totalCost: totalCost,
                                rooms: [roomName],
                                isPerArea: isPerArea,
                                coverage: coverage
                            });
                        }
                    });
                }
                // Add labor cost
                const laborRate = parseFloat(scope.labor_rate ?? 0) || 0;
                totalLaborCost += laborRate * area;
            }
        });
    });

    // Populate the table
    materialsMap.forEach((material, key) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${material.category}</td>
            <td>${material.name}</td>
            <td>${material.unit}</td>
            <td>₱${material.unitCost.toFixed(2)}</td>
            <td>${material.quantity.toFixed(2)} ${material.unit}${material.coverage ? ` (1 ${material.unit} covers ${material.coverage} sqm)` : ''}</td>
            <td>₱${material.totalCost.toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-sm btn-info" 
                    onclick="showMaterialDetails('${material.name}', ${JSON.stringify(material.rooms).replace(/'/g, "\\'")})">
                    <i class="fas fa-info-circle"></i> Details
                </button>
            </td>
        `;
        tableBody.appendChild(row);
        totalMaterialsCost += material.totalCost;
    });

    // Update totals
    document.getElementById('breakdownTotalMaterials').textContent = `₱${totalMaterialsCost.toFixed(2)}`;
    document.getElementById('breakdownTotalLabor').textContent = `₱${totalLaborCost.toFixed(2)}`;
    document.getElementById('breakdownGrandTotal').textContent = `₱${(totalMaterialsCost + totalLaborCost).toFixed(2)}`;
}

function showMaterialDetails(materialName, rooms) {
    const roomList = rooms.join(', ');
    Swal.fire({
        title: `Material: ${materialName}`,
        html: `
            <div class="text-start">
                <p><strong>Used in rooms:</strong></p>
                <p>${roomList}</p>
            </div>
        `,
        icon: 'info',
        confirmButtonText: 'Close'
    });
}
</script>
@endpush