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

    /* Make scope checkboxes larger and more visible */
    .scope-item .form-check-input {
        width: 1.2em;
        height: 1.2em;
        border: 2px solid #0d6efd;
        box-shadow: 0 0 2px #0d6efd44;
        margin-right: 0.5em;
    }
    .scope-item .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .scope-item .form-check-label {
        font-size: 1.1em;
        font-weight: 500;
    }
    
    /* Ensure days badges are always visible */
    .days-badge {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
        font-size: 0.875em;
        font-weight: 500;
        padding: 0.25em 0.5em;
        border-radius: 0.25rem;
        background-color: #17a2b8 !important;
        color: white !important;
        margin-left: 0.5rem;
    }
    
    .days-badge:empty {
        display: none !important;
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
                                            <div class="col-md-3">
                                                <p class="mb-1">Estimated Time:</p>
                                                <h5 id="grandTotalEstimatedDays">0 days</h5>
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
                                                <th>Warranty</th>
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
console.log('Script is running: Top of file');

// Debug logging
console.log('Initializing contract step 2 page');

// Use only the keyed object and convert to array for iteration
const scopeTypesByCode = {!! json_encode($scopeTypesByCode ?? []) !!};
const scopeTypes = Object.values(scopeTypesByCode); // Convert to array for iteration
console.log('Scope types (array) loaded:', scopeTypes);
console.log('Scope types (keyed) loaded:', scopeTypesByCode);

// Debug: Check if scope data has materials and tasks
console.log('=== SCOPE DATA DEBUG ===');
scopeTypes.forEach(scope => {
    console.log(`Processed scope (initial load): ${scope.name} (ID: ${scope.id}), materials type: ${typeof scope.materials}, tasks type: ${typeof scope.tasks}`);
    if (scope.materials) {
        console.log(`  - Materials: ${Array.isArray(scope.materials) ? scope.materials.length : 'not array'}`);
        if (Array.isArray(scope.materials) && scope.materials.length > 0) {
            console.log(`  - First material:`, scope.materials[0]);
        }
    }
    if (scope.tasks) {
        console.log(`  - Tasks: ${Array.isArray(scope.tasks) ? scope.tasks.length : 'not array'}`);
        if (Array.isArray(scope.tasks) && scope.tasks.length > 0) {
            console.log(`  - First task:`, scope.tasks[0]);
        }
    }
});
console.log('=== END SCOPE DATA DEBUG ===');

// Test getScopeMaterials function
if (scopeTypes.length > 0) {
    const firstScope = scopeTypes[0];
    console.log('=== TESTING getScopeMaterials ===');
    console.log('Testing with first scope:', firstScope.name);
    const materials = getScopeMaterials(firstScope);
    console.log('getScopeMaterials result:', materials);
    console.log('=== END TESTING getScopeMaterials ===');
}

const DEFAULT_CREW_SIZE = 4; // Standard crew size for most construction work
const DEFAULT_HOURS_PER_DAY = 8; // Standard working hours
const DEFAULT_PRODUCTIVITY_FACTOR = 0.85; // 85% productivity rate (industry standard)

// Add these validation functions after the existing constants
const MAX_DIMENSION = 100; // Maximum dimension in meters
const MAX_AREA = 1000; // Maximum area in square meters
const MIN_DIMENSION = 0.1; // Minimum dimension in meters
const MAX_LABOR_RATE = 1000; // Maximum labor rate per square meter
const MIN_LABOR_RATE = 50; // Minimum labor rate per square meter

function validateDimension(value, fieldName) {
    const num = parseFloat(value);
    if (isNaN(num)) {
        // Swal.fire({
        //     title: 'Invalid Input',
        //     text: `${fieldName} must be a valid number`,
        //     icon: 'error'
        // });
        // return false; // This was the problematic line, now completely removed
    }
    // if (num < MIN_DIMENSION) {
    //     Swal.fire({
    //         title: 'Invalid Input',
    //         text: `${fieldName} must be at least ${MIN_DIMENSION} meters`,
    //         icon: 'error'
    //     });
    //     return false;
    // }
    // if (num > MAX_DIMENSION) {
    //     Swal.fire({
    //         title: 'Invalid Input',
    //         text: `${fieldName} cannot exceed ${MAX_DIMENSION} meters`,
    //         icon: 'error'
    //     });
    //     return false;
    // }
    return true; // Always return true now, as we're disabling strict validation
}

function validateArea(area, areaType) {
    // if (area > MAX_AREA) {
    //     Swal.fire({
    //         title: 'Invalid Area',
    //         text: `${areaType} area cannot exceed ${MAX_AREA} square meters`,
    //         icon: 'error'
    //     });
    //     return false;
    // }
    return true; // Always return true
}

function validateLaborRate(rate) {
    const num = parseFloat(rate);
    if (isNaN(num)) {
        // Swal.fire({
        //     title: 'Invalid Labor Rate',
        //     text: 'Labor rate must be a valid number',
        //     icon: 'error'
        // });
        // return false; // Also remove this
    }
    // if (num < MIN_LABOR_RATE) {
    //     Swal.fire({
    //         title: 'Invalid Labor Rate',
    //         text: `Labor rate must be at least ₱${MIN_LABOR_RATE} per square meter`,
    //         icon: 'error'
    //     });
    //     return false;
    // }
    // if (num > MAX_LABOR_RATE) {
    //     Swal.fire({
    //         title: 'Invalid Labor Rate',
    //         text: `Labor rate cannot exceed ₱${MAX_LABOR_RATE} per square meter`,
    //         icon: 'error'
    //     });
    //     return false;
    // }
    return true; // Always return true
}

function validateMaterialQuantity(quantity, materialName) {
    const num = parseFloat(quantity);
    if (isNaN(num)) {
        // Swal.fire({
        //     title: 'Invalid Quantity',
        //     text: `Quantity for ${materialName} must be a valid number`,
        //     icon: 'error'
        // });
        // return false; // Also remove this
    }
    // if (num <= 0) {
    //     Swal.fire({
    //         title: 'Invalid Quantity',
    //         text: `Quantity for ${materialName} must be greater than 0`,
    //         icon: 'error'
    //     });
    //     return false;
    // }
    return true; // Always return true
}

// Helper for key adjustments
function getAdjustmentFactor(room, scope) {
    let factor = 1;
    
    // Room size adjustments
    const floorArea = parseFloat(room.querySelector('input[name$="[floor_area]"]').value) || 0;
    const wallArea = parseFloat(room.querySelector('input[name$="[wall_area]"]').value) || 0;
    const height = parseFloat(room.querySelector('input[name$="[height]"]').value) || 0;
    
    console.log(`getAdjustmentFactor for scope ${scope.name} (Room: ${room.dataset.roomId}): floorArea=${floorArea}, wallArea=${wallArea}, height=${height}`);

    // Large room adjustment (more than 50 sqm)
    if (floorArea > 50) factor *= 1.2; // 20% more time for large areas
    if (floorArea > 100) factor *= 1.3; // 30% more time for very large areas
    
    // Height adjustments for wall work
    if (scope.is_wall_work) {
        if (height > 3) factor *= 1.3; // 30% more time for high walls
        if (height > 4) factor *= 1.4; // 40% more time for very high walls
    }
    
    // Complexity adjustments based on scope type
    if (scope.complexity_factor) {
        factor *= parseFloat(scope.complexity_factor);
    }
    
    // Material-specific adjustments
    if (scope.materials) {
        const materials = Array.isArray(scope.materials) ? scope.materials : 
            (typeof scope.materials === 'string' ? JSON.parse(scope.materials) : []);
        
        materials.forEach(material => {
            // Add time for materials that require special handling
            if (material.requires_curing) factor *= 1.2;
            if (material.requires_priming) factor *= 1.15;
            if (material.is_delicate) factor *= 1.25;
        });
    }
    
    console.log(`Adjustment factor for scope ${scope.name}: ${factor}`);
    return factor;
}

function calculateAllCosts() {
    console.log('calculateAllCosts: Starting calculation.');
    const allRooms = document.querySelectorAll('.room-row');
    console.log(`calculateAllCosts: Found ${allRooms.length} room(s).`);

    let totalFloorArea = 0;
    let totalWallArea = 0;
    let totalLabor = 0;
    let totalEstimatedDays = 0;
    let materialsMap = new Map();

    allRooms.forEach(room => {
        const roomId = room.dataset.roomId;
        const roomNameInput = room.querySelector('input[name$="[name]"]');
        const roomName = roomNameInput ? roomNameInput.value : `Room ${roomId}`; // Fallback name
        const floorArea = parseFloat(room.querySelector('input[name$="[floor_area]"]').value) || 0;
        const wallArea = parseFloat(room.querySelector('input[name$="[wall_area]"]').value) || 0;
        console.log(`  calculateAllCosts: Processing Room ${roomName} (${roomId}): Floor Area = ${floorArea.toFixed(2)}, Wall Area = ${wallArea.toFixed(2)}`);
        
        // Accumulate total floor and wall areas
        totalFloorArea += floorArea;
        totalWallArea += wallArea;
        
        let roomLabor = 0;
        let roomMaterialsCost = 0;
        let roomEstimatedDays = 0;

        let roomMaterials = 0;
        
        // Get selected scopes for this room
        const selectedScopes = Array.from(room.querySelectorAll('input[type="checkbox"]:checked'))
            .map(cb => scopeTypesByCode[cb.value])
            .filter(Boolean);
        
        console.log(`  calculateAllCosts:   Room ${room.dataset.roomId} has ${selectedScopes.length} selected scopes.`);

        selectedScopes.forEach(scope => {
            if (!scope) return;
            
            // --- FIX: Use the materials from the current scope object, not the global scopeTypes mapping ---
            const materials = getScopeMaterials(scope);
            
            console.log(`  calculateAllCosts:     Scope ${scope.name} has ${materials.length} materials.`);

            materials.forEach(material => {
                if (!material.base_price || isNaN(material.base_price)) {
                    console.warn(`Skipping material with invalid price: ${material.name}`);
                    return;
                }
                const price = parseFloat(material.srp_price ?? 0) > 0 ? parseFloat(material.srp_price) : parseFloat(material.base_price ?? 0);
                let quantity = 0;
                const area = material.is_wall_material ? wallArea : floorArea;
                
                console.log(`  calculateAllCosts:     Material ${material.name} - is_per_area: ${material.is_per_area}, is_wall_material: ${material.is_wall_material}, coverage_rate: ${material.coverage_rate}, minimum_quantity: ${material.minimum_quantity}, calculated_area: ${area.toFixed(2)}`);

                if (material.is_per_area) {
                    const coverage = parseFloat(material.coverage_rate ?? 1) || 1;
                    quantity = area > 0 && coverage > 0 ? Math.ceil(area / coverage) : 0;
                } else {
                    quantity = parseFloat(material.minimum_quantity ?? 1) || 0;
                }
                if (quantity > 0) {
                    const wasteFactor = parseFloat(material.waste_factor ?? 1.1) || 1.1;
                    quantity = Math.ceil(quantity * wasteFactor);
                }
                let finalPrice = price;
                if (material.bulk_pricing) {
                    const bulkPricing = Array.isArray(material.bulk_pricing) ? material.bulk_pricing : Object.values(material.bulk_pricing || {});
                    for (const tier of bulkPricing) {
                        if (tier && typeof tier === 'object' && quantity >= (tier.min_quantity ?? 0)) {
                            finalPrice = parseFloat(tier.price ?? finalPrice) || 0;
                        }
                    }
                }
                const totalCost = finalPrice * quantity;
                roomMaterialsCost += totalCost;
                const key = `${material.name ?? 'Unnamed Material'}-${material.unit ?? 'pcs'}`;
                if (materialsMap.has(key)) {
                    const existing = materialsMap.get(key);
                    existing.quantity += quantity;
                    existing.totalCost += totalCost;
                    if (!existing.rooms.includes(roomName)) {
                        existing.rooms.push(roomName);
                    }
                } else {
                    materialsMap.set(key, {
                        category: scope.category ?? 'Uncategorized',
                        name: material.name ?? 'Unnamed Material',
                        unit: material.unit ?? 'pcs',
                        unitCost: finalPrice,
                        quantity: quantity,
                        totalCost: totalCost,
                        rooms: [roomName],
                        isPerArea: material.is_per_area ?? false,
                        coverage: material.coverage_rate ?? 1,
                        warranty_period: material.warranty_period || null
                    });
                }
                console.log(`  calculateAllCosts:     Material ${material.name}: Price=${price}, Quantity=${quantity}, Total Cost=${totalCost.toFixed(2)}`);
            });
            // --- Labor ---
            const laborRate = parseFloat(scope.labor_rate ?? 0) || 0;
            const complexityFactor = parseFloat(scope.complexity_factor ?? 1) || 1;
            const minimumLaborCost = parseFloat(scope.minimum_labor_cost ?? 0) || 0;
            let scopeLaborCost = 0;
            let scopeLaborHours = 0;
            // Calculate labor based on tasks
            if (scope.tasks) {
                const tasks = getScopeTasks(scope);
                
                const laborArea = scope.is_wall_work ? wallArea : floorArea;
                const adjustment = getAdjustmentFactor(room, scope);
                console.log(`  calculateAllCosts:     Scope ${scope.name} Tasks: laborArea=${laborArea}, adjustment=${adjustment}`);

                tasks.forEach(task => {
                    const taskHours = (task.labor_hours_per_sqm || 0) * laborArea;
                    console.log(`  calculateAllCosts:       Task ${task.name}: labor_hours_per_sqm=${task.labor_hours_per_sqm}, taskHours=${taskHours.toFixed(2)}`);
                    scopeLaborHours += taskHours;
                });
                scopeLaborCost = scopeLaborHours * laborRate;
            } else {
                // Fallback to old calculation method if no tasks defined
                const laborArea = scope.is_wall_work ? wallArea : floorArea;
                scopeLaborCost = laborRate * laborArea;
                console.log(`  calculateAllCosts:     Scope ${scope.name} (no tasks): laborArea=${laborArea}, laborRate=${laborRate}, scopeLaborCost=${scopeLaborCost.toFixed(2)}`);

            }
            scopeLaborCost = Math.max(minimumLaborCost, scopeLaborCost * complexityFactor);
            roomLabor += scopeLaborCost;
            console.log(`  calculateAllCosts:     Scope ${scope.name} - Final Labor Cost: ${scopeLaborCost.toFixed(2)}`);

            // --- Estimated Days --- // This part is now handled by getScopeEstimatedDays called in updateScopeDaysBadges
        });
        // Update room-specific display and hidden inputs
        room.querySelector('.materials-cost').textContent = `₱${roomMaterialsCost.toFixed(2)}`;
        room.querySelector('.labor-cost').textContent = `₱${roomLabor.toFixed(2)}`;
        room.querySelector('.total-cost').textContent = `₱${(roomMaterialsCost + roomLabor).toFixed(2)}`;
        room.querySelector('.materials-cost-hidden').value = roomMaterialsCost.toFixed(2);
        room.querySelector('.labor-cost-hidden').value = roomLabor.toFixed(2);
        console.log(`  calculateAllCosts: Room ${roomName} (${roomId}) - Room Materials: ${roomMaterialsCost.toFixed(2)}, Room Labor: ${roomLabor.toFixed(2)}`);

        // Estimated Time for the room row is updated by updateScopeDaysBadges

        totalLabor += roomLabor;
        // totalEstimatedDays is now summed up directly by updateScopeDaysBadges for the room, not globally here.
    });
    let totalMaterials = 0;
    materialsMap.forEach(material => {
        totalMaterials += material.totalCost;
    });
    console.log(`calculateAllCosts: Total Materials across all rooms: ${totalMaterials.toFixed(2)}`);
    console.log(`calculateAllCosts: Total Labor across all rooms: ${totalLabor.toFixed(2)}`);
    return {
        totalFloorArea,
        totalWallArea,
        totalMaterials,
        totalLabor,
        grandTotal: totalMaterials + totalLabor,
        materialsMap,
        totalEstimatedDays // This will remain 0 from this function, as room-specific badges handle days
    };
}

function calculateOverallEstimatedDays() {
    let overallEstimatedDays = 0;
    document.querySelectorAll('.room-row').forEach(room => {
        const estimatedTimeElem = room.querySelector('.estimated-time');
        if (estimatedTimeElem) {
            const daysText = estimatedTimeElem.textContent; // e.g., "5.5 days"
            const daysMatch = daysText.match(/([\d\.]+) day/);
            if (daysMatch && daysMatch[1]) {
                overallEstimatedDays += parseFloat(daysMatch[1]);
            }
        }
    });
    console.log(`calculateOverallEstimatedDays: Total estimated days from all rooms: ${overallEstimatedDays}`);
    return overallEstimatedDays;
}

function updateProjectTimeline() {
    console.log('updateProjectTimeline: Updating project timeline.');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    if (!startDateInput || !startDateInput.value) {
        console.log('updateProjectTimeline: Start date not set, skipping timeline update.');
        return;
    }
    const totalEstimatedDays = calculateOverallEstimatedDays(); // Recalculate to get overall estimated days
    console.log(`updateProjectTimeline: Total estimated days from all scopes: ${totalEstimatedDays}`);

    const startDate = new Date(startDateInput.value);
    const endDate = new Date(startDate);
    endDate.setDate(startDate.getDate() + Math.ceil(totalEstimatedDays));
    if (endDateInput) {
        endDateInput.value = endDate.toISOString().split('T')[0];
        console.log(`updateProjectTimeline: End date set to ${endDateInput.value}`);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    
    // Set initialization flag
    window.isInitializing = true;
    
    // Ensure initializeForm is called AFTER DOM is ready
    initializeForm();
    
    // Clear initialization flag after a short delay
    setTimeout(() => {
        window.isInitializing = false;
    }, 1000);
    
    // Debug the add room button
    const addRoomBtn = document.getElementById('addRoomBtn');
    console.log('Add Room Button:', addRoomBtn);
    
    if (addRoomBtn) {
        addRoomBtn.addEventListener('click', function(e) {
            console.log('Add Room button clicked');
            e.preventDefault();
            createRoomRow();
        });
    } else {
        console.error('Add Room button not found!');
    }
    
    document.getElementById('applyToAllBtn')?.addEventListener('click', applyScopesToAll);
    
    // Call updateProjectTimeline when start date changes
    document.getElementById('start_date')?.addEventListener('change', function() {
        updateProjectTimeline();
        if (!window.isInitializing) {
            saveFormData();
        }
    });
    
    // Call updateProjectTimeline when end date is manually changed
    document.getElementById('end_date')?.addEventListener('change', function() {
        updateProjectTimeline();
        if (!window.isInitializing) {
            saveFormData();
        }
    });
    
    // Call updateProjectTimeline when any scope checkbox changes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('scope-checkbox')) {
            const roomRow = e.target.closest('.room-row');
            
            // The setTimeout was causing a race condition with Bootstrap's accordion.
            // Running these updates synchronously resolves the issue.
            updateScopeDaysBadges(roomRow);
            updateGrandTotalAndBreakdown();
            updateProjectTimeline();
            if (!window.isInitializing) {
                saveFormData();
            }
        } else if (e.target.classList.contains('room-dimension')) {
            calculateRoomArea(e.target);
        }
    });

    // Add form submission handler
    const form = document.getElementById('step2Form');
    form.addEventListener('submit', function(e) {
        console.log('Form submission event detected.');
        // Ensure we save the final state before submitting
        if (!window.isInitializing) {
            saveFormData();
        }
    });

    // Add window unload handler to save data when navigating away
    window.addEventListener('beforeunload', function() {
        console.log('Window is about to unload, saving data.');
        if (!window.isInitializing) {
            saveFormData();
        }
    });
});

// Ensure materials and tasks are consistently arrays
scopeTypes.forEach(scope => {
    ['tasks', 'materials'].forEach(prop => {
        // First, if it's a string, try to parse it
        if (typeof scope[prop] === 'string') {
            try {
                scope[prop] = JSON.parse(scope[prop]);
            } catch (e) {
                console.error(`Error parsing ${prop} for scope:`, scope.id, e);
                scope[prop] = [];
            }
        }
        
        // Next, if it's a non-array object, convert it to an array
        if (scope[prop] && typeof scope[prop] === 'object' && !Array.isArray(scope[prop])) {
            scope[prop] = Object.values(scope[prop]);
        } 
        // Finally, if it's missing, ensure it's an empty array
        else if (!scope[prop]) {
            scope[prop] = [];
        }
    });
});

const scopesByCategory = {};
scopeTypes.forEach(scope => {
    if (!scopesByCategory[scope.category]) scopesByCategory[scope.category] = [];
    scopesByCategory[scope.category].push(scope);
});

// Get session data
const sessionData = @json($sessionData ?? []);
console.log('Client-side sessionData received:', sessionData);

// Helper to generate a stable room id
function getRoomId(room, idx) {
    if (room.id) return room.id;
    if (room._id) return room._id;
    return `room_${idx}_${Date.now()}`;
}

function saveFormData() {
    console.log('saveFormData called');
    
    // Don't save if we're just initializing
    if (window.isInitializing) {
        console.log('Skipping save during initialization');
        return Promise.resolve();
    }
    
    const form = document.getElementById('step2Form');
    const formData = new FormData(form);
    const data = {
        rooms: [],
        start_date: formData.get('start_date'),
        end_date: formData.get('end_date'),
        total_materials: formData.get('total_materials'),
        total_labor: formData.get('total_labor'),
        grand_total: formData.get('grand_total'),
        total_amount: formData.get('grand_total'),
        labor_cost: formData.get('total_labor'),
        materials_cost: formData.get('total_materials')
    };

    const roomElements = document.querySelectorAll('.room-row');
    console.log(`saveFormData: Found ${roomElements.length} room(s) in the DOM.`);

    // Validate that we have at least one room
    if (roomElements.length === 0) {
        console.log('No rooms found, skipping save');
        return Promise.resolve();
    }

    roomElements.forEach((room, idx) => {
        const roomId = room.dataset.roomId;
        const roomData = {
            id: roomId,
            name: formData.get(`rooms[${roomId}][name]`),
            length: formData.get(`rooms[${roomId}][length]`),
            width: formData.get(`rooms[${roomId}][width]`),
            height: formData.get(`rooms[${roomId}][height]`),
            floor_area: formData.get(`rooms[${roomId}][floor_area]`),
            wall_area: formData.get(`rooms[${roomId}][wall_area]`),
            area: formData.get(`rooms[${roomId}][area]`),
            materials_cost: formData.get(`rooms[${roomId}][materials_cost]`),
            labor_cost: formData.get(`rooms[${roomId}][labor_cost]`),
            scope: Array.from(room.querySelectorAll('input[type="checkbox"]:checked')).map(cb => cb.value)
        };
        
        // Only add room if it has valid data
        if (roomData.name && roomData.length && roomData.width && roomData.height) {
            data.rooms.push(roomData);
            console.log(`saveFormData: Added room ${roomData.name} (${roomData.id}) to save data.`);
        }
    });

    // Only save if we have valid rooms
    if (data.rooms.length === 0) {
        console.log('No valid rooms to save, skipping save');
        return Promise.resolve();
    }

    console.log('saveFormData: Data being sent to server:', data);

    return fetch('{{ route("contracts.save.step2") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    }).then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    }).then(data => {
        console.log('Data saved successfully:', data);
    }).catch(error => {
        console.error('Error saving data:', error);
    });
}

function initializeForm() {
    console.log('Initializing form with session data.');
    
    // Clear any existing rooms first
    const roomDetails = document.getElementById('roomDetails');
    roomDetails.innerHTML = '';
    
    // Convert rooms object to array if it's an object and not already an array
    let roomsToInitialize = [];
    if (sessionData && sessionData.rooms) {
        if (Array.isArray(sessionData.rooms)) {
            roomsToInitialize = sessionData.rooms;
        } else if (typeof sessionData.rooms === 'object' && sessionData.rooms !== null) {
            // If it's an object, convert its values to an array
            roomsToInitialize = Object.values(sessionData.rooms);
        }
    }

    // Check if we have valid session data and if it contains rooms
    if (roomsToInitialize.length > 0) {
        console.log('Found existing session data, initializing rooms:', roomsToInitialize);
        roomsToInitialize.forEach(room => {
            createRoomRow(room);
        });
    } else {
        console.log('No valid session data for rooms found. Starting with an empty room section.');
        // Do NOT call createRoomRow() here. The user will click 'Add Room/Area' to add the first room.
    }
    
    // Reset all totals to zero initially (these are for the grand total summary, not room specific)
    document.getElementById('total_materials').value = '0';
    document.getElementById('total_labor').value = '0';
    document.getElementById('grand_total').value = '0';
    document.getElementById('total_area').value = '0';
    
    // Update displays to reflect zero totals
    document.getElementById('grandTotalArea').textContent = '0.00 sq m';
    document.getElementById('grandTotalMaterials').textContent = '₱0.00';
    document.getElementById('grandTotalLabor').textContent = '₱0.00';
    document.getElementById('grandTotal').textContent = '₱0.00';
    
    // Initial timeline update and cost update after form is potentially initialized
    updateGrandTotalAndBreakdown();
    updateProjectTimeline();
}

function createRoomRow(initialRoomData = {}) {
    console.log('Creating new room row', initialRoomData);
    
    // Ensure scope is always an array
    if (!initialRoomData.scope || !Array.isArray(initialRoomData.scope)) {
        initialRoomData.scope = [];
    }
    
    console.log('initialRoomData.scope:', initialRoomData.scope);
    const roomContainer = document.createElement('div');
    roomContainer.className = 'room-row mb-4';
    const roomId = initialRoomData.id || Date.now(); // Use existing ID or generate new
    roomContainer.dataset.roomId = roomId;
    roomContainer.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Room/Area Name</label>
                    <input type="text" class="form-control" name="rooms[${roomId}][name]" required value="${initialRoomData.name || ''}" autocomplete="off">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Length (m)</label>
                    <input type="number" class="form-control room-dimension" name="rooms[${roomId}][length]" step="0.01" min="0.01" value="${initialRoomData.length || ''}" autocomplete="off">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Width (m)</label>
                    <input type="number" class="form-control room-dimension" name="rooms[${roomId}][width]" step="0.01" min="0.01" value="${initialRoomData.width || ''}" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Height (m)</label>
                    <input type="number" class="form-control room-dimension" name="rooms[${roomId}][height]" step="0.01" min="0.01" value="${initialRoomData.height || ''}" autocomplete="off">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Floor Area (sq m)</label>
                    <input type="number" class="form-control" name="rooms[${roomId}][floor_area]" readonly value="${initialRoomData.floor_area || '0.00'}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Wall Area (sq m)</label>
                    <input type="number" class="form-control" name="rooms[${roomId}][wall_area]" readonly value="${initialRoomData.wall_area || '0.00'}">
                </div>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-group w-100">
                    <label class="form-label visually-hidden">Remove Room</label>
                    <button type="button" class="btn btn-danger w-100" onclick="removeRoom(this)">
                        <i class="fas fa-trash"></i> Remove Room
                    </button>
                </div>
            </div>
        </div>
        <input type="hidden" name="rooms[${roomId}][materials_cost]" class="materials-cost-hidden" value="${initialRoomData.materials_cost || 0}">
        <input type="hidden" name="rooms[${roomId}][labor_cost]" class="labor-cost-hidden" value="${initialRoomData.labor_cost || 0}">
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="mb-3">Scope of Work</h6>
                <div class="accordion" id="scopeAccordion${roomId}">
                    ${Object.entries(scopesByCategory).map(([category, scopes], categoryIndex) => `
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading${roomId}${categoryIndex}">
                                <button class="accordion-button ${categoryIndex > 0 ? 'collapsed' : ''}" type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#category${roomId}${categoryIndex}"
                                    aria-expanded="${categoryIndex === 0}"
                                    aria-controls="category${roomId}${categoryIndex}">
                                    ${category}
                                </button>
                            </h2>
                            <div id="category${roomId}${categoryIndex}" 
                                class="accordion-collapse collapse ${categoryIndex === 0 ? 'show' : ''}"
                                aria-labelledby="heading${roomId}${categoryIndex}"
                                data-bs-parent="#scopeAccordion${roomId}">
                                <div class="accordion-body">
                                    <div class="row">
                                        ${scopes.map(scope => {
                                            const scopeMaterials = getScopeMaterials(scope);
                                            const scopeTasks = getScopeTasks(scope);
                                            console.log(`Checking scope: ${scope.id}, initialRoomData.scope:`, initialRoomData.scope, `includes(${scope.id.toString()}):`, initialRoomData.scope && initialRoomData.scope.includes(scope.id.toString()));
                                            return `
                                            <div class="col-md-6">
                                                <div class="scope-item mb-4">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input scope-checkbox" 
                                                            name="rooms[${roomId}][scope][]" 
                                                            value="${scope.id}" 
                                                            id="scope_${scope.id}_${roomId}" 
                                                            ${initialRoomData.scope && initialRoomData.scope.includes(scope.id.toString()) ? 'checked' : ''}> <!-- Re-add check for session data -->
                                                        <label class="form-check-label" for="scope_${scope.id}_${roomId}">
                                                            <strong>${scope.name}</strong>
                                                        </label>
                                                    </div>
                                                    <div class="ms-4 mt-2">
                                                        <small class="text-muted">Materials:</small>
                                                        <ul class="list-unstyled small">
                                                            ${scopeMaterials.length > 0 ? scopeMaterials.map(material => `<li>${material.name || 'Unnamed Material'} - ₱${(parseFloat(material.srp_price ?? 0) > 0 ? parseFloat(material.srp_price) : parseFloat(material.base_price ?? 0)).toFixed(2)}</li>`).join('') : '<li><em>No materials assigned</em></li>'}
                                                        </ul>
                                                        <small class="text-muted">Tasks:</small>
                                                        <ul class="list-unstyled small">
                                                            ${scopeTasks.length > 0 ? scopeTasks.map(task => `<li><i class="fas fa-check-circle text-success"></i> ${task.name || 'Unnamed Task'}</li>`).join('') : '<li><em>No tasks listed</em></li>'}
                                                        </ul>
                                                        <div class="ms-4 mt-2">
                                                            <small class="text-muted">Estimated Time:</small>
                                                            <span class="badge bg-info ms-2 days-badge" data-scope-id="${scope.id}"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                        }).join('')}
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
                    <h6 class="materials-cost">₱${parseFloat(initialRoomData.materials_cost || 0).toFixed(2)}</h6>
                </div>
                <div class="col-md-4">
                    <p class="mb-1">Labor Cost:</p>
                    <h6 class="labor-cost">₱${parseFloat(initialRoomData.labor_cost || 0).toFixed(2)}</h6>
                </div>
                <div class="col-md-4">
                    <p class="mb-1">Total Cost:</p>
                    <h6 class="total-cost">₱${(parseFloat(initialRoomData.materials_cost || 0) + parseFloat(initialRoomData.labor_cost || 0)).toFixed(2)}</h6>
                </div>
                <div class="col-md-4">
                    <p class="mb-1">Estimated Time:</p>
                    <h6 class="estimated-time">${initialRoomData.estimated_time || '0'} days</h6>
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

    // Trigger initial calculation after adding the room to ensure areas are computed.
    // We can simulate an input event on one of the dimension fields.
    const lengthInput = roomContainer.querySelector('input[name$="[length]"]');
    if (lengthInput) {
        calculateRoomArea(lengthInput);
    }

    // It's crucial to call updateScopeDaysBadges after room areas are calculated.
    // Add a small delay to ensure calculations are complete
    setTimeout(() => {
        updateScopeDaysBadges(roomContainer);
        updateGrandTotalAndBreakdown(); // Force update global totals
        updateProjectTimeline(); // Force update timeline
    }, 200);

    console.log('New room row created and initial calculations triggered.');
}

function removeRoom(button) {
    const roomRow = button.closest('.room-row');
    roomRow.remove();
    updateGrandTotalAndBreakdown();
    updateProjectTimeline();
    saveFormData();
}

function calculateRoomArea(input) {
    const roomRow = input.closest('.room-row');
    const lengthInput = roomRow.querySelector('input[name$="[length]"]');
    const widthInput = roomRow.querySelector('input[name$="[width]"]');
    const heightInput = roomRow.querySelector('input[name$="[height]"]');
    const floorAreaInput = roomRow.querySelector('input[name$="[floor_area]"]');
    const wallAreaInput = roomRow.querySelector('input[name$="[wall_area]"]');

    // Validate dimensions
    if (!validateDimension(lengthInput.value, 'Length') ||
        !validateDimension(widthInput.value, 'Width') ||
        !validateDimension(heightInput.value, 'Height')) {
        return;
    }

    const length = parseFloat(lengthInput.value);
    const width = parseFloat(widthInput.value);
    const height = parseFloat(heightInput.value);
    
    // Calculate areas
    const floorArea = length * width;
    const wallArea = 2 * (length + width) * height;

    // Validate areas
    if (!validateArea(floorArea, 'Floor') || !validateArea(wallArea, 'Wall')) {
        return;
    }

    floorAreaInput.value = floorArea.toFixed(2);
    wallAreaInput.value = wallArea.toFixed(2);
    
    updateGrandTotalAndBreakdown();
    saveFormData();
    
    // Add a small delay to ensure areas are updated before updating badges
    setTimeout(() => {
        updateScopeDaysBadges(roomRow);
    }, 100);
}

function applyScopesToAll() {
    const rooms = document.querySelectorAll('.room-row');
    if (rooms.length < 2) return;

    const firstRoom = rooms[0];
    const selectedScopes = Array.from(firstRoom.querySelectorAll('.scope-checkbox:checked'))
        .map(checkbox => checkbox.value);

    rooms.forEach((room, index) => {
        if (index === 0) return;
        
        room.querySelectorAll('.scope-checkbox').forEach(checkbox => {
            checkbox.checked = selectedScopes.includes(checkbox.value);
        });
        // Recalculate costs for this room after applying scopes
        calculateRoomArea(room.querySelector('input[name$="[length]"]')); // Trigger calculation for each room
    });
    // updateGrandTotalAndBreakdown(); // This will be called by calculateRoomArea
    // saveFormData(); // This will be called by calculateRoomArea
}

function updateGrandTotalAndBreakdown() {
    console.log('Calculating all costs for grand total and breakdown.');
    const { totalFloorArea, totalWallArea, totalMaterials, totalLabor, grandTotal, materialsMap } = calculateAllCosts();

    // Calculate materials cost from breakdown (materialsMap) - already done in calculateAllCosts
    let breakdownMaterials = 0; // This variable is no longer needed, totalMaterials from calculateAllCosts is correct
    materialsMap.forEach(material => {
        breakdownMaterials += material.totalCost;
    });

    // Update summary (use totalMaterials for both summary and breakdown)
    document.getElementById('grandTotalArea').textContent = `${totalFloorArea.toFixed(2)} sq m`;
    document.getElementById('grandTotalMaterials').textContent = `₱${totalMaterials.toFixed(2)}`;
    document.getElementById('grandTotalLabor').textContent = `₱${totalLabor.toFixed(2)}`;
    document.getElementById('grandTotal').textContent = `₱${(totalMaterials + totalLabor).toFixed(2)}`;
    document.getElementById('grandTotalEstimatedDays').textContent = `${calculateOverallEstimatedDays()} day${calculateOverallEstimatedDays() > 1 ? 's' : ''}`;

    document.getElementById('total_area').value = (totalFloorArea + totalWallArea).toFixed(2);
    document.getElementById('total_materials').value = totalMaterials.toFixed(2);
    document.getElementById('total_labor').value = totalLabor.toFixed(2);
    document.getElementById('grand_total').value = (totalMaterials + totalLabor).toFixed(2);

    console.log(`Grand Totals: Area=${totalFloorArea.toFixed(2)}, Materials=₱${totalMaterials.toFixed(2)}, Labor=₱${totalLabor.toFixed(2)}, Grand=₱${(totalMaterials + totalLabor).toFixed(2)}`);
    console.log(`Number of rooms found by updateGrandTotalAndBreakdown: ${document.querySelectorAll('.room-row').length}`); // Added log

    // Update breakdown
    const tableBody = document.getElementById('breakdownTableBody');
    tableBody.innerHTML = '';
    if (document.querySelectorAll('.room-row').length === 0 || materialsMap.size === 0) {
        // No rooms or no materials: clear breakdown and set totals to zero
        document.getElementById('breakdownTotalMaterials').textContent = '₱0.00';
        document.getElementById('breakdownTotalLabor').textContent = '₱0.00';
        document.getElementById('breakdownGrandTotal').textContent = '₱0.00';
        return;
    }
    materialsMap.forEach(material => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${material.category}</td>
            <td>${material.name}</td>
            <td>${material.unit}</td>
            <td>₱${material.unitCost.toFixed(2)}</td>
            <td>${material.quantity.toFixed(2)} ${material.unit}${material.coverage ? ` (1 ${material.unit} covers ${material.coverage} sqm)` : ''}</td>
            <td>₱${material.totalCost.toFixed(2)}</td>
            <td>${material.warranty_period ? material.warranty_period + ' months' : 'No warranty'}</td>
            <td>
                <button type="button" class="btn btn-sm btn-info" 
                    onclick="showMaterialDetails('${material.name}', ${JSON.stringify(material.rooms).replace(/'/g, "\'")})">
                    <i class="fas fa-info-circle"></i> Details
                </button>
            </td>
        `;
        tableBody.appendChild(row);
    });
    document.getElementById('breakdownTotalMaterials').textContent = `₱${totalMaterials.toFixed(2)}`; // Use totalMaterials
    document.getElementById('breakdownTotalLabor').textContent = `₱${totalLabor.toFixed(2)}`;
    document.getElementById('breakdownGrandTotal').textContent = `₱${(totalMaterials + totalLabor).toFixed(2)}`;

    // updateProjectTimeline(); // Ensure timeline updates after all costs - this is now called from calculateRoomArea and initializeForm
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

function getScopeMaterials(scope) {
    if (!scope.materials) {
        console.log('Scope materials is null or undefined for scope:', scope.id);
        return [];
    }

    let materialsArray = [];
    
    try {
        // If it's a string, try to parse it as JSON
        if (typeof scope.materials === 'string') {
            // console.log('Parsing materials JSON string:', scope.materials);
            materialsArray = JSON.parse(scope.materials);
        } 
        // If it's already an array, use it directly
        else if (Array.isArray(scope.materials)) {
            materialsArray = scope.materials;
        }
        // If it's an object, convert to array
        else if (typeof scope.materials === 'object' && scope.materials !== null) {
            materialsArray = Object.values(scope.materials);
        }

        // Ensure materialsArray is actually an array
        if (!Array.isArray(materialsArray)) {
            materialsArray = [materialsArray];
        }

        return materialsArray.map(material => {
            if (!material || typeof material !== 'object') {
                console.warn('Invalid material found:', material);
                return null;
            }
            // Log the material object here to inspect its structure
            // console.log('Material object in getScopeMaterials:', material.name, 'Warranty Period:', material.warranty_period, 'Type:', typeof material.warranty_period); // REMOVED LOG

            // Validate material quantity
            const quantity = material.minimum_quantity || 1;
            if (!validateMaterialQuantity(quantity, material.name)) {
                return null;
            }

            // Handle bulk pricing
            let bulkPricing = [];
            if (material.bulk_pricing) {
                if (Array.isArray(material.bulk_pricing)) {
                    bulkPricing = material.bulk_pricing;
                } else if (typeof material.bulk_pricing === 'string') {
                    try {
                        bulkPricing = JSON.parse(material.bulk_pricing);
                    } catch (e) {
                        console.warn('Failed to parse bulk pricing:', e);
                        bulkPricing = [];
                    }
                } else if (typeof material.bulk_pricing === 'object') {
                    bulkPricing = Object.values(material.bulk_pricing);
                }
            }

            return {
                id: material.id || null,
                name: material.name || 'Unnamed Material',
                srp_price: parseFloat(material.srp_price ?? 0) > 0 ? parseFloat(material.srp_price) : parseFloat(material.base_price ?? 0),
                base_price: parseFloat(material.base_price ?? 0),
                unit: material.unit || 'pcs',
                is_per_area: material.is_per_area || material.isPerArea || false,
                coverage_rate: parseFloat(material.coverage_rate || 1),
                minimum_quantity: parseInt(material.minimum_quantity || 1),
                waste_factor: parseFloat(material.waste_factor || 1.1),
                bulk_pricing: bulkPricing,
                warranty_period: material.warranty_period || null // Ensure warranty period is passed
            };
        }).filter(m => m !== null);
    } catch (e) {
        console.error('Error processing materials:', e);
        return [];
    }
}

function getScopeTasks(scope) {
    if (!scope || !scope.tasks) {
        return [];
    }
    if (Array.isArray(scope.tasks)) {
        return scope.tasks;
    }
    if (typeof scope.tasks === 'object' && scope.tasks !== null) {
        return Object.values(scope.tasks);
    }
    if (typeof scope.tasks === 'string') {
        try {
            const parsed = JSON.parse(scope.tasks);
            return Array.isArray(parsed) ? parsed : Object.values(parsed);
        } catch (e) {
            console.error('Error parsing scope tasks JSON:', e);
            return [];
        }
    }
    return [];
}

// Add this helper to calculate estimated days for a scope in a room
function getScopeEstimatedDays(scope, room) {
    const floorArea = parseFloat(room.querySelector('input[name$="[floor_area]"]').value) || 0;
    const wallArea = parseFloat(room.querySelector('input[name$="[wall_area]"]').value) || 0;
    
    // Validate labor rate
    if (!validateLaborRate(scope.labor_rate)) {
        return 0;
    }

    const adjustment = getAdjustmentFactor(room, scope);
    const crewSize = DEFAULT_CREW_SIZE;
    const hoursPerDay = DEFAULT_HOURS_PER_DAY;
    const productivityFactor = DEFAULT_PRODUCTIVITY_FACTOR;
    
    console.log(`Calculating days for scope: ${scope.name} (Room: ${room.dataset.roomId})`);
    console.log(`  Floor Area: ${floorArea}, Wall Area: ${wallArea}, Adjustment: ${adjustment}`);

    let totalLaborHours = 0;
    
    // Calculate based on tasks if available
    if (scope.tasks) {
        const tasks = getScopeTasks(scope);
        
        tasks.forEach(task => {
            const laborArea = scope.is_wall_work ? wallArea : floorArea;
            const baseHours = (task.labor_hours_per_sqm || 0) * laborArea;
            totalLaborHours += baseHours;
            console.log(`    Task: ${task.name}, labor_hours_per_sqm: ${task.labor_hours_per_sqm}, laborArea: ${laborArea}, baseHours: ${baseHours}`);
        });
    } else {
        // Fallback calculation based on area and scope type
        const laborArea = scope.is_wall_work ? wallArea : floorArea;
        const baseRate = scope.labor_rate || 0.5; // Default 0.5 hours per sqm if not specified
        totalLaborHours = laborArea * baseRate;
        console.log(`    Fallback calculation: laborArea=${laborArea}, baseRate=${baseRate}, totalLaborHours=${totalLaborHours}`);
    }
    
    console.log(`  Total Labor Hours (raw): ${totalLaborHours}`);

    // Apply adjustments and productivity factor
    totalLaborHours *= adjustment;
    totalLaborHours /= productivityFactor;
    
    console.log(`  Total Labor Hours (adjusted): ${totalLaborHours}`);

    // Calculate days considering crew size and working hours
    let days = totalLaborHours / (crewSize * hoursPerDay);
    
    // Round up to nearest half day
    days = Math.ceil(days * 2) / 2;
    
    // Ensure minimum of 0.5 days for any work
    const finalDays = Math.max(0.5, days);
    console.log(`  Calculated Days: ${finalDays} (raw days: ${days})`);
    return finalDays;
}

function updateScopeDaysBadges(room) {
    console.log(`Updating scope days badges for Room: ${room.dataset.roomId}`);
    const scopeCheckboxes = room.querySelectorAll('.scope-checkbox');
    let totalDays = 0;
    
    scopeCheckboxes.forEach(cb => {
        const scope = scopeTypesByCode[cb.value];
        let badge = null;
        
        // Try multiple strategies to find the badge element
        const scopeId = cb.value;
        
        // Strategy 1: Look for badge with data-scope-id attribute
        badge = room.querySelector(`.days-badge[data-scope-id="${scopeId}"]`);
        
        // Strategy 2: If not found, look in the parent scope-item div
        if (!badge) {
            const scopeItem = cb.closest('.scope-item');
            if (scopeItem) {
                badge = scopeItem.querySelector('.days-badge');
            }
        }
        
        // Strategy 3: Look in the next sibling element
        if (!badge && cb.nextElementSibling) {
            badge = cb.nextElementSibling.querySelector('.days-badge');
        }
        
        // Strategy 4: Look in the parent form-check element
        if (!badge) {
            const formCheck = cb.closest('.form-check');
            if (formCheck) {
                badge = formCheck.querySelector('.days-badge');
            }
        }
        
        if (scope && badge) {
            const days = getScopeEstimatedDays(scope, room);
            console.log(`  Scope ${scope.name} (ID: ${scope.id}) - Days: ${days}, Checked: ${cb.checked}`);
            if (cb.checked) {
                totalDays += days;
            }
            
            // Update badge content with proper formatting
            if (days === 0.5) {
                badge.textContent = '0.5 day';
            } else if (days === 1) {
                badge.textContent = '1 day';
            } else {
                badge.textContent = `${days} days`;
            }
            
            // Ensure badge is visible
            badge.style.display = 'inline-block';
            badge.style.visibility = 'visible';
            
            console.log(`  Updated badge for scope ${scope.name}: "${badge.textContent}"`);
        } else {
            console.warn(`  Could not find badge for scope ${scope ? scope.name : 'unknown'} (ID: ${scopeId})`);
        }
    });
    
    // Update the room's total estimated time
    const estimatedTimeElem = room.querySelector('.estimated-time');
    if (estimatedTimeElem) {
        if (totalDays === 0.5) {
            estimatedTimeElem.textContent = '0.5 day';
        } else if (totalDays === 1) {
            estimatedTimeElem.textContent = '1 day';
        } else {
            estimatedTimeElem.textContent = `${totalDays} days`;
        }
        console.log(`  Room Total Estimated Time: ${totalDays} days`);
    }
}
</script>
@endpush