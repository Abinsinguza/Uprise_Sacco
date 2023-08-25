<!DOCTYPE html>
<!-- Coding By CodingNepal - www.codingnepalweb.com -->
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title> Login</title>
  <link rel="stylesheet" href="{{ asset('style.css')}}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<div class="logo">
    <p><strong>UPRISE SACCO</strong></p>
  </div>

  <div class="navigation">
    <a href="#" class="nav-link"><i class="fas fa-home"></i> Home</a>
    <a href="{{ route('login') }}" class="nav-link"><i class="fas fa-sign-in-alt"></i> Login</a>
    <a href="{{ route('register') }}" class="nav-link"><i class="fas fa-user-plus"></i> Register</a>
  </div>
  
  <div class="wrapper">
   
    <form class="form" method="POST" action="{{ route('login') }}">
    @csrf
      <h2>Login</h2>
        <div class="input-field">
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', 'admin@lightbp.com') }}" required autocomplete="email" autofocus>
        <label for="email" class="col-md-6 col-form-label">Enter your email</label>
      </div>
      @error('email')
        <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
        </span>
      @enderror
      <div class="input-field">
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" value="{{ old('password', 'secret') }}" required autocomplete="current-password">
        <label for="password">Enter your password</label>
      </div>
      @error('password')
       <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
        </span>
       @enderror
      <div class="forget">
        <label for="remember">
          <input class="form-check-input" type="checkbox" name="remember"  id="remember">
          <p>Remember me</p>
        </label>
        <a href="{{ route('password.request') }}">Forgot password?</a>
      </div>
      <button type="submit">Log In</button>
      <div class="register">
        <p>Don't have an account? <a href="{{ route('register') }}">Register</a></p>
      </div>
    </form>
  </div>

</body>
</html>