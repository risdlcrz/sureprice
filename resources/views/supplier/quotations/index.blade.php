@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">My Quotations</h1>
    </div>
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('supplier.quotations.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="draft">Draft</option>
                        <option value="sent">Sent</option>
                        <option value="responded">Responded</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sort" class="form-label">Sort By</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="created_at">Created Date</option>
                        <option value="rfq_number">RFQ Number</option>
                        <option value="due_date">Due Date</option>
                        <option value="status">Status</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="perPage" class="form-label">Per Page</label>
                    <select class="form-select" id="perPage" name="perPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('supplier.quotations.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">All Quotation Requests</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>RFQ Number</th>
                        <th>Project</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th># Materials</th>
                        <th>Materials (SRP / Base)</th>
                        <th>Awarded Supplier</th>
                        <th>Awarded Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotations as $quotation)
                    <tr>
                        <td>{{ $quotation->rfq_number }}</td>
                        <td>
                            @if($quotation->purchaseRequest)
                                <strong>PR-{{ $quotation->purchaseRequest->id }}</strong><br>
                                <small class="text-muted">{{ $quotation->purchaseRequest->department ?? '' }}</small>
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $quotation->due_date->format('M d, Y') }}</td>
                        <td>
                            <span class="badge badge-{{ $quotation->status_color }}">
                                {{ ucfirst($quotation->status) }}
                            </span>
                        </td>
                        <td>{{ $quotation->materials->count() }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-info" type="button" data-bs-toggle="collapse" data-bs-target="#materials-{{ $quotation->id }}" aria-expanded="false" aria-controls="materials-{{ $quotation->id }}">
                                View Materials
                            </button>
                            <div class="collapse mt-2" id="materials-{{ $quotation->id }}">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>SRP</th>
                                            <th>Base Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($quotation->materials as $material)
                                        <tr>
                                            <td>{{ $material->name }}</td>
                                            <td>₱{{ number_format($material->srp_price, 2) }}</td>
                                            <td>₱{{ number_format($material->base_price, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </td>
                        <td>
                            @if($quotation->awarded_supplier_id)
                                {{ optional($quotation->suppliers->find($quotation->awarded_supplier_id))->company_name ?? 'N/A' }}
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($quotation->awarded_amount)
                                ₱{{ number_format($quotation->awarded_amount, 2) }}
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('supplier.quotations.show', $quotation) }}" class="btn btn-sm btn-primary">View/Respond</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">No quotation requests found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $quotations->links() }}
        </div>
    </div>
</div>
@endsection 