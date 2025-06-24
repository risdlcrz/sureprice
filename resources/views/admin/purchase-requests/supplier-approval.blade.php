@if(
    $purchaseRequest->status === 'pending' &&
    auth()->user()->hasRole('supplier') &&
    $purchaseRequest->items->every(fn($item) => $item->preferred_supplier_id === auth()->user()->company?->id)
)
    <div class="card mt-4">
        <div class="card-header">
            <h4 class="card-title">Supplier Approval</h4>
        </div>
        <div class="card-body">
            @if(!$purchaseRequest->supplier_approved)
                <p>This request is pending your approval.</p>
                <form action="{{ route('purchase-requests.supplier.approve', $purchaseRequest) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Approve as Supplier
                    </button>
                </form>
            @else
                <p class="text-success"><i class="fas fa-check-circle"></i> You have approved this request.</p>
            @endif
        </div>
    </div>
@endif 