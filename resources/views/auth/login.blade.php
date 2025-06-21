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