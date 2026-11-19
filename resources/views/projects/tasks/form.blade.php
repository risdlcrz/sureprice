{{--
    Create/edit a project task. Used by the project task pages (projects.tasks.*) and the project timeline.
    Params: $users, optional $task; $project (fixed project) or $projects (timeline create: choose one);
            $rooms (rooms of the task's contract, with scopeTypes) on edit; $timeline (bool).
--}}
@extends('layouts.app')

@php
    $t = $task ?? null;
    $timeline = $timeline ?? false;
    $rooms = $rooms ?? collect();
    $title = $t ? 'Edit Task' : 'Create New Task';
    $action = match (true) {
        $timeline && $t => route('project-timeline.update', $t),
        $timeline => route('project-timeline.store'),
        (bool) $t => route('projects.tasks.update', [$project, $t]),
        default => route('projects.tasks.store', $project),
    };
    $back = match (true) {
        $timeline && $t => route('project-timeline.show', $t),
        $timeline => route('project-timeline.index'),
        (bool) $t => route('projects.tasks.show', [$project, $t]),
        default => route('projects.show', $project),
    };
@endphp
@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-page-header :title="$title . (isset($project) ? ' — ' . $project->name : '')" icon="tasks">
        <a href="{{ $back }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
    </x-page-header>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $action }}">
                @csrf
                @if($t) @method('PUT') @endif
                <div class="row">
                    @if(isset($projects))
                        <x-field name="project_id" label="Project" col="md-6" required>
                            <select name="project_id" id="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                <option value="">{{ $projects->isEmpty() ? 'No projects yet' : 'Select Project' }}</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}" @selected(old('project_id') == $p->id)>{{ $p->name }}{{ $p->contract ? ' (' . $p->contract->contract_number . ')' : '' }}</option>
                                @endforeach
                            </select>
                        </x-field>
                    @endif
                    <x-field name="name" label="Task Name" :value="$t?->name" col="md-6" required />
                </div>
                <x-field name="description" label="Description" type="textarea" :value="$t?->description" />
                <div class="row">
                    <x-field name="start_date" label="Start Date" type="date" :value="$t?->start_date?->format('Y-m-d')" col="md-6" required />
                    <x-field name="due_date" label="Due Date" type="date" :value="$t?->due_date?->format('Y-m-d')" col="md-6" required />
                    <x-field name="priority" label="Priority" col="md-4" required>
                        <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror" required>
                            @foreach(\App\Models\ProjectTask::PRIORITIES as $priority)
                                <option value="{{ $priority }}" @selected(old('priority', $t?->priority ?? 'medium') === $priority)>{{ ucfirst($priority) }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    @if($t)
                        <x-field name="status" label="Status" col="md-4" required>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                @foreach(\App\Models\ProjectTask::STATUSES as $status)
                                    <option value="{{ $status }}" @selected(old('status', $t->status) === $status)>{{ \App\Support\Status::label($status) }}</option>
                                @endforeach
                            </select>
                        </x-field>
                        <x-field name="progress" label="Progress (%)" type="number" min="0" max="100" :value="$t->progress" col="md-4" required />
                    @endif
                    <x-field name="assigned_to" label="Assigned To" col="md-6">
                        <select name="assigned_to" id="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected(old('assigned_to', $t?->assigned_to) == $user->id)>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    @if($rooms->isNotEmpty())
                        <x-field name="room_id" label="Room" col="md-3">
                            <select name="room_id" id="room_id" class="form-select">
                                <option value="">—</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" @selected(old('room_id', $t?->room_id) == $room->id)>{{ $room->name }}</option>
                                @endforeach
                            </select>
                        </x-field>
                        <x-field name="scope_type_id" label="Scope" col="md-3">
                            <select name="scope_type_id" id="scope_type_id" class="form-select">
                                <option value="">—</option>
                                @foreach($rooms as $room)
                                    @foreach($room->scopeTypes as $scope)
                                        <option value="{{ $scope->id }}" data-room="{{ $room->id }}" @selected(old('scope_type_id', $t?->scope_type_id) == $scope->id)>{{ $scope->name }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </x-field>
                    @endif
                </div>
                <x-field name="notes" label="Notes" type="textarea" :value="$t?->notes" />
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ $back }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">{{ $t ? 'Update Task' : 'Create Task' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@vite('resources/js/pages/projects-tasks-form.js')
@endpush
