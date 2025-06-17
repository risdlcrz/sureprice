@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">RFQ Details: {{ $rfq->rfq_number }}</h1>
        <div class="btn-group">
            <a href="{{ route('procurement.rfqs.index') }}" class="btn btn-secondary">Back to RFQs</a>
            @if($rfq->status === 'active')
            <a href="{{ route('procurement.rfqs.edit', $rfq) }}" class="btn btn-primary">Edit RFQ</a>
            <form action="{{ route('procurement.rfqs.destroy', $rfq) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this RFQ?')">Delete</button>
            </form>
            @endif
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">RFQ Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Title:</strong> {{ $rfq->title }}</p>
                    <p><strong>Description:</strong> {{ $rfq->description }}</p>
                    <p><strong>Due Date:</strong> {{ $rfq->due_date->format('M d, Y') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Status:</strong> 
                        <span class="badge bg-{{ $rfq->status_color }}">
                            {{ ucfirst($rfq->status) }}
                        </span>
                    </p>
                    <p><strong>Created By:</strong> {{ $rfq->createdBy->getDisplayNameAttribute() }}</p>
                    <p><strong>Created At:</strong> {{ $rfq->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Materials in RFQ -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Requested Materials</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Material Name</th>
                        <th>Code</th>
                        <th>Category</th>
                        <th>Requested Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rfq->materials as $material)
                    <tr>
                        <td>{{ $material->name }}</td>
                        <td>{{ $material->code }}</td>
                        <td>{{ $material->category->name ?? '-' }}</td>
                        <td>{{ $material->pivot->quantity }} {{ $material->unit }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No materials requested for this RFQ.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Invited Suppliers -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Invited Suppliers</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Supplier Name</th>
                        <th>Email</th>
                        <th>Invitation Status</th>
                        <th>Response Status</th>
                        <th>Response Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rfq->suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->email }}</td>
                        <td>
                            <span class="badge bg-{{ $supplier->pivot->status === 'pending' ? 'warning' : ($supplier->pivot->status === 'accepted' ? 'success' : 'danger') }}">
                                {{ ucfirst($supplier->pivot->status) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $response = $rfq->responses->where('supplier_id', $supplier->id)->first();
                            @endphp
                            @if($response)
                                <span class="badge bg-{{ $response->status === 'pending' ? 'warning' : ($response->status === 'accepted' ? 'success' : 'danger') }}">
                                    {{ ucfirst($response->status) }}
                                </span>
                            @else
                                <span class="badge bg-info">No Response Yet</span>
                            @endif
                        </td>
                        <td>{{ $response ? $response->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No suppliers invited to this RFQ.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quotation Responses -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Quotation Responses</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Responded At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rfq->responses as $response)
                    <tr>
                        <td>{{ $response->supplier->name }}</td>
                        <td>₱{{ number_format($response->total_amount, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $response->status === 'pending' ? 'warning' : ($response->status === 'accepted' ? 'success' : 'danger') }}">
                                {{ ucfirst($response->status) }}
                            </span>
                        </td>
                        <td>{{ $response->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <a href="{{ route('procurement.rfqs.response.show', [$rfq, $response]) }}" class="btn btn-sm btn-info">View Response</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No responses received yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 