@extends('layouts.app')
@section('content')
<div class="container mt-4">
    <h1 class="h3 mb-4 text-center page-title">Quotations Management</h1>
    <div class="card">
        <div class="card-header pb-0">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link @if($activeTab === 'client') active @endif" href="?tab=client">Client Quotation Requests</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($activeTab === 'supplier') active @endif" href="?tab=supplier">Supplier Quotations Sent</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            @if($activeTab === 'client')
                <h5 class="text-muted mb-3" style="font-size:0.9375rem;">All Client Quotation Requests</h5>
                <div class="table-responsive">
                <table class="table table-hover manager-quotations-table">
                    <thead>
                        <tr>
                            <th>Request #</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotationRequests as $qr)
                            <tr>
                                <td>{{ $qr->request_number }}</td>
                                <td>{{ $qr->user->name ?? 'N/A' }}</td>
                                <td><x-status-badge :status="$qr->status" /></td>
                                <td>{{ $qr->created_at->format('Y-m-d H:i') }}</td>
                                <td><a href="{{ route('manager.quotation-requests.view', $qr->id) }}" class="btn btn-sm btn-primary">View</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No client quotation requests found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            @else
                <h5 class="text-muted mb-3" style="font-size:0.9375rem;">All Supplier Quotations Sent</h5>
                <div class="table-responsive">
                <table class="table table-hover manager-quotations-table">
                    <thead>
                        <tr>
                            <th>RFQ #</th>
                            <th>Purchase Request</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Suppliers</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplierQuotations as $q)
                            <tr>
                                <td>{{ $q->rfq_number }}</td>
                                <td>{{ $q->purchaseRequest->pr_number ?? '-' }}</td>
                                <td><x-status-badge :status="$q->status" /></td>
                                <td>{{ $q->due_date ? $q->due_date->format('Y-m-d') : '-' }}</td>
                                <td>
                                    @php
                                        $respondedSuppliers = $q->responses->where('status', 'submitted')->pluck('supplier')->filter();
                                    @endphp
                                    @forelse($respondedSuppliers as $supplier)
                                        <span class="badge bg-info text-dark">{{ $supplier->company_name }}</span>
                                    @empty
                                        <span class="text-muted">No responses yet</span>
                                    @endforelse
                                </td>
                                <td><a href="{{ route('quotations.show', $q) }}" class="btn btn-sm btn-primary">View</a></td>
                            </tr>
                        @empty
                            <x-empty-row cols="6">No supplier quotations found.</x-empty-row>
                        @endforelse
                    </tbody>
                </table>
                </div>
            @endif
        </div>
    </div>
</div>
@push('styles')
@vite('resources/css/pages/manager-quotation-requests-index.css')
@endpush
@endsection 