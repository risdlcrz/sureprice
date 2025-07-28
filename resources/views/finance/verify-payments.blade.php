@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Payment Verification Dashboard</h1>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-clock fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Pending Verification</h5>
                    <h2 class="text-info">{{ $pendingVerifications->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Verified Today</h5>
                    <h2 class="text-success">{{ $verifiedToday }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                    <h5 class="card-title">Rejected Today</h5>
                    <h2 class="text-danger">{{ $rejectedToday }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-money-bill-wave fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Total Amount Pending</h5>
                    <h2 class="text-primary">₱{{ number_format($totalPendingAmount, 2) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Verifications -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Payments Pending Verification</h5>
            <div>
                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($pendingVerifications->count() > 0)
                @foreach($pendingVerifications as $payment)
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-0 fw-bold">{{ $payment->payment_number }}</h6>
                                    <small class="text-muted">
                                        Contract: {{ $payment->contract->contract_number ?? 'Contract #' . $payment->contract_id }} | 
                                        Client: {{ $payment->contract->client->name ?? 'N/A' }}
                                    </small>
                                </div>
                                <div class="col-md-6 text-end">
                                    <span class="badge bg-info">For Verification</span>
                                    <small class="text-muted d-block">Submitted: {{ $payment->updated_at->format('M d, Y H:i') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="fw-bold text-primary">Payment Details</h6>
                                    <p><strong>Amount Due:</strong> ₱{{ number_format($payment->amount, 2) }}</p>
                                    <p><strong>Amount Paid:</strong> ₱{{ number_format($payment->client_paid_amount, 2) }}</p>
                                    <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->client_payment_method)) }}</p>
                                    <p><strong>Reference Number:</strong> {{ $payment->client_reference_number }}</p>
                                    <p><strong>Payment Date:</strong> {{ $payment->client_paid_date ? $payment->client_paid_date->format('M d, Y') : 'N/A' }}</p>
                                    @if($payment->client_notes)
                                        <p><strong>Client Notes:</strong> {{ $payment->client_notes }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold text-primary">Contract Information</h6>
                                    <p><strong>Contractor:</strong> {{ $payment->contract->contractor->name ?? 'N/A' }}</p>
                                    <p><strong>Client:</strong> {{ $payment->contract->client->name ?? 'N/A' }}</p>
                                    <p><strong>Payment Type:</strong> {{ ucfirst($payment->payment_type) }}</p>
                                    <p><strong>Due Date:</strong> {{ $payment->due_date->format('M d, Y') }}</p>
                                    
                                    @if($payment->client_payment_proof)
                                        <div class="mt-3">
                                            <h6 class="fw-bold text-primary">Payment Proof</h6>
                                            <a href="{{ asset('storage/' . $payment->client_payment_proof) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View Proof
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <hr>
                            
                            <!-- Verification Form -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="fw-bold text-primary">Verification Actions</h6>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#verifyPaymentModal{{ $payment->id }}">
                                            <i class="fas fa-check"></i> Verify Payment
                                        </button>
                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectPaymentModal{{ $payment->id }}">
                                            <i class="fas fa-times"></i> Reject Payment
                                        </button>
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#requestMoreInfoModal{{ $payment->id }}">
                                            <i class="fas fa-question"></i> Request More Info
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Verify Payment Modal -->
                    @include('finance.partials.verify_payment_modal', ['payment' => $payment])
                    
                    <!-- Reject Payment Modal -->
                    @include('finance.partials.reject_payment_modal', ['payment' => $payment])
                    
                    <!-- Request More Info Modal -->
                    @include('finance.partials.request_more_info_modal', ['payment' => $payment])
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h5 class="text-success">All payments verified!</h5>
                    <p class="text-muted">No payments are currently pending verification.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Payments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="GET" action="{{ route('finance.verify-payments') }}">
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="form-select">
                            <option value="">All Methods</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="check">Check</option>
                            <option value="cash">Cash</option>
                            <option value="online_payment">Online Payment</option>
                            <option value="mobile_banking">Mobile Banking</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="date_range" class="form-label">Date Range</label>
                        <select name="date_range" id="date_range" class="form-select">
                            <option value="">All Dates</option>
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="this_week">This Week</option>
                            <option value="last_week">Last Week</option>
                            <option value="this_month">This Month</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount_range" class="form-label">Amount Range</label>
                        <select name="amount_range" id="amount_range" class="form-select">
                            <option value="">All Amounts</option>
                            <option value="0-10000">₱0 - ₱10,000</option>
                            <option value="10000-50000">₱10,000 - ₱50,000</option>
                            <option value="50000-100000">₱50,000 - ₱100,000</option>
                            <option value="100000+">₱100,000+</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                        <a href="{{ route('finance.verify-payments') }}" class="btn btn-secondary">Clear Filter</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}
</style>
@endpush 