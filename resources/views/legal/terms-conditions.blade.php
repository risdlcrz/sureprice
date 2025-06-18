<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - GDC Admin Center</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .header {
            background: linear-gradient(135deg, #02912d 0%, #154406 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-section img {
            height: 50px;
        }

        .logo-section h1 {
            font-size: 24px;
            font-weight: 600;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            text-decoration: none;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .content-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-bottom: 30px;
        }

        .page-title {
            color: #154406;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
        }

        .last-updated {
            text-align: center;
            color: #666;
            font-style: italic;
            margin-bottom: 40px;
        }

        .section {
            margin-bottom: 30px;
        }

        .section h2 {
            color: #154406;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 15px;
            border-bottom: 2px solid #02912d;
            padding-bottom: 8px;
        }

        .section h3 {
            color: #154406;
            font-size: 20px;
            font-weight: 600;
            margin: 25px 0 15px 0;
        }

        .section p {
            margin-bottom: 15px;
            color: #555;
        }

        .section ul, .section ol {
            margin: 15px 0;
            padding-left: 30px;
        }

        .section li {
            margin-bottom: 8px;
            color: #555;
        }

        .highlight-box {
            background: #f8f9fa;
            border-left: 4px solid #02912d;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }

        .highlight-box strong {
            color: #154406;
        }

        .contact-info {
            background: linear-gradient(135deg, #02912d, #154406);
            color: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin-top: 40px;
        }

        .contact-info h3 {
            color: white;
            margin-bottom: 15px;
        }

        .contact-info p {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 10px;
        }

        .contact-info a {
            color: white;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .content-card {
                padding: 25px;
            }

            .page-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="logo-section">
                <img src="{{ asset('Images/gdc_logo.png') }}" alt="GDC Logo">
                <h1>GDC Admin Center</h1>
            </div>
            <a href="{{ route('register') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to Registration
            </a>
        </div>
    </div>

    <div class="container">
        <div class="content-card">
            <h1 class="page-title">Terms and Conditions</h1>
            <p class="last-updated">Last updated: {{ date('F d, Y') }}</p>

            <div class="section">
                <h2>1. Acceptance of Terms</h2>
                <p>By accessing and using the GDC Admin Center ("the Service"), you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.</p>
            </div>

            <div class="section">
                <h2>2. Description of Service</h2>
                <p>The GDC Admin Center is a procurement and project management platform that facilitates:</p>
                <ul>
                    <li>Company registration and verification</li>
                    <li>Supplier and client management</li>
                    <li>Project tracking and documentation</li>
                    <li>Procurement processes and workflows</li>
                    <li>Financial transaction management</li>
                </ul>
            </div>

            <div class="section">
                <h2>3. User Registration and Account</h2>
                <h3>3.1 Registration Requirements</h3>
                <p>To use certain features of the Service, you must register for an account. You agree to provide accurate, current, and complete information during registration and to update such information to keep it accurate, current, and complete.</p>

                <h3>3.2 Account Security</h3>
                <p>You are responsible for safeguarding the password and for all activities that occur under your account. You agree to notify us immediately of any unauthorized use of your account.</p>

                <div class="highlight-box">
                    <strong>Important:</strong> You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.
                </div>
            </div>

            <div class="section">
                <h2>4. Acceptable Use Policy</h2>
                <p>You agree not to use the Service to:</p>
                <ul>
                    <li>Violate any applicable laws or regulations</li>
                    <li>Infringe upon the rights of others</li>
                    <li>Upload or transmit malicious code or content</li>
                    <li>Attempt to gain unauthorized access to the Service</li>
                    <li>Interfere with or disrupt the Service</li>
                    <li>Use the Service for any fraudulent or deceptive purpose</li>
                </ul>
            </div>

            <div class="section">
                <h2>5. Privacy and Data Protection</h2>
                <p>Your privacy is important to us. Please review our Privacy Policy, which also governs your use of the Service, to understand our practices regarding the collection and use of your information.</p>
            </div>

            <div class="section">
                <h2>6. Intellectual Property</h2>
                <p>The Service and its original content, features, and functionality are and will remain the exclusive property of GDC Admin Center and its licensors. The Service is protected by copyright, trademark, and other laws.</p>
            </div>

            <div class="section">
                <h2>7. Limitation of Liability</h2>
                <p>In no event shall GDC Admin Center, nor its directors, employees, partners, agents, suppliers, or affiliates, be liable for any indirect, incidental, special, consequential, or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from your use of the Service.</p>
            </div>

            <div class="section">
                <h2>8. Termination</h2>
                <p>We may terminate or suspend your account and bar access to the Service immediately, without prior notice or liability, under our sole discretion, for any reason whatsoever and without limitation, including but not limited to a breach of the Terms.</p>
            </div>

            <div class="section">
                <h2>9. Changes to Terms</h2>
                <p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material, we will provide at least 30 days notice prior to any new terms taking effect.</p>
            </div>

            <div class="section">
                <h2>10. Governing Law</h2>
                <p>These Terms shall be interpreted and governed by the laws of the Philippines, without regard to its conflict of law provisions.</p>
            </div>

            <div class="contact-info">
                <h3>Contact Us</h3>
                <p>If you have any questions about these Terms and Conditions, please contact us:</p>
                <p><strong>Email:</strong> <a href="mailto:legal@gdcadmin.com">legal@gdcadmin.com</a></p>
                <p><strong>Phone:</strong> +63 XXX XXX XXXX</p>
                <p><strong>Address:</strong> [Your Company Address]</p>
            </div>
        </div>
    </div>
</body>
</html> 