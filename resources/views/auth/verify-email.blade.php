<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - GDC Admin Center</title>
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

        .verification-container {
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
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-section {
            margin-bottom: 30px;
        }

        .logo-section img {
            height: 60px;
            margin-bottom: 15px;
        }

        .verification-title {
            color: #154406;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .verification-subtitle {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .verification-icon {
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

        .status-info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 24px;
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

        .info-box {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }

        .info-box h4 {
            color: #154406;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .info-box p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 0;
        }

        .steps-list {
            text-align: left;
            margin: 20px 0;
        }

        .steps-list li {
            margin-bottom: 10px;
            color: #666;
            line-height: 1.6;
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
            .verification-container {
                padding: 30px 20px;
            }

            .verification-title {
                font-size: 24px;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="logo-section">
            <img src="{{ asset('Images/gdc_logo.png') }}" alt="GDC Logo">
            <h1 class="verification-title">Email Verification</h1>
        </div>

        <div class="verification-icon">
            <i class="fas fa-envelope-open-text"></i>
        </div>

        <p class="verification-subtitle">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="status-message status-success">
                <i class="fas fa-check-circle"></i>
                A new verification link has been sent to the email address you provided during registration.
            </div>
        @endif

        <div class="info-box">
            <h4><i class="fas fa-info-circle"></i> What happens next?</h4>
            <ol class="steps-list">
                <li>Check your email inbox (and spam folder)</li>
                <li>Click the verification link in the email</li>
                <li>Your account will be activated automatically</li>
                <li>You can then log in to your account</li>
            </ol>
        </div>

        <div class="action-buttons">
            <form method="POST" action="{{ route('verification.send') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i>
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-sign-out-alt"></i>
                    Log Out
                </button>
            </form>
        </div>

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
