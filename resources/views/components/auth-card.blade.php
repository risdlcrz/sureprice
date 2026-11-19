{{-- Standalone card page for auth/status screens (forgot/reset password, verify email, pending, rejected). --}}
@props(['title', 'icon', 'tone' => 'brand', 'subtitle' => null, 'help' => true])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - GDC Admin Center</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @vite('resources/css/auth-card.css')
</head>
<body class="tone-{{ $tone }}">
    <div class="auth-card">
        <img class="auth-card__logo" src="{{ asset('Images/gdc_logo.png') }}" alt="GDC Logo">
        <h1 class="auth-card__title">{{ $title }}</h1>
        <div class="auth-card__icon"><i class="fas fa-{{ $icon }}"></i></div>
        @if($subtitle)<p class="auth-card__subtitle">{{ $subtitle }}</p>@endif

        @if(session('status') && session('status') !== 'verification-link-sent')
            <div class="status-message status-success"><i class="fas fa-check-circle"></i> {{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="status-message status-error">
                <i class="fas fa-exclamation-circle"></i>
                @foreach($errors->all() as $error){{ $error }}<br>@endforeach
            </div>
        @endif

        {{ $slot }}

        @if($help)
            <div class="contact-info">
                <i class="fas fa-question-circle"></i> Need help? Contact us at
                <a href="mailto:{{ config('app.support_email') }}">{{ config('app.support_email') }}</a>
            </div>
        @endif
    </div>
</body>
</html>
