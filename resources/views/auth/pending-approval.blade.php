<x-auth-card title="Registration Successful!" icon="clock" tone="warning"
    subtitle="Thank you for registering with GDC Admin Center. Your account is now pending approval.">
    <div class="info-box">
        <h4><i class="fas fa-info-circle"></i> What happens next?</h4>
        <ul>
            <li>Your registration has been submitted successfully</li>
            <li>Our administrators will review your application</li>
            <li>You'll receive an email notification once approved</li>
            <li>You can then log in to access the system</li>
        </ul>
    </div>

    <div class="info-box">
        <h4><i class="fas fa-calendar"></i> Approval Timeline</h4>
        <ul>
            <li>Typical approval time: 1-2 business days</li>
            <li>Please ensure all submitted documents are valid</li>
            <li>Contact us if you haven't heard back within 3 days</li>
        </ul>
    </div>

    <div class="action-buttons">
        <a href="{{ route('login.form') }}" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Go to Login</a>
    </div>
</x-auth-card>
