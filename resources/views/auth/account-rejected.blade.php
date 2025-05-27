{{-- resources/views/auth/account-rejected.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Account Rejected</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fee2e2; /* light red background */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            background-color: white;
            padding: 2rem 3rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(220, 38, 38, 0.2);
            max-width: 500px;
            text-align: center;
        }
        .card-header {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #dc2626;
        }
        .card-body {
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        .alert-danger {
            background-color: #fee2e2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .btn-primary {
            display: inline-block;
            text-decoration: none;
            background-color: #2563eb;
            color: white;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
        }
        .login-link {
            display: block;
            margin-top: 1rem;
            color: #6b7280;
            text-decoration: none;
        }
        .login-link:hover {
            color: #374151;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <i class="fas fa-exclamation-circle"></i> Account Not Approved
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <i class="fas fa-times-circle"></i> 
                Your company registration has been rejected.
                @if(auth()->user()->company && auth()->user()->company->rejection_reason)
                    <p><strong>Reason:</strong> {{ auth()->user()->company->rejection_reason }}</p>
                @endif
            </div>
            
            <p>Please contact our support team if you believe this was a mistake or if you need assistance.</p>
            
            <a href="mailto:jerry.nares30@gmail.com?subject=Company Registration Rejection Appeal - {{ auth()->user()->company->company_name ?? 'Unknown Company' }}&body=Hello Support Team,%0D%0A%0D%0AI am writing regarding the rejection of our company registration.%0D%0A%0D%0ACompany Details:%0D%0A- Company Name: {{ auth()->user()->company->company_name ?? 'N/A' }}%0D%0A- Contact Person: {{ auth()->user()->name }}%0D%0A- Email: {{ auth()->user()->email }}%0D%0A{{ auth()->user()->company && auth()->user()->company->rejection_reason ? '%0D%0ARejection Reason: ' . auth()->user()->company->rejection_reason : '' }}%0D%0A%0D%0AI would like to appeal this decision or get more information about the rejection.%0D%0A%0D%0AThank you,%0D%0A{{ auth()->user()->name }}" class="btn btn-primary">
                <i class="fas fa-envelope"></i> Contact Support
            </a>

            <a href="{{ route('login') }}" class="login-link">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
        </div>
    </div>
</body>
</html>