@php
    $user = auth()->user();
    $company = $user->company;
    $reason = $company?->rejection_reason;
    $body = "Hello Support Team,\n\nI am writing regarding the rejection of our company registration.\n\nCompany Details:\n"
        . '- Company Name: ' . ($company->company_name ?? 'N/A') . "\n- Contact Person: {$user->name}\n- Email: {$user->email}\n"
        . ($reason ? "\nRejection Reason: {$reason}\n" : '')
        . "\nI would like to appeal this decision or get more information about the rejection.\n\nThank you,\n{$user->name}";
    $mailto = 'mailto:jerry.nares30@gmail.com?subject=' . rawurlencode('Company Registration Rejection Appeal - ' . ($company->company_name ?? 'Unknown Company')) . '&body=' . rawurlencode($body);
@endphp
<x-auth-card title="Account Not Approved" icon="times" tone="danger" :help="false">
    <div class="status-message status-error">
        <i class="fas fa-times-circle"></i> Your company registration has been rejected.
        @if($reason)<p><strong>Reason:</strong> {{ $reason }}</p>@endif
    </div>
    <p class="auth-card__subtitle">Please contact our support team if you believe this was a mistake or if you need assistance.</p>
    <div class="action-buttons">
        <a href="{{ $mailto }}" class="btn btn-primary"><i class="fas fa-envelope"></i> Contact Support</a>
        <a href="{{ route('login') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Login</a>
    </div>
</x-auth-card>
