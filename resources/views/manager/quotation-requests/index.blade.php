@extends('layouts.app')

@php
    $sentStatuses = ['sent_to_suppliers', 'reviewed'];
    $awaiting = $quotationRequests->reject(fn ($qr) => in_array($qr->status, [...$sentStatuses, 'proceeded'], true))->count();
    $sent = $quotationRequests->whereIn('status', $sentStatuses)->count();
    $proceeded = $quotationRequests->where('status', 'proceeded')->count();
@endphp

@section('content')
<div class="container-fluid req-page">
    <x-page-header title="Quotation Requests" icon="file-signature" subtitle="Client requests for a quotation, and the RFQs sent to suppliers for them." />

    <div class="req-kpis">
        <a href="?tab=client" class="req-kpi req-kpi--warning">
            <span class="req-kpi__icon"><i class="fas fa-inbox" aria-hidden="true"></i></span>
            <span><span class="req-kpi__value">{{ $awaiting }}</span><span class="req-kpi__label">New — not yet sent to suppliers</span></span>
        </a>
        <a href="?tab=client" class="req-kpi req-kpi--info">
            <span class="req-kpi__icon"><i class="fas fa-paper-plane" aria-hidden="true"></i></span>
            <span><span class="req-kpi__value">{{ $sent }}</span><span class="req-kpi__label">Sent to suppliers</span></span>
        </a>
        <a href="?tab=client" class="req-kpi req-kpi--success">
            <span class="req-kpi__icon"><i class="fas fa-handshake" aria-hidden="true"></i></span>
            <span><span class="req-kpi__value">{{ $proceeded }}</span><span class="req-kpi__label">Client proceeded</span></span>
        </a>
        <a href="?tab=supplier" class="req-kpi req-kpi--neutral">
            <span class="req-kpi__icon"><i class="fas fa-truck-loading" aria-hidden="true"></i></span>
            <span><span class="req-kpi__value">{{ $supplierQuotations->count() }}</span><span class="req-kpi__label">Open supplier RFQs</span></span>
        </a>
    </div>

    <div class="card">
        <div class="card-header pb-0">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link @if($activeTab === 'client') active @endif" href="?tab=client" @if($activeTab === 'client') aria-current="page" @endif>
                        Client requests <span class="badge bg-light ms-1">{{ $quotationRequests->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($activeTab === 'supplier') active @endif" href="?tab=supplier" @if($activeTab === 'supplier') aria-current="page" @endif>
                        Supplier RFQs <span class="badge bg-light ms-1">{{ $supplierQuotations->count() }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="table-responsive">
            @if($activeTab === 'client')
                <table class="table table-hover req-table">
                    <thead>
                        <tr>
                            <th>Request</th>
                            <th>Client</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotationRequests as $qr)
                            <tr>
                                <td><a href="{{ route('manager.quotation-requests.view', $qr->id) }}" class="req-ref">{{ $qr->request_number }}</a></td>
                                <td>{{ $qr->user->name ?? '—' }}</td>
                                <td>{{ $qr->created_at->format('M d, Y') }}<span class="req-sub">{{ $qr->created_at->format('H:i') }}</span></td>
                                <td><x-status-badge :status="$qr->status" /></td>
                                <td class="text-end">
                                    <a href="{{ route('manager.quotation-requests.view', $qr->id) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                </td>
                            </tr>
                        @empty
                            <x-empty-row cols="5">No client quotation requests yet.</x-empty-row>
                        @endforelse
                    </tbody>
                </table>
            @else
                <table class="table table-hover req-table">
                    <thead>
                        <tr>
                            <th>RFQ</th>
                            <th>Purchase request</th>
                            <th>Due</th>
                            <th>Responses</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplierQuotations as $q)
                            @php $responded = $q->responses->where('status', 'submitted')->pluck('supplier')->filter(); @endphp
                            <tr>
                                <td><a href="{{ route('quotations.show', $q) }}" class="req-ref">{{ $q->rfq_number }}</a></td>
                                <td>{{ $q->purchaseRequest->pr_number ?? $q->purchaseRequest->request_number ?? '—' }}</td>
                                <td>
                                    @if($q->due_date)
                                        {{ $q->due_date->format('M d, Y') }}
                                        @if($q->due_date->isPast())<span class="req-sub text-danger">Overdue</span>@endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="req-wrap">
                                    @forelse($responded as $supplier)
                                        <span class="badge bg-light">{{ $supplier->company_name }}</span>
                                    @empty
                                        <span class="text-muted">No responses yet</span>
                                    @endforelse
                                </td>
                                <td><x-status-badge :status="$q->status" /></td>
                                <td class="text-end">
                                    <a href="{{ route('quotations.show', $q) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                </td>
                            </tr>
                        @empty
                            <x-empty-row cols="6">No open supplier RFQs.</x-empty-row>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
@vite('resources/css/pages/requests.css')
@endpush
