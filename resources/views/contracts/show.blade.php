@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Contract Details</h2>
        <div class="d-flex gap-2">
            @if($contract->status === 'draft')
                <span class="badge bg-warning">Draft</span>
            @endif
            
            <a href="{{ route('material-requests.create', ['contract_id' => $contract->id]) }}" 
               class="btn btn-info">
                <i class="fas fa-file-alt"></i> Create Material Request
            </a>

            <button class="btn btn-success" onclick="approveContract()">
                <i class="fas fa-check"></i> Approve
            </button>
            <button class="btn btn-danger" onclick="rejectContract()">
                <i class="fas fa-times"></i> Reject
            </button>
            <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Contract
            </a>
            <a href="{{ route('purchase-requests.edit', $contract->purchaseRequest) }}" 
               class="btn btn-success">
                <i class="fas fa-edit"></i> Edit Purchase Request
            </a>
            <form action="{{ route('contracts.destroy', $contract) }}" 
                  method="POST" 
                  class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="btn btn-danger" 
                        onclick="return confirm('Are you sure you want to delete this contract?')">
                    <i class="fas fa-trash"></i> Delete Contract
                </button>
            </form>
            <a href="{{ route('contracts.pdf', $contract) }}" 
               class="btn btn-success">
                <i class="fas fa-download"></i> Download PDF
            </a>
            <a href="{{ route('contracts.index') }}" 
               class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Contract Information -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Contract Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th width="30%">Contract ID:</th>
                            <td>{{ $contract->contract_number }}</td>
                        </tr>
                        <tr>
                            <th>Start Date:</th>
                            <td>{{ $contract->start_date->format('F d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>End Date:</th>
                            <td>{{ $contract->end_date->format('F d, Y') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th width="30%">Status:</th>
                            <td>
                                <span class="badge bg-{{ $contract->status_color }}">
                                    {{ ucwords(str_replace('_', ' ', $contract->status)) }}
                                </span>
                                @if($contract->payments && $contract->payments->count())
                                    <div class="mt-2">
                                        @php
                                            $total = $contract->total_amount;
                                            $paid = $contract->total_paid;
                                            $percent = $total > 0 ? round(($paid / $total) * 100) : 0;
                                        @endphp
                                        <div class="progress" style="height: 18px;">
                                            <div class="progress-bar {{ $percent >= 100 ? 'bg-success' : 'bg-info' }}" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $percent }}% Paid
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Total Amount:</th>
                            <td>₱{{ number_format($contract->total_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Property Information -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Property Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <tr>
                            <th width="20%">Property Type:</th>
                            <td>{{ $contract->property->type }}</td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td>
                                {{ $contract->property->address }}<br>
                                {{ $contract->property->city }}<br>
                                {{ $contract->property->postal_code }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Contractor and Client Information -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5>Contractor Information</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th width="30%">Name:</th>
                            <td>{{ $contract->contractor->name }}</td>
                        </tr>
                        <tr>
                            <th>Company:</th>
                            <td>{{ $contract->contractor->company_name }}</td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td>{{ $contract->contractor->address }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $contract->contractor->email }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $contract->contractor->phone }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5>Client Information</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th width="30%">Name:</th>
                            <td>{{ $contract->client->name }}</td>
                        </tr>
                        <tr>
                            <th>Company:</th>
                            <td>{{ $contract->client->company_name }}</td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td>{{ $contract->client->address }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $contract->client->email }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $contract->client->phone }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Details -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Payment Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th width="30%">Payment Method:</th>
                            <td>{{ $contract->payment_method }}</td>
                        </tr>
                        <tr>
                            <th>Payment Terms:</th>
                            <td>{{ $contract->payment_terms }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Workflow Status -->
    @include('contracts.partials.workflow-status')
</div>
@endsection

@push('styles')
<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    margin-bottom: 1.5rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.table th {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.875rem;
    padding: 0.5em 1em;
}

.workflow-steps .step {
    margin-bottom: 20px;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.workflow-steps .step.completed {
    border-color: #28a745;
    background-color: #f8fff8;
}

.workflow-steps .step.pending {
    border-color: #ffc107;
    background-color: #fffff8;
}

.stock-results {
    margin: 10px 0;
    padding: 10px;
    background-color: #f8f9fa;
    border-radius: 4px;
}

.approval-status, 
.payment-status {
    margin: 10px 0;
}

.btn-group {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
function handleResponse(response) {
    if (response.success) {
        toastr.success(response.message);
        setTimeout(() => window.location.reload(), 1000);
    } else {
        toastr.error(response.message || 'An error occurred');
    }
}

function handleError(error) {
    console.error('Error:', error);
    toastr.error('An error occurred while processing your request');
}

function approveContract() {
    axios.post("{{ route('contracts.approve', $contract) }}")
        .then(response => handleResponse(response.data))
        .catch(handleError);
}

function rejectContract() {
    axios.post("{{ route('contracts.reject', $contract) }}")
        .then(response => handleResponse(response.data))
        .catch(handleError);
}

function checkStock() {
    axios.post("{{ route('contracts.workflow.check-stock', $contract) }}")
        .then(response => handleResponse(response.data))
        .catch(handleError);
}

function adminApprove() {
    axios.post("{{ route('contracts.workflow.admin-approve', $contract) }}")
        .then(response => handleResponse(response.data))
        .catch(handleError);
}

function supplierApprove() {
    axios.post("{{ route('contracts.workflow.supplier-approve', $contract) }}")
        .then(response => handleResponse(response.data))
        .catch(handleError);
}

function validatePaymentAdmin() {
    axios.post("{{ route('contracts.workflow.validate-payment-admin', $contract) }}")
        .then(response => handleResponse(response.data))
        .catch(handleError);
}

function validatePaymentSupplier() {
    axios.post("{{ route('contracts.workflow.validate-payment-supplier', $contract) }}")
        .then(response => handleResponse(response.data))
        .catch(handleError);
}

function createDelivery() {
    axios.post("{{ route('contracts.workflow.create-delivery', $contract) }}")
        .then(response => handleResponse(response.data))
        .catch(handleError);
}

function confirmDelivery() {
    axios.post("{{ route('contracts.workflow.confirm-delivery', $contract) }}")
        .then(response => handleResponse(response.data))
        .catch(handleError);
}

function updateStock() {
    axios.post("{{ route('contracts.workflow.update-stock', $contract) }}")
        .then(response => handleResponse(response.data))
        .catch(handleError);
}
</script>
@endpush 