{{-- Project overview card (shared by projects.show and procurement.projects.show). Param: $project --}}
@php
    $fields = array_filter([
        'Contract' => $project->contract
            ? '<a href="' . e(route('contracts.show', $project->contract)) . '" class="text-decoration-none">' . e($project->contract->contract_number) . '</a>'
            : null,
        'Budget' => '₱' . number_format($project->budget, 2),
        'Timeline' => $project->start_date && $project->end_date
            ? e($project->start_date->format('M d, Y') . ' - ' . $project->end_date->format('M d, Y'))
              . '<br><small class="text-muted">' . e($project->start_date->diffForHumans($project->end_date, true)) . ' duration</small>'
            : null,
        'Project Manager' => e($project->projectManager?->name ?? 'Unassigned'),
        'Client Representative' => e($project->clientRepresentative?->name ?? 'Unassigned'),
        'Description' => $project->description ? e($project->description) : null,
        'Notes' => $project->notes ? e($project->notes) : null,
    ]);
@endphp
<div class="card mb-4">
    <div class="card-header">Project Overview</div>
    <div class="card-body">
        <div class="mb-3">
            <div class="fw-bold">Status</div>
            <x-status-badge :status="$project->status" />
        </div>
        <div class="mb-3">
            <div class="fw-bold">Progress</div>
            <x-progress :value="$project->progress" />
        </div>
        @foreach($fields as $label => $html)
            <div class="mb-3">
                <div class="fw-bold">{{ $label }}</div>
                <div>{!! $html !!}</div>
            </div>
        @endforeach
    </div>
</div>
