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
                                                <a href="{{ route('contracts.show', $purchaseRequest->contract) }}">
                                                    {{ $purchaseRequest->contract->contract_id }}
                                                </a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Project</th>
                                        <td>
                                            @if($purchaseRequest->project)
                                                <a href="{{ route('projects.show', $purchaseRequest->project) }}">
                                                    {{ $purchaseRequest->project->name }}
                                                </a>
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
                    @if($purchaseRequest->status === 'pending' && auth()->user()->can('approve-purchase-requests'))
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
                                                <th>Created Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($purchaseRequest->purchaseOrders)
                                                @foreach($purchaseRequest->purchaseOrders as $po)
                                                    <tr>
                                                        <td>{{ $po->po_number }}</td>
                                                        <td>{{ $po->supplier->company_name }}</td>
                                                        <td class="text-right">{{ $po->total_amount ? number_format($po->total_amount, 2) : 'N/A' }}</td>
                                                        <td>
                                                            <span class="badge badge-{{ $po->status === 'pending' ? 'warning' : ($po->status === 'approved' ? 'success' : 'danger') }}">
                                                                {{ ucfirst($po->status) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $po->created_at->format('M d, Y') }}</td>
                                                        <td>
                                                            <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-sm btn-info">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" class="text-center">No purchase orders found</td>
                                                </tr>
                                            @endif
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
    </div>
@endsection 