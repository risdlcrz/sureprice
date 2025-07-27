@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Payment Details</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Payment Information</h6>
                            <p><strong>Payment Number:</strong> {{ $payment->payment_number }}</p>
                            <p><strong>Amount:</strong> ₱{{ number_format($payment->amount, 2) }}</p>
                            <p><strong>Payment Type:</strong> {{ $payment->payment_type }}</p>
                            <p><strong>Due Date:</strong> {{ $payment->due_date->format('M d, Y') }}</p>
                            <p><strong>Status:</strong> 
                                @if($payment->status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($payment->status === 'for_verification')
                                    <span class="badge bg-info">For Verification</span>
                                @elseif($payment->status === 'pending')
                                    @if($payment->due_date->isPast())
                                        <span class="badge bg-danger">Overdue</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($payment->status) }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Contract Information</h6>
                            <p><strong>Contract:</strong> {{ $payment->contract->title ?? 'Contract #' . $payment->contract->id }}</p>
                            <p><strong>Contractor:</strong> {{ $payment->contract->contractor->name ?? 'N/A' }}</p>
                            <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->contract->payment_method)) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Submission Section -->
            @if($payment->status === 'pending')
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Submit Payment Proof</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('payments.submit-client-proof', $payment) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="client_payment_method" class="form-label">Payment Method</label>
                                        <select name="client_payment_method" id="client_payment_method" class="form-control" required>
                                            <option value="">Select Payment Method</option>
                                            <option value="bank_transfer">Bank Transfer</option>
                                            <option value="check">Check</option>
                                            <option value="cash">Cash</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="client_reference_number" class="form-label">Reference Number</label>
                                        <input type="text" name="client_reference_number" id="client_reference_number" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="client_paid_amount" class="form-label">Amount Paid</label>
                                        <input type="number" name="client_paid_amount" id="client_paid_amount" class="form-control" value="{{ $payment->amount }}" step="0.01" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="client_paid_date" class="form-label">Payment Date</label>
                                        <input type="date" name="client_paid_date" id="client_paid_date" class="form-control" value="{{ now()->toDateString() }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="client_payment_proof" class="form-label">Proof of Payment</label>
                                        <input type="file" name="client_payment_proof" id="client_payment_proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                                        <small class="form-text text-muted">Upload receipt, screenshot, or proof of payment</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="client_notes" class="form-label">Notes (Optional)</label>
                                        <textarea name="client_notes" id="client_notes" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Payment Proof</button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Payment Status Display -->
            @if($payment->status === 'paid')
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Payment Verified</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Your Payment</h6>
                                <p><strong>Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->client_payment_method)) }}</p>
                                <p><strong>Reference:</strong> {{ $payment->client_reference_number }}</p>
                                <p><strong>Amount:</strong> ₱{{ number_format($payment->client_paid_amount, 2) }}</p>
                                <p><strong>Date:</strong> {{ $payment->client_paid_date ? $payment->client_paid_date->format('M d, Y') : 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Admin Verification</h6>
                                <p><strong>Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->admin_payment_method)) }}</p>
                                <p><strong>Reference:</strong> {{ $payment->admin_reference_number }}</p>
                                <p><strong>Amount:</strong> ₱{{ number_format($payment->admin_received_amount, 2) }}</p>
                                <p><strong>Date:</strong> {{ $payment->admin_received_date ? $payment->admin_received_date->format('M d, Y') : 'N/A' }}</p>
                            </div>
                        </div>
                        @if($payment->client_payment_proof)
                            <p><strong>Your Proof:</strong> <a href="{{ asset('storage/' . $payment->client_payment_proof) }}" target="_blank" class="btn btn-sm btn-info">View</a></p>
                        @endif
                        @if($payment->admin_payment_proof)
                            <p><strong>Admin Proof:</strong> <a href="{{ asset('storage/' . $payment->admin_payment_proof) }}" target="_blank" class="btn btn-sm btn-info">View</a></p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Payment Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Payment Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6>Payment Created</h6>
                                <p class="text-muted">{{ $payment->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @if($payment->client_paid_date)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6>Payment Submitted</h6>
                                    <p class="text-muted">{{ $payment->client_paid_date->format('M d, Y') }}</p>
                                </div>
                            </div>
                        @endif
                        @if($payment->admin_received_date)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6>Payment Verified</h6>
                                    <p class="text-muted">{{ $payment->admin_received_date->format('M d, Y') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('client.payments') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> Back to Payments
                        </a>
                        <a href="{{ route('client.payments.dashboard') }}" class="btn btn-outline-info">
                            <i class="fas fa-chart-bar"></i> Payment Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -29px;
    top: 17px;
    width: 2px;
    height: calc(100% + 10px);
    background-color: #dee2e6;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-content p {
    margin-bottom: 0;
    font-size: 0.875rem;
}

.card {
    border-radius: 1rem;
    box-shadow: 0 4px 24px rgba(44,62,80,0.08), 0 1.5px 6px rgba(44,62,80,0.04);
    border: none;
    margin-bottom: 2rem;
}

.card-header {
    background: #fff;
    border-radius: 1rem 1rem 0 0;
    border-bottom: 1px solid #e9ecef;
    padding: 1.5rem 2rem 1rem 2rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.5em 0.75em;
}
</style>
@endsection 