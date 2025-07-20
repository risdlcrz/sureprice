@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Project Procurement</h2>
            <p class="text-muted mb-0">{{ $project->name }} (#{{ $project->project_number }})</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('procurement.projects.show', $project) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Project
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Purchase Requests -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Purchase Requests</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Request #</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($project->contract->purchaseRequests as $pr)
                                <tr>
                                    <td>{{ $pr->request_number }}</td>
                                    <td>{{ $pr->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $pr->status === 'approved' ? 'success' : 
                                            ($pr->status === 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($pr->status) }}
                                        </span>
                                    </td>
                                    <td>₱{{ number_format($pr->total_amount, 2) }}</td>
                                    <td>
                                        @if($pr->status === 'pending')
                                        <form action="{{ route('purchase-requests.request-approval') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="purchase_request_id" value="{{ $pr->id }}">
                                            <button type="submit" class="btn btn-outline-info btn-sm"><i class="fas fa-paper-plane"></i> Request Approval</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No purchase requests found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase Orders -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Purchase Orders</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>PO #</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($project->contract->purchaseOrders as $po)
                                <tr>
                                    <td>{{ $po->po_number }}</td>
                                    <td>{{ $po->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $po->status === 'completed' ? 'success' : 
                                            ($po->status === 'approved' ? 'primary' : 'secondary') }}">
                                            {{ ucfirst($po->status) }}
                                        </span>
                                    </td>
                                    <td>₱{{ number_format($po->total_amount, 2) }}</td>
                                    <td>
                                        @if($po->status === 'pending' || $po->status === 'draft')
                                        <form action="{{ route('purchase-orders.request-approval') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="purchase_order_id" value="{{ $po->id }}">
                                            <button type="submit" class="btn btn-outline-info btn-sm"><i class="fas fa-paper-plane"></i> Request Approval</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No purchase orders found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Materials Summary -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Materials Summary</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th>Total Quantity</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $materials = collect();
                            foreach($project->contract->purchaseOrders as $po) {
                                foreach($po->items as $item) {
                                    $materials->push([
                                        'name' => $item->material->name,
                                        'quantity' => $item->quantity,
                                        'amount' => $item->total_amount,
                                        'status' => $po->status
                                    ]);
                                }
                            }
                            $materials = $materials->groupBy('name')->map(function($items) {
                                return [
                                    'quantity' => $items->sum('quantity'),
                                    'amount' => $items->sum('amount'),
                                    'status' => $items->contains('status', 'completed') ? 'completed' : 
                                        ($items->contains('status', 'approved') ? 'approved' : 'pending')
                                ];
                            });
                        @endphp
                        @forelse($materials as $name => $data)
                        <tr>
                            <td>{{ $name }}</td>
                            <td>{{ number_format($data['quantity'], 2) }}</td>
                            <td>₱{{ number_format($data['amount'], 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $data['status'] === 'completed' ? 'success' : 
                                    ($data['status'] === 'approved' ? 'primary' : 'secondary') }}">
                                    {{ ucfirst($data['status']) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">No materials found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 

@push('styles')
    @vite(['resources/css/procurement/projects/procurement.css'])
@endpush 