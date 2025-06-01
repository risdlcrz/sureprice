@extends('layouts.app')

@section('content')
    <div class="sidebar">
        @include('include.header_project')
    </div>

    <div class="content">
        <h1 class="text-center my-4">Procurement Dashboard</h1>

        <div class="container-fluid">
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <!-- Purchase Requests Card -->
                <div class="col">
                    <div class="card h-100">
                        <img src="{{ asset('images/purchase-request.jpg') }}" class="card-img-top" alt="Purchase Requests">
                        <div class="card-body">
                            <h5 class="card-title">Purchase Requests</h5>
                            <p class="card-text">Create and manage purchase requests for materials and supplies.</p>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('purchase-request.create') }}" class="btn btn-primary w-100">Create Purchase Request</a>
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

                <!-- Create Invitation Card -->
                <div class="col">
                    <div class="card h-100">
                        <img src="{{ asset('images/new-invitation.jpg') }}" class="card-img-top" alt="Create Invitation">
                        <div class="card-body">
                            <h5 class="card-title">Create Invitation</h5>
                            <p class="card-text">Create new supplier invitations for bidding opportunities.</p>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('supplier-invitations.create') }}" class="btn btn-primary w-100">Create New Invitation</a>
                        </div>
                    </div>
                </div>

                <!-- View Invitations Card -->
                <div class="col">
                    <div class="card h-100">
                        <img src="{{ asset('images/view-invitations.jpg') }}" class="card-img-top" alt="View Invitations">
                        <div class="card-body">
                            <h5 class="card-title">View Invitations</h5>
                            <p class="card-text">Track and manage supplier invitations and their responses.</p>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('supplier-invitations.index') }}" class="btn btn-secondary w-100">View All Invitations</a>
                        </div>
                    </div>
                </div>

                <!-- Materials Management Card -->
                <div class="col">
                    <div class="card h-100">
                        <img src="{{ asset('images/materials.jpg') }}" class="card-img-top" alt="Materials Management">
                        <div class="card-body">
                            <h5 class="card-title">Materials Management</h5>
                            <p class="card-text">View and manage materials inventory and specifications.</p>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('materials.index') }}" class="btn btn-secondary w-100">Manage Materials</a>
                        </div>
                    </div>
                </div>

                <!-- Suppliers Card -->
                <div class="col">
                    <div class="card h-100">
                        <img src="{{ asset('images/suppliers.jpg') }}" class="card-img-top" alt="Suppliers">
                        <div class="card-body">
                            <h5 class="card-title">Suppliers</h5>
                            <p class="card-text">Manage supplier information and relationships.</p>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary w-100">Manage Suppliers</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities Section -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card">
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

                <div class="col-md-4">
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
                                    <p class="mb-1">Status: {{ ucfirst($quotation->status) }}</p>
                                </a>
                                @empty
                                <div class="list-group-item">
                                    <p class="mb-0">No recent RFQs</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recent Invitations</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                @forelse($recentInvitations ?? [] as $invitation)
                                <a href="{{ route('supplier-invitations.show', $invitation->id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $invitation->company_name }}</h6>
                                        <small>{{ $invitation->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">
                                        Project: {{ $invitation->project->name }}<br>
                                        Status: {{ ucfirst($invitation->status) }}
                                    </p>
                                </a>
                                @empty
                                <div class="list-group-item">
                                    <p class="mb-0">No recent invitations</p>
                                </div>
                                @endforelse
                            </div>
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