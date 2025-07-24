@push('styles')
<link rel="stylesheet" href="{{ asset('css/workflow.css') }}">
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const progressBar = document.querySelector('.progress-bar');
    const progress = progressBar.getAttribute('data-progress');
    progressBar.style.width = progress + '%';
});
</script>
@endpush

<div class="card mt-4">
    <div class="card-header">
        <h5>Workflow Status</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="progress">
                    @php
                        $progress = match($contract->getWorkflowStatus()) {
                            'Pending Material Request' => 0,
                            'Pending Stock Check' => 15,
                            'Pending Admin Approval' => 30,
                            'Pending Supplier Approval' => 45,
                            'Pending Payment Validation' => 60,
                            'Pending Delivery' => 75,
                            'Pending Delivery Confirmation' => 90,
                            'Completed' => 100,
                            default => 0
                        };
                    @endphp
                    <div 
                        class="progress-bar bg-success text-white progress-{{ $progress }}"
                        role="progressbar"
                        aria-valuenow="{{ $progress }}"
                        aria-valuemin="0"
                        aria-valuemax="100"
                    >{{ $contract->getWorkflowStatus() }}</div>
                </div>

                <div class="workflow-steps">
                    <!-- Material Request -->
                    <div class="step {{ $contract->material_request_status ? 'completed' : 'pending' }}">
                        <h6>Material Request</h6>
                        <p>Status: {{ $contract->material_request_status ?? 'Not Started' }}</p>
                        @if(!$contract->material_request_status)
                            <a href="{{ route('material-requests.create', ['contract_id' => $contract->id]) }}" 
                               class="btn btn-primary btn-sm">
                                Create Material Request
                            </a>
                        @endif
                    </div>

                    <!-- Stock Check -->
                    @if($contract->material_request_status)
                    <div class="step {{ $contract->stock_checked_at ? 'completed' : 'pending' }}">
                        <h6>Stock Check</h6>
                        @if($contract->stock_check_results)
                            <div class="stock-results">
                                <h7>Stock Levels:</h7>
                                <pre>{{ json_encode($contract->stock_check_results, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        @endif
                        @if(!$contract->stock_checked_at)
                            <button class="btn btn-primary btn-sm" onclick="checkStock()">Check Stock</button>
                        @endif
                    </div>
                    @endif

                    <!-- Approval Process -->
                    @if($contract->stock_checked_at)
                    <div class="step">
                        <h6>Approvals</h6>
                        <div class="approval-status">
                            <p>Admin: 
                                <span class="badge bg-{{ $contract->isAdminApproved() ? 'success' : 'warning' }}">
                                    {{ $contract->admin_approval_status }}
                                </span>
                            </p>
                            <p>Supplier: 
                                <span class="badge bg-{{ $contract->isSupplierApproved() ? 'success' : 'warning' }}">
                                    {{ $contract->supplier_approval_status }}
                                </span>
                            </p>
                        </div>
                        @if(Auth::user()->hasRole('manager') && $contract->status === 'draft')
                            <form method="POST" action="{{ route('contracts.requestApproval', $contract) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-paper-plane"></i> Request for Admin Approval
                                </button>
                            </form>
                        @endif
                        @if(Auth::user()->hasRole('admin') && !$contract->isAdminApproved())
                            <button class="btn btn-success btn-sm" onclick="adminApprove()">Admin Approve</button>
                            <button class="btn btn-danger btn-sm" onclick="adminReject()">Admin Reject</button>
                        @elseif($contract->admin_approval_status === 'pending')
                            <span class="badge bg-warning">Approval Pending</span>
                        @endif
                        @if(Auth::user()->hasRole('supplier') && $contract->isAdminApproved() && !$contract->isSupplierApproved())
                            <button class="btn btn-success btn-sm" onclick="supplierApprove()">Supplier Approve</button>
                        @endif
                    </div>
                    @endif

                    <!-- Payment Validation -->
                    @if($contract->isFullyApproved())
                    <div class="step">
                        <h6>Payment Validation</h6>
                        <div class="payment-status">
                            <p>Admin Validation: 
                                <span class="badge bg-{{ $contract->admin_payment_validated_at ? 'success' : 'warning' }}">
                                    {{ $contract->admin_payment_validated_at ? 'Validated' : 'Pending' }}
                                </span>
                            </p>
                            <p>Supplier Validation: 
                                <span class="badge bg-{{ $contract->supplier_payment_validated_at ? 'success' : 'warning' }}">
                                    {{ $contract->supplier_payment_validated_at ? 'Validated' : 'Pending' }}
                                </span>
                            </p>
                        </div>
                        @if((Auth::user()->hasRole('admin') || Auth::user()->hasRole('finance')) && !$contract->admin_payment_validated_at)
                            <button class="btn btn-primary btn-sm" onclick="validatePaymentAdmin()">
                                Validate Payment (Admin/Finance)
                            </button>
                        @endif
                        @if(Auth::user()->hasRole('supplier') && $contract->admin_payment_validated_at && !$contract->supplier_payment_validated_at)
                            <button class="btn btn-primary btn-sm" onclick="validatePaymentSupplier()">
                                Validate Payment (Supplier)
                            </button>
                        @endif
                    </div>
                    @endif

                    <!-- Delivery -->
                    @if($contract->isPaymentValidated())
                    <div class="step">
                        <h6>Delivery</h6>
                        <p>Status: {{ $contract->delivery_status ?? 'Not Started' }}</p>
                        @if(!$contract->delivery_status)
                            <button class="btn btn-primary btn-sm" onclick="createDelivery()">Create Delivery</button>
                        @endif
                        @if($contract->delivery_status === 'pending_confirmation')
                            <button class="btn btn-success btn-sm" onclick="confirmDelivery()">Confirm Delivery</button>
                        @endif
                    </div>
                    @endif

                    <!-- Stock Update -->
                    @if($contract->isDeliveryConfirmed() && !$contract->stock_updated)
                    <div class="step">
                        <h6>Stock Update</h6>
                        <p>Status: {{ $contract->stock_updated ? 'Updated' : 'Pending Update' }}</p>
                        @if(!$contract->stock_updated)
                            <button class="btn btn-primary btn-sm" onclick="updateStock()">Update Stock</button>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div> 