@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
@vite('resources/css/pages/client-payments-index.css')

<div class="container">
    <h1 class="h3 mb-4 text-center page-title">My Payments Dashboard</h1>
    
    @if(isset($error))
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            {{ $error }}
        </div>
    @endif

    @if(isset($message))
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            {{ $message }}
        </div>
    @endif

    <!-- Payment Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-file-contract fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Total Contracts</h5>
                    <h2 class="text-primary">{{ $pagedContracts->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Pending Payments</h5>
                    <h2 class="text-warning">{{ $pagedContracts->sum(function($contract) { return $contract->payments->where('status', 'pending')->count(); }) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Paid Payments</h5>
                    <h2 class="text-success">{{ $pagedContracts->sum(function($contract) { return $contract->payments->where('status', 'paid')->count(); }) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-hourglass-half fa-3x text-info mb-3"></i>
                    <h5 class="card-title">For Verification</h5>
                    <h2 class="text-info">{{ $pagedContracts->sum(function($contract) { return $contract->payments->where('status', 'for_verification')->count(); }) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Contract Payments Section -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Contract Payments</h5>
            <div>
                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#searchContractModal">
                    <i class="fas fa-search"></i> Search Contract
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($pagedContracts->count() > 0)
                @foreach($pagedContracts as $contractData)
                    @php
                        $contract = $contractData->contract;
                        $nextDue = $contractData->nextDue;
                        $contractPayments = $contractData->payments;
                        $allPaid = $contractPayments->every(fn($p) => strtolower(trim($p->status)) === 'paid');
                        $forVerification = $contractPayments->contains(fn($p) => strtolower(trim($p->status)) === 'for_verification');
                    @endphp
                    
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-0 fw-bold">{{ $contract->contract_number ?? 'Contract #' . $contract->id }}</h6>
                                    <small class="text-muted">
                                        Contractor: {{ $contract->contractor->name ?? 'N/A' }} - {{ $contract->contractor->company_name ?? 'N/A' }}
                                    </small>
                                </div>
                                <div class="col-md-6 text-end">
                                    @if($allPaid)
                                        <span class="badge bg-success">Fully Paid</span>
                                    @elseif($forVerification)
                                        <span class="badge bg-info">For Verification: ₱{{ number_format($contractData->verificationAmount, 2) }}</span>
                                    @elseif($contractData->pendingAmount > 0)
                                        <span class="badge bg-warning">Pending: ₱{{ number_format($contractData->pendingAmount, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Client:</strong> {{ $contract->client->name ?? 'N/A' }}<br>
                                    <strong>Contractor:</strong> {{ $contract->contractor->name ?? 'N/A' }} - {{ $contract->contractor->company_name ?? 'N/A' }}<br>
                                    <strong>Total Contract Amount:</strong> ₱{{ number_format($contractData->totalAmount, 2) }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Paid Amount:</strong> ₱{{ number_format($contractData->paidAmount, 2) }}<br>
                                    <strong>Pending Amount:</strong> ₱{{ number_format($contractData->pendingAmount, 2) }}<br>
                                    @if($nextDue)
                                        <strong>Next Due:</strong> ₱{{ number_format($nextDue->amount, 2) }} on {{ $nextDue->due_date->format('M d, Y') }}
                                    @endif
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Payment #</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Payment Method</th>
                                            <th>Reference #</th>
                                            <th>Proof</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($contractPayments as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_number }}</td>
                                            <td>{{ ucfirst($payment->payment_type) }}</td>
                                            <td>₱{{ number_format($payment->amount, 2) }}</td>
                                            <td>{{ $payment->due_date ? $payment->due_date->format('M d, Y') : 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $status = trim(strtolower($payment->status));
                                                @endphp
                                                @if($status === 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($status === 'for_verification')
                                                    <span class="badge bg-info">For Verification</span>
                                                @elseif($payment->isOverdue())
                                                    <span class="badge bg-danger">Overdue</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>{{ $payment->client_payment_method ?? $payment->payment_method ?? '-' }}</td>
                                            <td>{{ $payment->client_reference_number ?? $payment->reference_number ?? '-' }}</td>
                                            <td>
                                                @php 
                                                    $has_proof = $payment->client_payment_proof ?? ($payment->attachment ? $payment->attachment->path : null) ?? $payment->admin_payment_proof;
                                                @endphp
                                                @if($has_proof)
                                                    <a href="{{ route('payments.proof', $payment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($payment->status === 'pending')
                                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#submitPaymentModal{{ $payment->id }}">
                                                        <i class="fas fa-credit-card"></i> Pay Now
                                                    </button>
                                                    @include('client.payments.partials.submit_payment_modal', ['payment' => $payment])
                                                @elseif($payment->status === 'for_verification')
                                                    <span class="text-info"><i class="fas fa-clock"></i> Awaiting Verification</span>
                                                @elseif($payment->status === 'paid')
                                                    <span class="text-success"><i class="fas fa-check"></i> Paid</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Feedback Section for Completed Contracts -->
                            @if($allPaid)
                                <div class="mt-3 p-3 bg-light rounded">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="mb-1 text-success">
                                                <i class="fas fa-check-circle me-2"></i>
                                                Contract Completed Successfully!
                                            </h6>
                                            <p class="mb-0 text-muted small">
                                                Thank you for choosing GDC! We'd love to hear about your experience.
                                            </p>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            @php
                                                $existingFeedback = \App\Models\ClientFeedback::where('contract_id', $contract->id)
                                                    ->where('user_id', auth()->id())
                                                    ->first();
                                            @endphp
                                            
                                            @if($existingFeedback)
                                                @if($existingFeedback->submitted_at)
                                                    <a href="{{ route('client.feedback.show', $existingFeedback) }}" class="btn btn-success btn-sm">
                                                        <i class="fas fa-eye me-1"></i>View Feedback
                                                    </a>
                                                @else
                                                    <a href="{{ route('client.feedback.edit', $existingFeedback) }}" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit me-1"></i>Continue Feedback
                                                    </a>
                                                @endif
                                            @else
                                                <a href="{{ route('client.feedback.create', $contract) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-star me-1"></i>Provide Feedback
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                <!-- Pagination Links -->
                @if($pagedContracts instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="d-flex justify-content-center">
                        {{ $pagedContracts->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice-dollar fa-4x text-muted mb-3"></i>
                    @if($hasContracts ?? false)
                        <h5>No payments scheduled yet</h5>
                        <p class="text-muted mb-0">Your payment schedule will appear here as soon as it's set up for your contract.</p>
                    @else
                        <h5>No payments yet</h5>
                        <p class="text-muted">Payments appear here once you have an approved contract. Start by requesting a quotation — when you proceed with it, our team prepares your contract.</p>
                        <a href="{{ route('client.quotation.index') }}" class="btn btn-outline-secondary me-2">My quotation requests</a>
                        <a href="{{ route('client.quotation.create') }}" class="btn btn-success"><i class="fas fa-plus me-1"></i> Request a quotation</a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Search Contract Modal -->
<x-modal-dialog id="searchContractModal">
    <x-slot:title>Search Contracts</x-slot:title>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="contractSearch" class="form-label">Search by Contract Number or Client Name</label>
                    <input type="text" class="form-control" id="contractSearch" placeholder="Enter search term...">
                </div>
                <div id="searchResults"></div>
            </div>
</x-modal-dialog>

@vite('resources/js/pages/client-payments-index.js')

@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('contractSearch');
    const resultsDiv = document.getElementById('searchResults');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const contractCards = document.querySelectorAll('.card.mb-3');
        
        contractCards.forEach(card => {
            const contractText = card.textContent.toLowerCase();
            if (contractText.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>
@endpush 