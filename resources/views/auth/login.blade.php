<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="{{ asset('resources/css/login.css') }}">
  <script src="{{ asset('resources/js/login.js') }}"></script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Admin Center</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

  <div class="top-bar">
    <img src="{{ asset('Images/gdc_logo.png') }}" alt="Logo">
    <span class="top-title">Admin Center</span>
  </div>

  <div class="main-container">
    <div class="login-box">
      <div class="login-left"></div> <!-- Background image handled in CSS -->

      <div class="login-right">
        <h2 class="login-title">Login</h2>
        @if (session('success'))
        <div class="success-banner" id="successBanner" style="margin-bottom: 1rem; background: #e8f5e9; color: #256029; border: 1px solid #4caf50; border-radius: 6px; padding: 0.75rem 1rem; display: flex; align-items: center; gap: 0.75rem; position: relative;">
          <i class="fas fa-check-circle" style="font-size: 1.2rem; color: #388e3c;"></i>
          <span style="flex:1">{{ session('success') }}</span>
          <button type="button" aria-label="Dismiss success" onclick="document.getElementById('successBanner').style.display='none'" style="background: none; border: none; color: #256029; font-size: 1.2rem; cursor: pointer; position: absolute; top: 8px; right: 12px; line-height: 1;">&times;</button>
        </div>
        @endif
        <div class="approval-warning" id="approvalNotice" style="margin: 0 0 1.5rem 0; background: #e8f5e9; color: #256029; border: 1px solid #b2dfdb; border-radius: 6px; padding: 0.75rem 1rem; display: flex; align-items: center; gap: 0.75rem; position: relative;">
          <i class="fas fa-info-circle" style="font-size: 1.2rem;"></i>
          <span style="flex:1"><strong>Notice:</strong> All client and supplier accounts must be reviewed and approved by an administrator before you can access the system. You will receive an email once your account is approved.</span>
          <button type="button" aria-label="Dismiss notice" onclick="document.getElementById('approvalNotice').style.display='none'" style="background: none; border: none; color: #256029; font-size: 1.2rem; cursor: pointer; position: absolute; top: 8px; right: 12px; line-height: 1;">&times;</button>
        </div>
        
        @if (Route::has('register'))
          <div class="signup-link-inside">
            Don't have an account yet? <a href="{{ route('register') }}">Sign up</a>
          </div>
        @endif

   @if (session('status'))
    <div class="mb-4 session-status">
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 validation-errors">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        <form method="POST" action="{{ route('login') }}">
          @csrf

          <!-- Email Address -->
          <div class="input-group">
           <input type="text" name="login" placeholder="Email or Username" required 
       value="{{ old('login') }}" autofocus autocomplete="username">

          </div>

          <!-- Password -->
          <div class="input-group password-wrapper">
            <input type="password" name="password" placeholder="Password" required
                   autocomplete="current-password">
          </div>

          <!-- Remember Me -->
          <div class="remember-forgot">
    <label for="remember_me" class="remember-me">
        <input id="remember_me" type="checkbox" name="remember">
        <span>Remember me</span>
    </label>
    
    @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}" class="forgot-password">
            Forgot password?
        </a>
    @endif
</div>

             <input type="submit" value="Login">
        
         
        </form>
      </div>
    </div>
  </div>

</body>
</html>