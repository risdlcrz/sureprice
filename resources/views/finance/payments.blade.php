@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h3 mb-4 text-gray-800 text-center" style="font-weight:700;color:#198754;letter-spacing:0.01em;">Finance Payments Dashboard</h1>
    
    <!-- Contract Payments Section -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Contract Payments</h5>
            <div>
                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#searchContractModal">
                    <i class="fas fa-search"></i> Search Contract
                </button>
                <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#allContractsModal">
                    <i class="fas fa-list"></i> View All Contracts
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($contractsWithPayments->count() > 0)
                @foreach($contractsWithPayments as $contractData)
                    @php
                        $contract = $contractData->contract;
                        $nextDue = $contractData->nextDue;
                    @endphp
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Contract #{{ $contract->contract_number ?? $contract->id }} - {{ $contract->title ?? 'Contract for ' . ($contract->client->name ?? 'Client') }}</h6>
                            <div>
                                @if($contractData->verificationAmount > 0)
                                    <span class="badge bg-info">For Verification: ₱{{ number_format($contractData->verificationAmount, 2) }}</span>
                                @endif
                                @if($contractData->pendingAmount > 0)
                                    <span class="badge bg-warning">Pending: ₱{{ number_format($contractData->pendingAmount, 2) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Client:</strong> {{ $contract->client->name ?? 'N/A' }}<br>
                                    <strong>Contractor:</strong> {{ $contract->contractor->name ?? 'N/A' }}<br>
                                    <strong>Total Contract Amount:</strong> ₱{{ number_format($contract->total_amount, 2) }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Paid Amount:</strong> ₱{{ number_format($contractData->paidAmount, 2) }}<br>
                                    <strong>Pending Amount:</strong> ₱{{ number_format($contractData->pendingAmount, 2) }}<br>
                                    @if($nextDue)
                                        <strong>Next Due:</strong> ₱{{ number_format($nextDue->amount, 2) }} on {{ $nextDue->due_date->format('M d, Y') }}
                                        @if($nextDue->due_date->isPast())
                                            <span class="badge bg-danger">Overdue</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Payment Stage</th>
                                            <th>Amount</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($contractData->payments as $payment)
                                            <tr>
                                                <td>{{ $payment->payment_type }}</td>
                                                <td>₱{{ number_format($payment->amount, 2) }}</td>
                                                <td>{{ $payment->due_date->format('M d, Y') }}</td>
                                                <td>
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
                                                </td>
                                                <td>
                                                    @if($payment->status === 'for_verification')
                                                        <a href="{{ route('payments.show', $payment) }}" class="btn btn-sm btn-info">Review</a>
                                                    @elseif($payment->status === 'pending')
                                                        <a href="{{ route('payments.show', $payment) }}" class="btn btn-sm btn-warning">Track</a>
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
            @else
                <div class="alert alert-info">No contract payments requiring attention.</div>
            @endif
        </div>
    </div>

    <!-- Purchase Order Payments Section -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Purchase Order Payments</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
        <thead>
            <tr>
                <th>PO Number</th>
                <th>Supplier</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendingPOs as $po)
                <tr>
                    <td>{{ $po->po_number }}</td>
                    <td>{{ $po->supplier->company_name ?? 'N/A' }}</td>
                    <td>₱{{ number_format($po->total_amount, 2) }}</td>
                    <td>{{ ucfirst($po->status) }}</td>
                    <td>
                        @if(auth()->user()->role === 'finance')
                        <!-- Pay Button triggers modal -->
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#payModal-{{ $po->id }}">
                            Pay
                        </button>
                        <!-- Payment Modal -->
                        <div class="modal fade" id="payModal-{{ $po->id }}" tabindex="-1" aria-labelledby="payModalLabel-{{ $po->id }}" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <form action="{{ route('purchase-order-payments.store', $po->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-header">
                                  <h5 class="modal-title" id="payModalLabel-{{ $po->id }}">Pay Purchase Order #{{ $po->po_number }}</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <div class="mb-3">
                                    <label for="amount-{{ $po->id }}" class="form-label">Amount</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="amount-{{ $po->id }}" name="amount" value="{{ $po->total_amount }}" required>
                                  </div>
                                  <div class="mb-3">
                                    <label for="payment_method-{{ $po->id }}" class="form-label">Payment Method</label>
                                    <select class="form-select" id="payment_method-{{ $po->id }}" name="payment_method" required>
                                      <option value="">Select method</option>
                                      <option value="bank_transfer">Bank Transfer</option>
                                      <option value="check">Check</option>
                                      <option value="cash">Cash</option>
                                    </select>
                                  </div>
                                  <div class="mb-3">
                                    <label for="admin_reference_number-{{ $po->id }}" class="form-label">Reference Number</label>
                                    <input type="text" class="form-control" id="admin_reference_number-{{ $po->id }}" name="admin_reference_number" required>
                                  </div>
                                  <div class="mb-3">
                                    <label for="admin_paid_date-{{ $po->id }}" class="form-label">Payment Date</label>
                                    <input type="date" class="form-control" id="admin_paid_date-{{ $po->id }}" name="admin_paid_date" required>
                                  </div>
                                  <div class="mb-3">
                                    <label for="admin_proof-{{ $po->id }}" class="form-label">Upload Proof</label>
                                    <input type="file" class="form-control" id="admin_proof-{{ $po->id }}" name="admin_proof" accept=".jpg,.jpeg,.png,.pdf" required>
                                  </div>
                                  <div class="mb-3">
                                    <label for="admin_notes-{{ $po->id }}" class="form-label">Notes</label>
                                    <textarea class="form-control" id="admin_notes-{{ $po->id }}" name="admin_notes" rows="2"></textarea>
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                  <button type="submit" class="btn btn-success">Submit Payment</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                                <td colspan="5" class="text-center">No pending purchase order payments.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
        </div>
    </div>
</div>

<!-- Search Contract Modal -->
<div class="modal fade" id="searchContractModal" tabindex="-1" aria-labelledby="searchContractModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchContractModalLabel">Search Contract</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="contractSearch" class="form-label">Search by Contract Number or Client Name</label>
                    <input type="text" class="form-control" id="contractSearch" placeholder="Enter contract number or client name...">
                </div>
                <div id="searchResults" class="mt-3">
                    <!-- Search results will be populated here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- All Contracts Modal -->
<div class="modal fade" id="allContractsModal" tabindex="-1" aria-labelledby="allContractsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="allContractsModalLabel">All Contracts with Payments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Contract #</th>
                                <th>Client</th>
                                <th>Contractor</th>
                                <th>Total Amount</th>
                                <th>Payment Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allContracts as $contract)
                                @php
                                    $payments = $contract->payments;
                                    $totalAmount = $payments->sum('amount');
                                    $paidAmount = $payments->where('status', 'paid')->sum('amount');
                                    $pendingAmount = $payments->where('status', 'pending')->sum('amount');
                                    $verificationAmount = $payments->where('status', 'for_verification')->sum('amount');
                                @endphp
                                <tr>
                                    <td>{{ $contract->contract_number ?? 'Contract #' . $contract->id }}</td>
                                    <td>{{ $contract->client->name ?? 'N/A' }}</td>
                                    <td>{{ $contract->contractor->name ?? 'N/A' }}</td>
                                    <td>₱{{ number_format($totalAmount, 2) }}</td>
                                    <td>
                                        @if($paidAmount == $totalAmount)
                                            <span class="badge bg-success">Fully Paid</span>
                                        @elseif($verificationAmount > 0)
                                            <span class="badge bg-info">For Verification: ₱{{ number_format($verificationAmount, 2) }}</span>
                                        @elseif($pendingAmount > 0)
                                            <span class="badge bg-warning">Pending: ₱{{ number_format($pendingAmount, 2) }}</span>
                                        @else
                                            <span class="badge bg-secondary">No Payments</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('payments.show', $contract->payments->first()) }}" class="btn btn-sm btn-primary">View Payments</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Search functionality
document.getElementById('contractSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const contracts = @json($allContracts);
    const resultsDiv = document.getElementById('searchResults');
    
    if (searchTerm.length < 2) {
        resultsDiv.innerHTML = '';
        return;
    }
    
    const filteredContracts = contracts.filter(contract => {
        const contractNumber = (contract.contract_number || 'Contract #' + contract.id).toLowerCase();
        const clientName = (contract.client?.name || '').toLowerCase();
        return contractNumber.includes(searchTerm) || clientName.includes(searchTerm);
    });
    
    if (filteredContracts.length === 0) {
        resultsDiv.innerHTML = '<div class="alert alert-info">No contracts found matching your search.</div>';
        return;
    }
    
    let html = '<div class="list-group">';
    filteredContracts.forEach(contract => {
        const payments = contract.payments;
        const totalAmount = payments.reduce((sum, p) => sum + parseFloat(p.amount), 0);
        const paidAmount = payments.filter(p => p.status === 'paid').reduce((sum, p) => sum + parseFloat(p.amount), 0);
        const pendingAmount = payments.filter(p => p.status === 'pending').reduce((sum, p) => sum + parseFloat(p.amount), 0);
        
        html += `
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">${contract.contract_number || 'Contract #' + contract.id}</h6>
                        <small>Client: ${contract.client?.name || 'N/A'} | Contractor: ${contract.contractor?.name || 'N/A'}</small>
                    </div>
                    <div class="text-end">
                        <div>₱${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
                        <small class="text-muted">Pending: ₱${pendingAmount.toLocaleString('en-US', {minimumFractionDigits: 2})}</small>
                    </div>
                    <a href="/payments/${contract.payments[0].id}" class="btn btn-sm btn-primary">View</a>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    resultsDiv.innerHTML = html;
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
.table {
    border-radius: 1.25rem;
    box-shadow: 0 8px 32px 0 rgba(44,62,80,0.10), 0 1.5px 6px rgba(44,62,80,0.04);
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(8px);
    overflow: hidden;
    margin-bottom: 2rem;
    font-size: 1.05em;
}
.table th {
    font-weight: 600;
    color: #495057;
    background: #f8fafc;
    border-top: none;
    text-align: center;
}
.table-hover tbody tr:hover {
    background: #f4faff;
    transition: background 0.2s;
}
.table td, .table th {
    vertical-align: middle;
    text-align: center;
}
.btn-success, .btn-primary {
    background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%) !important;
    color: #fff !important;
    border: none;
    font-weight: 600;
    border-radius: 2rem;
    padding: 0.5em 1.5em;
    font-size: 1.08em;
    letter-spacing: 0.01em;
    box-shadow: 0 2px 8px #43e97b22;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.btn-success:hover, .btn-primary:hover {
    background: linear-gradient(90deg, #38f9d7 0%, #43e97b 100%) !important;
    color: #fff;
    box-shadow: 0 4px 16px #43e97b44;
}
.btn-secondary {
    border-radius: 2rem;
    font-weight: 600;
    padding: 0.5em 1.5em;
    font-size: 1.08em;
}
.form-select, .form-control {
    padding: 0.7rem 1.2rem;
    border-radius: 1.1rem;
    border: 1.5px solid #ced4da;
    font-size: 1.08em;
    background: #f8fafc;
    transition: border 0.2s, box-shadow 0.2s;
}
.form-select:focus, .form-control:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem #19875422;
}
.badge {
    font-size: 0.95em;
    padding: 0.5em 1em;
    border-radius: 0.7em;
    font-weight: 600;
    letter-spacing: 0.01em;
    box-shadow: 0 1px 4px #38b6ff22;
}
</style>
@endpush 