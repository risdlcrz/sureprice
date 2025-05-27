@extends('layouts.app')

@section('content')
<div class="content">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Company Details</h1>
            <div class="mt-2">
                <span class="badge bg-{{ $company->designation === 'client' ? 'info' : 'primary' }} me-2" style="font-size: 1rem;">
                    <i class="fas fa-{{ $company->designation === 'client' ? 'user-tie' : 'truck' }} me-1"></i>
                    {{ ucfirst($company->designation) }}
                </span>
                <span class="badge bg-{{ $company->status === 'approved' ? 'success' : ($company->status === 'pending' ? 'warning' : 'danger') }}" style="font-size: 1rem;">
                    <i class="fas fa-{{ $company->status === 'approved' ? 'check-circle' : ($company->status === 'pending' ? 'clock' : 'times-circle') }} me-1"></i>
                    {{ ucfirst($company->status) }}
                </span>
            </div>
        </div>
        <a href="{{ route('information-management.index', ['type' => 'company']) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Companies
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Company Name:</strong> {{ $company->company_name }}</p>
                            <p><strong>Contact Person:</strong> {{ $company->contact_person }}</p>
                            <p><strong>Email:</strong> {{ $company->email }}</p>
                            <p><strong>Mobile:</strong> {{ $company->mobile_number }}</p>
                            @if($company->telephone_number)
                                <p><strong>Telephone:</strong> {{ $company->telephone_number }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $company->status === 'approved' ? 'success' : ($company->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($company->status) }}
                                </span>
                            </p>
                            <p><strong>Type:</strong> {{ $company->supplier_type }}</p>
                            <p><strong>Designation:</strong> {{ ucfirst($company->designation) }}</p>
                            <p><strong>Business Registration:</strong> {{ $company->business_reg_no ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Address</h5>
                </div>
                <div class="card-body">
                    <p><strong>Street:</strong> {{ $company->street }}</p>
                    <p><strong>City:</strong> {{ $company->city }}</p>
                    <p><strong>Province:</strong> {{ $company->province }}</p>
                    <p><strong>ZIP Code:</strong> {{ $company->zip_code }}</p>
                </div>
            </div>

            <!-- Business Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Business Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Years in Operation:</strong> {{ $company->years_operation ?? 'N/A' }}</p>
                            <p><strong>Business Size:</strong> {{ $company->business_size ?? 'N/A' }}</p>
                            <p><strong>VAT Registered:</strong> {{ $company->vat_registered ? 'Yes' : 'No' }}</p>
                            <p><strong>Using SurePrice:</strong> {{ $company->use_sureprice ? 'Yes' : 'No' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Payment Terms:</strong> {{ $company->payment_terms ?? 'N/A' }}</p>
                            <p><strong>Service Areas:</strong> {{ $company->service_areas ?? 'N/A' }}</p>
                            @if($company->primary_products_services)
                                <p><strong>Primary Products/Services:</strong> {{ $company->primary_products_services }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Documents -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Documents</h5>
                </div>
                <div class="card-body">
                    @forelse($company->documents as $document)
                        <div class="mb-3">
                            <strong>{{ str_replace('_', ' ', $document->type) }}:</strong><br>
                            <a href="{{ Storage::disk($document->disk)->url($document->path) }}" 
                               class="btn btn-sm btn-outline-primary" 
                               target="_blank">
                                <i class="fas fa-download me-1"></i>
                                View Document
                            </a>
                        </div>
                    @empty
                        <p class="text-muted">No documents uploaded.</p>
                    @endforelse
                </div>
            </div>

            <!-- Bank Details -->
            @if($company->bankDetails)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-university me-2"></i>Bank Details</h5>
                </div>
                <div class="card-body">
                    <p><strong>Bank Name:</strong> {{ $company->bankDetails->bank_name }}</p>
                    <p><strong>Account Name:</strong> {{ $company->bankDetails->account_name }}</p>
                    <p><strong>Account Number:</strong> {{ $company->bankDetails->account_number }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection 