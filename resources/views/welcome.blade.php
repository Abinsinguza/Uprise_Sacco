<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sacco Home Page</title>
  <link rel="stylesheet" href="{{ asset('style.css')}}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Handjet&display=swap" rel="stylesheet">
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
  <div class="content">
      <img src="{{ asset('img/logo.png')}}">
  </div>
  <div class="welcome-message">
    <h1>Welcome to Uprise Sacco...</h1>
    <p>Digital gateway to financial empowerment!...<br><br>
    Our goal is to provide you with a range of financial solutions 
    and <br>services tailored to meet your unique needs. Whether you're 
    looking <br>to save for the future, secure a loan, or invest wisely, 
    we are here to <br>support you every step of the way.
    </p>
  </div>
</body>
</html>