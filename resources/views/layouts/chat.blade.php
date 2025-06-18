<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messenger - {{ config('app.name') }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .chat-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .chat-header {
            background-color: #2563eb;
            color: white;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .back-button {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.1rem;
        }
        .back-button:hover {
            color: #e2e8f0;
        }
        .chat-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            @php
                $user = auth()->user();
                if ($user->user_type === 'admin') {
                    $dashboardRoute = route('admin.dbadmin');
                } elseif ($user->user_type === 'client') {
                    $dashboardRoute = route('client.dashboard');
                } elseif ($user->user_type === 'supplier') {
                    $dashboardRoute = route('supplier.dashboard');
                } else {
                    $dashboardRoute = url('/');
                }
            @endphp
            <a href="{{ $dashboardRoute }}" class="back-button">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
            <h1 class="chat-title">Messenger</h1>
        </div>
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
</body>
</html> 