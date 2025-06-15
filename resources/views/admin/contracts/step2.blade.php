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
const scopeTypes = {
    'painting_crew': {
        id: 'painting_crew',
        name: 'Painting Crew',
        category: 'Painting',
        is_wall_work: true,
        tasks: [
            {
                name: 'Surface Prep',
                labor_hours_per_sqm: 0.2, // avg of 0.15–0.25
                description: 'Includes cleaning, sanding, priming.'
            },
            {
                name: 'Paint Application',
                labor_hours_per_sqm: 0.15, // avg of 0.1–0.2
                description: '2 coats (cut-in + rolling).'
            }
        ],
        materials: [
            {
                name: 'Paint (latex/acrylic)',
                unit: 'liters',
                is_per_area: true,
                is_wall_material: true,
                coverage_rate: 10,
                waste_factor: 1.1,
                base_price: 500
            },
            {
                name: 'Primer',
                unit: 'liters',
                is_per_area: true,
                is_wall_material: true,
                coverage_rate: 12,
                waste_factor: 1.1,
                base_price: 450
            },
            {
                name: 'Sandpaper',
                unit: 'sheets',
                is_per_area: true,
                is_wall_material: true,
                coverage_rate: 10,
                waste_factor: 1.2,
                base_price: 25
            },
            {
                name: 'Caulk',
                unit: 'kg',
                is_per_area: true,
                is_wall_material: true,
                coverage_rate: 100,
                waste_factor: 1.1,
                base_price: 300
            },
            {
                name: "Painter's tape",
                unit: 'meters',
                is_per_area: true,
                is_wall_material: true,
                coverage_rate: 2,
                waste_factor: 1.1,
                base_price: 50
            }
        ],
        labor_rate: 350,
        labor_type: 'per_area',
        complexity_factor: 1.2,
        estimated_days: 2
    },
    'drywall_finishing': {
        id: 'drywall_finishing',
        name: 'Drywall Finishing',
        category: 'Painting',
        is_wall_work: true,
        tasks: [
            {
                name: 'Drywall Finishing',
                labor_hours_per_sqm: 0.35, // avg of 0.3–0.4
                description: 'Taping, mudding, sanding.'
            }
        ],
        materials: [
            {
                name: 'Joint compound',
                unit: 'kg',
                is_per_area: true,
                is_wall_material: true,
                coverage_rate: 5,
                waste_factor: 1.2,
                base_price: 200
            },
            {
                name: 'Drywall tape',
                unit: 'meters',
                is_per_area: true,
                is_wall_material: true,
                coverage_rate: 3,
                waste_factor: 1.1,
                base_price: 30
            },
            {
                name: 'Sandpaper',
                unit: 'sheets',
                is_per_area: true,
                is_wall_material: true,
                coverage_rate: 5,
                waste_factor: 1.2,
                base_price: 25
            }
        ],
        labor_rate: 400,
        labor_type: 'per_area',
        complexity_factor: 1.3,
        estimated_days: 3
    },
    'drywall_installation': {
        id: 'drywall_installation',
        name: 'Drywall Installation',
        category: 'Fit-outs',
        is_wall_work: true,
        tasks: [
            {
                name: 'Framing',
                labor_hours_per_sqm: 0.4,
                description: 'Install metal/wood studs'
            },
            {
                name: 'Hanging',
                labor_hours_per_sqm: 0.3,
                description: 'Secure gypsum boards to studs'
            },
            {
                name: 'Cutting',
                labor_hours_per_sqm: 0.2,
                description: 'Fit boards around outlets/doors'
            }
        ],
        materials: [
            {
                name: 'Gypsum board',
                unit: 'sqm',
                is_per_area: true,
                is_wall_material: true,
                waste_factor: 1.1,
                base_price: 350
            },
            {
                name: 'Screws',
                unit: 'pcs',
                is_per_area: true,
                is_wall_material: true,
                coverage_rate: 0.1,
                waste_factor: 1.2,
                base_price: 5
            },
            {
                name: 'Metal studs/channels',
                unit: 'meters',
                is_per_area: true,
                is_wall_material: true,
                coverage_rate: 0.5,
                waste_factor: 1.1,
                base_price: 150
            }
        ],
        labor_rate: 450,
        labor_type: 'per_area',
        complexity_factor: 1.4,
        estimated_days: 4
    },
    'tile_installation': {
        id: 'tile_installation',
        name: 'Tile Installation',
        category: 'Fit-outs',
        is_wall_work: true,
        tasks: [
            {
                name: 'Tile Installation',
                labor_hours_per_sqm: 0.5, // avg of 0.4–0.6
                description: 'Layout, mortar, grout.'
            }
        ],
        materials: [
            {
                name: 'Tiles',
                unit: 'sqm',
                is_per_area: true,
                is_wall_material: true,
                waste_factor: 1.1,
                base_price: 800
            },
            {
                name: 'Thin-set mortar',
                unit: 'kg',
                is_per_area: true,
                is_wall_material: true,
                coverage_rate: 4,
                waste_factor: 1.2,
                base_price: 250
            },
            {
                name: 'Grout',
                unit: 'kg',
                is_per_area: true,
                is_wall_material: true,
                coverage_rate: 2,
                waste_factor: 1.1,
                base_price: 300
            },
            {
                name: 'Spacers',
                unit: 'pcs',
                is_per_area: true,
                is_wall_material: true,
                coverage_rate: 12,
                waste_factor: 1.2,
                base_price: 2
            }
        ],
        labor_rate: 500,
        labor_type: 'per_area',
        complexity_factor: 1.5,
        estimated_days: 5
    },
    'cabinetry_installation': {
        id: 'cabinetry_installation',
        name: 'Cabinetry Installation',
        category: 'Fit-outs',
        is_wall_work: true,
        tasks: [
            {
                name: 'Measurement & Assembly',
                labor_hours_per_sqm: 0.5,
                description: 'Verify dimensions, construct cabinets'
            },
            {
                name: 'Installation',
                labor_hours_per_sqm: 0.4,
                description: 'Secure to walls/floor'
            },
            {
                name: 'Finishing',
                labor_hours_per_sqm: 0.2,
                description: 'Attach hardware (handles, hinges)'
            }
        ],
        materials: [
            {
                name: 'Plywood/MDF',
                unit: 'sqm',
                is_per_area: true,
                is_wall_material: true,
                waste_factor: 1.15,
                base_price: 1200
            },
            {
                name: 'Screws/nails',
                unit: 'pcs',
                is_per_area: true,
                is_wall_material: true,
                coverage_rate: 17,
                waste_factor: 1.1,
                base_price: 8
            },
            {
                name: 'Adhesive',
                unit: 'kg',
                is_per_area: true,
                is_wall_material: true,
                coverage_rate: 10,
                waste_factor: 1.2,
                base_price: 400
            }
        ],
        labor_rate: 550,
        labor_type: 'per_area',
        complexity_factor: 1.6,
        estimated_days: 6
    },
    'fireproofing': {
        id: 'fireproofing',
        name: 'Fireproofing Spray',
        category: 'MEPFS',
        is_wall_work: true,
        tasks: [
            {
                name: 'Fireproofing Spray',
                labor_hours_per_sqm: 0.075, // avg of 0.05–0.1
                description: 'Vertical surfaces only.'
            }
        ],
        materials: [
            {
                name: 'Spray-applied fireproofing',
                unit: 'kg',
                is_per_area: true,
                is_wall_material: true,
                coverage_rate: 1.75,
                waste_factor: 1.2,
                base_price: 600
            },
            {
                name: 'Wire mesh',
                unit: 'sqm',
                is_per_area: true,
                is_wall_material: true,
                waste_factor: 1.1,
                base_price: 200
            }
        ],
        labor_rate: 400,
        labor_type: 'per_area',
        complexity_factor: 1.4,
        estimated_days: 3
    },
    'electrical_wiring': {
        id: 'electrical_wiring',
        name: 'Electrical Wiring',
        category: 'MEPFS',
        is_wall_work: true,
        tasks: [
            {
                name: 'Electrical Wiring',
                labor_hours_per_sqm: 0.125, // avg of 0.1–0.15
                description: 'Rough-in for walls/floors.'
            }
        ],
        materials: [
            {
                name: 'Conduit',
                unit: 'meters',
                is_per_area: true,
                is_wall_material: true,
                coverage_rate: 0.5,
                waste_factor: 1.1,
                base_price: 150
            },
            {
                name: 'Wires',
                unit: 'meters',
                is_per_area: true,
                is_wall_material: true,
                coverage_rate: 1,
                waste_factor: 1.2,
                base_price: 80
            },
            {
                name: 'Junction boxes',
                unit: 'pcs',
                is_per_area: true,
                is_wall_material: true,
                coverage_rate: 0.1,
                waste_factor: 1.1,
                base_price: 200
            }
        ],
        labor_rate: 450,
        labor_type: 'per_area',
        complexity_factor: 1.5,
        estimated_days: 4
    },
    'plumbing_rough_in': {
        id: 'plumbing_rough_in',
        name: 'Plumbing Pipes',
        category: 'MEPFS',
        is_wall_work: true,
        tasks: [
            {
                name: 'Plumbing Pipes',
                labor_hours_per_sqm: 0.175, // avg of 0.15–0.2
                description: 'PVC/CPVC installation.'
            }
        ],
        materials: [
            {
                name: 'PVC pipes',
                unit: 'meters',
                is_per_area: true,
                is_wall_material: true,
                coverage_rate: 0.3,
                waste_factor: 1.1,
                base_price: 200
            },
            {
                name: 'Fittings',
                unit: 'pcs',
                is_per_area: true,
                is_wall_material: true,
                coverage_rate: 2.5,
                waste_factor: 1.2,
                base_price: 150
            }
        ],
        labor_rate: 400,
        labor_type: 'per_area',
        complexity_factor: 1.3,
        estimated_days: 3
    },
    'flooring_installation': {
        id: 'flooring_installation',
        name: 'Vinyl Flooring',
        category: 'Infrastructure',
        is_wall_work: false,
        tasks: [
            {
                name: 'Vinyl Flooring',
                labor_hours_per_sqm: 0.25, // avg of 0.2–0.3
                description: 'Includes underlayment.'
            }
        ],
        materials: [
            {
                name: 'Vinyl planks',
                unit: 'sqm',
                is_per_area: true,
                is_wall_material: false,
                waste_factor: 1.1,
                base_price: 1200
            },
            {
                name: 'Underlayment',
                unit: 'sqm',
                is_per_area: true,
                is_wall_material: false,
                waste_factor: 1.05,
                base_price: 150
            },
            {
                name: 'Adhesive',
                unit: 'kg',
                is_per_area: true,
                is_wall_material: false,
                coverage_rate: 5,
                waste_factor: 1.2,
                base_price: 400
            }
        ],
        labor_rate: 500,
        labor_type: 'per_area',
        complexity_factor: 1.4,
        estimated_days: 4
    },
    'concrete_coating': {
        id: 'concrete_coating',
        name: 'Concrete Waterproofing',
        category: 'Infrastructure',
        is_wall_work: false,
        tasks: [
            {
                name: 'Concrete Waterproofing',
                labor_hours_per_sqm: 0.125, // avg of 0.1–0.15
                description: 'Epoxy/polyurethane application.'
            }
        ],
        materials: [
            {
                name: 'Epoxy coating',
                unit: 'kg',
                is_per_area: true,
                is_wall_material: false,
                coverage_rate: 0.35,
                waste_factor: 1.2,
                base_price: 800
            },
            {
                name: 'Sealant',
                unit: 'kg',
                is_per_area: true,
                is_wall_material: false,
                coverage_rate: 0.1,
                waste_factor: 1.1,
                base_price: 600
            }
        ],
        labor_rate: 450,
        labor_type: 'per_area',
        complexity_factor: 1.3,
        estimated_days: 3
    }
};

const DEFAULT_CREW_SIZE = 8; // Default number of workers per crew (was 2)
const DEFAULT_HOURS_PER_DAY = 8; // Default working hours per day

// Helper for key adjustments
function getAdjustmentFactor(room, scope) {
    let factor = 1;
    // Large room adjustment (arbitrary threshold: >50 sqm floor area)
    const floorArea = parseFloat(room.querySelector('input[name$="[floor_area]"]').value) || 0;
    if (floorArea > 50) factor *= 1.15; // 15% more time
    // High ceiling adjustment (>3m)
    const height = parseFloat(room.querySelector('input[name$="[height]"]').value) || 0;
    if (scope.is_wall_work && height > 3) factor *= 1.7; // 1.5-2x, use 1.7x as average
    // Complex design (add UI/checkbox for this if needed, for now assume not complex)
    // if (room.querySelector('input[name$="[complex]"]').checked) factor *= 1.3;
    return factor;
}

function calculateAllCosts() {
    let totalFloorArea = 0;
    let totalWallArea = 0;
    let totalLabor = 0;
    let totalEstimatedDays = 0;
    let materialsMap = new Map();

    document.querySelectorAll('.room-row').forEach(room => {
        const floorArea = parseFloat(room.querySelector('input[name$="[floor_area]"]').value) || 0;
        const wallArea = parseFloat(room.querySelector('input[name$="[wall_area]"]').value) || 0;
        totalFloorArea += floorArea;
        totalWallArea += wallArea;
        let roomLabor = 0;
        let roomMaterialsCost = 0;
        let roomEstimatedDays = 0;

        const selectedScopes = Array.from(room.querySelectorAll('.scope-checkbox:checked')).map(cb => cb.value);

        selectedScopes.forEach(scopeKey => {
            const scope = scopeTypes[scopeKey];
            if (!scope) return;
            // --- Materials ---
            const materialsArr = getScopeMaterials(scope);
            materialsArr.forEach(material => {
                if (!material || typeof material !== 'object') return;
                const price = parseFloat(material.srp_price ?? 0) > 0 ? parseFloat(material.srp_price) : parseFloat(material.base_price ?? 0);
                let quantity = 0;
                const area = material.is_wall_material ? wallArea : floorArea;
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
                    if (!existing.rooms.includes(room.querySelector('input[name$="[name]"]').value ?? `Room ${room.dataset.roomId}`)) {
                        existing.rooms.push(room.querySelector('input[name$="[name]"]').value ?? `Room ${room.dataset.roomId}`);
                    }
                } else {
                    materialsMap.set(key, {
                        category: scope.category ?? 'Uncategorized',
                        name: material.name ?? 'Unnamed Material',
                        unit: material.unit ?? 'pcs',
                        unitCost: finalPrice,
                        quantity: quantity,
                        totalCost: totalCost,
                        rooms: [room.querySelector('input[name$="[name]"]').value ?? `Room ${room.dataset.roomId}`],
                        isPerArea: material.is_per_area ?? false,
                        coverage: material.coverage_rate ?? 1
                    });
                }
            });
            // --- Labor ---
            const laborRate = parseFloat(scope.labor_rate ?? 0) || 0;
            const complexityFactor = parseFloat(scope.complexity_factor ?? 1) || 1;
            const minimumLaborCost = parseFloat(scope.minimum_labor_cost ?? 0) || 0;
            let scopeLaborCost = 0;
            let scopeLaborHours = 0;
            // Calculate labor based on tasks
            if (scope.tasks) {
                const laborArea = scope.is_wall_work ? wallArea : floorArea;
                const adjustment = getAdjustmentFactor(room, scope);
                scopeLaborHours = scope.tasks.reduce((total, task) => {
                    return total + (task.labor_hours_per_sqm * laborArea * adjustment);
                }, 0);
                scopeLaborCost = scopeLaborHours * laborRate;
            } else {
                // Fallback to old calculation method if no tasks defined
                const laborArea = scope.is_wall_work ? wallArea : floorArea;
                scopeLaborCost = laborRate * laborArea;
            }
            scopeLaborCost = Math.max(minimumLaborCost, scopeLaborCost * complexityFactor);
            roomLabor += scopeLaborCost;
            // --- Estimated Days ---
            if (scopeLaborHours > 0) {
                const crewSize = DEFAULT_CREW_SIZE;
                const hoursPerDay = DEFAULT_HOURS_PER_DAY;
                const days = scopeLaborHours / (crewSize * hoursPerDay);
                roomEstimatedDays = Math.max(roomEstimatedDays, days);
            }
        });
        // Update room-specific display and hidden inputs
        room.querySelector('.materials-cost').textContent = `₱${roomMaterialsCost.toFixed(2)}`;
        room.querySelector('.labor-cost').textContent = `₱${roomLabor.toFixed(2)}`;
        room.querySelector('.total-cost').textContent = `₱${(roomMaterialsCost + roomLabor).toFixed(2)}`;
        room.querySelector('.materials-cost-hidden').value = roomMaterialsCost.toFixed(2);
        room.querySelector('.labor-cost-hidden').value = roomLabor.toFixed(2);
        // Show estimated days
        const estimatedTimeElem = room.querySelector('.estimated-time');
        if (estimatedTimeElem) {
            estimatedTimeElem.textContent = `${roomEstimatedDays > 0 ? roomEstimatedDays.toFixed(1) : 0} days`;
        }
        totalLabor += roomLabor;
        totalEstimatedDays = Math.max(totalEstimatedDays, roomEstimatedDays);
    });
    let totalMaterials = 0;
    materialsMap.forEach(material => {
        totalMaterials += material.totalCost;
    });
    return {
        totalFloorArea,
        totalWallArea,
        totalMaterials,
        totalLabor,
        grandTotal: totalMaterials + totalLabor,
        materialsMap,
        totalEstimatedDays
    };
}

function updateProjectTimeline() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    if (!startDateInput || !startDateInput.value) return;
    const { totalEstimatedDays } = calculateAllCosts();
    const startDate = new Date(startDateInput.value);
    const endDate = new Date(startDate);
    endDate.setDate(startDate.getDate() + Math.ceil(totalEstimatedDays));
    if (endDateInput) {
        endDateInput.value = endDate.toISOString().split('T')[0];
    }
}

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
            updateGrandTotalAndBreakdown();
            updateProjectTimeline();
            saveFormData();
            updateScopeDaysBadges(e.target.closest('.room-row'));
        } else if (e.target.classList.contains('room-dimension')) {
            calculateRoomArea(e.target);
            updateScopeDaysBadges(e.target.closest('.room-row'));
        }
    });

    // Add form submission handler
    const form = document.getElementById('step2Form');
    form.addEventListener('submit', function(e) {
        // Let the form submit normally, do not call saveFormData here
    });

    // Optionally, initialize the form if needed
    if (typeof initializeForm === 'function') {
        initializeForm();
    }
    // Initial timeline update
    updateProjectTimeline();
    // Initial cost update after form is potentially initialized
    updateGrandTotalAndBreakdown();

    // Add window unload handler to save data when navigating away
    window.addEventListener('beforeunload', function() {
        saveFormData();
    });
});

// Ensure materials are parsed for each scope type
Object.values(scopeTypes).forEach(scope => {
    if (typeof scope.materials === 'string') {
        try {
            scope.materials = JSON.parse(scope.materials);
        } catch (e) {
            console.error('Error parsing materials for scope:', scope.id, e);
            scope.materials = [];
        }
    }
});

const scopesByCategory = {};
Object.values(scopeTypes).forEach(scope => {
    if (!scopesByCategory[scope.category]) scopesByCategory[scope.category] = [];
    scopesByCategory[scope.category].push(scope);
});

// Get session data
const sessionData = @json($sessionData ?? []);

// Helper to generate a stable room id
function getRoomId(room, idx) {
    if (room.id) return room.id;
    if (room._id) return room._id;
    return `room_${idx}_${Date.now()}`;
}

// Restore saveFormData function with stable room id
function saveFormData() {
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

    // Log the data being saved
    console.log('Saving form data:', data);

    document.querySelectorAll('.room-row').forEach((room, idx) => {
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
        data.rooms.push(roomData);
    });

    // Log the complete data being sent
    console.log('Sending data to server:', data);

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

// Always call initializeForm on page load
initializeForm();

function initializeForm() {
    console.log('Restoring sessionData:', sessionData);
    // Clear existing rooms first
    document.getElementById('roomDetails').innerHTML = '';

    // If we have session data, use it to initialize the form
    if (sessionData.rooms && (Array.isArray(sessionData.rooms) || typeof sessionData.rooms === 'object')) {
        // Convert to array if it's an object
        const roomsArray = Array.isArray(sessionData.rooms) ? 
            sessionData.rooms : 
            Object.entries(sessionData.rooms).map(([id, room]) => ({...room, id}));

        roomsArray.forEach((room, idx) => {
            const roomId = getRoomId(room, idx);
            room.id = roomId; // Save back for future saves
            const roomContainer = document.createElement('div');
            roomContainer.className = 'room-row mb-4';
            roomContainer.dataset.roomId = roomId;
            roomContainer.innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Room/Area Name</label>
                            <input type="text" class="form-control" name="rooms[${roomId}][name]" required value="${room.name || ''}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Length (m)</label>
                            <input type="number" class="form-control room-dimension" name="rooms[${roomId}][length]" step="0.01" min="0.01" required value="${room.length || ''}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Width (m)</label>
                            <input type="number" class="form-control room-dimension" name="rooms[${roomId}][width]" step="0.01" min="0.01" required value="${room.width || ''}">
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Height (m)</label>
                            <input type="number" class="form-control room-dimension" name="rooms[${roomId}][height]" step="0.01" min="0.01" required value="${room.height || ''}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Floor Area (sq m)</label>
                            <input type="number" class="form-control" name="rooms[${roomId}][floor_area]" readonly value="${room.floor_area || ''}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Wall Area (sq m)</label>
                            <input type="number" class="form-control" name="rooms[${roomId}][wall_area]" readonly value="${room.wall_area || ''}">
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
                                                                    <span class="badge bg-info ms-2 days-badge" data-scope-id="${scope.id}"></span>
                                                                </label>
                                                            </div>
                                                            <div class="ms-4 mt-2">
                                                                <small class="text-muted">Materials:</small>
                                                                <ul class="list-unstyled small">
                                                                    ${(scope.materials && getScopeMaterials(scope).length > 0) ? getScopeMaterials(scope).map(material => {
                                                                        const name = material.name || 'Unnamed Material';
                                                                        // Determine the price to display, prioritizing srp_price if greater than 0, else base_price
                                                                        const displayPrice = parseFloat(material.srp_price ?? 0) > 0 ? parseFloat(material.srp_price) : parseFloat(material.base_price ?? 0);
                                                                        const unit = material.unit || 'pcs';
                                                                        return `<li>${name} - ₱${displayPrice.toFixed(2)}</li>`;
                                                                    }).filter(Boolean).join('') : '<li><em>No materials assigned</em></li>'}
                                                                </ul>
                                                                <small class="text-muted">Tasks:</small>
                                                                <ul class="list-unstyled small">
                                                                    ${(scope.tasks && scope.tasks.length > 0) ? scope.tasks.map(task => `
                                                                        <li><i class=\"fas fa-check-circle text-success\"></i> ${task.name}</li>
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
                            <h6 class="materials-cost">₱${parseFloat(room.materials_cost || 0).toFixed(2)}</h6>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1">Labor Cost:</p>
                            <h6 class="labor-cost">₱${parseFloat(room.labor_cost || 0).toFixed(2)}</h6>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1">Total Cost:</p>
                            <h6 class="total-cost">₱${(parseFloat(room.materials_cost || 0) + parseFloat(room.labor_cost || 0)).toFixed(2)}</h6>
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
            updateScopeDaysBadges(roomContainer);
        });
    }

    // Initialize timeline dates from session if available
    if (sessionData.start_date) {
        document.getElementById('start_date').value = sessionData.start_date;
    }
    if (sessionData.end_date) {
        document.getElementById('end_date').value = sessionData.end_date;
    }

    // Initialize hidden fields from session if available
    if (sessionData.total_materials) {
        document.getElementById('total_materials').value = sessionData.total_materials;
    }
    if (sessionData.total_labor) {
        document.getElementById('total_labor').value = sessionData.total_labor;
    }
    if (sessionData.grand_total) {
        document.getElementById('grand_total').value = sessionData.grand_total;
    }

    // Update all calculations
    updateGrandTotalAndBreakdown();
    updateProjectTimeline();
}

function createRoomRow() {
    const roomContainer = document.createElement('div');
    roomContainer.className = 'room-row mb-4';
    const roomId = Date.now();
    roomContainer.dataset.roomId = roomId;
    roomContainer.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Room/Area Name</label>
                    <input type="text" class="form-control" name="rooms[${roomId}][name]" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Length (m)</label>
                    <input type="number" class="form-control room-dimension" name="rooms[${roomId}][length]" step="0.01" min="0.01" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Width (m)</label>
                    <input type="number" class="form-control room-dimension" name="rooms[${roomId}][width]" step="0.01" min="0.01" required>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Height (m)</label>
                    <input type="number" class="form-control room-dimension" name="rooms[${roomId}][height]" step="0.01" min="0.01" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Floor Area (sq m)</label>
                    <input type="number" class="form-control" name="rooms[${roomId}][floor_area]" readonly>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Wall Area (sq m)</label>
                    <input type="number" class="form-control" name="rooms[${roomId}][wall_area]" readonly>
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
                                                            <span class="badge bg-info ms-2 days-badge" data-scope-id="${scope.id}"></span>
                                                        </label>
                                                    </div>
                                                    <div class="ms-4 mt-2">
                                                        <small class="text-muted">Materials:</small>
                                                        <ul class="list-unstyled small">
                                                            ${(scope.materials && getScopeMaterials(scope).length > 0) ? getScopeMaterials(scope).map(material => {
                                                                const name = material.name || 'Unnamed Material';
                                                                // Determine the price to display, prioritizing srp_price if greater than 0, else base_price
                                                                const displayPrice = parseFloat(material.srp_price ?? 0) > 0 ? parseFloat(material.srp_price) : parseFloat(material.base_price ?? 0);
                                                                const unit = material.unit || 'pcs';
                                                                return `<li>${name} - ₱${displayPrice.toFixed(2)}</li>`;
                                                            }).filter(Boolean).join('') : '<li><em>No materials assigned</em></li>'}
                                                        </ul>
                                                        <small class="text-muted">Tasks:</small>
                                                        <ul class="list-unstyled small">
                                                            ${(scope.tasks && scope.tasks.length > 0) ? scope.tasks.map(task => `
                                                                <li><i class=\"fas fa-check-circle text-success\"></i> ${task.name}</li>
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
    updateScopeDaysBadges(roomContainer);
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

    if (lengthInput.value && widthInput.value && heightInput.value) {
        const length = parseFloat(lengthInput.value);
        const width = parseFloat(widthInput.value);
        const height = parseFloat(heightInput.value);
        
        // Calculate floor area
        const floorArea = length * width;
        floorAreaInput.value = floorArea.toFixed(2);
        
        // Calculate wall area (perimeter * height)
        const wallArea = 2 * (length + width) * height;
        wallAreaInput.value = wallArea.toFixed(2);
        
        updateGrandTotalAndBreakdown();
        saveFormData();
        updateScopeDaysBadges(roomRow);
    }
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
    });
    updateGrandTotalAndBreakdown(); // Call once after applying to all rooms
    saveFormData();
}

function updateGrandTotalAndBreakdown() {
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

    document.getElementById('total_area').value = (totalFloorArea + totalWallArea).toFixed(2);
    document.getElementById('total_materials').value = totalMaterials.toFixed(2);
    document.getElementById('total_labor').value = totalLabor.toFixed(2);
    document.getElementById('grand_total').value = (totalMaterials + totalLabor).toFixed(2);

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
                    onclick="showMaterialDetails('${material.name}', ${JSON.stringify(material.rooms).replace(/'/g, "\\'")})">
                    <i class="fas fa-info-circle"></i> Details
                </button>
            </td>
        `;
        tableBody.appendChild(row);
    });
    document.getElementById('breakdownTotalMaterials').textContent = `₱${totalMaterials.toFixed(2)}`; // Use totalMaterials
    document.getElementById('breakdownTotalLabor').textContent = `₱${totalLabor.toFixed(2)}`;
    document.getElementById('breakdownGrandTotal').textContent = `₱${(totalMaterials + totalLabor).toFixed(2)}`;

    updateProjectTimeline(); // Ensure timeline updates after all costs
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
            console.log('Parsing materials JSON string:', scope.materials);
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
            console.log('Material object in getScopeMaterials:', material);

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
                bulk_pricing: bulkPricing
            };
        }).filter(m => m !== null);
    } catch (e) {
        console.error('Error processing materials:', e);
        return [];
    }
}

// Add this helper to calculate estimated days for a scope in a room
function getScopeEstimatedDays(scope, room) {
    const floorArea = parseFloat(room.querySelector('input[name$="[floor_area]"]').value) || 0;
    const wallArea = parseFloat(room.querySelector('input[name$="[wall_area]"]').value) || 0;
    const adjustment = getAdjustmentFactor(room, scope);
    const crewSize = DEFAULT_CREW_SIZE;
    const hoursPerDay = DEFAULT_HOURS_PER_DAY;
    let totalLaborHours = 0;
    if (scope.tasks) {
        const laborArea = scope.is_wall_work ? wallArea : floorArea;
        totalLaborHours = scope.tasks.reduce((total, task) => {
            return total + (task.labor_hours_per_sqm * laborArea * adjustment);
        }, 0);
    }
    if (totalLaborHours > 0 && crewSize > 0 && hoursPerDay > 0) {
        return Math.max(1, Math.ceil(totalLaborHours / (crewSize * hoursPerDay)));
    }
    return 1;
}

// Update the code that renders the scope/service card in each room
// Find the section where the scope checkboxes and badges are rendered, and update it like this:

// ... inside the function that renders each scope/service in a room ...
// Example (pseudo-code, adapt to your rendering logic):
//
// <div class="scope-card">
//   <input type="checkbox" ...>
//   <label>Vinyl Flooring</label>
//   <span class="badge badge-info days-badge">4 days</span>
//   ...
// </div>
//
// Change to:
//
//   <span class="badge badge-info days-badge" data-scope-id="${scope.id}"></span>
//
// Then, after rendering, call updateScopeDaysBadges(room) to update all badges for that room.

function updateScopeDaysBadges(room) {
    const scopeCheckboxes = room.querySelectorAll('.scope-checkbox');
    scopeCheckboxes.forEach(cb => {
        const scope = scopeTypes[cb.value];
        let badge = null;
        // Try to find badge in the label next to the checkbox
        if (cb.nextElementSibling && cb.nextElementSibling.querySelector && cb.nextElementSibling.querySelector('.days-badge')) {
            badge = cb.nextElementSibling.querySelector('.days-badge');
        } else if (cb.parentElement && cb.parentElement.querySelector('.days-badge')) {
            badge = cb.parentElement.querySelector('.days-badge');
        } else {
            badge = room.querySelector(`.days-badge[data-scope-id="${cb.value}"]`);
        }
        if (scope && badge) {
            const days = getScopeEstimatedDays(scope, room);
            badge.textContent = `${days} day${days > 1 ? 's' : ''}`;
            badge.style.display = '';
        }
    });
}
</script>
@endpush