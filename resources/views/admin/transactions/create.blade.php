@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Add Transaction</h1>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('transactions.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="contract_id" class="form-label">Contract</label>
            <select name="contract_id" id="contract_id" class="form-select" required>
                <option value="">Select Contract</option>
                @foreach($contracts as $contract)
                    <option value="{{ $contract->id }}" {{ old('contract_id', $contract_id ?? '') == $contract->id ? 'selected' : '' }}>
                        {{ $contract->contract_number }}
                    </option>
                @endforeach
            </select>
            <div id="contract-info" class="mt-2 text-info" style="display:none;"></div>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ old('date') }}" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <input type="text" name="description" id="description" class="form-control" value="{{ old('description') }}" required>
        </div>
        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" id="amount" class="form-control" value="{{ old('amount') }}" required>
        </div>
        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select name="type" id="type" class="form-select" required>
                <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Income</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select" required>
                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="payment_method" class="form-label">Payment Method</label>
            <input type="text" name="payment_method" id="payment_method" class="form-control" value="{{ old('payment_method') }}">
        </div>
        <div class="mb-3">
            <label for="reference_number" class="form-label">Reference Number</label>
            <input type="text" name="reference_number" id="reference_number" class="form-control" value="{{ old('reference_number') }}">
        </div>
        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea name="notes" id="notes" class="form-control">{{ old('notes') }}</textarea>
        </div>
        <button type="submit" class="btn btn-success">Save Transaction</button>
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@php
$contractData = $contracts->mapWithKeys(function($c) {
    $spent = $c->transactions->sum('amount') ?? 0;
    return [$c->id => [
        'total' => (float)$c->total_amount,
        'spent' => (float)$spent,
        'remaining' => (float)$c->total_amount - (float)$spent
    ]];
});
@endphp

@push('scripts')
<script>
    const contractData = @json($contractData);
    function updateContractInfo() {
        const contractId = document.getElementById('contract_id').value;
        const infoDiv = document.getElementById('contract-info');
        if (contractId && contractData[contractId]) {
            infoDiv.style.display = '';
            infoDiv.innerHTML =
                `<strong>Total Contract Value:</strong> ₱${parseFloat(contractData[contractId].total).toLocaleString(undefined, {minimumFractionDigits:2})}<br>` +
                `<strong>Already Spent:</strong> ₱${parseFloat(contractData[contractId].spent).toLocaleString(undefined, {minimumFractionDigits:2})}<br>` +
                `<strong>Remaining Balance:</strong> ₱${parseFloat(contractData[contractId].remaining).toLocaleString(undefined, {minimumFractionDigits:2})}`;
        } else {
            infoDiv.style.display = 'none';
            infoDiv.innerHTML = '';
        }
    }
    document.getElementById('contract_id').addEventListener('change', updateContractInfo);
    window.addEventListener('DOMContentLoaded', updateContractInfo);
</script>
@endpush
@endsection 