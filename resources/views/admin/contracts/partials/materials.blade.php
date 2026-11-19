{{-- Scope, Materials, and Suppliers (contract details page). Param: $contract --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Scope, Materials, and Suppliers</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Scope of Work</th>
                        <th>Material</th>
                        <th>Supplier</th>
                    </tr>
                </thead>
                <tbody>
                    @if($contract->items && $contract->items->count())
                        @foreach($contract->items as $item)
                            <tr>
                                <td>{{ $item->room->name ?? '-' }}</td>
                                <td>{{ $item->scope->scope_name ?? '-' }}</td>
                                <td>{{ $item->material_name }}</td>
                                <td>{{ $item->supplier_name ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    @elseif($contract->quotationRequest && $contract->quotationRequest->rooms && $contract->quotationRequest->rooms->count())
                        @foreach($contract->quotationRequest->rooms as $room)
                            @foreach($room->scopes as $scope)
                                @if(is_array($scope->selected_materials))
                                    @foreach($scope->selected_materials as $material)
                                        <tr>
                                            <td>{{ $room->name }}</td>
                                            <td>{{ $scope->scope_name }}</td>
                                            <td>{{ $material['name'] ?? $material }}</td>
                                            <td>{{ $material['supplier'] ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        @endforeach
                    @else
                        <tr><td colspan="4">No rooms or materials found for this contract.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
