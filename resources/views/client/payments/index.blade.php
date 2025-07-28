@extends('layouts.app')

@section('content')
<style>
/* Comprehensive fix for modal glitching */
.modal {
    z-index: 1055 !important;
    /* Prevent any animations or transitions */
    animation: none !important;
    transition: none !important;
}

.modal-backdrop {
    z-index: 1050 !important;
    animation: none !important;
    transition: none !important;
}

.modal-dialog {
    animation: none !important;
    transition: none !important;
    transform: none !important;
}

.modal-content {
    animation: none !important;
    transition: none !important;
    transform: none !important;
}

/* Disable ALL hover effects and transitions globally when modal is open */
body.modal-open * {
    transition: none !important;
    animation: none !important;
    transform: none !important;
}

/* Ensure proper form styling */
.form-control:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

.is-invalid {
    border-color: #dc3545 !important;
}

.is-invalid:focus {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

/* Fix button loading states */
.btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

/* Ensure proper table responsiveness */
.table-responsive {
    overflow-x: auto;
}

/* Completely disable hover effects on cards */
.card {
    transition: none !important;
}

.card:hover {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    transform: none !important;
}

/* Disable all hover effects on buttons */
.btn {
    transition: none !important;
}

.btn:hover {
    transform: none !important;
    box-shadow: none !important;
}

/* Disable hover effects on form elements */
.form-control, .form-select, .form-check-input {
    transition: none !important;
}

.form-control:hover, .form-select:hover {
    transform: none !important;
    box-shadow: none !important;
}

/* Disable hover effects on navigation */
.nav-link {
    transition: none !important;
}

.nav-link:hover {
    transform: none !important;
}

/* Ensure modal elements are completely stable */
.modal * {
    transition: none !important;
    animation: none !important;
    transform: none !important;
}

/* Fix pointer events */
.modal {
    pointer-events: auto !important;
}

.modal-content {
    pointer-events: auto !important;
}

/* Prevent any CSS animations globally */
* {
    animation: none !important;
}

/* Override any Bootstrap transitions */
.fade {
    transition: none !important;
}

.show {
    transition: none !important;
}

/* Ensure backdrop is stable */
.modal-backdrop.show {
    transition: none !important;
}

/* Disable any potential CSS transforms */
.modal-dialog-centered {
    transform: none !important;
}

/* Ensure form elements don't have any hover states */
input, select, textarea, button {
    transition: none !important;
}

input:hover, select:hover, textarea:hover, button:hover {
    transform: none !important;
    box-shadow: none !important;
}
</style>

<div class="container">
    <h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">My Payments Dashboard</h1>
    
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
    <div class="row mb-4">
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
                                        Contractor: {{ $contract->contractor->name ?? 'N/A' }} | 
                                        Total: ₱{{ number_format($contractData->totalAmount, 2) }}
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
                                    <strong>Contractor:</strong> {{ $contract->contractor->name ?? 'N/A' }}<br>
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
                                                    $proof_path = $payment->client_payment_proof ?? ($payment->attachment ? $payment->attachment->path : null);
                                                @endphp
                                                @if($proof_path)
                                                    <a href="{{ asset('storage/' . $proof_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
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
                    <i class="fas fa-file-contract fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No contracts found</h5>
                    <p class="text-muted">You don't have any contracts with payment schedules yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Search Contract Modal -->
<div class="modal fade" id="searchContractModal" tabindex="-1" aria-labelledby="searchContractModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchContractModalLabel">Search Contracts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="contractSearch" class="form-label">Search by Contract Number or Client Name</label>
                    <input type="text" class="form-control" id="contractSearch" placeholder="Enter search term...">
                </div>
                <div id="searchResults"></div>
            </div>
        </div>
    </div>
</div>

<script>
// Comprehensive modal stability fix
document.addEventListener('DOMContentLoaded', function() {
    // Disable all hover effects when modal is open
    function disableHoverEffects() {
        const style = document.createElement('style');
        style.id = 'modal-stability-fix';
        style.textContent = `
            * {
                transition: none !important;
                animation: none !important;
                transform: none !important;
            }
            .modal * {
                transition: none !important;
                animation: none !important;
                transform: none !important;
            }
        `;
        document.head.appendChild(style);
    }

    // Enable hover effects when modal is closed
    function enableHoverEffects() {
        const style = document.getElementById('modal-stability-fix');
        if (style) {
            style.remove();
        }
    }

    // Listen for modal events
    document.addEventListener('show.bs.modal', function() {
        disableHoverEffects();
    });

    document.addEventListener('hidden.bs.modal', function() {
        enableHoverEffects();
    });

    // Ensure modals work properly
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        // Remove any existing event listeners
        const newModal = modal.cloneNode(true);
        modal.parentNode.replaceChild(newModal, modal);
    });

    // Simple form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const invalidFields = form.querySelectorAll('.is-invalid');
            if (invalidFields.length > 0) {
                e.preventDefault();
                alert('Please correct the errors before submitting.');
            }
        });
    });
});

// Prevent any mouse events from interfering with modal
document.addEventListener('mouseover', function(e) {
    if (e.target.closest('.modal')) {
        e.stopPropagation();
    }
});

document.addEventListener('mouseenter', function(e) {
    if (e.target.closest('.modal')) {
        e.stopPropagation();
    }
});

document.addEventListener('mouseleave', function(e) {
    if (e.target.closest('.modal')) {
        e.stopPropagation();
    }
});

// Ensure modal backdrop is stable
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-backdrop')) {
        e.stopPropagation();
    }
});
</script>

@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body, .container {
    background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%) !important;
    font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
}
h1, .h3 {
    font-family: 'Inter', Arial, sans-serif;
    font-weight: 700;
    letter-spacing: 0.01em;
    color: #198754;
    font-size: 2.2rem;
    margin-bottom: 2rem;
}
.card {
    border-radius: 12px;
    transition: all 0.3s ease;
}
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}
.badge {
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
}
.table th {
    font-weight: 600;
    color: #495057;
}
.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}
</style>
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