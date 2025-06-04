@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Edit Transaction</h1>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('transactions.update', $transaction) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="contract_id" class="form-label">Contract</label>
            <select name="contract_id" id="contract_id" class="form-select" required>
                <option value="">Select Contract</option>
                @foreach($contracts as $contract)
                    <option value="{{ $contract->id }}" {{ old('contract_id', $transaction->contract_id) == $contract->id ? 'selected' : '' }}>
                        {{ $contract->contract_id }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ old('date', $transaction->date->format('Y-m-d')) }}" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <input type="text" name="description" id="description" class="form-control" value="{{ old('description', $transaction->description) }}" required>
        </div>
        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" id="amount" class="form-control" value="{{ old('amount', $transaction->amount) }}" required>
        </div>
        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select name="type" id="type" class="form-select" required>
                <option value="expense" {{ old('type', $transaction->type) == 'expense' ? 'selected' : '' }}>Expense</option>
                <option value="income" {{ old('type', $transaction->type) == 'income' ? 'selected' : '' }}>Income</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select" required>
                <option value="completed" {{ old('status', $transaction->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="payment_method" class="form-label">Payment Method</label>
            <input type="text" name="payment_method" id="payment_method" class="form-control" value="{{ old('payment_method', $transaction->payment_method) }}">
        </div>
        <div class="mb-3">
            <label for="reference_number" class="form-label">Reference Number</label>
            <input type="text" name="reference_number" id="reference_number" class="form-control" value="{{ old('reference_number', $transaction->reference_number) }}">
        </div>
        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea name="notes" id="notes" class="form-control">{{ old('notes', $transaction->notes) }}</textarea>
        </div>
        <button type="submit" class="btn btn-success">Update Transaction</button>
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection 