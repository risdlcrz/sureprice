@extends('layouts.app')
@section('content')
<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Client Quotation Request #{{ $quotationRequest->request_number }}</h5>
            <span class="badge bg-{{ $quotationRequest->status === 'sent_to_suppliers' ? 'info' : 'warning' }}">
                {{ $quotationRequest->status === 'sent_to_suppliers' ? 'Sent to Suppliers' : ucfirst($quotationRequest->status) }}
            </span>
            <a href="{{ route('manager.quotations', ['tab' => 'client']) }}" class="btn btn-secondary">Back to List</a>
        </div>
        <div class="card-body">
            <h6>Client Information</h6>
            <ul>
                <li><strong>User ID:</strong> {{ $quotationRequest->user_id }}</li>
                <li><strong>Submitted At:</strong> {{ $quotationRequest->created_at->format('Y-m-d H:i') }}</li>
            </ul>
            @foreach($quotationRequest->rooms as $room)
                <div class="mb-3">
                    <strong>Room:</strong> {{ $room->name }}<br>
                    <strong>Dimensions:</strong> {{ $room->length }} × {{ $room->width }} × {{ $room->height }}<br>
                    <strong>Scopes:</strong>
                    <ul>
                        @foreach($room->scopes as $scope)
                            <li>{{ $scope->scopeType->name ?? '' }} ({{ $scope->scopeType->category ?? '' }})</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
            @if(!in_array($quotationRequest->status, ['sent_to_suppliers', 'proceeded']))
                <form method="POST" action="{{ route('manager.quotation-requests.send-to-suppliers', $quotationRequest->id) }}" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-success float-end">Send Quotation Request to All Suppliers</button>
                </form>
            @elseif($quotationRequest->status === 'sent_to_suppliers')
                <div class="alert alert-info text-center">This request has already been sent to all suppliers.</div>
            @endif
            <h6>Materials in this Quotation</h6>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Scope</th>
                        <th>Material</th>
                        <th>Unit</th>
                        <th>Base Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotationRequest->rooms as $room)
                        @foreach($room->scopes as $scope)
                            @if($scope->scopeType && $scope->scopeType->materials)
                                @foreach($scope->scopeType->materials as $material)
                                    <tr>
                                        <td>{{ $room->name }}</td>
                                        <td>{{ $scope->scopeType->name ?? '' }}</td>
                                        <td>{{ $material->name }}</td>
                                        <td>{{ $material->unit }}</td>
                                        <td>₱{{ number_format($material->base_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            @if($quotationRequest->status === 'proceeded' && !$quotationRequest->materialRequest)
                <a href="{{ route('material-requests.create', ['quotation_id' => $quotationRequest->id]) }}" class="btn btn-primary mt-3">Create Material Request</a>
            @endif
            <div class="card mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Chosen Suppliers</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Room</th>
                                    <th>Scope</th>
                                    <th>Material</th>
                                    <th>Chosen Supplier</th>
                                    <th>Quoted Price</th>
                                    <th>Badges</th>
                                    <th>Contact</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selectedSuppliers as $materialId => $supplierId)
                                    @php
                                        $material = null;
                                        $supplier = null;
                                        $roomName = null;
                                        $scopeName = null;
                                        $price = null;
                                        $badges = [];
                                        $contact = null;
                                        foreach ($rfqs as $rfq) {
                                            $mat = $rfq->materials->firstWhere('id', $materialId);
                                            if ($mat) {
                                                $material = $mat;
                                                $supplier = $rfq->suppliers->firstWhere('id', $supplierId);
                                                $roomName = $mat->pivot->room_name ?? null;
                                                $scopeName = $mat->pivot->scope_name ?? null;
                                                $price = $mat->pivot->quoted_price ?? null;
                                                $badges = $mat->pivot->badges ?? [];
                                                $contact = $supplier->contact_number ?? null;
                                                break;
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $roomName ?? '-' }}</td>
                                        <td>{{ $scopeName ?? '-' }}</td>
                                        <td>{{ $material ? $material->name : 'Material #'.$materialId }}</td>
                                        <td>{{ $supplier ? $supplier->company_name : 'N/A' }}</td>
                                        <td>
                                            @if($price)
                                                ₱{{ number_format($price, 2) }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($badges))
                                                @foreach($badges as $badge)
                                                    <span class="badge @if($badge=='Cheapest') badge-cheapest @elseif($badge=='Best Delivery') badge-delivery @elseif($badge=='Least Defects') badge-defects @elseif($badge=='Overall Best') badge-overall @endif" data-bs-toggle="tooltip" title="{{ $badge }}">{{ $badge }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($contact)
                                                <span class="text-nowrap"><i class="fas fa-phone-alt me-1"></i>{{ $contact }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
$(function () {
    $('[data-bs-toggle="tooltip"]').tooltip();
});
@endpush