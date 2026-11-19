// Extracted from resources/views/client/quotation/create.blade.php
const __page = window.__page || {};
const DEFAULT_CREW_SIZE = 8; // 8 workers
const DEFAULT_HOURS_PER_DAY = 8; // 8 hours per day
const scopeTypesByCode = __page.create0_0;
const materialSuppliers = __page.create0_1;
const guestQuotationData = __page.create0_2;
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

function updateScopeDays(room) {
    const length = parseFloat(room.querySelector('.room-dimension[name$="[length]"]')?.value) || 1;
    const width = parseFloat(room.querySelector('.room-dimension[name$="[width]"]')?.value) || 1;
    const floorArea = length * width;
    const height = parseFloat(room.querySelector('.room-dimension[name$="[height]"]')?.value) || 1;
    const wallArea = 2 * (length + width) * height;
    room.querySelectorAll('.scope-checkbox').forEach(cb => {
        const scopeId = cb.value;
        const scope = scopeTypesByCode[scopeId];
        if (!scope) return;
        // Use wall area for wall work, floor area otherwise
        const area = scope.is_wall_work ? wallArea : floorArea;
        const laborHoursPerSqm = parseFloat(scope.labor_hours_per_sqm) || 1; // default 1 hour per sqm if not set
        const totalLaborHours = area * laborHoursPerSqm;
        let days = totalLaborHours / (DEFAULT_CREW_SIZE * DEFAULT_HOURS_PER_DAY);
        days = Math.ceil(days * 2) / 2; // round up to nearest half day
        days = Math.max(0.5, days); // minimum 0.5 days
        const daysSpan = room.querySelector(`.scope-days[data-scope-id="${scopeId}"]`);
        if (daysSpan) {
            daysSpan.textContent = days > 0 ? `(${days} day${days !== 1 ? 's' : ''})` : '';
        }
    });
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
                                    ${scopes.map(scope => {
    // Calculate area and days for this scope
    const length = parseFloat(roomContainer.querySelector('.room-dimension[name$="[length]"]')?.value) || 1;
    const width = parseFloat(roomContainer.querySelector('.room-dimension[name$="[width]"]')?.value) || 1;
    const height = parseFloat(roomContainer.querySelector('.room-dimension[name$="[height]"]')?.value) || 1;
    const floorArea = length * width;
    const wallArea = 2 * (length + width) * height;
    const area = scope.is_wall_work ? wallArea : floorArea;
    const laborHoursPerSqm = parseFloat(scope.labor_hours_per_sqm) || 1;
    const totalLaborHours = area * laborHoursPerSqm;
    let days = totalLaborHours / (DEFAULT_CREW_SIZE * DEFAULT_HOURS_PER_DAY);
    days = Math.ceil(days * 2) / 2;
    days = Math.max(0.5, days);
    return `
        <div class="col-md-6">
            <div class="scope-item mb-4">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input scope-checkbox" 
                        name="rooms[${roomId}][scope][]" 
                        value="${scope.id}" 
                        id="scope_${scope.id}_${roomId}"
                        data-days="${days}">
                    <label class="form-check-label" for="scope_${scope.id}_${roomId}">
                        <strong>${scope.name}</strong>
                        <span class="badge bg-info ms-2 scope-days" data-scope-id="${scope.id}" data-room-id="${roomId}"></span>
                        ${(scope.materials && scope.materials.length > 0) ? `
                            <ul class='mb-0 ms-3'>
                                ${scope.materials.map(material => `
                                    <li>${material.name} <span class='text-muted'>(₱${parseFloat(material.base_price).toFixed(2)})</span></li>
                                `).join('')}
                            </ul>
                        ` : ''}
                    </label>
                </div>
            </div>
        </div>
    `;
}).join('')}
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
            updateScopeDays(roomContainer);
            updateBreakdownTable();
        });
    });
    roomContainer.querySelectorAll('.scope-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            updateScopeDays(roomContainer);
            updateBreakdownTable();
        });
    });
    // Initial area calculation
    updateRoomAreas(roomContainer);
    updateScopeDays(roomContainer);
    updateBreakdownTable();
}

function updateBreakdownTable() {
    const tbody = document.querySelector('#breakdownTable tbody');
    tbody.innerHTML = '';
    let rowIdx = 0;
    let overallTotal = 0;
    document.querySelectorAll('.room-row').forEach(room => {
        updateScopeDays(room);
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
                overallTotal += baseTotal;
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
    // Update Overall Total (sum of Base Total Cost)
    const overallCell = document.getElementById('overallTotalCell');
    if (overallCell) overallCell.textContent = '₱' + overallTotal.toFixed(2);
}

// Restore form from guest data (after login)
function restoreGuestQuotationData() {
    if (!guestQuotationData || !guestQuotationData.rooms || typeof guestQuotationData.rooms !== 'object') return;
    const rooms = guestQuotationData.rooms;
    document.getElementById('roomDetails').innerHTML = '';
    Object.keys(rooms).forEach(roomId => {
        const r = rooms[roomId];
        const roomData = {
            id: roomId,
            name: r.name || '',
            length: r.length != null ? r.length : '',
            width: r.width != null ? r.width : '',
            height: r.height != null ? r.height : '',
            floor_area: r.floor_area != null ? r.floor_area : '0.00',
            wall_area: r.wall_area != null ? r.wall_area : '0.00'
        };
        createRoomRow(roomData);
        const scopeIds = Array.isArray(r.scope) ? r.scope : [];
        const roomRow = document.querySelector(`.room-row[data-room-id="${roomId}"]`);
        if (roomRow) {
            scopeIds.forEach(scopeId => {
                const cb = roomRow.querySelector(`.scope-checkbox[value="${scopeId}"]`);
                if (cb) cb.checked = true;
            });
            updateScopeDays(roomRow);
        }
    });
    updateBreakdownTable();
}

// --- Event Listeners ---
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addRoomBtn').addEventListener('click', function() {
        createRoomRow();
    });
    if (guestQuotationData && guestQuotationData.rooms && Object.keys(guestQuotationData.rooms).length > 0) {
        restoreGuestQuotationData();
    } else {
        updateBreakdownTable();
    }
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
