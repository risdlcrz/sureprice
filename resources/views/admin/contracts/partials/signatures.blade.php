{{--                 Signatures (contract details page). Param: $contract --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            Signatures
            @if($contract->hasBothSignatures())
                <span class="badge bg-success ms-2">Complete</span>
            @else
                <span class="badge bg-warning ms-2">Incomplete</span>
            @endif
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 text-center">
                <h6>
                    Contractor's Signature
                    @if($contract->hasContractorSignature())
                        <span class="badge bg-success ms-1">✓</span>
                    @else
                        <span class="badge bg-danger ms-1">✗</span>
                    @endif
                </h6>
                @if($contract->contractor_signature)
                    <img src="{{ asset('storage/' . $contract->contractor_signature) }}" 
                         alt="Contractor's Signature" 
                         class="img-fluid mb-2" 
                         style="max-height: 100px;">
                    <p class="mb-0">{{ $contract->contractor->name }}</p>
                    <small class="text-muted">Contractor</small>
                    @if($contract->contractor_date_signed)
                        <br><small class="text-muted">Signed: {{ $contract->contractor_date_signed instanceof \Carbon\Carbon ? $contract->contractor_date_signed->format('M d, Y') : \Carbon\Carbon::parse($contract->contractor_date_signed)->format('M d, Y') }}</small>
                    @endif
                @else
                    <p class="text-muted">No signature provided</p>
                    @if(!$isClient)
                        <button type="button" class="btn btn-sm btn-primary" onclick="showSignatureModal('contractor')">
                            <i class="fas fa-signature"></i> Add Contractor Signature
                        </button>
                    @endif
                @endif
            </div>
            <div class="col-md-6 text-center">
                <h6>
                    Client's Signature
                    @if($contract->hasClientSignature())
                        <span class="badge bg-success ms-1">✓</span>
                    @else
                        <span class="badge bg-danger ms-1">✗</span>
                    @endif
                </h6>
                @if($contract->client_signature)
                    <img src="{{ asset('storage/' . $contract->client_signature) }}" 
                         alt="Client's Signature" 
                         class="img-fluid mb-2" 
                         style="max-height: 100px;">
                    <p class="mb-0">{{ $contract->client->name }}</p>
                    <small class="text-muted">Client</small>
                    @if($contract->client_date_signed)
                        <br><small class="text-muted">Signed: {{ $contract->client_date_signed instanceof \Carbon\Carbon ? $contract->client_date_signed->format('M d, Y') : \Carbon\Carbon::parse($contract->client_date_signed)->format('M d, Y') }}</small>
                    @endif
                @else
                    <p class="text-muted">No signature provided</p>
                    @if($isClient)
                        <button type="button" class="btn btn-sm btn-primary" onclick="showSignatureModal('client')">
                            <i class="fas fa-signature"></i> Add Client Signature
                        </button>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
