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

    /* Custom Accordion */
    .custom-accordion {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        overflow: hidden;
        margin-bottom: 1rem;
    }

    .custom-accordion-item {
        border-bottom: 1px solid #dee2e6;
    }

    .custom-accordion-item:last-child {
        border-bottom: none;
    }

    .custom-accordion-header {
        background-color: #f8f9fa;
        border: none;
        padding: 1rem 1.25rem;
        font-weight: 500;
        color: #344767;
        cursor: pointer;
        width: 100%;
        text-align: left;
        transition: all 0.2s ease;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .custom-accordion-header:hover {
        background-color: #e7f3ff;
        color: #0d6efd;
    }

    .custom-accordion-header.active {
        background-color: #e7f3ff;
        color: #0d6efd;
    }

    .custom-accordion-header::after {
        content: '▼';
        font-size: 0.8em;
        transition: transform 0.2s ease;
    }

    .custom-accordion-header.active::after {
        transform: rotate(180deg);
    }

    .custom-accordion-body {
        background-color: #fff;
        padding: 1.25rem;
        display: none;
        border-top: 1px solid #dee2e6;
    }

    .custom-accordion-body.show {
        display: block;
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
                        <h5 class="mb-0">
                            Request a Quotation - 
                            @if(is_array($category))
                                {{ implode(', ', $category) }}
                            @else
                                {{ $category ?? '' }}
                            @endif
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('client.quotation.store') }}" id="quotationForm" novalidate>
                            @csrf
                            <!-- Room/Area Details (step2 logic) -->
                            <div class="section-container" id="roomSection">
                                <h5 class="section-title">Room/Area Details</h5>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-primary" id="addRoomBtn">
                                            <i class="fas fa-plus"></i> Add Room/Area
                                        </button>
                                    </div>
                                </div>
                                <div id="roomDetails">
                                    <!-- Rooms will be added here dynamically (step2 logic) -->
                                </div>
                            </div>

                            <!-- Cost Breakdown & Supplier Selection (step2 + supplier features) -->
                            <div class="section-container" id="breakdownSection">
                                <h5 class="section-title">Cost Breakdown & Supplier Selection</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="breakdownTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Room</th>
                                                <th>Scope</th>
                                                <th>Category</th>
                                                <th>Material</th>
                                                <th>Unit</th>
                                                <th>Unit Cost</th>
                                                <th>Quantity</th>
                                                <th>Base Total Cost</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Dynamically filled by JS (step2 logic) -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Submit Quotation Request</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Supplier Recommendation Modal (as before) -->
<div class="modal fade" id="supplierRecommendationModal" tabindex="-1" aria-labelledby="supplierRecommendationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supplierRecommendationModalLabel">Supplier Recommendation for <span id="modalMaterialName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="supplier-recommendation-form" class="row g-3 mb-3">
                    <input type="hidden" name="material_id" id="modalMaterialId">
                    <div class="col-12">
                        <label for="rec_mode" class="form-label">Recommendation Mode</label>
                        <select class="form-select" id="rec_mode" name="mode">
                            <option value="best_score">Best Overall Score</option>
                            <option value="on_time_delivery">Best On-Time Delivery</option>
                            <option value="lowest_defect">Lowest Defect Rate</option>
                            <option value="cheapest">Cheapest</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100">Show Recommendations</button>
                    </div>
                </form>
                <div id="supplier-recommendation-results"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const scopeTypesByCode = @json($scopeTypesByCode);
const materialSuppliers = @json($materialSuppliers);
const badgeColors = {
    'Overall Best': 'success',
    'Cheapest': 'primary',
    'Best Delivery': 'info',
    'Least Defects': 'warning',
};

// Build scopesByCategory for the current category
const scopesByCategory = {};
Object.values(scopeTypesByCode).forEach(scope => {
    if (!scopesByCategory[scope.category]) scopesByCategory[scope.category] = [];
    scopesByCategory[scope.category].push(scope);
});

// Helper: Calculate and update floor and wall area for a room row
function updateRoomAreas(roomRow) {
    const length = parseFloat(roomRow.querySelector('input[name$="[length]"]').value) || 0;
    const width = parseFloat(roomRow.querySelector('input[name$="[width]"]').value) || 0;
    const height = parseFloat(roomRow.querySelector('input[name$="[height]"]').value) || 0;
    const floorArea = length * width;
    const wallArea = 2 * (length + width) * height;
    roomRow.querySelector('input[name$="[floor_area]"]').value = floorArea.toFixed(2);
    roomRow.querySelector('input[name$="[wall_area]"]').value = wallArea.toFixed(2);
}

function createRoomRow(initialRoomData = {}) {
    const roomContainer = document.createElement('div');
    roomContainer.className = 'room-row mb-4';
    const roomId = initialRoomData.id || Date.now();
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
                    <button type="button" class="btn btn-danger w-100" onclick="this.closest('.room-row').remove(); updateBreakdownTable();">
                        <i class="fas fa-trash"></i> Remove Room
                    </button>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="mb-3">Scope of Work</h6>
                <div class="custom-accordion" id="scopeAccordion${roomId}">
                    ${Object.entries(scopesByCategory).map(([category, scopes], categoryIndex) => `
                        <div class="custom-accordion-item">
                            <div class="custom-accordion-header ${categoryIndex === 0 ? 'active' : ''}" 
                                 data-target="#custom-collapse-${roomId}-${categoryIndex}" 
                                 aria-expanded="${categoryIndex === 0}">
                                ${category}
                            </div>
                            <div id="custom-collapse-${roomId}-${categoryIndex}" class="custom-accordion-body ${categoryIndex === 0 ? 'show' : ''}">
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
                                                        ${(scope.materials && scope.materials.length > 0) ? `
                                                            <ul class="mb-0 ms-3">
                                                                ${scope.materials.map(material => `
                                                                    <li>${material.name} <span class='text-muted'>(₱${parseFloat(material.base_price).toFixed(2)})</span></li>
                                                                `).join('')}
                                                            </ul>
                                                        ` : ''}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        </div>
    `;
    document.getElementById('roomDetails').appendChild(roomContainer);
    // Add event listeners for dimension and scope checkboxes
    roomContainer.querySelectorAll('.room-dimension').forEach(input => {
        input.addEventListener('input', function() {
            updateRoomAreas(roomContainer);
            updateBreakdownTable();
        });
    });
    roomContainer.querySelectorAll('.scope-checkbox').forEach(cb => {
        cb.addEventListener('change', updateBreakdownTable);
    });
    // Initial area calculation
    updateRoomAreas(roomContainer);
    updateBreakdownTable();
}

function updateBreakdownTable() {
    const tbody = document.querySelector('#breakdownTable tbody');
    tbody.innerHTML = '';
    let rowIdx = 0;
    document.querySelectorAll('.room-row').forEach(room => {
        const roomName = room.querySelector('input[name$="[name]"]').value || `Room ${room.dataset.roomId}`;
        const roomId = room.dataset.roomId;
        // Get dimensions
        const length = parseFloat(room.querySelector('.room-dimension[name$="[length]"]')?.value) || 1;
        const width = parseFloat(room.querySelector('.room-dimension[name$="[width]"]')?.value) || 1;
        const height = parseFloat(room.querySelector('.room-dimension[name$="[height]"]')?.value) || 1;
        const floorArea = length * width;
        const wallArea = 2 * (length + width) * height; // Perimeter * height
        const checkedScopes = Array.from(room.querySelectorAll('.scope-checkbox:checked')).map(cb => cb.value);
        checkedScopes.forEach(scopeId => {
            const scope = scopeTypesByCode[scopeId];
            if (!scope) return;
            (scope.materials || []).forEach(material => {
                const tr = document.createElement('tr');
                tr.setAttribute('data-material-id', material.id);
                tr.setAttribute('data-room-id', roomId);
                tr.setAttribute('data-scope-id', scopeId);
                // --- Quantity logic (step2 style) ---
                let quantity = 1;
                let area = material.is_wall_material ? wallArea : floorArea;
                if (material.is_per_area || material.isPerArea) {
                    const coverage = parseFloat(material.coverage_rate ?? 1) || 1;
                    quantity = area > 0 && coverage > 0 ? Math.ceil(area / coverage) : 0;
                } else {
                    quantity = area > 0 ? Math.ceil(area) : 1;
                }
                if (quantity > 0) {
                    const wasteFactor = parseFloat(material.waste_factor ?? 1.1) || 1.1;
                    quantity = Math.ceil(quantity * wasteFactor);
                }
                quantity = Math.max(1, parseFloat(quantity));
                // --- Cost logic ---
                let coverageInfo = '';
                if (material.coverage_rate && material.unit) {
                    coverageInfo = ` (1 ${material.unit} covers ${material.coverage_rate} sqm)`;
                } else if (material.unit) {
                    coverageInfo = ` (1 ${material.unit} covers 1 sqm)`;
                }
                const baseTotal = material.base_price * quantity;
                tr.innerHTML = `
                    <td>${roomName}</td>
                    <td>${scope.name}</td>
                    <td>${scope.category}</td>
                    <td>${material.name}</td>
                    <td>${material.unit}</td>
                    <td class="unit-cost-cell">
                        <span class="supplier-price">₱${parseFloat(material.base_price).toFixed(2)}</span>
                    </td>
                    <td class="quantity-cell">${quantity} ${material.unit}${coverageInfo}</td>
                    <td class="base-total-cost-cell">₱${baseTotal.toFixed(2)}</td>
                `;
                tbody.appendChild(tr);
                rowIdx++;
            });
        });
    });
}

// --- Event Listeners ---
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addRoomBtn').addEventListener('click', function() {
        createRoomRow();
    });
    updateBreakdownTable();
    // Only add recommendAllBtn event listener if the button exists
    const recommendAllBtn = document.getElementById('recommendAllBtn');
    if (recommendAllBtn) {
        recommendAllBtn.addEventListener('click', function() {
            document.querySelectorAll('#breakdownTable tr[data-material-id]').forEach(row => {
                const dropdown = row.querySelector('.dropdown-menu');
                const best = dropdown ? dropdown.querySelector('.supplier-option[data-badges*="Overall Best"]') : null;
                if (best) {
                    const btn = row.querySelector('.supplier-dropdown-btn');
                    btn.innerHTML = `<span>${best.querySelector('span').textContent}</span>`;
                    row.querySelector('.supplier-price').textContent = `₱${parseFloat(best.getAttribute('data-price')).toFixed(2)}`;
                    row.querySelector('.supplier-total-cost-cell').textContent = `₱${parseFloat(best.getAttribute('data-price')).toFixed(2)}`;
                    // Show badges
                    const badgeList = row.querySelector('.badge-list');
                    badgeList.innerHTML = '';
                    (best.getAttribute('data-badges') || '').split(',').filter(Boolean).forEach(badge => {
                        const span = document.createElement('span');
                        span.className = `badge bg-${badgeColors[badge] || 'secondary'} me-1`;
                        span.textContent = badge;
                        badgeList.appendChild(span);
                    });
                    // Store selected supplier ID in hidden input
                    const hiddenInput = row.querySelector('.selected-supplier-input');
                    if (hiddenInput) hiddenInput.value = best.getAttribute('data-supplier-id');
                }
            });
        });
    }

    // Restore accordion click handler
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('custom-accordion-header')) {
            e.preventDefault();
            e.stopPropagation();
            const header = e.target;
            const accordion = header.closest('.custom-accordion');
            const targetId = header.getAttribute('data-target');
            const body = accordion.querySelector(targetId);
            if (!body || !accordion) return;
            const isActive = header.classList.contains('active');
            // Close all
            accordion.querySelectorAll('.custom-accordion-header').forEach(h => {
                h.classList.remove('active');
                h.setAttribute('aria-expanded', 'false');
                const otherBody = accordion.querySelector(h.getAttribute('data-target'));
                if (otherBody) otherBody.classList.remove('show');
            });
            // Toggle clicked
            if (!isActive) {
                header.classList.add('active');
                header.setAttribute('aria-expanded', 'true');
                body.classList.add('show');
            }
        }
    });
});
</script>
@endpush 