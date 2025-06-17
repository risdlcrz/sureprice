@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">My Quotations</h1>
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
                        <th>Due Date</th>
                        <th>Status</th>
                        <th># Materials</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotations as $quotation)
                    <tr>
                        <td>{{ $quotation->rfq_number }}</td>
                        <td>{{ $quotation->due_date->format('M d, Y') }}</td>
                        <td>
                            <span class="badge badge-{{ $quotation->status_color }}">
                                {{ ucfirst($quotation->status) }}
                            </span>
                        </td>
                        <td>{{ $quotation->materials->count() }}</td>
                        <td>
                            <a href="{{ route('supplier.quotations.show', $quotation) }}" class="btn btn-sm btn-primary">View/Respond</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No quotation requests found.</td>
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