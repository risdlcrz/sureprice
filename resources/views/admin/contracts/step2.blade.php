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

                        <form method="POST" action="{{ route('contracts.store.step2') }}" id="step2Form">
                            @csrf

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
                                
                                <!-- Grand Total Summary -->
                                <div class="card mt-4">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">Grand Total Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <p class="mb-1">Total Floor Area:</p>
                                                <h5 id="grandTotalArea">0 sq m</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <p class="mb-1">Total Materials Cost:</p>
                                                <h5 id="grandTotalMaterials">₱0.00</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <p class="mb-1">Total Labor Cost:</p>
                                                <h5 id="grandTotalLabor">₱0.00</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <p class="mb-1">Grand Total:</p>
                                                <h5 id="grandTotal">₱0.00</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Timeline Section -->
                            <div class="section-container" id="timelineSection">
                                <h5 class="section-title">Project Timeline</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="start_date">Start Date</label>
                                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                                id="start_date" name="start_date" 
                                                value="{{ old('start_date', session('contract_step2.start_date')) }}" 
                                                required>
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="end_date">End Date</label>
                                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                                id="end_date" name="end_date" 
                                                value="{{ old('end_date', session('contract_step2.end_date')) }}" 
                                                required>
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <a href="{{ route('contracts.step1') }}" class="btn btn-secondary">Previous Step</a>
                                <button type="submit" class="btn btn-primary">Next Step</button>
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
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
// Include the scopeMaterials object from the original form
const scopeMaterials = {
    // ... (copy the entire scopeMaterials object from the original form)
};

document.addEventListener('DOMContentLoaded', function() {
    // Initialize core functionality
    createRoomRow(); // Create initial room
    
    // Add room button click handler
    document.getElementById('addRoomBtn')?.addEventListener('click', createRoomRow);

    // Apply to all rooms button handler
    document.getElementById('applyToAllBtn')?.addEventListener('click', applyScopesToAll);

    // Property type change listener
    document.getElementById('property_type')?.addEventListener('change', updateGrandTotal);

    // Form submission handler
    document.getElementById('step2Form')?.addEventListener('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        this.classList.add('was-validated');
    });

    // Delegate events for dynamic elements
    document.getElementById('roomDetails')?.addEventListener('change', function(e) {
        if (e.target.classList.contains('room-dimension')) {
            calculateRoomArea(e.target);
        }
        if (e.target.classList.contains('scope-checkbox')) {
            updateRoomCalculations(e.target);
            updateProjectTimeline();
        }
    });

    // Initialize date fields
    const startDateInput = document.getElementById('start_date');
    if (startDateInput) {
        startDateInput.addEventListener('change', function() {
            const endDateInput = document.getElementById('end_date');
            if (endDateInput && this.value) {
                // Set end date to 30 days after start date by default
                const startDate = new Date(this.value);
                const endDate = new Date(startDate);
                endDate.setDate(startDate.getDate() + 30);
                endDateInput.value = endDate.toISOString().split('T')[0];
            }
        });
    }
});

// Include all the helper functions from the original form
function createRoomRow() {
    // ... (copy the createRoomRow function from the original form)
}

function reindexRooms() {
    // ... (copy the reindexRooms function from the original form)
}

function applyScopesToAll() {
    // ... (copy the applyScopesToAll function from the original form)
}

function calculateRoomArea(input) {
    // ... (copy the calculateRoomArea function from the original form)
}

function updateRoomCalculations(element) {
    // ... (copy the updateRoomCalculations function from the original form)
}

function updateGrandTotal() {
    // ... (copy the updateGrandTotal function from the original form)
}

function updateProjectTimeline() {
    // ... (copy the updateProjectTimeline function from the original form)
}
</script>
@endpush 