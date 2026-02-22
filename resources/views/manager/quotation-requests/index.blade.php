@extends('layouts.app')
@section('content')
<div class="container mt-4">
    <h1 class="h3 mb-4 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Quotations Management</h1>
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
                                <td><span class="badge bg-{{ $qr->status_color }}">{{ $qr->status_label }}</span></td>
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
                                <td><span class="badge bg-{{ $q->status_color }}">{{ ucfirst($q->status) }}</span></td>
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
                                <td><a href="#" class="btn btn-sm btn-primary">View</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No supplier quotations found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            @endif
        </div>
    </div>
</div>
@push('styles')
<style>
body { background: #f5f6f8 !important; }
.card {
    border: 1px solid #e8eaed;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.nav-link.active {
    background: #198754 !important;
    color: #fff !important;
    border-radius: 8px;
}
.nav-link:not(.active) {
    background: #f5f6f8;
    color: #1f2937;
    border-radius: 8px;
}
.manager-quotations-table thead th {
    background: #f5f6f8;
    color: #1f2937;
    font-weight: 600;
    font-size: 0.8125rem;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e8eaed;
}
.manager-quotations-table tbody td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e8eaed;
    font-size: 0.9375rem;
}
.manager-quotations-table tbody tr:hover { background: rgba(25, 135, 84, 0.04); }
.manager-quotations-table .btn-primary {
    background: #198754 !important;
    background-image: none !important;
    border: none;
    border-radius: 6px;
}
.manager-quotations-table .btn-primary:hover { background: #157347 !important; }
.table-responsive { border: 1px solid #e8eaed; border-radius: 8px; overflow: hidden; }
</style>
@endpush
@endsection 