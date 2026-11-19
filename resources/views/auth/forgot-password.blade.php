<x-auth-card title="Forgot Password" icon="key"
    subtitle="Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.">
    <div class="info-box">
        <h4><i class="fas fa-info-circle"></i> How it works</h4>
        <ol>
            <li>Enter your email address below</li>
            <li>We'll send you a password reset link</li>
            <li>Click the link in your email</li>
            <li>Create a new password for your account</li>
        </ol>
    </div>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        @include('auth.partials.field', ['name' => 'email', 'type' => 'email', 'label' => 'Email Address', 'placeholder' => 'Enter your email address', 'attrs' => 'autofocus'])
        <div class="action-buttons">
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Email Password Reset Link</button>
            <a href="{{ route('login') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Login</a>
        </div>
    </form>
</x-auth-card>
