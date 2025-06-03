@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Inquiry Details</h4>
                    <div>
                        <a href="{{ route('inquiries.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        <a href="{{ route('inquiries.edit', $inquiry->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Basic Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">Basic Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Inquiry ID</th>
                                    <td>{{ $inquiry->id }}</td>
                                </tr>
                                <tr>
                                    <th>Contract</th>
                                    <td>{{ $inquiry->contract->contract_id ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Subject</th>
                                    <td>{{ $inquiry->subject }}</td>
                                </tr>
                                <tr>
                                    <th>Department</th>
                                    <td>{{ $inquiry->department }}</td>
                                </tr>
                                <tr>
                                    <th>Priority</th>
                                    <td>
                                        <span class="badge badge-priority">
                                            {{ ucfirst($inquiry->priority) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge badge-status">
                                            {{ ucfirst($inquiry->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Required Date</th>
                                    <td>{{ $inquiry->required_date->format('M d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Description</h5>
                            <div class="card">
                                <div class="card-body">
                                    {{ $inquiry->description }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Materials -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">Requested Materials</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Code</th>
                                            <th>Description</th>
                                            <th>Unit</th>
                                            <th>Quantity</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($inquiry->materials as $material)
                                        <tr>
                                            <td><strong>{{ $material->name }}</strong></td>
                                            <td>{{ $material->code ?? 'N/A' }}</td>
                                            <td>{{ $material->description ?? 'N/A' }}</td>
                                            <td>{{ $material->unit ?? 'N/A' }}</td>
                                            <td>{{ $material->pivot->quantity }}</td>
                                            <td>{{ $material->pivot->notes ?? 'N/A' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No materials requested</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Attachments -->
                    @if($inquiry->attachments->count() > 0)
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3">Attachments</h5>
                            <div class="row">
                                @foreach($inquiry->attachments as $attachment)
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fas fa-file"></i>
                                                    {{ $attachment->original_name }}
                                                </div>
                                                <div>
                                                    <a href="{{ Storage::url($attachment->path) }}" 
                                                       class="btn btn-sm btn-info" 
                                                       target="_blank"
                                                       title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table th {
        background-color: #f8f9fa;
    }
    .badge {
        font-size: 0.9em;
        background-color: #222 !important;
        color: #fff !important;
        padding: 0.5em 0.75em;
        border-radius: 0.5em;
    }
    .badge-priority, .badge-status {
        background-color: #6c757d !important;
        color: #fff !important;
    }
    .table tbody tr {
        background-color: #f6f6f6;
    }
</style>
@endpush
@endsection 