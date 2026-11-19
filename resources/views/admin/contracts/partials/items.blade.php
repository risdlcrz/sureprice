{{-- Contract Items (contract details page). Param: $contract --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Contract Items</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="contractItemsTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Unit</th>
                        <th>Unit Cost</th>
                        <th>Quantity</th>
                        <th>Total Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contract->items as $item)
                        <tr>
                            <td data-item-name>{{ $item->material_name }}</td>
                            <td data-item-unit>{{ $item->unit }}</td>
                            <td data-item-unit-cost>₱{{ number_format($item->amount, 2) }}</td>
                            <td data-item-quantity>{{ number_format($item->quantity, 2) }}</td>
                            <td data-item-total-cost>₱{{ number_format($item->total, 2) }}</td>
                        </tr>
                    @empty
                        <x-empty-row cols="5">No items found.</x-empty-row>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total Materials Cost:</strong></td>
                        <td>
                            @if($contract->discount_amount && $contract->final_amount)
                                <span class="text-decoration-line-through text-muted">₱{{ number_format($contract->materials_cost, 2) }}</span><br>
                                <span>₱{{ number_format($contract->final_amount, 2) }}</span>
                            @else
                                ₱{{ number_format($contract->materials_cost, 2) }}
                            @endif
                        </td>
                    </tr>
                    @if($contract->discount_amount && $contract->final_amount)
                    <tr>
                        <td colspan="4" class="text-end"><strong>Supplier Discount:</strong></td>
                        <td>{{ ucfirst($contract->discount_type) }}
                            @if($contract->discount_percentage)
                                ({{ rtrim(rtrim(number_format($contract->discount_percentage,2), '0'), '.') }}%)
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Final Amount After Discount:</strong></td>
                        <td>₱{{ number_format($contract->final_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total Labor Cost:</strong></td>
                        <td>₱{{ number_format($contract->labor_cost, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Grand Total:</strong></td>
                        <td>₱{{ number_format(($contract->final_amount ?? $contract->materials_cost) + $contract->labor_cost, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
