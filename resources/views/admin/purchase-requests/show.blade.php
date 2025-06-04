@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Purchase Request Details</h1>
            <div>
                @if(in_array($purchaseRequest->status, ['draft', 'pending']))
                    <a href="{{ route('purchase-requests.edit', $purchaseRequest) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endif
                <a href="{{ route('purchase-requests.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>PR Number:</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $purchaseRequest->pr_number }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Contract:</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $purchaseRequest->contract->contract_id ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Department:</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $purchaseRequest->department }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Required Date:</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $purchaseRequest->required_date->format('F d, Y') }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Purpose:</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $purchaseRequest->purpose }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Notes:</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $purchaseRequest->notes ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Estimated Unit Price</th>
                                        <th>Total Amount</th>
                                        <th>Specifications</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseRequest->items as $item)
                                        <tr>
                                            <td>{{ $item->material->name }}</td>
                                            <td>{{ $item->description }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->unit }}</td>
                                            <td>{{ number_format($item->estimated_unit_price, 2) }}</td>
                                            <td>{{ number_format($item->total_amount, 2) }}</td>
                                            <td>{{ !empty($item->specifications) ? $item->specifications : 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if($purchaseRequest->attachments->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Attachments</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                @foreach($purchaseRequest->attachments as $attachment)
                                    <li class="mb-2">
                                        <i class="fas fa-file"></i>
                                        <a href="{{ Storage::url($attachment->path) }}" target="_blank">
                                            {{ $attachment->original_name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Status Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Current Status:</strong>
                            <span class="badge bg-{{ $purchaseRequest->status_color }} ms-2">
                                {{ ucfirst($purchaseRequest->status) }}
                            </span>
                        </div>
                        <div class="mb-3">
                            <strong>Created By:</strong>
                            <div class="mt-1">{{ $purchaseRequest->requester->name }}</div>
                        </div>
                        <div class="mb-3">
                            <strong>Created At:</strong>
                            <div class="mt-1">{{ $purchaseRequest->created_at->format('F d, Y H:i:s') }}</div>
                        </div>
                        <div class="mb-3">
                            <strong>Last Updated:</strong>
                            <div class="mt-1">{{ $purchaseRequest->updated_at->format('F d, Y H:i:s') }}</div>
                        </div>

                        @if(!in_array($purchaseRequest->status, ['approved', 'rejected']))
                            <form action="{{ route('purchase-requests.update-status', $purchaseRequest) }}" method="POST" class="d-flex flex-column gap-2">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-success w-100 mb-2">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>
                            <form action="{{ route('purchase-requests.update-status', $purchaseRequest) }}" method="POST" class="d-flex flex-column gap-2">
                                @csrf
                                <input type="hidden" name="status" value="draft">
                                <button type="submit" class="btn btn-secondary w-100 mb-2">
                                    <i class="fas fa-file-alt"></i> Mark as Draft
                                </button>
                            </form>
                            <form action="{{ route('purchase-requests.update-status', $purchaseRequest) }}" method="POST" class="d-flex flex-column gap-2">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 