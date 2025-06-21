@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Purchase Request Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('purchase-requests.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        @if($purchaseRequest->status === 'pending')
                    <a href="{{ route('purchase-requests.edit', $purchaseRequest) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endif
                        @if($purchaseRequest->status === 'approved')
                            <a href="{{ route('purchase-orders.create', ['purchase_request_id' => $purchaseRequest->id]) }}" class="btn btn-success">
                                <i class="fas fa-file-invoice"></i> Create Purchase Order
                </a>
                        @endif
        </div>
                    </div>
                    <div class="card-body">
                    <!-- Request Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4>Request Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Request Number</th>
                                    <td>{{ $purchaseRequest->request_number }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge badge-{{ $purchaseRequest->status === 'pending' ? 'warning' : ($purchaseRequest->status === 'approved' ? 'success' : 'danger') }}">
                                            {{ ucfirst($purchaseRequest->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Request Type</th>
                                    <td>{{ $purchaseRequest->is_project_related ? 'Project Related' : 'Standalone' }}</td>
                                </tr>
                                @if($purchaseRequest->is_project_related)
                                    <tr>
                                        <th>Contract</th>
                                        <td>
                                            @if($purchaseRequest->contract)
                                                {{ $purchaseRequest->contract->contract_number }} - {{ $purchaseRequest->contract->client->name ?? '' }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Requested By</th>
                                    <td>{{ $purchaseRequest->requestedBy?->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Created Date</th>
                                    <td>{{ $purchaseRequest->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated</th>
                                    <td>{{ $purchaseRequest->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h4>Financial Summary</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Total Amount</th>
                                    <td class="text-right">{{ $purchaseRequest->total_amount ? number_format($purchaseRequest->total_amount, 2) : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Number of Items</th>
                                    <td class="text-right">{{ $purchaseRequest->items?->count() ?? 0 }}</td>
                                </tr>
                            </table>

                            @if($purchaseRequest->notes)
                                <h4 class="mt-4">Notes</h4>
                                <div class="p-3 bg-light">
                                    {{ $purchaseRequest->notes }}
                            </div>
                            @endif
                    </div>
                </div>

                    <!-- Request Items -->
                    <div class="row">
                        <div class="col-12">
                            <h4>Request Items</h4>
                        <div class="table-responsive">
                                <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Estimated Unit Price</th>
                                        <th>Total Amount</th>
                                            <th>Preferred Brand</th>
                                            <th>Preferred Supplier</th>
                                            <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($purchaseRequest->items)
                                        @foreach($purchaseRequest->items as $item)
                                            <tr>
                                                <td>{{ $item->material?->name ?? 'N/A' }}</td>
                                                <td>{{ $item->description }}</td>
                                                    <td class="text-right">{{ $item->quantity ? number_format($item->quantity, 2) : 'N/A' }}</td>
                                                <td>{{ $item->unit }}</td>
                                                    <td class="text-right">{{ $item->estimated_unit_price ? number_format($item->estimated_unit_price, 2) : 'N/A' }}</td>
                                                    <td class="text-right">{{ $item->total_amount ? number_format($item->total_amount, 2) : 'N/A' }}</td>
                                                    <td>{{ $item->preferred_brand ?? 'N/A' }}</td>
                                                    <td>
                                                        @if($item->preferredSupplier)
                                                            {{ $item->preferredSupplier->company_name }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->notes ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="9" class="text-center">No items found</td>
                                        </tr>
                                    @endif
                                </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="5" class="text-right">Total:</th>
                                            <th class="text-right">{{ $purchaseRequest->total_amount ? number_format($purchaseRequest->total_amount, 2) : 'N/A' }}</th>
                                            <th colspan="3"></th>
                                        </tr>
                                    </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                    <!-- Approval Actions -->
                    @if($purchaseRequest->status === 'pending' && (auth()->user()->role === 'procurement' || auth()->user()->role === 'admin'))
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                        <div class="card-header">
                                        <h4 class="card-title">Approval Actions</h4>
                        </div>
                        <div class="card-body">
                                        <form action="{{ route('purchase-requests.approve', $purchaseRequest) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this request?')">
                                                <i class="fas fa-check"></i> Approve Request
                                            </button>
                                        </form>
                                        <form action="{{ route('purchase-requests.reject', $purchaseRequest) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this request?')">
                                                <i class="fas fa-times"></i> Reject Request
                                            </button>
                                        </form>
                        </div>
                    </div>
                    </div>
                        </div>
                    @endif

                    <!-- Related Purchase Orders -->
                    @if($purchaseRequest->purchaseOrders?->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4>Related Purchase Orders</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>PO Number</th>
                                                <th>Supplier</th>
                                                <th>Total Amount</th>
                                                <th>Status</th>
                                                <th>Payment Status</th>
                                                <th>Created Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($purchaseRequest->purchaseOrders as $po)
                                                @php $poPayment = $po->payments->last(); @endphp
                                                <tr>
                                                    <td>{{ $po->po_number }}</td>
                                                    <td>{{ $po->supplier->company_name }}</td>
                                                    <td class="text-right">{{ $po->total_amount ? number_format($po->total_amount, 2) : 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $po->status_color }}">
                                                            {{ ucfirst($po->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($poPayment)
                                                            <span class="badge bg-{{ $poPayment->status === 'verified' ? 'success' : ($poPayment->status === 'for_verification' ? 'info' : ($poPayment->status === 'rejected' ? 'danger' : 'secondary')) }}">
                                                                {{ ucfirst($poPayment->status) }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary">Unpaid</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $po->created_at->format('M d, Y') }}</td>
                                                    <td>
                                                        <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-sm btn-info">View</a>
                                                        @if(auth()->user()->isAdmin() && (!$poPayment || $poPayment->status === 'rejected'))
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#submitPaymentModal{{ $po->id }}">Pay</button>
                                                            <!-- Payment Modal for this PO -->
                                                            <div class="modal fade" id="submitPaymentModal{{ $po->id }}" tabindex="-1">
                                                              <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                  <form method="POST" action="{{ route('purchase-orders.payments.store', $po) }}" enctype="multipart/form-data">
                                                                    @csrf
                                                                    <div class="modal-header">
                                                                      <h5 class="modal-title">Submit Payment to Supplier</h5>
                                                                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                      <div class="mb-3">
                                                                        <label>Amount</label>
                                                                        <input type="number" name="amount" class="form-control" value="{{ $po->total_amount }}" required>
                                                                      </div>
                                                                      <div class="mb-3">
                                                                        <label>Payment Method</label>
                                                                        <select name="payment_method" class="form-control" required>
                                                                          <option value="bank_transfer">Bank Transfer</option>
                                                                          <option value="check">Check</option>
                                                                          <option value="cash">Cash</option>
                                                                        </select>
                                                                      </div>
                                                                      <div class="mb-3">
                                                                        <label>Reference Number</label>
                                                                        <input type="text" name="admin_reference_number" class="form-control" required>
                                                                      </div>
                                                                      <div class="mb-3">
                                                                        <label>Date Paid</label>
                                                                        <input type="date" name="admin_paid_date" class="form-control" value="{{ now()->toDateString() }}" required>
                                                                      </div>
                                                                      <div class="mb-3">
                                                                        <label>Proof of Payment</label>
                                                                        <input type="file" name="admin_proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                                                                      </div>
                                                                      <div class="mb-3">
                                                                        <label>Notes (optional)</label>
                                                                        <textarea name="admin_notes" class="form-control"></textarea>
                                                                      </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                      <button type="submit" class="btn btn-primary">Submit for Verification</button>
                                                                    </div>
                                                                  </form>
                                                                </div>
                                                              </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 