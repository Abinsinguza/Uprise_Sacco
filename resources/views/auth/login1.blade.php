@extends('layouts/app', ['activePage' => 'login', 'title' => 'Light Bootstrap Dashboard Laravel by Creative Tim & UPDIVISION'])

@section('content')
<div class="login1">
    <div class="container">
        <div class="login__content">
            <img src="img/bg-login.png" alt="login image" class="login__img">

            <form action="" class="login__form" method="POST" action="">
                @csrf
                <div>
                    <h1 class="login__title">
                        <span>Welcome</span> Back
                    </h1>
                    <p class="login__description">
                        Welcome! Please login to continue.
                    </p>
                </div>
                
                <div>
                    <div class="login__inputs">
                        <div>
                            <label for="email" class="login__label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter your email address" required class="login__input">
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        </div>

                        <div>
                            <label for="" class="login__label">{{ __('Password') }}</label>

                            <div class="login__box">
                                <input type="password" placeholder="Enter your password" required class="login__input" id="input-pass" class="form-control @error('password') is-invalid @enderror" name="password"  required autocomplete="current-password" >
                                <i class="ri-eye-off-line login__eye" id="input-icon"></i>
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            </div>
                        </div>
                    </div>

                    <div class="login__check">
                        <input type="checkbox" class="login__check-input">
                        <label for="" class="login__check-label" type="checkbox" name="remember"  id="remember">Remember me</label>
                    </div>
                </div>

                <div>
                    <div class="login__buttons">
                        <button class="login__button">Log In</button>
                        <button class="login__button login__button-ghost">Sign Up</button>
                    </div>

                    <a href="#" class="login__forgot">Forgot Password?</a>
                    <a href="#" class="login__forgot">Create account</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('js')
    <script>
        $(document).ready(function() {
            demo.checkFullPageBackgroundImage();

            setTimeout(function() {
                // after 1000 ms we add the class animated to the login/register card
                $('.card').removeClass('card-hidden');
            }, 700)
        });
    </script>
@endpush