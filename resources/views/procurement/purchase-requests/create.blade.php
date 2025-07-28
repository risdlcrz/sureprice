@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Purchase Request</h1>
        <a href="{{ route('procurement.purchase-requests.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Purchase Request Form</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('procurement.purchase-requests.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="purpose" class="form-label">Purpose *</label>
                            <textarea name="purpose" id="purpose" class="form-control" rows="3" required>{{ old('purpose') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="required_date" class="form-label">Required Date</label>
                            <input type="date" name="required_date" id="required_date" class="form-control" value="{{ old('required_date', now()->addDays(7)->format('Y-m-d')) }}">
                        </div>
                        <div class="mb-3">
                            <label for="contract_id" class="form-label">Contract (Optional)</label>
                            <select name="contract_id" id="contract_id" class="form-select">
                                <option value="">Select a contract</option>
                                @foreach($contracts as $contract)
                                    <option value="{{ $contract->id }}" {{ old('contract_id') == $contract->id ? 'selected' : '' }}>
                                        {{ $contract->contract_number }} - {{ $contract->client->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Purchase Request
                </button>
            </form>
        </div>
    </div>
</div>
@endsection 