@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">Client Quotation Request #{{ $quotationRequest->request_number }}</h4>
                        <span class="badge bg-{{ $quotationRequest->status_color }}">{{ $quotationRequest->status_label }}</span>
                    </div>
                    <a href="{{ route('admin.notification') }}" class="btn btn-secondary">Back to Notifications</a>
                </div>
                <div class="card-body">
                    <h5>Client Information</h5>
                    <ul>
                        <li><strong>User ID:</strong> {{ $quotationRequest->user_id }}</li>
                        <li><strong>Submitted At:</strong> {{ $quotationRequest->created_at->format('M d, Y H:i') }}</li>
                    </ul>
                    <hr>
                    <h5>Rooms & Scopes</h5>
                    @foreach($quotationRequest->rooms as $room)
                        <div class="mb-3">
                            <strong>Room:</strong> {{ $room->name }}<br>
                            <strong>Dimensions:</strong> {{ $room->length }} x {{ $room->width }} x {{ $room->height }}<br>
                            <strong>Scopes:</strong>
                            <ul>
                                @foreach($room->scopes as $scope)
                                    <li>{{ $scope->scopeType->name ?? 'N/A' }} ({{ $scope->scopeType->category ?? '' }})</li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                    <hr>
                    <h5>Materials in this Quotation</h5>
                    @if(empty($rfqsSent))
                        <form method="POST" action="{{ route('admin.quotation.send-rfq', ['id' => $quotationRequest->id]) }}">
                            @csrf
                            <div class="text-end mb-3">
                                <button type="submit" class="btn btn-success btn-lg">Send Quotation Request to All Suppliers</button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info mb-3">RFQs have already been sent to all relevant suppliers.</div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Room</th>
                                    <th>Scope</th>
                                    <th>Material</th>
                                    <th>Unit</th>
                                    <th>Base Price</th>
                                    <th>Supplier Offers</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $shown = collect(); @endphp
                                @foreach($quotationRequest->rooms as $room)
                                    @foreach($room->scopes as $scope)
                                        @if($scope->scopeType && $scope->scopeType->materials)
                                            @foreach($scope->scopeType->materials as $material)
                                                @php $key = $room->id.'-'.$scope->scopeType->id.'-'.$material->id; @endphp
                                                @if(!$shown->contains($key))
                                                    <tr>
                                                        <td>{{ $room->name }}</td>
                                                        <td>{{ $scope->scopeType->name ?? 'N/A' }}</td>
                                                        <td>{{ $material->name }}</td>
                                                        <td>{{ $material->unit }}</td>
                                                        <td>₱{{ number_format($material->base_price, 2) }}</td>
                                                        <td>
                                                            @php
                                                                $offers = $materialSupplierResponses[$material->id] ?? [];
                                                            @endphp
                                                            @if(count($offers) > 0)
                                                                <ul class="list-unstyled mb-0">
                                                                    @foreach($offers as $offer)
                                                                        <li>
                                                                            <span class="fw-bold">{{ $offer['supplier_name'] }}</span>:
                                                                            <span class="badge bg-primary">₱{{ number_format($offer['unit_price'], 2) }}</span>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <span class="text-muted">No supplier offers</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @php $shown->push($key); @endphp
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const recommendBtn = document.getElementById('recommendAllBtn');
    const recommendModal = new bootstrap.Modal(document.getElementById('recommendModal'));
    const applyRecommendBtn = document.getElementById('applyRecommendBtn');
    recommendBtn.addEventListener('click', function() {
        recommendModal.show();
    });
    applyRecommendBtn.addEventListener('click', function() {
        const category = document.getElementById('recommendCategory').value;
        fetch(`{{ url('admin/quotation-requests/' . $quotationRequest->id . '/recommend-suppliers') }}?category=${category}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.recommendations) {
                    for (const materialId in data.recommendations) {
                        const supplierId = data.recommendations[materialId];
                        const select = document.querySelector(`select[name='selected_suppliers[${materialId}]']`);
                        if (select) select.value = supplierId;
                    }
                }
                recommendModal.hide();
            });
    });
});
</script>
@endpush 