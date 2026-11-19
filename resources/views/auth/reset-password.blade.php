<x-auth-card title="Reset Password" icon="unlock-alt" subtitle="Enter your email and choose a new password for your account.">
    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        @include('auth.partials.field', ['name' => 'email', 'type' => 'email', 'label' => 'Email Address', 'placeholder' => 'Enter your email address', 'value' => $request->email, 'attrs' => 'autofocus autocomplete="username"'])
        @include('auth.partials.field', ['name' => 'password', 'type' => 'password', 'label' => 'Password', 'placeholder' => 'Enter new password', 'attrs' => 'autocomplete="new-password"'])
        @include('auth.partials.field', ['name' => 'password_confirmation', 'type' => 'password', 'label' => 'Confirm Password', 'placeholder' => 'Confirm new password', 'attrs' => 'autocomplete="new-password"'])
        <div class="action-buttons">
            <button type="submit" class="btn btn-primary"><i class="fas fa-sync-alt"></i> Reset Password</button>
            <a href="{{ route('login.form') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Login</a>
        </div>
    </form>
</x-auth-card>
