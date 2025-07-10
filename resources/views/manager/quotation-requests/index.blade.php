@extends('layouts.app')
@section('content')
<div class="container mt-4">
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
                                    @foreach($q->suppliers as $supplier)
                                        <span class="badge bg-info text-dark">{{ $supplier->company_name }}</span>
                                    @endforeach
                                </td>
                                <td><a href="#" class="btn btn-sm btn-primary">View</a></td>
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