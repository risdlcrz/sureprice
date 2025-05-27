{{-- resources/views/auth/pending-approval.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Account Pending Approval</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e6f4ea; /* light green background */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            background-color: #2e7d32; /* dark green */
            color: white;
            padding: 2rem 3rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(46, 125, 50, 0.4);
            max-width: 400px;
            text-align: center;
        }
        .card-header {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .card-body {
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        a.login-link {
            display: inline-block;
            text-decoration: none;
            background-color: #81c784; /* medium green */
            color: #1b5e20; /* dark green */
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        a.login-link:hover {
            background-color: #4caf50; /* brighter green */
            color: white;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">Account Pending Approval</div>
        <div class="card-body">
            Your account is currently pending approval.<br />
            Please wait for the admin to approve your registration.
        </div>
        <a href="{{ route('login') }}" class="login-link">Go to Login Page</a>
    </div>
</body>
</html>
