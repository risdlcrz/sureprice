{{-- Standalone legal page (privacy policy, terms). The slot is the document body. --}}
@props(['title'])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - GDC Admin Center</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @vite('resources/css/legal.css')
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="logo-section">
                <img src="{{ asset('images/gdc_logo.png') }}" alt="GDC Logo">
                <h1>GDC Admin Center</h1>
            </div>
            <a href="{{ route('register') }}" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Registration</a>
        </div>
    </div>
    <div class="container">
        <div class="content-card">
            {{ $slot }}
            <div class="contact-info">
                <h3>Contact Us</h3>
                <p>If you have any questions about this {{ $title }}, please contact us at
                    <a href="mailto:{{ config('app.support_email') }}">{{ config('app.support_email') }}</a>.</p>
            </div>
        </div>
    </div>
</body>
</html>
