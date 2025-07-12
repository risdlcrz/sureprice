@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2>Contract for Quotation #{{ $quotationRequest->request_number }}</h2>
    <form method="POST" action="{{ route('client.contract.submit', ['id' => $quotationRequest->id]) }}">
        @csrf
        <div class="mb-3">
            <label for="client_name" class="form-label">Client Name</label>
            <input type="text" class="form-control" id="client_name" name="client_name" value="{{ old('client_name', auth()->user()->name ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label for="project_details" class="form-label">Project Details</label>
            <textarea class="form-control" id="project_details" name="project_details" rows="3" required>{{ old('project_details') }}</textarea>
        </div>
        <div class="mb-3">
            <label for="delivery_address" class="form-label">Delivery Address</label>
            <input type="text" class="form-control" id="delivery_address" name="delivery_address" value="{{ old('delivery_address') }}" required>
        </div>
        <div class="mb-3">
            <label for="terms" class="form-label">Terms & Conditions</label>
            <textarea class="form-control" id="terms" name="terms" rows="3" required>{{ old('terms', 'Standard terms and conditions apply.') }}</textarea>
        </div>
        <div class="mb-3">
            <label for="client_signature" class="form-label">Client Signature</label>
            <input type="text" class="form-control" id="client_signature" name="client_signature" required>
        </div>
        <div class="mb-3">
            <label for="admin_signature" class="form-label">Admin Signature</label>
            <input type="text" class="form-control" id="admin_signature" name="admin_signature" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Project Timeline</label>
            <div>
                <strong>Start Date:</strong>
                <input type="date" name="start_date" value="{{ $timelineStartDate ?? '' }}" readonly>
                <strong>End Date:</strong>
                <input type="date" name="end_date" value="{{ $timelineEndDate ?? '' }}" readonly>
                <br>
                <strong>Total Estimated Days:</strong> {{ $timelineEstimatedDays ?? 'Not set' }}
            </div>
        </div>
        <button type="submit" class="btn btn-success">Submit Contract</button>
        <a href="{{ route('client.contract.download', ['id' => $quotationRequest->id]) }}" class="btn btn-primary">Download as PDF</a>
    </form>
</div>
@endsection 