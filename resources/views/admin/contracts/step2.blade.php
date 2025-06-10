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
// Scope materials and their costs
const scopeMaterials = {
    // Building Finishing
    'drywall': {
        name: 'Drywall & Insulation',
        category: 'Building Finishing',
        estimatedDays: 3,
        materials: [
            { name: 'Drywall Sheets', cost: 300, unit: 'sheet', isPerArea: true, coverage: 3 },
            { name: 'Insulation Material', cost: 200, unit: 'roll', isPerArea: true, coverage: 4 },
            { name: 'Joint Compound', cost: 150, unit: 'bucket', isPerArea: false }
        ],
        labor: 250,
        items: [
            'Frame installation',
            'Drywall hanging',
            'Insulation installation',
            'Taping and mudding',
            'Sanding and finishing'
        ]
    },
    'ceiling': {
        name: 'Ceiling Work',
        category: 'Building Finishing',
        estimatedDays: 4,
        materials: [
            { name: 'Ceiling Boards', cost: 400, unit: 'board', isPerArea: true, coverage: 3 },
            { name: 'Support Structure', cost: 200, unit: 'meter', isPerArea: true, coverage: 2 },
            { name: 'Fasteners', cost: 50, unit: 'per set', isPerArea: false }
        ],
        labor: 250,
        items: [
            'Frame installation',
            'Board installation',
            'Joint treatment',
            'Surface finishing',
            'Fixture mounting points'
        ]
    },
    'flooring': {
        name: 'Flooring Installation',
        category: 'Building Finishing',
        estimatedDays: 5,
        materials: [
            { name: 'Flooring Material', cost: 500, unit: 'sqm', isPerArea: true, coverage: 1 },
            { name: 'Underlayment', cost: 100, unit: 'roll', isPerArea: true, coverage: 5 },
            { name: 'Adhesive', cost: 150, unit: 'bucket', isPerArea: false }
        ],
        labor: 300,
        items: [
            'Subfloor preparation',
            'Underlayment installation',
            'Flooring layout',
            'Material installation',
            'Finishing and sealing'
        ]
    },
    'cabinetry': {
        name: 'Cabinetry & Millwork',
        category: 'Building Finishing',
        estimatedDays: 4,
        materials: [
            { name: 'Cabinet Units', cost: 1000, unit: 'unit', isPerArea: false },
            { name: 'Hardware', cost: 200, unit: 'set', isPerArea: false },
            { name: 'Trim Material', cost: 150, unit: 'meter', isPerArea: true, coverage: 2 }
        ],
        labor: 400,
        items: [
            'Cabinet assembly',
            'Installation',
            'Hardware mounting',
            'Trim work',
            'Final adjustments'
        ]
    },

    // Nonresidential Construction
    'industrial': {
        name: 'Industrial Construction',
        category: 'Nonresidential Building',
        estimatedDays: 30,
        materials: [
            { name: 'Structural Steel', cost: 5000, unit: 'ton', isPerArea: true, coverage: 50 },
            { name: 'Concrete', cost: 3000, unit: 'cubic meter', isPerArea: true, coverage: 10 },
            { name: 'Industrial Equipment', cost: 10000, unit: 'unit', isPerArea: false }
        ],
        labor: 1000,
        items: [
            'Site preparation',
            'Foundation work',
            'Structural framework',
            'Equipment installation',
            'Safety systems'
        ]
    },
    'commercial': {
        name: 'Commercial Construction',
        category: 'Nonresidential Building',
        estimatedDays: 25,
        materials: [
            { name: 'Building Materials', cost: 3000, unit: 'sqm', isPerArea: true, coverage: 1 },
            { name: 'HVAC Systems', cost: 5000, unit: 'unit', isPerArea: false },
            { name: 'Electrical Systems', cost: 2000, unit: 'unit', isPerArea: false }
        ],
        labor: 800,
        items: [
            'Building framework',
            'Interior finishing',
            'HVAC installation',
            'Electrical work',
            'Final inspection'
        ]
    },

    // Carpentry & Woodwork
    'carpentry': {
        name: 'Custom Carpentry',
        category: 'Carpentry & Woodwork',
        estimatedDays: 5,
        materials: [
            { name: 'Wood Materials', cost: 300, unit: 'board foot', isPerArea: true, coverage: 2 },
            { name: 'Hardware', cost: 150, unit: 'set', isPerArea: false },
            { name: 'Finishing Materials', cost: 200, unit: 'gallon', isPerArea: false }
        ],
        labor: 350,
        items: [
            'Custom design',
            'Material cutting',
            'Assembly',
            'Installation',
            'Finishing work'
        ]
    },
    'decking': {
        name: 'Deck & Stair Construction',
        category: 'Carpentry & Woodwork',
        estimatedDays: 4,
        materials: [
            { name: 'Decking Material', cost: 400, unit: 'sqm', isPerArea: true, coverage: 1 },
            { name: 'Support Structure', cost: 300, unit: 'meter', isPerArea: true, coverage: 2 },
            { name: 'Fasteners', cost: 100, unit: 'set', isPerArea: false }
        ],
        labor: 300,
        items: [
            'Frame construction',
            'Decking installation',
            'Stair construction',
            'Railing installation',
            'Finishing touches'
        ]
    },

    // Doors & Windows
    'doors_windows': {
        name: 'Doors & Windows Installation',
        category: 'Doors & Windows',
        estimatedDays: 3,
        materials: [
            { name: 'Doors', cost: 800, unit: 'piece', isPerArea: false },
            { name: 'Windows', cost: 1200, unit: 'piece', isPerArea: false },
            { name: 'Hardware', cost: 200, unit: 'set', isPerArea: false }
        ],
        labor: 250,
        items: [
            'Frame preparation',
            'Installation',
            'Hardware mounting',
            'Weather sealing',
            'Final adjustments'
        ]
    },
    'garage_doors': {
        name: 'Garage Door Installation',
        category: 'Doors & Windows',
        estimatedDays: 2,
        materials: [
            { name: 'Garage Door', cost: 2000, unit: 'unit', isPerArea: false },
            { name: 'Opener System', cost: 800, unit: 'unit', isPerArea: false },
            { name: 'Hardware', cost: 150, unit: 'set', isPerArea: false }
        ],
        labor: 300,
        items: [
            'Door assembly',
            'Track installation',
            'Opener installation',
            'Safety system setup',
            'Testing and adjustment'
        ]
    },

    // Painting & Surface Finishing
    'painting': {
        name: 'Painting & Surface Finishing',
        category: 'Painting & Surface',
        estimatedDays: 4,
        materials: [
            { name: 'Paint', cost: 250, unit: 'gallon', isPerArea: true, coverage: 10 },
            { name: 'Primer', cost: 100, unit: 'gallon', isPerArea: true, coverage: 10 },
            { name: 'Wallpaper', cost: 300, unit: 'roll', isPerArea: true, coverage: 5 }
        ],
        labor: 150,
        items: [
            'Surface preparation',
            'Primer application',
            'Paint application',
            'Wallpaper installation',
            'Touch-ups'
        ]
    },
    'special_painting': {
        name: 'Special Painting Services',
        category: 'Painting & Surface',
        estimatedDays: 5,
        materials: [
            { name: 'Special Paint', cost: 400, unit: 'gallon', isPerArea: true, coverage: 8 },
            { name: 'Equipment', cost: 500, unit: 'set', isPerArea: false },
            { name: 'Safety Gear', cost: 200, unit: 'set', isPerArea: false }
        ],
        labor: 300,
        items: [
            'Surface preparation',
            'Equipment setup',
            'Special coating application',
            'Quality inspection',
            'Cleanup'
        ]
    },

    // Special Installations
    'special_installations': {
        name: 'Special Installations',
        category: 'Special Installations',
        estimatedDays: 4,
        materials: [
            { name: 'Installation Units', cost: 1000, unit: 'unit', isPerArea: false },
            { name: 'Support Structure', cost: 300, unit: 'set', isPerArea: false },
            { name: 'Hardware', cost: 200, unit: 'set', isPerArea: false }
        ],
        labor: 400,
        items: [
            'Site preparation',
            'Unit assembly',
            'Installation',
            'Safety checks',
            'Final testing'
        ]
    },
    'vibration_isolation': {
        name: 'Vibration Isolation',
        category: 'Special Installations',
        estimatedDays: 3,
        materials: [
            { name: 'Isolation Pads', cost: 500, unit: 'set', isPerArea: false },
            { name: 'Mounting Hardware', cost: 300, unit: 'set', isPerArea: false },
            { name: 'Sealant', cost: 150, unit: 'tube', isPerArea: false }
        ],
        labor: 350,
        items: [
            'System design',
            'Pad installation',
            'Equipment mounting',
            'Testing',
            'Adjustments'
        ]
    }
};

// Store the initial session data
const initialSessionData = @json(session('contract_step2', []));

document.addEventListener('DOMContentLoaded', function() {
    // Initialize form with any saved data
    initializeForm();

    // Add event listeners
    document.getElementById('addRoomBtn')?.addEventListener('click', createRoomRow);
    document.getElementById('applyToAllBtn')?.addEventListener('click', applyScopesToAll);
    
    // Form change handler for auto-saving
    document.getElementById('step2Form')?.addEventListener('change', function(e) {
        if (e.target.classList.contains('room-dimension')) {
            calculateRoomArea(e.target);
        } else if (e.target.classList.contains('scope-checkbox')) {
            updateRoomCalculations(e.target.closest('.room-row'));
        }
        
        updateGrandTotal();
        updateBreakdownTable();
        updateProjectTimeline();
        
        // Auto-save form data
        saveFormData();
    });

    // Form submit handler
    document.getElementById('step2Form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        if (!this.checkValidity()) {
            e.stopPropagation();
            this.classList.add('was-validated');
            return false;
        }
        
        // Submit the form
        this.submit();
    });

    // Initialize date fields
    document.getElementById('start_date')?.addEventListener('change', function() {
        updateProjectTimeline();
        saveFormData();
    });
});

function initializeForm() {
    // Clear existing rooms first
    document.getElementById('roomDetails').innerHTML = '';

    // Check if we have session data to restore
    if (initialSessionData.rooms && Object.keys(initialSessionData.rooms).length > 0) {
        // Restore each room from session data
        Object.entries(initialSessionData.rooms).forEach(([roomId, room]) => {
            createRoomRow();
            const roomRow = document.querySelector('.room-row:last-child');
            
            // Populate room details
            roomRow.querySelector('input[name$="[name]"]').value = room.name || '';
            roomRow.querySelector('input[name$="[length]"]').value = room.length || '';
            roomRow.querySelector('input[name$="[width]"]').value = room.width || '';
            roomRow.querySelector('input[name$="[area]"]').value = room.area || '';
            
            // Set scopes if they exist
            if (room.scope && Array.isArray(room.scope)) {
                room.scope.forEach(scope => {
                    const checkbox = roomRow.querySelector(`input[type="checkbox"][value="${scope}"]`);
                    if (checkbox) checkbox.checked = true;
                });
            }
            
            // Trigger calculations
            updateRoomCalculations(roomRow);
        });
    } else {
        // Create a default room if no session data exists
        createRoomRow();
    }

    // Restore timeline data
    if (initialSessionData.start_date) {
        document.getElementById('start_date').value = initialSessionData.start_date;
    }
    
    if (initialSessionData.end_date) {
        document.getElementById('end_date').value = initialSessionData.end_date;
    }

    // Update all calculations
    updateGrandTotal();
    updateBreakdownTable();
    updateProjectTimeline();
}

function saveFormData() {
    const formData = new FormData(document.getElementById('step2Form'));
    
    // Add room data
    const rooms = {};
    document.querySelectorAll('.room-row').forEach(roomRow => {
        const roomId = roomRow.dataset.roomId;
        const name = roomRow.querySelector('input[name$="[name]"]').value;
        const length = roomRow.querySelector('input[name$="[length]"]').value;
        const width = roomRow.querySelector('input[name$="[width]"]').value;
        const area = roomRow.querySelector('input[name$="[area]"]').value;
        const materialsCost = roomRow.querySelector('.materials-cost-hidden').value;
        const laborCost = roomRow.querySelector('.labor-cost-hidden').value;
        
        // Get selected scopes
        const scopes = Array.from(roomRow.querySelectorAll('.scope-checkbox:checked'))
            .map(checkbox => checkbox.value);
        
        rooms[roomId] = {
            name,
            length,
            width,
            area,
            materials_cost: materialsCost,
            labor_cost: laborCost,
            scope: scopes
        };
    });
    
    formData.append('rooms', JSON.stringify(rooms));
    
    // Add totals
    formData.append('total_materials', document.getElementById('total_materials').value);
    formData.append('total_labor', document.getElementById('total_labor').value);
    formData.append('grand_total', document.getElementById('grand_total').value);
    formData.append('total_area', document.getElementById('total_area').value);
    
    fetch('{{ route("contracts.save.step2") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).catch(error => {
        console.error('Error saving form data:', error);
    });
}

function createRoomRow() {
    const roomContainer = document.createElement('div');
    roomContainer.className = 'room-row mb-4';
    
    // Generate a unique ID for this room
    const roomId = Date.now();
    roomContainer.dataset.roomId = roomId;

    // Group scopes by category
    const scopesByCategory = {};
    Object.entries(scopeMaterials).forEach(([key, scope]) => {
        if (!scopesByCategory[scope.category]) {
            scopesByCategory[scope.category] = [];
        }
        scopesByCategory[scope.category].push({ key, ...scope });
    });

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

        <!-- Hidden cost fields -->
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
                                                            value="${scope.key}" 
                                                            id="scope_${scope.key}_${roomId}">
                                                        <label class="form-check-label" for="scope_${scope.key}_${roomId}">
                                                            <strong>${scope.name}</strong>
                                                            <span class="badge bg-info ms-2">${scope.estimatedDays} days</span>
                                                        </label>
                                                    </div>
                                                    <div class="ms-4 mt-2">
                                                        <small class="text-muted">Materials:</small>
                                                        <ul class="list-unstyled small">
                                                            ${scope.materials.map(material => `
                                                                <li>${material.name} - ₱${material.cost} ${material.unit}</li>
                                                            `).join('')}
                                                        </ul>
                                                        <small class="text-muted">Tasks:</small>
                                                        <ul class="list-unstyled small">
                                                            ${scope.items.map(item => `
                                                                <li><i class="fas fa-check-circle text-success"></i> ${item}</li>
                                                            `).join('')}
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
        const scope = scopeMaterials[scopeKey];
        if (scope) {
            // Add materials cost
            scope.materials.forEach(material => {
                let quantity = material.isPerArea ? (area / (material.coverage || 1)) : 1;
                quantity = Math.ceil(quantity * 100) / 100; // Round up to 2 decimals
                materialsCost += material.cost * quantity;
            });

            // Add labor cost based on area
            laborCost += scope.labor * area;
            
            // Add estimated days
            estimatedDays += scope.estimatedDays;
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

    // Calculate total project days based on selected scopes
    let totalDays = 0;
    document.querySelectorAll('.room-row').forEach(room => {
        let roomDays = 0;
        room.querySelectorAll('.scope-checkbox:checked').forEach(checkbox => {
            const scope = scopeMaterials[checkbox.value];
            if (scope) roomDays += scope.estimatedDays;
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
            const scope = scopeMaterials[checkbox.value];
            if (scope) {
                // Add materials
                scope.materials.forEach(material => {
                    const key = `${material.name}-${material.unit}`;
                    let quantity, totalCost;
                    
                    if (material.isPerArea && material.coverage) {
                        quantity = area / material.coverage;
                        quantity = Math.ceil(quantity * 100) / 100; // round up to 2 decimals
                        totalCost = material.cost * quantity;
                    } else if (material.isPerArea) {
                        quantity = area;
                        totalCost = material.cost * area;
                    } else {
                        quantity = 1;
                        totalCost = material.cost;
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
                            unitCost: material.cost,
                            quantity: quantity,
                            totalCost: totalCost,
                            rooms: [roomName],
                            isPerArea: material.isPerArea,
                            coverage: material.coverage || null
                        });
                    }
                });
                
                // Add labor cost
                totalLaborCost += scope.labor * area;
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