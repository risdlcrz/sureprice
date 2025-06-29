{{-- resources/views/auth/pending-approval.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approval - GDC Admin Center</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #02912d 0%, #026f22 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .pending-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: #e8f5e9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: #4caf50;
            font-size: 2rem;
        }

        .pending-icon {
            width: 80px;
            height: 80px;
            background: #fff3e0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: #ff9800;
            font-size: 2rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 2rem;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .info-box {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
            text-align: left;
        }

        .info-box h3 {
            color: #495057;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .info-box ul {
            list-style: none;
            padding: 0;
        }

        .info-box li {
            padding: 8px 0;
            color: #6c757d;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-box li i {
            color: #28a745;
            width: 16px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #02912d;
            color: white;
        }

        .btn-primary:hover {
            background: #026f22;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .contact-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .contact-info a {
            color: #02912d;
            text-decoration: none;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .pending-container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 1.5rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="pending-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        
        <h1>Registration Successful!</h1>
        <p class="subtitle">Thank you for registering with GDC Admin Center. Your account is now pending approval.</p>

        <div class="pending-icon">
            <i class="fas fa-clock"></i>
        </div>

        <div class="info-box">
            <h3><i class="fas fa-info-circle"></i> What happens next?</h3>
            <ul>
                <li><i class="fas fa-check"></i> Your registration has been submitted successfully</li>
                <li><i class="fas fa-clock"></i> Our administrators will review your application</li>
                <li><i class="fas fa-envelope"></i> You'll receive an email notification once approved</li>
                <li><i class="fas fa-sign-in-alt"></i> You can then log in to access the system</li>
            </ul>
        </div>

        <div class="info-box">
            <h3><i class="fas fa-clock"></i> Approval Timeline</h3>
            <ul>
                <li><i class="fas fa-calendar"></i> Typical approval time: 1-2 business days</li>
                <li><i class="fas fa-exclamation"></i> Please ensure all submitted documents are valid</li>
                <li><i class="fas fa-phone"></i> Contact us if you haven't heard back within 3 days</li>
            </ul>
        </div>

        <div class="action-buttons">
            <a href="{{ route('login.form') }}" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i>
                Go to Login
            </a>
        </div>

        <div class="contact-info">
            <p><strong>Need help?</strong> Contact our support team:</p>
            <p><i class="fas fa-envelope"></i> Email: <a href="mailto:support@gdc.com">support@gdc.com</a></p>
            <p><i class="fas fa-phone"></i> Phone: <a href="tel:+1234567890">+1 (234) 567-890</a></p>
        </div>
    </div>
</body>
</html>
