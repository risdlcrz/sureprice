@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Payment Details</h1>
                <a href="{{ route('client.payments') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Payments
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Payment Information Card -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">Payment Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Payment Number:</td>
                                    <td>{{ $payment->payment_number }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Contract:</td>
                                    <td>{{ $payment->contract->contract_number ?? 'Contract #' . $payment->contract_id }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Payment Type:</td>
                                    <td>{{ $payment->payment_type }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Amount:</td>
                                    <td class="fw-bold text-primary">₱{{ number_format($payment->amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Due Date:</td>
                                    <td>{{ $payment->due_date ? $payment->due_date->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Status:</td>
                                    <td>
                                @if($payment->status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($payment->status === 'for_verification')
                                    <span class="badge bg-info">For Verification</span>
                                        @elseif($payment->isOverdue())
                                        <span class="badge bg-danger">Overdue</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Payment Method:</td>
                                    <td>{{ $payment->payment_method ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Reference Number:</td>
                                    <td>{{ $payment->reference_number ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contract Information Card -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">Contract Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Contract Title:</td>
                                    <td>{{ $payment->contract->title ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Contractor:</td>
                                    <td>{{ $payment->contract->contractor->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Total Contract Amount:</td>
                                    <td class="fw-bold">₱{{ number_format($payment->contract->total_amount ?? 0, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Start Date:</td>
                                    <td>{{ $payment->contract->start_date ? $payment->contract->start_date->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">End Date:</td>
                                    <td>{{ $payment->contract->end_date ? $payment->contract->end_date->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Contract Status:</td>
                                    <td>
                                        <span class="badge bg-{{ $payment->contract->getStatusColorAttribute() }}">
                                            {{ ucfirst($payment->contract->status ?? 'N/A') }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Submission Section -->
            @if($payment->status === 'pending' || $payment->status === 'for_verification')
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">Submit Payment Proof</h5>
                    </div>
                    <div class="card-body">
                        @if($payment->status === 'for_verification')
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Your payment proof has been submitted and is currently under verification. You will be notified once the verification is complete.
                            </div>
                        @endif

                        <form action="{{ route('client.payments.submit-client-proof', $payment) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="client_payment_method" class="form-label fw-bold">Payment Method *</label>
                                        <select class="form-select" id="client_payment_method" name="client_payment_method" required>
                                            <option value="">Select Payment Method</option>
                                            <option value="bank_transfer" {{ old('client_payment_method', $payment->contract->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                            <option value="gcash" {{ old('client_payment_method', $payment->contract->payment_method) == 'gcash' ? 'selected' : '' }}>GCash</option>
                                            <option value="paymaya" {{ old('client_payment_method', $payment->contract->payment_method) == 'paymaya' ? 'selected' : '' }}>PayMaya</option>
                                            <option value="cash" {{ old('client_payment_method', $payment->contract->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="check" {{ old('client_payment_method', $payment->contract->payment_method) == 'check' ? 'selected' : '' }}>Check</option>
                                            <option value="other" {{ old('client_payment_method', $payment->contract->payment_method) == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        <div class="form-text">
                                            <strong>Contract Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->contract->payment_method)) }}
                                            <br>You can change this if you used a different payment method.
                                        </div>
                                        @error('client_payment_method')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="client_reference_number" class="form-label fw-bold">Reference Number *</label>
                                        <input type="text" class="form-control" id="client_reference_number" name="client_reference_number" 
                                               value="{{ old('client_reference_number') }}" required 
                                               placeholder="Enter transaction/reference number">
                                        @error('client_reference_number')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="client_paid_amount" class="form-label fw-bold">Amount Paid *</label>
                                        <input type="number" class="form-control" id="client_paid_amount" name="client_paid_amount" 
                                               value="{{ old('client_paid_amount', $payment->amount) }}" required 
                                               step="0.01" min="0">
                                        @error('client_paid_amount')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="client_paid_date" class="form-label fw-bold">Payment Date *</label>
                                        <input type="date" class="form-control" id="client_paid_date" name="client_paid_date" 
                                               value="{{ old('client_paid_date', date('Y-m-d')) }}" required>
                                        @error('client_paid_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="client_payment_proof" class="form-label fw-bold">Payment Proof *</label>
                                        <input type="file" class="form-control" id="client_payment_proof" name="client_payment_proof" 
                                               accept="image/*,.pdf" required>
                                        <div class="form-text">Upload screenshot, photo, or PDF of your payment receipt/proof (Max: 5MB)</div>
                                        @error('client_payment_proof')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="client_notes" class="form-label fw-bold">Additional Notes</label>
                                        <textarea class="form-control" id="client_notes" name="client_notes" rows="4" 
                                                  placeholder="Any additional information about your payment...">{{ old('client_notes') }}</textarea>
                                        @error('client_notes')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-upload"></i> Submit Payment Proof
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Payment History -->
            @if($payment->attachment)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">Payment Proof</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Submitted:</strong> {{ $payment->updated_at->format('M d, Y g:i A') }}</p>
                                <p><strong>File:</strong> {{ $payment->attachment->original_name }}</p>
                                <a href="{{ Storage::url($payment->attachment->path) }}" target="_blank" class="btn btn-outline-primary">
                                    <i class="fas fa-download"></i> View Payment Proof
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
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

.table {
    margin-bottom: 0;
    font-size: 0.9rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.5em 0.75em;
}

.form-control, .form-select {
    border-radius: 0.5rem;
    border: 1px solid #dee2e6;
}

.form-control:focus, .form-select:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

.btn {
    border-radius: 0.5rem;
    font-weight: 500;
}
</style>
@endsection 