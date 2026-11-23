@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4 page-title">Payment Dashboard</h1>

            @if(isset($error))
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ $error }}
                </div>
            @else

            <!-- Payment Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-money-bill-wave fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Total Payments</h5>
                            <h2 class="text-primary">{{ $totalPayments }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                            <h5 class="card-title">Pending</h5>
                            <h2 class="text-warning">{{ $pendingPayments }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Paid</h5>
                            <h2 class="text-success">{{ $paidPayments }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
                            <h5 class="card-title">Total Amount</h5>
                            <h2 class="text-info">₱{{ number_format($totalAmount, 2) }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Progress -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Payment Progress</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Paid Amount</span>
                                    <span>₱{{ number_format($paidAmount, 2) }}</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $totalAmount > 0 ? ($paidAmount / $totalAmount) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Pending Amount</span>
                                    <span>₱{{ number_format($pendingAmount, 2) }}</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-warning" role="progressbar" 
                                         style="width: {{ $totalAmount > 0 ? ($pendingAmount / $totalAmount) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('client.payments') }}" class="btn btn-primary">
                                    <i class="fas fa-list"></i> View All Payments
                                </a>
                                @if($pendingPayments > 0)
                                    <a href="{{ route('client.payments') }}" class="btn btn-warning">
                                        <i class="fas fa-credit-card"></i> Pay Pending Amounts
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Payments -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Payments</h5>
                </div>
                <div class="card-body">
                    @if($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Payment #</th>
                                        <th>Contract</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments->take(10) as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_number }}</td>
                                            <td>{{ $payment->contract->title ?? 'Contract #' . $payment->contract_id }}</td>
                                            <td>₱{{ number_format($payment->amount, 2) }}</td>
                                            <td>{{ $payment->due_date ? $payment->due_date->format('M d, Y') : 'N/A' }}</td>
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
                                            <td>
                                                <a href="{{ route('client.payments.show', $payment) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                            <h5>No payments yet</h5>
                            <p class="text-muted">Payments appear here once you have an approved contract.</p>
                            <a href="{{ route('client.quotation.create') }}" class="btn btn-success"><i class="fas fa-plus me-1"></i> Request a quotation</a>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@vite('resources/css/pages/client-payments-dashboard.css')
@endsection 