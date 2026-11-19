@extends('layouts.app')

@section('content')
    @php
        $payment = $purchaseOrder->payments->last();
    @endphp
    <div class="container">
        <x-page-header title="Purchase Order Details">
            @if($purchaseOrder->isEditable())
                    <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endif
                @if($purchaseOrder->canComplete())
                    <button type="button" class="btn btn-success" 
                            data-bs-toggle="modal" 
                            data-bs-target="#completePurchaseOrderModal"
                            data-po-id="{{ $purchaseOrder->id }}">
                        <i class="fas fa-check"></i> Complete Order
                    </button>
                @endif
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
        </x-page-header>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Purchase Order Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>PO Number:</strong> {{ $purchaseOrder->po_number }}</p>
                                <p><strong>Contract:</strong> 
                                    @if($purchaseOrder->contract)
                                        {{ $purchaseOrder->contract->contract_number ?? 'N/A' }} - {{ $purchaseOrder->contract->name ?? $purchaseOrder->contract->title ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                                <p><strong>Purchase Request:</strong> {{ $purchaseOrder->purchaseRequest->pr_number }}</p>
                                <p><strong>Supplier:</strong> {{ $purchaseOrder->supplier->company_name }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status:</strong> 
                                    <x-status-badge :status="$purchaseOrder->status" />
                                </p>
                                <p><strong>Delivery Date:</strong> {{ $purchaseOrder->delivery_date->format('M d, Y') }}</p>
                                <p><strong>Payment Terms:</strong> {{ $purchaseOrder->payment_terms }}</p>
                                <p><strong>Shipping Terms:</strong> {{ $purchaseOrder->shipping_terms }}</p>
                            </div>
                        </div>

                        @if($purchaseOrder->notes)
                            <div class="mt-3">
                                <strong>Notes:</strong>
                                <p class="mb-0">{{ $purchaseOrder->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Items</h5>
                    </div>
                    <div class="card-body">
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
                                    @foreach($purchaseOrder->items as $item)
                                        <tr>
                                            <td>{{ $item->material->name }}</td>
                                            <td>{{ number_format($item->quantity, 2) }} {{ $item->material->unit }}</td>
                                            <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                            <td>₱{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                                            <td>{{ $item->specifications ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                        <td colspan="2"><strong>₱{{ number_format($purchaseOrder->total_amount, 2) }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Status Updates</h5>
                    </div>
                    <div class="card-body">
                        @if(in_array($purchaseOrder->status, ['draft', 'pending']))
                            <form action="{{ route('purchase-orders.update-status', $purchaseOrder) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="status" class="form-label">Update Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="">Select Status</option>
                                        @if($purchaseOrder->status === 'draft')
                                            <option value="pending">Submit for Approval</option>
                                        @endif
                                        @if($purchaseOrder->status === 'pending')
                                            <option value="approved">Approve</option>
                                            <option value="rejected">Reject</option>
                                        @endif
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Status</button>
                            </form>
                        @else
                            <p class="mb-0">No status updates available for {{ strtolower($purchaseOrder->status) }} purchase orders.</p>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Timeline</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-0">Created</h6>
                                    <small>{{ $purchaseOrder->created_at->format('M d, Y H:i') }}</small>
                                </div>
                            </div>
                            @if($purchaseOrder->updated_at->gt($purchaseOrder->created_at))
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-0">Last Updated</h6>
                                        <small>{{ $purchaseOrder->updated_at->format('M d, Y H:i') }}</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Delivery Workflow</h5>
                    </div>
                    <div class="card-body">
                        <ul class="timeline mb-3">
                            <li class="{{ $purchaseOrder->status === 'confirmed' || $purchaseOrder->status === 'shipping' || $purchaseOrder->status === 'delivered' || $purchaseOrder->status === 'completed' ? 'active' : '' }}">Confirmed</li>
                            <li class="{{ $purchaseOrder->status === 'shipping' || $purchaseOrder->status === 'delivered' || $purchaseOrder->status === 'completed' ? 'active' : '' }}">Shipping</li>
                            <li class="{{ $purchaseOrder->status === 'delivered' || $purchaseOrder->status === 'completed' ? 'active' : '' }}">Received</li>
                            <li class="{{ $purchaseOrder->status === 'completed' ? 'active' : '' }}">Completed</li>
                        </ul>
                        @if((auth()->user()->hasRole('warehousing') || auth()->user()->isAdmin()) && $purchaseOrder->canReceive())
                            <form method="POST" action="{{ route('purchase-orders.mark-received', $purchaseOrder->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-success">Mark as Received</button>
                            </form>
                        @endif
                        @if($purchaseOrder->shipping_note)
                            <div class="mt-3">
                                <strong>Shipping Note:</strong>
                                <p>{{ $purchaseOrder->shipping_note }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @vite('resources/css/pages/admin-purchase-orders-show.css')

    @include('admin.purchase-orders.partials.complete-modal')


    {{-- Payment to the supplier: admin/finance submit it, the supplier verifies it on their own PO page --}}
    @php
        $canPay = (auth()->user()->isAdmin() || auth()->user()->hasRole('finance'))
            && in_array($purchaseOrder->status, ['pending_payment', 'approved'], true)
            && (!$payment || $payment->status === 'rejected');
    @endphp
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Supplier Payment</span>
            @if($canPay)
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#submitPaymentModal">
                    <i class="fas fa-money-check-alt me-1"></i>{{ $payment ? 'Resubmit Payment' : 'Submit Payment to Supplier' }}
                </button>
            @endif
        </div>
        <div class="card-body">
            @if($payment)
                <dl class="row mb-0">
                    <dt class="col-sm-3">Status</dt><dd class="col-sm-9"><x-status-badge :status="$payment->status" /></dd>
                    <dt class="col-sm-3">Amount</dt><dd class="col-sm-9">₱{{ number_format($payment->amount, 2) }}</dd>
                    <dt class="col-sm-3">Method</dt><dd class="col-sm-9">{{ \App\Support\Status::label($payment->payment_method) }}</dd>
                    <dt class="col-sm-3">Reference #</dt><dd class="col-sm-9">{{ $payment->admin_reference_number }}</dd>
                    <dt class="col-sm-3">Date Paid</dt><dd class="col-sm-9">{{ $payment->admin_paid_date }}</dd>
                    <dt class="col-sm-3">Proof</dt><dd class="col-sm-9"><a href="{{ asset('storage/' . $payment->admin_proof) }}" target="_blank">View</a></dd>
                    @if($payment->admin_notes)<dt class="col-sm-3">Admin Notes</dt><dd class="col-sm-9">{{ $payment->admin_notes }}</dd>@endif
                    @if($payment->supplier_notes)<dt class="col-sm-3">Supplier Notes</dt><dd class="col-sm-9">{{ $payment->supplier_notes }}</dd>@endif
                </dl>
            @else
                <p class="text-muted mb-0">No payment submitted yet.</p>
            @endif
        </div>
    </div>
    @if($canPay)
        @include('admin.purchase-orders.partials.payment-modal')
    @endif
</div>
@endsection 