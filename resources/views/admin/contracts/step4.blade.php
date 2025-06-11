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
                            <input type="hidden" name="payment_schedule" id="payment_schedule">
                            <input type="hidden" name="start_date" id="start_date" value="{{ session('contract_step2.start_date', '') }}">
                            <input type="hidden" name="end_date" id="end_date" value="{{ session('contract_step2.end_date', '') }}">

                            <!-- Payment Due Days -->
                            <div class="section-container">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="payment_due_days">Payment Due Days</label>
                                            <input type="number" class="form-control" id="payment_due_days" name="payment_due_days" 
                                                value="{{ old('payment_due_days', session('contract_step4.payment_due_days', 5)) }}" 
                                                min="0" max="30">
                                            <small class="text-muted">Number of days after milestone date when payment becomes due</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="section-container mt-4">
                                <h5 class="section-title">Payment Method</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="payment_method">Select Payment Method</label>
                                            <select class="form-control" id="payment_method" name="payment_method" required>
                                                <option value="">Select Payment Method</option>
                                                <option value="bank_transfer" {{ old('payment_method', session('contract_step4.payment_method')) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                <option value="check" {{ old('payment_method', session('contract_step4.payment_method')) == 'check' ? 'selected' : '' }}>Check</option>
                                                <option value="cash" {{ old('payment_method', session('contract_step4.payment_method')) == 'cash' ? 'selected' : '' }}>Cash</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bank Transfer Details -->
                                <div id="bankTransferDetails" class="row mt-3" style="display: none;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bank_name">Bank Name</label>
                                            <input type="text" class="form-control" id="bank_name" name="bank_name" 
                                                value="{{ old('bank_name', session('contract_step4.bank_name')) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bank_account_name">Account Name</label>
                                            <input type="text" class="form-control" id="bank_account_name" name="bank_account_name" 
                                                value="{{ old('bank_account_name', session('contract_step4.bank_account_name')) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <div class="form-group">
                                            <label for="bank_account_number">Account Number</label>
                                            <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" 
                                                value="{{ old('bank_account_number', session('contract_step4.bank_account_number')) }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Check Details -->
                                <div id="checkDetails" class="row mt-3" style="display: none;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="check_number">Check Number</label>
                                            <input type="text" class="form-control" id="check_number" name="check_number" 
                                                value="{{ old('check_number', session('contract_step4.check_number')) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="check_date">Check Date</label>
                                            <input type="date" class="form-control" id="check_date" name="check_date" 
                                                value="{{ old('check_date', session('contract_step4.check_date')) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Schedule -->
                            <div class="section-container mt-4">
                                <h5 class="section-title">Payment Schedule</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Payment Terms</label>
                                            <div class="payment-terms-display mb-3">
                                                <strong>Selected Payment Terms:</strong> 
                                                <span id="selectedPaymentTerms">{{ session('contract_step3.payment_terms', '') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="paymentScheduleTable">
                                                <thead>
                                                    <tr>
                                                        <th>Payment Stage</th>
                                                        <th>Milestone Date</th>
                                                        <th>Payment Due Date</th>
                                                        <th>Amount (₱)</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Will be populated dynamically -->
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                                        <td><strong id="totalAmount">₱0.00</strong></td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
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

                            <div class="form-group mt-4">
                                <a href="{{ route('contracts.step3') }}" class="btn btn-secondary">Previous Step</a>
                                <button type="submit" class="btn btn-primary">Create Contract</button>
                                <a href="{{ route('contracts.clear-session') }}" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel? All entered data will be lost.')">Cancel</a>
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
    const paymentTerms = document.getElementById('selectedPaymentTerms').textContent;
    const totalProjectAmount = parseFloat('{{ session("contract_step2.grand_total", 0) }}');
    const startDate = new Date('{{ session("contract_step2.start_date") }}');
    const endDate = new Date('{{ session("contract_step2.end_date") }}');
    const paymentDueDaysInput = document.getElementById('payment_due_days');
    
    // Payment method handling
    const paymentMethodSelect = document.getElementById('payment_method');
    const bankTransferDetails = document.getElementById('bankTransferDetails');
    const checkDetails = document.getElementById('checkDetails');

    function togglePaymentDetails() {
        const selectedMethod = paymentMethodSelect.value;
        
        // Hide all details sections first
        bankTransferDetails.style.display = 'none';
        checkDetails.style.display = 'none';
        
        // Show the appropriate section based on selection
        if (selectedMethod === 'bank_transfer') {
            bankTransferDetails.style.display = 'block';
        } else if (selectedMethod === 'check') {
            checkDetails.style.display = 'block';
        }
    }

    // Add change event listener for payment method
    paymentMethodSelect.addEventListener('change', function() {
        togglePaymentDetails();
        
        // Auto-save the payment method selection
        const data = {
            payment_method: this.value,
            bank_name: document.getElementById('bank_name').value,
            bank_account_name: document.getElementById('bank_account_name').value,
            bank_account_number: document.getElementById('bank_account_number').value,
            check_number: document.getElementById('check_number').value,
            check_date: document.getElementById('check_date').value
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
        .catch(error => console.error('Error saving data:', error));
    });

    // Add change event listeners for bank transfer and check details
    ['bank_name', 'bank_account_name', 'bank_account_number', 'check_number', 'check_date'].forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('change', function() {
                const data = {
                    payment_method: paymentMethodSelect.value,
                    bank_name: document.getElementById('bank_name').value,
                    bank_account_name: document.getElementById('bank_account_name').value,
                    bank_account_number: document.getElementById('bank_account_number').value,
                    check_number: document.getElementById('check_number').value,
                    check_date: document.getElementById('check_date').value
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
                .catch(error => console.error('Error saving data:', error));
            });
        }
    });

    // Initialize payment details visibility
    togglePaymentDetails();
    
    const tableBody = document.querySelector('#paymentScheduleTable tbody');
    const scheduleData = [];
    
    function calculateDueDate(milestoneDate, dueDays) {
        const dueDate = new Date(milestoneDate);
        dueDate.setDate(dueDate.getDate() + parseInt(dueDays));
        return dueDate;
    }
    
    function addPaymentRow(stage, milestoneDate, amount, status = 'Pending') {
        const dueDays = parseInt(paymentDueDaysInput.value) || 0;
        const dueDate = calculateDueDate(milestoneDate, dueDays);
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${stage}</td>
            <td>${milestoneDate.toISOString().split('T')[0]}</td>
            <td>${dueDate.toISOString().split('T')[0]}</td>
            <td>₱${amount.toFixed(2)}</td>
            <td>${status}</td>
        `;
        tableBody.appendChild(row);
        
        scheduleData.push({
            stage: stage,
            milestone_date: milestoneDate.toISOString().split('T')[0],
            due_date: dueDate.toISOString().split('T')[0],
            amount: amount,
            status: status
        });
    }
    
    function clearTable() {
        tableBody.innerHTML = '';
        scheduleData.length = 0;
    }
    
    function updateTotalAmount() {
        const total = scheduleData.reduce((sum, item) => sum + item.amount, 0);
        document.getElementById('totalAmount').textContent = `₱${total.toFixed(2)}`;
    }
    
    function generateSchedule() {
        clearTable();
        
        if (paymentTerms.includes('Pay All In')) {
            // Single payment at project completion
            addPaymentRow('Full Payment', endDate, totalProjectAmount);
        }
        else if (paymentTerms.includes('Progress Payment')) {
            // Progress payment with advance payment and retention
            const advancePayment = totalProjectAmount * 0.15; // 15% advance payment
            const retention = totalProjectAmount * 0.10; // 10% retention
            const progressPayment = totalProjectAmount - advancePayment - retention;
            
            // Add advance payment (due at start)
            addPaymentRow('Advance Payment (15%)', startDate, advancePayment);
            
            // Add progress payment (due at 70% completion or completion based on terms)
            const progressDueDate = new Date(startDate);
            const totalDays = (endDate - startDate) / (1000 * 60 * 60 * 24);
            
            if (paymentTerms.includes('70% completion')) {
                progressDueDate.setDate(startDate.getDate() + Math.ceil(totalDays * 0.7));
                addPaymentRow('Progress Payment (75%)', progressDueDate, progressPayment);
            } else {
                progressDueDate.setDate(endDate.getDate());
                addPaymentRow('Progress Payment (75%)', progressDueDate, progressPayment);
            }
            
            // Add retention (due 30 days after completion)
            const retentionDueDate = new Date(endDate);
            retentionDueDate.setDate(endDate.getDate() + 30);
            addPaymentRow('Retention (10%)', retentionDueDate, retention);
        }
        else if (paymentTerms.includes('Installment')) {
            // Parse installment terms
            const match = paymentTerms.match(/(\d+)% downpayment, (\d+) months/);
            if (match) {
                const downpaymentPercent = parseInt(match[1]);
                const months = parseInt(match[2]);
                
                const downpayment = (totalProjectAmount * downpaymentPercent) / 100;
                const remainingAmount = totalProjectAmount - downpayment;
                const monthlyPayment = remainingAmount / months;
                
                // Add downpayment
                addPaymentRow(`Downpayment (${downpaymentPercent}%)`, startDate, downpayment);
                
                // Add monthly installments
                const installmentDate = new Date(startDate);
                for (let i = 1; i <= months; i++) {
                    installmentDate.setMonth(installmentDate.getMonth() + 1);
                    addPaymentRow(`Installment ${i}`, new Date(installmentDate), monthlyPayment);
                }
            }
        }
        
        updateTotalAmount();
        
        // Store payment schedule in hidden input
        document.getElementById('payment_schedule').value = JSON.stringify(scheduleData);
    }
    
    // Generate initial schedule
    generateSchedule();
    
    // Update schedule when payment due days changes
    paymentDueDaysInput.addEventListener('change', function() {
        generateSchedule();
    });

    // Auto-save when payment due days changes
    paymentDueDaysInput.addEventListener('change', function() {
        const data = {
            payment_due_days: this.value,
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
        .catch(error => console.error('Error saving data:', error));
    });
});
</script>
@endpush