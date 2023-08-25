<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register</title>
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
       <form method="POST" action="{{ route('register') }}">
       @csrf
          <h2>Register</h2>
          <div class="input-field">
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required autofocus>
            <label>Name</label>
          </div>
          <div class="input-field">
            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
            <label for="email">Email</label>
          </div>
          <div class="input-field">
            <input type="password" name="password" class="form-control" required>
            <label>Password</label>
          </div>
          <div class="input-field">
            <input type="password" name="password_confirmation" class="form-control" required autofocus>
            <label>Confirm password</label>
          </div>
          <div class="forget">
            <label>
              <input class="form-check-input" name="agree" type="checkbox" required>
              <p>Agree with terms and conditions</p>
            </label>
          </div>
          <button type="submit">Register</button>
          <div class="register">
            <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
          </div>
        </form>
    </div>
    
</body>
</html>