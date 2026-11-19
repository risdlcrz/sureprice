{{-- Scope of Work (contract details page). Param: $contract --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Scope of Work</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-12">
                <h6>Project Timeline</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="review-item">
                            <span class="review-label">Project Timeline:</span>
                            <span class="review-value">
                                {{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('M d, Y') : '-' }} to 
                                {{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('M d, Y') : '-' }}
                                @if($contract->estimated_days)
                                    ({{ $contract->estimated_days }} days)
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h6>Rooms & Work Categories</h6>
                @if($contract->rooms && $contract->rooms->count())
                    @foreach($contract->rooms as $room)
                        <div class="room-section mb-4">
                            <h6>{{ $room->name }}</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Dimensions:</strong> {{ $room->length }}m x {{ $room->width }}m (Area: {{ $room->area }}㎡)</p>
                                </div>
                                <div class="col-md-6">
                                    <!--<p><strong>Total Cost:</strong> ₱{{ number_format(($room->materials_cost ?? 0) + ($room->labor_cost ?? 0), 2) }}</p>-->
                                </div>
                            </div>
                            @if($room->scopeTypes && $room->scopeTypes->count())
                                <div class="scope-types mt-2">
                                    <strong>Work Categories:</strong>
                                    <ul class="list-unstyled">
                                        @foreach($room->scopeTypes as $scope)
                                            <li>
                                                <i class="fas fa-check-circle text-success"></i>
                                                {{ $scope->name }} ({{ $scope->category }})
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @elseif($contract->quotationRequest && $contract->quotationRequest->rooms && $contract->quotationRequest->rooms->count())
                    @foreach($contract->quotationRequest->rooms as $room)
                        <div class="room-section mb-4">
                            <h6>{{ $room->name }}</h6>
                            <!-- Add more details if needed -->
                        </div>
                    @endforeach
                @else
                    <p class="text-center">No rooms defined for this contract.</p>
                @endif
                <h6>Description</h6>
                <p>{{ $contract->scope_description }}</p>
            </div>
        </div>
    </div>
</div>
