@extends('layouts.app')

@section('content')
<style>
    .card, .table {
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .card-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        border-radius: 12px 12px 0 0;
    }
    .table th, .table td {
        vertical-align: middle;
        font-size: 1rem;
    }
    .table th {
        background: #f1f5f9;
        color: #222;
    }
    .badge {
        font-size: 1em;
        padding: 0.5em 1em;
        border-radius: 8px;
    }
    .btn-primary {
        background: #1d4ed8;
        border: none;
        border-radius: 8px;
        padding: 0.5em 1.5em;
        font-weight: 600;
    }
    .btn-primary:hover {
        background: #2563eb;
    }
    .grouped-items-table tr.table-primary td {
        background: #e0e7ef !important;
        font-weight: bold;
        font-size: 1.08em;
    }
    .grouped-items-table tr.subtotal-row td {
        background: #dbeafe !important;
        font-weight: bold;
        border-top: 2px solid #60a5fa;
    }
    .grouped-items-table tbody tr:nth-child(even):not(.table-primary):not(.subtotal-row) td {
        background: #f8fafc;
    }
    .legend {
        font-size: 0.95em;
        color: #555;
    }
    .material-indent {
        padding-left: 2em;
        border: none;
        background: transparent !important;
    }
    .text-danger {
        color: #dc2626 !important;
        font-weight: 500;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Material Request Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('material-requests.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
        </div>
                    </div>
                    <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if($purchaseRequest)
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle"></i>
                            <strong>Purchase Request Created:</strong> A purchase request has been automatically created for items that exceeded available stock.
                            <a href="{{ route('purchase-requests.show', $purchaseRequest->id) }}" class="alert-link">View Purchase Request #{{ $purchaseRequest->request_number }}</a>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <!-- Request Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4>Request Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Request Number</th>
                                    <td>MR-{{ $materialRequest->id }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge badge-{{ $materialRequest->status === 'pending' ? 'warning' : ($materialRequest->status === 'completed' ? 'success' : 'secondary') }}">
                                            {{ ucfirst($materialRequest->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Requested By</th>
                                    <td>{{ $materialRequest->requestedBy?->name ?? 'N/A' }}</td>
                                </tr>
                                @if($materialRequest->quotationRequest)
                                <tr>
                                    <th>Originating Quotation Request</th>
                                    <td>
                                        <a href="{{ route('admin.quotation.review', $materialRequest->quotationRequest->id) }}">
                                            {{ $materialRequest->quotationRequest->request_number }}
                                        </a>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Created Date</th>
                                    <td>{{ $materialRequest->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            @if($materialRequest->notes)
                                <h4 class="mt-4">Notes</h4>
                                <div class="p-3 bg-light">
                                    {{ $materialRequest->notes }}
                            </div>
                            @endif
                    </div>
                </div>

                    <!-- Request Items -->
                    <div class="row">
                        <div class="col-12">
                            <h4>Request Items</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered grouped-items-table">
                                    <thead>
                                        <tr>
                                            <th>Material</th>
                                            <th>Supplier</th>
                                            <th>Requested Quantity</th>
                                            <th>Fulfilled from Stock</th>
                                            <th>Needs Purchasing</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $grouped = collect($materialRequest->items)->groupBy(function($item) {
                                            return $item->material->name . '|' . $item->unit;
                                        });
                                    @endphp
                                    @foreach($grouped as $key => $rows)
                                        @php
                                            [$matName, $unit] = explode('|', $key);
                                            $totalRequested = $rows->sum('quantity');
                                            $totalFulfilled = $rows->sum('fulfilled_quantity');
                                            $totalNeeds = $rows->sum(function($item){ return $item->quantity - $item->fulfilled_quantity; });
                                        @endphp
                                        <tr class="table-primary">
                                            <td colspan="5" style="font-weight:bold; background:#e0e7ef;">{{ $matName }} ({{ $unit }})</td>
                                        </tr>
                                        @foreach($rows as $item)
                                        <tr>
                                            <td class="material-indent"></td>
                                            <td>
                                                @if($item->supplier)
                                                    {{ $item->supplier->company_name }}
                                                @elseif($materialRequest->quotationRequest)
                                                    @php
                                                        $supplierName = null;
                                                        $quotationRequest = $materialRequest->quotationRequest;
                                                        $selectedSuppliers = $quotationRequest->selected_suppliers ?? [];
                                                        $selectedSupplierId = $selectedSuppliers[$item->material_id] ?? null;
                                                        
                                                        if ($selectedSupplierId) {
                                                            $supplier = \App\Models\Supplier::find($selectedSupplierId);
                                                            $supplierName = $supplier ? $supplier->company_name : null;
                                                        }
                                                        
                                                        // Fallback to material_quotation pivot if not found in selected_suppliers
                                                        if (!$supplierName) {
                                                            $rfqs = \App\Models\Quotation::where('notes', 'like', '%client quotation request #'.$quotationRequest->request_number.'%')->with(['materials', 'suppliers'])->get();
                                                            foreach ($rfqs as $rfq) {
                                                                $mat = $rfq->materials->firstWhere('id', $item->material_id);
                                                                if ($mat && $mat->pivot && $mat->pivot->selected_supplier_id) {
                                                                    $supplier = $rfq->suppliers->firstWhere('id', $mat->pivot->selected_supplier_id);
                                                                    if ($supplier) {
                                                                        $supplierName = $supplier->company_name;
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    @if($supplierName)
                                                        <span class="text-success font-weight-bold">{{ $supplierName }}</span>
                                                        <small class="text-muted d-block">(Client's choice)</small>
                                                    @else
                                                        <span class="text-danger">No Supplier</span>
                                                    @endif
                                                @else
                                                    <span class="text-danger">No Supplier</span>
                                                @endif
                                            </td>
                                            <td>{{ number_format($item->quantity, 2) }} {{ $item->unit }}</td>
                                            <td>{{ number_format($item->fulfilled_quantity, 2) }} {{ $item->unit }}</td>
                                            <td>{{ number_format($item->quantity - $item->fulfilled_quantity, 2) }} {{ $item->unit }}</td>
                                        </tr>
                                        @endforeach
                                        <tr class="table-info subtotal-row">
                                            <td style="font-weight:bold;">Subtotal</td>
                                            <td></td>
                                            <td style="font-weight:bold;">{{ number_format($totalRequested, 2) }} {{ $unit }}</td>
                                            <td style="font-weight:bold;">{{ number_format($totalFulfilled, 2) }} {{ $unit }}</td>
                                            <td style="font-weight:bold;">{{ number_format($totalNeeds, 2) }} {{ $unit }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="legend mt-2">
                                    <span style="background:#e0e7ef; padding:2px 8px; border-radius:4px;">Material Group</span>
                                    <span style="background:#dbeafe; padding:2px 8px; border-radius:4px; margin-left:10px;">Subtotal</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Associated Purchase Request -->
                    @if($purchaseRequest)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4>Associated Purchase Request</h4>
                                <p>
                                    Some items were not available in stock and a purchase request has been generated.
                                    <a href="{{ route('purchase-requests.show', $purchaseRequest) }}" class="btn btn-primary btn-sm">
                                        View Purchase Request #{{ $purchaseRequest->request_number }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
                </div>
            </div>
        </div>
    </div>
@endsection 