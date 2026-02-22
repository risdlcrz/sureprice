<!DOCTYPE html>
<html lang="en">
<head>
  @vite(['resources/css/login.css', 'resources/js/login.js'])
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Admin Center</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

  <header class="login-header">
    <img src="{{ asset('Images/gdc_logo.png') }}" alt="Logo" class="login-header__logo">
    <span class="login-header__title">Admin Center</span>
  </header>

  <main class="login-main">
    <div class="login-card">
      <div class="login-card__illustration" aria-hidden="true"></div>

      <div class="login-card__form-wrap">
        <h1 class="login-card__title">Login</h1>

        <div class="login-alerts">
          @if (session('success'))
            <div class="alert alert--success" id="successBanner" role="alert">
              <i class="fas fa-check-circle alert__icon" aria-hidden="true"></i>
              <span class="alert__text">{{ session('success') }}</span>
              <button type="button" class="alert__dismiss" aria-label="Dismiss" onclick="this.closest('.alert').style.display='none'">&times;</button>
            </div>
          @endif
          @if (session('status'))
            <div class="alert alert--info" role="alert">
              <i class="fas fa-info-circle alert__icon" aria-hidden="true"></i>
              <span class="alert__text">{{ session('status') }}</span>
            </div>
          @endif
          @if ($errors->any())
            <div class="alert alert--error" role="alert">
              <i class="fas fa-exclamation-circle alert__icon" aria-hidden="true"></i>
              <ul class="alert__list">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif
          <div class="alert alert--notice" id="approvalNotice">
            <i class="fas fa-info-circle alert__icon" aria-hidden="true"></i>
            <span class="alert__text"><strong>Notice:</strong> Client and supplier accounts require administrator approval. You will receive an email once approved.</span>
            <button type="button" class="alert__dismiss" aria-label="Dismiss" onclick="this.closest('.alert').style.display='none'">&times;</button>
          </div>
        </div>

        @if (Route::has('register'))
          <p class="login-card__signup">
            Don't have an account? <a href="{{ route('register') }}" class="login-card__link">Sign up</a>
          </p>
        @endif

        <form method="POST" action="{{ route('login') }}" class="login-form">
          @csrf

          <div class="login-form__field">
            <label for="login" class="login-form__label visually-hidden">Email or username</label>
            <input id="login" type="text" name="login" class="login-form__input" placeholder="Email or username" required
                   value="{{ old('login') }}" autofocus autocomplete="username">
          </div>

          <div class="login-form__field">
            <label for="password" class="login-form__label visually-hidden">Password</label>
            <input id="password" type="password" name="password" class="login-form__input" placeholder="Password" required
                   autocomplete="current-password">
          </div>

          <div class="login-form__row">
            <label for="remember_me" class="login-form__checkbox-label">
              <input id="remember_me" type="checkbox" name="remember" class="login-form__checkbox">
              <span>Remember me</span>
            </label>
            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}" class="login-form__link">Forgot password?</a>
            @endif
          </div>

          <button type="submit" class="login-form__submit">Login</button>
        </form>
      </div>
    </div>
  </main>

</body>
</html>
