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
            <div class="input-group">
                <input type="text" class="form-control" id="client_signature" name="client_signature" required readonly>
                <button type="button" class="btn btn-outline-primary" id="addClientSignatureBtn">Add Client Signature</button>
            </div>
        </div>
        <div class="mb-3">
            <label for="admin_signature" class="form-label">Admin Signature</label>
            <input type="text" class="form-control" id="admin_signature" name="admin_signature" required readonly>
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

<!-- Signature Modal -->
<div class="modal fade" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="signatureModalLabel">Add Signature</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="signatureModalSubtitle" class="mb-2">Add Client signature below</div>
        <canvas id="signatureCanvas" width="400" height="150" style="border:1px solid #ccc; width:100%; height:150px;"></canvas>
        <div class="mt-2 d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" id="clearSignatureBtn">Clear</button>
          <button type="button" class="btn btn-primary" id="saveSignatureBtn">Save Signature</button>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<!-- SignaturePad CDN -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.6/dist/signature_pad.umd.min.js"></script>
<!-- SweetAlert2 CDN (if not already included) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/contracts-show.js') }}"></script>
<script>
// Setup for client signature only
window.contractSignatureUrl = null; // Not used for client-side only

// Modal logic for client signature
const addClientSignatureBtn = document.getElementById('addClientSignatureBtn');
const signatureModal = new bootstrap.Modal(document.getElementById('signatureModal'));
const signatureCanvas = document.getElementById('signatureCanvas');
const clientSignatureInput = document.getElementById('client_signature');
let signaturePad = null;

addClientSignatureBtn.addEventListener('click', function() {
    signatureModal.show();
    if (!signaturePad) {
        signaturePad = new SignaturePad(signatureCanvas, {
            backgroundColor: 'rgb(255,255,255)',
            penColor: 'rgb(0,0,0)'
        });
    } else {
        signaturePad.clear();
    }
});

document.getElementById('clearSignatureBtn').addEventListener('click', function() {
    if (signaturePad) signaturePad.clear();
});

document.getElementById('saveSignatureBtn').addEventListener('click', function() {
    if (!signaturePad || signaturePad.isEmpty()) {
        Swal.fire({
            icon: 'warning',
            title: 'No Signature',
            text: 'Please provide a signature before saving.'
        });
        return;
    }
    const dataUrl = signaturePad.toDataURL();
    clientSignatureInput.value = dataUrl;
    signatureModal.hide();
    Swal.fire({
        icon: 'success',
        title: 'Signature Saved',
        timer: 1000,
        showConfirmButton: false
    });
});
</script>
@endpush
@endsection 