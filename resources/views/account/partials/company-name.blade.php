{{--
    Client: request a change to the registered company name. Needs proof; applied once an admin approves
    (CompanyChangeRequestController / Admin\CompanyChangeRequestController). $company, $nameChange (latest request or null).
--}}
@php
    $err = $errors->companyName;
    $pending = $nameChange?->isPending();
@endphp
<div class="card mb-4" id="company-name">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="req-section-title"><i class="fas fa-building"></i> Company name</h2>
        <span class="badge {{ $pending ? 'bg-warning' : 'bg-success' }}">{{ $pending ? 'Change under review' : 'Verified' }}</span>
    </div>
    <div class="card-body">
        <dl class="req-meta mb-3">
            <div>
                <dt>Registered company name</dt>
                <dd>{{ $company->company_name ?: 'None — registered as an individual' }}</dd>
            </div>
        </dl>

        @if($pending)
            <div class="req-callout req-callout--warning">
                <div class="req-callout__text">
                    <i class="fas fa-hourglass-half" aria-hidden="true"></i>
                    <div>
                        <strong>Waiting for review:</strong> change to “{{ $nameChange->new_value }}”, sent {{ $nameChange->created_at->format('M d, Y') }}.
                        <br><a href="{{ route('company-changes.proof', $nameChange) }}" target="_blank" rel="noopener"><i class="fas fa-paperclip me-1"></i>{{ $nameChange->proof_name }}</a>
                        <br><span class="text-muted small">Your current name stays on your account and documents until an administrator approves the change.</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('account.company-name.destroy', $nameChange) }}" onsubmit="return confirm('Withdraw this company name change?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-secondary">Withdraw request</button>
                </form>
            </div>
        @else
            @if($nameChange?->status === 'rejected')
                <div class="req-callout req-callout--danger mb-3">
                    <div class="req-callout__text">
                        <i class="fas fa-times-circle" aria-hidden="true"></i>
                        <div>
                            <strong>Your change to “{{ $nameChange->new_value }}” wasn't approved</strong>
                            ({{ $nameChange->reviewed_at?->format('M d, Y') }}).
                            @if($nameChange->review_notes)<br>Reason: {{ $nameChange->review_notes }}@endif
                            <br><span class="small">You can send a new request with the correct proof below.</span>
                        </div>
                    </div>
                </div>
            @elseif($nameChange?->status === 'approved' && $nameChange->reviewed_at?->gt(now()->subDays(14)))
                <div class="req-callout req-callout--success mb-3">
                    <div class="req-callout__text">
                        <i class="fas fa-check-circle" aria-hidden="true"></i>
                        <div><strong>Name change approved</strong> on {{ $nameChange->reviewed_at->format('M d, Y') }}.</div>
                    </div>
                </div>
            @endif

            <details class="account-change" @if($err->any()) open @endif>
                <summary class="btn btn-outline-success btn-sm">{{ $company->company_name ? 'Request a name change' : 'Add a company name' }}</summary>
                <form method="POST" action="{{ route('account.company-name.store') }}" enctype="multipart/form-data" class="mt-3">
                    @csrf
                    <p class="text-muted small">
                        For your security, a new company name has to be verified. Upload an official document that shows it —
                        e.g. your DTI or SEC registration, amended articles, or mayor's/business permit.
                    </p>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="new_company_name" class="form-label">New company name<span class="text-danger"> *</span></label>
                            <input type="text" id="new_company_name" name="new_company_name" value="{{ old('new_company_name') }}" maxlength="100" required
                                class="form-control @if($err->has('new_company_name')) is-invalid @endif" autocomplete="organization" placeholder="Exactly as it appears on your registration">
                            @if($err->has('new_company_name'))<div class="invalid-feedback d-block">{{ $err->first('new_company_name') }}</div>@endif
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="proof" class="form-label">Proof document<span class="text-danger"> *</span></label>
                            <input type="file" id="proof" name="proof" accept=".pdf,.jpg,.jpeg,.png" required
                                class="form-control @if($err->has('proof')) is-invalid @endif">
                            <div class="form-text">PDF, JPG or PNG, up to 10 MB. Only administrators can see it.</div>
                            @if($err->has('proof'))<div class="invalid-feedback d-block">{{ $err->first('proof') }}</div>@endif
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="reason" class="form-label">Reason <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea id="reason" name="reason" rows="2" maxlength="500" class="form-control @if($err->has('reason')) is-invalid @endif"
                                placeholder="e.g. We incorporated and the business is now registered with the SEC.">{{ old('reason') }}</textarea>
                            @if($err->has('reason'))<div class="invalid-feedback d-block">{{ $err->first('reason') }}</div>@endif
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane me-1"></i> Send for review</button>
                </form>
            </details>
        @endif
    </div>
</div>
