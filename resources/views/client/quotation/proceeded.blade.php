@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm text-center">
                <div class="card-body py-5">
                    <h2 class="text-success mb-3" style="font-weight:700;">Thank you for proceeding!</h2>
                    <p class="lead mb-4">Your quotation request has been sent to our admin team. We will review your request and create a material request for you soon.</p>
                    <p class="mb-4">Request Number: <span class="badge bg-primary">{{ $quotationRequest->request_number ?? $quotationRequest->id }}</span></p>
                    <a href="{{ route('client.quotation.index') }}" class="btn btn-primary">Back to My Quotations</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 