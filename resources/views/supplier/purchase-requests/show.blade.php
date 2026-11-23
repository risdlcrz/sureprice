@extends('layouts.app')

@php
    $pr = $purchaseRequest;
    $itemCount = $pr->items?->count() ?? 0;
@endphp

@section('content')
<div class="container-fluid req-page">
    <x-page-header title="Purchase Request" icon="file-invoice">
        <a href="{{ route('supplier.purchase-requests.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
    </x-page-header>

    <div class="card mb-4">
        <div class="card-body">
            <div class="req-hero mb-4">
                <div>
                    <div class="req-hero__ref">Purchase request</div>
                    <h2 class="req-hero__title">{{ $pr->request_number }} <x-status-badge :status="$pr->status" /></h2>
                </div>
                <div class="req-hero__amount">
                    <strong>{{ $pr->total_amount ? '₱' . number_format($pr->total_amount, 2) : '—' }}</strong>
                    <span>{{ $itemCount }} {{ Str::plural('item', $itemCount) }} · estimated total</span>
                </div>
            </div>
            <dl class="req-meta">
                <div><dt>Requested by</dt><dd>{{ $pr->requestedBy?->name ?? '—' }}</dd></div>
                <div><dt>Created</dt><dd>{{ $pr->created_at->format('M d, Y H:i') }}</dd></div>
                @if($pr->admin_approved_at)
                    <div><dt>Admin approved</dt><dd>{{ $pr->admin_approved_at->format('M d, Y H:i') }}</dd></div>
                @endif
                @if($pr->supplier_approved_at)
                    <div><dt>You approved</dt><dd>{{ $pr->supplier_approved_at->format('M d, Y H:i') }}</dd></div>
                @endif
            </dl>
        </div>
    </div>

    @if($pr->status === 'pending_supplier_approval')
        @if(!$pr->supplier_approved)
            <div class="req-callout req-callout--warning mb-4">
                <div class="req-callout__text">
                    <i class="fas fa-hourglass-half" aria-hidden="true"></i>
                    <div><strong>Waiting for your approval.</strong><br>The admin has approved this request. Confirm you can supply the items below so a purchase order can be issued.</div>
                </div>
                <form action="{{ route('supplier.purchase-requests.approve', $pr) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Approve this purchase request?')"><i class="fas fa-check me-1"></i> Approve request</button>
                </form>
            </div>
        @else
            <div class="req-callout req-callout--success mb-4">
                <div class="req-callout__text">
                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                    <div><strong>You approved this request</strong>@if($pr->supplier_approved_at) on {{ $pr->supplier_approved_at->format('M d, Y H:i') }}@endif.</div>
                </div>
            </div>
        @endif
    @elseif($pr->status === 'approved')
        <div class="req-callout req-callout--success mb-4">
            <div class="req-callout__text">
                <i class="fas fa-check-circle" aria-hidden="true"></i>
                <div><strong>Fully approved.</strong> This request is ready for a purchase order.</div>
            </div>
        </div>
    @elseif($pr->status === 'pending_admin_approval')
        <div class="req-callout req-callout--info mb-4">
            <div class="req-callout__text">
                <i class="fas fa-clock" aria-hidden="true"></i>
                <div><strong>Pending admin approval.</strong> You'll be able to approve it once the admin has.</div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header"><h3 class="req-section-title"><i class="fas fa-boxes"></i> Items</h3></div>
        <div class="table-responsive">
            <table class="table req-table">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Description</th>
                        <th class="req-num">Quantity</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pr->items as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->material?->name ?? '—' }}</td>
                            <td class="req-wrap">{{ $item->description ?: '—' }}</td>
                            <td class="req-num">{{ $item->quantity ? number_format($item->quantity, 2) : '—' }} <span class="text-muted">{{ $item->unit }}</span></td>
                            <td class="req-wrap">{{ $item->notes ?: '—' }}</td>
                        </tr>
                    @empty
                        <x-empty-row cols="4">No items on this request.</x-empty-row>
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
