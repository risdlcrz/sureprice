@extends('layouts.app')
@section('content')
<div class="container mt-4">
    <h1 class="h3 mb-4 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Quotations Management</h1>
    <div class="card">
        <div class="card-header pb-0">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link @if(request('tab', 'client') === 'client') active @endif" href="?tab=client">Client Quotation Requests</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(request('tab') === 'supplier') active @endif" href="?tab=supplier">Supplier Quotations Sent</a>
                </li>
            </ul>
                </div>
                <div class="card-body">
            @php $activeTab = request('tab', 'client'); @endphp
            @if($activeTab === 'client')
                <h5>All Client Quotation Requests</h5>
                <table class="table table-bordered table-hover">
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
                                                @else
                <h5>All Supplier Quotations Sent</h5>
                <table class="table table-bordered table-hover">
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
                                <td><a href="{{ route('quotations.show', $q->id) }}" class="btn btn-sm btn-primary">View</a></td>
                                </tr>
                        @empty
                            <tr><td colspan="6">No supplier quotations found.</td></tr>
                        @endforelse
                            </tbody>
                        </table>
            @endif
        </div>
    </div>
</div>
@endsection
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body, .container {
    background: #f5f6f8 !important;
    font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
}
.card {
    border: none;
    border-radius: 1.25rem;
    box-shadow: 0 8px 32px 0 rgba(44,62,80,0.10), 0 1.5px 6px rgba(44,62,80,0.04);
    margin-bottom: 2rem;
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(8px);
    transition: box-shadow 0.2s, background 0.2s;
}
.card:hover {
    box-shadow: 0 16px 48px 0 rgba(44,62,80,0.16), 0 2px 8px rgba(44,62,80,0.08);
    background: rgba(255,255,255,0.97);
}
.card-header {
    background: transparent;
    border-bottom: none;
    padding-bottom: 0.5rem;
}
.nav-tabs {
    border-bottom: none;
    gap: 1rem;
}
.nav-link {
    font-weight: 600;
    color: #198754;
    border: none;
    border-radius: 2rem 2rem 0 0;
    background: #f8fafc;
    padding: 0.7em 2em;
    margin-bottom: -2px;
    transition: background 0.2s, color 0.2s;
}
.nav-link.active {
    background: #198754 !important;
    color: #fff !important;
}
.table th {
    font-weight: 600;
    color: #1f2937;
    background: #f5f6f8;
    border-bottom: 1px solid #e8eaed;
    padding: 0.75rem 1rem;
}
.table td { padding: 0.75rem 1rem; border-bottom: 1px solid #e8eaed; }
.table-hover tbody tr:hover {
    background: rgba(25, 135, 84, 0.04);
}
.table td, .table th {
    vertical-align: middle;
}
.badge {
    font-size: 0.95em;
    padding: 0.5em 1em;
    border-radius: 0.7em;
    font-weight: 600;
    letter-spacing: 0.01em;
    box-shadow: 0 1px 4px #38b6ff22;
}
.btn-primary {
    background: #198754 !important;
    background-image: none !important;
    color: #fff !important;
    border: none;
    font-weight: 600;
    border-radius: 8px;
    padding: 0.5em 1.25em;
}
.btn-primary:hover {
    background: #157347 !important;
    color: #fff;
}
</style>
@endpush 