{{-- Create/edit project. Params: $contracts, $users, optional $project. --}}
@extends('layouts.app')

@php $p = $project ?? null; $title = $p ? 'Edit Project' : 'Create New Project'; @endphp
@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-page-header :title="$title" icon="project-diagram">
        <a href="{{ $p ? route('projects.show', $p) : route('projects.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
    </x-page-header>

    <div class="card">
        <div class="card-body">
            <form action="{{ $p ? route('projects.update', $p) : route('projects.store') }}" method="POST">
                @csrf
                @if($p) @method('PUT') @endif
                <div class="row">
                    @if($p)
                        <x-field name="status" label="Status" col="md-6" required>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                @foreach(\App\Models\Project::STATUSES as $status)
                                    <option value="{{ $status }}" @selected(old('status', $p->status) === $status)>{{ \App\Support\Status::label($status) }}</option>
                                @endforeach
                            </select>
                        </x-field>
                    @else
                        <x-field name="contract_id" label="Contract" col="md-6" required>
                            <select name="contract_id" id="contract_id" class="form-select @error('contract_id') is-invalid @enderror" required>
                                <option value="">{{ $contracts->isEmpty() ? 'No approved contracts without a project' : 'Select Contract' }}</option>
                                @foreach($contracts as $contract)
                                    <option value="{{ $contract->id }}" @selected(old('contract_id') == $contract->id)>{{ $contract->contract_number }}{{ $contract->title ? ' - ' . $contract->title : '' }}</option>
                                @endforeach
                            </select>
                        </x-field>
                    @endif
                    <x-field name="name" label="Project Name" :value="$p?->name" col="md-6" required />
                </div>
                <x-field name="description" label="Description" type="textarea" :value="$p?->description" />
                <div class="row">
                    <x-field name="start_date" label="Start Date" type="date" :value="$p?->start_date?->format('Y-m-d')" col="md-6" required />
                    <x-field name="end_date" label="End Date" type="date" :value="$p?->end_date?->format('Y-m-d')" col="md-6" required />
                    @foreach(['project_manager_id' => 'Project Manager', 'client_representative_id' => 'Client Representative'] as $field => $label)
                        <x-field :name="$field" :label="$label" col="md-6" required>
                            <select name="{{ $field }}" id="{{ $field }}" class="form-select @error($field) is-invalid @enderror" required>
                                <option value="">Select {{ $label }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(old($field, $p?->$field) == $user->id)>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </x-field>
                    @endforeach
                    <x-field name="budget" label="Budget (₱)" type="number" step="0.01" min="0" :value="$p?->budget" col="md-6" required />
                </div>
                <x-field name="notes" label="Notes" type="textarea" :value="$p?->notes" />
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ $p ? route('projects.show', $p) : route('projects.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">{{ $p ? 'Update Project' : 'Create Project' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@vite('resources/js/pages/projects-form.js')
@endpush
