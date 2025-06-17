@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Request for Quotations</h1>
        <a href="{{ route('procurement.rfqs.create') }}" class="btn btn-primary">Create RFQ</a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('procurement.rfqs.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="RFQ # or Title" value="{{ request('search') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('procurement.rfqs.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- RFQs Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>RFQ #</th>
                        <th>Title</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Responses</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rfqs as $rfq)
                    <tr>
                        <td>{{ $rfq->rfq_number }}</td>
                        <td>{{ $rfq->title }}</td>
                        <td>{{ $rfq->due_date->format('M d, Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $rfq->status_color }}">
                                {{ ucfirst($rfq->status) }}
                            </span>
                        </td>
                        <td>{{ $rfq->responses_count }} responses</td>
                        <td>{{ $rfq->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('procurement.rfqs.show', $rfq) }}" class="btn btn-sm btn-primary">View</a>
                                @if($rfq->status === 'active')
                                <a href="{{ route('procurement.rfqs.edit', $rfq) }}" class="btn btn-sm btn-secondary">Edit</a>
                                <form action="{{ route('procurement.rfqs.destroy', $rfq) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this RFQ?')">Delete</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No RFQs found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($rfqs->hasPages())
        <div class="card-footer">
            {{ $rfqs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection 