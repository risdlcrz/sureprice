@extends('layouts.app')

@section('content')
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
                                        <th>Contract</th>
                                        <td>
                                        @if($materialRequest->contract)
                                            <a href="{{ route('contracts.show', $materialRequest->contract) }}">
                                                {{ $materialRequest->contract->contract_number }} - {{ $materialRequest->contract->title }}
                                            </a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                <tr>
                                    <th>Requested By</th>
                                    <td>{{ $materialRequest->user->name ?? 'N/A' }}</td>
                                </tr>
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
                                <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                            <th>Requested Quantity</th>
                                            <th>Fulfilled from Stock</th>
                                            <th>Needs Purchasing</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        @foreach($materialRequest->items as $item)
                                            <tr>
                                                <td>{{ $item->material->name }}</td>
                                                <td>{{ $item->quantity }} {{ $item->unit }}</td>
                                                <td>{{ $item->fulfilled_quantity }} {{ $item->unit }}</td>
                                                <td>{{ $item->quantity - $item->fulfilled_quantity }} {{ $item->unit }}</td>
                                            </tr>
                                        @endforeach
                                </tbody>
                            </table>
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