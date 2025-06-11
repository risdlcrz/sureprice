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
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
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
                                            <span class="review-value">{{ session('contract_step1.contractor_name', 'Not provided') }}</span>
                                        </div>
                                        <div class="review-item">
                                            <span class="review-label">Contractor Email:</span>
                                            <span class="review-value">{{ session('contract_step1.contractor_email', 'Not provided') }}</span>
                                        </div>
                                        <div class="review-item">
                                            <span class="review-label">Contractor Phone:</span>
                                            <span class="review-value">{{ session('contract_step1.contractor_phone', 'Not provided') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="review-item">
                                            <span class="review-label">Client:</span>
                                            <span class="review-value">{{ session('contract_step1.client_name', 'Not provided') }}</span>
                                        </div>
                                        <div class="review-item">
                                            <span class="review-label">Client Email:</span>
                                            <span class="review-value">{{ session('contract_step1.client_email', 'Not provided') }}</span>
                                        </div>
                                        <div class="review-item">
                                            <span class="review-label">Client Phone:</span>
                                            <span class="review-value">{{ session('contract_step1.client_phone', 'Not provided') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="review-item">
                                            <span class="review-label">Property Type:</span>
                                            <span class="review-value">{{ ucfirst(session('contract_step1.property_type', 'Not provided')) }}</span>
                                        </div>
                                        <div class="review-item">
                                            <span class="review-label">Property Address:</span>
                                            <span class="review-value">
                                                {{ session('contract_step1.property_street', '') }}
                                                {{ session('contract_step1.property_city', '') }}
                                                {{ session('contract_step1.property_state', '') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="review-item">
                                            <span class="review-label">Project Timeline:</span>
                                            <span class="review-value">
                                                @if(session('contract_step2.start_date'))
                                                    {{ \Carbon\Carbon::parse(session('contract_step2.start_date'))->format('M d, Y') }} to 
                                                    {{ \Carbon\Carbon::parse(session('contract_step2.end_date'))->format('M d, Y') }}
                                                @else
                                                    Not provided
                                                @endif
                                            </span>
                                        </div>
                                        <div class="review-item">
                                            <span class="review-label">Total Contract Amount:</span>
                                            <span class="review-value">
                                                ₱{{ number_format(session('contract_step2.total_amount', 0), 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Scope & Materials Review -->
                            <div class="review-section">
                                <h6>Scope & Materials</h6>
                                @if(session('contract_step2.rooms') && count(session('contract_step2.rooms')) > 0)
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
                                            @foreach(session('contract_step2.rooms') as $room)
                                            <tr>
                                                <td>{{ $room['name'] ?? 'Unnamed Room' }}</td>
                                                <td>{{ number_format($room['area'] ?? 0, 2) }} sq m</td>
                                                <td>
                                                    @if(isset($room['scope']) && is_array($room['scope']))
                                                        @foreach($room['scope'] as $scope)
                                                            {{ $scope }}<br>
                                                        @endforeach
                                                    @else
                                                        No scopes selected
                                                    @endif
                                                </td>
                                                <td>₱{{ number_format($room['materials_cost'] ?? 0, 2) }}</td>
                                                <td>₱{{ number_format($room['labor_cost'] ?? 0, 2) }}</td>
                                                <td>₱{{ number_format(($room['materials_cost'] ?? 0) + ($room['labor_cost'] ?? 0), 2) }}</td>
                                            </tr>
                                            @endforeach
                                            <tr class="table-secondary">
                                                <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                                                <td>₱{{ number_format(session('contract_step2.materials_cost', 0), 2) }}</td>
                                                <td>₱{{ number_format(session('contract_step2.labor_cost', 0), 2) }}</td>
                                                <td>₱{{ number_format(session('contract_step2.total_amount', 0), 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="alert alert-warning">No room/scope information found</div>
                                @endif
                            </div>

                            <!-- Terms Review -->
                            <div class="review-section">
                                <h6>Terms & Conditions</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="review-item">
                                            <span class="review-label">Payment Terms:</span>
                                            <div class="review-value">
                                                {!! nl2br(e(session('contract_step3.payment_terms', 'No payment terms provided'))) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="review-item">
                                            <span class="review-label">Warranty Terms:</span>
                                            <div class="review-value">
                                                {!! nl2br(e(session('contract_step3.warranty_terms', 'No warranty terms provided'))) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="review-item">
                                            <span class="review-label">Cancellation Terms:</span>
                                            <div class="review-value">
                                                {!! nl2br(e(session('contract_step3.cancellation_terms', 'No cancellation terms provided'))) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="review-item">
                                            <span class="review-label">Additional Terms:</span>
                                            <div class="review-value">
                                                {!! nl2br(e(session('contract_step3.additional_terms', 'No additional terms provided'))) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('contracts.store') }}" id="step4Form">
                            @csrf
                            <input type="hidden" name="payment_terms" value="{{ session('contract_step3.payment_terms', '') }}">
                            <!-- Add hidden start/end date fields for JS -->
                            <input type="hidden" id="start_date" value="{{ session('contract_step2.start_date', '') }}">
                            <input type="hidden" id="end_date" value="{{ session('contract_step2.end_date', '') }}">

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
                                                <option value="cash" {{ old('payment_method', session('contract_step4.payment_method')) == 'cash' ? 'selected' : '' }}>Cash</option>
                                                <option value="check" {{ old('payment_method', session('contract_step4.payment_method')) == 'check' ? 'selected' : '' }}>Check</option>
                                                <option value="bank_transfer" {{ old('payment_method', session('contract_step4.payment_method')) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                            </select>
                                            @error('payment_method')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Bank Transfer Details -->
                                <div class="row g-3" id="bankDetails" style="display: none;">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="bank_name">Bank Name</label>
                                            <input type="text" class="form-control @error('bank_name') is-invalid @enderror" 
                                                id="bank_name" name="bank_name" 
                                                value="{{ old('bank_name', session('contract_step4.bank_name', '')) }}">
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
                                                value="{{ old('bank_account_name', session('contract_step4.bank_account_name', '')) }}">
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
                                                value="{{ old('bank_account_number', session('contract_step4.bank_account_number', '')) }}">
                                            @error('bank_account_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Check Details -->
                                <div class="row g-3 mt-0" id="checkDetails" style="display: none;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="check_number">Check Number</label>
                                            <input type="text" class="form-control @error('check_number') is-invalid @enderror" 
                                                id="check_number" name="check_number" 
                                                value="{{ old('check_number', session('contract_step4.check_number', '')) }}">
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
                                                value="{{ old('check_date', session('contract_step4.check_date', '')) }}">
                                            @error('check_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Schedule (Smart Table) -->
                                <div class="form-group mt-3">
                                    <label>Payment Schedule</label>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> Payment schedule will be automatically calculated based on project milestones.
                                    </div>
                                    <table class="table table-bordered" id="paymentScheduleTable">
                                        <thead>
                                            <tr>
                                                <th>Milestone</th>
                                                <th>Description</th>
                                                <th>Amount (%)</th>
                                                <th>Due Date</th>
                                                <th>Payment Due Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Will be populated by JavaScript -->
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2" class="text-end"><strong>Total:</strong></td>
                                                <td><span id="totalPercentage">100</span>%</td>
                                                <td colspan="2"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <input type="hidden" name="payment_schedule" id="payment_schedule">

                                    <!-- Only show these controls for milestone-based payments -->
                                    <div id="milestoneControls" style="display: none;">
                                        <div class="row g-3 mt-3">
                                            <div class="col-md-6">
                                                <label for="advance_payment_percentage">Advance Payment (%)</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="advance_payment_percentage" 
                                                           min="20" max="40" value="30">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <small class="text-muted">Recommended: 20-40%</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="retention_percentage">Retention (%)</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="retention_percentage" 
                                                           min="0" max="10" value="5">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <small class="text-muted">Maximum allowed: 10%</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <div id="payment-warnings"></div>
                                    </div>
                                </div>

                                <!-- Hidden fields for contract data -->
                                <input type="hidden" name="contract_data" value="{{ json_encode([
                                    'step1' => session('contract_step1', []),
                                    'step2' => session('contract_step2', []),
                                    'step3' => session('contract_step3', []),
                                    'step4' => session('contract_step4', [])
                                ]) }}">

                                <!-- Hidden fields for financial data -->
                                <input type="hidden" name="total_amount" value="{{ session('contract_step2.total_amount', 0) }}">
                                <input type="hidden" name="labor_cost" value="{{ session('contract_step2.labor_cost', 0) }}">
                                <input type="hidden" name="materials_cost" value="{{ session('contract_step2.materials_cost', 0) }}">

                                <!-- New fields -->
                                <div class="form-group mt-3">
                                    <label for="payment_due_days">Payment Due Days</label>
                                    <input type="number" class="form-control" id="payment_due_days" name="payment_due_days" min="0" value="0" required>
                                </div>

                                <div class="form-group mt-4">
                                    <a href="{{ route('contracts.step3') }}" class="btn btn-secondary">Previous Step</a>
                                    <button type="submit" class="btn btn-primary">Create Contract</button>
                                    <a href="{{ route('contracts.clear-session') }}" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel? All entered data will be lost.')">Cancel</a>
                                </div>
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
    const form = document.getElementById('step4Form');

    // Payment method change handler
        paymentMethod.addEventListener('change', function() {
        // Hide all payment details sections first
        bankDetails.style.display = 'none';
        checkDetails.style.display = 'none';

        // Show relevant section based on selection
            if (this.value === 'bank_transfer') {
            bankDetails.style.display = 'block';
            } else if (this.value === 'check') {
            checkDetails.style.display = 'block';
        }

        // Clear validation states
        clearValidation();
    });

    // Function to clear validation states
    function clearValidation() {
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    }

    // Form submission handler
        form.addEventListener('submit', function(e) {
            e.preventDefault();
        clearValidation();
            let isValid = true;

            // Validate payment method selection
            if (!paymentMethod.value) {
                isValid = false;
            showError(paymentMethod, 'Please select a payment method');
        }

            // Validate bank transfer fields
            if (paymentMethod.value === 'bank_transfer') {
                ['bank_name', 'bank_account_name', 'bank_account_number'].forEach(field => {
                    const input = document.getElementById(field);
                    if (!input.value.trim()) {
                        isValid = false;
                    showError(input, 'This field is required');
                    }
                });
            }

            // Validate check fields
            if (paymentMethod.value === 'check') {
                ['check_number', 'check_date'].forEach(field => {
                    const input = document.getElementById(field);
                    if (!input.value.trim()) {
                        isValid = false;
                    showError(input, 'This field is required');
                    }
                });
            }

        // Validate payment schedule
            const schedule = [];
            let total = 0;
        const scheduleTable = document.querySelector('#paymentScheduleTable tbody');
        if (scheduleTable) {
            scheduleTable.querySelectorAll('tr').forEach(row => {
                const milestone = row.cells[0].textContent.trim();
                const description = row.cells[1].textContent.trim();
                const amount = parseFloat(row.querySelector('.milestone-amount').value) || 0;
                const dueDate = row.querySelector('.milestone-date').value;

                if (!dueDate) {
                    isValid = false;
                    showError(row.querySelector('.milestone-date'), 'Due date is required');
                }

                if (amount <= 0) {
                    isValid = false;
                    showError(row.querySelector('.milestone-amount'), 'Amount must be greater than 0');
                }
                
                schedule.push({
                    milestone,
                    description,
                    amount,
                    due_date: dueDate
                });
                
                total += amount;
            });

            if (total !== 100) {
                isValid = false;
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger mt-3';
                errorDiv.textContent = 'Total payment schedule must add up to 100%';
                scheduleTable.parentNode.appendChild(errorDiv);
            }
        }

        // If all validations pass, submit the form
        if (isValid) {
            document.getElementById('payment_schedule').value = JSON.stringify(schedule);
                form.submit();
        }
    });

    // Helper function to show error messages
    function showError(input, message) {
        input.classList.add('is-invalid');
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = message;
        input.parentNode.appendChild(feedback);
    }

    // Auto-save functionality
    function saveFormData() {
        const formData = new FormData(form);
        const data = {
            payment_method: formData.get('payment_method'),
            bank_name: formData.get('bank_name'),
            bank_account_name: formData.get('bank_account_name'),
            bank_account_number: formData.get('bank_account_number'),
            check_number: formData.get('check_number'),
            check_date: formData.get('check_date'),
            payment_schedule: document.getElementById('payment_schedule').value
        };

        fetch('{{ route("contracts.save.step4") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Failed to save form data');
            }
        })
        .catch(error => {
            console.error('Error saving form data:', error);
        });
    }

    // Add auto-save event listeners
    paymentMethod.addEventListener('change', debounce(saveFormData, 1000));
    document.querySelectorAll('#bankDetails input, #checkDetails input').forEach(input => {
        input.addEventListener('change', debounce(saveFormData, 1000));
    });

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // --- Milestone & Payment Schedule Logic ---
    const advanceInput = document.getElementById('advance_payment_percentage');
    const retentionInput = document.getElementById('retention_percentage');
    const dueDaysInput = document.getElementById('payment_due_days');
    const scheduleTable = document.getElementById('paymentScheduleTable');
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    // Set default milestone percentages
    const defaultPercents = [30, 20, 20, 25, 5];
    function setDefaultMilestones() {
        // Force initialization of payment schedule based on payment terms
        initializePaymentSchedule();
        
        // Ensure all milestone amounts are readonly
        document.querySelectorAll('.milestone-amount').forEach(input => {
            input.setAttribute('readonly', 'readonly');
        });

        // Update dates and totals
        updateMilestoneDates();
        updateTotalPercentage();
    }

    function updateMilestonePercentages() {
        const rows = scheduleTable.querySelectorAll('tbody tr');
        let advance = parseInt(advanceInput.value) || 0;
        let retention = parseInt(retentionInput.value) || 0;

        // Clear existing warnings
        hideMilestoneWarning();

        // Validate retention (max 10%)
        if (retention > 10) {
            showMilestoneWarning('⚠️ Retention cannot exceed 10% of total contract value. Value has been adjusted.', 'warning');
            retentionInput.value = '10';
            retention = 10;
        }

        // Check if we should use default distribution (30-20-20-25-5)
        const defaultDistribution = [30, 20, 20, 25, 5];
        const isDefaultCase = advance === 30 && retention === 5;

        // Update all milestone amounts
        rows.forEach((row, index) => {
            const amountInput = row.querySelector('.milestone-amount');
            let value = 0;

            if (isDefaultCase) {
                // Use default distribution
                value = defaultDistribution[index];
            } else {
                if (index === 0) {
                    // Advance Payment
                    value = advance;
                } else if (index === rows.length - 1) {
                    // Retention
                    value = retention;
                } else {
                    // Middle milestone - distribute remaining evenly
                    const middleMilestones = rows.length - 2; // Exclude advance and retention
                    const remainingPercent = 100 - advance - retention;
                    let baseShare = Math.floor(remainingPercent / middleMilestones);
                    let extra = remainingPercent - (baseShare * middleMilestones);
                    
                    value = baseShare;
                    if (extra > 0 && index === rows.length - 2) {
                        value += extra; // Add any remainder to the last middle milestone
                    }
                }
            }
            
            // Set the value and make it read-only
            amountInput.value = value;
            amountInput.readOnly = true;
        });

        // Validate advance payment with proper range checks
        if (advance > 80) {
            showMilestoneWarning('⚠️ High advance payment detected. Consider reducing for better risk management.', 'warning');
        } else if (advance > 40) {
            showMilestoneWarning('⚠️ Advance payment is higher than typical range (20-40%).', 'warning');
        } else if (advance < 20 && advance > 0) {
            showMilestoneWarning('ℹ️ Low advance payment. Typical range is 20-40% of total value.', 'info');
        }

        updateTotalPercentage();
    }

    function updateTotalPercentage() {
        const rows = scheduleTable.querySelectorAll('tbody tr');
        let total = 0;
        
        rows.forEach(row => {
            total += parseInt(row.querySelector('.milestone-amount').value) || 0;
        });

        const totalElement = document.getElementById('totalPercentage');
        // Show total as whole number without decimals
        totalElement.textContent = total;
        
        // Visual feedback for total
        if (total !== 100) {
            totalElement.style.color = 'red';
            showMilestoneWarning('Total must be exactly 100%');
        } else {
            totalElement.style.color = '';
            hideMilestoneWarning();
        }
    }

    function showMilestoneWarning(msg, type = 'danger') {
        const warningsContainer = document.getElementById('payment-warnings');
        let warning = document.createElement('div');
        warning.className = `alert alert-${type} alert-dismissible fade show`;
        warning.innerHTML = `
            <i class="fas fa-exclamation-circle"></i> 
            ${msg}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Remove any existing warnings
        while (warningsContainer.firstChild) {
            warningsContainer.firstChild.remove();
        }
        
        warningsContainer.appendChild(warning);
    }
    function hideMilestoneWarning() {
        let warn = document.getElementById('milestone-warning');
        if (warn) warn.remove();
    }

    function addDays(dateStr, days) {
        const date = new Date(dateStr);
        date.setDate(date.getDate() + days);
        return date.toISOString().split('T')[0];
    }

    function updateMilestoneDates() {
        const rows = scheduleTable.querySelectorAll('tbody tr');
        const dueDays = parseInt(dueDaysInput.value) || 0;
        const milestoneCount = rows.length;
        const start = new Date(startDate);
        const end = new Date(endDate);
        const totalDays = Math.max(1, Math.round((end - start) / (1000 * 60 * 60 * 24)));
        let interval = Math.floor(totalDays / (milestoneCount - 1));
        rows.forEach((row, idx) => {
            let dueDate = '';
            if (idx === 0) {
                dueDate = startDate;
            } else if (idx === milestoneCount - 1) {
                dueDate = addDays(endDate, 30);
            } else {
                dueDate = addDays(startDate, interval * idx);
            }
            row.querySelector('.milestone-date').value = dueDate;
            row.querySelector('.payment-due-date').value = dueDate ? addDays(dueDate, dueDays) : '';
        });
    }

    // --- Event Listeners ---
    advanceInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (parseInt(this.value) > 100) this.value = '100';
        updateMilestonePercentages();
    });
    retentionInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        // Limit retention to 10%
        if (parseInt(this.value) > 10) this.value = '10';
        updateMilestonePercentages();
    });
    dueDaysInput.addEventListener('input', function() {
        updateMilestoneDates();
    });
    // Make other milestone-amount fields readonly
    scheduleTable.querySelectorAll('tbody tr').forEach((row, idx) => {
        if (idx !== 0 && idx !== scheduleTable.querySelectorAll('tbody tr').length - 1) {
            row.querySelector('.milestone-amount').setAttribute('readonly', 'readonly');
        }
    });

    // Initial sync on page load
    setDefaultMilestones();
    updateMilestoneDates();

    // If user changes start/end date (shouldn't happen here, but just in case)
    // document.getElementById('start_date').addEventListener('change', updateMilestoneDates);
    // document.getElementById('end_date').addEventListener('change', updateMilestoneDates);

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    function initializePaymentSchedule() {
        const paymentTerms = '{{ session('contract_step3.payment_terms') ?: session('step3_data.payment_terms') }}';
        console.log('Payment Terms:', paymentTerms);
        
        const scheduleTable = document.getElementById('paymentScheduleTable');
        if (!scheduleTable) {
            console.error('Payment schedule table not found');
            return;
        }
        const tbody = scheduleTable.querySelector('tbody');
        tbody.innerHTML = ''; // Clear existing rows

        // Hide milestone controls by default
        document.getElementById('milestoneControls').style.display = 'none';

        if (!paymentTerms) {
            console.log('No payment terms found in session');
            setDefaultMilestoneSchedule();
            return;
        }

        if (paymentTerms === 'Pay All In') {
            // Single payment
            tbody.innerHTML = `
                <tr>
                    <td>Full Payment</td>
                    <td>Payment upon contract signing</td>
                    <td><input type="number" class="form-control milestone-amount" value="100" min="0" max="100" readonly></td>
                    <td><input type="date" class="form-control milestone-date" required readonly></td>
                    <td><input type="text" class="form-control payment-due-date" readonly></td>
                </tr>`;
        } else if (paymentTerms.startsWith('Installment Plan:')) {
            // Extract downpayment and period from payment terms
            const match = paymentTerms.match(/(\d+)% downpayment, (\d+) months/);
            if (match) {
                const downpayment = parseInt(match[1]);
                const months = parseInt(match[2]);
                const monthlyPayment = Math.floor((100 - downpayment) / months);
                const remainder = 100 - downpayment - (monthlyPayment * months);

                // Add downpayment row
                tbody.innerHTML = `
                    <tr>
                        <td>Downpayment</td>
                        <td>Initial payment</td>
                        <td><input type="number" class="form-control milestone-amount" value="${downpayment}" min="0" max="100" readonly></td>
                        <td><input type="date" class="form-control milestone-date" required readonly></td>
                        <td><input type="text" class="form-control payment-due-date" readonly></td>
                    </tr>`;

                // Add monthly installment rows
                for (let i = 0; i < months; i++) {
                    const amount = i === months - 1 ? monthlyPayment + remainder : monthlyPayment;
                    tbody.innerHTML += `
                        <tr>
                            <td>Installment ${i + 1}</td>
                            <td>Monthly payment</td>
                            <td><input type="number" class="form-control milestone-amount" value="${amount}" min="0" max="100" readonly></td>
                            <td><input type="date" class="form-control milestone-date" required readonly></td>
                            <td><input type="text" class="form-control payment-due-date" readonly></td>
                        </tr>`;
                }
            }
        } else if (paymentTerms.startsWith('Progress Payment:')) {
            // Show milestone controls for progress payments
            document.getElementById('milestoneControls').style.display = 'block';
            
            if (paymentTerms.includes('70% completion')) {
                tbody.innerHTML = `
                    <tr>
                        <td>Initial Payment</td>
                        <td>Payment upon contract signing</td>
                        <td><input type="number" class="form-control milestone-amount" value="30" min="0" max="100" readonly></td>
                        <td><input type="date" class="form-control milestone-date" required readonly></td>
                        <td><input type="text" class="form-control payment-due-date" readonly></td>
                    </tr>
                    <tr>
                        <td>70% Completion</td>
                        <td>Payment at 70% project completion</td>
                        <td><input type="number" class="form-control milestone-amount" value="40" min="0" max="100" readonly></td>
                        <td><input type="date" class="form-control milestone-date" required readonly></td>
                        <td><input type="text" class="form-control payment-due-date" readonly></td>
                    </tr>
                    <tr>
                        <td>Completion</td>
                        <td>Payment upon project completion</td>
                        <td><input type="number" class="form-control milestone-amount" value="25" min="0" max="100" readonly></td>
                        <td><input type="date" class="form-control milestone-date" required readonly></td>
                        <td><input type="text" class="form-control payment-due-date" readonly></td>
                    </tr>
                    <tr>
                        <td>Retention</td>
                        <td>Retention payment</td>
                        <td><input type="number" class="form-control milestone-amount" value="5" min="0" max="100" readonly></td>
                        <td><input type="date" class="form-control milestone-date" required readonly></td>
                        <td><input type="text" class="form-control payment-due-date" readonly></td>
                    </tr>`;
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td>Initial Payment</td>
                        <td>Payment upon contract signing</td>
                        <td><input type="number" class="form-control milestone-amount" value="30" min="0" max="100" readonly></td>
                        <td><input type="date" class="form-control milestone-date" required readonly></td>
                        <td><input type="text" class="form-control payment-due-date" readonly></td>
                    </tr>
                    <tr>
                        <td>Completion</td>
                        <td>Payment upon project completion</td>
                        <td><input type="number" class="form-control milestone-amount" value="65" min="0" max="100" readonly></td>
                        <td><input type="date" class="form-control milestone-date" required readonly></td>
                        <td><input type="text" class="form-control payment-due-date" readonly></td>
                    </tr>
                    <tr>
                        <td>Retention</td>
                        <td>Retention payment</td>
                        <td><input type="number" class="form-control milestone-amount" value="5" min="0" max="100" readonly></td>
                        <td><input type="date" class="form-control milestone-date" required readonly></td>
                        <td><input type="text" class="form-control payment-due-date" readonly></td>
                    </tr>`;
            }
        } else {
            // Default milestone-based schedule
            setDefaultMilestoneSchedule();
        }

        // Update dates and totals after modifying the table
        updateMilestoneDates();
        updateTotalPercentage();
    }

    function setDefaultMilestoneSchedule() {
        // Show milestone controls for default schedule
        document.getElementById('milestoneControls').style.display = 'block';
        
        const tbody = document.getElementById('paymentScheduleTable').querySelector('tbody');
        tbody.innerHTML = `
            <tr>
                <td>Initial Payment</td>
                <td>Payment upon contract signing</td>
                <td><input type="number" class="form-control milestone-amount" value="30" min="0" max="100" readonly></td>
                <td><input type="date" class="form-control milestone-date" required readonly></td>
                <td><input type="text" class="form-control payment-due-date" readonly></td>
            </tr>
            <tr>
                <td>Materials Delivery</td>
                <td>Payment upon delivery of materials</td>
                <td><input type="number" class="form-control milestone-amount" value="20" min="0" max="100" readonly></td>
                <td><input type="date" class="form-control milestone-date" required readonly></td>
                <td><input type="text" class="form-control payment-due-date" readonly></td>
            </tr>
            <tr>
                <td>Work Start</td>
                <td>Payment upon commencement of work</td>
                <td><input type="number" class="form-control milestone-amount" value="20" min="0" max="100" readonly></td>
                <td><input type="date" class="form-control milestone-date" required readonly></td>
                <td><input type="text" class="form-control payment-due-date" readonly></td>
            </tr>
            <tr>
                <td>Completion</td>
                <td>Payment upon project completion</td>
                <td><input type="number" class="form-control milestone-amount" value="25" min="0" max="100" readonly></td>
                <td><input type="date" class="form-control milestone-date" required readonly></td>
                <td><input type="text" class="form-control payment-due-date" readonly></td>
            </tr>
            <tr>
                <td>Retention</td>
                <td>Retention payment (after warranty period)</td>
                <td><input type="number" class="form-control milestone-amount" value="5" min="0" max="100" readonly></td>
                <td><input type="date" class="form-control milestone-date" required readonly></td>
                <td><input type="text" class="form-control payment-due-date" readonly></td>
            </tr>`;
    }

    // Call initializePaymentSchedule when the page loads and when payment terms change
    document.addEventListener('DOMContentLoaded', function() {
        initializePaymentSchedule();
        
        // Also call it when payment terms change
        const paymentTermsInput = document.querySelector('input[name="payment_terms"]');
        if (paymentTermsInput) {
            paymentTermsInput.addEventListener('change', initializePaymentSchedule);
        }
    });

    // Save the schedule data before form submission
    document.getElementById('step4Form').addEventListener('submit', function(e) {
        const scheduleData = [];
        document.querySelectorAll('#paymentScheduleTable tbody tr').forEach(row => {
            scheduleData.push({
                milestone: row.cells[0].textContent,
                description: row.cells[1].textContent,
                percentage: row.querySelector('.milestone-amount').value,
                date: row.querySelector('.milestone-date').value,
                dueDate: row.querySelector('.payment-due-date').value
            });
        });
        document.getElementById('payment_schedule').value = JSON.stringify(scheduleData);
    });
});
</script>
@endpush