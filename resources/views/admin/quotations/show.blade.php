@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">{{ $quotation->rfq_number }}</h4>
                        <span class="badge badge-{{ $quotation->status_color }}">
                            {{ ucfirst($quotation->status) }}
                        </span>
                    </div>
                    <div class="btn-group">
                        @if(in_array($quotation->status, ['draft', 'sent']))
                        <a href="{{ route('quotations.edit', $quotation->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit RFQ
                        </a>
                        @endif
                        @if($quotation->status == 'draft')
                        <button type="button" class="btn btn-success send-quotation" data-id="{{ $quotation->id }}">
                            <i class="fas fa-paper-plane"></i> Send to Suppliers
                        </button>
                        @endif
                        <a href="{{ route('quotations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Quotation Details -->
                    <div class="section mb-4">
                        <h5 class="section-title">Quotation Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th width="30%">RFQ Number:</th>
                                        <td>{{ $quotation->rfq_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Due Date:</th>
                                        <td>{{ $quotation->due_date->format('M d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <span class="badge badge-{{ $quotation->status_color }}">
                                                {{ ucfirst($quotation->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th width="30%">Payment Terms:</th>
                                        <td>{{ $quotation->payment_terms ?? 'Not specified' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Delivery Terms:</th>
                                        <td>{{ $quotation->delivery_terms ?? 'Not specified' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Validity Period:</th>
                                        <td>{{ $quotation->validity_period ?? 'Not specified' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Request Information (Conditional) -->
                    @if($quotation->purchase_request_id)
                    <div class="section mb-4">
                        <h5 class="section-title">Purchase Request Information</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-sm">
                                    <tr>
                                        <th width="20%">PR Number:</th>
                                        <td>PR-{{ $quotation->purchaseRequest->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Department:</th>
                                        <td>{{ $quotation->purchaseRequest->department }}</td>
                                    </tr>
                                    <tr>
                                        <th>Purpose:</th>
                                        <td>{{ $quotation->purchaseRequest->purpose ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Materials -->
                    <div class="section mb-4">
                        <h5 class="section-title">Materials for Quotation</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        @if(!$quotation->purchase_request_id)
                                        <th>Specifications</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($quotation->purchase_request_id)
                                        @foreach($quotation->purchaseRequest->items as $item)
                                        <tr>
                                            <td>{{ $item->material->name }} ({{ $item->material->code }})</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->material->unit }}</td>
                                        </tr>
                                        @endforeach
                                    @else
                                        @foreach($quotation->materials as $material)
                                        <tr>
                                            <td>{{ $material->name }} ({{ $material->code }})</td>
                                            <td>{{ $material->pivot->quantity }}</td>
                                            <td>{{ $material->unit }}</td>
                                            <td>{{ $material->pivot->specifications ?? 'N/A' }}</td>
                                        </tr>
                                        @endforeach
                                    @endif
                                    @if(($quotation->purchase_request_id && $quotation->purchaseRequest->items->isEmpty()) || (!$quotation->purchase_request_id && $quotation->materials->isEmpty()))
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No materials attached to this quotation.</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Invited Suppliers -->
                    <div class="section mb-4">
                        <h5 class="section-title">Invited Suppliers</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Supplier</th>
                                        <th>Contact</th>
                                        <th>Notes</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quotation->suppliers as $supplier)
                                    <tr>
                                        <td>{{ $supplier->company_name }}</td>
                                        <td>
                                            {{ $supplier->email }}<br>
                                            {{ $supplier->phone }}
                                        </td>
                                        <td>{{ $supplier->pivot->notes }}</td>
                                        <td>
                                            @php
                                                $response = $quotation->responses->where('supplier_id', $supplier->id)->first();
                                            @endphp
                                            @if($response)
                                                <span class="badge badge-success">Responded</span>
                                            @else
                                                <span class="badge badge-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Supplier Responses -->
                    @if($quotation->responses->count() > 0)
                    <div class="section mb-4">
                        <h5 class="section-title">Supplier Responses</h5>
                        @foreach($quotation->responses as $response)
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ $response->supplier->company_name }}</h6>
                                    <span class="badge badge-{{ $response->status_color }}">
                                        {{ ucfirst($response->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm">
                                            <tr>
                                                <th width="30%">Total Amount:</th>
                                                <td>₱{{ number_format($response->total_amount, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Payment Terms:</th>
                                                <td>{{ $response->payment_terms }}</td>
                                            </tr>
                                            <tr>
                                                <th>Delivery Terms:</th>
                                                <td>{{ $response->delivery_terms }}</td>
                                            </tr>
                                            <tr>
                                                <th>Validity Period:</th>
                                                <td>{{ $response->validity_period }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Notes</h6>
                                        <p>{{ $response->notes ?? 'No notes provided' }}</p>
                                    </div>
                                </div>

                                <h6 class="mt-3">Material Prices</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Material</th>
                                                <th>Quantity</th>
                                                <th>Unit Price</th>
                                                <th>Total Price</th>
                                                <th>Specifications</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($response->items as $item)
                                            <tr>
                                                <td>{{ $item->material->name }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                                <td>₱{{ number_format($item->total_price, 2) }}</td>
                                                <td>{{ $item->specifications }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @if($response->attachments->count() > 0)
                                <h6 class="mt-3">Attachments</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>File Name</th>
                                                <th>Size</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($response->attachments as $attachment)
                                            <tr>
                                                <td>{{ $attachment->file_name }}</td>
                                                <td>{{ $attachment->formatted_size }}</td>
                                                <td>
                                                    <a href="{{ route('quotations.response.attachment.download', $attachment->id) }}" 
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Notes -->
                    @if($quotation->notes)
                    <div class="section mb-4">
                        <h5 class="section-title">Notes</h5>
                        <p>{{ $quotation->notes }}</p>
                    </div>
                    @endif

                    <!-- Attachments -->
                    @if($quotation->attachments->count() > 0)
                    <div class="section">
                        <h5 class="section-title">RFQ Attachments</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>File Name</th>
                                        <th>Size</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quotation->attachments as $attachment)
                                    <tr>
                                        <td>{{ $attachment->original_name }}</td>
                                        <td>{{ $attachment->formatted_size }}</td>
                                        <td>
                                            <a href="{{ route('quotations.attachment.download', $attachment->id) }}" 
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    @if($quotation->awarded_supplier_id && $quotation->awarded_amount)
                        <div class="mb-2">
                            <strong>Awarded Supplier:</strong> {{ optional($quotation->suppliers->find($quotation->awarded_supplier_id))->company_name ?? 'N/A' }}<br>
                            <strong>Awarded Amount:</strong> ₱{{ number_format($quotation->awarded_amount, 2) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Send Confirmation Modal -->
<div class="modal fade" id="sendModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Request for Quotation</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to send this RFQ to all selected suppliers?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSend">Send</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.section-title {
    border-bottom: 2px solid #eee;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
}
.badge {
    font-size: 0.9em;
}
.table th {
    background-color: #f8f9fa;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Send quotation functionality
    const sendModal = document.getElementById('sendModal');
    const confirmSend = document.getElementById('confirmSend');
    let quotationId = '';

    document.querySelectorAll('.send-quotation').forEach(btn => {
        btn.addEventListener('click', function() {
            quotationId = this.dataset.id;
            $(sendModal).modal('show');
        });
    });

    confirmSend.addEventListener('click', function() {
        fetch(`/api/quotations/${quotationId}/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Failed to send RFQ');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error sending RFQ');
        });
    });
});
</script>
@endpush
@endsection 