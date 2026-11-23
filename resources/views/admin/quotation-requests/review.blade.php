@extends('layouts.app')

@php $qr = $quotationRequest; @endphp

@section('content')
<div class="container-fluid req-page">
    <x-page-header title="Review Quotation Request" icon="file-signature">
        <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back to notifications</a>
    </x-page-header>

    <div class="card mb-4">
        <div class="card-body">
            <div class="req-hero mb-4">
                <div>
                    <div class="req-hero__ref">Client quotation request</div>
                    <h2 class="req-hero__title">{{ $qr->request_number }} <x-status-badge :status="$qr->status" /></h2>
                </div>
            </div>
            <dl class="req-meta">
                <div><dt>Client</dt><dd>{{ $qr->user->name ?? 'User #' . $qr->user_id }}</dd></div>
                <div><dt>Submitted</dt><dd>{{ $qr->created_at->format('M d, Y H:i') }}</dd></div>
                <div><dt>Rooms</dt><dd>{{ $qr->rooms->count() }}</dd></div>
            </dl>
        </div>
    </div>

    @if(empty($rfqsSent) && auth()->user()->hasRole('manager'))
        <div class="req-callout req-callout--warning mb-4">
            <div class="req-callout__text">
                <i class="fas fa-paper-plane" aria-hidden="true"></i>
                <div><strong>Not sent to suppliers yet.</strong><br>Send an RFQ to every supplier of these materials to collect their prices.</div>
            </div>
            <form method="POST" action="{{ route('admin.quotation.send-rfq', ['id' => $qr->id]) }}">
                @csrf
                <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane me-1"></i> Send to all suppliers</button>
            </form>
        </div>
    @elseif(!empty($rfqsSent))
        <div class="req-callout req-callout--info mb-4">
            <div class="req-callout__text">
                <i class="fas fa-check" aria-hidden="true"></i>
                <div><strong>RFQs sent.</strong> Supplier offers appear in the table below as they come in.</div>
            </div>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header"><h3 class="req-section-title"><i class="fas fa-ruler-combined"></i> Rooms &amp; scope of work</h3></div>
        <div class="card-body">
            <x-requests.rooms :rooms="$qr->rooms" />
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="req-section-title"><i class="fas fa-boxes"></i> Materials &amp; supplier offers</h3></div>
        <div class="table-responsive">
            <table class="table req-table">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Scope</th>
                        <th>Material</th>
                        <th>Unit</th>
                        <th class="req-num">Base price</th>
                        <th>Supplier offers</th>
                    </tr>
                </thead>
                <tbody>
                    @php $shown = collect(); @endphp
                    @foreach($qr->rooms as $room)
                        @foreach($room->scopes as $scope)
                            @foreach($scope->scopeType?->materials ?? [] as $material)
                                @php $key = $room->id . '-' . $scope->scopeType->id . '-' . $material->id; @endphp
                                @continue($shown->contains($key))
                                @php
                                    $shown->push($key);
                                    $offers = collect($materialSupplierResponses[$material->id] ?? [])->sortBy('unit_price')->values();
                                @endphp
                                <tr>
                                    <td>{{ $room->name }}</td>
                                    <td>{{ $scope->scopeType->name ?? 'N/A' }}</td>
                                    <td class="fw-semibold">{{ $material->name }}</td>
                                    <td>{{ $material->unit }}</td>
                                    <td class="req-num">₱{{ number_format($material->base_price, 2) }}</td>
                                    <td class="req-wrap">
                                        @forelse($offers as $offer)
                                            <div class="req-offer @if($loop->first && $offers->count() > 1) is-best @endif">
                                                <span>{{ $offer['supplier_name'] }}@if($loop->first && $offers->count() > 1) <span class="badge bg-success ms-1">Lowest</span>@endif</span>
                                                <strong>₱{{ number_format($offer['unit_price'], 2) }}</strong>
                                            </div>
                                        @empty
                                            <span class="text-muted">No offers yet</span>
                                        @endforelse
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                    @if($shown->isEmpty())
                        <x-empty-row cols="6">No materials linked to these scopes.</x-empty-row>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
@vite('resources/css/pages/requests.css')
@endpush
