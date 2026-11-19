@extends('layouts.app')

@section('title', 'Supplier Invitation')
@section('is_landing', true)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h4 class="card-title mb-0">Supplier Invitation Response</h4>
                </div>
                <div class="card-body">
                    <x-flash />
                    @if(!$invitation->isPending())
                        <div class="alert alert-info">
                            <h5>You have already responded to this invitation.</h5>
                            <p>Your response: <x-status-badge :status="$invitation->status" /></p>
                            @if($invitation->response_notes)
                                <p>Your notes: {{ $invitation->response_notes }}</p>
                            @endif
                        </div>
                    @else
                        <div class="mb-4">
                            <h5>Contract Details</h5>
                            @if($invitation->contract)
                                <p><strong>Contract:</strong> {{ $invitation->contract->contract_number }} - {{ $invitation->contract->title }}</p>
                            @endif
                            <p><strong>Due Date:</strong> {{ $invitation->due_date->format('M d, Y') }}</p>
                        </div>

                        <div class="mb-4">
                            <h5>Required Materials</h5>
                            <ul class="list-unstyled">
                                @foreach($invitation->materials as $material)
                                    <li><i class="fas fa-box me-2"></i>{{ $material->name }}</li>
                                @endforeach
                            </ul>
                        </div>

                        @if($invitation->message)
                            <div class="mb-4">
                                <h5>Message</h5>
                                <p class="mb-0">{{ $invitation->message }}</p>
                            </div>
                        @endif

                        <form action="{{ route('supplier.respond.submit', $invitation->invitation_code) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Your Response</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="response" id="accept" value="accept" required>
                                        <label class="form-check-label" for="accept">Accept Invitation</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="response" id="reject" value="reject" required>
                                        <label class="form-check-label" for="reject">Decline Invitation</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="4"></textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Submit Response</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 