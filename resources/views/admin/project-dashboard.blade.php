@extends('layouts.app')

@section('content')
    <div class="sidebar">
    @include('include.header_project')
    </div>

    <div class="content">
    <h1 class="text-center my-4">Project & Procurement Dashboard</h1>

    <div class="container-fluid">
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <!-- Create Contract Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('images/new-contract.jpg') }}" class="card-img-top" alt="Create Contract">
                    <div class="card-body">
                        <h5 class="card-title">Create Contract</h5>
                        <p class="card-text">Start a new contract and set up initial terms and conditions.</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('contracts.create') }}" class="btn btn-primary w-100">Create New Contract</a>
                    </div>
                </div>
            </div>

            <!-- View Contracts Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('images/view-contracts.jpg') }}" class="card-img-top" alt="View Contracts">
                    <div class="card-body">
                        <h5 class="card-title">View Contracts</h5>
                        <p class="card-text">Access and manage existing contracts, track status and approvals.</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('contracts.index') }}" class="btn btn-secondary w-100">View All Contracts</a>
                    </div>
                </div>
            </div>

            <!-- Create Inquiry Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('images/new-inquiry.jpg') }}" class="card-img-top" alt="Create Inquiry">
                    <div class="card-body">
                        <h5 class="card-title">Create Inquiry</h5>
                        <p class="card-text">Submit new material inquiries and track procurement requests.</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('inquiries.create') }}" class="btn btn-primary w-100">Create New Inquiry</a>
                    </div>
                </div>
            </div>

            <!-- View Inquiries Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('images/view-inquiries.jpg') }}" class="card-img-top" alt="View Inquiries">
                    <div class="card-body">
                        <h5 class="card-title">View Inquiries</h5>
                        <p class="card-text">Monitor and manage material inquiries and their responses.</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('inquiries.index') }}" class="btn btn-secondary w-100">View All Inquiries</a>
                    </div>
                </div>
            </div>

            <!-- Add Material Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('images/new-material.jpg') }}" class="card-img-top" alt="Add Material">
                    <div class="card-body">
                        <h5 class="card-title">Add Material</h5>
                        <p class="card-text">Add new materials to the system and set their specifications.</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('materials.create') }}" class="btn btn-primary w-100">Add New Material</a>
                    </div>
                </div>
            </div>

            <!-- View Materials Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('images/view-materials.jpg') }}" class="card-img-top" alt="View Materials">
                    <div class="card-body">
                        <h5 class="card-title">View Materials</h5>
                        <p class="card-text">Browse and manage existing materials, track inventory levels.</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('materials.index') }}" class="btn btn-secondary w-100">View All Materials</a>
                    </div>
                </div>
            </div>

            <!-- Add Supplier Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('images/new-supplier.jpg') }}" class="card-img-top" alt="Add Supplier">
                    <div class="card-body">
                        <h5 class="card-title">Add Supplier</h5>
                        <p class="card-text">Register new suppliers and their product offerings.</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('suppliers.create') }}" class="btn btn-primary w-100">Add New Supplier</a>
                    </div>
                </div>
            </div>

            <!-- View Suppliers Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('images/view-suppliers.jpg') }}" class="card-img-top" alt="View Suppliers">
                    <div class="card-body">
                        <h5 class="card-title">View Suppliers</h5>
                        <p class="card-text">Manage existing suppliers and their performance records.</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary w-100">View All Suppliers</a>
                    </div>
                </div>
            </div>

            <!-- Create RFQ Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('images/new-quotation.jpg') }}" class="card-img-top" alt="Create RFQ">
                    <div class="card-body">
                        <h5 class="card-title">Create RFQ</h5>
                        <p class="card-text">Create new requests for quotation and send to suppliers.</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('quotations.create') }}" class="btn btn-primary w-100">Create New RFQ</a>
                    </div>
                </div>
            </div>

            <!-- View RFQs Card -->
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('images/view-quotations.jpg') }}" class="card-img-top" alt="View RFQs">
                    <div class="card-body">
                        <h5 class="card-title">View RFQs</h5>
                        <p class="card-text">Track and manage requests for quotation and supplier responses.</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('quotations.index') }}" class="btn btn-secondary w-100">View All RFQs</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Contracts</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @forelse($recentContracts ?? [] as $contract)
                            <a href="{{ route('contracts.show', $contract->id) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $contract->project_name }}</h6>
                                    <small>{{ $contract->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ $contract->client_name }}</p>
                            </a>
                            @empty
                            <div class="list-group-item">
                                <p class="mb-0">No recent contracts</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Inquiries</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @forelse($recentInquiries ?? [] as $inquiry)
                            <a href="{{ route('inquiries.show', $inquiry->id) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $inquiry->subject }}</h6>
                                    <small>{{ $inquiry->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ Str::limit($inquiry->description, 50) }}</p>
                                <small class="text-muted">Priority: {{ ucfirst($inquiry->priority) }}</small>
                            </a>
                            @empty
                            <div class="list-group-item">
                                <p class="mb-0">No recent inquiries</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent RFQs</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @forelse($recentQuotations ?? [] as $quotation)
                            <a href="{{ route('quotations.show', $quotation->id) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $quotation->rfq_number }}</h6>
                                    <small>{{ $quotation->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ $quotation->project->name }}</p>
                                <small class="text-muted">Status: {{ ucfirst($quotation->status) }}</small>
                            </a>
                            @empty
                            <div class="list-group-item">
                                <p class="mb-0">No recent RFQs</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Activities</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @forelse($recentActivities ?? [] as $activity)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $activity->description }}</h6>
                                    <small>{{ $activity->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ $activity->details }}</p>
                            </div>
                            @empty
                            <div class="list-group-item">
                                <p class="mb-0">No recent activities</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('styles')
<style>
    .card {
        transition: transform 0.2s;
        cursor: pointer;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .card-img-top {
        height: 200px;
        object-fit: cover;
    }
    .card-footer {
        background: none;
        border-top: none;
    }
    .btn {
        margin-right: 5px;
    }
</style>
@endpush
@endsection
