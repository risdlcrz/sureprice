<x-auth-card title="Email Verification" icon="envelope-open-text"
    subtitle="Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.">
    @if (session('status') == 'verification-link-sent')
        <div class="status-message status-success">
            <i class="fas fa-check-circle"></i> A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <div class="info-box">
        <h4><i class="fas fa-info-circle"></i> What happens next?</h4>
        <ol>
            <li>Check your email inbox (and spam folder)</li>
            <li>Click the verification link in the email</li>
            <li>Your account will be activated automatically</li>
            <li>You can then log in to your account</li>
        </ol>
    </div>

    <div class="action-buttons">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Resend Verification Email</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-secondary"><i class="fas fa-sign-out-alt"></i> Log Out</button>
        </form>
    </div>
</x-auth-card>
