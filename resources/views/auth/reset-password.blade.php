<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - GDC Admin Center</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #02912d 0%, #154406 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .reset-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .logo-section {
            margin-bottom: 30px;
        }
        .logo-section img {
            height: 60px;
            margin-bottom: 15px;
        }
        .reset-title {
            color: #154406;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .reset-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #02912d, #154406);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: white;
            font-size: 32px;
        }
        .reset-subtitle {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .status-message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: 500;
        }
        .status-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .status-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-label {
            display: block;
            color: #154406;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .form-input {
            width: 100%;
            padding: 14px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }
        .form-input:focus {
            outline: none;
            border-color: #02912d;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(2, 145, 45, 0.1);
        }
        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 30px;
        }
        .btn {
            padding: 14px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #02912d, #154406);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(2, 145, 45, 0.3);
        }
        .btn-secondary {
            background: #f8f9fa;
            color: #6c757d;
            border: 1px solid #dee2e6;
        }
        .btn-secondary:hover {
            background: #e9ecef;
            color: #495057;
        }
        .contact-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        .contact-info p {
            color: #666;
            font-size: 14px;
        }
        .contact-info a {
            color: #02912d;
            text-decoration: none;
            font-weight: 500;
        }
        .contact-info a:hover {
            text-decoration: underline;
        }
        @media (max-width: 480px) {
            .reset-container {
                padding: 30px 20px;
            }
            .reset-title {
                font-size: 24px;
            }
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="logo-section">
            <img src="{{ asset('Images/gdc_logo.png') }}" alt="GDC Logo">
            <h1 class="reset-title">Reset Password</h1>
        </div>
        <div class="reset-icon">
            <i class="fas fa-unlock-alt"></i>
        </div>
        <p class="reset-subtitle">
            Enter your email and choose a new password for your account.
        </p>
        @if ($errors->any())
            <div class="status-message status-error">
                <i class="fas fa-exclamation-circle"></i>
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" class="form-input @error('email') error @enderror" placeholder="Enter your email address">
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" class="form-input @error('password') error @enderror" placeholder="Enter new password">
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="form-input @error('password_confirmation') error @enderror" placeholder="Confirm new password">
                @error('password_confirmation')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            <div class="action-buttons">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sync-alt"></i>
                    Reset Password
                </button>
                <a href="{{ route('login') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Login
                </a>
            </div>
        </form>
        <div class="contact-info">
            <p>
                <i class="fas fa-question-circle"></i>
                Need help? Contact us at 
                <a href="mailto:support@gdcadmin.com">support@gdcadmin.com</a>
            </p>
        </div>
    </div>
</body>
</html>
