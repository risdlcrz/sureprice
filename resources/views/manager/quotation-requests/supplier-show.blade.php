@extends('layouts.app')
@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Supplier Quotation: {{ $quotation->rfq_number }}</h5>
            <span class="badge bg-{{ $quotation->status_color ?? 'secondary' }}">{{ ucfirst($quotation->status) }}</span>
            <a href="{{ route('manager.quotations', ['tab' => 'supplier']) }}" class="btn btn-secondary">Back to List</a>
        </div>
        <div class="card-body">
            <h6>Details</h6>
            <ul>
                <li><strong>RFQ Number:</strong> {{ $quotation->rfq_number }}</li>
                <li><strong>Status:</strong> {{ ucfirst($quotation->status) }}</li>
                <li><strong>Due Date:</strong> {{ $quotation->due_date ? $quotation->due_date->format('Y-m-d') : '-' }}</li>
                <li><strong>Purchase Request:</strong> {{ $quotation->purchaseRequest->pr_number ?? '-' }}</li>
                <li><strong>Suppliers:</strong>
                    @foreach($quotation->suppliers as $supplier)
                        <span class="badge bg-info text-dark">{{ $supplier->company_name }}</span>
                    @endforeach
                </li>
            </ul>
            <h6>Materials</h6>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotation->materials as $material)
                        <tr>
                            <td>{{ $material->name }}</td>
                            <td>{{ $material->unit }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($quotation->responses && $quotation->responses->count())
                <h6>Supplier Responses</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Material</th>
                            <th>Quoted Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotation->responses as $response)
                            @foreach($response->items as $item)
                                <tr>
                                    <td>{{ $response->supplier->company_name ?? 'N/A' }}</td>
                                    <td>{{ $item->material->name ?? 'Material #'.$item->material_id }}</td>
                                    <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection 