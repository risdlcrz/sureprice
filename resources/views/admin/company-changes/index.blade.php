@extends('layouts.app')

@section('content')
<div class="container-fluid req-page">
    <x-page-header title="Company Name Changes" icon="id-card" subtitle="Clients asking to change their registered company name. Check the proof before approving — the new name is used on future contracts and documents." />

    <div class="card">
        <div class="card-header pb-0">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link @if($tab === 'pending') active @endif" href="{{ route('admin.company-changes.index') }}" @if($tab === 'pending') aria-current="page" @endif>
                        Waiting for review <span class="badge bg-light ms-1">{{ $pendingCount }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($tab === 'history') active @endif" href="{{ route('admin.company-changes.index', ['tab' => 'history']) }}" @if($tab === 'history') aria-current="page" @endif>History</a>
                </li>
            </ul>
        </div>

        @if($tab === 'pending')
            <div class="card-body">
                @forelse($requests as $cr)
                    <article class="change-review @unless($loop->last) mb-3 @endunless">
                        <div class="change-review__main">
                            <div class="change-review__who">
                                @if($cr->company)
                                    <a href="{{ route('admin.companies.show', $cr->company) }}" class="req-ref">{{ $cr->company->company_name ?: $cr->company->contact_person }}</a>
                                @endif
                                <span class="req-sub">{{ $cr->company?->contact_person }} · {{ $cr->company?->email }} · sent {{ $cr->created_at->format('M d, Y g:i A') }}</span>
                            </div>
                            <div class="change-review__diff">
                                <span class="change-review__old">{{ $cr->old_value ?: 'No company name (individual)' }}</span>
                                <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                <strong class="change-review__new">{{ $cr->new_value }}</strong>
                            </div>
                            @if($cr->reason)
                                <p class="change-review__reason mb-0"><i class="fas fa-quote-left me-1 text-muted"></i>{{ $cr->reason }}</p>
                            @endif
                            <a href="{{ route('company-changes.proof', $cr) }}" target="_blank" rel="noopener" class="change-review__proof">
                                <i class="fas {{ str_contains((string) $cr->proof_mime, 'pdf') ? 'fa-file-pdf' : 'fa-file-image' }}" aria-hidden="true"></i>
                                <span>{{ $cr->proof_name }}</span>
                                <small>{{ $cr->proof_size ? number_format($cr->proof_size / 1024, 0) . ' KB' : '' }} · open proof</small>
                            </a>
                        </div>
                        <div class="change-review__actions">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveChange{{ $cr->id }}"><i class="fas fa-check me-1"></i> Approve</button>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectChange{{ $cr->id }}"><i class="fas fa-times me-1"></i> Reject</button>
                        </div>
                    </article>

                    <x-confirm-modal :id="'approveChange' . $cr->id" title="Approve name change" :action="route('admin.company-changes.approve', $cr)" confirm="Approve" tone="success">
                        <p>Change <strong>{{ $cr->old_value ?: 'this individual client' }}</strong> to <strong>{{ $cr->new_value }}</strong>?</p>
                        <p class="text-muted small">Make sure the proof shows the new name. Existing signed contracts keep the name they were signed under.</p>
                        <label for="approveNotes{{ $cr->id }}" class="form-label">Note to the client <span class="text-muted fw-normal">(optional)</span></label>
                        <textarea id="approveNotes{{ $cr->id }}" name="review_notes" rows="2" maxlength="500" class="form-control"></textarea>
                    </x-confirm-modal>
                    <x-confirm-modal :id="'rejectChange' . $cr->id" title="Reject name change" :action="route('admin.company-changes.reject', $cr)" confirm="Reject" tone="danger">
                        <p>Keep <strong>{{ $cr->old_value ?: 'no company name' }}</strong> instead of “{{ $cr->new_value }}”.</p>
                        <label for="rejectNotes{{ $cr->id }}" class="form-label">Reason for the client<span class="text-danger"> *</span></label>
                        <textarea id="rejectNotes{{ $cr->id }}" name="review_notes" rows="3" maxlength="500" class="form-control" required
                            placeholder="e.g. The document doesn't show the new name / is expired / is unreadable."></textarea>
                    </x-confirm-modal>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-check-circle fa-2x mb-2 text-success" aria-hidden="true"></i>
                        <p class="mb-0">Nothing to review — no company name changes are waiting.</p>
                    </div>
                @endforelse
            </div>
        @else
            <div class="table-responsive">
                <table class="table req-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Change</th>
                            <th>Status</th>
                            <th>Reviewed</th>
                            <th>Notes</th>
                            <th class="text-end">Proof</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $cr)
                            <tr>
                                <td>{{ $cr->company?->contact_person ?? '—' }}<span class="req-sub">sent {{ $cr->created_at->format('M d, Y') }}</span></td>
                                <td class="req-wrap"><span class="text-muted">{{ $cr->old_value ?: '—' }}</span> → <strong>{{ $cr->new_value }}</strong></td>
                                <td><x-status-badge :status="$cr->status" /></td>
                                <td>{{ $cr->reviewer?->name ?? ($cr->status === 'cancelled' ? 'Withdrawn by client' : '—') }}<span class="req-sub">{{ $cr->reviewed_at?->format('M d, Y') }}</span></td>
                                <td class="req-wrap">{{ $cr->review_notes ?: '—' }}</td>
                                <td class="text-end">
                                    @if($cr->status !== 'cancelled')
                                        <a href="{{ route('company-changes.proof', $cr) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">Open</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <x-empty-row cols="6">No reviewed requests yet.</x-empty-row>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        @if($requests->hasPages())
            <div class="req-footer">{{ $requests->links() }}</div>
        @endif
    </div>
</div>
@endsection

@push('styles')
@vite(['resources/css/pages/requests.css', 'resources/css/pages/admin-company-changes.css'])
@endpush
