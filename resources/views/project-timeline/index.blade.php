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
                            <div class="col-12 col-md-6 col-lg-4">
                                <a href="{{ route('contracts.show', $contract->id) }}" class="card shadow-sm h-100 text-decoration-none text-dark contract-card" style="cursor:pointer; transition:box-shadow 0.2s;">
                                    <div class="card-body">
                                        <h5 class="card-title mb-2">{{ $contract->client->name ?? $contract->client ?? 'N/A' }} <small class="text-muted">(Client)</small></h5>
                                        <div class="mb-2"><strong>Contractor:</strong> {{ $contract->contractor->name ?? $contract->contractor ?? 'N/A' }}</div>
                                        <div class="mb-2"><strong>Contract Number:</strong> {{ $contract->contract_number ?? 'N/A' }}</div>
                                        <div class="mb-2">
                                            <strong>Status:</strong>
                                            <span class="badge {{ $contract->status === 'APPROVED' ? 'bg-success' : 'bg-secondary' }}">
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
                                        <div class="mb-2">
                                            <strong>Scope of Work:</strong>
                                            {{ is_array($contract->scope_of_work ?? null) ? implode(', ', $contract->scope_of_work) : ($contract->scope_of_work ?? 'N/A') }}
                                        </div>

                                        @if($contract->tasks->count() > 0)
                                            <div class="mt-3">
                                                <button class="btn btn-link p-0" type="button" data-bs-toggle="collapse" data-bs-target="#tasksCollapse{{ $contract->id }}" aria-expanded="false" aria-controls="tasksCollapse{{ $contract->id }}">
                                                    <i class="bi bi-list-task"></i> Show Tasks ({{ $contract->tasks->count() }})
                                                </button>
                                                <div class="collapse mt-2" id="tasksCollapse{{ $contract->id }}">
                                                    @foreach($contract->tasks as $task)
                                                        <div class="card card-body mb-2 p-2 border-0 shadow-sm">
                                                            <strong>{{ $task->title }}</strong>
                                                            <small class="text-muted">{{ $task->start_date->format('M d, Y') }} - {{ $task->end_date->format('M d, Y') }}</small>
                                                            <div class="progress mt-1" style="height: 15px;">
                                                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $task->progress ?? 0 }}%;" aria-valuenow="{{ $task->progress ?? 0 }}" aria-valuemin="0" aria-valuemax="100">{{ $task->progress ?? 0 }}%</div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <div class="mt-3 text-muted">No tasks defined for this contract.</div>
                                        @endif
                                    </div>
                                </a>
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
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/main.min.css" rel="stylesheet" crossorigin="anonymous" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" crossorigin="anonymous" />
<style>
.progress-bar {
    font-size: 1.2rem;
}
.card {
    border-radius: 1rem;
}
.card-body {
    background: #f9f9f9;
}
.accordion-button:focus {
    box-shadow: none;
}
.contract-card:hover {
    box-shadow: 0 0 0 4px #0d6efd33, 0 2px 8px rgba(0,0,0,0.08);
    border-color: #0d6efd;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js" crossorigin="anonymous"></script>
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
                statusClass = `status-${props.status}`;
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