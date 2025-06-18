<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - GDC Admin Center</title>
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
            <a href="{{ url()->previous() }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to Registration
            </a>
        </div>
    </div>
    <div class="container">
        <div class="content-card">
            <div class="page-title">Privacy Policy</div>
            <div class="last-updated">Last updated: June 2024</div>
            <div class="section">
                <h2>Introduction</h2>
                <p>This Privacy Policy describes how we collect, use, and protect your personal information when you use our application. By using this service, you agree to the collection and use of information in accordance with this policy.</p>
            </div>
            <div class="section">
                <h2>Information We Collect</h2>
                <ul>
                    <li>Personal identification information (Name, email address, etc.)</li>
                    <li>Usage data and cookies</li>
                </ul>
            </div>
            <div class="section">
                <h2>How We Use Your Information</h2>
                <ul>
                    <li>To provide and maintain our service</li>
                    <li>To notify you about changes to our service</li>
                    <li>To provide customer support</li>
                    <li>To monitor usage and improve our service</li>
                </ul>
            </div>
            <div class="section">
                <h2>Security</h2>
                <div class="highlight-box">
                    <strong>We value your trust in providing us your personal information, thus we strive to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure.</strong>
                </div>
            </div>
            <div class="section">
                <h2>Changes to This Policy</h2>
                <p>We may update our Privacy Policy from time to time. We advise you to review this page periodically for any changes.</p>
            </div>
        </div>
        <div class="contact-info">
            <h3>Contact Us</h3>
            <p>If you have any questions about this Privacy Policy, please contact us at <a href="mailto:support@gdcadmin.com">support@gdcadmin.com</a>.</p>
        </div>
    </div>
</body>
</html> 