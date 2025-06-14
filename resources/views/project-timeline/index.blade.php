@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h1 class="h3 mb-4">Project Timeline</h1>
                    <!-- Overall Project Progress Bar -->
                    <div id="projectProgressBar" class="progress mb-4" style="height: 40px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            <span class="fw-bold">0% Complete</span>
                        </div>
                    </div>
                    <!-- Contract Progress Cards -->
                    <div id="contractProgressDetails" class="row g-4">
                        @foreach($contracts as $contract)
                            <div class="col-12 col-md-12 col-lg-12 mb-4">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Left Column: Contract Details -->
                                            <div class="col-md-6 border-end pe-4">
                                                <h5 class="card-title mb-2">{{ $contract->client->name ?? $contract->client ?? 'N/A' }} <small class="text-muted">(Client)</small></h5>
                                                <div class="mb-2"><strong>Contractor:</strong> {{ $contract->contractor->name ?? $contract->contractor ?? 'N/A' }}</div>
                                                <div class="mb-2"><strong>Contract Number:</strong> {{ $contract->contract_number ?? 'N/A' }}</div>
                                                <div class="mb-2">
                                                    <strong>Status:</strong>
                                                    <span class="badge bg-{{ $contract->status === 'APPROVED' ? 'success' : ($contract->status === 'DRAFT' ? 'secondary' : 'info') }}">
                                                        {{ strtoupper($contract->status ?? 'N/A') }}
                                                    </span>
                                                </div>
                                                <div class="mb-2">
                                                    <strong>Start:</strong> {{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('M d, Y') : 'N/A' }}
                                                </div>
                                                <div class="mb-2">
                                                    <strong>End:</strong> {{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('M d, Y') : 'N/A' }}
                </div>
                                                <div class="mb-2"><strong>Budget:</strong> ₱{{ number_format($contract->total_amount ?? 0, 2) }}</div>
                                                
                                                @php
                                                    $pendingPO = $contract->purchaseOrders->where('status', 'pending')->count();
                                                    $partiallyDeliveredPO = $contract->purchaseOrders->where('status', 'partially_delivered')->count();
                                                    $completedPO = $contract->purchaseOrders->where('status', 'completed')->count();
                                                    $totalPO = $contract->purchaseOrders->count();
                                                @endphp

                                                <div class="mb-2 mt-3">
                                                    <strong>Delivery Status:</strong>
                                                    @if($totalPO > 0)
                                                        <ul class="list-unstyled mb-0 ms-2">
                                                            <li><i class="bi bi-box-seam-fill text-primary"></i> Total POs: {{ $totalPO }}</li>
                                                            @if($pendingPO > 0)
                                                                <li><i class="bi bi-hourglass text-warning"></i> Pending: {{ $pendingPO }}</li>
                                                            @endif
                                                            @if($partiallyDeliveredPO > 0)
                                                                <li><i class="bi bi-arrow-right-circle text-info"></i> Partially Delivered: {{ $partiallyDeliveredPO }}</li>
                                                            @endif
                                                            @if($completedPO > 0)
                                                                <li><i class="bi bi-truck text-success"></i> Delivered: {{ $completedPO }}</li>
                                                            @endif
                                                        </ul>
                                                    @else
                                                        <span class="text-muted">No Purchase Orders.</span>
                                                    @endif
            </div>

                                                <div class="mt-3">
                                                    <a href="{{ route('contracts.show', $contract->id) }}" class="btn btn-primary btn-sm">View Full Contract</a>
        </div>
    </div>

                                            <!-- Right Column: Scope of Work & Tasks -->
                                            <div class="col-md-6 ps-4">
                                                <h5 class="card-title mb-3">Scope of Work & Tasks</h5>
                                                @if($contract->rooms->count() > 0)
                                                    @foreach($contract->rooms as $room)
            <div class="mb-3">
                                                            <h6>Room: {{ $room->name ?? 'N/A' }}</h6>
                                                            @if($room->scopeTypes->count() > 0)
                                                                <div class="accordion accordion-flush" id="roomAccordion{{ $room->id }}">
                                                                    @foreach($room->scopeTypes as $scopeType)
                                                                        <div class="accordion-item">
                                                                            <h2 class="accordion-header" id="headingScope{{ $room->id }}{{ $loop->parent->index }}{{ $loop->index }}">
                                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseScope{{ $room->id }}{{ $loop->parent->index }}{{ $loop->index }}" aria-expanded="false" aria-controls="collapseScope{{ $room->id }}{{ $loop->parent->index }}{{ $loop->index }}">
                                                                                    <strong>{{ $scopeType->name ?? 'N/A' }}</strong> ({{ $scopeType->estimated_days ?? 0 }} days)
                </button>
                                                                            </h2>
                                                                            <div id="collapseScope{{ $room->id }}{{ $loop->parent->index }}{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->index == 0 ? 'show' : '' }}" aria-labelledby="headingScope{{ $room->id }}{{ $loop->parent->index }}{{ $loop->index }}" data-bs-parent="#roomAccordion{{ $room->id }}">
                                                                                <div class="accordion-body">
                                                                                    @if(isset($scopeType->items) && count($scopeType->items) > 0)
                                                                                        <p class="fw-bold mb-1">Tasks:</p>
                                                                                        <ul class="list-unstyled mb-2">
                                                                                            @foreach($scopeType->items as $taskItem)
                                                                                                <li>
                                                                                                    <i class="bi bi-check-circle-fill text-success"></i> {{ $taskItem }}
                                                                                                </li>
                                                                                            @endforeach
                                                                                        </ul>
                                                                                    @else
                                                                                        <p class="text-muted mb-2">No defined tasks for this scope.</p>
                                                                                    @endif

                                                                                    @if($scopeType->materials->count() > 0)
                                                                                        <p class="fw-bold mb-1">Materials:</p>
                                                                                        <ul class="list-unstyled">
                                                                                            @foreach($scopeType->materials as $material)
                                                                                                <li>- {{ $material->name ?? 'N/A' }}</li>
                                                                                            @endforeach
                                                                                        </ul>
                                                                                    @else
                                                                                        <p class="text-muted">No materials defined.</p>
                                                                                    @endif
            </div>
                                </div>
                            </div>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <p class="text-muted">No scope types defined for this room.</p>
                                                            @endif
                                    </div>
                                                    @endforeach
                                                @else
                                                    <p class="text-muted">No rooms or scopes defined for this contract.</p>
                                                @endif
                                    </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <hr/>
                    <!-- Filters Section (kept as requested) -->
                    <div class="mb-3">
                        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="false" aria-controls="filtersCollapse">
                            <i class="bi bi-funnel"></i> Filters
                        </button>
                        <div class="collapse mt-2" id="filtersCollapse">
                            <form id="filtersForm" class="row g-3">
                                <div class="col-md-3">
                                    <label for="searchInput" class="form-label">Search</label>
                                    <input type="text" class="form-control" id="searchInput" name="searchInput" placeholder="Search contracts or tasks...">
                                </div>
                                <div class="col-md-3">
                                    <label for="statusFilter" class="form-label">Status</label>
                                    <select class="form-select" id="statusFilter" name="statusFilter">
                                        <option value="">All</option>
                                        <option value="approved">Approved</option>
                                        <option value="draft">Draft</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="startDate" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="startDate" name="startDate">
                                </div>
                                <div class="col-md-3">
                                    <label for="endDate" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="endDate" name="endDate">
                                </div>
                            <div class="col-12">
                                    <button type="submit" class="btn btn-success">Apply Filters</button>
                                    <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
                            </form>
                        </div>
                    </div>
                    <!-- Calendar Section -->
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" crossorigin="anonymous" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" crossorigin="anonymous" />
<style>
.progress-bar {
    font-size: 1.2rem;
}
.card {
    border-radius: 1rem;
}
.card-body {
    background: #fff;
}
.accordion-button:focus {
    box-shadow: none;
}
.contract-card:hover {
    box-shadow: 0 0 0 4px #0d6efd33, 0 2px 8px rgba(0,0,0,0.08);
    border-color: #0d6efd;
}
.accordion-button:not(.collapsed) {
    background-color: #e9ecef;
    color: #495057;
}
.accordion-body {
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    padding: 1rem;
}
.contract-card .card-body .row .col-md-6:first-child {
    border-right: 1px solid #dee2e6;
}
.contract-card .card-body .row .col-md-6:last-child {
    padding-left: 1.5rem;
}
/* Color coding for FullCalendar events by status */
.status-approved {
    background-color: #198754 !important;
    color: #fff !important;
    border: none !important;
}
.status-draft {
    background-color: #6c757d !important;
    color: #fff !important;
    border: none !important;
}
.status-rejected {
    background-color: #dc3545 !important;
    color: #fff !important;
    border: none !important;
}
.status-pending {
    background-color: #ffc107 !important;
    color: #212529 !important;
    border: none !important;
}
.status-in_progress {
    background-color: #0dcaf0 !important;
    color: #212529 !important;
    border: none !important;
}
.status-completed {
    background-color: #198754 !important;
    color: #fff !important;
    border: none !important;
}
.status-delayed {
    background-color: #fd7e14 !important;
    color: #fff !important;
    border: none !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js" crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Progress Bar ---
    const overallProgress = {{ $overallProjectProgress ?? 0 }};
    const progressBar = document.getElementById('projectProgressBar').querySelector('.progress-bar');
    progressBar.style.width = overallProgress + '%';
    progressBar.setAttribute('aria-valuenow', overallProgress);
    progressBar.innerHTML = `<span class='fw-bold'>${overallProgress}% Complete</span>`;

    // --- Contract Progress Cards (Removed JS rendering, now handled by Blade) ---
    // Original renderContractProgressDetails function and its call are removed.

    // --- Calendar ---
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: [ FullCalendar.dayGridPlugin ],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,dayGridDay'
        },
        events: @json($calendarEvents ?? []),
        eventDidMount: function(info) {
            const event = info.event;
            const props = event.extendedProps || {};
            let statusClass = '';
            if (props.status) {
                // Ensure status is lowercase and underscores for spaces
                statusClass = `status-${String(props.status).toLowerCase().replace(/\s+/g, '_')}`;
            }
            if (statusClass) {
                info.el.classList.add(statusClass);
            }
            // Tooltip
            let tooltipContent = `<div class='p-2'><strong>${event.title}</strong><br/>`;
            if (props.client) tooltipContent += `Client: ${props.client}<br/>`;
            if (props.contractor) tooltipContent += `Contractor: ${props.contractor}<br/>`;
            if (props.status) tooltipContent += `Status: ${props.status}<br/>`;
            if (props.progress !== undefined) tooltipContent += `Progress: ${props.progress}%<br/>`;
            tooltipContent += `</div>`;
            info.el.title = tooltipContent.replace(/<br\/>/g, '\n');
        },
        eventClick: function(info) {
            // Optionally show modal/details
        }
    });
    calendar.render();
});
</script>
@endpush 