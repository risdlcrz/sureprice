@extends('layouts.app')

@push('styles')
<style>
    .content-wrapper {
        margin-left: 0;
        padding: 20px;
        min-height: 100vh;
        background-color: #f8f9fa;
    }

    .section-container {
        margin-bottom: 2rem;
        padding: 1.25rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        background-color: #fff;
    }

    .section-title {
        margin-bottom: 1.25rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #0d6efd;
        color: #344767;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #344767;
    }

    .progress {
        height: 0.5rem;
        margin-bottom: 2rem;
    }

    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
    }

    .step {
        text-align: center;
        flex: 1;
        position: relative;
    }

    .step:not(:last-child):after {
        content: '';
        position: absolute;
        top: 50%;
        right: 0;
        width: 100%;
        height: 2px;
        background: #dee2e6;
        z-index: 1;
    }

    .step.active:not(:last-child):after {
        background: #0d6efd;
    }

    .step.completed:not(:last-child):after {
        background: #198754;
    }

    .step-number {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #dee2e6;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
        position: relative;
        z-index: 2;
    }

    .step.active .step-number {
        background: #0d6efd;
    }

    .step.completed .step-number {
        background: #198754;
    }

    .step-label {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .step.active .step-label {
        color: #0d6efd;
        font-weight: 600;
    }

    .step.completed .step-label {
        color: #198754;
        font-weight: 600;
    }

    .review-section {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
    }

    .review-section h6 {
        color: #344767;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .review-item {
        margin-bottom: 0.5rem;
    }

    .review-label {
        font-weight: 500;
        color: #6c757d;
    }

    .review-value {
        color: #344767;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Create New Contract - Step 4</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Progress Steps -->
                        <div class="step-indicator">
                            <div class="step completed">
                                <div class="step-number">1</div>
                                <div class="step-label">Basic Information</div>
                            </div>
                            <div class="step completed">
                                <div class="step-number">2</div>
                                <div class="step-label">Scope & Materials</div>
                            </div>
                            <div class="step completed">
                                <div class="step-number">3</div>
                                <div class="step-label">Terms & Conditions</div>
                            </div>
                            <div class="step active">
                                <div class="step-number">4</div>
                                <div class="step-label">Payment & Review</div>
                            </div>
                        </div>

                        <!-- Review Section -->
                        <div class="section-container mb-4">
                            <h5 class="section-title">Contract Review</h5>
                            
                            <!-- Basic Information Review -->
                            <div class="review-section">
                                <h6>Basic Information</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="review-item">
                                            <span class="review-label">Contractor:</span>
                                            <span class="review-value">{{ session('contract_step1.contractor_name') }}</span>
                                        </div>
                                        <div class="review-item">
                                            <span class="review-label">Client:</span>
                                            <span class="review-value">{{ session('contract_step1.client_name') }}</span>
                                        </div>
                                        <div class="review-item">
                                            <span class="review-label">Property Type:</span>
                                            <span class="review-value">{{ ucfirst(session('contract_step1.property_type')) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="review-item">
                                            <span class="review-label">Project Timeline:</span>
                                            <span class="review-value">
                                                {{ \Carbon\Carbon::parse(session('contract_step2.start_date'))->format('M d, Y') }} to 
                                                {{ \Carbon\Carbon::parse(session('contract_step2.end_date'))->format('M d, Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Scope & Materials Review -->
                            <div class="review-section">
                                <h6>Scope & Materials</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Room/Area</th>
                                                <th>Area</th>
                                                <th>Scopes</th>
                                                <th>Materials Cost</th>
                                                <th>Labor Cost</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(session('contract_step2.rooms', []) as $room)
                                            <tr>
                                                <td>{{ $room['name'] }}</td>
                                                <td>{{ number_format($room['area'], 2) }} sq m</td>
                                                <td>
                                                    @foreach($room['scope'] as $scope)
                                                        {{ $scope }}<br>
                                                    @endforeach
                                                </td>
                                                <td>₱{{ number_format($room['materials_cost'] ?? 0, 2) }}</td>
                                                <td>₱{{ number_format($room['labor_cost'] ?? 0, 2) }}</td>
                                                <td>₱{{ number_format(($room['materials_cost'] ?? 0) + ($room['labor_cost'] ?? 0), 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Terms Review -->
                            <div class="review-section">
                                <h6>Terms & Conditions</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="review-item">
                                            <span class="review-label">Payment Terms:</span>
                                            <div class="review-value">
                                                {!! nl2br(e(session('contract_step3.payment_terms'))) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="review-item">
                                            <span class="review-label">Warranty Terms:</span>
                                            <div class="review-value">
                                                {!! nl2br(e(session('contract_step3.warranty_terms'))) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('contracts.store') }}" id="step4Form">
                            @csrf

                            <!-- Payment Details -->
                            <div class="section-container" id="paymentSection">
                                <h5 class="section-title">Payment Details</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="payment_method">Payment Method</label>
                                            <select class="form-control @error('payment_method') is-invalid @enderror" 
                                                id="payment_method" name="payment_method" required>
                                                <option value="">Select Payment Method</option>
                                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                                <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                            </select>
                                            @error('payment_method')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Bank Transfer Details -->
                                <div id="bankDetails" class="row mt-3" style="display: none;">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="bank_name">Bank Name</label>
                                            <input type="text" class="form-control @error('bank_name') is-invalid @enderror" 
                                                id="bank_name" name="bank_name" 
                                                value="{{ old('bank_name') }}">
                                            @error('bank_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="bank_account_name">Account Name</label>
                                            <input type="text" class="form-control @error('bank_account_name') is-invalid @enderror" 
                                                id="bank_account_name" name="bank_account_name" 
                                                value="{{ old('bank_account_name') }}">
                                            @error('bank_account_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="bank_account_number">Account Number</label>
                                            <input type="text" class="form-control @error('bank_account_number') is-invalid @enderror" 
                                                id="bank_account_number" name="bank_account_number" 
                                                value="{{ old('bank_account_number') }}">
                                            @error('bank_account_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Check Details -->
                                <div id="checkDetails" class="row mt-3" style="display: none;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="check_number">Check Number</label>
                                            <input type="text" class="form-control @error('check_number') is-invalid @enderror" 
                                                id="check_number" name="check_number" 
                                                value="{{ old('check_number') }}">
                                            @error('check_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="check_date">Check Date</label>
                                            <input type="date" class="form-control @error('check_date') is-invalid @enderror" 
                                                id="check_date" name="check_date" 
                                                value="{{ old('check_date') }}">
                                            @error('check_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden fields for costs -->
                                <input type="hidden" id="total_amount" name="total_amount" value="{{ session('contract_step2.total_amount') }}">
                                <input type="hidden" id="labor_cost" name="labor_cost" value="{{ session('contract_step2.labor_cost') }}">
                                <input type="hidden" id="materials_cost" name="materials_cost" value="{{ session('contract_step2.materials_cost') }}">
                            </div>

                            <div class="form-group mt-4">
                                <a href="{{ route('contracts.step3') }}" class="btn btn-secondary">Previous Step</a>
                                <button type="submit" class="btn btn-primary">Create Contract</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize payment method handling
    const paymentMethod = document.getElementById('payment_method');
    const bankDetails = document.getElementById('bankDetails');
    const checkDetails = document.getElementById('checkDetails');

    if (paymentMethod) {
        paymentMethod.addEventListener('change', function() {
            bankDetails.style.display = this.value === 'bank_transfer' ? 'flex' : 'none';
            checkDetails.style.display = this.value === 'check' ? 'flex' : 'none';
        });
        
        // Initialize on page load
        paymentMethod.dispatchEvent(new Event('change'));
    }

    // Form validation
    const form = document.getElementById('step4Form');
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>
@endpush 