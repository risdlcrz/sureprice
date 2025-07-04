@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h4>Supplier Details</h4>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Company Name</dt>
                <dd class="col-sm-9">{{ $supplier->company_name }}</dd>

                <dt class="col-sm-3">Contact Person</dt>
                <dd class="col-sm-9">{{ $supplier->contact_person }}</dd>

                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $supplier->email }}</dd>

                <dt class="col-sm-3">Phone</dt>
                <dd class="col-sm-9">{{ $supplier->phone }}</dd>

                <dt class="col-sm-3">Address</dt>
                <dd class="col-sm-9">{{ $supplier->address }}</dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">{{ ucfirst($supplier->status) }}</dd>
            </dl>

            <h5 class="mt-4">Materials</h5>
            @if($supplier->materials && $supplier->materials->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th>Base Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($supplier->materials as $material)
                                <tr>
                                    <td>{{ $material->code }}</td>
                                    <td>{{ $material->name }}</td>
                                    <td>{{ $material->category->name ?? '' }}</td>
                                    <td>{{ $material->unit }}</td>
                                    <td>₱{{ number_format($material->base_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">No materials linked to this supplier.</p>
            @endif

            <h5 class="mt-4">Purchase Order Metrics (Latest)</h5>
            @if($latestOrder)
            <dl class="row">
                <dt class="col-sm-4">On-time Deliveries</dt>
                <dd class="col-sm-8">{{ $latestOrder->ontime_deliveries }}</dd>
                <dt class="col-sm-4">Total Deliveries</dt>
                <dd class="col-sm-8">{{ $latestOrder->total_deliveries }}</dd>
                <dt class="col-sm-4">Defective Units</dt>
                <dd class="col-sm-8">{{ $latestOrder->defective_units }}</dd>
                <dt class="col-sm-4">Total Units</dt>
                <dd class="col-sm-8">{{ $latestOrder->total_units }}</dd>
                <dt class="col-sm-4">Actual Cost</dt>
                <dd class="col-sm-8">₱{{ number_format($latestOrder->actual_cost, 2) }}</dd>
                <dt class="col-sm-4">Estimated Cost</dt>
                <dd class="col-sm-8">₱{{ number_format($latestOrder->estimated_cost, 2) }}</dd>
            </dl>
            @else
            <p class="text-muted">No purchase order metrics available.</p>
            @endif
            <h5 class="mt-4">Purchase Order Metrics (Average)</h5>
            @if($avgOrder)
            <dl class="row">
                <dt class="col-sm-4">Avg. On-time Deliveries</dt>
                <dd class="col-sm-8">{{ number_format($avgOrder->ontime_deliveries, 2) }}</dd>
                <dt class="col-sm-4">Avg. Total Deliveries</dt>
                <dd class="col-sm-8">{{ number_format($avgOrder->total_deliveries, 2) }}</dd>
                <dt class="col-sm-4">Avg. Defective Units</dt>
                <dd class="col-sm-8">{{ number_format($avgOrder->defective_units, 2) }}</dd>
                <dt class="col-sm-4">Avg. Total Units</dt>
                <dd class="col-sm-8">{{ number_format($avgOrder->total_units, 2) }}</dd>
                <dt class="col-sm-4">Avg. Actual Cost</dt>
                <dd class="col-sm-8">₱{{ number_format($avgOrder->actual_cost, 2) }}</dd>
                <dt class="col-sm-4">Avg. Estimated Cost</dt>
                <dd class="col-sm-8">₱{{ number_format($avgOrder->estimated_cost, 2) }}</dd>
            </dl>
            @else
            <p class="text-muted">No average purchase order metrics available.</p>
            @endif
        </div>
    </div>
</div>
@endsection 