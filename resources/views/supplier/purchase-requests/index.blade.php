@extends('layouts.app')

@section('content')
<div class="container-fluid req-page">
    <x-page-header title="Purchase Requests" icon="file-invoice" subtitle="Requests that list you as the supplier. Approve them so a purchase order can be raised." />

    @php
        $waiting = $purchaseRequests->where('status', 'pending_supplier_approval')->where('supplier_approved', false)->count();
    @endphp
    <div class="req-kpis">
        <div class="req-kpi req-kpi--warning">
            <span class="req-kpi__icon"><i class="fas fa-hourglass-half" aria-hidden="true"></i></span>
            <span><span class="req-kpi__value">{{ $waiting }}</span><span class="req-kpi__label">Waiting for your approval</span></span>
        </div>
        <div class="req-kpi req-kpi--success">
            <span class="req-kpi__icon"><i class="fas fa-check-circle" aria-hidden="true"></i></span>
            <span><span class="req-kpi__value">{{ $purchaseRequests->where('status', 'approved')->count() }}</span><span class="req-kpi__label">Approved</span></span>
        </div>
        <div class="req-kpi req-kpi--neutral">
            <span class="req-kpi__icon"><i class="fas fa-list" aria-hidden="true"></i></span>
            <span><span class="req-kpi__value">{{ $purchaseRequests->count() }}</span><span class="req-kpi__label">All requests</span></span>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover req-table">
                <thead>
                    <tr>
                        <th>Request</th>
                        <th>Requested by</th>
                        <th>Created</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseRequests as $pr)
                        <tr>
                            <td><a href="{{ route('supplier.purchase-requests.show', $pr) }}" class="req-ref">{{ $pr->request_number }}</a></td>
                            <td>{{ $pr->requestedBy?->name ?? '—' }}</td>
                            <td>{{ $pr->created_at->format('M d, Y') }}<span class="req-sub">{{ $pr->created_at->format('H:i') }}</span></td>
                            <td><x-status-badge :status="$pr->status" /></td>
                            <td class="text-end">
                                @if($pr->status === 'pending_supplier_approval' && !$pr->supplier_approved)
                                    <a href="{{ route('supplier.purchase-requests.show', $pr) }}" class="btn btn-sm btn-success">Review &amp; approve</a>
                                @else
                                    <a href="{{ route('supplier.purchase-requests.show', $pr) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <x-empty-row cols="5">No purchase requests yet.</x-empty-row>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
@vite('resources/css/pages/requests.css')
@endpush
