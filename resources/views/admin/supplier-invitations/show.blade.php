@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Supplier Invitation Details</h4>
                    <div>
                        @if($invitation->status === 'pending')
                            <a href="{{ route('supplier-invitations.edit', $invitation) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('supplier-invitations.resend', $invitation) }}" 
                                  method="POST" 
                                  class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-paper-plane"></i> Resend
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('supplier-invitations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Contract Information -->
                        <div class="col-md-6 mb-4">
                            <h5 class="mb-3">Contract Information</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="mb-1"><strong>Contract Name:</strong> {{ $invitation->contract->contract_id }}</p>
                                    <p class="mb-1"><strong>Invitation Code:</strong> {{ $invitation->invitation_code }}</p>
                                    <p class="mb-1">
                                        <strong>Status:</strong>
                                        <span class="badge bg-{{ $invitation->status_color }}">
                                            {{ ucfirst($invitation->status) }}
                                        </span>
                                    </p>
                                    <p class="mb-0"><strong>Due Date:</strong> {{ $invitation->due_date->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Supplier Information -->
                        <div class="col-md-6 mb-4">
                            <h5 class="mb-3">Supplier Information</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="mb-1"><strong>Company Name:</strong> {{ $invitation->company_name }}</p>
                                    <p class="mb-1"><strong>Contact Person:</strong> {{ $invitation->contact_name }}</p>
                                    <p class="mb-1"><strong>Email:</strong> {{ $invitation->email }}</p>
                                    <p class="mb-0"><strong>Phone:</strong> {{ $invitation->phone }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Materials -->
                        <div class="col-12 mb-4">
                            <h5 class="mb-3">Materials</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        @forelse($invitation->materials as $material)
                                            <div class="col-md-4 mb-2">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-box me-2"></i>
                                                    <span>{{ $material->name }}</span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <p class="text-muted mb-0">No materials selected</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Message -->
                        <div class="col-12">
                            <h5 class="mb-3">Message</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {{ $invitation->message ?? 'No message provided.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 