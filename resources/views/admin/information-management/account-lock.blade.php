{{--
    Account lock UI for one user row of Information Management.
    Params: $account (App\Models\User|null), $part: 'status' (cell), 'actions' (admin lock/unlock button), 'modals'.
--}}
@php
    $locked = $account?->isLocked();
    $canLock = $account && auth()->user()->isAdmin() && !$account->is(auth()->user());
@endphp

@if($part === 'status')
    @if(!$account)
        <span class="text-muted">—</span>
    @elseif($locked)
        <div class="acct-lock">
            <span class="acct-badge acct-badge--locked"><i class="fas fa-lock" aria-hidden="true"></i> Locked</span>
            <div class="acct-lock__reason">{{ $account->lock_reason }}</div>
            <div class="acct-lock__meta">
                {{ $account->lockedBy ? 'By ' . $account->lockedBy->name : 'Automatic' }}
                @if($account->locked_at) &middot; {{ $account->locked_at->timezone('Asia/Manila')->format('M j, Y g:i A') }}@endif
            </div>
        </div>
    @else
        <span class="acct-badge acct-badge--active">Active</span>
        @if($account->failed_login_attempts > 0)
            <div class="acct-lock__meta">{{ $account->failed_login_attempts }} failed sign-in{{ $account->failed_login_attempts === 1 ? '' : 's' }}</div>
        @endif
    @endif
@elseif($part === 'actions' && $canLock)
    @if($locked)
        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#unlockUser{{ $account->id }}" title="Unlock account">
            <i class="fas fa-lock-open"></i>
        </button>
    @else
        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#lockUser{{ $account->id }}" title="Lock account">
            <i class="fas fa-lock"></i>
        </button>
    @endif
@elseif($part === 'modals' && $canLock)
    @if($locked)
        <x-confirm-modal :id="'unlockUser' . $account->id" title="Unlock {{ $account->name }}" :action="route('admin.users.unlock', $account)"
                         method="DELETE" confirm="Unlock account" tone="success">
            {{ $account->name }} will be able to sign in again, and their failed sign-in count is reset.
        </x-confirm-modal>
    @else
        <x-modal-dialog :id="'lockUser' . $account->id">
            <x-slot:title>Lock {{ $account->name }}</x-slot:title>
            <form action="{{ route('admin.users.lock', $account) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="mb-3">They will be signed out immediately and cannot sign in or make changes until an administrator unlocks the account.</p>
                    <label for="lockReason{{ $account->id }}" class="form-label">Reason <span class="text-danger">*</span></label>
                    <textarea id="lockReason{{ $account->id }}" name="reason" class="form-control" rows="3" maxlength="255" required
                              placeholder="e.g. Suspicious activity reported by the client"></textarea>
                    <div class="form-text">Visible to administrators and managers. Not shown to the locked user.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-lock me-1"></i> Lock account</button>
                </div>
            </form>
        </x-modal-dialog>
    @endif
@endif
