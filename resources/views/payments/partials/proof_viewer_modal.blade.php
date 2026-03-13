@php
    $proofUrl = route('payments.proof', $payment);
    $downloadUrl = route('payments.proof', $payment) . '?download=1';
    $modalId = 'proofModal' . $payment->id;
@endphp
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true" data-bs-backdrop="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content proof-viewer-modal">
            <div class="modal-header py-3 bg-light border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="modal-title d-flex align-items-center gap-2 mb-0" id="{{ $modalId }}Label">
                    <i class="fas fa-file-invoice text-primary"></i>
                    Payment Proof — {{ $payment->payment_number }}
                </h5>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ $proofUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-primary" title="Open in new tab">
                        <i class="fas fa-external-link-alt"></i> Open in new tab
                    </a>
                    <a href="{{ $downloadUrl }}" class="btn btn-sm btn-success" title="Download file">
                        <i class="fas fa-download"></i> Download
                    </a>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
            <div class="modal-body p-0 bg-dark overflow-auto d-flex align-items-center justify-content-center" style="min-height: 75vh;">
                <iframe
                    src="{{ $proofUrl }}"
                    title="Payment proof"
                    class="proof-iframe w-100 border-0"
                    style="min-height: 75vh; display: block;"
                    loading="lazy"
                ></iframe>
            </div>
            <div class="modal-footer py-2 bg-light border-top small text-muted">
                <span>Images and PDFs open in the viewer. Use <strong>Open in new tab</strong> or <strong>Download</strong> if you need to print or save.</span>
            </div>
        </div>
    </div>
</div>
